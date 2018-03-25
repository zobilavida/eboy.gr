<?php

class eboywp_Facet_Dropdown extends eboywp_Facet
{

    function __construct() {
        $this->label = __( 'Dropdown', 'EWP' );
    }


    /**
     * Load the available choices
     */
    function load_values( $params ) {
        global $wpdb;

        $facet = $params['facet'];

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
        $from_clause = $wpdb->prefix . 'eboywp_index f';

        // Orderby
        $orderby = $this->get_orderby( $facet );

        $orderby = apply_filters( 'eboywp_facet_orderby', $orderby, $facet );
        $from_clause = apply_filters( 'eboywp_facet_from', $from_clause, $facet );
        $where_clause = apply_filters( 'eboywp_facet_where', $where_clause, $facet );

        // Limit
        $limit = ctype_digit( $facet['count'] ) ? $facet['count'] : 20;

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

        $label_any = empty( $facet['label_any'] ) ? __( 'Any', 'EWP' ) : $facet['label_any'];
        $label_any = eboywp_i18n( $label_any );

        $output .= '<select class="eboywp-dropdown">';
        $output .= '<option value="">' . esc_attr( $label_any ) . '</option>';

        foreach ( $values as $result ) {
            $selected = in_array( $result['facet_value'], $selected_values ) ? ' selected' : '';

            $display_value = '';
            for ( $i = 0; $i < (int) $result['depth']; $i++ ) {
                $display_value .= '&nbsp;&nbsp;';
            }

            // Determine whether to show counts
            $display_value .= esc_attr( $result['facet_display_value'] );
            $show_counts = apply_filters( 'eboywp_facet_dropdown_show_counts', true, array( 'facet' => $facet ) );

            if ( $show_counts ) {
                $display_value .= ' (' . $result['counter'] . ')';
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

        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        $selected_values = is_array( $selected_values ) ? $selected_values[0] : $selected_values;

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}eboywp_index
        WHERE facet_name = '{$facet['name']}' AND facet_value IN ('$selected_values')";
        return eboywp_sql( $sql, $facet );
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('eboywp/load/dropdown', function($this, obj) {
        $this.find('.facet-source').val(obj.source);
        $this.find('.facet-label-any').val(obj.label_any);
        $this.find('.facet-parent-term').val(obj.parent_term);
        $this.find('.facet-orderby').val(obj.orderby);
        $this.find('.facet-hierarchical').val(obj.hierarchical);
        $this.find('.facet-count').val(obj.count);
    });

    wp.hooks.addFilter('eboywp/save/dropdown', function(obj, $this) {
        obj['source'] = $this.find('.facet-source').val();
        obj['label_any'] = $this.find('.facet-label-any').val();
        obj['parent_term'] = $this.find('.facet-parent-term').val();
        obj['orderby'] = $this.find('.facet-orderby').val();
        obj['hierarchical'] = $this.find('.facet-hierarchical').val();
        obj['count'] = $this.find('.facet-count').val();
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
                <?php _e( 'Default label', 'EWP' ); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content">
                        Customize the first option label (default: "Any")
                    </div>
                </div>
            </td>
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
                <?php _e('Count', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'The maximum number of facet choices to show', 'EWP' ); ?></div>
                </div>
            </td>
            <td><input type="text" class="facet-count" value="20" /></td>
        </tr>
<?php
    }
}
