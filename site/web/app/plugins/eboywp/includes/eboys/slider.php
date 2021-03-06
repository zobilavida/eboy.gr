<?php

class eboywp_Eboy_Slider extends eboywp_Eboy
{

    function __construct() {
        $this->label = __( 'Slider', 'EWP' );
    }


    /**
     * Generate the eboy HTML
     */
    function render( $params ) {

        $output = '<div class="eboywp-slider-wrap">';
        $output .= '<div class="eboywp-slider"></div>';
        $output .= '</div>';
        $output .= '<span class="eboywp-slider-label"></span>';
        $output .= '<div><input type="button" class="eboywp-slider-reset" value="' . __( 'Reset', 'EWP' ) . '" /></div>';
        return $output;
    }


    /**
     * Filter the query based on selected values
     */
    function filter_posts( $params ) {
        global $wpdb;

        $eboy = $params['eboy'];
        $values = $params['selected_values'];
        $where = '';

        $start = ( '' == $values[0] ) ? false : $values[0];
        $end = ( '' == $values[1] ) ? false : $values[1];

        $is_dual = ! empty( $eboy['source_other'] );
        $is_intersect = EWP()->helper->eboy_is( $eboy, 'compare_type', 'intersect' );

        /**
         * Intersect compare
         * @link http://stackoverflow.com/a/325964
         */
        if ( $is_dual && $is_intersect ) {
            $start = ( false !== $start ) ? $start : '-999999999999';
            $end = ( false !== $end ) ? $end : '999999999999';

            $where .= " AND (eboy_value + 0) <= '$end'";
            $where .= " AND (eboy_display_value + 0) >= '$start'";
        }
        else {
            if ( false !== $start ) {
                $where .= " AND (eboy_value + 0) >= '$start'";
            }
            if ( false !== $end ) {
                $where .= " AND (eboy_display_value + 0) <= '$end'";
            }
        }

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}eboywp_index
        WHERE eboy_name = '{$eboy['name']}' $where";
        return eboywp_sql( $sql, $eboy );
    }


    /**
     * (Front-end) Attach settings to the AJAX response
     */
    function settings_js( $params ) {
        global $wpdb;

        $eboy = $params['eboy'];
        $where_clause = $params['where_clause'];
        $selected_values = $params['selected_values'];

        // Set default slider values
        $defaults = array(
            'format' => '',
            'prefix' => '',
            'suffix' => '',
            'step' => 1,
        );
        $eboy = array_merge( $defaults, $eboy );

        $sql = "
        SELECT MIN(eboy_value + 0) AS `min`, MAX(eboy_display_value + 0) AS `max` FROM {$wpdb->prefix}eboywp_index
        WHERE eboy_name = '{$eboy['name']}' AND eboy_display_value != '' $where_clause";
        $row = $wpdb->get_row( $sql );

        $selected_min = isset( $selected_values[0] ) ? $selected_values[0] : $row->min;
        $selected_max = isset( $selected_values[1] ) ? $selected_values[1] : $row->max;

        return array(
            'range' => array(
                'min' => (float) $selected_min,
                'max' => (float) $selected_max
            ),
            'decimal_separator' => EWP()->helper->get_setting( 'decimal_separator' ),
            'thousands_separator' => EWP()->helper->get_setting( 'thousands_separator' ),
            'start' => array( $row->min, $row->max ),
            'format' => $eboy['format'],
            'prefix' => $eboy['prefix'],
            'suffix' => $eboy['suffix'],
            'step' => $eboy['step']
        );
    }


    /**
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('eboywp/load/slider', function($this, obj) {
        $this.find('.eboy-source').val(obj.source);
        $this.find('.eboy-source-other').val(obj.source_other);
        $this.find('.eboy-compare-type').val(obj.compare_type);
        $this.find('.eboy-prefix').val(obj.prefix);
        $this.find('.eboy-suffix').val(obj.suffix);
        $this.find('.eboy-format').val(obj.format);
        $this.find('.eboy-step').val(obj.step);
    });

    wp.hooks.addFilter('eboywp/save/slider', function(obj, $this) {
        obj['source'] = $this.find('.eboy-source').val();
        obj['source_other'] = $this.find('.eboy-source-other').val();
        obj['compare_type'] = $this.find('.eboy-compare-type').val();
        obj['prefix'] = $this.find('.eboy-prefix').val();
        obj['suffix'] = $this.find('.eboy-suffix').val();
        obj['format'] = $this.find('.eboy-format').val();
        obj['step'] = $this.find('.eboy-step').val();
        return obj;
    });

    wp.hooks.addAction('eboywp/change/slider', function($this) {
        $this.closest('.eboywp-row').find('.eboy-source-other').trigger('change');
    });
})(jQuery);
</script>
<?php
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
        EWP()->display->assets['nouislider.css'] = eboywp_URL . '/assets/vendor/noUiSlider/nouislider.min.css';
        EWP()->display->assets['nouislider.js'] = eboywp_URL . '/assets/vendor/noUiSlider/nouislider.min.js';
        EWP()->display->assets['nummy.js'] = eboywp_URL . '/assets/js/src/nummy.js';
    }


    /**
     * (Admin) Output settings HTML
     */
    function settings_html() {
        $thousands = EWP()->helper->get_setting( 'thousands_separator' );
        $decimal = EWP()->helper->get_setting( 'decimal_separator' );
        $sources = EWP()->helper->get_data_sources();
?>
        <tr>
            <td>
                <?php _e('Other data source', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'Use a separate value for the upper limit?', 'EWP' ); ?></div>
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
            <td><?php _e('Compare type', 'EWP'); ?>:</td>
            <td>
                <select class="eboy-compare-type">
                    <option value=""><?php _e( 'Basic', 'EWP' ); ?></option>
                    <option value="intersect"><?php _e( 'Intersect', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Prefix', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'Text that appears before each slider value', 'EWP' ); ?></div>
                </div>
            </td>
            <td><input type="text" class="eboy-prefix" value="" /></td>
        </tr>
        <tr>
            <td>
                <?php _e('Suffix', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'Text that appears after each slider value', 'EWP' ); ?></div>
                </div>
            </td>
            <td><input type="text" class="eboy-suffix" value="" /></td>
        </tr>
        <tr>
            <td>
                <?php _e('Format', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'The number format', 'EWP' ); ?></div>
                </div>
            </td>
            <td>
                <select class="eboy-format">
                    <?php if ( '' != $thousands ) : ?>
                    <option value="0,0">5<?php echo $thousands; ?>280</option>
                    <option value="0,0.0">5<?php echo $thousands; ?>280<?php echo $decimal; ?>4</option>
                    <option value="0,0.00">5<?php echo $thousands; ?>280<?php echo $decimal; ?>42</option>
                    <?php endif; ?>
                    <option value="0">5280</option>
                    <option value="0.0">5280<?php echo $decimal; ?>4</option>
                    <option value="0.00">5280<?php echo $decimal; ?>42</option>
                    <option value="0a">5k</option>
                    <option value="0.0a">5<?php echo $decimal; ?>3k</option>
                    <option value="0.00a">5<?php echo $decimal; ?>28k</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Step', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content"><?php _e( 'The amount of increase between intervals', 'EWP' ); ?> (default = 1)</div>
                </div>
            </td>
            <td><input type="text" class="eboy-step" value="1" /></td>
        </tr>
<?php
    }
}
