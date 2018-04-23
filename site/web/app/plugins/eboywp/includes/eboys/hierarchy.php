<?php

class eboywp_Eboy_Hierarchy extends eboywp_Eboy
{

    function __construct() {
        $this->label = __( 'Hierarchy', 'EWP' );
    }


    /**
     * Load the available choices
     */
    function load_values( $params ) {
        global $wpdb;

        $eboy = $params['eboy'];
        $from_clause = $wpdb->prefix . 'eboywp_index f';
        $where_clause = $params['where_clause'];

        $selected_values = (array) $params['selected_values'];
        $eboy_parent_id = 0;
        $output = array();

        // Orderby
        $orderby = $this->get_orderby( $eboy );

        // Determine the parent_id and depth
        if ( ! empty( $selected_values[0] ) ) {

            // Get term ID from slug
            $sql = "
            SELECT t.term_id
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id AND tt.taxonomy = %s
            WHERE t.slug = %s
            LIMIT 1";

            $value = $selected_values[0];
            $taxonomy = str_replace( 'tax/', '', $eboy['source'] );
            $eboy_parent_id = (int) $wpdb->get_var( $wpdb->prepare( $sql, $taxonomy, $value ) );

            // Invalid term
            if ( $eboy_parent_id < 1 ) {
                return array();
            }

            // Create term lookup array
            $depths = EWP()->helper->get_term_depths( $taxonomy );
            $max_depth = (int) $depths[ $eboy_parent_id ]['depth'];
            $last_parent_id = $eboy_parent_id;

            // Loop backwards
            for ( $i = 0; $i <= $max_depth; $i++ ) {
                $output[] = array(
                    'eboy_value'           => $depths[ $last_parent_id ]['slug'],
                    'eboy_display_value'   => $depths[ $last_parent_id ]['name'],
                    'depth'                 => $depths[ $last_parent_id ]['depth'] + 1,
                    'counter'               => 1, // EWP.settings.num_choices
                );

                $last_parent_id = (int) $depths[ $last_parent_id ]['parent_id'];
            }

            $output[] = array(
                'eboy_value'           => '',
                'eboy_display_value'   => __( 'Any', 'EWP' ),
                'depth'                 => 0,
                'counter'               => 1,
            );

            // Reverse it
            $output = array_reverse( $output );
        }

        // Update the WHERE clause
        $where_clause .= " AND parent_id = '$eboy_parent_id'";

        $orderby = apply_filters( 'eboywp_eboy_orderby', $orderby, $eboy );
        $from_clause = apply_filters( 'eboywp_eboy_from', $from_clause, $eboy );
        $where_clause = apply_filters( 'eboywp_eboy_where', $where_clause, $eboy );

        $sql = "
        SELECT f.eboy_value, f.eboy_display_value, COUNT(DISTINCT f.post_id) AS counter
        FROM $from_clause
        WHERE f.eboy_name = '{$eboy['name']}' $where_clause
        GROUP BY f.eboy_value
        ORDER BY $orderby";

        $results = $wpdb->get_results( $sql, ARRAY_A );
        $new_depth = empty( $output ) ? 0 : $output[ count( $output ) - 1 ]['depth'] + 1;

        foreach ( $results as $result ) {
            $result['depth'] = $new_depth;
            $result['is_choice'] = true;
            $output[] = $result;
        }

        return $output;
    }


    /**
     * Generate the eboy HTML
     */
    function render( $params ) {
        $eboy = $params['eboy'];
        $values = (array) $params['values'];
        $selected_values = (array) $params['selected_values'];

        $output = '';
        $num_visible = ctype_digit( $eboy['count'] ) ? $eboy['count'] : 10;
        $num = 0;

        if ( ! empty( $values ) ) {
            foreach ( $values as $data ) {
                $last_depth = isset( $last_depth ) ? $last_depth : $data['depth'];

                $label = esc_html( $data['eboy_display_value'] );
                $is_checked = ( ! empty( $selected_values ) && $data['eboy_value'] == $selected_values[0] );
                $class = $is_checked ? ' checked' : '';

                if ( $data['depth'] > $last_depth ) {
                    $output .= '<div class="eboywp-depth">';
                }

                if ( $num == $num_visible ) {
                    $output .= '<div class="eboywp-overflow eboywp-hidden">';
                }

                if ( ! $is_checked ) {
                    if ( isset( $data['is_choice'] ) ) {
                        $label .= ' <span class="eboywp-counter">(' . $data['counter'] . ')</span>';
                    }
                    else {
                        $label = '&#8249; ' . $label;
                    }
                }

                $output .= '<div class="eboywp-link' . $class . '" data-value="' . esc_attr( $data['eboy_value'] ) . '">' . $label . '</div>';

                if ( isset( $data['is_choice'] ) ) {
                    $num++;
                }

                $last_depth = $data['depth'];
            }

            if ( $num_visible < $num ) {
                $output .= '</div>';
                $output .= '<a class="eboywp-toggle">' . __( 'See more', 'EWP' ) . '</a>';
                $output .= '<a class="eboywp-toggle eboywp-hidden">' . __( 'See less', 'EWP' ) . '</a>';
            }

            for ( $i = 0; $i <= $last_depth; $i++ ) {
                $output .= '</div>';
            }
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
        $selected_values = implode( "','", $selected_values );

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
    wp.hooks.addAction('eboywp/load/hierarchy', function($this, obj) {
        $this.find('.eboy-source').val(obj.source);
        $this.find('.eboy-orderby').val(obj.orderby);
        $this.find('.eboy-count').val(obj.count);
    });

    wp.hooks.addFilter('eboywp/save/hierarchy', function(obj, $this) {
        obj['source'] = $this.find('.eboy-source').val();
        obj['orderby'] = $this.find('.eboy-orderby').val();
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
            <td><?php _e( 'Sort by', 'EWP' ); ?>:</td>
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
                <?php _e( 'Count', 'EWP' ); ?>:
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
