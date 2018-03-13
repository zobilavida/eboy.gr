<?php

class piTabsposts extends piWilokeWidgets
{
    public $aDef = array('pi_popular'=>'Popular', 'pi_recent_posts'=>'Recent Posts', 'pi_recent_comments'=>'Recent Comments', 'number_of_posts'=>5, 'order_of_tabs'=>'pi_popular,pi_recent_posts,pi_recent_comments');
    public function __construct()
    {
        parent::__construct('wiloke_tabs_posts', parent::PI_PREFIX . 'Tabs Posts', array('class'=>'pi_tabs_posts'));
    }

    public function form($aInstance)
    {
        $aInstance = wp_parse_args($aInstance, $this->aDef);

        ?>
        <ul class="pi_widget_sortable">
            <?php
                if ( strpos($this->id, '__i__') )
                {
                    echo '<p><code>'.__('Click Save button to be able to change the order of tabs', 'pi').'</code></p>';
                }
                $aOrderOfTabs = explode(",", $this->aDef['order_of_tabs']);

                foreach ( $aOrderOfTabs as $tab) :

            ?>
            <li data-tab="<?php echo esc_attr($tab); ?>">
                <?php
                    echo sprintf( (__('%s', 'pi')), esc_html($aInstance[$tab]) );
                    $this->pi_text_field('Title', $this->get_field_id($tab), $this->get_field_name($tab), $aInstance[$tab]);
                ?>
            </li>
            <?php
                endforeach;
            ?>
        </ul>

        <?php
        echo '<div class="pi-order-of-tabs hidden">';
            $this->pi_text_field('Order', $this->get_field_id('pi_order_of_tabs'), $this->get_field_name('order_of_tabs'), $aInstance['order_of_tabs']);
        echo '</div>';
        $this->pi_text_field('Number of items to show: ', $this->get_field_id('number_of_posts'), $this->get_field_name('number_of_posts'), $aInstance['number_of_posts']);
        ?>
        <script>
            jQuery(document).ready(function($){
                $(".pi_widget_sortable").each(function(){
                    var $self = $(this);
                    $self.piWilokeWidgetsSortable({
                        stop: function(event, ui){
                            var _getOrder = [];
                            $(event.target).children().each(function(){
                                _getOrder.push($(this).data("tab"));
                            })
                            $self.next().find('input').val(_getOrder).trigger("change");
                        }
                    });
                })
            })
        </script>
        <?php
    }

    public function update($newInstance, $oldInstance)
    {
        $aInstance = $oldInstance;
        foreach ( $newInstance as $key => $val )
        {
            $aInstance[$key] = strip_tags($val);
        }
        return $aInstance;
    }

    public function widget($atts, $aInstance)
    {
        print $atts['before_widget'];
            $aOrder = explode(',', $aInstance['order_of_tabs']);
            $aIds   = array();
            ?>
            <div class="tab-wrapper">
                <ul class="tab-control nav nav-tabs">
                    <?php
                        foreach ( $aOrder as $key => $tab )
                        {
                            $aIds[$tab] = uniqid("tab_");
                            $key = $key == 0 ? 'active' : '';
                            ?>
                            <li class="tab-item <?php echo esc_attr($key); ?>">
                                <a data-toggle="tab" href="#<?php echo esc_attr($aIds[$tab]); ?>"><span><?php printf( (__('%s', 'pi')), wp_unslash($aInstance[$tab]) ); ?></span></a>
                            </li>
                            <?php
                        }
                    ?>
                </ul>
                <div class="tab-content">
                    <?php
                    foreach ( $aOrder as $key => $tab )
                    {
                        $key = $key == 0 ? 'active' : '';
                        ?>
                        <div id="<?php echo esc_attr($aIds[$tab]) ?>" class="tab-pane <?php echo esc_attr($key); ?>">
                            <?php
                                $args = array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>$aInstance['number_of_posts']);
                                switch ($tab)
                                {
                                    case 'pi_popular':
                                        $args['meta_key'] = 'pi_post_views_count';
                                        $args['orderby']  = 'meta_value_num';
                                        $args['order']    = 'DESC';
                                        $this->pi_list_of_posts($args);
                                        break;
                                    case 'pi_recent_comments':
                                        $args   = array(
                                            'number'        => (int)$aInstance['number_of_posts'],
                                            'status'        => 'approve',
                                            'post_type'     => 'post',
                                            'post_status'   => 'publish',
                                        );
                                        $this->pi_list_of_commented($args);
                                        break;
                                    case 'pi_recent_posts':
                                        $this->pi_list_of_posts($args);
                                        break;
                                }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        print $atts['after_widget'];
    }
}