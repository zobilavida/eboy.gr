<?php
namespace WilokeListGoFunctionality\Register;
$sessionID = session_id();
if ( empty($sessionID) && (!is_admin() || (isset($_GET['page']) && ($_GET['page'] === 'detail'))) ){
    session_start();
}

use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory as PaymentHistory;
use WilokeListGoFunctionality\AlterTable\AlterTablePackageStatus as PackageStatus;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentRelationships;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\Payment\Payment;
use WilokeListGoFunctionality\Payment\CheckPayment;

class RegisterWilokeSubmission implements RegisterInterface{
	public $parentSlug = 'wiloke-submission';
	public $invoicesSlug = 'invoices';
	public $settingsSlug = 'settings';
	public $detailSlug = 'detail';
	public $postPerPages = 10;
	protected $_sessionKey = 'wiloke_listgo_save_payment_ID';
	public static $submissionConfigurationKey = 'wiloke_submission_configuration';
	public $aPaymentStatus = array(
        'any'       => 'Any',
        'completed' => 'Completed',
        'processing'=> 'Processing',
        'pending'   => 'Pending',
        'canceled'  => 'Canceled',
        'failed'    => 'Failed'
    );
	public $aFilterByDate = array(
        'any'       => 'Any',
	    'this_week' => 'This Week',
        'this_month'=> 'This Month',
        'period'    => 'Period'
    );
	public $total = 0;
	public $addNewOrder = '';
	public $editOrder = '';
	public $aPackages = array();
	protected $_aInvoices = array();
	public $aErrors = array();

	public function __construct() {
		add_action('init', array($this, 'init'));
		add_action('admin_init', array($this, 'adminInit'));
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'), 10, 1);
		add_action('wp_ajax_wiloke_payment_export', array($this, 'exportFile'));
		add_action('wp_ajax_wiloke_submission_update_customer_order', array($this, 'updateCustomerOrder'));
		add_action('wp_ajax_wiloke_submission_add_new_order', array($this, 'addNewOrder'));
		add_action('wp_ajax_wiloke_submission_install_pages', array($this, 'installPages'));
	}

	public function init(){
		$this->setDefault();
    }

	public function adminInit(){
		$this->addNewOrder = admin_url('admin.php?page='.$this->detailSlug.'&type=addneworder');
		$this->editOrder = admin_url('admin.php?page='.$this->detailSlug.'&type=editorder&payment_ID=');
    }

    public static function setDefaultSubmissionPages(){
	    $aSubmissionPages = \Wiloke::getOption(self::$submissionConfigurationKey);
	    if ( !empty($aSubmissionPages) ){
	        return false;
        }

	    $aPages = array(
		    'package'   => array(
			    'post_title'    => esc_html__('Wiloke Package', 'wiloke'),
			    'post_content'  => ''
		    ),
		    'addlisting'=> array(
			    'post_title'    => esc_html__('Wiloke Submission', 'wiloke'),
			    'post_content'  => '',
			    'page_template' => 'wiloke-submission/addlisting.php'
		    ),
		    'checkout'  => array(
			    'post_title'    => esc_html__('Wiloke Checkout', 'wiloke'),
			    'post_content'  => '',
			    'page_template' => 'wiloke-submission/checkout.php'
		    ),
		    'myaccount' => array(
			    'post_title'    => esc_html__('Wiloke My Account', 'wiloke'),
			    'post_content'  => '',
			    'page_template' => 'wiloke-submission/myaccount.php'
		    ),
		    'thankyou'  => array(
			    'post_title'    => esc_html__('Wiloke Thank You', 'wiloke'),
			    'post_content'  => 'Thanks for submitting with ' . get_option('blogname') . '!. We will mail to you when your article be approved.',
			    'page_template' => 'wiloke-submission/payment-thankyou.php'
		    ),
		    'cancel'    => array(
			    'post_title'    => esc_html__('Wiloke Cancel', 'wiloke'),
			    'post_content'  => 'Thanks for submitting with ' . get_option('blogname') . '!. It seems not right time but feel free come back when you are ready. Hope to see you soon ;)!',
			    'page_template' => 'wiloke-submission/payment-cancel.php'
		    )
	    );

	    $aPostIDs = array();
        foreach ( $aPages as $key => $aArgs ){
            $aArgs['post_type'] = 'page';
            $aArgs['post_status'] = 'publish';
            $aPostIDs[$key] = wp_insert_post($aArgs);
        }

	    \Wiloke::updateOption(self::$submissionConfigurationKey, $aPostIDs);
	}
    
    protected function setDefault(){
	    if ( !get_option(self::$submissionConfigurationKey) ){
		    global $WilokeListGoFunctionalityApp;

		    $aDefault = array();
		    foreach ( $WilokeListGoFunctionalityApp['settings']['submission']['fields'] as $aField ){
			    if ( isset($aField['name']) ){
			        $fieldName = str_replace(array('wiloke_listgo[', ']'), array('', ''), $aField['name']);
				    $aDefault[$fieldName] = isset($aField['default'])  ? $aField['default'] : '';
			    }
		    }

		    update_option(self::$submissionConfigurationKey, json_encode($aDefault));
        }
    }

