<?php

class eboywp_Eboy_Proximity_Core extends eboywp_Eboy
{

    /* (array) Ordered array of post IDs */
    public $ordered_posts = array();

    /* (array) Associative array containing each post ID and its distance */
    public $distance = array();


    function __construct() {
        $this->label = __( 'Proximity', 'EWP' );

        add_filter( 'eboywp_index_row', array( $this, 'index_latlng' ), 1, 2 );
        add_filter( 'eboywp_sort_options', array( $this, 'sort_options' ), 1, 2 );
        add_filter( 'eboywp_filtered_post_ids', array( $this, 'sort_by_distance' ), 10, 2 );
    }


    /**
     * Generate the eboy HTML
     */
    function render( $params ) {

        $output = '';
        $eboy = $params['eboy'];
        $value = $params['selected_values'];
        $unit = empty( $eboy['unit'] ) ? 'mi' : $eboy['unit'];

        $lat = empty( $value[0] ) ? '' : $value[0];
        $lng = empty( $value[1] ) ? '' : $value[1];
        $chosen_radius = empty( $value[2] ) ? '' : (float) $value[2];
        $location_name = empty( $value[3] ) ? '' : urldecode( $value[3] );

        $radius_options = array( 10, 25, 50, 100, 250 );

        // Grab the radius UI
        $radius_ui = empty( $eboy['radius_ui'] ) ? 'dropdown' : $eboy['radius_ui'];

        // Grab radius options from the UI
        if ( ! empty( $eboy['radius_options'] ) ) {
            $radius_options = explode( ',', preg_replace( '/\s+/', '', $eboy['radius_options'] ) );
        }

        // Grab default radius from the UI
        if ( empty( $chosen_radius ) && ! empty( $eboy['radius_default'] ) ) {
            $chosen_radius = (float) $eboy['radius_default'];
        }

        // Support dynamic radius
        if ( ! empty( $chosen_radius ) && 0 < $chosen_radius ) {
            if ( ! in_array( $chosen_radius, $radius_options ) ) {
                $radius_options[] = $chosen_radius;
            }
        }

        $radius_options = apply_filters( 'eboywp_proximity_radius_options', $radius_options );

        ob_start();
?>

        <input type="text" class="eboywp-location form-control  form-control-lg" value="<?php echo esc_attr( $location_name ); ?>" placeholder="<?php _e( 'Enter location', 'EWP' ); ?>" />
        <div class="eboywp-radius-label text-center pt-2">
            <span class="eboywp-radius-dist"><?php echo $chosen_radius; ?></span>
            <span class="eboywp-radius-unit"><?php echo $eboy['unit']; ?></span>
        </div>
        <?php if ( 'dropdown' == $radius_ui ) : ?>

        <select class="eboywp-radius eboywp-radius-dropdown">
            <?php foreach ( $radius_options as $radius ) : ?>
            <?php $selected = ( $chosen_radius == $radius ) ? ' selected' : ''; ?>
            <option value="<?php echo $radius; ?>"<?php echo $selected; ?>><?php echo "$radius $unit"; ?></option>
            <?php endforeach; ?>
        </select>

        <?php elseif ( 'slider' == $radius_ui ) : ?>

        <div class="eboywp-radius-wrap">
            <input class="eboywp-radius eboywp-radius-slider" type="range"
                data-slider-min="<?php echo $eboy['radius_min']; ?>"
                data-slider-max="<?php echo $eboy['radius_max']; ?>"
                data-slider-value="<?php echo $chosen_radius; ?>"
                data-slider-id="ex1Slider"
                id="ex1"
            />

        </div>

        <?php elseif ( 'none' == $radius_ui ) : ?>

        <input class="eboywp-radius eboywp-hidden" value="<?php echo $chosen_radius; ?>" />

        <?php endif; ?>

        <div class="eboywp-hidden">
            <input type="text" class="eboywp-lat" value="<?php echo esc_attr( $lat ); ?>" />
            <input type="text" class="eboywp-lng" value="<?php echo esc_attr( $lng ); ?>" />
        </div>
<?php
        return ob_get_clean();
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $eboy = $params['eboy'];
        $selected_values = $params['selected_values'];
        $unit = empty( $eboy['unit'] ) ? 'mi' : $eboy['unit'];
        $earth_radius = ( 'mi' == $unit ) ? 3959 : 6371;

        if ( empty( $selected_values ) || empty( $selected_values[0] ) ) {
            return 'continue';
        }

        $lat = (float) $selected_values[0];
        $lng = (float) $selected_values[1];
        $radius = (float) $selected_values[2];

        $sql = "
        SELECT DISTINCT post_id, ( $earth_radius * acos(
            greatest( -1, least( 1, ( /* acos() must be between -1 and 1 */
                cos( radians( $lat ) ) *
                cos( radians( eboy_value ) ) *
                cos( radians( eboy_display_value ) - radians( $lng ) ) +
                sin( radians( $lat ) ) *
                sin( radians( eboy_value ) )
            ) ) )
        ) ) AS distance
        FROM {$wpdb->prefix}eboywp_index
        WHERE eboy_name = '{$eboy['name']}'
        HAVING distance < $radius
        ORDER BY distance";

        $this->ordered_posts = array();
        $this->distance = array();

        if ( apply_filters( 'eboywp_proximity_store_distance', false ) ) {
            $results = $wpdb->get_results( $sql );
            foreach ( $results as $row ) {
                $this->ordered_posts[] = $row->post_id;
                $this->distance[ $row->post_id ] = $row->distance;
            }
        }
        else {
            $this->ordered_posts = $wpdb->get_col( $sql );
        }

        return $this->ordered_posts;
    }


