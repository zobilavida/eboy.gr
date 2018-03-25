<?php

class eboywp_Facet_fSelect extends eboywp_Facet
{

    function __construct() {
        $this->label = __( 'fSelect', 'EWP' );
    }


    /**
     * Load the available choices
     */
    function load_values( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $from_clause = $wpdb->prefix . 'eboywp_index f';
        $where_clause = $params['where_clause'];

        // Preserve options when single-select or when behavior = "OR"
        $is_single = EWP()->helper->facet_is( $facet, 'multiple', 'no' );
        $using_or = EWP()->helper->facet_is( $facet, 'operator', 'or' );

        if ( $is_single || $using_or ) {

            // Apply filtering (ignore the facet's current selection)
            if ( isset( EWP()->or_values ) && ( 1 < count( EWP()->or_values ) || ! isset( EWP()->or_values[ $facet['name'] ] ) ) ) {
                $post_ids = array();
                $or_values = EWP()->or_values; // Preserve the original
                unset( $or_values[ $facet['name'] ] );

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
        $orderby = $this->get_orderby( $facet );

        // Limit
        $limit = ctype_digit( $facet['count'] ) ? $facet['count'] : 10;

        $orderby = apply_filters( 'eboywp_facet_orderby', $orderby, $facet );
        $from_clause = apply_filters( 'eboywp_facet_from', $from_clause, $facet );
        $where_clause = apply_filters( 'eboywp_facet_where', $where_clause, $facet );

        $sql = "
        SELECT f.facet_value, f.facet_display_value, f.term_id, f.parent_id, f.depth, COUNT(DISTINCT f.post_id) AS counter
        FROM $from_clause
        WHERE f.facet_name = '{$facet['name']}' $where_clause
        GROUP BY f.facet_value
        ORDER BY $orderby
        LIMIT $limit";

        return $wpdb->get_results( $sql, ARRAY_A );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $output = '';
        $facet = $params['facet'];
        $values = (array) $params['values'];
        $selected_values = (array) $params['selected_values'];

        if ( EWP()->helper->facet_is( $facet, 'hierarchical', 'yes' ) ) {
            $values = EWP()->helper->sort_taxonomy_values( $params['values'], $facet['orderby'] );
        }

        $multiple = EWP()->helper->facet_is( $facet, 'multiple', 'yes' ) ? ' multiple="multiple"' : '';
        $label_any = empty( $facet['label_any'] ) ? __( 'Any', 'EWP' ) : $facet['label_any'];
        $label_any = eboywp_i18n( $label_any );

        $output .= '<select class="eboywp-dropdown"' . $multiple . '>';
        $output .= '<option value="">' . esc_html( $label_any ) . '</option>';

        foreach ( $values as $result ) {
            $selected = in_array( $result['facet_value'], $selected_values ) ? ' selected' : '';

            $display_value = '';
            for ( $i = 0; $i < (int) $result['depth']; $i++ ) {
                $display_value .= '&nbsp;&nbsp;';
            }

            // Determine whether to show counts
            $display_value .= esc_html( $result['facet_display_value'] );
            $show_counts = apply_filters( 'eboywp_facet_dropdown_show_counts', true, array( 'facet' => $facet ) );

            if ( $show_counts ) {
                $display_value .= ' {{(' . $result['counter'] . ')}}';
            }

            $output .= '<option value="' . esc_attr( $result['facet_value'] ) . '"' . $selected . '>' . $display_value . '</option>';
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
        $facet = $params['facet'];
        $selected_values = $params['selected_values'];

        $sql = $wpdb->prepare( "SELECT DISTINCT post_id
            FROM {$wpdb->prefix}eboywp_index
            WHERE facet_name = %s",
            $facet['name']
        );

        // Match ALL values
        if ( 'and' == $facet['operator'] ) {
            foreach ( $selected_values as $key => $value ) {
                $results = eboywp_sql( $sql . " AND facet_value IN ('$value')", $facet );
                $output = ( $key > 0 ) ? array_intersect( $output, $results ) : $results;

                if ( empty( $output ) ) {
                    break;
                }
            }
        }
        // Match ANY value
        else {
            $selected_values = implode( "','", $selected_values );
            $output = eboywp_sql( $sql . " AND facet_value IN ('$selected_values')", $facet );
        }

        return $output;
    }


    /**
     * (Front-end) Attach settings to the AJAX response
     */
    function settings_js( $params ) {
        $facet = $params['facet'];

        $label_any = empty( $facet['label_any'] ) ? __( 'Any', 'EWP' ) : $facet['label_any'];
        $label_any = eboywp_i18n( $label_any );

        return array(
            'placeholder'   => $label_any,
            'overflowText'  => __( '{n} selected', 'EWP' ),
            'searchText'    => __( 'Search', 'EWP' )
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
        $this.find('.facet-source').val(obj.source);
        $this.find('.facet-multiple').val(obj.multiple);
        $this.find('.facet-label-any').val(obj.label_any);
        $this.find('.facet-parent-term').val(obj.parent_term);
        $this.find('.facet-orderby').val(obj.orderby);
        $this.find('.facet-hierarchical').val(obj.hierarchical);
        $this.find('.facet-operator').val(obj.operator);
        $this.find('.facet-count').val(obj.count);
    });

    wp.hooks.addFilter('eboywp/save/fselect', function(obj, $this) {
        obj['source'] = $this.find('.facet-source').val();
        obj['multiple'] = $this.find('.facet-multiple').val();
        obj['label_any'] = $this.find('.facet-label-any').val();
        obj['parent_term'] = $this.find('.facet-parent-term').val();
        obj['orderby'] = $this.find('.facet-orderby').val();
        obj['hierarchical'] = $this.find('.facet-hierarchical').val();
        obj['operator'] = $this.find('.facet-operator').val();
        obj['count'] = $this.find('.facet-count').val();
        return obj;
    });

    wp.hooks.addAction('eboywp/change/fselect', function($this) {
        $this.closest('.eboywp-row').find('.facet-multiple').trigger('change');
    });

    $(document).on('change', '.facet-multiple', function() {
        var $facet = $(this).closest('.eboywp-row');
        var display = ('yes' == $(this).val()) ? 'table-row' : 'none';
        $facet.find('.facet-operator').closest('tr').css({ 'display' : display });
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
        EWP()->display->assets['fSelect.css'] = eboywp_URL . '/assets/js/fSelect/fSelect.css';
        EWP()->display->assets['fSelect.js'] = eboywp_URL . '/assets/js/fSelect/fSelect.js';
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
                <select class="facet-multiple">
                    <option value="no"><?php _e( 'No', 'EWP' ); ?></option>
                    <option value="yes"><?php _e( 'Yes', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Default label', 'EWP' ); ?>:</td>
            <td>
                <input type="text" class="facet-label-any" value="<?php _e( 'Any', 'EWP' ); ?>" />
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
                <input type="text" class="facet-parent-term" value="" />
            </td>
        </tr>
        <tr>
            <td><?php _e('Sort by', 'EWP'); ?>:</td>
            <td>
                <select class="facet-orderby">
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
                <select class="facet-hierarchical">
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
                <select class="facet-operator">
                    <option value="and"><?php _e( 'Narrow the result set', 'EWP' ); ?></option>
                    <option value="or"><?php _e( 'Widen the result set', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Count', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'The maximum number of facet choices to show', 'EWP' ); ?></div>
                </div>
            </td>
            <td><input type="text" class="facet-count" value="10" /></td>
        </tr>
<?php
    }
}
