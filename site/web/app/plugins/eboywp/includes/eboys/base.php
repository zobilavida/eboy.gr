<?php

class eboywp_Eboy
{

    /**
     * Grab the orderby, as needed by several eboy types
     * @since 3.0.4
     */
    function get_orderby( $eboy ) {
        $key = $eboy['orderby'];

        // Count (default)
        $orderby = 'counter DESC, f.eboy_display_value ASC';

        // Display value
        if ( 'display_value' == $key ) {
            $orderby = 'f.eboy_display_value ASC';
        }
        // Raw value
        elseif ( 'raw_value' == $key ) {
            $orderby = 'f.eboy_value ASC';
        }
        // Term order
        elseif ('term_order' == $key && 'tax' == substr( $eboy['source'], 0, 3 ) ) {
            $term_ids = get_terms( array(
                'taxonomy' => str_replace( 'tax/', '', $eboy['source'] ),
                'fields' => 'ids',
            ) );

            if ( ! empty( $term_ids ) && ! is_wp_error( $term_ids ) ) {
                $term_ids = implode( ',', $term_ids );
                $orderby = "FIELD(f.term_id, $term_ids)";
            }
        }

        // Sort by depth just in case
        $orderby = "f.depth, $orderby";

        return $orderby;
    }
}
