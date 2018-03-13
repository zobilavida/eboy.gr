(function ($) {
	$(document).ready(function () {
		let nonce = $('#wiloke_admin_nonce_field').val();
		let $tableReportWrapper = $('#listgo-table-wrapper');

		$tableReportWrapper.on('click', '.js_ban_ip', (event=>{
			event.preventDefault();
			let $target = $(event.currentTarget);
			let className = $target.attr('class');
			let IPAddress = $target.data('ipaddress');
			if ( className.search('banned') !== -1 ){
				return false;
			}

			$.ajax({
				type: 'POST',
				data: {action: 'wiloke_ban_this_ip', security: nonce, ip: IPAddress},
				url: ajaxurl,
				success: (response=>{
					if ( response.success ){
						$tableReportWrapper.find('.js_ban_ip[data-ipaddress="'+IPAddress+'"]').addClass('banned');
						$('#banned-ips').append('<li><a href="#" data-ipaddress="'+IPAddress+'" class="tag">'+IPAddress+'</a></li>');
						$tableReportWrapper.find('.banned-ips-wrapper').removeClass('hidden');

					}else{
						alert('Something went wrong');
					}
				})
			})
		}));

		$('#banned-ips').on('click', '.tag', function (event) {
			event.preventDefault();
			let $target = $(event.currentTarget);
			let IPAddress = $target.data('ipaddress');

			$.ajax({
				type: 'POST',
				data: {action: 'wiloke_remove_ip_from_black_list', security: nonce, ip: IPAddress},
				url: ajaxurl,
				success: (response=>{
					if ( response.success ){
						$tableReportWrapper.find('.js_ban_ip[data-ipaddress="'+IPAddress+'"]').removeClass('banned');
						$target.parent().remove();
					}else{
						alert('Something went wrong');
					}
				})
			})
		});

		$('#listgo-table').on('click', '.js_delete_report', function (event) {
			event.preventDefault();

			let $target = $(event.currentTarget);
			let postID = $target.data('postid');
			let isDelete = confirm('Do you want to delete this report?');
			if ( isDelete ){
				$.ajax({
					type: 'POST',
					data: {action: 'wiloke_delete_report', security: nonce, postid: postID},
					url: ajaxurl,
					success: (response=>{
						if ( response.success ){
							$target.closest('tr').remove();
						}else{
							alert('Something went wrong');
						}
					})
				})
			}
		});

		$(document).tooltip();
	})
})(jQuery);
