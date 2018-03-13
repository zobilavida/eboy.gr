;(function ($) {
	'use strict';

	class WilokeProceedPaymentWithTwoCheckout{
		constructor(){
			this.$app = $('#wiloke-proceed-with-2checkout');
			this.init();
		}

		init(){
			if ( this.$app.length ){
				this.xhr = null;
				this.token = null;
				this.formData = null;
				this.$creditCardPopup = $('#wiloke-form-two-checkout-wrapper');
				this.$form = $('#wiloke-form-creditcard-with-2checkout');
				this.closePopup();
				this.pay();
				this.$app.on('click', ((event)=>{
					event.preventDefault();
					this.$app.addClass('loading');
					this.showPopup();
				}));
			}
		}

		ajaxHandleSubmit(){
			if ( (this.xhr !== null) && this.xhr.status !== 200 ){
				this.xhr.abort();
			}
			this.xhr = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_submission_handle_2checkout', token: this.token, formData: this.$form.serializeArray(), security: WILOKE_GLOBAL.wiloke_nonce, post_id: $('#post_id').val()},
				success: (response=>{
					if ( response.success ){
						window.location.href = response.data.redirect;
					}else{
						this.$form.find('.message').html(response.data.msg).removeClass('hidden');
					}

					this.$form.removeClass('loading');
				})
			})
		}

		successCallback(data){
			// Set the token as the value for the token input
			this.token = data.response.token.token;
			// IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
			this.ajaxHandleSubmit();
		}

		errorCallback(data){
			if (data.errorCode === 200) {
				// This error code indicates that the ajax call failed. We recommend that you retry the token request.
			} else {
				alert(data.errorMsg);
			}
			this.$form.removeClass('loading');
		}

		tokenRequest(){
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
				this.successCallback(data)
			}), (data=>{
				this.errorCallback(data)
			}), args);
		}

		pay(){
			// Pull in the public encryption key for our environment
			TCO.loadPubKey(WILOKE_GLOBAL.twocheckoutMode);
			this.$form.on('submit', ((event)=>{
				this.$form.addClass('loading');
				// Call our token request function
				this.tokenRequest();
				// Prevent form from submitting
				return false;
			}));
		}

		showPopup(){
			this.$creditCardPopup.addClass('wil-modal--open');
		}

		closePopup(){
			this.$form.on('click', '.wil-modal__close', ((event)=>{
				this.$creditCardPopup.removeClass('wil-modal--open');
				this.$app.removeClass('loading');
			}));

			this.$creditCardPopup.on('closed', (event=>{
				this.$app.removeClass('loading');
			}))
		}
	}

	$(document).ready(function () {
		new WilokeProceedPaymentWithTwoCheckout();
	});

})(jQuery);