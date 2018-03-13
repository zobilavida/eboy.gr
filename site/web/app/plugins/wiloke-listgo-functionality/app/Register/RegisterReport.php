<?php
namespace WilokeListGoFunctionality\Register;
use WilokeListGoFunctionality\AlterTable\AlterTableReport;

class RegisterReport implements RegisterInterface{
	public static $blackIP = 'wiloke_black_ip';
    public $postsPerPage = 10;

	public function __construct() {
		add_action('wp_ajax_add_report', array($this, 'addReport'));
		add_action('wp_ajax_nopriv_add_report', array($this, 'addReport'));
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'adminScripts'), 10, 1);
		add_action('wp_ajax_wiloke_ban_this_ip', array($this, 'processBanIP'));
		add_action('wp_ajax_wiloke_remove_ip_from_black_list', array($this, 'removeIPFromBlackList'));
		add_action('wp_ajax_wiloke_delete_report', array($this, 'deleteReport'));
	}

	public function test(){
	    $aPostIDs = array(2053);
	    $reason = "This is appropriate content";
	    foreach ( $aPostIDs as $postID ){
		    $ipAddress = \Wiloke::clientIP();
		    if ( !$ipAddress ){
			    wp_send_json_error();
		    }

		    $aBlackIP = get_option(self::$blackIP);
		    $aBlackIP = !empty($aBlackIP) ? json_decode($aBlackIP, true) : false;

		    if ( $aBlackIP && in_array($ipAddress, $aBlackIP) ){
			    wp_send_json_error();
		    }

		    global $wpdb;

		    $ipAddress = esc_sql($ipAddress);
		    $postID    = absint($postID);
		    $reason    = esc_sql($reason);
		    $tblName = $wpdb->prefix.AlterTableReport::$tblName;

		    $aInfo = array(
			    'post_ID'        => $postID,
			    'reported_by_IP' => $ipAddress,
			    'reason'         => $reason
		    );

		    $checkStatus = $wpdb->get_var($wpdb->prepare(
			    "SELECT post_ID FROM $tblName WHERE post_ID=%d AND reported_by_IP=%s",
			    $postID, $ipAddress
		    ));

		    $wpdb->insert(
			    $tblName,
			    $aInfo,
			    array(
				    '%d',
				    '%s',
				    '%s'
			    )
		    );

		    if ( \Wiloke::$wilokePredis ){
			    \Wiloke::$wilokePredis->hSet(\Wiloke::$prefix . 'listing_report', time(), json_encode($aInfo));
		    }
        }
    }

	public function register() {
		add_menu_page(esc_html__('Reports', 'wiloke'), esc_html__('Reports', 'wiloke'), 'administrator', 'listgo-reports', array($this, 'showReports'), 'dashicons-flag', 28);
	}

	public function adminScripts($hook){
		if ( $hook === 'toplevel_page_listgo-reports' ){
			wp_enqueue_style('jquery-ui-style', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_style('semantic-ui', plugin_dir_url(__FILE__) . '../../admin/assets/semantic-ui/form.min.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_script('jquery-ui-tooltip');
			wp_enqueue_script('semantic-ui', plugin_dir_url(__FILE__) . '../../admin/assets/semantic-ui/semantic.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
			wp_enqueue_script('listgo-report', plugin_dir_url(__FILE__) . '../../admin/js/report-table.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
		}
	}

	public function deleteReport(){
		if ( !check_ajax_referer('wiloke_admin_nonce_action', 'security', false) ){
			wp_send_json_error();
		}

		if ( empty($_POST['postid']) ){
			wp_send_json_error();
		}

		global $wpdb;
        $postID = esc_sql($_POST['postid']);

		$wpdb->delete(
            $wpdb->prefix . AlterTableReport::$tblName,
            array(
                'post_ID' => $postID
            ),
            array(
                '%d'
            )
        );

		wp_send_json_success();
    }

	public function removeIPFromBlackList(){
		if ( !check_ajax_referer('wiloke_admin_nonce_action', 'security', false) ){
			wp_send_json_error();
		}

		if ( empty($_POST['ip']) ){
			wp_send_json_error();
		}

		$aBanned = get_option(self::$blackIP);
		$aBanned = json_decode($aBanned, true);
		$IPAddress = $_POST['ip'];

		$key = array_search($IPAddress, $aBanned);

		unset($aBanned[$key]);
		$aBanned = !empty($aBanned) ? json_encode($aBanned) : '';

		update_option(self::$blackIP, $aBanned);
		wp_send_json_success();
	}

	public function processBanIP(){
		if ( !check_ajax_referer('wiloke_admin_nonce_action', 'security', false) ){
			wp_send_json_error();
		}

		if ( empty($_POST['ip']) ){
			wp_send_json_error();
		}

		$aBanned = get_option(self::$blackIP);
		$aBanned = !empty($aBanned) ? json_decode($aBanned, true) : array();
		$aBanned[] = $_POST['ip'];

		update_option(self::$blackIP, json_encode($aBanned));
		wp_send_json_success();
	}

	public function addReport(){
	    $msg = esc_html__('Thanks for your reporting! Your action make the website better.', 'wiloke');

		if ( !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_success(
				array(
					'msg' => $msg
				)
			);
		}

		if ( !isset($_POST['ID']) || empty($_POST['ID']) || !isset($_POST['reason']) || empty($_POST['reason']) ){
			wp_send_json_success(
				array(
					'msg' => $msg
				)
			);
		}

		$ipAddress = \Wiloke::clientIP();
		if ( !$ipAddress ){
			wp_send_json_success(
				array(
					'msg' => $msg
				)
			);
		}

		$aBlackIP = get_option(self::$blackIP);
		$aBlackIP = !empty($aBlackIP) ? json_decode($aBlackIP, true) : false;

		if ( $aBlackIP && in_array($ipAddress, $aBlackIP) ){
			wp_send_json_success(
                array(
                    'msg' => $msg
                )
            );
		}

		global $wpdb;
		$tblName = $wpdb->prefix.AlterTableReport::$tblName;

		$ipAddress = esc_sql($ipAddress);
		$postID    = absint($_POST['ID']);
		$reason    = esc_sql($_POST['reason']);

		$checkStatus = $wpdb->get_var($wpdb->prepare(
            "SELECT post_ID FROM $tblName WHERE post_ID=%d AND reported_by_IP=%s",
			$postID, $ipAddress
        ));

		if ( !empty($checkStatus) ){
			wp_send_json_success(
				array(
					'msg' => $msg
				)
			);
        }

		$aInfo = array(
			'post_ID'        => $postID,
			'reported_by_IP' => $ipAddress,
			'reason'         => $reason
		);

		$wpdb->insert(
			$tblName,
			$aInfo,
			array(
				'%d',
				'%s',
				'%s'
			)
		);

		if ( \Wiloke::$wilokePredis ){
			\Wiloke::$wilokePredis->hSet(\Wiloke::$prefix . 'listing_report', time(), json_encode($aInfo));
		}

		wp_send_json_success(
			array(
				'msg' => $msg
			)
		);
	}

	public function sqlQuery($paged=1, $keywords='', $target=''){
		global $wpdb;
		$start      = ($paged - 1)*$this->postsPerPage;
		$tblName    = $wpdb->prefix . AlterTableReport::$tblName;

		if ( !empty($keywords) ){
		    if ( $target !== 'ip' ){
				$tblPosts = $wpdb->prefix . 'posts';
				$sql = "SELECT * FROM $tblName LEFT JOIN $tblPosts ON ($tblName.post_ID = $tblPosts.ID) WHERE $tblPosts.$target like '%{$keywords}%' LIMIT $start, $this->postsPerPage";
				$countSql =  "SELECT COUNT(post_ID) FROM $tblName LEFT JOIN $tblPosts ON ($tblName.post_ID = $tblPosts.ID) WHERE $tblPosts.$target like '%{$keywords}%'";
            }else{
			    $sql = "SELECT * FROM $tblName WHERE reported_by_IP = '{$keywords}' LIMIT $start, $this->postsPerPage";
			    $countSql =  "SELECT COUNT(post_ID) FROM $tblName WHERE reported_by_IP = '{$keywords}'";
            }
        }else{
			$sql = "SELECT * FROM $tblName LIMIT $start, $this->postsPerPage";
			$countSql = "SELECT COUNT(post_ID) FROM $tblName";
        }

		$aResults = $wpdb->get_results($sql, ARRAY_A);
		$total = $wpdb->get_var($countSql);
		
		return array(
			'results' => $aResults,
			'total' => $total
		);
	}

	public function showReports(){
        $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $keywords = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : '';
        $target = isset($_GET['target']) ? $_GET['target'] : '';
		$total  = 0;
		if ( \Wiloke::$wilokePredis && (empty($keywords) || (!empty($keywords) && ($_GET['target'] === 'ip')) )  ){
			$aReports = \Wiloke::$wilokePredis->hGetAll(\Wiloke::$prefix.'listing_report');
			if ( !empty($aReports) ){
				$aReports = json_decode($aReports, true);
				$aReports = collect($aReports);
				if ( $paged !== 1 ){
					$aReports = $aReports->slice($this->postsPerPage*$paged - 1, $this->postsPerPage);
				}

				if ( !empty($keywords) ){
					$aReports = $aReports->where('created_by_IP', '=', $keywords);
				}

				$aReports = $aReports->all();
				$total    = count($aReports);
            }
		}else{
			$aQuery = $this->sqlQuery($paged, $keywords, $target);
			$total    = $aQuery['total'];
			$aReports = $aQuery['results'];
		}

		$aBannedIPs = get_option(self::$blackIP);
		$hideListIPs = '';
		if ( empty($aBannedIPs) ){
			$hideListIPs = 'hidden';
		}else{
			$aBannedIPs = json_decode($aBannedIPs, true);
		}
		$pagination = $total > $this->postsPerPage ? ceil($total/$this->postsPerPage) : 1;

		$aFields = array(
            'post_title'    => esc_html__('Post Title', 'wiloke'),
            'ip'            => esc_html__('IP Address', 'wiloke'),
        );

		?>
			<div id="listgo-table-wrapper"  style="margin: 20px; auto;">
                <?php
                if ( empty($aReports) ) :
                    if ( empty($keywords) ) {
                        \WilokeAlert::render_alert( esc_html__('Everything is progressing well. There are no reports.', 'wiloke'), 'info' );
                    }else{
                        \WilokeAlert::render_alert( esc_html__('Search Not Found', 'wiloke'), 'info' );
                        ?>
                        <p><a href="<?php echo esc_url(admin_url('admin.php?page=listgo-reports')); ?>" class="button button-primary"><?php esc_html_e('Reset Default', 'wiloke'); ?></a></p>
                        <?php
                    }
                else:
                ?>
                <div class="searchform">
                    <form class="ui equal width form" action="<?php echo esc_url(admin_url('admin.php')); ?>" method="GET">
                        <input type="hidden" name="page" value="listgo-reports">
                        <div class="fields">
                            <div class="search-field field">
                                <div class="ui selection dropdown">
                                    <div class="default text"><?php esc_html_e('Target', 'wiloke'); ?></div>
                                    <i class="dropdown icon"></i>
                                    <input type="hidden" name="target" value="<?php echo esc_attr($target); ?>">
                                    <div class="menu">
                                        <?php foreach ($aFields as $key => $name): ?>
                                        <div class="item" data-value="<?php echo esc_attr($key); ?>"><?php echo esc_html($name); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="search-field field">
                                <input type="text" class="search_keyword" name="search_keyword" value="<?php echo esc_attr($keywords); ?>" placeholder="<?php esc_html_e('Enter in your keywords here', 'wiloke');?>" />
                            </div>
                            <div class="search-field field">
                                <button type="submit" class="ui primary basic button"><?php esc_html_e('Search', 'wiloke'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>

				<table id="listgo-table" class="ui striped table">
					<?php echo wp_nonce_field('wiloke_admin_nonce_action', 'wiloke_admin_nonce_field'); ?>
					<thead>
						<tr>
							<th colspan="7"><?php esc_html_e('Report analytics', 'wiloke'); ?></th>
						</tr>
						<tr>
							<th><?php esc_html_e('Post Title', 'wiloke'); ?></th>
							<th><?php esc_html_e('Post Author', 'wiloke'); ?></th>
							<th><?php esc_html_e('Reported By IP', 'wiloke'); ?></th>
							<th><?php esc_html_e('Reported At', 'wiloke'); ?></th>
							<th><?php esc_html_e('Reason', 'wiloke'); ?></th>
							<th><?php esc_html_e('Ban IP', 'wiloke'); ?></th>
							<th><?php esc_html_e('Delete', 'wiloke'); ?></th>
						</tr>
					</thead>
					<tbody class="report-content">
						<?php
						foreach ( $aReports as $aReport ) :
							$oPost = get_post($aReport['post_ID']);
						    if ( !empty($oPost) && !is_wp_error($oPost) ) :
                                $link = get_permalink($oPost->ID);
                                if (  !empty($aBannedIPs) && in_array($aReport['reported_by_IP'], $aBannedIPs) ){
                                    $bannedClass = 'banned';
                                }else{
                                    $bannedClass = '';
                                }

                                $excerptReason = \Wiloke::wiloke_content_limit(25, '', false, $aReport['reason'], true, '...');
                        ?>
                            <tr>
                                <td class="post-title text-left"><a href="<?php echo esc_url($link) ?>"><?php echo esc_attr($oPost->post_title); ?></a></td>
                                <td><a href="<?php echo esc_url($link) ?>"><?php echo get_the_author_meta('nicename', $oPost->post_author); ?></a></td>
                                <td class="ip-address"><span title="<?php echo esc_attr($aReport['reported_by_IP']); ?>" class="<?php echo esc_attr($bannedClass); ?>"><?php echo esc_html($aReport['reported_by_IP']); ?></span></td>
                                <td><?php echo esc_attr($aReport['reported_at']); ?></td>
                                <td class="text-left"><p title="<?php echo esc_attr($aReport['reason']); ?>"><?php echo esc_html($excerptReason); ?></p></td>
                                <td class="text-center"><span class="virtual-a js_ban_ip js_action <?php echo esc_attr($bannedClass); ?>" data-ipaddress="<?php echo esc_attr($aReport['reported_by_IP']); ?>"><i class="dashicons dashicons-thumbs-down"></i></span></td>
                                <td class="text-center"><span class="virtual-a js_delete_report js_action" data-postid="<?php echo esc_attr($aReport['post_ID']); ?>"><i class="dashicons dashicons-trash"></i></span></td>
                            </tr>
						<?php endif; endforeach; ?>
					</tbody>

                    <?php if ( $pagination !== 1 ) : ?>
                    <tfoot>
                        <tr>
                            <th><?php esc_html_e('Pagination ', 'wiloke'); ?></th>
                            <td colspan="6">
	                            <?php for ($i = 1; $i <= $pagination; $i++ ) :
                                    $actived = $paged === $i ? 'active' : '';
                                ?>
                                    <a class="<?php echo esc_attr($actived); ?>" href="<?php echo esc_url(admin_url('admin.php?page=listgo-reports&paged='.$i.'&search_keyword='.$keywords.'&target='.$target)); ?>"><?php echo esc_html($i); ?></a>
	                            <?php endfor; ?>
                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
				</table>

				<div class="banned-ips-wrapper <?php echo esc_attr($hideListIPs); ?>">
					<h2 class="header dividing"><?php esc_html_e('List of banned IPs', 'wiloke'); ?></h2>
                    <div class="ui message info"><i><?php esc_html_e('If you want to remove an IP address from this black list, please click on that ip', 'wiloke'); ?></i></div>
					<ul id="banned-ips" class="tags ui list">
						<?php
						if (!empty($aBannedIPs)){
							foreach ($aBannedIPs as $IP){
								echo '<li><a href="#" data-ipaddress="'.esc_attr($IP).'" class="tag">'.esc_html($IP).'</a></li>';
							}
						}
						?>
					</ul>
				</div>

				<ul class="listgo-table-note message ui info">
					<li><strong><?php esc_html_e('Post Title:', 'wiloke'); ?></strong> <?php esc_html_e('The title of post has been reported', 'wiloke'); ?></li>
					<li><strong><?php esc_html_e('Post Author:', 'wiloke'); ?></strong> <?php esc_html_e('The author of post has been reported', 'wiloke'); ?></li>
					<li><strong><?php esc_html_e('Reported By IP:', 'wiloke'); ?></strong> <?php esc_html_e('The post has been reported by that IP address', 'wiloke'); ?></li>
					<li><strong><?php esc_html_e('Ban IP:', 'wiloke'); ?></strong> <?php esc_html_e('Do not allow person who has that IP address be reported anymore', 'wiloke'); ?></li>
					<li><strong><?php esc_html_e('Delete Report:', 'wiloke'); ?></strong> <?php esc_html_e('If a report is wrong, You can delete that.', 'wiloke'); ?></li>
				</ul>
                <?php endif; ?>
			</div>
		<?php
	}

}
