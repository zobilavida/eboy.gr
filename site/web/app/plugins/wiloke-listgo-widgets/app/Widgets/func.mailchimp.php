<?php
/**
* Config mailchimp
*/

if ( !function_exists('pi_add_mailchimp_menu') )
{
    add_action('admin_menu', 'pi_add_mailchimp_menu');

    function pi_add_mailchimp_menu()
    {
        add_action('admin_enqueue_scripts', 'wiloke_include_mailchimp_js');
        add_options_page(esc_html__('Wiloke MailChimp', 'wiloke'), esc_html__('Wiloke MailChimp', 'wiloke'), 'edit_theme_options', 'wiloke-mailchimp', 'wiloke_config_mailchimp');
    }

    function wiloke_include_mailchimp_js($hook)
    {
        if ( $hook == 'settings_page_wiloke-mailchimp' )
        {
            wp_enqueue_script('pi_mailchimp_js', plugin_dir_url(dirname(dirname(__FILE__))) . 'source/js/mailchimp.js', array('jquery'), '1.0', true);
        }
    }

    function wiloke_config_mailchimp()
    {
        $aLists = get_option("pi_mailchimp_lists");

        if ( isset($_POST['pi_mailchimp']['list_id']) && !empty($_POST['pi_mailchimp']['list_id']) )
        {
            $_POST['pi_mailchimp']['list_id'] = sanitize_text_field($_POST['pi_mailchimp']['list_id']);
            $_POST['pi_mailchimp']['api_key'] = sanitize_text_field($_POST['pi_mailchimp']['api_key']);
            foreach ( $_POST['wiloke_subscribe'] as $key => $val ){
                $_POST['wiloke_subscribe'][$key] = strip_tags($val);
            }

            update_option('pi_mailchimp_listid', $_POST['pi_mailchimp']['list_id']);
            update_option('pi_mailchimp_api_key', $_POST['pi_mailchimp']['api_key']);
            update_option('wiloke_subscribe_settings', $_POST['wiloke_subscribe']);
        }
        $mailchimpAPI = get_option('pi_mailchimp_api_key');
        $selected     = get_option('pi_mailchimp_listid');
        $aSubscribeSettings = get_option('wiloke_subscribe_settings');
	    $aSubscribeSettings = wp_parse_args($aSubscribeSettings, array('title'=>'Subscribe', 'description'=>'Subscribe us and never miss our new articles', 'thanks'=>'Wait! Just one more thing! We just sent to you a confirmation email to know you\'re real traveller and not a robot. So please check your mailbox, click on the confirmation button. That\'s it!'))

        ?>
        <form method="POST" action="">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="wiloke_subscribe_title"><?php esc_html_e('Title', 'wiloke'); ?></label>
                        </th>
                        <td><input id="wiloke_subscribe_title" name="wiloke_subscribe[title]" value="<?php echo esc_attr($aSubscribeSettings['title']); ?>" type="text" class="widefat"></td>
                    </tr>
                    <tr>
                        <th>
                            <label for="wiloke_subscribe_description"><?php esc_html_e('Description', 'wiloke'); ?></label>
                        </th>
                        <td><input id="wiloke_subscribe_description" name="wiloke_subscribe[description]" value="<?php echo esc_attr($aSubscribeSettings['description']); ?>" type="text" class="widefat"></td>
                    </tr>
                    <tr>
                        <th>
                            <label for="wiloke_subscribe_thanks"><?php esc_html_e('Thanks for subscribing', 'wiloke'); ?></label>
                        </th>
                        <td><input id="wiloke_subscribe_thanks" name="wiloke_subscribe[thanks]" value="<?php echo esc_attr(stripslashes($aSubscribeSettings['thanks'])); ?>" type="text" class="widefat"></td>
                    </tr>
                    <tr class="wrapper">
                        <th>
                            <label for="pi_mailchimp_api_key"><?php esc_html_e('API key', 'wiloke'); ?></label> <br>
                            <span class="help"><a href="https://admin.mailchimp.com/account/api-key-popup" target="_blank"><?php  esc_html_e('How to get mailchimp', 'wiloke'); ?></a></span>
                        </th>
                        <td><input id="pi_mailchimp_api_key" name="pi_mailchimp[api_key]" value="<?php echo esc_attr($mailchimpAPI); ?>" type="text"><button id="pi_get_list_id" class="button button-primary"><?php esc_html_e('Get Lists', 'wiloke'); ?></button></td>
                    </tr>
                    <tr class="pi_mailchimp_lists">
                        <th><label for="pi_mailchimp_lists"><?php esc_html_e('List ID', 'wiloke') ?></label></th>
                        <td>
                            <select name="pi_mailchimp[list_id]" id="pi_mailchimp_lists" class="pi_append_mailchimp_lists">
                                <?php if ( !empty($aLists) ) : ?>
                                    <?php
                                    foreach ( $aLists as $key => $listName ) :
                                        ?>
                                        <option value="<?php echo esc_attr($key); ?>" <?php selected($selected, $key) ?>><?php echo esc_html($listName); ?></option>
                                        <?php
                                    endforeach;
                                    ?>
                                <?php else: ?>
                                    <option value="0">---</option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td><input value="<?php esc_html_e('Save', 'wiloke'); ?>" type="submit" class="button button-primary"></td>
                    </tr>
                </tbody>
            </table>
        </form>
    <?php
    }
}