	public function enqueueScripts($hook){
	    wp_enqueue_style('wiloke-submission-general', plugin_dir_url(__FILE__) . '../../admin/css/submission-general.css', array(), WILOKE_LISTGO_FC_VERSION);
		wp_enqueue_script('wiloke-submission-general', plugin_dir_url(__FILE__) . '../../admin/js/wiloke-submission-general.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);

		if (  strpos($hook, $this->parentSlug) !== false ){
			if ( (strpos($hook, $this->invoicesSlug) !== false) ){
				wp_enqueue_style('jquery-ui-style', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
            }

            if ( (strpos($hook, $this->detailSlug) !== false) || ($hook === 'toplevel_page_' . $this->parentSlug) || (strpos($hook, $this->settingsSlug) !== false) || strpos($hook, $this->invoicesSlug) ){
			    wp_dequeue_script('sematic-selection-ui');
				wp_enqueue_style('sematic-ui', plugin_dir_url(__FILE__) . '../../admin/assets/semantic-ui/form.min.css');
				wp_enqueue_script('sematic-ui', plugin_dir_url(__FILE__) . '../../admin/assets/semantic-ui/semantic.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
            }

			wp_enqueue_script($this->parentSlug, plugin_dir_url(__FILE__) . '../../admin/js/wiloke-submission.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-tooltip'), WILOKE_LISTGO_FC_VERSION, true);
		}
	}

	public function register() {
		add_menu_page( esc_html__( 'Wiloke Submission', 'wiloke' ), esc_html__( 'Wiloke Submission', 'wiloke' ), 'administrator', 'wiloke-submission', array($this, 'submissionArea'), 'dashicons-hammer', 29 );
		add_submenu_page($this->parentSlug, esc_html__('Invoices', 'wiloke'), esc_html__('Invoices', 'wiloke'), 'administrator', $this->invoicesSlug, array($this, 'showInvoices'));
		add_submenu_page($this->parentSlug, esc_html__('Settings', 'wiloke'), esc_html__('Settings', 'wiloke'), 'administrator', $this->settingsSlug, array($this, 'settingsArea'));
		add_submenu_page($this->parentSlug, esc_html__('Submission Detail', 'wiloke'), '', 'administrator', $this->detailSlug, array($this, 'detailArea'));
	}

	/**
	 * Install Wiloke Submission Pages
     * @since 1.0
	 */
	public function installPages(){
	    if ( !current_user_can('edit_theme_options') ){
	        wp_send_json_error(
                array(
                    'message' => esc_html__('You do not have permission to access this page!', 'wiloke')
                )
            );
        }

        $aPages = array(
            'package'   => array(
                'post_title'    => esc_html__('Wiloke Package', 'wiloke'),
                'post_content'  => '',
                'page_template' => 'default'
            ),
            'addlisting'=> array(
                'post_title'    => esc_html__('Wiloke Submission', 'wiloke'),
                'post_content'  => '',
                'page_template' => 'wiloke-submission/addlisting.php'
            ),
            'checkout'  => array(
                'post_title'    => esc_html__('Wiloke Checkout', 'wiloke'),
                'post_content'  => '',
                'page_template' => 'wiloke-submission/checkout.php'
            ),
            'myaccount' => array(
                'post_title'    => esc_html__('Wiloke My Account', 'wiloke'),
                'post_content'  => '',
                'page_template' => 'wiloke-submission/myaccount.php'
            ),
            'thankyou'  => array(
                'post_title'    => esc_html__('Wiloke Thank You', 'wiloke'),
                'post_content'  => 'Thanks for submitting with ' . get_option('blogname') . '!. We will mail to you when your article be approved.',
                'page_template' => 'wiloke-submission/payment-thankyou.php'
            ),
            'cancel'    => array(
                'post_title'    => esc_html__('Wiloke Cancel', 'wiloke'),
                'post_content'  => 'Thanks for submitting with ' . get_option('blogname') . '!. It seems not right time but feel free come back when you are ready. Hope to see you soon ;)!',
                'page_template' => 'wiloke-submission/payment-cancel.php'
            )
        );

	    $aPostIDs = array();
        $aSubmissionPages = \Wiloke::getOption(self::$submissionConfigurationKey);

        if ( empty($aSubmissionPages) ){
            foreach ( $aPages as $key => $aArgs ){
	            $aArgs['post_type'] = 'page';
	            $aArgs['post_status'] = 'publish';
	            $aPostIDs[$key] = wp_insert_post($aArgs);
            }
        }else{
            foreach ( $aPages as $key => $aArgs ){
                if ( !isset($aSubmissionPages[$key]) || empty($aSubmissionPages[$key]) ){
	                $oPage = get_posts(array(
                        'post_type'  => 'page',
		                'fields'     => 'ids',
		                'nopaging'   => true,
		                'meta_key'   => '_wp_page_template',
		                'meta_value' => $aArgs['page_template']
	                ));
	                $aArgs['post_type']     = 'page';
	                $aArgs['post_status']   = 'publish';

	                if ( !empty($oPage) ){
		                $postID = end($oPage);
		                wp_update_post(
                            array(
                                'ID' => $postID,
                                'post_status' => 'publish'
                            )
                        );
		                $aPostIDs[$key] = $postID;
                    }else {
		                $aPostIDs[$key] = wp_insert_post($aArgs);
                    }
                }else{
	                wp_update_post(
		                array(
			                'ID' => $aSubmissionPages[$key],
			                'post_status' => 'publish'
		                )
	                );
                }
            }
        }

        if ( empty($aPostIDs) ) {
            wp_send_json_success(
                array(
                    'message' => esc_html__('You have already installed all required pages before.', 'wiloke')
                )
            );
        }

		$aSubmissionPages = empty($aSubmissionPages) ? $aPostIDs : array_merge($aSubmissionPages, $aPostIDs);
        \Wiloke::updateOption(self::$submissionConfigurationKey, $aSubmissionPages);

        $msg = '<div class="header">'. esc_html__('Congratulations! Your setup has been successfully.', 'wiloke') . '</div>';
		$msg .= '<p>'. esc_html__('Here are list of pages have been installed: ', 'wiloke') . '</p>';
        $msg .= '<ul class="list">';
        foreach ( $aPostIDs as $key => $postID ){
	        $msg .= '<li><a href="'.esc_url(admin_url('post.php?post='.$postID.'&action=edit')).'">' .get_the_title($postID). '</a></li>';
        }
		$msg .= '</ul>';
		$msg .= '<p class="error ui">' . esc_html__('One more thing, The pricing page - Where you show your packages  -  requires at least one package. So if you have not created any package yet before, please click on Pricings -> Creating some packages. Then go to Pages -> Pricing Tables page and insert those packages to this page. Please refer to Wiloke Guide -> FAQs -> Looking for Creating Package page to know more.', 'wiloke') . '</p>';

		wp_send_json_success(
            array(
                'message' => $msg
            )
        );
    }

	public function submissionArea(){
        ?>
        <div id="wiloke-submission-wrap" class="wrap">
            <h3 class="header"><?php esc_html_e('Tools', 'wiloke'); ?></h3>

            <h4 class="header ui dividing"><?php esc_html_e('Install Wiloke Submission Pages', 'wiloke'); ?></h4>
            <form id="ws-install-pages" action="<?php echo esc_url(admin_url('admin.php?page='.$this->parentSlug)); ?>" class="ui form">
                <?php
                    \WilokeHtmlHelper::semantic_render_desc_field(
                        array(
                            'status' => 'info',
                            'desc' => esc_html__('This tool will install all the missing Wiloke Submission pages. Pages already defined and set up will not be replaced.', 'wiloke')
                        )
                    );
                    \WilokeHtmlHelper::semantic_render_submit(
                        array(
                            'name' => esc_html__('Install', 'wiloke')
                        )
                    );
                ?>
            </form>
        </div>
        <?php
    }

	public function save(){
	    if ( !current_user_can('administrator') ){
            return false;
        }

		if ( isset($_POST['wiloke_nonce_field']) && !empty($_POST['wiloke_nonce_field']) && wp_verify_nonce($_POST['wiloke_nonce_field'], 'wiloke_nonce_action') ){
			$options = json_encode($_POST['wiloke_listgo']);
			update_option(self::$submissionConfigurationKey, $options);
		}
	}

    public function addNewOrder(){
	    if ( !current_user_can('edit_theme_options') ){
		    wp_send_json_error(
			    array(
				    'message' => esc_html__('You do not permission to edit this page.', 'wiloke')
			    )
		    );
	    }

	    if ( !isset($_POST['package_ID']) || empty($_POST['package_ID']) ){
		    wp_send_json_error(
			    array(
				    'add_new_order_packageid' => esc_html__('Please choose an add listing package.', 'wiloke')
			    )
		    );
	    }

	    if ( !isset($_POST['user_ID']) || empty($_POST['user_ID']) ){
		    wp_send_json_error(
			    array(
				    'add_new_order_user_ID' => esc_html__('The user is required', 'wiloke')
			    )
		    );
	    }

	    if ( !isset($_POST['order_status']) || empty($_POST['order_status']) ){
		    wp_send_json_error(
			    array(
				    'add_new_order_status' => esc_html__('Order Status is required', 'wiloke')
			    )
		    );
	    }

	    $instPayMent = new CheckPayment();
        $oResult = $instPayMent->manuallyAddPackage($_POST['package_ID'], $_POST['user_ID'], $_POST['order_status']);

        if ( is_wp_error($oResult) ){
            wp_send_json_error(
                array(
                    'message' => $oResult->get_error_message()
                )
            );
        }else{
            wp_send_json_success(
                array(
                    'message' => esc_html__('This package has been added', 'wiloke'),
	                'redirect'=> admin_url('admin.php?page=detail&invoice_ID='.$oResult->payment_ID)
                )
            );
        }
    }

    public function updateCustomerOrder(){
	    if ( !current_user_can('edit_theme_options') ){
	        wp_send_json_error(
                array(
	                'message' => esc_html__('You do not permission to edit this page.', 'wiloke')
                )
            );
        }

        if ( !isset($_POST['new_package']) || empty($_POST['new_package']) ){
	        wp_send_json_error(
		        array(
			        'new_package_name' => esc_html__('Please choose a package.', 'wiloke')
                )
            );
        }

	    if ( !isset($_POST['customer_ID']) || empty($_POST['customer_ID']) ){
		    wp_send_json_error(
			    array(
				    'message' => esc_html__('The user id does not exist!', 'wiloke')
			    )
		    );
	    }

	    if ( !isset($_POST['payment_ID']) || empty($_POST['payment_ID']) ){
		    wp_send_json_error(
			    array(
				    'message' => esc_html__('The payment ID is required!', 'wiloke')
			    )
		    );
	    }

	    if ( !isset($_POST['new_order_status']) || empty($_POST['new_order_status']) ){
		    wp_send_json_error(
			    array(
				    'new_package_name' => esc_html__('Please assign a status for this order', 'wiloke')
			    )
		    );
	    }

	    if ( $_POST['new_order_status'] === $_POST['current_order_status'] || $_POST['new_package'] === $_POST['current_package'] ){
	        wp_send_json_success(
                array(
                    'message' => esc_html__('Your update has been successfully', 'wiloke')
                )
            );
        }

        $instCheckPayment = new CheckPayment();
	    $oError = $instCheckPayment->manuallyUpdatePackage($_POST['new_package'], $_POST['customer_ID'], $_POST['new_order_status'], $_POST['payment_ID']);

	    if ( is_wp_error($oError) ){
	        wp_send_json_error(
                array(
                    'message' => $oError->get_error_message()
                )
            );
        }

        wp_send_json_success(
            array(
                'message' => esc_html__('Your update has been successfully', 'wiloke')
            )
        );
    }

	public function detailArea(){
	    global $WilokeListGoFunctionalityApp;
	    if ( isset($_GET['type']) && (($_GET['type'] === 'addneworder') || ($_GET['type'] === 'editorder'))  ) :
            if ( $_GET['type'] === 'addneworder' ){
	            unset($_SESSION[$this->_sessionKey]);
            }

            if ( isset($_REQUEST['payment_ID']) && !empty($_REQUEST['payment_ID']) ){
	            $_SESSION[$this->_sessionKey] = $_REQUEST['payment_ID'];
            }
        ?>
        <div id="wiloke-submission-wrapper" class="wrap">
            <a class="wiloke-submission-addnew" href="<?php echo esc_url($this->addNewOrder); ?>"><?php esc_html_e('Add New order', 'wiloke'); ?></a>
            <form id="wiloke-submission-add-new-order" class="form ui" action="#" method="POST">
                <?php
	            \WilokeHtmlHelper::semantic_render_desc_field(
		            array(
			            'desc_id' => 'wiloke-submission-message-after-addnew',
			            'desc'    => '',
			            'status'  => 'error hidden'
		            )
	            );

                wp_nonce_field('wiloke_add_new_order_action', 'wiloke_add_new_order_nonce');
                foreach ( $WilokeListGoFunctionalityApp['settings']['addneworder']['fields'] as $aField ){
	                if ( $aField['type'] !== 'open_table' && $aField['type'] !== 'close_table' && $aField['type'] !== 'submit' && $aField['type'] !== 'desc' ){
		                $name = str_replace(array('wiloke_submission_order', '[', ']'), array('', '', ''), $aField['name']);
		                $aField['value'] = isset($aData[$name]) ? $aData[$name] : $aField['default'];
	                }

	                switch ($aField['type']){
		                case 'text';
			                \WilokeHtmlHelper::semantic_render_text_field($aField);
			                break;
		                case 'select_post';
		                case 'select_ui';
			                \WilokeHtmlHelper::semantic_render_select_ui_field($aField);
			                break;
		                case 'select_user';
			                \WilokeHtmlHelper::semantic_render_select_user_field($aField);
			                break;
		                case 'select':
			                \WilokeHtmlHelper::semantic_render_select_field($aField);
			                break;
		                case 'submit':
			                \WilokeHtmlHelper::semantic_render_submit($aField);
			                break;
		                case 'desc';
			                \WilokeHtmlHelper::semantic_render_desc_field($aField);
			                break;
	                }
                }
                ?>
            </form>
        </div>
        <?php
	        return false;
        endif;

	    $invoiceID = isset($_REQUEST['invoice_ID']) ? $_REQUEST['invoice_ID'] : '';
	    if ( empty($invoiceID) ){
	        return false;
        }

        global $wpdb;
	    $tblName = $wpdb->prefix . PaymentHistory::$tblName;

	    $aDetails = $wpdb->get_row(
	      $wpdb->prepare(
	              "SELECT * FROM $tblName WHERE ID=%d",
              $invoiceID
          ),
            ARRAY_A
        );

        ?>
        <div id="wiloke-submission-wrapper" class="wrap">
            <a class="wiloke-submission-addnew" href="<?php echo esc_url($this->addNewOrder); ?>"><?php esc_html_e('Add New order', 'wiloke'); ?></a>
            <?php if ( empty($aDetails) ) : ?>
                <p><?php esc_html_e('This order does not exist', 'wiloke'); ?></p>
            <?php
                else:
	            $userID = $aDetails['user_ID'];
                $IPAddress = get_user_meta($userID, 'wiloke_user_IP', true);
	            $oUser    = (object)\Wiloke::getUserMeta($userID);
            ?>
                <form id="wiloke-submission-change-customer-order" class="form ui" action="" method="POST">
                    <h2 class="ui dividing header"><?php \Wiloke::wiloke_kses_simple_html( sprintf(__('Order #%d details.', 'wiloke'), $aDetails['ID']), false ); ?></h2>
                    <?php
                    \WilokeHtmlHelper::semantic_render_desc_field(
	                    array(
                            'desc_id' => 'wiloke-submission-message-after-update',
		                    'desc'    => esc_html__('Your update has been successfully', 'wiloke'),
                            'status'  => 'success hidden'
	                    )
                    );

                    echo '<div class="two fields">';
                    \WilokeHtmlHelper::render_hidden_field(
	                    array(
		                    'name'          => 'wiloke_submission_order[payment_id]',
		                    'value'         => $aDetails['ID']
	                    )
                    );
                    \WilokeHtmlHelper::semantic_render_text_field(
                        array(
                            'id'            => 'payment_method',
                            'name'          => 'payment_method',
                            'heading'       => esc_html__('Payment method', 'wiloke'),
                            'value'         => $aDetails['method'],
                            'is_readonly'   => true
                        )
                    );

                    \WilokeHtmlHelper::semantic_render_text_field(
                        array(
                            'id'            => 'customer_ip',
                            'name'          => 'customer_ip',
                            'heading'       => esc_html__('Customer IP', 'wiloke'),
                            'value'         => $IPAddress,
                            'is_readonly'   => true
                        )
                    );
                    echo '</div>';

                    echo '<div class="two fields">';
                    \WilokeHtmlHelper::render_hidden_field(
	                    array(
		                    'name'          => 'wiloke_submission_order[customer_id]',
		                    'value'         => $oUser->ID
	                    )
                    );

                    \WilokeHtmlHelper::semantic_render_text_field(
                        array(
                            'id'            => 'customer_name',
                            'name'          => 'customer_name',
                            'heading'       => esc_html__('Customer', 'wiloke'),
                            'value'         => $oUser->display_name,
                            'is_readonly'   => true,
                            'desc_status'   => 'info',
                            'desc'          => \Wiloke::wiloke_kses_simple_html(sprintf(__('<a href="%s">Check Customer Information</a>', 'wiloke'), esc_url(admin_url('user-edit.php?user_id='.$userID))), true)
                        )
                    );

                    \WilokeHtmlHelper::semantic_render_text_field(
                        array(
                            'id'            => 'customer_email',
                            'name'          => 'customer_email',
                            'heading'       => esc_html__('Customer Email', 'wiloke'),
                            'value'         => $oUser->user_email,
                            'is_readonly'   => true,
                            'desc_status'   => 'info',
                            'desc'          => \Wiloke::wiloke_kses_simple_html(sprintf(__('<a href="mailto:%s">Mail to customer</a>', 'wiloke'), esc_url(admin_url('user-edit.php?user_id='.$oUser->user_email))), true)
                        )
                    );
                    echo '</div>';

                    \WilokeHtmlHelper::semantic_render_text_field(
                        array(
                            'id'            => 'order_date',
                            'name'          => 'order_date',
                            'heading'       => esc_html__('Order date', 'wiloke'),
                            'value'         => $aDetails['created_at'],
                            'is_readonly'   => true
                        )
                    );

                    $aStatus = $this->aPaymentStatus;
                    array_shift($aStatus);
                    \WilokeHtmlHelper::render_hidden_field(
                        array(
                            'name'  => 'wiloke_submission_order[current_order_status]',
                            'value' => $aDetails['status']
                        )
                    );
                    \WilokeHtmlHelper::semantic_render_select_field(
                        array(
                            'id'            => 'new_order_status',
                            'name'          => 'wiloke_submission_order[new_order_status]',
                            'heading'       => esc_html__('Order Status', 'wiloke'),
                            'desc'          => esc_html__('Change Order status is possbile', 'wiloke'),
                            'desc_status'   => 'info',
                            'value'         => $aDetails['status'],
                            'options'       => $aStatus
                        )
                    );
                    ?>

                    <h4 class="ui dividing header"><?php esc_html_e('Purchased Package Information: ', 'wiloke'); ?></h4>
                    <?php
                        $tblPackageStatus = $wpdb->prefix . PackageStatus::$tblName;
                        $aPackageStatus = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * FROM $tblPackageStatus WHERE payment_ID=%d",
                                $invoiceID
                            ),
                            ARRAY_A
                        );

                        $aPackageInformation = json_decode($aPackageStatus['package_information'], true);
                        $packageTitle = get_the_title($aDetails['package_ID']);
                        $packageTitle = $packageTitle ? $packageTitle : esc_html__('This package has been removed', 'wiloke');

                        echo '<div class="fields five">';
                        \WilokeHtmlHelper::semantic_render_text_field(
                            array(
                                'id'            => 'old_package_name',
                                'name'          => 'old_package_name',
                                'heading'       => esc_html__('Package Name', 'wiloke'),
                                'value'         => $packageTitle,
                                'is_readonly'   => true
                            )
                        );

                        \WilokeHtmlHelper::semantic_render_text_field(
                            array(
                                'id'            => 'package_price',
                                'name'          => 'package_price',
                                'heading'       => esc_html__('Package Price', 'wiloke'),
                                'value'         => empty($aPackageInformation['price']) ? esc_html__('Free', 'wiloke') : Payment::renderPrice($aPackageInformation['price'], true),
                                'is_readonly'   => true
                            )
                        );

                        \WilokeHtmlHelper::semantic_render_text_field(
                            array(
                                'id'            => 'package_duration',
                                'name'          => 'package_duration',
                                'heading'       => esc_html__('Listing Duration', 'wiloke'),
                                'value'         => $aPackageInformation['duration'],
                                'is_readonly'   => true
                            )
                        );

                        \WilokeHtmlHelper::semantic_render_text_field(
                            array(
                                'id'            => 'package_availability',
                                'name'          => 'package_availability',
                                'heading'       => esc_html__('Listing Availability', 'wiloke'),
                                'value'         => $aPackageInformation['number_of_posts'],
                                'is_readonly'   => true
                            )
                        );

                        \WilokeHtmlHelper::semantic_render_text_field(
                            array(
                                'id'            => 'package_allow_to_publish_on_map',
                                'name'          => 'package_allow_to_publish_on_map',
                                'heading'       => esc_html__('Allow Publish On map', 'wiloke'),
                                'value'         => $aPackageInformation['publish_on_map'],
                                'is_readonly'   => true
                            )
                        );
                        echo '</div>';
                    ?>
                    <h4 class="ui dividing header"><?php esc_html_e('Change Package', 'wiloke'); ?></h4>
                    <?php
                        \WilokeHtmlHelper::render_hidden_field(
                            array(
                                'name'  => 'wiloke_submission_order[current_package_name]',
                                'value' => $aDetails['package_ID']
                            )
                        );
                        if ( $aDetails['package_type'] === 'pricing' ){
	                        \WilokeHtmlHelper::semantic_render_select_ui_field(
		                        array(
			                        'id'        => 'new_package_name',
			                        'name'      => 'wiloke_submission_order[new_package_name]',
			                        'post_type' => 'pricing',
			                        'heading'   => esc_html__('Add New Package', 'wiloke'),
			                        'value'     => $aDetails['package_ID'],
			                        'desc'      => esc_html__('Switch to new package for customer', 'wiloke'),
			                        'desc_status' => 'info'
		                        )
	                        );
                        }else{
	                        \WilokeHtmlHelper::semantic_render_select_ui_field(
		                        array(
			                        'id'        => 'new_package_name',
			                        'name'      => 'wiloke_submission_order[new_package_name]',
			                        'post_type' => 'event-pricing',
			                        'heading'   => esc_html__('Add New Package', 'wiloke'),
			                        'value'     => $aDetails['package_ID'],
			                        'desc'      => esc_html__('Switch to new package for customer', 'wiloke'),
			                        'desc_status' => 'info'
		                        )
	                        );
                        }
                    ?>

                    <?php
                    \WilokeHtmlHelper::semantic_render_submit(array(
                        'name' => esc_html__('Save Changes', 'wiloke')
                    ));
                    ?>
                </form>
            <?php endif; ?>
        </div>
        <?php
    }

