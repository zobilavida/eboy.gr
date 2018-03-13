;(function ($) {
	'use strict';

	function subscribe() {
		let $wilokeSubscribe = $('.wiloke-submit-subscribe');

		$wilokeSubscribe.on('click', (event=>{
			event.preventDefault();

			let xhr         = null,
				$self       = $(event.currentTarget),
				currentBtnName = $self.html(),
				$form       = $self.closest('form'),
				$message    = $form.find('.message'),
				email       = $form.find('.wiloke-subscribe-email').val();

			if ( xhr !== null && xhr.status !==  200 ){
				xhr.abort();
			}

			if ( email !== '' )
			{
				$self.val( $self.attr('data-handle') );
				$self.html($self.data('sendingtext') + ' <i class="icon_mail_alt"></i>');
				$.ajax({
					type: 'POST',
					url: WILOKE_GLOBAL.ajaxurl,
					data: { action: 'wiloke_mailchimp_subscribe', security: WILOKE_GLOBAL.wiloke_nonce, email: email },
					success: (response=>{
						if ( response.success ){
							$message.html(response.data).addClass('alert-done').removeClass('alert-error').fadeIn();
							$form.find('.form-remove').remove();
						}else{
							$message.html(response.data).addClass('alert-error').removeClass('alert-done').fadeIn();
						}
						$self.html(currentBtnName);
					})
				});
			}else{
				$message.html('Please enter your e-mail').addClass('alert-error').removeClass('alert-done').fadeIn();
			}
			return false;
		}));
	}
	
	function openTable() {
		let now = (Date.now() - 86400000); //allow today to be selected
		$( "#listgo-open-table-startdate" ).datepicker({
			autopick : true,
			altField: '#listgo-open-table-startdate-input',
			altFormat: "mm/dd/yy",
			inline: true,
			weekStart: 0,
			filter: function ( date ) {
				return date.valueOf() >= now;
			}
		}).trigger('change');
	}
	
	$(document).ready(function () {
		subscribe();
		openTable();
	});

})(jQuery);