/**
 * Parse and get the list of mailchimp
 */
if ( !function_exists('wiloke_save_mailchimp_info') ){
    add_action('wp_ajax_pi_mailchimp_get_lists', 'wiloke_save_mailchimp_info');
    function wiloke_save_mailchimp_info()
    {
        if( isset($_POST['api_key']) && !empty($_POST['api_key']) )
        {
            $current = get_option('pi_mailchimp_api_key');

            if ( !$current || $_POST['api_key'] != $current )
            {
                if ( !class_exists('MailChimp') )
                {
                    include plugin_dir_path(__FILE__) . 'mailchimp/Mailchimp.php';
                }

                $MailChimp = new MailChimp($_POST['api_key']);

                $data = $MailChimp->call('lists/list');

                $lists=array();

                if( is_array($data) && is_array($data['data']) )
                {
                    foreach($data['data'] as $item)
                    {
                        $lists[$item['id']]=$item['name'];
                    }
                }

                if( count($lists) > 0 )
                {
                    update_option('pi_mailchimp_lists',$lists);
                    update_option('pi_mailchimp_api_key', $_POST['api_key']);
                    echo json_encode(array('type'=>'success','data'=>json_encode($lists)));
                }else{
                    echo json_encode(array('type'=>'error','msg'=>esc_html__('Can not get list from your MailChimp', 'wiloke')));
                }
            }else{
                echo json_encode(array('type'=>'error','msg'=>esc_html__('This api key is already exist!', 'wiloke')));
            }
        }else{
            echo json_encode(array('type'=>'error','msg'=>esc_html__('Can not get list from your MailChimp', 'wiloke')));
        }

        wp_die();
    }
}


/**
 * handle on front-end
 */
if ( !function_exists('wiloke_mailchimp_subscribe') ) {
    add_action( 'wp_ajax_wiloke_mailchimp_subscribe', 'wiloke_mailchimp_subscribe');
    add_action( 'wp_ajax_nopriv_wiloke_mailchimp_subscribe','wiloke_mailchimp_subscribe');
    function wiloke_mailchimp_subscribe()
    {
	    if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
		    wp_send_json_error(esc_html__('Wrong Security code', 'wiloke'));
	    }

	    if ( !isset($_POST['email']) || empty($_POST['email']) || !is_email($_POST['email']) ){
		    wp_send_json_error(esc_html__('You entered an invalid email. Please try with another', 'wiloke'));
	    }

	    $mailchimpAPI = get_option('pi_mailchimp_api_key');

        if ( empty($mailchimpAPI) )
        {
            if ( current_user_can('edit_theme_options') ) {
                wp_send_json_error(esc_html__('You haven\'t configured MailChimp yet!', 'wiloke'));
            }else{
                wp_send_json_error(esc_html__('Oops! Something went wrong. Please feedback this issue to the administrator.', 'wiloke'));
            }

        }else{
            if ( !class_exists('MailChimp') )
            {
                include plugin_dir_path(__FILE__) . 'mailchimp/Mailchimp.php';
            }

            $MailChimp = new MailChimp($mailchimpAPI);
            $result = $MailChimp->call('lists/subscribe', array(
                'id'                => get_option('pi_mailchimp_listid'),
                'email'             => array('email'=>$_POST['email']),
                'double_optin'      => true,
                'update_existing'   => true,
                'replace_interests' => false,
                'send_welcome'      => false,
            ));

            if(isset($result['status']) && $result['status']=='error')
            {
	            wp_send_json_error(esc_html__('Oops! Something went wrong. Please feedback this issue to the administrator.', 'wiloke'));
            }

	        $aSubscribeSettings = get_option('wiloke_subscribe_settings');
            wp_send_json_success(stripslashes($aSubscribeSettings['thanks']));
        }
    }
}
