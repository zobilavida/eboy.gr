<?php

class eboywp_Overrides
{

    public $raw;


    function __construct() {
        add_filter( 'eboywp_index_row', array( $this, 'index_row' ), 5, 2 );
        add_filter( 'eboywp_index_row', array( $this, 'format_numbers' ), 15, 2 );
    }


    /**
     * Indexer modifications
     */
    function index_row( $params, $class ) {
        if ( $class->is_overridden ) {
            return $params;
        }

        $eboy = EWP()->helper->get_eboy_by_name( $params['eboy_name'] );

        // Support "Other data source" values
        if ( ! empty( $eboy['source_other'] ) ) {
            $other_params = $params;
            $other_params['eboy_source'] = $eboy['source_other'];
            $rows = $class->get_row_data( $other_params );
            $params['eboy_display_value'] = $rows[0]['eboy_display_value'];
        }

        // Store raw numbers to format later, if needed
        if ( in_array( $eboy['type'], array( 'number_range', 'slider' ) ) ) {
            $this->raw = array(
                'value' => $params['eboy_value'],
                'label' => $params['eboy_display_value']
            );
        }

        return $params;
    }


    /**
     * Make sure that numbers are properly formatted
     */
    function format_numbers( $params, $class ) {

        $value = $params['eboy_value'];
        $label = $params['eboy_display_value'];

        if ( empty( $this->raw ) ) {
            return $params;
        }

        // Only format if un-altered
        if ( $this->raw['value'] === $value && $this->raw['label'] === $label ) {
            $params['eboy_value'] = EWP()->helper->format_number( $this->raw['value'] );
            $params['eboy_display_value'] = EWP()->helper->format_number( $this->raw['label'] );
        }

        $this->raw = null;

        return $params;
    }
}
