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
						console.log($target.find('.add-listing__upload-preview').length);
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