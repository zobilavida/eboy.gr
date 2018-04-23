<?php

class eboywp_Eboy_fSelect extends eboywp_Eboy
{

    function __construct() {
        $this->label = __( 'fSelect', 'EWP' );
    }


    /**
     * Load the available choices
     */
    function load_values( $params ) {
        global $wpdb;

        $eboy = $params['eboy'];
        $from_clause = $wpdb->prefix . 'eboywp_index f';
        $where_clause = $params['where_clause'];

        // Preserve options when single-select or when behavior = "OR"
        $is_single = EWP()->helper->eboy_is( $eboy, 'multiple', 'no' );
        $using_or = EWP()->helper->eboy_is( $eboy, 'operator', 'or' );

        if ( $is_single || $using_or ) {

            // Apply filtering (ignore the eboy's current selection)
            if ( isset( EWP()->or_values ) && ( 1 < count( EWP()->or_values ) || ! isset( EWP()->or_values[ $eboy['name'] ] ) ) ) {
                $post_ids = array();
                $or_values = EWP()->or_values; // Preserve the original
                unset( $or_values[ $eboy['name'] ] );

                $counter = 0;
                foreach ( $or_values as $name => $vals ) {
                    $post_ids = ( 0 == $counter ) ? $vals : array_intersect( $post_ids, $vals );
                    $counter++;
                }

                // Return only applicable results
                $post_ids = array_intersect( $post_ids, EWP()->unfiltered_post_ids );
            }
            else {
                $post_ids = EWP()->unfiltered_post_ids;
            }

            $post_ids = empty( $post_ids ) ? array( 0 ) : $post_ids;
            $where_clause = ' AND post_id IN (' . implode( ',', $post_ids ) . ')';
        }

        // Orderby
        $orderby = $this->get_orderby( $eboy );

        // Limit
        $limit = ctype_digit( $eboy['count'] ) ? $eboy['count'] : 10;

        $orderby = apply_filters( 'eboywp_eboy_orderby', $orderby, $eboy );
        $from_clause = apply_filters( 'eboywp_eboy_from', $from_clause, $eboy );
        $where_clause = apply_filters( 'eboywp_eboy_where', $where_clause, $eboy );

        $sql = "
        SELECT f.eboy_value, f.eboy_display_value, f.term_id, f.parent_id, f.depth, COUNT(DISTINCT f.post_id) AS counter
        FROM $from_clause
        WHERE f.eboy_name = '{$eboy['name']}' $where_clause
        GROUP BY f.eboy_value
        ORDER BY $orderby
        LIMIT $limit";

        $output = $wpdb->get_results( $sql, ARRAY_A );

        // Show "ghost" eboy choices
        // For performance gains, only run if eboys are in use
        $show_ghosts = EWP()->helper->eboy_is( $eboy, 'ghosts', 'yes' );
        $is_filtered = EWP()->unfiltered_post_ids !== EWP()->eboy->query_args['post__in'];

        if ( $show_ghosts && $is_filtered && ! empty( EWP()->unfiltered_post_ids ) ) {
            $raw_post_ids = implode( ',', EWP()->unfiltered_post_ids );

            $sql = "
            SELECT f.eboy_value, f.eboy_display_value, f.term_id, f.parent_id, f.depth, 0 AS counter
            FROM $from_clause
            WHERE f.eboy_name = '{$eboy['name']}' AND post_id IN ($raw_post_ids)
            GROUP BY f.eboy_value
            ORDER BY $orderby
            LIMIT $limit";

            $ghost_output = $wpdb->get_results( $sql, ARRAY_A );

            // Keep the eboy placement intact
            if ( EWP()->helper->eboy_is( $eboy, 'preserve_ghosts', 'yes' ) ) {
                $tmp = array();
                foreach ( $ghost_output as $row ) {
                    $tmp[ $row['eboy_value'] . ' ' ] = $row;
                }

                foreach ( $output as $row ) {
                    $tmp[ $row['eboy_value'] . ' ' ] = $row;
                }

                $output = $tmp;
            }
            else {
                // Make the array key equal to the eboy_value (for easy lookup)
                $tmp = array();
                foreach ( $output as $row ) {
                    $tmp[ $row['eboy_value'] . ' ' ] = $row; // Force a string array key
                }
                $output = $tmp;

                foreach ( $ghost_output as $row ) {
                    $eboy_value = $row['eboy_value'];
                    if ( ! isset( $output[ "$eboy_value " ] ) ) {
                        $output[ "$eboy_value " ] = $row;
                    }
                }
            }

            $output = array_splice( $output, 0, $limit );
            $output = array_values( $output );
        }

        return $output;
    }


