;(function ($) {
	'use strict';
	
	$(document).ready(function () {
		$('#wiloke-delete-rest-of-claims-wrapper').on('click', 'button', function (event) {
			event.preventDefault();
			let $confirmField = $('#confirm_claim');
			if ( $confirmField.val() !== 'delete' ){
				$confirmField.parent().addClass('error');
				return false;
			}

			let $this = $(this);
			$this.addClass('loading');

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'wiloke_delete_the_rest_claims',
					listingID: $('#listing_for_claim').val(),
					claimID: $('#post_ID').val()
				},
				success: (response=>{
					alert(response.data.msg);
					$this.removeClass('loading');
				})
			})
		})	
	});
	
})(jQuery);