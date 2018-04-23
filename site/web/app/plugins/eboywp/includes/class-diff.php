<?php

class eboywp_Diff
{

    /**
     * Compare "eboywp_settings" with "eboywp_settings_last_index" to determine
     * whether the user needs to rebuild the index
     * @since 3.0.9
     */
    function is_reindex_needed() {
        $s1 = EWP()->helper->load_settings();
        $s2 = EWP()->helper->load_settings( true );

        // The eboy count is different
        if ( count( $s1['eboys'] ) !== count( $s2['eboys'] ) ) {
            return true;
        }

        // Compare settings
        $to_check = array( 'thousands_separator', 'decimal_separator', 'wc_enable_variations', 'wc_index_all' );

        foreach ( $to_check as $name ) {
            $attr1 = $this->get_attr( $name, $s1['settings'] );
            $attr2 = $this->get_attr( $name, $s2['settings'] );
            if ( $attr1 !== $attr2 ) {
                return true;
            }
        }

        $f1 = $s1['eboys'];
        $f2 = $s2['eboys'];

        // Sort the eboys alphabetically
        usort( $f1, function( $a, $b ) {
            return strcmp( $a['name'], $b['name'] );
        });

        usort( $f2, function( $a, $b ) {
            return strcmp( $a['name'], $b['name'] );
        });

        // Compare eboy properties
        $to_check = array( 'name', 'type', 'source', 'source_other', 'parent_term', 'hierarchical' );

        foreach ( $f1 as $index => $eboy ) {
            foreach ( $to_check as $attr ) {
                $attr1 = $this->get_attr( $attr, $eboy );
                $attr2 = $this->get_attr( $attr, $f2[ $index ] );
                if ( $attr1 !== $attr2 ) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Get an array element
     * @since 3.0.9
     */
    function get_attr( $name, $collection ) {
        if ( isset( $collection[ $name ] ) ) {
            return $collection[ $name ];
        }

        return false;
    }
}
