<?php

class eboywp_Eboy_Number_Range extends eboywp_Eboy
{

    function __construct() {
        $this->label = __( 'Number Range', 'EWP' );
    }


    /**
     * Generate the eboy HTML
     */
    function render( $params ) {

        $output = '';
        $value = $params['selected_values'];
        $value = empty( $value ) ? array( '', '', ) : $value;
        $output .= '<label>' . __( 'Min', 'EWP' ) . '</label>';
        $output .= '<input type="text" class="eboywp-number eboywp-number-min" value="' . esc_attr( $value[0] ) . '" />';
        $output .= '<label>' . __( 'Max', 'EWP' ) . '</label>';
        $output .= '<input type="text" class="eboywp-number eboywp-number-max" value="' . esc_attr( $value[1] ) . '" />';
        $output .= '<input type="button" class="eboywp-submit" value="' . __( 'OK', 'EWP' ) . '" />';
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

        $start = ( '' == $values[0] ) ? false : EWP()->helper->format_number( $values[0] );
        $end = ( '' == $values[1] ) ? false : EWP()->helper->format_number( $values[1] );

        $is_dual = ! empty( $eboy['source_other'] );
        $is_intersect = EWP()->helper->eboy_is( $eboy, 'compare_type', 'intersect' );

        /**
         * Intersect compare
         * @link http://stackoverflow.com/a/325964
         */
        if ( $is_dual && $is_intersect ) {
            $start = ( false !== $start ) ? $start : -999999999999;
            $end = ( false !== $end ) ? $end : 999999999999;

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
     * Output any admin scripts
     */
    function admin_scripts() {
?>
<script>
(function($) {
    wp.hooks.addAction('eboywp/load/number_range', function($this, obj) {
        $this.find('.eboy-source').val(obj.source);
        $this.find('.eboy-source-other').val(obj.source_other);
        $this.find('.eboy-compare-type').val(obj.compare_type);
    });

    wp.hooks.addFilter('eboywp/save/number_range', function(obj, $this) {
        obj['source'] = $this.find('.eboy-source').val();
        obj['source_other'] = $this.find('.eboy-source-other').val();
        obj['compare_type'] = $this.find('.eboy-compare-type').val();
        return obj;
    });

    wp.hooks.addAction('eboywp/change/number_range', function($this) {
        $this.closest('.eboywp-row').find('.eboy-source-other').trigger('change');
    });
})(jQuery);
</script>
<?php
    }


    /**
     * (Admin) Output settings HTML
     */
    function settings_html() {
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
<?php
    }
}