    /**
     * Generate the eboy HTML
     */
    function render( $params ) {

        $output = '';
        $eboy = $params['eboy'];
        $values = (array) $params['values'];
        $selected_values = (array) $params['selected_values'];

        if ( EWP()->helper->eboy_is( $eboy, 'hierarchical', 'yes' ) ) {
            $values = EWP()->helper->sort_taxonomy_values( $params['values'], $eboy['orderby'] );
        }

        $multiple = EWP()->helper->eboy_is( $eboy, 'multiple', 'yes' ) ? ' multiple="multiple"' : '';
        $label_any = empty( $eboy['label_any'] ) ? __( 'Any', 'EWP' ) : $eboy['label_any'];
        $label_any = eboywp_i18n( $label_any );

        $output .= '<select class="eboywp-dropdown"' . $multiple . '>';
        $output .= '<option value="">' . esc_html( $label_any ) . '</option>';

        foreach ( $values as $result ) {
            $selected = in_array( $result['eboy_value'], $selected_values ) ? ' selected' : '';
            $selected .= ( 0 == $result['counter'] && '' == $selected ) ? ' disabled' : '';

            $display_value = '';
            for ( $i = 0; $i < (int) $result['depth']; $i++ ) {
                $display_value .= '&nbsp;&nbsp;';
            }

            // Determine whether to show counts
            $display_value .= esc_html( $result['eboy_display_value'] );
            $show_counts = apply_filters( 'eboywp_eboy_dropdown_show_counts', true, array( 'eboy' => $eboy ) );

            if ( $show_counts ) {
                $display_value .= ' {{(' . $result['counter'] . ')}}';
            }

            $output .= '<option value="' . esc_attr( $result['eboy_value'] ) . '"' . $selected . '>' . $display_value . '</option>';
        }

        $output .= '</select>';
        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $output = array();
        $eboy = $params['eboy'];
        $selected_values = $params['selected_values'];

        $sql = $wpdb->prepare( "SELECT DISTINCT post_id
            FROM {$wpdb->prefix}eboywp_index
            WHERE eboy_name = %s",
            $eboy['name']
        );

        // Match ALL values
        if ( 'and' == $eboy['operator'] ) {
            foreach ( $selected_values as $key => $value ) {
                $results = eboywp_sql( $sql . " AND eboy_value IN ('$value')", $eboy );
                $output = ( $key > 0 ) ? array_intersect( $output, $results ) : $results;

                if ( empty( $output ) ) {
                    break;
                }
            }
        }
        // Match ANY value
        else {
            $selected_values = implode( "','", $selected_values );
            $output = eboywp_sql( $sql . " AND eboy_value IN ('$selected_values')", $eboy );
        }

        return $output;
    }