    /**
     * Output admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('eboywp/load/proximity', function($this, obj) {
        $this.find('.eboy-source').val(obj.source);
        $this.find('.eboy-source-other').val(obj.source_other);
        $this.find('.eboy-unit').val(obj.unit);
        $this.find('.eboy-radius-ui').val(obj.radius_ui);
        $this.find('.eboy-radius-options').val(obj.radius_options);
        $this.find('.eboy-radius-default').val(obj.radius_default);
        $this.find('.eboy-radius-min').val(obj.radius_min);
        $this.find('.eboy-radius-max').val(obj.radius_max);
    });

    wp.hooks.addFilter('eboywp/save/proximity', function(obj, $this) {
        obj['source'] = $this.find('.eboy-source').val();
        obj['source_other'] = $this.find('.eboy-source-other').val();
        obj['unit'] = $this.find('.eboy-unit').val();
        obj['radius_ui'] = $this.find('.eboy-radius-ui').val();
        obj['radius_options'] = $this.find('.eboy-radius-options').val();
        obj['radius_default'] = $this.find('.eboy-radius-default').val();
        obj['radius_min'] = $this.find('.eboy-radius-min').val();
        obj['radius_max'] = $this.find('.eboy-radius-max').val();
        return obj;
    });

    wp.hooks.addAction('eboywp/change/proximity', function($this) {
        $this.closest('.eboywp-row').find('.eboy-radius-ui').trigger('change');
    });

    $(document).on('change', '.eboy-radius-ui', function() {
        var $eboy = $(this).closest('.eboywp-row');
        var ui = $(this).val();

        var radius_options = ('dropdown' == ui) ? 'table-row' : 'none';
        var range = ('slider' == ui) ? 'table-row' : 'none';

        $eboy.find('.eboy-radius-options').closest('tr').css({ 'display' : radius_options });
        $eboy.find('.eboy-radius-min').closest('tr').css({ 'display' : range });
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output front-end scripts
     */
    function front_scripts() {
        if ( apply_filters( 'eboywp_proximity_load_js', true ) ) {

            // hard-coded
            $api_key = defined( 'GMAPS_API_KEY' ) ? GMAPS_API_KEY : '';

            // admin ui
            $tmp_key = EWP()->helper->get_setting( 'gmaps_api_key' );
            $api_key = empty( $tmp_key ) ? $api_key : $tmp_key;

            // hook
            $api_key = apply_filters( 'eboywp_gmaps_api_key', $api_key );

            EWP()->display->assets['gmaps'] = '//maps.googleapis.com/maps/api/js?libraries=places&key=' . $api_key;
        }

        // Pass extra options into Places Autocomplete
        $options = apply_filters( 'eboywp_proximity_autocomplete_options', array() );
        EWP()->display->json['proximity']['autocomplete_options'] = $options;
        EWP()->display->json['proximity']['clearText'] = __( 'Clear location', 'EWP' );
    }


