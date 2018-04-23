<?php

class eboywp_Eboy_Radio_Core extends eboywp_Eboy
{

    function __construct() {
        $this->label = __( 'Radio', 'EWP' );
    }


    /**
     * Load the available choices
     */
    function load_values( $params ) {
        global $wpdb;

        $eboy = $params['eboy'];
        $from_clause = $wpdb->prefix . 'eboywp_index f';

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

        // Orderby
        $orderby = $this->get_orderby( $eboy );

        $orderby = apply_filters( 'eboywp_eboy_orderby', $orderby, $eboy );
        $from_clause = apply_filters( 'eboywp_eboy_from', $from_clause, $eboy );
        $where_clause = apply_filters( 'eboywp_eboy_where', $where_clause, $eboy );

        // Limit
        $limit = ctype_digit( $eboy['count'] ) ? $eboy['count'] : 20;

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

        if ( $show_ghosts && $is_filtered ) {
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

        $eboy = $params['eboy'];

        $output = '';
        $values = (array) $params['values'];
        $selected_values = (array) $params['selected_values'];

        $key = 0;
        foreach ( $values as $key => $result ) {
            $selected = in_array( $result['eboy_value'], $selected_values ) ? ' checked' : '';
            $selected .= ( 0 == $result['counter'] && '' == $selected ) ? ' disabled' : '';
            $output .= '<div class="eboywp-radio' . $selected . '" data-value="' . esc_attr( $result['eboy_value'] ) . '">';
            $output .= esc_html( $result['eboy_display_value'] ) . ' <span class="eboywp-counter">(' . $result['counter'] . ')</span>';
            $output .= '</div>';
        }

        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $eboy = $params['eboy'];
        $selected_values = $params['selected_values'];
        $selected_values = is_array( $selected_values ) ? $selected_values[0] : $selected_values;

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}eboywp_index
        WHERE eboy_name = '{$eboy['name']}' AND eboy_value IN ('$selected_values')";
        return eboywp_sql( $sql, $eboy );
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('eboywp/load/radio', function($this, obj) {
        $this.find('.eboy-source').val(obj.source);
        $this.find('.eboy-parent-term').val(obj.parent_term);
        $this.find('.eboy-orderby').val(obj.orderby);
        $this.find('.eboy-ghosts').val(obj.ghosts);
        $this.find('.eboy-preserve-ghosts').val(obj.preserve_ghosts);
        $this.find('.eboy-count').val(obj.count);
    });

    wp.hooks.addFilter('eboywp/save/radio', function(obj, $this) {
        obj['source'] = $this.find('.eboy-source').val();
        obj['parent_term'] = $this.find('.eboy-parent-term').val();
        obj['orderby'] = $this.find('.eboy-orderby').val();
        obj['ghosts'] = $this.find('.eboy-ghosts').val();
        obj['preserve_ghosts'] = $this.find('.eboy-preserve-ghosts').val();
        obj['count'] = $this.find('.eboy-count').val();
        return obj;
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output admin settings HTML
     */
    function settings_html() {
?>
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
            <td><input type="text" class="eboy-count" value="20" /></td>
        </tr>
<?php
    }
}
