<?php

class eboywp_Facet_Select2
{

    function __construct() {
        $this->label = __( 'Select2', 'ewp' );
    }


    /**
     * Load the available choices
     */
    function load_values( $params ) {
        global $wpdb;

        // Inherit the load_values() method from the Dropdown facet type
        $dropdown_facet = EWP()->helper->facet_types['dropdown'];
        $facet = $params['facet'];

        $values = $dropdown_facet->load_values( $params );

        // Show "ghost" facet choices (those that return zero results)
        if ( 'yes' == $facet['ghosts'] ) {

            //$unfiltered_ids = implode( ',', $this->unfiltered_ids );
            $limit = ctype_digit( $facet['count'] ) ? $facet['count'] : 10;

            // Orderby
            $orderby = 'counter DESC, f.facet_display_value ASC';
            if ( 'display_value' == $facet['orderby'] ) {
                $orderby = 'f.facet_display_value ASC';
            }
            elseif ( 'raw_value' == $facet['orderby'] ) {
                $orderby = 'f.facet_value ASC';
            }

            $orderby = apply_filters( 'eboywp_facet_orderby', $orderby, $facet );

            // Primary difference here is that $where_clause is omitted to grab all values
            $sql = "
            SELECT f.facet_value, f.facet_display_value, f.parent_id, f.depth, 0 AS counter
            FROM {$wpdb->prefix}eboywp_index f
            WHERE f.facet_name = '{$facet['name']}'
            GROUP BY f.facet_value
            ORDER BY $orderby
            LIMIT $limit";

            $ghost_output = $wpdb->get_results( $sql );


            // Merge data on facet_value
            $load_values = $dropdown_facet->load_values( $params );

            foreach($ghost_output as $k=>&$option){

                foreach($values as $j=>&$value){
                    if( $option->facet_value == $value->facet_value){
                        $option->counter = $value->counter;
                    }
                } // foreach system-loaded vals

            } // foreach sql-loaded vals
            $values = $ghost_output;

        } // if show_ghosts



        return $values;
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $output = '';
        $facet = $params['facet'];
        $values = (array) $params['values'];
        $selected_values = (array) $params['selected_values'];

        $label_any = empty( $facet['label_any'] ) ? __( 'Any', 'ewp' ) : $facet['label_any'];

        $output .= '<select class="eboywp-select2">';
        $output .= '<option value="">' . esc_attr( $label_any ) . '</option>';

        foreach ( $values as $result ) {
            $selected = in_array( $result->facet_value, $selected_values ) ? ' selected' : '';
            $display_counter = $result->counter ? "($result->counter)" : "";
            $display_value = "$result->facet_display_value $display_counter";
            $output .= '<option value="' . $result->facet_value . '"' . $selected . '>' . $display_value . '</option>';
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
        return $wpdb->get_col( $sql );
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('eboywp/load/select2', function($this, obj) {
        $this.find('.facet-source').val(obj.source);
        $this.find('.facet-label-any').val(obj.label_any);
        $this.find('.type-select2 .facet-orderby').val(obj.orderby);
        $this.find('.type-select2 .facet-count').val(obj.count);
        $this.find('.type-select2 .facet-ghosts').val(obj.ghosts);
    });

    wp.hooks.addFilter('eboywp/save/select2', function($this, obj) {
        obj['source'] = $this.find('.facet-source').val();
        obj['label_any'] = $this.find('.type-select2 .facet-label-any').val();
        obj['orderby'] = $this.find('.type-select2 .facet-orderby').val();
        obj['count'] = $this.find('.type-select2 .facet-count').val();
        obj['ghosts'] = $this.find('.type-select2 .facet-ghosts').val();
        return obj;
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('eboywp/refresh/select2', function($this, facet_name) {
        $this.find('.eboywp-select2').select2('destroy');
        EWP.facets[facet_name] = $this.find('.eboywp-select2').val() || '';
    });

    wp.hooks.addAction('eboywp/ready', function() {
        $(document).on('change', '.eboywp-facet .eboywp-select2', function() {
            EWP.autoload();
        });
    });

    $(document).on('eboywp-loaded', function() {
        $('.eboywp-select2').select2({
            width: 'element'
        });
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
        <tr class="eboywp-conditional type-select2">
            <td>
                <?php _e( 'Default label', 'ewp' ); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content">
                        Customize the first option label (default: "Any")
                    </div>
                </div>
            </td>
            <td>
                <input type="text" class="facet-label-any" value="<?php _e( 'Any', 'ewp' ); ?>" />
            </td>
        </tr>
        <tr class="eboywp-conditional type-select2">
            <td><?php _e('Sort by', 'ewp'); ?>:</td>
            <td>
                <select class="facet-orderby">
                    <option value="count">Facet Count</option>
                    <option value="display_value">Display Value</option>
                    <option value="raw_value">Raw Value</option>
                </select>
            </td>
        </tr>
        <tr class="eboywp-conditional type-select2">
            <td>
                <?php _e('Count', 'ewp'); ?>:
                <span class="icon-question" title="The number of items to show">?</span>
            </td>
            <td><input type="text" class="facet-count" value="10" /></td>
        </tr>
        <tr class="eboywp-conditional type-select2">
            <td>
                <?php _e('Show ghosts', 'ewp'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'Show choices that would return zero results?', 'ewp' ); ?></div>
                </div>
            </td>
            <td>
                <select class="facet-ghosts">
                    <option value="no"><?php _e( 'No', 'ewp' ); ?></option>
                    <option value="yes"><?php _e( 'Yes', 'ewp' ); ?></option>
                </select>
            </td>
        </tr>
<?php
    }

     /**
     * For ghost facets, get all default facet options
     */
    function save_unfiltered_post_ids( $post_ids, $class ) {
        foreach ( $class->facets as $f ) {
            if ( isset( $f['ghosts'] ) && 'yes' == $f['ghosts'] ) {
                $this->unfiltered_ids = $post_ids;
                break;
            }
        }

        return $post_ids;
    }
}
