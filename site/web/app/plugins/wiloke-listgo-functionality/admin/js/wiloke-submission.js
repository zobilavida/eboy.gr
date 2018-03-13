;(function ($) {
	'use strict';
	
	function generalSettings() {
		$('.wiloke_datepicker').datepicker();
		$(document).tooltip();

		let $formUI = $('.form.ui');
		if ( $formUI.length ){
			$formUI.find('.dropdown').each(function() {
				$(this).dropdown({
					forceSelection: false
				});
			});
		}
	}

	function datePicker() {
		$('#filter-by-date').on('change', function () {
			let $period = $('#filter-by-period'),
				val = $(this).val();
			if ( val === 'period' ){
				$period.removeClass('hidden');
			}else{
				$period.addClass('hidden');
			}
		}).trigger('change');
	}

	function deletePayment() {
		let xhrDeleteOrder = false;
		$('.js_delete_payment').on('click', function (event) {
			event.preventDefault();
			let $target      = $(event.currentTarget),
				wantToDelete = confirm('Do you want to delete this order? Please note that all listings belong to this order will also be moved in trash.');

			if ( wantToDelete ){
				let paymentID = $target.data('paymentid');

				if ( xhrDeleteOrder && xhrDeleteOrder.status !== 200 ){
					xhrDeleteOrder.abort();
				}

				xhrDeleteOrder = $.ajax({
					type: 'POST',
					data: {action: 'wiloke_submission_delete_order', payment_ID: paymentID},
					url: ajaxurl,
					success: (response=>{
						if ( response.success ){
							$target.closest('tr.item').remove();
						}else{
							alert('Something went error');
						}
					})
				})
			}
		});
	}
	
	function scrollTop($scrollTo) {
		$('html, body').animate({
			scrollTop: $scrollTo.offset().top - 100
		}, 600);
	}

	function addNewOrder() {
		let $app = $('#wiloke-submission-add-new-order');
		if ( $app.length ){
			let $msg = $app.find('#wiloke-submission-message-after-addnew');
			$app.on('submit', function (event) {
				event.preventDefault();
				let $userID         = $('#add_new_order_user_ID'),
				    $packageID      = $('#add_new_order_packageid'),
				    $packageType    = $('#add_new_order_package_type'),
				    $eventPlanID    = $('#add_new_order_add_event_planid'),
				    $orderStatus    = $('#add_new_order_status'),
					userId          = $userID.val(),
					packageType     = $packageType.val(),
					packageID       = '',
					orderStatus     = $orderStatus.val();

				if ( $userID.val() === '' ){
					$userID.closest('.field').addClass('error');
					return false;
				}


				if ( packageType === 'pricing' ){
					packageID = $packageID.val();
					if ( packageID === '' ){
						$packageID.closest('.field').addClass('error');
						return false;
					}
				}else{
					packageID = $eventPlanID.val();
					if ( packageID === '' ){
						$eventPlanID.closest('.field').addClass('error');
						return false;
					}
				}

				if ( orderStatus  === '' ){
					$orderStatus.closest('.field').addClass('error');
					return false;
				}

				$msg.addClass('hidden');
				$app.addClass('loading');

				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {action: 'wiloke_submission_add_new_order', package_type: packageType, user_ID: userId, package_ID: packageID, order_status: orderStatus},
					success: function (response) {
						$app.removeClass('loading');
						$msg.css('display', 'block');
						if ( response.success ){
							$msg.removeClass('hidden error').addClass('success').html(response.data.message);
							setTimeout((()=>{
								window.location.href = response.data.redirect;
							}), 700);
						}else{
							_.forEach(response.data, (msg, key)=>{
								let $field = $('#'+key);
								if ( $field.length && key !== 'message' ){
									$field.closest('.field').addClass('error');
								}else{
									$msg.removeClass('hidden success').addClass('error').html(msg);
								}
							})
						}
					}
				})
			});
		}
	}
	
	function changeCustomerOrder() {
		let $app = $('#wiloke-submission-change-customer-order');
		let $msg = $('#wiloke-submission-message-after-update');

		if ( $app.length ){
			$app.on('submit', function(event) {
				event.preventDefault();

				let currentOrderStatus  = $app.find('input[name="wiloke_submission_order[current_order_status]"]').val(),
					newOrderStatus      = $app.find('select[name="wiloke_submission_order[new_order_status]"]').find('option:selected').val(),
					currentPackage      = $app.find('input[name="wiloke_submission_order[current_package_name]"]').val(),
					newPackage          = $app.find('input[name="wiloke_submission_order[new_package_name]"]').val(),
					paymentID           = $app.find('input[name="wiloke_submission_order[payment_id]"]').val(),
					customerID          = $app.find('input[name="wiloke_submission_order[customer_id]"]').val();

				if ( (currentOrderStatus === newOrderStatus) && (currentPackage === newPackage) ){
					scrollTop($msg);
					return false;
				}

				$msg.addClass('hidden');
				$app.addClass('loading');

				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {action: 'wiloke_submission_update_customer_order', customer_ID: customerID, payment_ID: paymentID, new_order_status: newOrderStatus, current_order_status: currentPackage, current_package: currentPackage, new_package: newPackage},
					success: function (response) {
						$app.removeClass('loading');
						$msg.css('display', 'block');
						if ( response.success ){
							$app.find('input[name="wiloke_submission_order[current_order_status]"]').val(newOrderStatus);
							$app.find('input[name="wiloke_submission_order[current_package_name]"]').val(newPackage);
							$msg.removeClass('hidden error').addClass('success').html(response.data.message);
							scrollTop($msg);
							setTimeout(function () {
								location.reload();
							}, 400);
						}else{
							_.forEach(response.data, (msg, key)=>{
								let $field = $('#'+key);
								if ( $field.length ){
									$field.closest('.field').addClass('error');
									scrollTop($field);
								}else{
									$msg.removeClass('hidden success').addClass('error').html(msg);
									scrollTop($msg);
								}
							})
						}

					}
				})
			});
		}
	}

	class WilokeInstallSubmissionPage{
		constructor(){
			this.$app = $('#ws-install-pages');
			this.runApp();
		}

		runApp(){
			if ( this.$app.length ){
				let xhr = null;
				let $msg = this.$app.find('.message');
				this.$app.on('submit', ((event)=>{
					event.preventDefault();

					if ( xhr !== null && xhr.status !== 200 ){
						return false;
					}

					this.$app.addClass('loading');

					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {action: 'wiloke_submission_install_pages'},
						success: (response=>{
							if ( response.success ){
								$msg.html(response.data.message).removeClass('error info').addClass('success');
								this.$app.addClass('success');
							}else{
								$msg.html(response.data.message).removeClass('success info').addClass('error');
								this.$app.addClass('error');
							}

							this.$app.removeClass('loading');
						})
					})
				}));
			}
		}
	}
	
	$(document).ready(function () {
		generalSettings();
		datePicker();
		deletePayment();
		changeCustomerOrder();
		addNewOrder();
		new WilokeInstallSubmissionPage();
	});
	
})(jQuery);