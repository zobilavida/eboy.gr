;(function ($) {
	'use strict';

	class wilokeMediaUpload{
		constructor(){
			this.$trigger = $('.wiloke-js-upload');
			this.init();
		}

		init(){
			if ( this.$trigger.length ){
				// ADD IMAGE LINK
				this.$trigger.on( 'click', (event=>{
					let $target = $(event.currentTarget);
					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( $target.data('frame') ) {
						$target.data('frame').open();
						return false;
					}

					// Create a new media frame
					let frame = wp.media({
						title: 'Select or Upload Media',
						button: {
							text: 'Use this media'
						},
						multiple: false  // Set to true to allow multiple files to be selected
					});
					$target.data('frame', frame);

					// When an image is selected in the media frame...
					$target.data('frame').on( 'select', (()=>{
						// Get media attachment details from the frame state
						let attachment = $target.data('frame').state().get('selection').first().toJSON();
						// Send the attachment URL to our custom image input field.
						$target.find('.wiloke-preview').attr( 'src', attachment.url );
						// Send the attachment id to our hidden input
						$target.find('.wiloke-insert-id').val( attachment.id );

						if( $target.hasClass('profile-background') ) {
							$target.css('background-image', 'url('+ attachment.url +')')
						}
					}));

					// Finally, open the modal on click
					$target.data('frame').open();
				}));
			}
		}
	}

	class wilokeUpdatePlan{
		constructor(){
			this.$app = $('#wiloke-modify-subscription-plan-form');
			this.$controlPlan = $('#wiloke-submission-customer-plan');
			this.init();
		}
		init(){
			if ( !this.$app.length  ){
				return false;
			}

			this.proceedAjax = false;
			this.$confirmPopup = $('#wiloke-confirm-change-plan-wrapper');
			this.$wantToChangeBtn = $('#wiloke-want-to-change-plan');
			this.$cancelChangeBtn = $('#wiloke-cancel-change-plan');
			this.$proceedWith = $('#wiloke-submission-proceed-with');
			this.gateWay = null;
			this.token = null;
			TCO.loadPubKey('sandbox');

			this.listenGateway();
			this.confirmChangePlan();
			this.cancelChangePlan();
			this.previewPlan();

			this.$btn = this.$app.find('button');
			this.$ajax = null;
			this.$successMsg = $('#wiloke-success-change-plan');
			this.$failedMsg = $('#wiloke-failed-change-plan');

			this.$app.on('submit', (event=>{
				event.preventDefault();

				if ( !this.proceedAjax ){
					this.$confirmPopup.addClass('wil-modal--open');
					return false;
				}

				if ( this.$ajax !== null && this.$app.status !== 200 ){
					this.$ajax.abort();
				}

				this.$btn.addClass('loading');

				if ( this.gateWay !== '2checkout' ){
					this.ajaxRequest();
				}else{
					if ( this.checkCardForm() ){
						this.twoCheckoutTokenRequest();
					}else{
						$(window).scrollTop($('#wiloke-my-credi-debit-card-form').offset().top);
						this.$btn.removeClass('loading');
					}
				}

			}));
		}

		checkCardForm(){
			let passed = true;
			$('#wiloke-my-credi-debit-card-form').find('.row input').each(function(){
				if ( $(this).attr('name') !== '' ){
					if ( $(this).val() === '' ){
						$(this).closest('.form-item').addClass('validate-required');
						passed = false;
					}
				}
			});

			return passed;
		}

		previewPlan(){
			let xhr = null;
			let aCachingPlan = [];
			let $previewArea = $("#wiloke-show-package-detail");
			this.$controlPlan.change((event=>{
				if ( xhr !== null && xhr.status !== 200 ){
					xhr.abort();
				}
				let $target = $(event.target);
				$previewArea.addClass('loading');
				let packageID = $target.val();

				if ( !_.isUndefined(aCachingPlan[packageID]) ){
					$previewArea.html(aCachingPlan[packageID]);
					$previewArea.removeClass('loading');
					return false;
				}

				xhr = $.ajax({
					type: 'GET',
					url: WILOKE_GLOBAL.ajaxurl,
					data: {action: 'wiloke_submission_render_package_preview', packageID: packageID},
					success: (response=>{
						$previewArea.html(response.data.msg);
						$previewArea.removeClass('loading');
						aCachingPlan[packageID] = response.data.msg;
					})
				})
			}));
		}

		confirmChangePlan(){
			this.$wantToChangeBtn.on('click', (event=>{
				event.preventDefault();
				this.$confirmPopup.removeClass('wil-modal--open');
				this.gateWay = this.$proceedWith.val();
				this.proceedAjax = true;
				this.$app.trigger('submit');
			}))
		}

		listenGateway(){
			this.$proceedWith.change((event)=>{
				this.gateWay = this.$proceedWith.val();
			})
		}

		errorCallback(data){
			if (data.errorCode === 200) {
				// This error code indicates that the ajax call failed. We recommend that you retry the token request.
			} else {
				alert(data.errorMsg);
			}
			this.$btn.removeClass('loading');
		}

		successCallback(data){
			// Set the token as the value for the token input
			this.token = data.response.token.token;
			// IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
			this.ajaxRequest();
		}

		twoCheckoutTokenRequest(){
			// Setup token request arguments
			let args = {
				sellerId: WILOKE_GLOBAL.twoCheckoutSellerID,
				publishableKey: WILOKE_GLOBAL.twoCheckoutPublishableKey,
				ccNo: $("#cardNumber").val(),
				cvv: $("#cvv").val(),
				expMonth: $("#expMonth").val(),
				expYear: $("#expYear").val()
			};
			// Make the token request
			TCO.requestToken((data=>{
				this.successCallback(data)
			}), (data=>{
				this.errorCallback(data)
			}), args);
		}

		cancelChangePlan(){
			this.$cancelChangeBtn.on('click', (event=>{
				event.preventDefault();
				this.proceedAjax = false;
				this.$confirmPopup.removeClass('wil-modal--open');
			}))
		}

		ajaxRequest(){
			this.$ajax = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_submission_change_plan_with_'+this.gateWay, security: WILOKE_GLOBAL.wiloke_nonce, packageID: this.$app.find('#wiloke-submission-customer-plan').val(), token: this.token},
				success: (response=>{
					if ( typeof response.success === 'undefined' ){
						this.$successMsg.addClass('hidden');
						this.$failedMsg.find('.wil-alert-message').html(response);
						this.$failedMsg.removeClass('hidden');
					}else{
						$('#wiloke-transaction-message').addClass('hidden');
						if ( response.success ){
							if ( this.gateWay === '2checkout' ){
								this.$failedMsg.addClass('hidden');
								this.$successMsg.find('.wil-alert-message').html(response.data.msg);
								this.$successMsg.removeClass('hidden');

								setTimeout(function () {
									location.reload(true);
								}, 4000);
							}else{
								if ( response.data.status !== 'redirect' ){
									this.$failedMsg.addClass('hidden');
									this.$successMsg.find('.wil-alert-message').html(response.data.msg);
									this.$successMsg.removeClass('hidden');
								}else{
									window.location.href = decodeURIComponent(response.data.msg);
								}
							}
						}else{
							this.$successMsg.addClass('hidden');
							this.$failedMsg.find('.wil-alert-message').html(response.data.msg);
							this.$failedMsg.removeClass('hidden');
						}
					}
					this.proceedAjax = false;
					this.$btn.removeClass('loading');
				})
			})
		}
	}

	class wilokeBillOutstandingAmount{
		constructor(){
			this.$app = $('#wiloke-billing-outstanding-amount-form');
			this.init();
		}

		init(){
			this.xhr = null;
			if ( this.$app.length > 0 ){
				let $btn = this.$app.find('button'),
					$successMsg = $('#wiloke-success-bill-outstanding'),
					$failedMsg = $('#wiloke-failed-bill-outstanding');
				this.$app.on('click', (event=>{
					event.preventDefault();
					$btn.addClass('loading');
					if ( this.xhr !== null && this.xhr.status !== 200 ){
						this.xhr.abort();
					}

					this.xhr = $.ajax({
						type: 'POST',
						url: WILOKE_GLOBAL.ajaxurl,
						data: {action: 'wiloke_paypal_bill_outstanding_amount', security: WILOKE_GLOBAL.wiloke_nonce},
						success: (response=>{
							if ( response.success ){
								$successMsg.find('.wil-alert-message').html(response.data.msg);
								$successMsg.removeClass('hidden');
								$failedMsg.addClass('hidden');
								this.$app.remove();
							}else{
								$failedMsg.find('.wil-alert-message').html(response.data.msg);
								$failedMsg.removeClass('hidden');
								$successMsg.addClass('hidden');
							}
							$btn.removeClass('loading');
						})
					})
				}))
			}
		}
	}

	class wilokeRenderPlanMessage{
		constructor(){
			this.$msg = $('#wiloke-transaction-message');
			this.message();
		}

		message(){
			if ( this.$msg.length ){
				this.$form = this.$msg.closest('#wiloke-modify-subscription-plan-form');
				this.$form.addClass('loading');
				$.ajax({
					type: 'GET',
					url: WILOKE_GLOBAL.ajaxurl,
					data: {security: WILOKE_GLOBAL.wiloke_nonce, action: 'wiloke_change_plan_message', token: this.$msg.data('token')},
					success: (response=>{
						if ( typeof response.data.msg !== 'undefined' ){
							if ( response.success ){
								this.$msg.attr('class', 'wil-alert wil-alert-has-icon alert-success');
								this.$msg.html('<span class="wil-alert-icon"><i class="icon_box-checked"></i></span><p class="wil-alert-message">'+response.data.msg+'</p>');
								let $packageSelected = $('#wiloke-submission-customer-plan').find('option[value="'+response.data.packageID+'"]');
								$packageSelected.attr('selected', 'selected');
							}else{
								this.$msg.attr('class', 'wil-alert wil-alert-has-icon alert-danger');
								this.$msg.html('<span class="wil-alert-icon"><i class="icon_error-triangle_alt"></i></span><p class="wil-alert-message">'+response.data.msg+'</p>');
							}
						}else{
							this.$msg.addClass('hidden');
						}

						this.$form.removeClass('loading');
					})
				})
			}
		}
	}

	class wilokeSaveCard{
		constructor(){
			this.$app = $("#wiloke-my-credi-debit-card-form");
			this.init();
		}

		init(){
			if ( this.$app.length > 0 ){
				this.xhr = null;
				this.$msg = $("#wiloke-save-card-msg-wrapper");
				this.$app.on('submit', (event=>{
					event.preventDefault();

					if ( this.xhr !== null && this.xhr.status !== 200 ){
						this.xhr.abort();
					}
					this.$app.addClass('loading');
					this.xhr = $.ajax({
						type: 'POST',
						url: WILOKE_GLOBAL.ajaxurl,
						data: {action: 'wiloke_save_card', data: this.$app.serializeArray()},
						success: (response=>{
							this.$msg.find('.wil-alert-message').html(response.data.msg);
							this.$msg.removeClass('hidden');
							this.$app.removeClass('loading');
							this.$app.find('.form-item').each(function () {
								$(this).removeClass('validate-required');
							})
						})
					})
				}))
			}
		}
	}

	function colorPicker(){
		$('.colorpicker').each(function () {
			let $this = $(this);
			$this.spectrum({
				showAlpha: true,
				showInput: true,
				allowEmpty:true,
				change: function(color) {
					$this.val(color.toRgbString());
				}
			});
		})

	}

	$(document).ready(function () {
		new wilokeMediaUpload();
		new wilokeUpdatePlan();
		new wilokeBillOutstandingAmount();
		new wilokeSaveCard();
		colorPicker();
	});

	$(window).load(function () {
		new wilokeRenderPlanMessage();
	})

})(jQuery);