<?php

add_action( 'init', 'wiloke_listgo_widget_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function wiloke_listgo_widget_load_textdomain() {
    load_plugin_textdomain( 'wiloke', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Wiloke Unlimited Widgets
 * This function contains the general functions, which be used in the each widget item
 */

if ( !class_exists('piWilokeWidgets') && class_exists('WP_Widget') )
{
    define('PI_WIDGET_URI', plugin_dir_url(__FILE__) . 'source/');

    class piWilokeWidgets extends WP_Widget
    {
        const PI_PREFIX = 'Wiloke - ';

        public function __construct($widgetID="", $widgetName="", $aParams)
        {
            if (!empty($widgetName) && !empty($widgetName))
            {
                parent::__construct($widgetID, $widgetName, $aParams);
            }
        }

        /**
         * Creating text field
         */
        public function pi_text_field($label, $id, $name, $val, $des="")
        {
            ?>
            <p>
                <label for="<?php echo esc_attr($id); ?>"><?php printf( (__('%s', 'wiloke')), $label); ?></label>
                <input id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($val); ?>" class="widefat <?php echo isset($args['class']) ? esc_attr($args['class']) : ''; ?>" type="text" />
                <?php
                if ( !empty($des) )
                {
                    echo '<p><code>'.$des.'</code></p>';
                }
                ?>
            </p>
        <?php
        }

        /**
         * Creating text field
         */
        public function pi_link_field($label, $id, $name, $val, $des="")
        {
            ?>
            <p>
                <label for="<?php echo esc_attr($id); ?>"><?php printf( (__('%s', 'wiloke')), $label); ?></label>
                <input id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($val); ?>" class="widefat <?php echo isset($args['class']) ? esc_url($args['class']) : ''; ?>" type="text" />
                <?php
                if ( !empty($des) )
                {
                    echo '<p><code>'.$des.'</code></p>';
                }
                ?>
            </p>
        <?php
        }

        /**
         * Creating upload field
         * pi_upload_form this function created at help->func.help.php
         */
        public function pi_upload_field($label, $id, $buttonId, $name, $multiple=false, $value, $des="")
        {
            ?>
            <p>
                <label for="<?php echo esc_attr($id); ?>"><?php print $label; ?></label>
                <?php $this->pi_upload_form($multiple, $name, $value, $buttonId); ?>
                <?php
                    if ( !empty($des) )
                    {
                        print '<code class="help">'. $des . '</code>';
                    }
                ?>
                <script type="text/javascript">
                    (function($){

                        if ( $.piWidgetIsWindowLoad )
                        {
                            var interval = setInterval( function()
                            {
                                var $self = $('.pi-btn-upload', '#widgets-right');
                                $.each($self, function()
                                {

                                    if ( $(this).attr('id') != 'widget-pi_about-__i__-upload_button')
                                    {
                                        $(this).parent().piWidgetMedia();
                                        clearInterval(interval);
                                    }
                                });
                            },100);
                        }
                    })(jQuery)
                </script>
            </p>
            <?php
        }

        /**
         * Help upload
         * @param $buttonId: it's very important with the widget upload field
         */

        public function pi_upload_form($multiple=false, $name, $value="", $buttonId="")
        {
            $buttonId = $buttonId ? $buttonId : uniqid("pi_upload_button");
            $url = "";
            if ( $multiple )
            {
                $type   =  'pi-multiple-upload';
                $ids    = $value;
                $value  =  explode(",", $value);
                foreach ( $value as $key  )
                {
                    $url .= wp_get_attachment_url($key) . ",";
                }
                $url   = rtrim($url, ",");
            }else{
                $type  =  'pi-single-upload';
            }

            ?>
            <div class="<?php echo esc_attr($type); ?>">
                <?php if ( !$multiple ) : ?>
                <input class="pi-list-image" type="" value="<?php echo !empty($value) ? esc_url($value) : ''; ?>" name="<?php echo esc_attr($name); ?>">
                <input class="pi-list-id" type="hidden" value="" name="">
                <?php else : ?>
                <input class="pi-list-image" type="hidden" value="<?php echo esc_attr($url); ?>" name="">
                <input class="pi-list-id" type="hidden" value="<?php echo esc_attr($ids); ?>" name="<?php echo esc_attr($name); ?>">
                <?php endif; ?>
                <button id="<?php echo esc_attr($buttonId); ?>" type="button" class="button button-primary pi-btn-upload" value="<?php _e('Upload', 'wiloke'); ?>"><?php _e("Upload", 'wiloke') ?></button>
                <ul class="pi-wrap-image pi-show-image">
                    <?php
                        if( !empty($value) )
                        {
                            if ( $multiple )
                            {
                                foreach ( $value as $id )
                                {
                                    echo '<li class="pi-item-image" style="display: inline-block;">'.wp_get_attachment_image($id, array(75, 75)).'<div class="pi-media-controls"><i class="pi-media-edit" title="Edit"></i><i class="pi-media-remove" title="Remove"></i></div></li>';
                                }
                            }else{
                                echo '<li class="pi-item-image" style="display: inline-block;"><img src="'.esc_url($value).'" alt="" style="width:75px; height:75px;"><div class="pi-media-controls"><i class="pi-media-edit" title="Edit"></i><i class="pi-media-remove" title="Remove"></i></div></li>';
                            }
                        }
                    ?>
                </ul>
            </div>
            <?php
        }

        public function pi_upload_media($name, $value="")
        {
            echo '<div class="audio-wrapper pi-wrap-text-upload">';
                echo '<p><textarea class="wiloke_input form-control pi-insert-audio" type="text" name="'.esc_attr($name).'">'.esc_textarea($value).'</textarea></p>';
                echo '<button type="button" class="button button-primary pi-audio-upload" value="'.__('Upload', 'wiloke').'">'.__("Upload", 'wiloke').'</button>';
            echo '</div>';
        }


        /**
         * Creating select field
         */
        public function pi_select_field($label, $id, $name, $aOptions, $val, $des="")
        {
            ?>
            <p>
                <label for="<?php echo esc_attr($id); ?>"><?php printf( (__('%s', 'wiloke')), $label); ?></label>
                <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>">
                    <?php
                    foreach ( $aOptions as $value => $optionName ) :
                        ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($value, $val); ?>><?php echo esc_html($optionName); ?></option>
                    <?php
                    endforeach;
                    ?>
                </select>
                <?php
                if ( !empty($des) )
                {
                    echo '<p><code>'.$des.'</code></p>';
                }
                ?>
            </p>
        <?php
        }

        /**
         * Creating textarea field
         */
        public function pi_textarea_field($label, $id, $name, $val, $des="")
        {
            ?>
            <p>
                <label for="<?php echo esc_attr($id); ?>"><?php printf( (__('%s', 'wiloke')), $label); ?></label>
                <textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" class="widefat" type="text"><?php echo wp_kses_post($val); ?></textarea>
                <?php
                if ( !empty($des) )
                {
                    echo '<p><code>'.$des.'</code></p>';
                }
                ?>
            </p>
        <?php
        }

        /**
         * Showing the list of posts
         * @param $args an array contain all of info of query
         */
        public function pi_list_of_posts($args)
        {
            $query = new WP_Query($args);
            if ( $query->have_posts()  ) : while ( $query->have_posts() ) : $query->the_post();
                $link = get_permalink($query->post->ID);
            ?>
                <div class="item">
                    <div class="item-image">
                        <div class="image-cover">
                            <?php if ( has_post_thumbnail($query->post->ID) ) : ?>
                                <a href="<?php echo esc_url($link); ?>">
                                    <?php echo get_the_post_thumbnail($query->post->ID, 'pi-postlisting'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="item-content">
                        <h3 class="item-title" data-number-line="2">
                            <a href="<?php echo esc_url($link); ?>"><?php echo get_the_title($query->post->ID); ?></a>
                        </h3>
                        <span class="item-meta"><?php echo $this->pi_get_the_date($query->post->ID); ?></span>
                    </div>
                </div>
            <?php
            endwhile; else:
            _e('<p>There aren no posts yet</p>', 'wiloke');
            endif;wp_reset_postdata();
        }

        /**
         * Showing the list of commented
         */
        public function pi_list_of_commented($args)
        {
            $query =  get_comments( $args );
            if ( $query )
            {
                foreach( $query as $comment )
                {
                    $link = get_permalink($comment->comment_post_ID);
                    ?>
                    <div class="item">
                        <div class="item-image">
                            <div class="image-cover">
                                <a href="<?php echo esc_url( $link ); ?>">
                                    <?php print get_avatar( $comment->user_id, apply_filters('pi_tab_comment', 70)); ?>
                                </a>
                            </div>
                        </div>
                        <div class="item-content">
                            <h3 class="item-title" data-number-line="2">
                                <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($comment->comment_author); ?></a>
                            </h3>
                            <div class="font-size__14 font-style__italic">
                                <p><?php print $comment->comment_content; ?></p>
                            </div>
                            <span class="item-meta"><?php echo $this->pi_get_the_date($comment->comment_post_ID); ?></span>
                        </div>
                    </div>
                <?php
                }
            }else{
                _e('<p>There are no any comments yet</p>', 'wiloke');
            }
        }

        /*Get date*/
        public function pi_get_the_date($postID, $format="")
        {
            $format     = $format ? $format : get_option('date_format');
            $date       = get_the_date($format, $postID);
            return $date;
        }

    }

    // new piWilokeWidgets();


    function pi_wiloke_widgets_init()
    {
        $aWidgets = array('piAbout', 'piMailchimp', 'piTWitterFeed', 'piContactInfo', 'piFlickrFeed', 'piInstagram', 'piAds', 'piFacebookLikebox', 'piPostsListing');

        $dir     = plugin_dir_path(__FILE__);


        foreach ($aWidgets as $widget)
        {
            if ( !class_exists($widget) )
            {
                $folder = strtolower($widget);

                $fileDir = $dir . 'modules/' . $folder . '/class.' . $folder . '.php';

                if (file_exists($fileDir)) {
                    include $fileDir;
                    register_widget($widget);
                }
            }
        }
    }

    add_action('widgets_init', 'pi_wiloke_widgets_init');

}

add_action('admin_enqueue_scripts', 'pi_widgets_scripts');


/**
 * Enqueue scripts to the widgets area
 */
function pi_widgets_scripts($page)
{
    if ( !empty($page) && $page == 'widgets.php' )
    {
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');

        wp_enqueue_script('pi_widgets', PI_WIDGET_URI . 'js/widgets.js', array('jquery'), '1.0', true);
        wp_enqueue_style('pi_widgets', PI_WIDGET_URI . 'css/style.css', array(), '1.0');
    }
}

include plugin_dir_path(__FILE__) . 'class.wilokeWidgetOptions.php';