    public static function getSettings(){
        $aOptions = get_option(self::$submissionConfigurationKey);
        $aOptions = !empty($aOptions) ? json_decode($aOptions, true) : array();
        return $aOptions;
    }

	public function settingsArea(){
		global $WilokeListGoFunctionalityApp;
		$this->save();
		$aOptions = get_option(self::$submissionConfigurationKey);
		$aOptions = !empty($aOptions) ? json_decode($aOptions, true) : array();
		?>
        <div id="wiloke-submission-wrapper" class="wrap">
            <form class="form ui" action="<?php echo esc_url(admin_url('admin.php?page='.$this->settingsSlug)); ?>" method="POST">
				<?php wp_nonce_field('wiloke_nonce_action', 'wiloke_nonce_field'); ?>
				<?php
				foreach ( $WilokeListGoFunctionalityApp['settings']['submission']['fields'] as $aField ){
					if ( $aField['type'] !== 'header' && $aField['type'] !== 'submit' && $aField['type'] !== 'desc' ){
						$name = str_replace(array('wiloke_listgo', '[', ']'), array('', '', ''), $aField['name']);
						$aField['value'] = isset($aOptions[$name]) ? $aOptions[$name] : $aField['default'];
					}

					switch ($aField['type']){
						case 'text';
							\WilokeHtmlHelper::semantic_render_text_field($aField);
							break;
						case 'select_post';
						case 'select_ui';
							\WilokeHtmlHelper::semantic_render_select_ui_field($aField);
							break;
						case 'select':
							\WilokeHtmlHelper::semantic_render_select_field($aField);
							break;
						case 'textarea':
							\WilokeHtmlHelper::semantic_render_textarea_field($aField);
							break;
						case 'submit':
							\WilokeHtmlHelper::semantic_render_submit($aField);
							break;
						case 'header':
							\WilokeHtmlHelper::semantic_render_header($aField);
							break;
						case 'desc';
							\WilokeHtmlHelper::semantic_render_desc($aField);
							break;
					}
				}
				?>
            </form>
        </div>
		<?php
	}

