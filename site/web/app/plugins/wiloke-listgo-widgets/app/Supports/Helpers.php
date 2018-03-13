<?php
namespace WilokeWidget\Supports;

class Helpers{
	public static function kses_html($content, $isReturn=false)
	{
		$allowed_html = array(
			'a' => array(
				'href'  => array(),
				'style' => array(
					'color' => array()
				),
				'title' => array(),
				'target'=> array(),
				'class' => array()
			),
			'br'     => array('class' => array()),
			'p'      => array('class' => array(), 'style'=>array()),
			'em'     => array('class' => array()),
			'strong' => array('class' => array()),
			'span'   => array('data-typer-targets'=>array(), 'class' => array()),
			'i'      => array('class' => array()),
			'ul'     => array('class' => array()),
			'li'     => array('class' => array()),
			'code'   => array('class'=>array()),
			'pre'    => array('class' => array()),
			'iframe' => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class'=>array('embed-responsive-item')),
			'img'    => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class'=>array(), 'alt'=>array()),
			'embed'  => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class' => array()),
		);

		$content = str_replace('[wiloke_quotes]', '"', $content);

		if ( !$isReturn ) {
			echo wp_kses(wp_unslash($content), $allowed_html);
		}else{
			return wp_kses(wp_unslash($content), $allowed_html);
		}
	}

	/**
	 * Creating text field
	 */
	public static function description($des)
	{
		if ( !empty($des) )
		{
			\Wiloke::wiloke_kses_simple_html('<p><code>'.$des.'</code></p>');
		}
	}

	/**
	 * Creating text field
	 */
	public static function textField($label, $id, $name, $val, $des="")
	{
		?>
		<p>
			<label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
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
	public static function btnField($btnName, $id)
	{
		?>
        <p>
            <button id="<?php echo esc_attr($id); ?>" class="wiloke-widget-addnew wiloke-widget widefat button button-primary <?php echo isset($args['class']) ? esc_attr($args['class']) : ''; ?>"><?php echo esc_html($btnName); ?></button>
        </p>
		<?php
	}


	/**
	 * Creating text field
	 */
	public static function linkField($label, $id, $name, $val, $des="")
	{
		?>
		<p>
			<label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
			<input id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($val); ?>" class="widefat <?php echo isset($args['class']) ? esc_url($args['class']) : ''; ?>" type="text" />
			<?php
			if ( !empty($des) ) {
				self::kses_html( '<p><code>' . $des . '</code></p>' );
			}
			?>
		</p>
		<?php
	}

	/**
	 * Creating upload field
	 * pi_upload_form this function created at help->func.help.php
	 */
	public static function uploadField($label, $id, $buttonId, $name, $multiple=false, $value, $des="")
	{
		?>
		<p>
			<label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
			<?php self::uploadForm($multiple, $name, $value, $buttonId); ?>
			<?php
			if ( !empty($des) )
			{
				self::kses_html('<code class="help">'. $des . '</code>');
			}
			?>
			<script type="text/javascript">
				(function($){
					if ( $.piWidgetIsWindowLoad )
					{
						let interval = setInterval( function()
						{
							let $self = $('.pi-btn-upload', '#widgets-right');
							$.each($self, function()
							{
								if ( $(this).attr('id') !== 'widget-pi_about-__i__-upload_button')
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

	public static function uploadForm($multiple=false, $name, $value="", $buttonId="")
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

	public static function uploadMedia($name, $value="")
	{
		?>
		<div class="audio-wrapper pi-wrap-text-upload">
			<p>
				<textarea class="wiloke_input form-control pi-insert-audio" name="<?php echo esc_attr($name); ?>"><?php echo esc_textarea($value); ?></textarea>
			</p>
			<button type="button" class="button button-primary pi-audio-upload"><?php esc_html_e("Upload", 'wiloke'); ?></button>
		</div>
		<?php
	}

	/**
	 * Creating select field
	 */
	public static function selectField($label, $id, $name, $aOptions, $val, $des="")
	{
		?>
		<p>
			<label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
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
				self::kses_html('<code class="help">'. $des . '</code>');
			}
			?>
		</p>
		<?php
	}

	/**
	 * Creating textarea field
	 */
	public static function textareaField($label, $id, $name, $val, $des="")
	{
		?>
		<p>
			<label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
			<textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" class="widefat" type="text"><?php echo esc_textarea($val); ?></textarea>
			<?php
			if ( !empty($des) )
			{
				self::kses_html('<code class="help">'. $des . '</code>');
			}
			?>
		</p>
		<?php
	}
}