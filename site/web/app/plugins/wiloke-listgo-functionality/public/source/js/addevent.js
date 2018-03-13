;(function ($) {
	'use strict';

	class wilokeMediaUpload{
		constructor($target){
			this.$trigger = $target;
			this.init();
		}

		template(url, id){
			let item = _.template("<li class='gallery-item bg-scroll' data-id='<%- id %>' style='background-image: url(<%- backgroundUrl %>)'><span class='wil-addlisting-gallery__list-remove'>Remove</span></li>");
			return item({
				backgroundUrl: url,
				id: id
			});
		}

		init(){
			if ( this.$trigger.length ){

				// ADD IMAGE LINK
				this.$trigger.on( 'click', (event=>{
					let $target = $(event.currentTarget);
					event.preventDefault();

					let isMultiple = $target.data('multiple');
					// If the media frame already exists, reopen it.
					if ( $target.data('frame') ) {
						$target.data('frame').open();
						return false;
					}

					// Create a new media frame
					let frame = wp.media({
						title: '',
						button: {
							text: 'Select'
						},
						multiple: isMultiple  // Set to true to allow multiple files to be selected
					});
					$target.data('frame', frame);
					let imgSize = typeof $target.data('imgsize') !== 'undefined' ? $target.data('imgsize') : 'thumbnail';

					// When an image is selected in the media frame...
					$target.data('frame').on( 'select', (()=>{
						// Get media attachment details from the frame state

						if ( isMultiple ){
							let aAttachemnts = $target.data('frame').state().get('selection').toJSON();
							let imgs = '';
							_.forEach(aAttachemnts, (oAttachment=>{
								let thumbnailUrl = !_.isUndefined(oAttachment.sizes.thumbnail) && oAttachment.sizes[imgSize] ? oAttachment.sizes[imgSize].url : oAttachment.url;
								imgs += this.template(thumbnailUrl, oAttachment.id);
							}));
							$target.parent().before(imgs);
						}else{
							let attachment = $target.data('frame').state().get('selection').first().toJSON();
							// Send the attachment URL to our custom image input field.
							let thumbnailUrl = !_.isUndefined(attachment.sizes[imgSize]) && attachment.sizes[imgSize] ? attachment.sizes[imgSize].url : attachment.url;

							$target.find('.wiloke-preview').attr( 'src', thumbnailUrl );
							$target.find('.add-listing__upload-preview').css( 'background-image', 'url('+attachment.url+')' );
							// Send the attachment id to our hidden input
							$target.find('.wiloke-insert-id').val( attachment.id );
						}

					}));

					// Finally, open the modal on click
					$target.data('frame').open();
				}));
			}
		}
	}

	class addEvent{
		constructor(){
			this.$addEvent = $('#listgo-add-event');
			this.init();
		}

		formChanged(){
			this.$form.on('change', (event=>{
				this.allowedUpdate = true;
				this.$form.find('input[required], textarea[required]').each(function () {
					if ( $(this).val() !== '' ){
						$(this).parent().removeClass('validate-required');
					}
				});
			}));
		}

		eventPopup(){
			this.$addEvent.on('click', (event=>{
				event.preventDefault();
				if ( !this.isEdit ){
					if ( this.$packageWrapper.length ){
						this.$packageWrapper.addClass('active').removeClass('prev');
						this.$eventSettingWrapper.removeClass('active').addClass('prev');
						this.$form.find('.addlisting-popup__actions button').removeClass('hidden');
						this.$saveBtn.addClass('hidden');
					}

					this.resetForm();
					this.$saveBtn.html(WILOKE_LISTGO_EVENTS.create_btn);
					this.$popup.find('.addlisting-popup__header span').html(WILOKE_LISTGO_EVENTS.create_btn);
				}else{
					this.$saveBtn.html(WILOKE_LISTGO_EVENTS.update_event);
					this.$popup.find('.addlisting-popup__header span').html(WILOKE_LISTGO_EVENTS.update_event);
				}
				this.$popup.removeClass('hidden');
			}))
		}

		resetForm(){
			this.$eventSettingWrapper.find('input').val('');
			this.$eventSettingWrapper.find('textarea').val('');
			this.$eventSettingWrapper.find('.add-listing__upload-preview').css('background', '');
			this.resetTinymce();
		}

		datePicker(){
			let self = this;
			this.$datePicker.each(function(){
				$(this).datepicker({
					minDate: 0,
					dateFormat: self.convertWPDateFormatToDatePickerFormat(),
					monthNamesShort: ['Jan','Feb','Mar','Apr','Maj','Jun','Jul',
                      'Aug','Sep','Okt','Nov','Dec'],
                  	monthNames: [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ]
				});
			});
		}

		convertWPDateFormatToDatePickerFormat(){
			switch (WILOKE_LISTGO_EVENTS.date_format){
				case 'F j, Y':
					return 'MM dd, yy';
					break;
				case 'Y-m-d':
					return 'yy-MM-dd';
					break;
				case 'm/d/Y':
					return 'MM-dd-yy';
					break;
				case 'd/m/Y':
					return 'dd-MM-yy';
					break;
				default:
					return 'd MM, yy';
					break;
			}
		}

		mediaUpload(){
			this.$popup.find('.wiloke-add-featured-image').each(function () {
				new wilokeMediaUpload($(this));
			});
		}

		autoComplete(){
			let searchBox = new google.maps.places.SearchBox(document.getElementById('event-place-detail'));
			searchBox.addListener('places_changed', (event=>{
				let places = searchBox.getPlaces();

				if (places.length === 0) {
					return false;
				}

				places.forEach(function(place) {
					if (!place.geometry) {
						return false;
					}

					$('#event-latitude').val(place.geometry.location.lat());
					$('#event-longitude').val(place.geometry.location.lng());
				});

			}))

		}

		getContent(){
			if ($("#wp-event_content-wrap").hasClass("tmce-active")){
				return tinyMCE.activeEditor.getContent();
			}else{
				return $('#event_content').val();
			}
		}

		resetTinymce(){
			if ($("#wp-event_content-wrap").hasClass("tmce-active")){
				tinyMCE.activeEditor.setContent('');
			}else{
				$('#event_content').val('');
			}
		}

		closePopup(){
			this.$closed.on('click', (event)=>{
				event.preventDefault();
				this.$popup.addClass('hidden');
				this.previousID = this.eventID;
				this.eventID = null;
				this.isEdit = false;
				this.allowedUpdate = false;
			})
		}

		parseFormData(){
			let data = this.$form.serializeArray();
			data = window.btoa(encodeURIComponent(JSON.stringify(data)));
			return data;
		}

		saveCard(){
			$('#listgo-save-card').on('click', (event=>{
				event.preventDefault();
				let $target = $(event.currentTarget);
				$target.addClass('loading');
				let $form = $('#wiloke-credit-debit-card');
				$form.wrap('<form></form>');

				$.ajax({
					type: 'POST',
					url: WILOKE_GLOBAL.ajaxurl,
					data: {
						action: 'wiloke_save_card',
						data: $form.parent().serializeArray()
					},
					success: (response=>{
						$form.unwrap('form');
						if ( response.success ){
							$target.html(WILOKE_LISTGO_EVENTS.saved_btn);
						}else{
							$target.html(WILOKE_LISTGO_EVENTS.error_btn);
						}
						$target.removeClass('loading');
					})
				})
			}))
		}

		ajaxSubmit(){
			if ( this.ajax !== null && this.ajax.status !== 200 ){
				this.ajax.abort();
			}

			this.$popup.children().addClass('loading');

			this.ajax = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_listgo_event_update_event', security: WILOKE_GLOBAL.wiloke_nonce, listingID: WILOKE_GLOBAL.postID, eventID: this.eventID, data: this.parseFormData(), event_content: this.getContent()},
				success: (response=>{
					this.$popup.children().removeClass('loading');

					if ( !response.success ){
						this.$msg.html(response.data.msg);
					}else{
						let eventRendered = response.data.msg;
						if ( !this.$eventTab.length  ){
							eventRendered = '<div id="tab-event" class="tab__panel active">' + eventRendered + '</div>';
							$('.tab__content').html(eventRendered);
							this.eventID = response.data.eventID;
						}else{
							if ( this.eventID !== null ){
								$('#event-'+this.eventID).html($(eventRendered).html());
							}else{
								if ( this.$addEvent.hasClass('inline-add-event') ){
									this.$eventTab.find('#listgo-add-event').after(eventRendered);
								}else{
									if ( this.$eventTab.find('.listing-single__event').length ){
										this.$eventTab.prepend(eventRendered);
									}else{
										this.$eventTab.html(eventRendered);
									}
								}

								this.eventID = response.data.eventID;
							}
						}

						let $countdown = $('#listgo-countdown-editing-period-'+this.eventID);
						this.countdown(new Date(), $countdown);

						let remaining = this.$addEvent.data('remaining');
						remaining = parseInt(remaining, 10);
						if ( !this.isEdit ){
							if ( remaining <= 1 ){
								this.scheduleTriggerEvenTab();
								location.reload();
							}else{
								this.$addEvent.data('remaining', remaining - 1);
							}
						}

						this.$closed.trigger('click');
						this.$navEvent.removeClass('zero-event');
					}
				})
			})
		}

		submit(){
			this.$form.on('submit', (event=>{
				event.preventDefault();
				this.ajaxSubmit();
			}))
		}

		editEvent(){
			$(document).on('click', '.listing-single__event-edit', (event=>{
				event.preventDefault();

				this.isEdit  = true;
				this.eventID = $(event.currentTarget).attr('data-id');
				this.$addEvent.trigger('click');
				this.$form.find('.addlisting-popup__actions button').addClass('hidden');
				this.$saveBtn.removeClass('hidden');
				this.$packageWrapper.removeClass('active');
				this.$eventSettingWrapper.addClass('active').removeClass('prev');

				if ( this.eventID != this.previousID ){
					this.$popup.children().addClass('loading');

					$.ajax({
						type: 'POST',
						url: WILOKE_GLOBAL.ajaxurl,
						data: {security: WILOKE_GLOBAL.wiloke_nonce, eventID: this.eventID, action: 'wiloke_get_event_data'},
						success: (response=>{
							if ( !response.success ){
								this.$eventSettingWrapper.find('.addlisting-popup__form').html(response.data.msg);
							}else{
								let oData = response.data, key = '';
								for ( key in oData ){
									switch (key){
										case 'event_content':
											tinymce.get('event_content').setContent(oData[key]);
											break;
										case 'event_featured_image_url':
											this.$popup.find('.add-listing__upload-preview').css('background', 'url('+oData[key]+')');
											break;
										default:
											this.$popup.find('[name="'+key+'"]').attr('value', oData[key]);
											break;
									}
								}
							}

							this.$popup.children().removeClass('loading');
						})
					})
				}
				this.allowedUpdate = false;
			}))
		}

		zeroEvent(){
			if (!this.$navEvent.hasClass('zero-event')) {
				return false;
			}

			this.$navEvent.on('click', (event=> {
				if ($(event.currentTarget).hasClass('zero-event')) {
					this.$popup.removeClass('hidden');
				}
			}))
		}

		selectPackage(){
			$('#next-to-create-event').on('click', (event=>{
				event.preventDefault();
				if ( typeof TCO !== 'undefined' ){
					TCO.loadPubKey(WILOKE_GLOBAL.twocheckoutMode);
				}
				this.selectedEventPlan = $(event.currentTarget).data('id');
				this.$packageWrapper.removeClass('active').addClass('prev');
				this.$eventSettingWrapper.removeClass('prev').addClass('active');
			}))
		}

		backEventPlan(){
			$('#listgo-back-event-plan').on('click', (event=>{
				event.preventDefault();
				this.$eventSettingWrapper.removeClass('active');
				this.$packageWrapper.removeClass('prev').addClass('active');
			}))
		}

		countdown(today, $this){
			let createdAt = new Date($this.attr('data-created')),
				diffMs = (today - createdAt),
				timer2 = (30 - Math.round(((diffMs % 86400000) % 3600000) / 60000)) + ':00';
			let interval = setInterval(function() {
				let timer = timer2.split(':');
				//by parsing integer, I avoid all extra string processing
				let minutes = parseInt(timer[0], 10);
				let seconds = parseInt(timer[1], 10);
				--seconds;
				minutes = (seconds < 0) ? --minutes : minutes;
				if (minutes < 0) clearInterval(interval);
				seconds = (seconds < 0) ? 59 : seconds;
				seconds = (seconds < 10) ? '0' + seconds : seconds;
				//minutes = (minutes < 10) ?  minutes : minutes;
				$this.html(minutes + ':' + seconds);
				timer2 = minutes + ':' + seconds;
			}, 1000);
		}

		setCountdown(){
			let $coutdown = $('.listing-single__event-countdown-edit-time');
			if ( $coutdown.length ){
				let self = this,
				    today = new Date();
				$coutdown.each(function () {
					self.countdown(today, $(this));
				})
			}
		}

		paymentMethod(){
			$('.listgo-select-payment-method').on('click', (event=>{
				event.preventDefault();
				this.selectedpaymentMethod = $(event.currentTarget).data('method');
			}))
		}

		nextToPaymentMethod(){
			$('#listgo-next-to-payment-method').on('click', (event=>{
				event.preventDefault();
				let passed = true;
				this.$eventSettingWrapper.find('input[required], textarea[required]').each(function () {
					if ( $(this).val() === '' ){
						passed = false;
						$(this).parent().addClass('validate-required');
					}
				});
				if ( passed ){
					this.selectedpaymentMethod = $(event.currentTarget).data('method');
					this.$eventSettingWrapper.removeClass('active').addClass('prev');
					this.$eventSettingWrapper.next().removeClass('prev').addClass('active');
				}
			}))
		}

		showCardForm(){
			$('input[name="event_payment_method"]').on('change', function () {
				if ( $(this).val() === '2checkout' ){
					$('#listgo-gateway-2checkout').addClass('active');
				}else{
					$('#listgo-gateway-2checkout').removeClass('active');
				}
			});
		}

		backEventFormSettings(){
			$('#listgo-back-event-form-settings').on('click', (event=>{
				event.preventDefault();
				this.$eventSettingWrapper.next().removeClass('active');
				this.$eventSettingWrapper.removeClass('prev').addClass('active');
			}))
		}

		buyEventPlan(){
			$('#listgo-pay-and-publish').on('click', (event=>{
				event.preventDefault();
				let method = $('input[name="event_payment_method"]:checked').val();
				if ( method === 'paypal' ){
					this.buyPlanAjax();
				}else if(method === '2checkout'){
					this.twoCheckoutTokenRequest();
				}
			}))
		}

		buyPlanAjax(){
			this.$popup.children().addClass('loading');

			if ( this.ajaxBuyEventPlan !== null ){
				if( this.ajaxBuyEventPlan.status !== 200 ){
					this.ajaxBuyEventPlan.abort();
				}
			}

			this.ajaxBuyEventPlan = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_buy_event_plan', security: WILOKE_GLOBAL.wiloke_nonce, listingID: WILOKE_GLOBAL.postID, paymentMethod: this.selectedpaymentMethod, eventPlanID: this.selectedEventPlan, data: this.parseFormData(), token: this.token, event_content: this.getContent()},
				success: (response=>{
					if ( response.success ){
						if ( typeof response.data.status !== 'undefined' && response.data.status === 'redirect' ){
							this.scheduleTriggerEvenTab();
							window.location.href = decodeURIComponent(response.data.msg);
						}else{

						}
					}else{
						$('#listgo-payment-event-msg').html(response.data.msg).removeClass('hidden');
					}

					this.$popup.children().removeClass('loading');
				})
			})
		}

		twoCheckoutSuccessCallback(data){
			// Set the token as the value for the token input
			this.token = data.response.token.token;
			// IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
			this.buyPlanAjax();
		}

		twoCheckoutErrorCallback(data){
			if (data.errorCode === 401) {
				$('#listgo-buy-plan-msg').html(WILOKE_LISTGO_EVENTS.twoCheckoutMissingCardData).removeClass('hidden');
			}else{
				$('#listgo-buy-plan-msg').html(data.errorMsg).removeClass('hidden');
			}
			this.$popup.children().removeClass('loading');
		}

		twoCheckoutTokenRequest(){
			// Setup token request arguments
			let args = {
				sellerId: WILOKE_GLOBAL.twoCheckoutSellerID,
				publishableKey: WILOKE_GLOBAL.twoCheckoutPublishableKey,
				ccNo: $("#cardNumber").val(),
				cvv: $("#cardCvv").val(),
				expMonth: $("#expMonth").val(),
				expYear: $("#expYear").val()
			};

			TCO.requestToken((data=>{
				this.twoCheckoutSuccessCallback(data)
			}), (data=>{
				this.twoCheckoutErrorCallback(data)
			}), args);
		}

		triggerEventTab(){
			let isTrigger = localStorage.getItem('wiloke_listgo_trigger_tab');
			if ( isTrigger ){
				$('.tab-nav-event').find('a[href="#tab-event"]').trigger('click');
				localStorage.removeItem('wiloke_listgo_trigger_tab');
			}
		}

		scheduleTriggerEvenTab(){
			localStorage.setItem('wiloke_listgo_trigger_tab', true);
		}

		init(){
			if ( !this.$addEvent.length ){
				return false;
			}

			this.ajax = null;
			this.token = null;
			this.selectedEventPlan = null;
			this.ajaxBuyEventPlan = null;
			this.selectedpaymentMethod = null;
			this.ajaxEvent = null;
			this.allowedUpdate = false;
			this.eventID = null;
			this.previousID = null;
			this.isEdit = false;
			this.$eventTab = $('#tab-event');
			this.$navEvent = $('.tab-nav-event');
			this.$packageWrapper = $('#wiloke-event-package-wrapper');
			this.$eventSettingWrapper = $('#wiloke-event-settings-wrapper');
			this.$eventPlansWrapper = $('#wiloke-show-event-plans-here');
			this.$saveBtn = $('#listgo-create-event');
			this.$popup = $('#wiloke-event-settings');
			this.$datePicker = $('.event-date-picker');
			this.$closed = $('#wiloke-event-settings .addlisting-popup__close, #wiloke-event-settings .cancel-shortcode');
			this.$msg = $('#listgo-add-event-msg');
			this.$form = $('#wiloke-event-form');
			this.formChanged();
			this.nextToPaymentMethod();
			this.backEventFormSettings();
			this.autoComplete();
			this.selectPackage();
			this.mediaUpload();
			this.closePopup();
			this.showCardForm();
			// this.paymentMethod();
			this.backEventPlan();
			this.triggerEventTab();
			this.saveCard();
			this.buyEventPlan();
			this.zeroEvent();
			this.submit();
			this.editEvent();
			this.eventPopup();
			this.datePicker();
			this.setCountdown();
		}
	}

	class removeEvent{
		constructor(){
			this.$tabEvent = $('#tab-event');
			this.init();
		}

		init(){
			if ( !this.$tabEvent.length ){
				return false;
			}

			this.$popup = $('#wiloke-form-remove-event-wrapper');
			this.eventID = null;
			this.confirm();
			this.xhr = null;

			this.$tabEvent.on('click', '.listing-single__event-remove', (event=>{
				event.preventDefault();
				this.$popup.addClass('wil-modal--open');
				this.eventID = $(event.currentTarget).data('id');
			}))
		}

		confirm(){
			$('#listgo-want-to-remove-event').on('click', (event=>{
				event.preventDefault();
				this.$popup.removeClass('wil-modal--open');
				this.fadeOutEvent();
				this.ajaxSubmit();
			}));

			$('#listgo-cancel-remove-event').on('click', (event=>{
				event.preventDefault();
				this.$popup.removeClass('wil-modal--open');
				this.ajaxSubmit();
			}));
		}

		fadeOutEvent(){
			let self = this;
			$('#event-'+this.eventID).fadeOut('slow', function () {
				$(this).remove();
				if ( !self.$tabEvent.find('.listing-single__event').length && $('#listgo-add-event').hasClass('inline-add-event') ){
					let $navTab = $('.tab__nav').find('.tab-nav-event').removeClass('upcomming ongoing');
					$navTab.find('a').html(WILOKE_LISTGO_EVENTS.create_btn+'<span class="wil-sos"></span>');
				}
			});
		}

		ajaxSubmit(){

			if ( this.xhr !== null && this.xhr.status !== 200 ){
				this.xhr.abort();
			}

			$.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_listgo_delete_event', eventID: this.eventID, security: WILOKE_GLOBAL.wiloke_nonce},
				success: (response=>{
					this.eventID = null;
				})
			})
		}
	}

	$(document).ready(function () {
		new addEvent();
		new removeEvent();
	});

})(jQuery);