	public function exportFile(){
		if ( !current_user_can('edit_posts') ){
			return false;
		}
		$aQuery = json_decode(urldecode($_POST['args']), true);
		$this->_fetchPayMent($aQuery);

		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");

		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename=wiloke-payment-invoices-".date('Y-m-D') . '.csv');
		header("Content-Transfer-Encoding: binary");
		$csv_header = '';
		$csv_header .= 'ID, Customer Name, Customer Email, Total, Payment Method, Payment Status, Package Name, Date' . "\n";
		$csv_row ='';
		foreach ( $this->_aInvoices as $aPayment ){
		    $oUser = get_userdata($aPayment['user_ID']);
			$aPackageInformation = json_decode($aPayment['package_information'], true);
			$csv_row .= $aPayment['ID'] . ', ' . $oUser->display_name . ',' . $oUser->user_email . ', ' . $aPayment['total'] . ', ' .  $aPayment['method'] . ', ' . $aPayment['status'] . ', ' . $aPackageInformation['title'] . ', ' . $aPayment['created_at'] . "\n";
		}

		echo $csv_header . $csv_row;
		die();
    }

	public function getPackages(){

	    $query = new \WP_Query(
	        array(
                'post_type'         => 'pricing',
                'posts_per_page'    => -1,
                'post_status'       => 'publish'
            )
        );

	    if ( $query->have_posts() ){
		    $this->aPackages['any'] = 'Any';
	        while ($query->have_posts()){
	            $query->the_post();
	            $this->aPackages[$query->post->ID] = $query->post->post_title;
            }
        }
    }
    
