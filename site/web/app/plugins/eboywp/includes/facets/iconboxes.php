<?php

class eboywp_Facet_Iconboxes extends eboywp_Facet
{

    function __construct() {
        $this->label = __( 'Iconboxes', 'EWP' );
    }


    /**
     * Load the available choices
     */
    function load_values( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $from_clause = $wpdb->prefix . 'eboywp_index f';
        $where_clause = $params['where_clause'];

        // Orderby
        $orderby = $this->get_orderby( $facet );

        // Limit
        $limit = ctype_digit( $facet['count'] ) ? $facet['count'] : 10;

        // Properly handle "OR" facets
        if ( 'or' == $facet['operator'] ) {

            // Apply filtering (ignore the facet's current selections)
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

        $output = $wpdb->get_results( $sql, ARRAY_A );

        // Show "ghost" facet choices
        if ( EWP()->helper->facet_is( $facet, 'ghosts', 'yes' ) && ! empty( EWP()->unfiltered_post_ids ) ) {
            $raw_post_ids = implode( ',', EWP()->unfiltered_post_ids );

            $sql = "
            SELECT f.facet_value, f.facet_display_value, f.term_id, f.parent_id, f.depth, 0 AS counter
            FROM $from_clause
            WHERE f.facet_name = '{$facet['name']}' AND post_id IN ($raw_post_ids)
            GROUP BY f.facet_value
            ORDER BY $orderby
            LIMIT $limit";

            $ghost_output = $wpdb->get_results( $sql, ARRAY_A );

            // Keep the facet placement intact
            if ( EWP()->helper->facet_is( $facet, 'preserve_ghosts', 'yes' ) ) {
                $tmp = array();
                foreach ( $ghost_output as $row ) {
                    $tmp[ $row['facet_value'] . ' ' ] = $row;
                }

                foreach ( $output as $row ) {
                    $tmp[ $row['facet_value'] . ' ' ] = $row;
                }

                $output = $tmp;
            }
            else {
                // Make the array key equal to the facet_value (for easy lookup)
                $tmp = array();
                foreach ( $output as $row ) {
                    $tmp[ $row['facet_value'] . ' ' ] = $row; // Force a string array key
                }
                $output = $tmp;

                foreach ( $ghost_output as $row ) {
                    $facet_value = $row['facet_value'];
                    if ( ! isset( $output[ "$facet_value " ] ) ) {
                        $output[ "$facet_value " ] = $row;
                    }
                }
            }

            $output = array_splice( $output, 0, $limit );
            $output = array_values( $output );
        }

        return $output;
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $facet = $params['facet'];

        if ( isset( $facet['hierarchical'] ) && 'yes' == $facet['hierarchical'] ) {
            return $this->render_hierarchy( $params );
        }

        $output = '';
        $values = (array) $params['values'];
        $selected_values = (array) $params['selected_values'];
        $soft_limit = empty( $facet['soft_limit'] ) ? 0 : (int) $facet['soft_limit'];

        $key = 0;
        foreach ( $values as $key => $result ) {
            if ( 0 < $soft_limit && $key == $soft_limit ) {
                $output .= '<div class="eboywp-overflow eboywp-hidden">';
            }
            $selected = in_array( $result['facet_value'], $selected_values ) ? ' checked' : '';
            $selected .= ( 0 == $result['counter'] && '' == $selected ) ? ' disabled' : '';
            $output .= '<div class="eboywp-checkbox 5' . $selected . '" data-value="' . esc_attr( $result['facet_value'] ) . '">';
            $output .= esc_html( $result['facet_display_value'] ) . ' <span class="eboywp-counter">(' . $result['counter'] . ')</span>';
            $output .= '</div>';
        }

        if ( 0 < $soft_limit && $soft_limit <= $key ) {
            $output .= '</div>';
            $output .= '<a class="eboywp-toggle">' . __( 'See {num} more', 'EWP' ) . '</a>';
            $output .= '<a class="eboywp-toggle eboywp-hidden">' . __( 'See less', 'EWP' ) . '</a>';
        }

        return $output;
    }


    /**
     * Generate the facet HTML (hierarchical taxonomies)
     */
    function render_hierarchy( $params ) {

        $output = '';
        $facet = $params['facet'];
        $selected_values = (array) $params['selected_values'];
        $values = EWP()->helper->sort_taxonomy_values( $params['values'], $facet['orderby'] );

        $init_depth = -1;
        $last_depth = -1;

        foreach ( $values as $result ) {
            $depth = (int) $result['depth'];

            if ( -1 == $last_depth ) {
                $init_depth = $depth;
            }
            elseif ( $depth > $last_depth ) {
                $output .= '<div class="eboywp-depth">';
            }
            elseif ( $depth < $last_depth ) {
                for ( $i = $last_depth; $i > $depth; $i-- ) {
                    $output .= '</div>';
                }
            }

            $selected = in_array( $result['facet_value'], $selected_values ) ? ' checked' : '';
            $selected .= ( 0 == $result['counter'] && '' == $selected ) ? ' disabled' : '';
            $output .= '<div class="eboywp-checkbox' . $selected . '" data-value="' . esc_attr( $result['facet_value'] ) . '">';
            $output .= esc_html( $result['facet_display_value'] ) . ' <span class="eboywp-counter">(' . $result['counter'] . ')</span>';
            $output .= '</div>';

            $last_depth = $depth;
        }

        for ( $i = $last_depth; $i > $init_depth; $i-- ) {
            $output .= '</div>';
        }

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
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('eboywp/load/iconboxes', function($this, obj) {
        $this.find('.facet-source').val(obj.source);
        $this.find('.facet-parent-term').val(obj.parent_term);
        $this.find('.facet-orderby').val(obj.orderby);
        $this.find('.facet-operator').val(obj.operator);
        $this.find('.facet-hierarchical').val(obj.hierarchical);
        $this.find('.facet-show-expanded').val(obj.show_expanded);
        $this.find('.facet-ghosts').val(obj.ghosts);
        $this.find('.facet-preserve-ghosts').val(obj.preserve_ghosts);
        $this.find('.facet-count').val(obj.count);
        $this.find('.facet-soft-limit').val(obj.soft_limit);
    });

    wp.hooks.addFilter('eboywp/save/iconboxes', function(obj, $this) {
        obj['source'] = $this.find('.facet-source').val();
        obj['parent_term'] = $this.find('.facet-parent-term').val();
        obj['orderby'] = $this.find('.facet-orderby').val();
        obj['operator'] = $this.find('.facet-operator').val();
        obj['hierarchical'] = $this.find('.facet-hierarchical').val();
        obj['show_expanded'] = $this.find('.facet-show-expanded').val();
        obj['ghosts'] = $this.find('.facet-ghosts').val();
        obj['preserve_ghosts'] = $this.find('.facet-preserve-ghosts').val();
        obj['count'] = $this.find('.facet-count').val();
        obj['soft_limit'] = $this.find('.facet-soft-limit').val();
        return obj;
    });

    wp.hooks.addAction('eboywp/change/iconboxes', function($this) {
        $this.closest('.eboywp-row').find('.facet-ghosts').trigger('change');
        $this.closest('.eboywp-row').find('.facet-hierarchical').trigger('change');
    });

    $(document).on('change', '.facet-ghosts', function() {
        var $facet = $(this).closest('.eboywp-row');
        var display = ('yes' == $(this).val()) ? 'table-row' : 'none';
        $facet.find('.facet-preserve-ghosts').closest('tr').css({ 'display' : display });
    });

    $(document).on('change', '.facet-hierarchical', function() {
        var $facet = $(this).closest('.eboywp-row');
        var display = ('yes' == $(this).val()) ? 'table-row' : 'none';
        $facet.find('.facet-show-expanded').closest('tr').css({ 'display' : display });
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
        EWP()->display->json['expand'] = '[+]';
        EWP()->display->json['collapse'] = '[-]';
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
                <?php _e('Show expanded', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'Should child terms be visible by default?', 'EWP' ); ?></div>
                </div>
            </td>
            <td>
                <select class="facet-show-expanded">
                    <option value="no"><?php _e( 'No', 'EWP' ); ?></option>
                    <option value="yes"><?php _e( 'Yes', 'EWP' ); ?></option>
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
                <select class="facet-ghosts">
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
                <select class="facet-preserve-ghosts">
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
            <td><input type="text" class="facet-count" value="10" /></td>
        </tr>
        <tr>
            <td>
                <?php _e('Soft Limit', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'Show a toggle link after this many choices', 'EWP' ); ?></div>
                </div>
            </td>
            <td><input type="text" class="facet-soft-limit" value="5" /></td>
        </tr>
<?php
    }


    /**
     * (Front-end) Attach settings to the AJAX response
     */
    function settings_js( $params ) {
        $expand = empty( $params['facet']['show_expanded'] ) ? 'no' : $params['facet']['show_expanded'];
        return array( 'show_expanded' => $expand );
    }
}
