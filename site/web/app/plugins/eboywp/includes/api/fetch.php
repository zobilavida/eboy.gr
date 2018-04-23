<?php

class eboywp_API_Fetch
{

    function __construct() {
        add_action( 'rest_api_init', array( $this, 'register' ) );
    }


    // PHP < 5.3
    function register() {
        register_rest_route( 'eboywp/v1/', '/fetch', array(
            'methods' => 'POST',
            'callback' => array( $this, 'callback' ),
            'permission_callback' => array( $this, 'permission_callback' )
        ) );
    }


    // PHP < 5.3
    function callback( $request ) {
        $data = $request->get_param( 'data' );
        $params = empty( $data ) ? array() : json_decode( $data, true );
        return $this->process_request( $params );
    }


    // PHP < 5.3
    function permission_callback( $request ) {
        return apply_filters( 'eboywp_api_can_access', false, $request );
    }


    function process_request( $params = array() ) {
        global $wpdb;

        $defaults = array(
            'eboys' => array(
                // 'category' => array( 'acf' )
            ),
            'query_args' => array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 10,
                'paged' => 1,
            ),
            'settings' => array(
                'first_load' => true
            )
        );

        $params = array_merge( $defaults, $params );
        $eboy_types = EWP()->helper->eboy_types;
        $valid_eboys = array();
        $eboys = array();

        // Validate input
        $page = (int) $params['query_args']['paged'];
        $per_page = (int) $params['query_args']['posts_per_page'];

        $page = max( $page, 1 );
        $per_page = ( 0 === $per_page ) ? 10 : $per_page;
        $per_page = ( -1 > $per_page ) ? absint( $per_page ) : $per_page;

        $params['query_args']['paged'] = $page;
        $params['query_args']['posts_per_page'] = $per_page;

        // Generate EWP()->eboy->eboys
        // Required by EWP()->helper->eboy_setting_exists()
        foreach ( $params['eboys'] as $eboy_name => $eboy_value ) {
            $eboy = EWP()->helper->get_eboy_by_name( $eboy_name );
            if ( false !== $eboy ) {
                $eboy['selected_values'] = (array) $eboy_value;
                $valid_eboys[ $eboy_name ] = $eboy;
                EWP()->eboy->eboys[] = $eboy;
            }
        }

        // Get bucket of post IDs
        EWP()->eboy->query_args = $params['query_args'];
        $post_ids = EWP()->eboy->get_filtered_post_ids();

        // SQL WHERE used by eboys
        $where_clause = empty( $post_ids ) ? '' : "AND post_id IN (" . implode( ',', $post_ids ) . ")";

        // Check if empty
        if ( 0 === $post_ids[0] && 1 === count( $post_ids ) ) {
            $post_ids = array();
        }

        // Get valid eboys and their values
        foreach ( $valid_eboys as $eboy_name => $eboy ) {
            $args = array(
                'eboy' => $eboy,
                'where_clause' => $where_clause,
                'selected_values' => $eboy['selected_values'],
            );

            $eboy_data = array(
                'name'          => $eboy['name'],
                'label'         => $eboy['label'],
                'type'          => $eboy['type'],
                'selected'      => $eboy['selected_values'],
            );

            // Load eboy choices if available
            if ( method_exists( $eboy_types[ $eboy['type'] ], 'load_values' ) ) {
                $choices = $eboy_types[ $eboy['type'] ]->load_values( $args );
                foreach ( $choices as $key => $choice ) {
                    $choices[ $key ] = array(
                        'value'     => $choice['eboy_value'],
                        'label'     => $choice['eboy_display_value'],
                        'depth'     => (int) $choice['depth'],
                        'count'     => (int) $choice['counter'],
                    );
                }
                $eboy_data['choices'] = $choices;
            }

            // Load eboy settings if available
            if ( method_exists( $eboy_types[ $eboy['type'] ], 'settings_js' ) ) {
                $eboy_data['settings'] = $eboy_types[ $eboy['type'] ]->settings_js( $args );
            }

            $eboys[ $eboy_name ] = $eboy_data;
        }

        $total_rows = count( $post_ids );

        // Paginate?
        if ( 0 < $per_page ) {
            $total_pages = ceil( $total_rows / $per_page );

            if ( $page > $total_pages ) {
                $post_ids = array();
            }
            else {
                $offset = ( $per_page * ( $page - 1 ) );
                $post_ids = array_slice( $post_ids, $offset, $per_page );
            }
        }
        else {
            $total_pages = ( 0 < $total_rows ) ? 1 : 0;
        }

        // Generate the output
        $output = array(
            'results' => $post_ids,
            'eboys' => $eboys,
            'pager' => array(
                'page' => $page,
                'per_page' => $per_page,
                'total_rows' => $total_rows,
                'total_pages' => $total_pages,
            )
        );

        return apply_filters( 'eboywp_api_output', $output );
    }
}