    public function getPaymentStatusIcon($status){
        switch ($status){
            case 'completed':
                $icon = 'dashicons-yes';
                break;
	        case 'pending':
		        $icon = 'dashicons-clock';
		        break;
	        case 'processing':
		        $icon = 'dashicons-update';
		        break;
            case 'canceled':
                $icon = 'dashicons-warning';
                break;
	        case 'failed':
            case 'denied':
		        $icon = 'dashicons-thumbs-down';
		        break;
            default:
                $icon = 'dashicons-visibility';
                break;
        }
        return $icon;
    }

	public function showInvoices(){
		$this->fetchPayMent();
		$pagination = absint(ceil($this->total/$this->postPerPages));

		$paged = isset($_REQUEST['paged']) && !empty($_REQUEST['paged']) ? absint($_REQUEST['paged']) : 1;
		$aRequest = isset($_REQUEST) ? $_REQUEST : array();
		?>
		<div id="listgo-table-wrapper" style="margin: 30px auto">
			<h2><?php esc_html_e('Invoices', 'wiloke'); ?></h2>
            <div class="searchform">
                <form class="form ui" action="<?php echo esc_url(admin_url('admin.php')); ?>" method="GET">
                    <div class="equal width fields">
                        <input type="hidden" name="paged" value="<?php echo esc_attr($paged); ?>">
                        <input type="hidden" name="page" value="<?php echo esc_attr($this->invoicesSlug); ?>">
                        <?php if ( !empty($this->aPackages) ) : ?>
                        <div class="search-field field">
                            <label for="package_id"><?php esc_html_e('Package', 'wiloke'); ?></label>
                            <select id="package_id" class="ui dropdown" name="package_id">
                                <?php foreach ($this->aPackages as $ID => $title):
                                    $selected = isset($_REQUEST['package_id']) && (absint($_REQUEST['package_id']) === $ID) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo esc_attr($ID); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($title); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="search-field field">
                            <label for="payment_status"><?php esc_html_e('Status', 'wiloke'); ?></label>
                            <select id="payment_status" class="ui dropdown" name="payment_status">
                                <?php
                                    foreach ($this->aPaymentStatus as $status => $title) :
                                    $selected = isset($_REQUEST['payment_status']) && $_REQUEST['payment_status'] === $status ? 'selected' : '';
                                ?>
                                    <option value="<?php echo esc_attr($status); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($title); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="search-field field">
                            <label for="filter-by-date"><?php esc_html_e('Date', 'wiloke'); ?></label>
                            <select id="filter-by-date" class="ui dropdown" name="date">
                                <?php foreach ($this->aFilterByDate as $date => $title):
                                    $selected = isset($_REQUEST['date']) && $_REQUEST['date'] === $date ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo esc_attr($date); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($title); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="filter-by-period" class="search-field field">
                            <input class="wiloke_datepicker" type="text" name="from" value="" placeholder="<?php echo esc_html_e('Date Start', 'wiloke') ?>">
                            <input class="wiloke_datepicker" type="text" name="to" value="" placeholder="<?php echo esc_html_e('Date End', 'wiloke') ?>">
                        </div>
                        <div class="search-field field">
                            <label for="posts_per_page"><?php esc_html_e('Posts Per Page', 'wiloke'); ?></label>
                            <input type="text" name="posts_per_page" value="<?php echo esc_attr($this->postPerPages); ?>">
                        </div>
                        <div class="search-field field">
                            <label for="posts_per_page"><?php esc_html_e('Apply', 'wiloke'); ?></label>
                            <input type="submit" class="button ui basic green" value="<?php esc_html_e('Filter', 'wiloke'); ?>">
                        </div>
                    </div>
                </form>
            </div>
<!--            wp-list-table widefat fixed striped posts-->
            <table id="listgo-table" class="ui striped table">
				<thead>
					<tr>
						<th class="invoices-id manage-column check-column">#</th>
						<th class="invoices-customer manage-column column-primary"><?php esc_html_e('Customer', 'wiloke'); ?></th>
						<th class="invoices-total manage-column"><?php esc_html_e('Total', 'wiloke'); ?></th>
						<th class="invoices-status manage-column"><?php esc_html_e('Status', 'wiloke'); ?></th>
						<th class="invoices-package manage-column"><?php esc_html_e('Package', 'wiloke'); ?></th>
						<th class="invoices-package manage-column"><?php esc_html_e('Type', 'wiloke'); ?></th>
						<th class="invoices-date manage-column"><?php esc_html_e('Date', 'wiloke'); ?></th>
						<th class="invoices-remove manage-column"><?php esc_html_e('Remove', 'wiloke'); ?></th>
					</tr>
				</thead>

                <tbody>
                    <?php if ( empty($this->_aInvoices) ) : ?>
                        <tr><td colspan="7" class="text-center"><strong><?php esc_html_e('There is no any payment yet', 'wiloke'); ?></strong></td></tr>
                    <?php else: ?>
                        <?php
                        foreach ( $this->_aInvoices as  $aInvoice ) :
                        $editLink = admin_url('admin.php') . '?page='.$this->detailSlug.'&invoice_ID='.$aInvoice['ID'];
                        ?>
                        <tr class="item">
                            <td class="invoices-id check-column manage-column"><a href="<?php echo esc_url($editLink); ?>" title="<?php esc_html_e('View Invoice Detail', 'wiloke'); ?>"><?php echo esc_html($aInvoice['ID']); ?></a></td>
                            <td class="invoices-customer manage-column column-primary" data-colname="<?php esc_html_e('Customer', 'wiloke'); ?>"><a title="<?php esc_html_e('View customer information', 'wiloke'); ?>" href="<?php echo esc_url(admin_url('user-edit.php?user_id='.$aInvoice['user_ID'])); ?>"><?php echo esc_html(get_user_meta($aInvoice['user_ID'], 'nickname', true)); ?></a></td>
                            <td class="invoices-total manage-column" data-colname="<?php esc_html_e('Total', 'wiloke'); ?>">
                                <a href="<?php echo esc_url($editLink); ?>" title="<?php esc_html_e('View Invoice Detail', 'wiloke'); ?>">
                                <?php
                                if ( empty($aInvoice['total']) ){
                                    esc_html_e('Free', 'wiloke');
                                }else{
                                    Payment::renderPrice($aInvoice['total']);
                                    echo esc_html__(' Via ', 'wiloke') . esc_html($aInvoice['method']);
                                }
                                ?>
                                </a>
                            </td>
                            <td class="invoices-status manage-column" data-colname="<?php esc_html_e('Status', 'wiloke'); ?>"><a href="<?php echo esc_url($editLink); ?>" title="<?php esc_html_e('View Invoice Detail', 'wiloke'); ?>"><?php echo esc_html($aInvoice['status']); ?></a></td>
                            <td class="invoices-package manage-column" data-colname="<?php esc_html_e('Package', 'wiloke'); ?>"><a href="<?php echo esc_url($editLink); ?>" title="<?php esc_html_e('View Invoice Detail', 'wiloke'); ?>"><?php echo get_the_title($aInvoice['package_ID']); ?></a></td>
                            <td class="invoices-package manage-column" data-colname="<?php esc_html_e('Package', 'wiloke'); ?>"><a href="<?php echo esc_url($editLink); ?>" title="<?php esc_html_e('View Invoice Detail', 'wiloke'); ?>"><?php echo esc_html($aInvoice['package_type']); ?></a></td>
                            <td class="invoices-date manage-column" data-colname="<?php esc_html_e('Date', 'wiloke'); ?>"><a href="<?php echo esc_url($editLink); ?>" title="<?php esc_html_e('View Invoice Detail', 'wiloke'); ?>"><?php echo esc_html($aInvoice['created_at']);  ?></a></td>
                            <td class="invoices-remove manage-column" data-colname="<?php esc_html_e('Remove', 'wiloke'); ?>">
                                <a class="js_delete_payment" href="<?php echo esc_url($editLink); ?>" title="<?php esc_html_e('Delete Payment', 'wiloke'); ?>" data-paymentID="<?php echo esc_attr($aInvoice['ID']); ?>"><?php esc_html_e('Remove', 'wiloke') ?></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
				</tbody>

				<?php if ( $pagination !== 1 && $pagination !== 0 ) : ?>
                <tfoot>
                    <tr>
                        <th colspan="8">
                            <div class="ui right floated pagination menu" style="padding-top: 0 !important;">
                                <?php for ($i = 1; $i <= $pagination; $i++ ) :
                                    $actived = $paged === $i ? 'active' : '';
                                    $aRequest['paged'] = $i;
                                    $aRequest['page']  = $this->invoicesSlug;
                                    $request = '';
                                    foreach ($aRequest as $key => $val){
                                        $request .= empty($request) ? $key.'='.$val :  '&'.$key.'='.$val;
                                    }
                                    ?>
                                    <a class="<?php echo esc_attr($actived); ?> item" href="<?php echo esc_url(admin_url('admin.php?'.$request)); ?>"><?php echo esc_html($i); ?></a>
                                <?php endfor; ?>
                            </div>
                        </th>
                    </tr>
                </tfoot>
				<?php endif; ?>

			</table>

            <div class="ui segment">
                <a class="button ui basic green" href="<?php echo esc_url($this->addNewOrder); ?>"><?php esc_html_e('Add New order', 'wiloke'); ?></a>
                <?php if ( !empty($this->_aInvoices) ) : ?>
                    <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST" style="display: inline-block;">
                        <input type="hidden" name="args" value="<?php echo esc_attr(urlencode(json_encode($_REQUEST))); ?>">
                        <input type="hidden" name="action" value="wiloke_payment_export">
                        <button class="js_export button ui basic purple"><?php esc_html_e('Export Payments', 'wiloke'); ?></button>
                    </form>
                <?php endif; ?>
            </div>

		</div>

		<?php
	}

