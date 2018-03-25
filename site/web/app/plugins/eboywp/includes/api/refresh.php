<?php

add_action( 'rest_api_init', function() {
    register_rest_route( 'eboywp/v1/', '/refresh', array(
        'methods' => 'POST',
        'callback' => 'eboywp_api_refresh'
    ) );
});

function eboywp_api_refresh( $request ) {
    $action = isset( $_POST['action'] ) ? $_POST['action'] : '';

    $valid_actions = array(
        'eboywp_refresh',
        'eboywp_autocomplete_load'
    );

    $valid_actions = apply_filters( 'eboywp_api_valid_actions', $valid_actions );

    if ( in_array( $action, $valid_actions ) ) {
        do_action( $action );
    }

    return array();
}
