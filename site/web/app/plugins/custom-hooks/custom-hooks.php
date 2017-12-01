<?php
/*
Plugin Name: Custom Hooks
Plugin URI: https://facetwp.com/
Description: A container for custom hooks
Version: 1.0
Author: FacetWP, LLC
*/

add_filter( 'facetwp_facet_types', function( $facet_types ) {
    $facet_types['links'] = new FacetWP_Facet_Links();
    return $facet_types;
});


class FacetWP_Facet_Links
{

    function __construct() {
        $this->label = __( 'Links', 'fwp' );
    }


    /**
     * Load the available choices
     */
    function load_values( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $from_clause = $wpdb->prefix . 'facetwp_index f';
        $where_clause = $params['where_clause'];

        // Count setting
        $limit = ctype_digit( $facet['count'] ) ? $facet['count'] : 10;

        $from_clause = apply_filters( 'facetwp_facet_from', $from_clause, $facet );
        $where_clause = apply_filters( 'facetwp_facet_where', $where_clause, $facet );

        $sql = "
        SELECT f.facet_value, f.facet_display_value, f.term_id, f.parent_id, f.depth, COUNT(DISTINCT f.post_id) AS counter
        FROM $from_clause
        WHERE f.facet_name = '{$facet['name']}' $where_clause
        GROUP BY f.facet_value
        ORDER BY f.depth, counter DESC, f.facet_display_value ASC
        LIMIT $limit";

        return $wpdb->get_results( $sql, ARRAY_A );
    }


    /**
     * Generate the output HTML
     */
    function render( $params ) {

        $output = '';
        $facet = $params['facet'];
        $values = (array) $params['values'];
        $selected_values = (array) $params['selected_values'];

        $key = 0;
        foreach ( $values as $key => $result ) {
            $selected = in_array( $result['facet_value'], $selected_values ) ? ' checked' : '';
            $selected .= ( 0 == $result['counter'] && '' == $selected ) ? ' disabled' : '';
            $output .= '<div class="facetwp-link' . $selected . '" data-value="' . esc_attr( $result['facet_value'] ) . '">';
            $output .= esc_html( $result['facet_display_value'] ) . ' <span class="facetwp-counter">(' . $result['counter'] . ')</span>';
            $output .= '</div>';
        }

        return $output;
    }


    /**
     * Return array of post IDs matching the selected values
     * using the wp_facetwp_index table
     */
    function filter_posts( $params ) {
        global $wpdb;

        $output = array();
        $facet = $params['facet'];
        $selected_values = $params['selected_values'];

        $sql = $wpdb->prepare( "SELECT DISTINCT post_id
            FROM {$wpdb->prefix}facetwp_index
            WHERE facet_name = %s",
            $facet['name']
        );

        foreach ( $selected_values as $key => $value ) {
            $results = facetwp_sql( $sql . " AND facet_value IN ('$value')", $facet );
            $output = ( $key > 0 ) ? array_intersect( $output, $results ) : $results;

            if ( empty( $output ) ) {
                break;
            }
        }

        return $output;
    }


    /**
     * Load and save facet settings
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/load/links', function($this, obj) {
        $this.find('.facet-source').val(obj.source);
        $this.find('.facet-count').val(obj.count);
    });

    wp.hooks.addFilter('facetwp/save/links', function($this, obj) {
        obj['source'] = $this.find('.facet-source').val();
        obj['count'] = $this.find('.facet-count').val();
        return obj;
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Parse the facet selections + other front-facing handlers
     */
    function front_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/refresh/links', function($this, facet_name) {
        var selected_values = [];
        $this.find('.facetwp-link.checked').each(function() {
            selected_values.push($(this).attr('data-value'));
        });
        FWP.facets[facet_name] = selected_values;
    });

    wp.hooks.addFilter('facetwp/selections/links', function(output, params) {
        var choices = [];
        $.each(params.selected_values, function(idx, val) {
            var choice = params.el.find('.facetwp-link[data-value="' + val + '"]').clone();
            choice.find('.facetwp-counter').remove();
            choices.push({
                value: val,
                label: choice.text()
            });
        });
        return choices;
    });

    $(document).on('click', '.facetwp-type-links .facetwp-link:not(.disabled)', function() {
        $(this).toggleClass('checked');
        FWP.autoload();
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Admin settings HTML
     */
    function settings_html() {
?>
        <tr>
            <td>
                <?php _e('Count', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'The maximum number of facet choices to show', 'fwp' ); ?></div>
                </div>
            </td>
            <td><input type="text" class="facet-count" value="10" /></td>
        </tr>
<?php
    }
}


add_filter( 'facetwp_facet_types', function( $facet_types ) {
    $facet_types['Capacity'] = new FacetWP_Facet_Capacity();
    return $facet_types;
});

class FacetWP_Facet_Capacity
{

    function __construct() {
        $this->label = __( 'Capacity', 'fwp' );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $output = '';
        $value = (array) $params['selected_values'];
        $value = empty( $value ) ? '' : $value[0];
        $output .= '<input type="text" placeholder="Capacity" class="facetwp-capacity-input" value="' . esc_attr( $value ) . '" />';
        $output .= '<input type="button" class="facetwp-submit" value="' . __( 'OK', 'fwp' ) . '" />';
        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $value = $params['selected_values'];
        $where = '';

        $number = (int) $value[0];
        $where .= " AND (facet_value + 0) <= '$number'";
        $where .= " AND (facet_display_value + 0) >= '$number'";

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}facetwp_index
        WHERE facet_name = '{$facet['name']}' $where";
        return facetwp_sql( $sql, $facet );
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('facetwp/load/capacity', function($this, obj) {
        $this.find('.facet-source').val(obj.source);
        $this.find('.facet-source-other').val(obj.source_other);
    });

    wp.hooks.addFilter('facetwp/save/capacity', function(obj, $this) {
        obj['source'] = $this.find('.facet-source').val();
        obj['source_other'] = $this.find('.facet-source-other').val();
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
    wp.hooks.addAction('facetwp/refresh/capacity', function($this, facet_name) {
        var val = $this.find('.facetwp-capacity-input').val();
        FWP.facets[facet_name] = ('' !== val) ? [val] : [];
    });

    $(document).on('click', '.facetwp-type-capacity .facetwp-submit', function() {
        FWP.autoload();
    });
})(jQuery);
</script>
<?php
    }


    /**
     * (Admin) Output settings HTML
     */
    function settings_html() {
        $sources = FWP()->helper->get_data_sources();
?>
        <tr>
            <td>
                <?php _e('Other data source', 'fwp'); ?>:
                <div class="facetwp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="facetwp-tooltip-content"><?php _e( 'Use a separate value for the upper limit?', 'fwp' ); ?></div>
                </div>
            </td>
            <td>
                <select class="facet-source-other">
                    <option value=""><?php _e( 'None', 'fwp' ); ?></option>
                    <?php foreach ( $sources as $group ) : ?>
                    <optgroup label="<?php echo $group['label']; ?>">
                        <?php foreach ( $group['choices'] as $val => $label ) : ?>
                        <option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
<?php
    }
}
