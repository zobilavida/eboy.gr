<?php
/**
 * WilokeHtmlHelper Class
 *
 * @category Helper
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

class WilokeHtmlHelper
{
    public static $aCaching = array();

	public static function render_hidden_field($aInput)
	{
		?>
        <input class="wiloke-hidden-field" type="hidden" name="<?php echo esc_attr($aInput['name']); ?>" value="<?php echo esc_attr($aInput['value']); ?>">
		<?php
	}

    /**
     * Media Upload Field
     * @param: The id of target
     */
    public static function wiloke_render_media_field($aInput)
    {
        if (  isset($aInput['is_multiple']) && $aInput['is_multiple'] )
        {
            $isMultiple = 'true';
        }else{
            $isMultiple = 'false';
        }

	    $aInput['return'] = isset($aInput['return']) ? $aInput['return'] : 'id';
        $target = str_replace(array('[', ']'), array('', ''), $aInput['id']);
	    $aMediaWrapper = 'list-wiloke-image-media';
	    $inputType = $aInput['return'] === 'id' ? 'hidden' : 'text';
        if ( !isset($aInput['value']) || empty($aInput['value']) ){
            $aInput['value'] = '';
            $aMediaWrapper .= ' hidden';
        }
        ?>
        <tr class="wiloke-format-field form-field">
            <th scope="row">
                <label><strong><?php echo esc_html($aInput['name']); ?></strong></label>
            </th>
            <td class="td-of-image-field-in-term">
                <div class="wiloke-image-field-id <?php echo esc_attr('wiloke-'.$target.'-wrapper'); ?>" data-multiple="<?php echo esc_attr($isMultiple); ?>" data-return="<?php echo esc_attr($aInput['return']); ?>">
                    <div class="image-show">
                        <ul class="<?php echo esc_attr($aMediaWrapper); ?>" data-column="column-<?php echo esc_attr($target); ?>">
                            <?php
                            $thumbnail = strpos($aInput['value'], 'http') !== false ? $aInput['value'] : wp_get_attachment_image_url($aInput['value'], 'thumbnail');
                            echo sprintf('<li class="wiloke-image" data-id="%1s"><img src="%2s"/><div class="wiloke-control-wrap"><i class="wiloke-edit dashicons dashicons-edit"></i><i class="wiloke-close dashicons dashicons-no-alt"></i></div></li>', esc_attr($aInput['value']), esc_url($thumbnail));
                            ?>
                        </ul>
                    </div>
                    <div class="wiloke-button-media" data-parent=".wiloke-image-field-id > .image-show">
                        <input class="wiloke-media-value" type="<?php echo esc_attr($inputType); ?>" name="<?php echo esc_attr($aInput['id']); ?>" value="<?php echo esc_attr($aInput['value']); ?>">
                        <a href="#" class="button wiloke-button button-primary"><?php esc_html_e('Upload Image', 'listgo'); ?></a>
                        <?php if ( isset($aInput['description']) ) : ?>
                            <p class="description"><?php Wiloke::wiloke_kses_simple_html($aInput['description']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
        <?php
    }

    public static function wiloke_render_text_field($aInput)
    {
        ?>
        <tr class="wiloke-format-field form-field">
            <th scope="row">
                <label><strong><?php echo esc_html($aInput['name']); ?></strong></label>
            </th>

            <td>
                <input class="wiloke-text-field" type="text" name="<?php echo esc_attr($aInput['id']); ?>" value="<?php echo esc_attr($aInput['value']); ?>" />
                <?php if ( isset($aInput['description']) ) : ?>
                    <p class="description"><?php Wiloke::wiloke_kses_simple_html($aInput['description']); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
    
    public static function wiloke_render_textarea_field($aInput)
    {
	    $aField['cols'] = isset($aField['cols']) ? $aField['cols'] : 30;
	    $aField['rows'] = isset($aField['rows']) ? $aField['rows'] : 10;
	    ?>
        ?>
        <tr class="wiloke-format-field form-field">
            <th scope="row">
                <label><strong><?php echo esc_html($aInput['name']); ?></strong></label>
            </th>

            <td>
                <textarea name="<?php echo esc_attr($aInput['id']); ?>" cols="30" rows="10"><?php echo esc_textarea(stripslashes($aInput['value'])); ?></textarea>
                <?php if ( isset($aInput['description']) ) : ?>
                    <p class="description"><?php Wiloke::wiloke_kses_simple_html($aInput['description']); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    public static function wiloke_render_checkbox_field($aInput)
    {
        ?>
        <tr class="wiloke-format-field form-field">
            <th scope="row">
                <strong><?php echo esc_html($aInput['name']); ?></strong>
            </th>

            <td>
                <?php
                $i = 0;
                foreach (  $aInput['options'] as $key => $val ) :
                    $i++;
                    if ( isset($aInput['value']) && !empty($aInput['value']) )
                    {
                        $checked = in_array($key, $aInput['value']) ? 'checked' : '';
                    }else{
                        $checked = '';
                    }

                    if ( !isset($aInput['check_supports']) || ( isset($aInput['check_supports']) && post_type_supports($key, $aInput['check_supports']) ) ) :
                ?>
                <label>
                    <input class="wiloke-checkbox-field" type="checkbox" name="<?php echo esc_attr($aInput['id']); ?>[]" value="<?php echo esc_attr($key); ?>" <?php echo esc_attr($checked); ?> />
                    <?php echo esc_html($val); ?>
                </label>
                <br />
                <?php endif; endforeach; ?>

                <?php if ( isset($aInput['description']) ) : ?>
                    <p class="description"><?php Wiloke::wiloke_kses_simple_html($aInput['description']); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    public static function wiloke_render_select_field($aInput)
    {
        ?>
        <tr class="wiloke-format-field form-field">
            <th scope="row">
                <label><strong><?php echo esc_html($aInput['name']); ?></strong></label>
            </th>

            <td>
                <select class="wiloke-select-field" name="<?php echo esc_attr($aInput['id']); ?>">
                    <?php
                        foreach ( $aInput['options'] as $option => $name ) :
                    ?>
                        <option value="<?php echo esc_attr($option) ?>" <?php selected($option, $aInput['value']) ?>><?php echo esc_html($name); ?></option>
                    <?php endforeach; ?>
                </select>

                <?php if ( isset($aInput['description']) ) : ?>
                    <p class="description"><?php Wiloke::wiloke_kses_simple_html($aInput['description']); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    public static function wiloke_render_timezone_field($aInput){
	    ?>
        <tr class="wiloke-format-field form-field">
            <th scope="row">
                <label for="<?php echo esc_attr($aInput['id']); ?>"><strong><?php echo esc_html($aInput['name']); ?></strong></label>
            </th>

            <td>
                <select id="<?php echo esc_attr($aInput['id']); ?>" class="wiloke-select-field wiloke-use-select2 js_select2_without_ajax" name="<?php echo esc_attr($aInput['id']); ?>">
				    <?php echo wp_timezone_choice($aInput['value']); ?>
                </select>
			    <?php if ( isset($aInput['description']) ) : ?>
                    <p class="description"><?php Wiloke::wiloke_kses_simple_html($aInput['description']); ?></p>
			    <?php endif; ?>
            </td>
        </tr>
	    <?php
    }

    public static function wiloke_render_colorpicker_field($aInput)
    {
        if ( !isset($aInput['value']) || empty($aInput['value']) )
        {
            $addClassEmpty = 'wiloke-start-empty';
        }else{
            $addClassEmpty = '';
        }
        ?>
        <tr class="wiloke-format-field form-field">
            <th scope="row">
                <label for="<?php echo esc_attr($aInput['id']); ?>"><strong><?php echo esc_html($aInput['name']); ?></strong></label>
            </th>

            <td>
                <input id="<?php echo esc_attr($aInput['id']); ?>" class="wiloke-text-field wiloke-colorpicker <?php echo esc_attr($addClassEmpty); ?>" type="text" name="<?php echo esc_attr($aInput['id']); ?>" value="<?php echo esc_attr($aInput['value']); ?>" />
                <?php if ( isset($aInput['description']) ) : ?>
                    <p class="description"><?php Wiloke::wiloke_kses_simple_html($aInput['description']); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    public static function wiloke_render_description_field($aInput)
    {
        ?>
        <tr class="wiloke-format-field form-field">
            <th scope="row">
                <label><strong><?php echo esc_html($aInput['name']); ?></strong></label>
            </th>

            <td>
                <?php if ( isset($aInput['description']) ) : ?>
                    <p class="description"><?php Wiloke::wiloke_kses_simple_html($aInput['description']); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    public static function wiloke_render_heading_field($aInput)
    {
        ?>
        <tr class="wiloke-format-field form-field">
            <th scope="row">
                <label><strong><?php echo esc_html($aInput['name']); ?></strong></label>
                <hr>
            </th>
        </tr>
        <?php
    }

    public static function wiloke_render_submit_field()
    {
        ?>
        <tr class="wiloke-format-field form-field">
            <th scope="row">
                <label><strong><?php esc_html_e('Save', 'listgo'); ?></strong></label>
            </th>
            <td>
                <input type="submit" class="button button-primary" value="<?php esc_html_e('Submit', 'listgo'); ?>" />
            </td>
        </tr>
        <?php
    }

	public static function tbl_render_open_tbl($aField){
		?>
        <?php if ( isset($aField['heading']) ) : ?>
        <h2 class="title"><?php echo esc_html($aField['heading']); ?></h2>
        <?php endif; ?>
        <table class="form-table"><tbody>
		<?php
	}

	public static function tbl_render_close_tbl(){
	    ?>
        </tbody></table>
        <?php
    }

    public static function desc($aField){
	   if ( isset($aField['desc']) ) :
    ?>
        <p><i class="desc"><?php Wiloke::wiloke_kses_simple_html($aField['desc']); ?></i></p>
    <?php
       endif;
    }

	public static function semantic_render_header($aField){
        $class = 'header ui' . (isset($aField['class']) ? ' ' . $aField['class'] : '');
        ?>
        <<?php echo esc_attr($aField['tag']) ?> class="<?php echo esc_attr($class); ?>"><?php echo esc_html($aField['text']); ?></<?php echo esc_attr($aField['tag']) ?>>
        <?php
        self::desc($aField);
	}

	public static function semantic_render_desc($aField){
		if ( isset($aField['desc']) ) :
			$status = isset($aField['desc_status']) ? $aField['desc_status'] : '';
			?>
            <p class="ui <?php echo esc_attr($status); ?> message"><i class="desc"><?php Wiloke::wiloke_kses_simple_html($aField['desc']); ?></i></p>
			<?php
		endif;
	}

	public static function semantic_render_text_field($aField){
		$class = 'field' . (isset($aField['wrapper_class']) ? ' ' . $aField['wrapper_class'] : '');
		$aField['class'] =  isset($aField['class']) ? $aField['class'] : '';
		$aField['required'] =  isset($aField['required']) ? 'required' : '';
		?>
        <div class="<?php echo esc_attr($class); ?>">
            <label for="<?php echo esc_attr($aField['id']); ?>"><?php echo esc_attr($aField['heading']); ?></label>
            <input type="text" placeholder="<?php echo isset($aField['placeholder']) ? esc_attr($aField['placeholder']) : ''; ?>" id="<?php echo esc_attr($aField['id']); ?>" class="<?php echo esc_attr($aField['class']); ?>" name="<?php echo esc_attr($aField['name']); ?>" value="<?php echo esc_attr($aField['value']); ?>" <?php if ( isset($aField['is_readonly'])  ) : ?> readonly="" <?php endif; ?> <?php echo esc_attr($aField['required']); ?>>
	        <?php self::semantic_render_desc($aField); ?>
        </div>
		<?php
	}

	public static function semantic_render_textarea_field($aField){
		$class = 'field' . (isset($aField['wrapper_class']) ? ' ' . $aField['wrapper_class'] : '');
		$aField['class'] =  isset($aField['class']) ? $aField['class'] : '';
		?>
        <div class="<?php echo esc_attr($class); ?>">
            <label for="<?php echo esc_attr($aField['id']); ?>"><?php echo esc_attr($aField['heading']); ?></label>
            <textarea rows="<?php echo isset($aField['rows']) ? esc_attr($aField['rows']) : ''; ?>" id="<?php echo esc_attr($aField['id']); ?>" class="<?php echo esc_attr($aField['class']); ?>" name="<?php echo esc_attr($aField['name']); ?>"><?php echo esc_textarea(stripslashes($aField['value'])); ?></textarea>
            <?php self::semantic_render_desc($aField); ?>
        </div>
		<?php
	}

	public static function semantic_render_select_post_field($aField){
		$class = 'field' . (isset($aField['wrapper_class']) ? ' ' . $aField['wrapper_class'] : '');
		$aField['class'] =  isset($aField['class']) ? $aField['class'] : '';
		$multiple = isset($aField['multiple']) && $aField['multiple'] ? 'multiple' : '';
		$required = isset($aField['required']) && $aField['required'] ? 'required' : '';
		?>
        <div class="<?php echo esc_attr($class); ?>">
            <label for="<?php echo esc_attr($aField['id']); ?>"><?php echo esc_attr($aField['heading']); ?></label>
            <div data-query="<?php echo esc_attr($aField['post_type']); ?>">
                <select id="<?php echo esc_attr($aField['id']); ?>" <?php echo esc_attr($required); ?> class="<?php echo esc_attr($aField['class']); ?> js_select2_ajax" name="<?php echo esc_attr($aField['name']); ?>" <?php echo esc_attr($multiple); ?>>
					<?php
					if ( !empty($aField['value']) ){
						$aField['value'] = is_array($aField['value']) ? $aField['value'] : array($aField['value']);
						foreach ( $aField['value'] as $val ){
							?>
                            <option value="<?php echo esc_attr($val); ?>" selected><?php echo esc_html(get_the_title($val)); ?></option>
							<?php
						}
					}
					?>
                </select>
	            <?php self::semantic_render_desc($aField); ?>
            </div>
        </div>
		<?php
	}

	public static function semantic_render_simple_select_post_field($aField){
		$class = 'field' . (isset($aField['wrapper_class']) ? ' ' . $aField['wrapper_class'] : '');
		$aField['class'] =  isset($aField['class']) ? $aField['class'] : '';
		$multiple = '';
		if ( isset($aField['multiple']) && $aField['multiple'] ){
			$multiple = 'multiple';
		}

		if ( !empty($aField['value']) ){
			$aField['value'] = !is_array($aField['value']) ? array($aField['value']) : $aField['value'];
		}else{
			$aField['value'] = array();
		}

		$required = isset($aField['required']) && $aField['required'] ? 'required' : '';
        $oPosts = self::getPosts($aField);

		?>
        <div class="<?php echo esc_attr($class); ?>">
            <label for="<?php echo esc_attr($aField['id']); ?>"><?php echo esc_attr($aField['heading']); ?></label>
            <div data-query="<?php echo esc_attr($aField['post_type']); ?>">
                <select id="<?php echo esc_attr($aField['id']); ?>" <?php echo esc_attr($required); ?> class="<?php echo esc_attr($aField['class']); ?> wiloke-use-select2 js_select2_without_ajax" name="<?php echo esc_attr($aField['name']); ?>" <?php echo esc_attr($multiple); ?>>
                    <option value="">---</option>
					<?php if ( !empty($oPosts) && !is_wp_error($oPosts) ) : ?>
						<?php foreach ( $oPosts as $oPost ) : ?>
							<?php
							$selected = !empty($aField['value']) && in_array($oPost->ID, $aField['value']) ? 'selected' : '';
							?>
                            <option value="<?php echo esc_attr($oPost->ID); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($oPost->post_title); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
                </select>
	            <?php self::semantic_render_desc($aField); ?>
            </div>
        </div>
		<?php
	}

	public static function getPosts($aField){
	    if ( isset($aCaching['post']) && isset($aCaching['post'][$aField['post_type']]) && !empty($aCaching['post'][$aField['post_type']]) ){
	        return $aCaching['post'][$aField['post_type']];
        }

		$oPosts = get_posts(
			array(
				'post_type'     => $aField['post_type'],
				'posts_per_page'=> -1,
				'post_status'   => 'publish'
			)
		);

		return $oPosts;
    }

	public static function semantic_render_select_user_field($aField){
		$class = 'field' . (isset($aField['wrapper_class']) ? ' ' . $aField['wrapper_class'] : '');
		$aField['class'] =  isset($aField['class']) ? $aField['class'] : '';
		$multiple = isset($aField['multiple']) && $aField['multiple'] ? 'multiple' : '';
		$required = isset($aField['required']) && $aField['required'] ? 'required' : '';
		$oUsers = get_users();
		?>
        <div class="<?php echo esc_attr($class); ?>">
            <label for="<?php echo esc_attr($aField['id']); ?>"><?php echo esc_attr($aField['heading']); ?></label>
            <div class="ui fluid <?php echo esc_attr($multiple); ?> search selection dropdown">
                <input id="<?php echo esc_attr($aField['id']); ?>" type="hidden" name="<?php echo esc_attr($aField['name']); ?>" value="<?php echo esc_attr($aField['value']); ?>" <?php echo esc_attr($required); ?>>
                <i class="dropdown icon"></i>
                <div class="default text"><?php echo isset($aField['placeholder']) ? esc_html($aField['placeholder']) : esc_html__('Select User', 'listgo'); ?></div>
                <div class="menu">
					<?php foreach ($oUsers as $oUser) : $avatar = Wiloke::getUserAvatar($oUser->data->ID); ?>
                        <div class="item" data-value="<?php echo esc_attr($oUser->data->ID); ?>" data-text="<?php echo esc_attr($oUser->data->display_name); ?>">
                            <?php if ( !empty($avatar) ) : ?>
                            <img class="ui mini avatar image" src="<?php echo esc_url(Wiloke::getUserAvatar($oUser->data->ID)); ?>" alt="<?php echo esc_attr($oUser->data->display_name); ?>">
                            <?php endif; ?>
                            <?php echo esc_html($oUser->data->display_name); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
			<?php self::semantic_render_desc($aField); ?>
        </div>
		<?php
	}

	public static function semantic_render_select_ui_field($aField){
		$class = 'field' . (isset($aField['wrapper_class']) ? ' ' . $aField['wrapper_class'] : '');
		$aField['class'] =  isset($aField['class']) ? $aField['class'] : '';
		$required = isset($aField['required']) && $aField['required'] ? 'required' : '';
		$multiple = '';
		if ( isset($aField['multiple']) && $aField['multiple'] ){
			$multiple = 'multiple';
		}
		if ( isset($aField['post_type']) && !empty($aField['post_type']) ){
			$aPosts = self::getPosts($aField);

			if ( empty($aPosts) || is_wp_error($aPosts) ){
				$aField['status'] = 'warning';
				$aField['desc'] = sprintf(esc_html__('The post type %s does not exist or there is no any post yet', 'listgo'), $aField['post_type']);
			    self::semantic_render_desc($aField);
			    return false;
            }
        }
	    ?>
        <div class="<?php echo esc_attr($class); ?>">
            <label for="<?php echo esc_attr($aField['id']); ?>"><?php echo esc_attr($aField['heading']); ?></label>
            <div class="ui fluid <?php echo esc_attr($multiple); ?> search selection dropdown">
                <input id="<?php echo esc_attr($aField['id']); ?>" type="hidden" name="<?php echo esc_attr($aField['name']); ?>" value="<?php echo esc_attr($aField['value']); ?>" <?php echo esc_attr($required); ?>>
                <i class="dropdown icon"></i>
                <div class="default text"><?php echo isset($aField['placeholder']) ? esc_html($aField['placeholder']) : esc_html__('Select Value', 'listgo'); ?></div>
                <div class="menu">
                    <?php
                        if ( isset($aPosts) ) :
                            foreach ($aPosts as $oPost) :
                    ?>
                        <div class="item" data-value="<?php echo esc_attr($oPost->ID); ?>" data-text="<?php echo esc_attr($oPost->post_title); ?>">
                            <?php if ( has_post_thumbnail($oPost->ID) ) : ?>
                            <img class="ui mini avatar image" src="<?php echo esc_url(get_the_post_thumbnail_url($oPost->ID, 'thumbnail')) ?>" alt="<?php echo esc_attr($oPost->post_title); ?>">
                            <?php endif; ?>
                            <?php echo esc_html($oPost->post_title); ?>
                        </div>
                    <?php
                            endforeach;
                        else :
                            foreach ( $aField['options'] as $aOption ) :
                    ?>
                            <div class="item" data-value="<?php echo esc_attr($aOption['value']); ?>" data-text="<?php echo esc_attr($aOption['text']); ?>">
                                <img class="ui mini avatar image" src="<?php echo esc_url($aOption['img']); ?>" alt="<?php echo esc_attr($aOption['text']); ?>">
                                <?php echo esc_html($aOption['text']); ?>
                            </div>
                    <?php
                            endforeach;
                        endif;
                    ?>
                </div>
            </div>
	        <?php self::semantic_render_desc($aField); ?>
        </div>
        <?php
    }

	public static function semantic_render_select_field($aField){
		$class = 'field' . (isset($aField['class']) ? ' ' . $aField['class'] : '');
		$required = isset($aField['required']) && $aField['required'] ? 'required' : '';
		?>
        <div class="<?php echo esc_attr($class); ?>">
            <label for="<?php echo esc_attr($aField['id']); ?>"><?php echo esc_attr($aField['heading']); ?></label>
            <select name="<?php echo esc_attr($aField['name']); ?>" <?php echo esc_attr($required); ?> id="<?php echo esc_attr($aField['id']); ?>" class="ui dropdown <?php echo esc_attr($class); ?>">
                <?php foreach ( $aField['options'] as $option => $name ) : ?>
                    <option value="<?php echo esc_attr($option); ?>" <?php selected($option, $aField['value']); ?>><?php echo esc_attr($name); ?></option>
                <?php endforeach; ?>
            </select>
	        <?php self::semantic_render_desc($aField); ?>
        </div>
		<?php
	}

	public static function semantic_render_submit($aField){
		?>
        <button class="ui button" type="submit"><?php echo esc_html($aField['name']); ?></button>
		<?php
	}

	public static function semantic_render_desc_field($aField){
		?>
        <div id="<?php echo isset($aField['desc_id']) ? esc_html($aField['desc_id']) : ''; ?>" class="ui ignored message <?php echo esc_attr($aField['status']); ?>">
            <?php Wiloke::wiloke_kses_simple_html($aField['desc']); ?>
        </div>
		<?php
	}

    public static function tbl_render_text_field($aField){
        $aField['class'] = isset($aField['class']) ? $aField['class'] : '';
        ?>
        <tr>
            <th scope="row"><?php echo esc_attr($aField['heading']); ?></th>
            <td>
                <input type="text" id="<?php echo esc_attr($aField['id']); ?>" class="<?php echo esc_attr($aField['class']); ?>" name="<?php echo esc_attr($aField['name']); ?>" value="<?php echo esc_attr($aField['value']); ?>">
                <?php self::desc($aField); ?>
            </td>
        </tr>
        <?php
    }

	public static function tbl_render_textarea_field($aField){
		$aField['class'] = isset($aField['class']) ? $aField['class'] : '';
		$aField['width'] = isset($aField['width']) ? $aField['width'] : 500;
		$aField['height'] = isset($aField['height']) ? $aField['height'] : 125;
		?>
        <tr>
            <th scope="row"><?php echo esc_attr($aField['heading']); ?></th>
            <td>
                <textarea style="max-width: 100%; width: <?php echo esc_attr($aField['width']); ?>px; height: <?php echo esc_attr($aField['height']); ?>px;" id="<?php echo esc_attr($aField['id']); ?>" class="<?php echo esc_attr($aField['class']); ?>" name="<?php echo esc_attr($aField['name']); ?>"><?php echo esc_textarea(stripslashes($aField['value'])); ?></textarea>
	            <?php self::desc($aField); ?>
            </td>
        </tr>
		<?php
	}

	public static function tbl_render_select_post_field($aField){
		$aField['class'] = isset($aField['class']) ? $aField['class'] : '';
		$multiple = isset($aField['multiple']) && $aField['multiple'] ? 'multiple' : '';
        $required = isset($aField['required']) && $aField['required'] ? 'required' : '';
		?>
        <tr>
            <th scope="row"><?php echo esc_attr($aField['heading']); ?></th>
            <td data-query="<?php echo esc_attr($aField['post_type']); ?>">
                <select style="width: 100%;" id="<?php echo esc_attr($aField['id']); ?>" <?php echo esc_attr($required); ?> class="<?php echo esc_attr($aField['class']); ?> js_select2_ajax wiloke-use-select2" name="<?php echo esc_attr($aField['name']); ?>" <?php echo esc_attr($multiple); ?>>
                    <?php
                        if ( !empty($aField['value']) ){
	                        $aField['value'] = is_array($aField['value']) ? $aField['value'] : array($aField['value']);
                            foreach ( $aField['value'] as $val ){
                            ?>
                                <option value="<?php echo esc_attr($val); ?>" selected><?php echo esc_html(get_the_title($val)); ?></option>
                            <?php
                            }
                        }
                    ?>
                </select>
				<?php self::desc($aField); ?>
            </td>
        </tr>
		<?php
	}

	public static function tbl_render_simple_select_post_field($aField){
		$aField['class'] = isset($aField['class']) ? $aField['class'] : '';
		$multiple = '';
		if ( isset($aField['multiple']) && $aField['multiple'] ){
			$multiple = 'multiple';
        }

        if ( !empty($aField['value']) ){
	        $aField['value'] = !is_array($aField['value']) ? array($aField['value']) : $aField['value'];
        }else{
	        $aField['value'] = array();
        }

		$required = isset($aField['required']) && $aField['required'] ? 'required' : '';
		$oPosts = get_posts(
            array(
                'post_type'     => $aField['post_type'],
                'posts_per_page'=> 40,
                'post_status'   => 'publish'
            )
        );

		?>
        <tr>
            <th scope="row"><?php echo esc_attr($aField['heading']); ?></th>
            <td data-query="<?php echo esc_attr($aField['post_type']); ?>">
                <select style="width: 100%;" id="<?php echo esc_attr($aField['id']); ?>" <?php echo esc_attr($required); ?> class="<?php echo esc_attr($aField['class']); ?> js_select2_without_ajax" name="<?php echo esc_attr($aField['name']); ?>" <?php echo esc_attr($multiple); ?>>
                    <option value="">---</option>
                    <?php if ( !empty($oPosts) && !is_wp_error($oPosts) ) : ?>
                        <?php foreach ( $oPosts as $oPost ) : ?>
                            <?php
                                $selected = !empty($aField['value']) && in_array($oPost->ID, $aField['value']) ? 'selected' : '';
                            ?>
                            <option value="<?php echo esc_attr($oPost->ID); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($oPost->post_title); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
				<?php self::desc($aField); ?>
            </td>
        </tr>
		<?php
	}

	public static function tbl_render_select_user_field($aField){
		$aField['class'] = isset($aField['class']) ? $aField['class'] : '';
		$multiple = isset($aField['multiple']) && $aField['multiple'] ? 'multiple' : '';
		$required = isset($aField['required']) && $aField['required'] ? 'required' : '';
		?>
        <tr>
            <th scope="row"><?php echo esc_attr($aField['heading']); ?></th>
            <td>
                <select style="width: 100%;" id="<?php echo esc_attr($aField['id']); ?>" <?php echo esc_attr($required); ?> class="<?php echo esc_attr($aField['class']); ?> js_select2_select_user_ajax wiloke-use-select2" name="<?php echo esc_attr($aField['name']); ?>" <?php echo esc_attr($multiple); ?>>
					<?php
					if ( !empty($aField['value']) ){
						$aField['value'] = is_array($aField['value']) ? $aField['value'] : array($aField['value']);
						foreach ( $aField['value'] as $val ){
							$oUser = get_userdata($val);
							?>
                            <option value="<?php echo esc_attr($val); ?>" selected><?php echo esc_html($oUser->user_login); ?></option>
							<?php
						}
					}
					?>
                </select>
				<?php self::desc($aField); ?>
            </td>
        </tr>
		<?php
	}

	public static function tbl_render_select_field($aField){
		$aField['class'] = isset($aField['class']) ? $aField['class'] : '';
		$required = isset($aField['required']) && $aField['required'] ? 'required' : '';
		?>
        <tr>
            <th scope="row"><?php echo esc_attr($aField['heading']); ?></th>
            <td>
                <select name="<?php echo esc_attr($aField['name']); ?>" <?php echo esc_attr($required); ?> id="<?php echo esc_attr($aField['id']); ?>" class="<?php echo esc_attr($aField['class']); ?>">
                    <?php foreach ( $aField['options'] as $option => $name ) : ?>
                        <option value="<?php echo esc_attr($option); ?>" <?php selected($option, $aField['value']); ?>><?php echo esc_attr($name); ?></option>
                    <?php endforeach; ?>
                </select>
	            <?php self::desc($aField); ?>
            </td>
        </tr>
		<?php
	}

	public static function tbl_render_submit($aField){
        ?>
        <tr>
            <th scope="row"><?php echo esc_attr($aField['heading']); ?></th>
            <td>
                <button type="submit" class="button button-primary"><?php echo esc_attr($aField['heading']); ?></button>
            </td>
        </tr>
        <?php
    }

	public static function tbl_render_desc_field($aField){
		?>
        <tr>
            <td colspan="2">
                <?php
                    Wiloke::wiloke_kses_simple_html($aField['desc']);
                ?>
            </td>
        </tr>
		<?php
	}
}