<?php

class eboywp_Display
{

    /* (array) Eboy types being used on the page */
    public $active_types = array();

    /* (array) Eboys being used on the page */
    public $active_eboys = array();

    /* (boolean) Whether to enable eboywp for the current page */
    public $load_assets = false;

    /* (array) Scripts and stylesheets to enqueue */
    public $assets = array();

    /* (array) Data to pass to front-end JS */
    public $json = array();


    function __construct() {
        add_filter( 'widget_text', 'do_shortcode' );
        add_action( 'loop_start', array( $this, 'add_template_tag' ) );
        add_action( 'loop_no_results', array( $this, 'add_template_tag' ) );
        add_action( 'wp_footer', array( $this, 'front_scripts' ), 25 );
        add_shortcode( 'eboywp', array( $this, 'shortcode' ) );
    }


    /**
     * Detect the loop container if the "eboywp-template" class is missing
     */
    function add_template_tag( $wp_query ) {
        if ( true === $wp_query->get( 'eboywp' ) && did_action( 'wp_head' ) ) {
            echo "<!--EWP-loop-->\n";
        }
    }


    /**
     * Register shortcodes
     */
    function shortcode( $atts ) {
        $output = '';
        if ( isset( $atts['eboy'] ) ) {
            $eboy = EWP()->helper->get_eboy_by_name( $atts['eboy'] );

            if ( $eboy ) {
                $output = '<div class="ginput_container ginput_container_select eboywp-eboy eboywp-eboy-' . $eboy['name'] . ' eboywp-type-' . $eboy['type'] . '" data-name="' . $eboy['name'] . '" data-type="' . $eboy['type'] . '"></div>';

                // Build list of active eboy types
                $this->active_types[ $eboy['type'] ] = $eboy['type'];
                $this->active_eboys[ $eboy['name'] ] = $eboy['name'];
                $this->load_assets = true;
            }
        }
        elseif ( isset( $atts['template'] ) ) {
            $template = EWP()->helper->get_template_by_name( $atts['template'] );

            if ( $template ) {
                global $wp_query;

                // Preload the template (search engine visible)
                $temp_query = $wp_query;
                $preload_data = EWP()->ajax->get_preload_data( $template['name'] );
                $wp_query = $temp_query;

                $output = '<div class="eboywp-template" data-name="' . $atts['template'] . '">';
                $output .= $preload_data['template'];
                $output .= '</div>';

                $this->load_assets = true;
            }
        }
        elseif ( isset( $atts['sort'] ) ) {
            $this->active_extras['sort'] = true;
            $output = '<div class="eboywp-sort"></div>';
        }
        elseif ( isset( $atts['selections'] ) ) {
            $output = '<div class="eboywp-selections"></div>';
        }
        elseif ( isset( $atts['counts'] ) ) {
            $this->active_extras['counts'] = true;
            $output = '<div class="eboywp-counts"></div>';
        }
        elseif ( isset( $atts['pager'] ) ) {
            $this->active_extras['pager'] = true;
            $output = '<div class="eboywp-pager"></div>';
        }
        elseif ( isset( $atts['per_page'] ) ) {
            $this->active_extras['per_page'] = true;
            $output = '<div class="eboywp-per-page"></div>';
        }

        $output = apply_filters( 'eboywp_shortcode_html', $output, $atts );

        return $output;
    }


    /**
     * Output eboy scripts
     */
    function front_scripts() {

        // Not enqueued - front.js needs to load before front_scripts()
        if ( true === apply_filters( 'eboywp_load_assets', $this->load_assets ) ) {


            $this->assets['front.js'] = eboywp_URL . '/assets/js/dist/front.min.js';

            // Use the REST API?
            $ajaxurl = admin_url( 'admin-ajax.php' );
            if ( function_exists( 'get_rest_url' ) && apply_filters( 'eboywp_use_rest_api', true ) ) {
                $ajaxurl = get_rest_url() . 'eboywp/v1/refresh';
            }

            // Pass GET and URI params
            $http_params = array(
                'get' => $_GET,
                'uri' => EWP()->helper->get_uri(),
                'url_vars' => EWP()->ajax->url_vars,
            );

            // See EWP()->eboy->get_query_args()
            if ( ! empty( EWP()->eboy->archive_args ) ) {
                $http_params['archive_args'] = EWP()->eboy->archive_args;
            }

            // Populate the EWP_JSON object
            $this->json['loading_animation'] = EWP()->helper->get_setting( 'loading_animation' );
            $this->json['prefix'] = EWP()->helper->get_setting( 'prefix' );
            $this->json['no_results_text'] = __( 'No results found', 'EWP' );
            $this->json['ajaxurl'] = $ajaxurl;
            $this->json['nonce'] = wp_create_nonce( 'wp_rest' );

            if ( apply_filters( 'eboywp_use_preloader', true ) ) {
                $this->json['preload_data'] = $this->prepare_preload_data();
            }

            ob_start();

            foreach ( $this->active_types as $type ) {
                $eboy_class = EWP()->helper->eboy_types[ $type ];
                if ( method_exists( $eboy_class, 'front_scripts' ) ) {
                    $eboy_class->front_scripts();
                }
            }

            $inline_scripts = ob_get_clean();
            $assets = apply_filters( 'eboywp_assets', $this->assets );

            foreach ( $assets as $slug => $url ) {
                $html = '<script src="{url}"></script>';

                if ( 'css' == substr( $slug, -3 ) ) {
                    $html = '<link href="{url}" rel="stylesheet">';
                }

                if ( false !== strpos( $url, 'eboywp' ) ) {
                    $url .= '?ver=' . eboywp_VERSION;
                }

                echo str_replace( '{url}', $url, $html ) . "\n";
            }

            echo $inline_scripts;
?>
<script>
window.EWP_JSON = <?php echo json_encode( $this->json ); ?>;
window.EWP_HTTP = <?php echo json_encode( $http_params ); ?>;
</script>
<?php
        }
    }


    /**
     * On initial pageload, preload the eboy data
     * and pass it client-side through the EWP_JSON object
     */
    function prepare_preload_data() {
        $overrides = array();
        $url_vars = EWP()->ajax->url_vars;

        foreach ( $this->active_eboys as $name ) {
            $selected_values = isset( $url_vars[ $name ] ) ? $url_vars[ $name ] : array();

            $overrides['eboys'][] = array(
                'eboy_name' => $name,
                'selected_values' => $selected_values,
            );
        }

        if ( isset( $this->active_extras['counts'] ) ) {
            $overrides['extras']['counts'] = true;
        }
        if ( isset( $this->active_extras['pager'] ) ) {
            $overrides['extras']['pager'] = true;
        }
        if ( isset( $this->active_extras['per_page'] ) ) {
            $per_page = isset( $url_vars['per_page'] ) ? $url_vars['per_page'] : 'default';
            $overrides['extras']['per_page'] = $per_page;
        }
        if ( isset( $this->active_extras['sort'] ) ) {
            $sort = isset( $url_vars['sort'] ) ? $url_vars['sort'] : 'default';
            $overrides['extras']['sort'] = $sort;
        }

        $overrides['first_load'] = 1; // skip the template
        $output = EWP()->ajax->get_preload_data( false, $overrides );
        return $output;
    }
}