    /**
     * (Front-end) Attach settings to the AJAX response
     */
    function settings_js( $params ) {
        $eboy = $params['eboy'];

        $label_any = empty( $eboy['label_any'] ) ? __( 'Any', 'EWP' ) : $eboy['label_any'];
        $label_any = eboywp_i18n( $label_any );

        return array(
            'placeholder'   => $label_any,
            'overflowText'  => __( '{n} selected', 'EWP' ),
            'searchText'    => __( 'Search', 'EWP' ),
            'operator'      => $eboy['operator']
        );
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('eboywp/load/fselect', function($this, obj) {
        $this.find('.eboy-source').val(obj.source);
        $this.find('.eboy-multiple').val(obj.multiple);
        $this.find('.eboy-label-any').val(obj.label_any);
        $this.find('.eboy-parent-term').val(obj.parent_term);
        $this.find('.eboy-orderby').val(obj.orderby);
        $this.find('.eboy-hierarchical').val(obj.hierarchical);
        $this.find('.eboy-operator').val(obj.operator);
        $this.find('.eboy-ghosts').val(obj.ghosts);
        $this.find('.eboy-preserve-ghosts').val(obj.preserve_ghosts);
        $this.find('.eboy-count').val(obj.count);
    });

    wp.hooks.addFilter('eboywp/save/fselect', function(obj, $this) {
        obj['source'] = $this.find('.eboy-source').val();
        obj['multiple'] = $this.find('.eboy-multiple').val();
        obj['label_any'] = $this.find('.eboy-label-any').val();
        obj['parent_term'] = $this.find('.eboy-parent-term').val();
        obj['orderby'] = $this.find('.eboy-orderby').val();
        obj['hierarchical'] = $this.find('.eboy-hierarchical').val();
        obj['operator'] = $this.find('.eboy-operator').val();
        obj['ghosts'] = $this.find('.eboy-ghosts').val();
        obj['preserve_ghosts'] = $this.find('.eboy-preserve-ghosts').val();
        obj['count'] = $this.find('.eboy-count').val();
        return obj;
    });

    wp.hooks.addAction('eboywp/change/fselect', function($this) {
        $this.closest('.eboywp-row').find('.eboy-multiple').trigger('change');
    });

    $(document).on('change', '.eboy-multiple', function() {
        var $eboy = $(this).closest('.eboywp-row');
        var display = ('yes' == $(this).val()) ? 'table-row' : 'none';
        $eboy.find('.eboy-operator').closest('tr').css({ 'display' : display });
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
        EWP()->display->assets['fSelect.css'] = eboywp_URL . '/assets/vendor/fSelect/fSelect.css';
        EWP()->display->assets['fSelect.js'] = eboywp_URL . '/assets/vendor/fSelect/fSelect.js';
    }


    /**
     * Output admin settings HTML
     */
    function settings_html() {
?>
        <tr>
            <td>
                <?php _e( 'Multi-select?', 'EWP' ); ?>:
            </td>
            <td>
                <select class="eboy-multiple">
                    <option value="no"><?php _e( 'No', 'EWP' ); ?></option>
                    <option value="yes"><?php _e( 'Yes', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Default label', 'EWP' ); ?>:</td>
            <td>
                <input type="text" class="eboy-label-any" value="<?php _e( 'Any', 'EWP' ); ?>" />
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Parent term', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content">
                        To show only child terms, enter the parent <a href="https://eboywp.com/how-to-find-a-wordpress-terms-id/" target="_blank">term ID</a>.
                        Otherwise, leave blank.
                    </div>
                </div>
            </td>
            <td>
                <input type="text" class="eboy-parent-term" value="" />
            </td>
        </tr>
        <tr>
            <td><?php _e('Sort by', 'EWP'); ?>:</td>
            <td>
                <select class="eboy-orderby">
                    <option value="count"><?php _e( 'Highest Count', 'EWP' ); ?></option>
                    <option value="display_value"><?php _e( 'Display Value', 'EWP' ); ?></option>
                    <option value="raw_value"><?php _e( 'Raw Value', 'EWP' ); ?></option>
                    <option value="term_order"><?php _e( 'Term Order', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Hierarchical', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'Is this a hierarchical taxonomy?', 'EWP' ); ?></div>
                </div>
            </td>
            <td>
                <select class="eboy-hierarchical">
                    <option value="no"><?php _e( 'No', 'EWP' ); ?></option>
                    <option value="yes"><?php _e( 'Yes', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Behavior', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'How should multiple selections affect the results?', 'EWP' ); ?></div>
                </div>
            </td>
            <td>
                <select class="eboy-operator">
                    <option value="and"><?php _e( 'Narrow the result set', 'EWP' ); ?></option>
                    <option value="or"><?php _e( 'Widen the result set', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Show ghosts', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'Show choices that would return zero results?', 'EWP' ); ?></div>
                </div>
            </td>
            <td>
                <select class="eboy-ghosts">
                    <option value="no"><?php _e( 'No', 'EWP' ); ?></option>
                    <option value="yes"><?php _e( 'Yes', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Preserve ghost order', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'Keep ghost choices in the same order?', 'EWP' ); ?></div>
                </div>
            </td>
            <td>
                <select class="eboy-preserve-ghosts">
                    <option value="no"><?php _e( 'No', 'EWP' ); ?></option>
                    <option value="yes"><?php _e( 'Yes', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Count', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'The maximum number of eboy choices to show', 'EWP' ); ?></div>
                </div>
            </td>
            <td><input type="text" class="eboy-count" value="10" /></td>
        </tr>
<?php
    }
}
