<?php
use WilokeListGoFunctionality\Payment\EventPayment as WilokeEventPayment;
use WilokeListGoFunctionality\Frontend\FrontendEvents as WilokeFrontendEvents;
$eventClass = 'active';
$eventRemaining = WilokeEventPayment::getRemainingEvent();
$isShowPayment = !current_user_can('edit_theme_options') && empty($eventRemaining) ? true : false;

?>
<div id="wiloke-event-settings" class="addlisting-popup-wrap hidden">

    <div class="addlisting-popup">
        <form id="wiloke-event-form" action="#" method="POST">
            <?php if ( $isShowPayment ) : $eventClass = ''; ?>
            <div id="wiloke-event-package-wrapper" class="wiloke-event-package active">
                <div class="addlisting-popup">
                    <div class="addlisting-popup__header">
                        <?php esc_html_e('Select your Event Plan', 'wiloke'); ?>
                        <div class="addlisting-popup__close"></div>
                    </div>

                    <div id="wiloke-show-event-plans-here" class="addlisting-popup__content">
                        <?php WilokeFrontendEvents::fetchEventPlan(); ?>
                    </div>

                    <div class="addlisting-popup__actions">
                        <button id="next-to-create-event" class="addlisting-popup__btn primary"><?php esc_html_e('Let\'s Create Event', 'wiloke'); ?> <i class="arrow_triangle-right"></i></button>
                    </div>

                </div>
            </div>
            <?php endif; ?>

            <div id="wiloke-event-settings-wrapper" class="wiloke-event-settings <?php echo esc_attr($eventClass); ?>">
                <div class="addlisting-popup__header">
                    <span><?php esc_html_e('Create Event', 'wiloke'); ?></span>
                    <div class="addlisting-popup__close"></div>
                </div>

                <div class="addlisting-popup__content">

                    <div class="addlisting-popup__form">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="event-title"><?php esc_html_e('Event Title', 'wiloke'); ?></label>
                                <div class="addlisting-popup__field">
                                    <input type="text" name="event_title" id="event-title" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="event-place-detail"><?php esc_html_e('Where', 'wiloke'); ?></label>
                                <div class="addlisting-popup__field">
                                    <input id="event-place-detail" type="text" placeholder="<?php esc_html_e('Detail Place', 'wiloke'); ?>" name="place_detail">
                                    <span class="addlisting-popup__field-icon"><i class="icon_pin_alt"></i></span>
                                    <input id="event-latitude" type="hidden" name="latitude">
                                    <input id="event-longitude" type="hidden" name="longitude">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="event-starts-from"><?php esc_html_e('This event starts from', 'wiloke'); ?></label>
                                <div class="addlisting-popup__field">
                                    <input id="event-starts-from" type="time" name="start_at" placeholder="<?php esc_html_e('08:00', 'wiloke'); ?>">
                                    <span class="addlisting-popup__field-icon"><i class="icon_clock_alt"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="event-starts-at">&nbsp;</label>
                                <div class="addlisting-popup__field">
                                    <input id="event-starts-at" class="event-date-picker" type="text" placeholder="<?php echo date('M d, Y'); ?>" name="start_on" required>
                                    <span class="addlisting-popup__field-icon"><i class="icon_table"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="event-end-on"><?php esc_html_e('To', 'wiloke'); ?></label>
                                <div class="addlisting-popup__field">
                                    <input id="event-end-on" type="time" placeholder="<?php esc_html_e('08:00', 'wiloke'); ?>" name="end_at">
                                    <span class="addlisting-popup__field-icon"><i class="icon_clock_alt"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="events-end-at">&nbsp;</label>
                                <div class="addlisting-popup__field">
                                    <input  id="events-end-at" type="text" class="event-date-picker" placeholder="<?php echo date('M d, Y', time() + 36000); ?>" name="end_on" required>
                                    <span class="addlisting-popup__field-icon"><i class="icon_table"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="addlisting-popup__field">
                                    <div class="add-listing__upload-img add-listing__upload-single wiloke-add-featured-image">
                                        <div class="add-listing__upload-preview" style="">
                                            <span class="add-listing__upload-placeholder"><i class="icon_image"></i><span class="add-listing__upload-placeholder-title"><?php esc_html_e('Featured Image', 'wiloke'); ?></span></span>
                                        </div>

                                        <input type="hidden" id="event-featured-image" class="wiloke-insert-id" name="event_featured_image" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <label for="event-description"><?php esc_html_e('Description', 'wiloke'); ?></label>
                                <div class="addlisting-popup__field">
                                    <?php
                                        wp_editor('', 'event_content', array(
	                                        'teeny'  => false,
	                                        'reinit' => true,
                                            'media_buttons' => false,
                                            'textarea_rows'=>5
                                        ));
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="button-wrapper addlisting-popup__actions">
                    <div id="listgo-add-event-msg" class="listg-event-msg" style="color: red;"></div>
		            <?php if ( $isShowPayment ) :  ?>
                        <button id="listgo-back-event-plan" class="addlisting-popup__btn"><?php esc_html_e('Back', 'wiloke'); ?></button>
                        <button id="listgo-next-to-payment-method" class="addlisting-popup__btn primary"><?php esc_html_e('Pay & Publish', 'wiloke'); ?></button>
                        <button type="submit" id="listgo-create-event" class="addlisting-popup__btn primary hidden"><?php esc_html_e('Update Event', 'wiloke'); ?></button>
		            <?php else: ?>
                        <button id="listgo-cancel-event" class="cancel-shortcode addlisting-popup__btn"><?php esc_html_e('Cancel', 'wiloke'); ?></button>
                        <button type="submit" id="listgo-create-event" class="addlisting-popup__btn primary wiloke-insert-shortcode"><?php esc_html_e('Create Event', 'wiloke'); ?></button>
		            <?php endif; ?>
                </div>
            </div>

	        <?php if ( $isShowPayment ) : ?>
                <div id="wiloke-event-payment-method-wrapper" class="wiloke-event-package wiloke-event-method">
                    <div class="addlisting-popup">
                        <div class="addlisting-popup__header">
					        <?php esc_html_e('Select Payment Method', 'wiloke'); ?>
                            <div class="addlisting-popup__close"></div>
                        </div>
                        <div id="wiloke-show-event-plans-here" class="addlisting-popup__content">
					        <?php WilokeFrontendEvents::paymentMethods(); ?>
                        </div>
                    </div>

                    <div class="button-wrapper addlisting-popup__actions">
                        <div id="listgo-payment-event-msg" class="listg-event-msg"></div>
		                <?php if ( empty($eventRemaining) ) :  ?>
                            <button id="listgo-back-event-form-settings" class="addlisting-popup__btn"><?php esc_html_e('Back', 'wiloke'); ?></button>
                            <button id="listgo-pay-and-publish" class="addlisting-popup__btn primary"><?php esc_html_e('Pay & Publish', 'wiloke'); ?></button>
		                <?php endif; ?>
                    </div>
                </div>
	        <?php endif; ?>

        </form>
    </div>

</div>