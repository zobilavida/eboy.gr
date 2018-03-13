<?php
/*
 * About Us/Me
 * @copyright Wiloke
 * @website https://wiloke.com
 */
use WilokeWidget\Supports\Helpers;
use WilokeListGoFunctionality\Frontend\FrontendManageSingleListing;

class WilokeGallery extends WP_Widget
{
	public $aDef = array('title' => 'Gallery');

	public function __construct(){
		parent::__construct('wiloke_gallery', WILOKE_WIDGET_PREFIX . '(OSP) Gallery', array('classname'=>'widget widget_author_gallery',  'description'=>esc_html__('OSP - Only Single Page. This widget is only available for single listing page.', 'wiloke')) );
	}

	public function form($aInstance){
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField(esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
	}

	public function update($aNewinstance, $aOldinstance) {
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val ){
			$aInstance[$key] = strip_tags($val);
		}

		return $aInstance;
	}

	public function widget($atts, $aInstance){
		global $post;
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		if ( !is_singular('listing') ){
			return false;
		}
		$aGallery = Wiloke::getPostMetaCaching($post->ID, 'gallery_settings');

		if ( !isset($aGallery['gallery']) || empty($aGallery['gallery']) ){
			return false;
		}

		if ( !FrontendManageSingleListing::packageAllow('toggle_allow_add_gallery') ){
		    return false;
        }

		$totalImages = count($aGallery['gallery']);
		echo $atts['before_widget'];
		    if ( !empty($aInstance['title']) ){
			    echo $atts['before_title'] . '<i class="icon_camera_alt"></i>' . ' '  . esc_html($aInstance['title']) . $atts['after_title'];
            }

			?>
				<div class="widget_author-gallery">
					<ul class="popup-gallery">
						<?php
							$i = 1;
							foreach ( $aGallery['gallery'] as $id => $url ){
								if ( $i < 4 ) {
									$this->renderGalleryItem($id);
								}elseif ($i===4){
									$this->renderGalleryItem($id, 'author__gallery-plus', $totalImages);
								}else{
									$this->renderGalleryItem($id, 'hidden');
								}
								$i++;
							}
						?>
					</ul>
				</div>
			<?php
		echo $atts['after_widget'];
	}

	public function renderGalleryItem($attachID, $class='', $total=0){
		$title = get_the_title($attachID);
		$thumb = wp_get_attachment_image_url($attachID, 'thumbnail');
		?>
		<li class="<?php echo esc_attr($class); ?>">
			<a class="lazy bg-scroll" href="<?php echo esc_url(wp_get_attachment_image_url($attachID, 'full')); ?>" data-title="<?php echo esc_attr($title); ?>" data-src="<?php echo esc_url($thumb); ?>">
				<?php if ($total) :  ?>
					<span class="count">+<?php echo esc_attr($total); ?></span>
				<?php endif; ?>
			</a>
		</li>
		<?php
	}
}
