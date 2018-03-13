<?php
namespace WilokeListGoFunctionality\Register;
use WilokeListGoFunctionality\Submit\User as WilokeSubmissionUser;

class RegisterClaim implements RegisterInterface{
	public static $alreadySetClaimedTemplateKey = 'wiloke_already_set_claimed_template';
	public static $alreadyUpdatedPostAuthorToOdlClaimed = 'wiloke_already_update_post_author_to_claimed';
	public static $metaKey = 'wiloke_claim_info';
	public static $claimsOfListingKey = 'wiloke_claim_id_of_listing';
	public static $userClaimedListingKey = 'wiloke_user_claimed_listings';
	public static $listingClaimRelationshipKey = 'wiloke_listing_claim_relationship';
	public static $listingPreviousPostAuthorID = 'wiloke_claim_original_post_author_id';
	public static $postType = 'claim';
	public static $capability = 'edit_theme_options';
	public static $aErrors = array();

	public function __construct() {
		add_action('add_meta_boxes', array($this, 'registerMetaBoxes'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'), 10, 1);
		add_action('save_post_'.self::$postType, array($this, 'saveSettings'), 10, 2);
		add_action('wp_ajax_wiloke_delete_the_rest_claims', array($this, 'doDeleteRestClaims'));
		add_action('updated_postmeta', array($this, 'claimCollection'), 10, 4);
		add_action('added_post_meta', array($this, 'claimCollection'), 10, 4);
		add_action('before_delete_post', array($this, 'deleteRelatedClaims'), 10);
		add_action('admin_init', array($this, 'setClaimedTemplate'));
		add_filter('manage_'.self::$postType.'_posts_columns', array($this, 'addHeadColumns'));
		add_action( 'manage_'.self::$postType.'_posts_custom_column' , array($this, 'addCustomColumns'), 10, 2 );
//		add_action('admin_init', array($this, 'automaticallyUpdatePostAuthorForClaimed'));
	}

	public function addCustomColumns($column, $postID){
		if ( $column == 'claim_status' ) {
            $aClaimStatus = \Wiloke::getPostMetaCaching($postID, self::$metaKey);
			$editUrl = admin_url('post.php') . '?post='.$postID . '&amp;action=edit';
            switch ($aClaimStatus['status']){
                case 'decline':
                    echo '<a href="'.esc_url($editUrl).'" title="'.esc_html__('Declined', 'wiloke').'"><img src="'.esc_url(plugin_dir_url(dirname(__FILE__)) . '../public/source/img/claim-declined.png').'"></a>';
                    break;
	            case 'approved':
		            echo '<a href="'.esc_url($editUrl).'" title="'.esc_html__('Approved', 'wiloke').'"><img src="'.esc_url(plugin_dir_url(dirname(__FILE__)) . '../public/source/img/claim-approved.png').'"></a>';
		            break;
                default:
	                echo '<a href="'.esc_url($editUrl).'" title="'.esc_html__('Pending', 'wiloke').'"><img src="'.esc_url(plugin_dir_url(dirname(__FILE__)) . '../public/source/img/claim-pending.png').'"></a>';
                    break;
            }
		}
    }

	public function addHeadColumns($aColumns){
	    $aNewOrder = array();
	    foreach ($aColumns as $key => $column){
		    $aNewOrder[$key] = $column;
	        if ( $key === 'title' ){
		        $aNewOrder['claim_status'] =  esc_html__( 'Claim Status', 'wiloke' );
            }
        }

		return $aNewOrder;
    }

	public function automaticallyUpdatePostAuthorForClaimed(){
	    if ( !get_option(self::$alreadyUpdatedPostAuthorToOdlClaimed) ){
            $query = new \WP_Query(
                array(
                    'post_type' => self::$postType,
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                )
            );

            if ( $query->have_posts() ){
                while ($query->have_posts()){
                    $query->the_post();
	                $aClaimSettings = get_post_meta($query->post->ID, self::$metaKey);
	                if ( $aClaimSettings['status'] === 'approved' ){
	                    $oListing = get_post($aClaimSettings['listing']);
		                update_post_meta($aClaimSettings['listing'], self::$listingPreviousPostAuthorID, $oListing->post_author);
		                wp_update_post(
			                array(
				                'ID'          => $oListing->ID,
				                'post_author' => $aClaimSettings['claimed_by']
			                )
		                );
                    }

                }
            }
	        update_option(self::$alreadyUpdatedPostAuthorToOdlClaimed, true);
        }
    }

	public function setClaimedTemplate(){
		if ( !get_option(self::$alreadySetClaimedTemplateKey) ){
			update_option(self::$alreadySetClaimedTemplateKey, 'yes');
            $pageID = wp_insert_post(
                array(
                    'post_title'  => esc_html__('Edit Claimed', 'wiloke'),
                    'post_type'   => 'page',
                    'post_status' => 'publish'
                )
            );

            update_post_meta($pageID, '_wp_page_template', 'templates/edit-claimed.php');
            $aThemeOptions = get_option('wiloke_themeoptions');
            $aThemeOptions['listing_claimed_template'] = $pageID;
            update_option('wiloke_themeoptions', $aThemeOptions);
        }
    }

	public function deleteRelatedClaims($postID){
	    $postType = get_post_type($postID);
	    if ( $postType === 'listing' ){
		    $aExisted = get_post_meta($postID, self::$claimsOfListingKey, true);
		    if ( !empty($aExisted) ){
			    foreach( $aExisted as $claimID ){
				    wp_delete_post($claimID);
			    }
		    }
        }

        if (  $postType === self::$postType ){
	        $adminID = get_current_user_id();
	        $aClaimInfo = get_post_meta($postID, self::$metaKey, true);
	        $this->pluckListingOutOfUserListingRelationship($aClaimInfo['claimed_by'], $aClaimInfo['listing']);
	        $this->deleteClaimRelationShip($postID, $aClaimInfo);
	        do_action('wiloke/wiloke_submission/add_notification', $adminID, $aClaimInfo['claimed_by'], $aClaimInfo['listing'], 'declined_claim');
        }
    }

    public function deleteClaimRelationShip($currentClaimID, $aClaimInfo){
        $claimedID = get_post_meta($aClaimInfo['listing'], self::$listingClaimRelationshipKey, true);
        if ( $claimedID == $currentClaimID ){
            delete_post_meta($aClaimInfo['listing'], self::$listingClaimRelationshipKey);
            update_post_meta($aClaimInfo['listing'], 'listing_claim', array('status'=>'not_claimed'));
        }
    }

    public function pluckListingOutOfUserListingRelationship($claimerID, $listingID){
	    $aData = get_post_meta($claimerID, self::$userClaimedListingKey, true);
	    if ( !empty($aData) ){
	        if ( in_array($listingID, $aData) ){
	            $position = array_search($listingID,$aData);
	            unset($aData[$position]);
		        update_post_meta($claimerID, self::$userClaimedListingKey, $aData);
            }
        }
    }

	/*
	 * Put all claims of a listing into one collection
	 */
	public function claimCollection($metaID, $objectID, $metaKey, $metaValue){
        if ( $metaKey === self::$metaKey ){
            $aData = maybe_unserialize($metaValue);
            $aExisted = get_post_meta($aData['listing'], self::$claimsOfListingKey, true);
            $aListingsClaimedByUser = get_post_meta($aData['claimed_by'], self::$userClaimedListingKey, true);
	        $aExisted[] = absint($objectID);
	        $aListingsClaimedByUser[] = absint($aData['listing']);

            update_post_meta($aData['listing'], self::$claimsOfListingKey, $aExisted);
            update_post_meta($aData['claimed_by'], self::$userClaimedListingKey, $aListingsClaimedByUser);
        }
    }

	public function doDeleteRestClaims(){
	    if ( !current_user_can('edit_theme_options') ){
	        wp_send_json_error(
	          array(
                  'msg' => esc_html__('You do not have permission to do that.', 'wiloke')
              )
            );
        }

        if ( !isset($_POST['listingID']) || empty($_POST['listingID']) ){
	        wp_send_json_error(
		        array(
			        'msg' => esc_html__('You can not delete nothingness. Stupid ...!', 'wiloke')
		        )
	        );
        }

        $currentClaimID = isset($_POST['claimID']) ? absint($_POST['claimID']) : '';
		if ( empty($currentClaimID) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Claim ID is empty. Your requirement has been rejected. Bey Bey!', 'wiloke')
				)
			);
		}

		$aExisted = get_post_meta($_POST['listingID'], self::$claimsOfListingKey, true);
        $adminID = get_current_user_id();
		if ( !empty($aExisted) ){
		    foreach ($aExisted as $claimID){
		        if ( $currentClaimID != $claimID ){
		            $aClaimInfo = get_post_meta($claimID, self::$metaKey, true);
			        do_action('wiloke/wiloke_submission/add_notification', $adminID, $aClaimInfo['claimed_by'], $aClaimInfo['listing'], 'declined_claim');
		            wp_delete_post($claimID, true);
                }
            }
        }

        wp_send_json_success(
            array(
                'msg' => esc_html__('Congratulations! The rest claims of this listing have been deleted', 'wiloke')
            )
        );
    }

