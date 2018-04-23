<?php

class eboywp_Integration_WooCommerce
{

    public $cache = array();
    public $lookup = array();
    public $storage = array();
    public $variations = array();


    function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
        add_filter( 'eboywp_eboy_sources', array( $this, 'eboy_sources' ) );
        add_filter( 'eboywp_indexer_post_eboy', array( $this, 'index_woo_values' ), 10, 2 );

        // Support WooCommerce product variations
        $is_enabled = ( 'yes' === EWP()->helper->get_setting( 'wc_enable_variations', 'no' ) );

        if ( apply_filters( 'eboywp_enable_product_variations', $is_enabled ) ) {
            add_filter( 'eboywp_indexer_post_eboy_defaults', array( $this, 'force_taxonomy' ), 10, 2 );
            add_filter( 'eboywp_indexer_query_args', array( $this, 'index_variations' ) );
            add_filter( 'eboywp_index_row', array( $this, 'attribute_variations' ), 1 );
            add_filter( 'eboywp_wpdb_sql', array( $this, 'wpdb_sql' ), 10, 2 );
            add_filter( 'eboywp_wpdb_get_col', array( $this, 'wpdb_get_col' ), 10, 3 );
            add_filter( 'eboywp_filtered_post_ids', array( $this, 'process_variations' ) );
            add_filter( 'eboywp_eboy_where', array( $this, 'eboy_where' ), 10, 2 );
        }
    }


    /**
     * Run WooCommerce handlers on eboywp-refresh
     * @since 2.0.9
     */
    function front_scripts() {
        EWP()->display->assets['query-string.js'] = eboywp_URL . '/assets/js/src/query-string.js';
        EWP()->display->assets['woocommerce.js'] = eboywp_URL . '/includes/integrations/woocommerce/woocommerce.js';
    }


    /**
     * Add WooCommerce-specific data sources
     * @since 2.1.4
     */
    function eboy_sources( $sources ) {
        $sources['woocommerce'] = array(
            'label' => __( 'WooCommerce', 'EWP' ),
            'choices' => array(
                'woo/price'             => __( 'Price' ),
                'woo/sale_price'        => __( 'Sale Price' ),
                'woo/regular_price'     => __( 'Regular Price' ),
                'woo/average_rating'    => __( 'Average Rating' ),
                'woo/stock_status'      => __( 'Stock Status' ),
                'woo/on_sale'           => __( 'On Sale' ),
                'woo/product_type'      => __( 'Product Type' ),
            ),
            'weight' => 5
        );

        // Move WC taxonomy choices
        foreach ( $sources['taxonomies']['choices'] as $key => $label ) {
            if ( 'tax/product_cat' == $key || 'tax/product_tag' == $key || 0 === strpos( $key, 'tax/pa_' ) ) {
                $sources['woocommerce']['choices'][ $key ] = $label;
                unset( $sources['taxonomies']['choices'][ $key ] );
            }
        }

        return $sources;
    }


    /**
     * Attributes for WC product variations are stored in postmeta
     * @since 2.7.2
     */
    function force_taxonomy( $defaults, $params ) {
        if ( 0 === strpos( $defaults['eboy_source'], 'tax/pa_' ) ) {
            $post_id = (int) $defaults['post_id'];

            if ( 'product_variation' == get_post_type( $post_id ) ) {
                $defaults['eboy_source'] = str_replace( 'tax/', 'cf/attribute_', $defaults['eboy_source'] );
            }
        }

        return $defaults;
    }


    /**
     * Index product variations
     * @since 2.7
     */
    function index_variations( $args ) {

        // Saving a single product
        if ( ! empty( $args['p'] ) ) {
            if ( 'product' == get_post_type( $args['p'] ) ) {
                $product = wc_get_product( $args['p'] );
                if ( 'variable' == $product->get_type() ) {
                    $children = $product->get_children();
                    $args['post_type'] = array( 'product', 'product_variation' );
                    $args['post__in'] = $children;
                    $args['post__in'][] = $args['p'];
                    $args['posts_per_page'] = -1;
                    unset( $args['p'] );
                }
            }
        }
        // Force product variations to piggyback products
        else {
            $pt = (array) $args['post_type'];

            if ( in_array( 'any', $pt ) ) {
                $pt = get_post_types();
            }
            if ( in_array( 'product', $pt ) ) {
                $pt[] = 'product_variation';
            }

            $args['post_type'] = $pt;
        }

        return $args;
    }


    /**
     * When indexing product variations, attribute its parent product
     * @since 2.7
     */
    function attribute_variations( $params ) {
        $post_id = (int) $params['post_id'];

        if ( 'product_variation' == get_post_type( $post_id ) ) {
            $params['post_id'] = wp_get_post_parent_id( $post_id );
            $params['variation_id'] = $post_id;

            // Lookup the term name for variation values
            if ( 0 === strpos( $params['eboy_source'], 'cf/attribute_pa_' ) ) {
                $taxonomy = str_replace( 'cf/attribute_', '', $params['eboy_source'] );
                $term = get_term_by( 'slug', $params['eboy_value'], $taxonomy );
                if ( false !== $term ) {
                    $params['term_id'] = $term->term_id;
                    $params['eboy_display_value'] = $term->name;
                }
            }
        }
        else {
            $params['variation_id'] = $post_id;
        }

        return $params;
    }


    /**
     * Hijack filter_posts() to grab variation IDs
     * @since 2.7
     */
    function wpdb_sql( $sql, $eboy ) {
        $sql = str_replace(
            'DISTINCT post_id',
            'DISTINCT post_id, GROUP_CONCAT(variation_id) AS variation_ids',
            $sql
        );

        $sql .= ' GROUP BY post_id';

        return $sql;
    }


    /**
     * Store a eboy's variation IDs
     * @since 2.7
     */
    function wpdb_get_col( $result, $sql, $eboy ) {
        global $wpdb;

        $eboy_name = $eboy['name'];
        $post_ids = $wpdb->get_col( $sql, 0 ); // arrays of product IDs
        $variations = $wpdb->get_col( $sql, 1 ); // variation IDs as arrays of comma-separated strings

        foreach ( $post_ids as $index => $post_id ) {
            $variations_array = explode( ',', $variations[ $index ] );
            $type = in_array( $post_id, $variations_array ) ? 'products' : 'variations';

            if ( isset( $this->cache[ $eboy_name ][ $type ] ) ) {
                $temp = $this->cache[ $eboy_name ][ $type ];
                $this->cache[ $eboy_name ][ $type ] = array_merge( $temp, $variations_array );
            }
            else {
                $this->cache[ $eboy_name ][ $type ] = $variations_array;
            }
        }

        return $result;
    }


    /**
     * We need lookup arrays for both products and variations
     * @since 2.7.1
     */
    function generate_lookup_array( $post_ids ) {
        global $wpdb;

        $output = array();

        if ( ! empty( $post_ids ) ) {
            $sql = "
            SELECT DISTINCT post_id, variation_id
            FROM {$wpdb->prefix}eboywp_index
            WHERE post_id IN (" . implode( ',', $post_ids ) . ")";
            $results = $wpdb->get_results( $sql );

            foreach ( $results as $result ) {
                $output['get_variations'][ $result->post_id ][] = $result->variation_id;
                $output['get_product'][ $result->variation_id ] = $result->post_id;
            }
        }

        return $output;
    }


    /**
     * Determine valid variation IDs
     * @since 2.7
     */
    function process_variations( $post_ids ) {
        if ( empty( $this->cache ) ) {
            return $post_ids;
        }

        $this->lookup = $this->generate_lookup_array( $post_ids );

        // Loop through each eboy's data
        foreach ( $this->cache as $eboy_name => $groups ) {
            $this->storage[ $eboy_name ] = array();

            // Create an array of variation IDs
            foreach ( $groups as $type => $ids ) { // products or variations
                $this->storage[ $eboy_name ] = array_merge( $this->storage[ $eboy_name ], $ids );

                // Lookup variation IDs for each product
                if ( 'products' == $type ) {
                    foreach ( $ids as $id ) {
                        if ( ! empty( $this->lookup['get_variations'][ $id ] ) ) {
                            $this->storage[ $eboy_name ] = array_merge( $this->storage[ $eboy_name ], $this->lookup['get_variations'][ $id ] );
                        }
                    }
                }
            }
        }

        $result = $this->calculate_variations();
        $this->variations = $result['variations'];
        $post_ids = array_intersect( $post_ids, array_keys( $result['products'] ) );
        $post_ids = empty( $post_ids ) ? array( 0 ) : $post_ids;
        return $post_ids;
    }


    /**
     * Calculate variation IDs
     * @param mixed $eboy_name Eboy name to ignore, or FALSE
     * @return array Associative array of product IDs + variation IDs
     * @since 2.8
     */
    function calculate_variations( $eboy_name = false ) {

        $new = true;
        $final_products = array();
        $final_variations = array();

        // Intersect product + variation IDs across eboys
        foreach ( $this->storage as $name => $variation_ids ) {

            // Skip eboys in "OR" mode
            if ( $eboy_name === $name ) {
                continue;
            }

            $final_variations = ( $new ) ? $variation_ids : array_intersect( $final_variations, $variation_ids );
            $new = false;
        }

        // Lookup each variation's product ID
        foreach ( $final_variations as $variation_id ) {
            if ( isset( $this->lookup['get_product'][ $variation_id ] ) ) {
                $final_products[ $this->lookup['get_product'][ $variation_id ] ] = true; // prevent duplicates
            }
        }

        // Append product IDs to the variations array
        $final_variations = array_merge( $final_variations, array_keys( $final_products ) );
        $final_variations = array_unique( $final_variations );

        return array(
            'products' => $final_products,
            'variations' => $final_variations
        );
    }


    /**
     * Apply variation IDs to load_values() method
     * @since 2.7
     */
    function eboy_where( $where_clause, $eboy ) {

        // Support eboys in "OR" mode
        if ( EWP()->helper->eboy_is( $eboy, 'operator', 'or' ) ) {
            $result = $this->calculate_variations( $eboy['name'] );
            $variations = $result['variations'];
        }
        else {
            $variations = $this->variations;
        }

        if ( ! empty( $variations ) ) {
            $where_clause .= ' AND variation_id IN (' . implode( ',', $variations ) . ')';
        }

        return $where_clause;
    }


    /**
     * Index WooCommerce-specific values
     * @since 2.1.4
     */
    function index_woo_values( $return, $params ) {
        $eboy = $params['eboy'];
        $defaults = $params['defaults'];
        $post_id = (int) $defaults['post_id'];
        $post_type = get_post_type( $post_id );

        // Index out of stock products?
        $index_all = ( 'yes' === EWP()->helper->get_setting( 'wc_index_all', 'no' ) );
        $index_all = apply_filters( 'eboywp_index_all_products', $index_all );

        if ( ! $index_all && ( 'product' == $post_type || 'product_variation' == $post_type ) ) {
            $product = wc_get_product( $post_id );
            if ( ! $product || ! $product->is_in_stock() ) {
                return true; // skip
            }
        }

        if ( 'product' != $post_type || empty( $eboy['source'] ) ) {
            return $return;
        }

        // Ignore product attributes with "Used for variations" ticked
        if ( 0 === strpos( $eboy['source'], 'tax/pa_' ) ) {
            $product = wc_get_product( $post_id );

            if ( $product->is_type( 'variable' ) ) {
                $attrs = $product->get_attributes();
                $attr_name = str_replace( 'tax/', '', $eboy['source'] );
                if ( isset( $attrs[ $attr_name ] ) && 1 === $attrs[ $attr_name ]['is_variation'] ) {
                    return true; // skip
                }
            }
        }

        // Custom woo fields
        if ( 0 === strpos( $eboy['source'], 'woo' ) ) {
            $product = wc_get_product( $post_id );

            // Price
            if ( 'woo/price' == $eboy['source'] ) {
                $price = $product->get_price();
                $defaults['eboy_value'] = $price;
                $defaults['eboy_display_value'] = $price;
                EWP()->indexer->index_row( $defaults );
            }

            // Sale Price
            elseif ( 'woo/sale_price' == $eboy['source'] ) {
                $price = $product->get_sale_price();
                $defaults['eboy_value'] = $price;
                $defaults['eboy_display_value'] = $price;
                EWP()->indexer->index_row( $defaults );
            }

            // Regular Price
            elseif ( 'woo/regular_price' == $eboy['source'] ) {
                $price = $product->get_regular_price();
                $defaults['eboy_value'] = $price;
                $defaults['eboy_display_value'] = $price;
                EWP()->indexer->index_row( $defaults );
            }

            // Average Rating
            elseif ( 'woo/average_rating' == $eboy['source'] ) {
                $rating = $product->get_average_rating();
                $defaults['eboy_value'] = $rating;
                $defaults['eboy_display_value'] = $rating;
                EWP()->indexer->index_row( $defaults );
            }

            // Stock Status
            elseif ( 'woo/stock_status' == $eboy['source'] ) {
                $in_stock = $product->is_in_stock();
                $defaults['eboy_value'] = (int) $in_stock;
                $defaults['eboy_display_value'] = $in_stock ? __( 'In Stock', 'EWP' ) : __( 'Out of Stock', 'EWP' );
                EWP()->indexer->index_row( $defaults );
            }

            // On Sale
            elseif ( 'woo/on_sale' == $eboy['source'] ) {
                if ( $product->is_on_sale() ) {
                    $defaults['eboy_value'] = 1;
                    $defaults['eboy_display_value'] = __( 'On Sale', 'EWP' );
                    EWP()->indexer->index_row( $defaults );
                }
            }

            // Product Type
            elseif ( 'woo/product_type' == $eboy['source'] ) {
                $type = $product->get_type();
                $defaults['eboy_value'] = $type;
                $defaults['eboy_display_value'] = $type;
                EWP()->indexer->index_row( $defaults );
            }

            return true; // skip
        }

        return $return;
    }
}


if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    new eboywp_Integration_WooCommerce();
}
