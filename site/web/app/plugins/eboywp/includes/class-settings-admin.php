<?php

class eboywp_Settings_Admin
{

    /**
     * Get the field settings array
     * @since 3.0.0
     */
    function get_settings() {

        $defaults = array(
            'general' => array(
                'label' => __( 'General', 'EWP' ),
                'fields' => array(
                    'license_key' => array(
                        'label' => __( 'License Key', 'EWP' ),
                        'html' => $this->get_field_html( 'license_key' )
                    ),
                    'gmaps_api_key' => array(
                        'label' => __( 'Google Maps API Key', 'EWP' ),
                        'html' => $this->get_field_html( 'gmaps_api_key' )
                    ),
                    'separators' => array(
                        'label' => __( 'Separators', 'EWP' ),
                        'html' => $this->get_field_html( 'separators' )
                    ),
                    'loading_animation' => array(
                        'label' => __( 'Loading Animation', 'EWP' ),
                        'html' => $this->get_field_html( 'loading_animation', 'dropdown', array(
                            'choices' => array( 'fade' => __( 'Fade', 'EWP' ), '' => __( 'Spin', 'EWP' ), 'none' => __( 'None', 'EWP' ) )
                        ) )
                    ),
                    'prefix' => array(
                        'label' => __( 'URL Prefix', 'EWP' ),
                        'html' => $this->get_field_html( 'prefix', 'dropdown', array(
                            'choices' => array( 'EWP_' => 'EWP_', '_' => '_' )
                        ) )
                    ),
                    'debug_mode' => array(
                        'label' => __( 'Debug Mode', 'EWP' ),
                        'html' => $this->get_field_html( 'debug_mode', 'dropdown', array(
                            'choices' => array( 'off' => __( 'Off', 'EWP' ), 'on' => __( 'On', 'EWP' ) )
                        ) )
                    )
                )
            ),
            'woocommerce' => array(
                'label' => __( 'WooCommerce', 'EWP' ),
                'fields' => array(
                    'wc_enable_variations' => array(
                        'label' => __( 'Support product variations?', 'EWP' ),
                        'notes' => __( 'Enable if your store uses variable products.', 'EWP' ),
                        'html' => $this->get_field_html( 'wc_enable_variations', 'dropdown', array(
                            'choices' => array( 'no' => __( 'No', 'EWP' ), 'yes' => __( 'Yes', 'EWP' ) )
                        ) )
                    ),
                    'wc_index_all' => array(
                        'label' => __( 'Include all products?', 'EWP' ),
                        'notes' => __( 'Show facet choices for out-of-stock products?', 'EWP' ),
                        'html' => $this->get_field_html( 'wc_index_all', 'dropdown', array(
                            'choices' => array( 'no' => __( 'No', 'EWP' ), 'yes' => __( 'Yes', 'EWP' ) )
                        ) )
                    )
                )
            ),
            'backup' => array(
                'label' => __( 'Backup', 'EWP' ),
                'fields' => array(
                    'export' => array(
                        'label' => __( 'Export', 'EWP' ),
                        'html' => $this->get_field_html( 'export' )
                    ),
                    'import' => array(
                        'label' => __( 'Import', 'EWP' ),
                        'html' => $this->get_field_html( 'import' )
                    )
                )
            )
        );

        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            unset( $defaults['woocommerce'] );
        }

        return apply_filters( 'eboywp_settings_admin', $defaults, $this );
    }


    /**
     * Return HTML for a setting field
     * @since 3.0.0
     */
    function get_field_html( $setting_name, $field_type = 'text', $atts = array() ) {
        ob_start();

        if ( 'license_key' == $setting_name ) : ?>

        <input type="text" class="eboywp-license" style="width:300px" value="<?php echo EWP()->helper->get_license_key(); ?>"<?php echo defined( 'eboywp_LICENSE_KEY' ) ? ' disabled' : ''; ?> />
        <input type="button" class="button button-small eboywp-activate" value="<?php _e( 'Activate', 'EWP' ); ?>" />
        <div class="eboywp-activation-status field-notes"><?php echo $this->get_activation_status(); ?></div>

<?php elseif ( 'gmaps_api_key' == $setting_name ) : ?>

        <input type="text" class="eboywp-setting" data-name="gmaps_api_key" style="width:300px" />
        <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#step-1-get-an-api-key-from-the-google-api-console" target="_blank">Get an API key</a>

<?php elseif ( 'separators' == $setting_name ) : ?>

        34
        <input type="text" style="width:20px" class="eboywp-setting" data-name="thousands_separator" />
        567
        <input type="text" style="width:20px" class="eboywp-setting" data-name="decimal_separator" />
        89

<?php elseif ( 'export' == $setting_name ) : ?>

        <select class="export-items" multiple="multiple" style="width:250px; height:100px">
            <?php foreach ( $this->get_export_choices() as $val => $label ) : ?>
            <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>
        <a class="button export-submit"><?php _e( 'Export', 'EWP' ); ?></a>

<?php elseif ( 'import' == $setting_name ) : ?>

        <div><textarea class="import-code" placeholder="<?php _e( 'Paste the import code here', 'EWP' ); ?>"></textarea></div>
        <div><input type="checkbox" class="import-overwrite" /> <?php _e( 'Overwrite existing items?', 'EWP' ); ?></div>
        <div style="margin-top:5px"><a class="button import-submit"><?php _e( 'Import', 'EWP' ); ?></a></div>

<?php elseif ( 'dropdown' == $field_type ) : ?>

        <select class="eboywp-setting slim" data-name="<?php echo $setting_name; ?>">
            <?php foreach ( $atts['choices'] as $val => $label ) : ?>
            <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>

<?php endif;

        return ob_get_clean();
    }


    /**
     * Get an array of all facets and templates
     * @since 3.0.0
     */
    function get_export_choices() {
        $export = array();

        $settings = EWP()->helper->settings;

        foreach ( $settings['facets'] as $facet ) {
            $export['facet-' . $facet['name']] = 'Facet - ' . $facet['label'];
        }

        foreach ( $settings['templates'] as $template ) {
            $export['template-' . $template['name']] = 'Template - '. $template['label'];
        }

        return $export;
    }


    /**
     * Get necessary data for the query builder
     * @since 3.0.0
     */
    function get_query_builder_choices() {
        $builder_taxonomies = array();
        $builder_post_types = array();

        $taxonomies = get_taxonomies( array(), 'object' );
        $post_types = get_post_types( array( 'public' => true ), 'objects' );

        foreach ( $taxonomies as $tax ) {
            $builder_taxonomies[ $tax->name ] = $tax->labels->singular_name;
        }

        foreach ( $post_types as $type ) {
            $builder_post_types[ $type->name ] = $type->labels->name;
        }

        return array(
            'taxonomies' => $builder_taxonomies,
            'post_types' => $builder_post_types
        );
    }


    /**
     * Get the activation status
     * @since 3.0.0
     */
    function get_activation_status() {
        $message = __( 'Not yet activated', 'EWP' );
        $activation = get_option( 'eboywp_activation' );

        if ( ! empty( $activation ) ) {
            $activation = json_decode( $activation );
            if ( 'success' == $activation->status ) {
                $message = __( 'License active', 'EWP' );
                $message .= ' (' . __( 'expires', 'EWP' ) . ' ' . date( 'M j, Y', strtotime( $activation->expiration ) ) . ')';
            }
            else {
                $message = $activation->message;
            }
        }

        return $message;
    }
}