    /**
     * Output admin settings HTML
     */
    function settings_html() {
        $sources = EWP()->helper->get_data_sources();
?>
        <tr>
            <td>
                <?php _e('Longitude', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( '(Optional) use a separate longitude field', 'EWP' ); ?></div>
                </div>
            </td>
            <td>
                <select class="eboy-source-other">
                    <option value=""><?php _e( 'None', 'EWP' ); ?></option>
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
        <tr>
            <td>
                <?php _e( 'Unit of measurement', 'EWP' ); ?>:
            </td>
            <td>
                <select class="eboy-unit">
                    <option value="mi"><?php _e( 'Miles', 'EWP' ); ?></option>
                    <option value="km"><?php _e( 'Kilometers', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e( 'Radius UI', 'EWP' ); ?>:
            </td>
            <td>
                <select class="eboy-radius-ui">
                    <option value="dropdown"><?php _e( 'Dropdown', 'EWP' ); ?></option>
                    <option value="slider"><?php _e( 'Slider', 'EWP' ); ?></option>
                    <option value="none"><?php echo _e( 'None', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e( 'Radius options', 'EWP' ); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content">
                        A comma-separated list of radius choices
                    </div>
                </div>
            </td>
            <td>
                <input type="text" class="eboy-radius-options" value="10, 25, 50, 100, 250" />
            </td>
        </tr>
        <tr>
            <td>
                <?php _e( 'Slider range', 'EWP' ); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content">
                        Set the lower and upper limits
                    </div>
                </div>
            </td>
            <td>
                <input type="number" class="eboy-radius-min slim" value="1" />
                <input type="number" class="eboy-radius-max slim" value="50" />
            </td>
        </tr>
        <tr>
            <td>
                <?php _e( 'Default radius', 'EWP' ); ?>:
            </td>
            <td>
                <input type="number" class="eboy-radius-default slim" value="25" />
            </td>
        </tr>
<?php
    }


    /**
     * Index the coordinates
     * We expect a comma-separated "latitude, longitude"
     */
    function index_latlng( $params, $class ) {

        $eboy = EWP()->helper->get_eboy_by_name( $params['eboy_name'] );

        if ( false !== $eboy && 'proximity' == $eboy['type'] ) {
            $latlng = $params['eboy_value'];

            // Only handle "lat, lng" strings
            if ( is_string( $latlng ) ) {
                $latlng = preg_replace( '/[^0-9.,-]/', '', $latlng );

                if ( ! empty( $eboy['source_other'] ) ) {
                    $other_params = $params;
                    $other_params['eboy_source'] = $eboy['source_other'];
                    $rows = $class->get_row_data( $other_params );

                    if ( false === strpos( $latlng, ',' ) ) {
                        $lng = $rows[0]['eboy_display_value'];
                        $lng = preg_replace( '/[^0-9.,-]/', '', $lng );
                        $latlng .= ',' . $lng;
                    }
                }

                if ( preg_match( "/^([\d.-]+),([\d.-]+)$/", $latlng ) ) {
                    $latlng = explode( ',', $latlng );
                    $params['eboy_value'] = $latlng[0];
                    $params['eboy_display_value'] = $latlng[1];
                }
            }
        }

        return $params;
    }


    /**
     * Add "Distance" to the sort box
     */
    function sort_options( $options, $params ) {

        if ( EWP()->helper->eboy_setting_exists( 'type', 'proximity' ) ) {
            $options['distance'] = array(
                'label' => __( 'Distance', 'EWP' ),
                'query_args' => array(
                    'orderby' => 'post__in',
                    'order' => 'ASC',
                ),
            );
        }

        return $options;
    }


    /**
     * After the final list of post IDs has been produced,
     * sort them by distance if needed
     */
    function sort_by_distance( $post_ids, $class ) {

        $ordered_posts = EWP()->helper->eboy_types['proximity']->ordered_posts;

        if ( ! empty( $ordered_posts ) ) {

            // Sort the post IDs according to distance
            $intersected_ids = array( 0 );

            foreach ( $ordered_posts as $p ) {
                if ( in_array( $p, $post_ids ) ) {
                    $intersected_ids[] = $p;
                }
            }

            $post_ids = $intersected_ids;
        }

        return $post_ids;
    }
}


/**
 * Get a post's distance
 * NOTE: SET eboywp_proximity_store_distance filter = TRUE
 */
function eboywp_get_distance( $post_id = false ) {
    global $post;

    // Get the post ID
    $post_id = ( false === $post_id ) ? $post->ID : $post_id;

    // Get the proximity class
    $eboy_type = EWP()->helper->eboy_types['proximity'];

    if ( isset( $eboy_type->distance[ $post_id ] ) ) {
        $distance = $eboy_type->distance[ $post_id ];
        return apply_filters( 'eboywp_proximity_distance_output', $distance );
    }

    return false;
}
