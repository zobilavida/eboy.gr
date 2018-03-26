<?php

class eboywp_Facet_Search extends eboywp_Facet
{

    function __construct() {
        $this->label = __( 'Search', 'EWP' );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $output = '';
        $value = (array) $params['selected_values'];
        $value = empty( $value ) ? '' : stripslashes( $value[0] );
        $placeholder = isset( $params['facet']['placeholder'] ) ? $params['facet']['placeholder'] : __( 'Enter keywords', 'EWP' );
        $placeholder = eboywp_i18n( $placeholder );
        $output .= '<span class="eboywp-search-wrap">';
        $output .= '<i class="eboywp-btn"></i>';
        $output .= '<input type="text" class="eboywp-search" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" />';
        $output .= '</span>';
        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {

        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        $selected_values = is_array( $selected_values ) ? $selected_values[0] : $selected_values;

        if ( empty( $selected_values ) ) {
            return 'continue';
        }

        // Default WP search
        $search_args = array(
            's' => $selected_values,
            'posts_per_page' => 200,
            'fields' => 'ids',
        );

        $search_args = apply_filters( 'eboywp_search_query_args', $search_args, $params );

        $query = new WP_Query( $search_args );

        return (array) $query->posts;
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('eboywp/load/search', function($this, obj) {
        $this.find('.facet-search-engine').val(obj.search_engine);
        $this.find('.facet-placeholder').val(obj.placeholder);
        $this.find('.facet-auto-refresh').val(obj.auto_refresh);
    });

    wp.hooks.addFilter('eboywp/save/search', function(obj, $this) {
        obj['search_engine'] = $this.find('.facet-search-engine').val();
        obj['placeholder'] = $this.find('.facet-placeholder').val();
        obj['auto_refresh'] = $this.find('.facet-auto-refresh').val();
        return obj;
    });

    wp.hooks.addAction('eboywp/change/search', function($this) {
        $this.closest('.eboywp-row').find('.name-source').hide();
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output admin settings HTML
     */
    function settings_html() {
        $engines = apply_filters( 'eboywp_facet_search_engines', array() );
?>
        <tr>
            <td><?php _e('Search engine', 'EWP'); ?>:</td>
            <td>
                <select class="facet-search-engine">
                    <option value=""><?php _e( 'WP Default', 'EWP' ); ?></option>
                    <?php foreach ( $engines as $key => $label ) : ?>
                    <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Placeholder text', 'EWP' ); ?>:</td>
            <td><input type="text" class="facet-placeholder" value="" /></td>
        </tr>
        <tr>
            <td>
                <?php _e('Auto refresh', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'Automatically refresh the results while typing?', 'EWP' ); ?></div>
                </div>
            </td>
            <td>
                <select class="facet-auto-refresh">
                    <option value="no"><?php _e( 'No', 'EWP' ); ?></option>
                    <option value="yes"><?php _e( 'Yes', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
<?php
    }


    /**
     * (Front-end) Attach settings to the AJAX response
     */
    function settings_js( $params ) {
        $auto_refresh = empty( $params['facet']['auto_refresh'] ) ? 'no' : $params['facet']['auto_refresh'];
        return array( 'auto_refresh' => $auto_refresh );
    }
}