	public function enqueueScripts($hook){
		global $post;
		if (  isset($post->post_type) && ($post->post_type === self::$postType) ){
			wp_dequeue_script('sematic-selection-ui');
			wp_enqueue_style('sematic-ui', plugin_dir_url(__FILE__) . '../../admin/assets/semantic-ui/form.min.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_script('sematic-ui', plugin_dir_url(__FILE__) . '../../admin/assets/semantic-ui/semantic.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
			wp_enqueue_script('wiloke-claim', plugin_dir_url(__FILE__) . '../../admin/js/claim.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
		}
	}

	public function registerMetaBoxes(){
		add_meta_box(
		'wiloke-claim-box-id',
			esc_html__( 'Claim Details', 'wiloke' ),
			array($this, 'claimSettings'),
			self::$postType
		);

		add_meta_box(
			'wiloke-delete-claim-box-id',
			esc_html__( 'Delete the rest claims', 'wiloke' ),
			array($this, 'deleteRestClaims'),
			self::$postType
		);
	}

	public function saveSettings($postID, $post){
		if (!current_user_can(self::$capability) || !isset($_POST['wiloke_nonce_field']) || empty($_POST['wiloke_nonce_field']) || !wp_verify_nonce($_POST['wiloke_nonce_field'], 'wiloke_nonce_action') ){
			return false;
		}

		if ( isset($_POST['wiloke_listgo_claim']) && !empty($_POST['wiloke_listgo_claim']) ){
			update_post_meta($postID, self::$metaKey, $_POST['wiloke_listgo_claim']);

			$listingID = absint($_POST['wiloke_listgo_claim']['listing']);
			$claimerID = absint($_POST['wiloke_listgo_claim']['claimed_by']);
			if ( $_POST['wiloke_listgo_claim']['status'] === 'approved' ){
				update_post_meta($listingID, self::$listingClaimRelationshipKey, $postID);
				update_post_meta($listingID, 'listing_claim', array('status'=>'claimed'));
				do_action('wiloke/wiloke_submission/add_notification', get_current_user_id(), $claimerID, $_POST['wiloke_listgo_claim']['listing'], 'approved_claim');

				update_post_meta($listingID, self::$listingPreviousPostAuthorID, $post->post_author);
				wp_update_post(
                    array(
                        'ID' => $listingID,
                        'post_author' => $claimerID
                    )
                );
			}else{
                $postAuthorID = get_post_meta($listingID, self::$listingPreviousPostAuthorID, true);
			    if ( !WilokeSubmissionUser::isUserIDExists($postAuthorID) ){
				    $postAuthorID = WilokeSubmissionUser::getSuperAdminID();
                }

				$claimedID = get_post_meta(absint($listingID), self::$listingClaimRelationshipKey, true);
				if ( $claimedID == $postID ){
					update_post_meta($listingID, 'listing_claim', array('status'=>'not_claimed'));
					delete_post_meta($listingID, self::$listingClaimRelationshipKey);
					wp_update_post(
					    array(
                            'ID' => $listingID,
                            'post_author' => $postAuthorID
                        )
                    );
				}

				if ( $_POST['wiloke_listgo_claim']['status'] === 'claim' ){
					do_action('wiloke/wiloke_submission/add_notification', get_current_user_id(), $claimerID, $listingID, 'declined_claim');
                }
			}

		}
	}

	public function checkClaimedByAnother($listingID, $post){
		if ( empty($listingID) ){
			return false;
		}
		$claimedID = get_post_meta($listingID, self::$listingClaimRelationshipKey, true);
		if ( !empty($claimedID) && $claimedID != $post->ID ){
			?>
			<div class="message ui error">
				<?php
				\Wiloke::wiloke_kses_simple_html(__('Note that this listing has already been claimned by <a href="'.admin_url('post.php?post='.$claimedID.'&action=edit').'">another author</a>', 'wiloke'));
				?>
			</div>
			<?php
		}
		return false;
	}

	public function deleteRestClaims(){
		global $WilokeListGoFunctionalityApp;
		?>
		<div id="wiloke-delete-rest-of-claims-wrapper" class="wrap form ui">
			<?php
			foreach ( $WilokeListGoFunctionalityApp['settings']['claim']['delete_others'] as $aField ){
				if ( $aField['type'] !== 'header' && $aField['type'] !== 'submit' && $aField['type'] !== 'desc' ){
					$name = str_replace(array('wiloke_listgo', '[', ']'), array('', '', ''), $aField['name']);
					$aField['value'] = isset($aOptions[$name]) ? $aOptions[$name] : $aField['default'];
				}

				switch ($aField['type']){
					case 'text';
						\WilokeHtmlHelper::semantic_render_text_field($aField);
						break;
					case 'desc';
						\WilokeHtmlHelper::semantic_render_desc($aField);
						break;
					case 'submit':
						\WilokeHtmlHelper::semantic_render_submit($aField);
						break;
				}
			}
			?>
		</div>
		<?php
	}

	public function claimSettings($post){
		global $WilokeListGoFunctionalityApp;
		?>
		<div id="wiloke-claim-wrapper" class="wrap form ui">
			<?php wp_nonce_field('wiloke_nonce_action', 'wiloke_nonce_field'); ?>
			<?php
			$aOptions = get_post_meta($post->ID, self::$metaKey, true);
			$listingID = isset($aOptions['listing']) ? $aOptions['listing'] : '';

			$this->checkClaimedByAnother($listingID, $post);
			foreach ( $WilokeListGoFunctionalityApp['settings']['claim']['fields'] as $aField ){
				if ( $aField['type'] !== 'header' && $aField['type'] !== 'submit' && $aField['type'] !== 'desc' ){
					$name = str_replace(array('wiloke_listgo_claim', '[', ']'), array('', '', ''), $aField['name']);
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
					case 'select_user':
						\WilokeHtmlHelper::semantic_render_select_user_field($aField);
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
		</div>
		<?php
	}

	public function register() {}
}