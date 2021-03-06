<?php

class eboywp_Eboy_Date_Range extends eboywp_Eboy
{

    function __construct() {
        $this->label = __( 'Date Range', 'EWP' );
    }


    /**
     * Generate the eboy HTML
     */
    function render( $params ) {

        $output = '';
        $value = $params['selected_values'];
        $value = empty( $value ) ? array( '', '' ) : $value;
        $fields = empty( $params['eboy']['fields'] ) ? 'both' : $params['eboy']['fields'];

        if ( 'exact' == $fields ) {
            $output .= '<input type="text" class="eboywp-date eboywp-date-min" value="' . esc_attr( $value[0] ) . '" placeholder="' . __( 'Date', 'EWP' ) . '" />';
        }
        if ( 'both' == $fields || 'start_date' == $fields ) {
            $output .= '<input type="text" class="eboywp-date eboywp-date-min" value="' . esc_attr( $value[0] ) . '" placeholder="' . __( 'Start Date', 'EWP' ) . '" />';
        }
        if ( 'both' == $fields || 'end_date' == $fields ) {
            $output .= '<input type="text" class="eboywp-date eboywp-date-max" value="' . esc_attr( $value[1] ) . '" placeholder="' . __( 'End Date', 'EWP' ) . '" />';
        }
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

        $start = empty( $values[0] ) ? false : $values[0];
        $end = empty( $values[1] ) ? false : $values[1];

        $is_dual = ! empty( $eboy['source_other'] );
        $compare_type = isset( $eboy['compare_type'] ) ? $eboy['compare_type'] : '';

        if ( $is_dual ) {
            $start = ( false !== $start ) ? $start : '0000-00-00';
            $end = ( false !== $end ) ? $end : '3000-12-31';

            /**
             * Single input, multiple data sources
             */
            if ( 'exact' == $eboy['fields'] ) {
                $end = $start;
            }

            /**
             * Intersect compare
             * @link http://stackoverflow.com/a/325964
             */
            if ( 'intersect' == $compare_type ) {
                $where .= " AND (LEFT(eboy_value, 10) <= '$end')";
                $where .= " AND (LEFT(eboy_display_value, 10) >= '$start')";
            }

            /**
             * Enclose compare
             * The post's range must surround the user-defined range
             */
            elseif ( 'enclose' == $compare_type ) {
                $where .= " AND LEFT(eboy_value, 10) <= '$start'";
                $where .= " AND LEFT(eboy_display_value, 10) >= '$end'";
            }
        }

        /**
         * Exact match
         */
        if ( 'exact' == $eboy['fields'] && '' == $where ) {
            if ( $start ) {
                $where .= " AND LEFT(eboy_value, 10) = '$start'";
            }
            if ( $end ) {
                $where .= " AND LEFT(eboy_display_value, 10) = '$end'";
            }
        }

        /**
         * Basic compare
         * The user-defined range must surround the post's range
         */
        if ( '' == $where ) {
            if ( $start ) {
                $where .= " AND LEFT(eboy_value, 10) >= '$start'";
            }
            if ( $end ) {
                $where .= " AND LEFT(eboy_display_value, 10) <= '$end'";
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
    wp.hooks.addAction('eboywp/load/date_range', function($this, obj) {
        $this.find('.eboy-source').val(obj.source);
        $this.find('.eboy-source-other').val(obj.source_other);
        $this.find('.eboy-compare-type').val(obj.compare_type);
        $this.find('.eboy-date-fields').val(obj.fields);
        $this.find('.eboy-format').val(obj.format);
    });

    wp.hooks.addFilter('eboywp/save/date_range', function(obj, $this) {
        obj['source'] = $this.find('.eboy-source').val();
        obj['source_other'] = $this.find('.eboy-source-other').val();
        obj['compare_type'] = $this.find('.eboy-compare-type').val();
        obj['fields'] = $this.find('.eboy-date-fields').val();
        obj['format'] = $this.find('.eboy-format').val();
        return obj;
    });

    wp.hooks.addAction('eboywp/change/date_range', function($this) {
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
        $locale = get_locale();
        $locale = empty( $locale ) ? 'en' : substr( $locale, 0, 2 );

        EWP()->display->json['datepicker'] = array(
            'locale'    => $locale,
            'clearText' => __( 'Clear', 'EWP' ),
        );
        EWP()->display->assets['flatpickr.css'] = eboywp_URL . '/assets/vendor/flatpickr/flatpickr.css';
        EWP()->display->assets['flatpickr.js'] = eboywp_URL . '/assets/vendor/flatpickr/flatpickr.min.js';

        if ( 'en' != $locale ) {
            EWP()->display->assets['flatpickr-l10n.js'] = eboywp_URL . "/assets/vendor/flatpickr/l10n/$locale.js";
        }
    }


    /**
     * Output admin settings HTML
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
                    <option value="enclose"><?php _e( 'Enclose', 'EWP' ); ?></option>
                    <option value="intersect"><?php _e( 'Intersect', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php _e('Fields to show', 'EWP'); ?>:</td>
            <td>
                <select class="eboy-date-fields">
                    <option value="both"><?php _e( 'Start + End Dates', 'EWP' ); ?></option>
                    <option value="exact"><?php _e( 'Exact Date', 'EWP' ); ?></option>
                    <option value="start_date"><?php _e( 'Start Date', 'EWP' ); ?></option>
                    <option value="end_date"><?php _e( 'End Date', 'EWP' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e('Display format', 'EWP'); ?>:
                <div class="eboywp-tooltip">
                    <span class="icon-question">?</span>
                    <div class="eboywp-tooltip-content">See available <a href="https://chmln.github.io/flatpickr/formatting/" target="_blank">formatting tokens</a></div>
                </div>
            </td>
            <td><input type="text" class="eboy-format" value="" placeholder="Y-m-d" /></td>
        </tr>
<?php
    }


    /**
     * (Front-end) Attach settings to the AJAX response
     */
    function settings_js( $params ) {
        $format = empty( $params['eboy']['format'] ) ? 'Y-m-d' : $params['eboy']['format'];
        return array( 'format' => $format );
    }
}
