;(function ($) {
	'use strict';
	
	function scrollTo($target) {
		if ( !$target.length ){
			return false;
		}

		$('html, body').animate({
			scrollTop: $target.offset().top - 100
		}, 500);
	}
	
	/*
	 |--------------------------------------------------------------------------
	 | Submit Listing
	 |--------------------------------------------------------------------------
	 | @since 1.0
	 |
	 */
	class WilokeSubmitListing{
		constructor(){
			this.$form = $('#wiloke-form-preview-listing');
			this.$submit = $('#wiloke-listgo-submit-listing');
			this.$editListing = $('#wiloke-listgo-update-listing');
			this.$acfWrapper = this.$form.find('.acf_postbox');
			this.acfData = '';
			this.previewListing();
			this.submitListing();
			this.removeGallery();
			this.mediaUpload();
			this.anonymousUploadFeaturedImg();
			this.anonymousUploadGalleryImg();
			this.toggleBusinessHours();
			this.editListing();
			// this.findOpenTableID();
		}

		findOpenTableID(){
			let $restaurantName = $('#listgo-restaurant-name');
			if ( !$restaurantName.length ){
				return false;
			}

			let aCaching = [], xhr = null;
			$restaurantName.autocomplete({
				length: 2,
				select: function (event, ui) {
					//Set Restaurant ID field when clicked
					$('#listgo-restaurant-name-id').val(ui.item.id);
				},
				source: ((request, response)=> {
					if ( xhr !== null && xhr.status !== 200 ){
						xhr.aborted();
					}

					if ( typeof aCaching[request.term] !== 'undefined' ){
						response(aCaching[request.term]);
						return false;
					}

					xhr = $.ajax({
						type: 'POST',
						url: ajaxurl,
						minLength: 2,
						data: {
							action: 'wiloke_find_open_table_id',
							term: request.term
						},
						success: (result=>{
							if ( !result.success ){
								response([{
									label: result.data.msg,
									value: result.data.msg,
									id: ''
								}]);
							}else{
								if ( typeof result.data.data.restaurants ){
									let aParseResponse = [],
										oResponse = jQuery.parseJSON(result.data.data);
									if (typeof oResponse.restaurants !== 'undefined' && oResponse.restaurants.length > 0) {
										oResponse.restaurants.filter(function (restaurant) {
											aParseResponse.push({
												label  : restaurant.name,
												value  : restaurant.name,
												id     : restaurant.id,
												address: restaurant.address
											});
										});
									}

									aCaching[request.term] = aParseResponse;
									response(aParseResponse);
								}
							}
						})
					});
				})
			})
		}

		toggleBusinessHours(){
			let $bsHours = $('#wiloke-toggle-business-hours'),
				$tblBSH = $('#wiloke-tbl-business-hours');

			$bsHours.on('change', function () {
				if ( $(this).val() === 'enable' ){
					$tblBSH.fadeIn('slow');
				}else{
					$tblBSH.fadeOut('slow');
				}
			}).trigger('change');
		}

		getContent(){
			if ($("#wp-listing_content-wrap").hasClass("tmce-active")){
				return tinyMCE.get('listing_content').getContent();
			}else{
				return $('#listing_content').val();
			}
		}

		anonymousUploadFeaturedImg(){
			let xhr = null;
			let $form = this.$form;
			let $btn = $('#wiloke-listgo-preview-listing');
			$('body').on('processUploadFeaturedImage', function () {
				let $featuredImg = $('[name="wiloke_raw_featured_image"]');

				$featuredImg.wrap('<form id="wiloke-create-form-submission" enctype="multipart/form-data"></form>');
				let temporaryForm = document.getElementById('wiloke-create-form-submission'),
					oTemporaryFormData = new FormData(temporaryForm);

				$form.on('stopping_upload', (event=>{
					if ( xhr !== null ){
						xhr.abort();
						$btn.removeClass('loading');
						$btn.prop('disabled', false);
					}
				}));

				xhr = $.ajax({
					url: WILOKE_GLOBAL.ajaxurl + '?type=single&action=wiloke_submission_insert_media&security='+WILOKE_GLOBAL.wiloke_nonce+'&where=wiloke-upload-feature-image&name=wiloke_raw_featured_image',
					type: 'POST',
					async: true,
					cache: false,
					contentType: false,
					processData: false,
					data: oTemporaryFormData,
					success: (response=>{
						if ( response.success ){
							$('#wiloke_feature_image').val(response.data.message);
						}
						$featuredImg.unwrap();
						$('body').trigger('processUploadGallery');
					})
				})
			});
		}

		anonymousUploadGalleryImg(){
			let $body = $('body');
			let $form = this.$form;
			let xhr = null;
			let $btn = $('#wiloke-listgo-preview-listing');
			$body.on('processUploadGallery', function () {
				$form.on('stopping_upload', (event=>{
					if ( xhr !== null ){
						xhr.abort();
						$btn.removeClass('loading');
						$btn.prop('disabled', false);
					}
				}));

				let $galleryImage = $('#wiloke-upload-gallery-image');

				if ( !$galleryImage.length ){
					$body.data('Wiloke/Submission/UploadedGallery', true);
					$body.trigger('galleryHasbeenUploaded');
				}else{
					$body.data('Wiloke/Submission/UploadedGallery', true);
					$body.trigger('galleryHasbeenUploaded');
					return false;
				}

				$galleryImage.wrap('<form id="wiloke-create-form-submission" enctype="multipart/form-data"></form>');

				let temporaryForm = document.getElementById('wiloke-create-form-submission');
				let	oTemporaryFormData = new FormData(temporaryForm);

				xhr = $.ajax({
					url: WILOKE_GLOBAL.ajaxurl + '?type=multiple&action=wiloke_submission_insert_media&security='+WILOKE_GLOBAL.wiloke_nonce+'&where=wiloke-upload-gallery-image&name=wiloke_raw_gallery_image',
					type: 'POST',
					async: true,
					cache: false,
					contentType: false,
					processData: false,
					data: oTemporaryFormData,
					success: (response=>{
						if ( response.success ){
							$('#wiloke_submission_listing_gallery').val(response.data.message);
						}
						$galleryImage.unwrap();
						$body.data('Wiloke/Submission/UploadedGallery', true);
						$body.trigger('galleryHasbeenUploaded');
					})
				})
			});
		}

		mediaUpload(){
			if ( !_.isUndefined(this.$form.data('isuserloggedin')) && this.$form.data('isuserloggedin') ){
				this.$form.find('.wiloke-add-featured-image').each(function () {
					new wilokeMediaUpload($(this));
				});
			}else{
				let $form = this.$form;
				this.$form.find('.wiloke-simple-upload').each(function () {
					new WilokeSimpleUpload($(this), $form);
				});
			}
		}

		validate(){
			let $title = $('#listing_title');
			if ( $title.val() === '' ){
				this.$error = $title;
				this.scrollTop();
				return false;
			}

			if ( this.getContent() === '' ){
				this.$error = $('#wp-listing_content-wrap');
				this.scrollTop();
				return false;
			}

			let $listingLocation = $('#listing_location');
			if ( $listingLocation.hasClass('add_listing_location_by_default') && $listingLocation.val() === '' ){
				this.$error = $listingLocation;
				this.scrollTop();
				return false;
			}

			let $listingAddress = $('#wiloke-location');
			if ( $listingAddress.val() === '' ){
				this.$error = $listingAddress;
				this.scrollTop();
				return false;
			}

			let $listingLatLong = $('#wiloke-latlong');
			if ( $listingLatLong.val() === '' ){
				this.$error = $listingLatLong;
				this.scrollTop();
				return false;
			}

			let $listingCats = $('#listing_cats');
			if ( $listingCats.val() === '' ){
				this.$error = $listingCats;
				this.scrollTop();
				return false;
			}

			if ( $('#createaccount').is(':checked') ){
				let $regEmail = $('#wiloke-reg-email');
				if ( $regEmail.val() === '' ){
					this.$error = $regEmail;
					this.scrollTop();
					return false;
				}
			}else{
				let $userName = $('#wiloke-user-login'),
					$password = $('#wiloke-my-password');
				if ( $userName.val() === '' ){
					this.$error = $userName;
					this.scrollTop();
					return false;
				}

				if ( $password.val() === '' ){
					this.$error = $password;
					this.scrollTop();
					return false;
				}
			}

			return true;
		}

		validateACF(){
			if ( this.$acfWrapper.length ){
				let fieldType = '', self = this;
				this.$acfWrapper.find('.field').each(function () {
					let $this = $(this);

					if ( $this.hasClass('required') ){
						fieldType = $this.data('field_type');
						if ( $this.find('.'+fieldType).val() === '' ){
							$this.addClass('validate-required');
							self.$error = $this;
							self.scrollTop();
							return false;
						}
					}
				})
			}
			this.getACFCustomFieldData();
			return true;
		}

		getACFCustomFieldData(){
			if ( this.$acfWrapper.length ){
				let fieldType = '', fieldName = '', oVal = {}, fieldKey = '';
				this.$acfWrapper.find('.field').each(function () {
					let $this = $(this);
					fieldType = $this.data('field_type');
					fieldKey  = $this.data('field_key');
					fieldName = $this.data('field_name');

					if ( fieldType === 'wysiwyg' ){
						let editorID = $this.find('.wp-editor-area').attr('id');
						if ($("#wp-acf_settings-wrap").hasClass("tmce-active")){
							oVal[fieldKey] = tinyMCE.get(editorID).getContent();
						}else{
							oVal[fieldKey] = $('#'+editorID).val();
						}
					}else if(fieldType === 'checkbox'){
						$this.find('[type="checkbox"]:checked').each(function () {
							if ( typeof oVal[fieldKey] === 'undefined' ){
								oVal[fieldKey] = [];
							}
							oVal[fieldKey].push($(this).val());
						})
					}else{
						oVal[fieldKey] = $this.find('[name="fields['+fieldKey+']"]').val();
					}
				});

				this.acfData = JSON.stringify(oVal);
			}
		}

		scrollTop(){
			if ( !this.$error.length ){
				return false;
			}

			this.addErrorClass();

			$('html, body').animate({
				scrollTop: this.$error.offset().top - 40
			}, 300);
		}

		addErrorClass(){
			this.$error.closest('.form-item').addClass('validate-required');
		}

		removeGallery(){
			this.$form.on('click', '.wil-addlisting-gallery__list-remove', function () {
				if ( WILOKE_GLOBAL.isLoggedIn === 'no' ){
					$(this).closest('#wiloke-show-gallery').empty();
				}else{
					$(this).closest('.gallery-item').remove();
				}
			});
		}

		requestGallery(){
			let $galleryID = this.$form.find('#wiloke-preview-gallery'),
				galleryIDs = [];

			if ( $galleryID.length ){
				$galleryID.find('.gallery-item').each(function () {
					if ( typeof $(this).data('id') !=='undefined' ){
						galleryIDs.push($(this).data('id'));
					}
				});

				let convertGalleryToString = galleryIDs.join(',');
				this.$form.find('#listing_gallery').val(convertGalleryToString);
			}
		}

		previewListing(){
			if ( this.$form.length ){
				let $body = $('body');
				this.$error = null;
				this.$preview = $('#wiloke-listgo-preview-listing');
				let xhr = null;
				this.$printMessage = this.$form.find('.wiloke-print-msg-here');

				$body.on('galleryHasbeenUploaded', (()=>{
					this.$form.trigger('submit');
				}));

				this.$form.on('submit', ((event)=>{
					event.preventDefault();
					let isValidated = this.validate();
					this.$printMessage.html('');
					if ( !isValidated ){
						return false;
					}

					isValidated = this.validateACF();

					if ( !isValidated ){
						return false;
					}

					let $target = $(event.target);

					this.$preview.prop('disabled', true);
					this.$preview.addClass('loading');

					if ( xhr !== null && xhr.status !== 200 ){
						this.$preview.removeClass('loading');
						this.$preview.prop('disabled', false);
						xhr.abort();
					}

					this.$form.on('stopping_upload', (event=>{
						if ( xhr !== null ){
							this.$preview.removeClass('loading');
							this.$preview.prop('disabled', false);
							xhr.abort();
						}
					}));

					this.$preview.addClass('loading');

					if ( (WILOKE_GLOBAL.isLoggedIn === 'no') && !$body.data('Wiloke/Submission/UploadedGallery') ){
						$body.trigger('processUploadFeaturedImage');
						return false;
					}

					let content = this.getContent();

					this.requestGallery();

					xhr = $.ajax({
						type: 'POST',
						url: WILOKE_GLOBAL.ajaxurl + '?action=wiloke_preview_listing',
						data: {security: WILOKE_GLOBAL.wiloke_nonce, data: $target.serialize(), content: content, acfData: this.acfData},
						success: (response=>{
							if ( response.success ){
								window.location.href = response.data.next;
							}else{
								let messages = '';
								_.forEach(response.data, ((message, id)=>{
									let $target = $('#'+id);
									if ($target.length ){
										$target.html(message).removeClass('hidden');
										$target.parent().removeClass('hidden').addClass('validate-required');
									}else{
										messages += '<p class="update-status error-msg">'+message+'</p>';
									}
								}));

								if ( messages !== '' ){
									this.$printMessage.html(messages).removeClass('hidden');
								}
							}

							this.$preview.prop('disabled', false);
							this.$preview.removeClass('loading');
						})
					})
				}));
			}
		}

		submitListing(){
			if ( this.$submit.length ){
				this.$submit.removeClass('not-active');
				this.$submit.on('click', (event=>{
					event.preventDefault();
					let $target = $(event.currentTarget);

					if ( $target.data('processing') ){
						return false;
					}

					$target.addClass('loading');
					$target.data('processing', true);
					this.requestGallery();

					$.ajax({
						type: 'POST',
						global: false,
						url: WILOKE_GLOBAL.ajaxurl,
						data: {action: 'wiloke_submit_listing', 'security': WILOKE_GLOBAL.wiloke_nonce, post_id: $target.data('postid'), package_id: $('#package_id').val()},
						success: (response=>{
							if ( response.success ){
								window.location.href = decodeURIComponent(response.data.redirect);
							}else{
								alert(response.data.message);
							}

							$target.removeClass('loading');
							$target.data('processing', false);
						})
					})
				}))
			}
		}

		ajaxUpdateListing(){
			let isValidated = this.validate();
			this.$printMessage.html('');
			if ( !isValidated ){
				return false;
			}

			isValidated = this.validateACF();

			if ( !isValidated ){
				return false;
			}


			if ( this.xhrUpdateListing !== null && this.xhrUpdateListing.status !== 200 ){
				this.xhrUpdateListing.abort();
			}

			this.$form.addClass('loading');

			this.xhrUpdateListing = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {
					action: 'wiloke_edit_published_listing',
					security: WILOKE_GLOBAL.wiloke_nonce,
					listingID: this.$form.find('[name="listing_id"]').val(),
					data: this.$form.serialize(),
					content: this.getContent()
				},
				success: (response=>{
					if ( response.success ){
						window.location.href = decodeURIComponent(response.data.redirect);
					}else{
						alert(response.data.msg);
					}
					this.$form.removeClass('loading');
				})
			})
		}

		confirmEditListing($popup){
			$('#listgo-cancel-edit-listing').on('click', (event=>{
				event.preventDefault();
				$popup.removeClass('wil-modal--open');
			}));

			$('#listgo-continue-editing-listing').on('click', (event=>{
				this.ajaxUpdateListing();
				event.preventDefault();
				$popup.removeClass('wil-modal--open');
			}));
		}

		editListing(){
			if ( this.$editListing.length ){
				this.xhrUpdateListing = null;
				let $popup = $('#wiloke-form-update-listing-wrapper');
				this.$editListing.on('click', (event=>{
					event.preventDefault();
					if ( this.$editListing.data('edittype') === 'allow_need_review' ){
						$popup.addClass('wil-modal--open');
						this.confirmEditListing($popup);
					}else{
						this.ajaxUpdateListing();
					}
				}))
			}
		}
	}

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

	class WilokeSimpleUpload{
		constructor($target, $form){
			this.$target = $target;
			this.$form = $form;
			this.$wrapper = this.$target.closest('.upload-file');
			this.maxfilesize = $form.data('uploadfilesize');
			this.xhr = null;
			this.uploadInputChanged();
			this.removeSingleUpload();
			this.fileStore = [];
		}

		uploadInputChanged(){
			let self = this;
			this.$remider = this.$target.closest('.input-upload-file').siblings('.wiloke-submission-reminder');
			this.maxfilesize = parseInt(this.maxfilesize.replace('M', ''), 10)*1000*1000;
			this.$target.on('change', function(){
				self.aListFiles = this.files;
				self.showImageReview();
			});
		}

		removeSingleUpload(){
			this.$form.on('click', '.wil-addlisting-gallery__list-remove', (event=>{
				if ( $(event.currentTarget).closest('.wil-addlisting-gallery').hasClass('single-upload') ){
					$('#wiloke-show-featured-image').find('.wil-addlisting-gallery__list').empty();
				}else{
					$(event.currentTarget).parent().remove();
				}
			}));
		}

		showImageReview(){
			if ( this.aListFiles.length ){
				let $previewImgs= this.$target.hasClass('wiloke_feature_image') ? $('#wiloke-show-featured-image').find('.wil-addlisting-gallery__list') : $('#wiloke-show-gallery'),
					isMultiple  = !_.isUndefined(this.$target.data('ismultiple')),
					$remider    = this.$remider,
					maxFileSize = this.maxfilesize;

				_.each(this.aListFiles, (file=>{
					if ( /\.(jpe?g|png|gif)$/i.test(file.name) ) {

						let reader = new FileReader();
						reader.addEventListener('load', function () {
							let errorClass = file.size <= maxFileSize ? '' : ' error';
							let img = '<li class="bg-scroll'+errorClass+'" style="background-image: url('+this.result+')"><span class="wil-addlisting-gallery__list-remove">Remove</span></li>';

							if ( isMultiple ){
								$previewImgs.append(img);
							}else{
								$previewImgs.html(img);
							}
							$remider.removeClass('review_status error-msg');

							$('body').trigger('processUpload');

						}, false);

						reader.readAsDataURL(file);
					}
				}));
			}
		}
	}

	$(document).ready(function () {
		let $listingLocation = $('#listing_location');
		if ( $listingLocation.hasClass('add_listing_location_by_default') ){
			$listingLocation.select2({
				placeolder: $listingLocation.data('placeholder')
			}).addClass('created');
		}

		let $listingCats = $('#listing_cats');
		$listingCats.select2({
			placeholder: $listingCats.data('placeholder')
		}).addClass('created');
	});

	$(window).load(function () {
		new WilokeSubmitListing();

		let $toggle = $('.mce-widget[aria-label="Toolbar Toggle"]'),
			toolbarStatus = $toggle.attr('aria-pressed');

		if ( toolbarStatus === 'false' || !toolbarStatus || (typeof toolbarStatus === 'undefined') ){
			$toggle.trigger('click');
		}
	})
})(jQuery);