<?php

if ( !defined('ABSPATH') )
{
    die();
}

/**
 * Wiloke Unlimited Widgets
 * This function contains the general functions, which be used in the each widget item
 */

if ( !class_exists('wilokeWidgetOptions')  )
{
    class wilokeWidgetOptions
    {
        public function __construct()
        {
            add_action('admin_menu', array($this, 'wiloke_register_menu'));
        }

        public function wiloke_register_menu()
        {
            add_options_page(esc_html__('Wiloke Instagram', 'wiloke'), esc_html__('Wiloke Instagram', 'wiloke'), 'edit_theme_options', 'wiloke-instagram', array($this, 'wiloke_instagram_settings'));
            add_options_page(esc_html__('Wiloke Twitter', 'wiloke'), esc_html__('Wiloke Twitter', 'wiloke'), 'edit_theme_options', 'wiloke-twitter', array($this, 'wiloke_twitter_settings'));
        }

        public function wiloke_instagram_settings()
        {
            if (current_user_can('edit_theme_options') && isset($_POST['instagram'])) {
                $this->wiloke_update_settings('_pi_instagram_settings', $_POST['instagram']);
            }

            $aInstagram = get_option('_pi_instagram_settings');
            $aInstagram = $aInstagram ? $aInstagram : array('userid' => '', 'profile_picture'=>'', 'access_token' => '', 'username'=>'', 'profile_picture', 'cache_interval'=>864000);
            $instagramRedirectUri = 'https://www.instagram.com/oauth/authorize/?client_id=54da896cf80343ecb0e356ac5479d9ec&scope=basic+public_content&redirect_uri=http://api.web-dorado.com/instagram/?return_url='.admin_url('options-general.php?page=wiloke-instagram') . '&response_type=token';

            if ( isset($_GET['access_token']) && !empty($_GET['access_token']) )
            {
                $url       = 'https://api.instagram.com/v1/users/self/?access_token='.$_GET['access_token'];
                $oResponse = wp_remote_get($url, array( 'timeout' => 120, 'httpversion' => '1.1' ) );

                if ( !empty($oResponse) && !is_wp_error($oResponse) )
                {
                    $oResponse = $oResponse['body'];
                    $oResponse = json_decode($oResponse);

                    if ( $oResponse->meta->code == 200 )
                    {
                        $aInstagram['username']         = $oResponse->data->username;
                        $aInstagram['userid']           = $oResponse->data->id;
                        $aInstagram['profile_picture']  = $oResponse->data->profile_picture;
                        $aInstagram['access_token']     = $_GET['access_token'];
                        update_option('_pi_instagram_settings', $aInstagram);
                    }
                }

                unset($_GET['access_token']);
            }

            ?>
            <form action="" method="POST">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="sign-in-with-instagram"><?php esc_html_e('Sign in with Instagram', 'wiloke'); ?></label></th>
                        <td>
                            <a id="sign-in-with-instagram" class="button button-primary" href="<?php echo esc_url($instagramRedirectUri); ?>"><?php esc_html_e('Do It', 'wiloke'); ?></a>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"></th>
                        <td>
                            <?php
                            if ( !empty($aInstagram['profile_picture']) )
                            {
                                ?>
                                <img style="width: 50px; height: 50px; border-radius: 100%;" src="<?php echo esc_url($aInstagram['profile_picture']); ?>" alt="Profile Picture" />
                                <?php
                            }
                            ?>
                            <input id="profilepicture" type="hidden" name="instagram[profile_picture]"
                                   value="<?php echo esc_url($aInstagram['profile_picture']); ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="userid"><?php esc_html_e('User ID', 'wiloke'); ?></label></th>
                        <td>
                            <input id="userid" type="text" name="instagram[userid]"
                                   value="<?php echo esc_attr($aInstagram['userid']); ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="username"><?php esc_html_e('User Name', 'wiloke'); ?></label></th>
                        <td>
                            <input id="username" type="text" name="instagram[username]"
                                   value="<?php echo esc_attr($aInstagram['username']); ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="access-token"><?php esc_html_e('Access Token', 'wiloke'); ?></label>
                        </th>
                        <td>
                            <input id="access-token" type="text" name="instagram[access_token]"
                                   value="<?php echo esc_attr($aInstagram['access_token']); ?>"/>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="cache-interval"><?php esc_html_e('Cache Interval', 'wiloke'); ?></label></th>
                        <td>
                            <input id="cache-interval" type="text" name="instagram[cache_interval]"
                                   value="<?php echo esc_attr($aInstagram['cache_interval']); ?>"/>
                            <p><?php esc_html_e('Leave empty to clear cache. Unit: mini seconds', 'wiloke'); ?></p>
                        </td>
                    </tr>

                    </tbody>
                    <tr>
                        <th></th>
                        <td><input type="submit" class="button button-primary"
                                   value="<?php esc_html_e('Save', 'wiloke'); ?>"></td>
                    </tr>
                </table>
            </form>
            <?php
        }

        public function wiloke_update_settings($key, $aData)
        {
            $aOldVal = get_option($key);
            $aData = !empty($aOldVal) ? array_merge($aOldVal, $aData) : $aData;
            update_option($key, $aData);
        }

        public function wiloke_twitter_settings()
        {
            if (current_user_can('edit_theme_options') && isset($_POST['twitter'])) {
                $this->wiloke_update_settings('_wiloke_twitter_settings', $_POST['twitter']);
            }
	        $aTwitter = get_option('_wiloke_twitter_settings');
            $aTwitter = $aTwitter ? $aTwitter : array( 'username'=>'wilokethemes', 'consumer_key' => '', 'consumer_secret' => '', 'access_token'=>'', 'access_token_secret'=>'', 'cache_interval'=>86400);

            ?>
            <form action="" method="POST">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="access-token"><?php esc_html_e('Username *', 'wiloke'); ?></label></th>
                        <td>
                            <input id="access-token" type="text" name="twitter[username]" value="<?php echo esc_attr($aTwitter['username']); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="access-token"><?php esc_html_e('Consumer Key *', 'wiloke'); ?></label></th>
                        <td>
                            <input id="access-token" type="text" name="twitter[consumer_key]" value="<?php echo esc_attr($aTwitter['consumer_key']); ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="access-token"><?php esc_html_e('Consumer Secret *', 'wiloke'); ?></label>
                        </th>
                        <td>
                            <input id="access-token" type="text" name="twitter[consumer_secret]" value="<?php echo esc_attr($aTwitter['consumer_secret']); ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="access-token"><?php esc_html_e('Access Token *', 'wiloke'); ?></label>
                        </th>
                        <td>
                            <input id="access-token" type="text" name="twitter[access_token]"  value="<?php echo esc_attr($aTwitter['access_token']); ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="access-token"><?php esc_html_e('Access Secret Token *', 'wiloke'); ?></label>
                        </th>
                        <td>
                            <input id="access-token" type="text" name="twitter[access_token_secret]" value="<?php echo esc_attr($aTwitter['access_token_secret']); ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="access-token"><?php esc_html_e('Cache Interval', 'wiloke'); ?></label>
                        </th>
                        <td>
                            <input id="access-token" type="text" name="twitter[cache_interval]" value="<?php echo esc_attr($aTwitter['cache_interval']); ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="access-token"><?php esc_html_e('Creating my twitter application.', 'wiloke'); ?></label>
                        </th>
                        <td>
                            <a href="http://blog.wiloke.com/how-to-get-twitter-api/" target="_blank">http://blog.wiloke.com/how-to-get-twitter-api/</a>
                        </td>
                    </tr>

                    </tbody>
                    <tr>
                        <th></th>
                        <td><input type="submit" class="button button-primary" value="<?php esc_html_e('Save', 'wiloke'); ?>"></td>
                    </tr>
                </table>
            </form>
            <?php
        }
    }

    new wilokeWidgetOptions;
}