	public function fetchPayMent(){
		$aQuery = isset($_REQUEST) ? $_REQUEST : array();
		$this->_fetchPayMent($aQuery);
	}

	private function _fetchPayMent($aQuery){
	    if ( !current_user_can('edit_posts') ){
	        return false;
        }

		global $wpdb;
        $paged = isset($aQuery['paged']) ? $aQuery['paged'] : 1;
		$tblPaymentHistory = $wpdb->prefix . PaymentHistory::$tblName;

		$tblPackageStatus = $wpdb->prefix . PackageStatus::$tblName;
		$offset = ($paged - 1)*$this->postPerPages;
		$this->postPerPages = isset($aQuery['posts_per_page']) && !empty($aQuery['posts_per_page']) ? $aQuery['posts_per_page'] : $this->postPerPages;

		$sql = "SELECT $tblPaymentHistory.*, $tblPackageStatus.package_information FROM $tblPaymentHistory LEFT JOIN $tblPackageStatus ON ($tblPaymentHistory.ID = $tblPackageStatus.payment_ID)";
        $concat = " WHERE";

		if ( isset($aQuery['package_id']) && $aQuery['package_id'] !== 'any' ){
            $sql .= $concat . " $tblPaymentHistory.package_ID=".esc_sql($aQuery['package_id']);
			$aParams[] = $aQuery['package_id'];
			$concat = " AND";
        }

        $additionalQuery = "";
		if ( isset($aQuery['payment_status']) && $aQuery['payment_status'] !== 'any' ){
			$additionalQuery .= $concat . " $tblPaymentHistory.status='".esc_sql($aQuery['payment_status'])."'";
			$concat = " AND";
		}

		if ( isset($aQuery['date']) && $aQuery['date'] !== 'any' ){
		    if ( $aQuery['date'] === 'this_week' ){
			    $additionalQuery .= $concat. " DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE()) DAY) <= $tblPaymentHistory.created_at";
            }elseif ( $aQuery['date'] === 'this_month' ){
			    $additionalQuery .= $concat. " $tblPaymentHistory.created_at >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE()) DAY) ";
            }else{
                if ( empty($aQuery['from']) ){
                    $from = date('Y-m-d');
                }else{

                    $from = date('Y-m-d', strtotime($aQuery['from']));
                }

                if ( empty($aQuery['to']) ){
	                $to = date('Y-m-d');
                }else{
	                $to = date('Y-m-d', strtotime($aQuery['to']));
                }

			    $additionalQuery .= $concat . " ($tblPaymentHistory.created_at BETWEEN '".esc_sql($from)."' AND '".esc_sql($to)."')";

            }
		}

		if ( !empty($additionalQuery) ){
		    $sql .= $additionalQuery;
        }

		$sql .= " ORDER BY $tblPaymentHistory.ID DESC LIMIT ".esc_sql($this->postPerPages)." OFFSET ".esc_sql($offset);

		$this->_aInvoices = $wpdb->get_results($sql,ARRAY_A);

		$this->getPackages();
		if ( !empty($this->_aInvoices) ){
			$this->getTotalPosts($additionalQuery);
		}
	}

	public function getTotalPosts($additionalQuery){
		global $wpdb;
		$tblName = $wpdb->prefix . PaymentHistory::$tblName;
		$additionalQuery = trim($additionalQuery);
		if ( strpos($additionalQuery, 'AND') === 0 ){
			$additionalQuery = ltrim($additionalQuery, 'AND');
			$additionalQuery = "WHERE" . $additionalQuery;
        }

        if ( !empty($additionalQuery) ){
	        $additionalQuery = " " . $additionalQuery;
        }

		$this->total = $wpdb->get_var(
			"SELECT COUNT(ID) FROM $tblName".$additionalQuery
		);
	}
}