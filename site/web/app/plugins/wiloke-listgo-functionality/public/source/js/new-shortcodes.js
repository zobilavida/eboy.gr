(function($) {
	let WilokeShortcodes = {
		Models: {},
		Collections: {},
		Views: {}
	};

	let WilokeHelps = {
		encode: function (str) {
			return window.btoa(encodeURIComponent(str));
		},
		decode: function (str) {
			return decodeURIComponent(window.atob(str));
		}
	}

	let $closeBtn = $('.addlisting-popup__close');
	let WilokeSCTab = {
		config: {
			$wrapper: $('#wiloke-menu-price-settings')
		},

		init: function (options) {
			$.extend(WilokeSCTab.config, options);

			this.$wrapper = this.config.$wrapper;
			this.$navWrapper = this.$wrapper.find('.addlisting-popup__nav');
			this.$popupPanel = this.$wrapper.find('.addlisting-popup__panel');

			this.handleClick();
			this.addNewTab();
			this.firstTime();
		},

		firstTime: function () {
			this.$navWrapper.find('a').removeClass('active');
			this.$popupPanel.find('.addlisting-popup__group').addClass('hidden');
			this.$navWrapper.find('a:first').trigger('click');
		},

		handleClick: function () {
			let self = this;

			this.$wrapper.find('.addlisting-popup__nav').on('click', 'a', function (event) {
				event.preventDefault();
				self.$navWrapper.find('a').removeClass('active');
				$(this).addClass('active');
				self.$popupPanel.find('.addlisting-popup__group').addClass('hidden');
				$($(this).attr('href')).removeClass('hidden');
			});
		},

		addNewTab: function () {
			let self = this;
			this.$wrapper.on('addedNew', function () {
				self.$wrapper.find('.addlisting-popup__nav').find('a:last').trigger('click');
			});
		}
	}

	WilokeShortcodes.emulateSC = {
		priceTable: '<div id="{{idhere}}" data-title="{{titlehere}}" class="addlisting-placeholder addlisting-placeholder-prices" data-settings="{{valuehere}}" draggable="true" contenteditable="false"><div class="addlisting-placeholder__icon" style="background-image: url('+ WILOKE_LISTGO_FUNCTIONALITY.url + 'public/source/img/icon-price.png' +')"></div><div class="addlisting-placeholder__title">{{titlehere}}</div><div class="addlisting-placeholder__actions"><span data-id="{{idhere}}" class="addlisting-placeholder__action-edit wiloke-edit-menu-prices">'+WILOKE_LISTGO_SC_TRANSLATION.edit+'</span><span class="addlisting-placeholder__action-remove">'+WILOKE_LISTGO_SC_TRANSLATION.remove+'</span></div></div>',
		accordion: '<div id="{{idhere}}" data-title="{{titlehere}}" class="addlisting-placeholder addlisting-placeholder-accordion" data-settings="{{valuehere}}" draggable="true" contenteditable="false"><div class="addlisting-placeholder__icon" style="background-image: url('+ WILOKE_LISTGO_FUNCTIONALITY.url + 'public/source/img/icon-accordion.png' +')"></div><div class="addlisting-placeholder__title">{{titlehere}}</div><div class="addlisting-placeholder__actions"><span class="addlisting-placeholder__action-edit wiloke-edit-accordion" data-id="{{idhere}}">'+WILOKE_LISTGO_SC_TRANSLATION.edit+'</span><span class="addlisting-placeholder__action-remove">'+WILOKE_LISTGO_SC_TRANSLATION.remove+'</span></div></div>',
		listFeatures: '<div id="{{idhere}}" data-title="{{titlehere}}" class="addlisting-placeholder addlisting-placeholder-list-features" data-settings="{{valuehere}}" draggable="true" contenteditable="false"><div class="addlisting-placeholder__icon" style="background-image: url('+ WILOKE_LISTGO_FUNCTIONALITY.url + 'public/source/img/icon-list.png' +')"></div><div class="addlisting-placeholder__title">{{titlehere}}</div><div class="addlisting-placeholder__actions"><span class="addlisting-placeholder__action-edit wiloke-edit-list-features" data-id="{{idhere}}">'+WILOKE_LISTGO_SC_TRANSLATION.edit+'</span><span class="addlisting-placeholder__action-remove">'+WILOKE_LISTGO_SC_TRANSLATION.remove+'</span></div></div>',
	};

	/**
	 * Price Table
	 */
	WilokeShortcodes.Models.priceTable = Backbone.Model.extend({
		defaults: {
			'name': 'Name',
			'price': '5$',
			'description': 'Write something describes this food'
		},
	});

	WilokeShortcodes.Collections.priceTable = Backbone.Collection.extend({
		model: WilokeShortcodes.Models.priceTable,
		initialize: function () {
			this.on('add', function (model) {
				// console.log('something got changed')
			});

			this.on('change', function () {
				// console.log('Something changed');
			})
		}
	});

	WilokeShortcodes.Views.priceTable = Backbone.View.extend({
		el: '#wiloke-menu-price-settings',
		events: {
			'click .addlisting-popup__plus': 'addOne',
			'click .addlisting-popup__nav-remove': 'removeModel'
		},
		tagName     : 'div',

		loadIntro: function () {
			let $target = this.$el.find('.wiloke-sc-intro');
			if ( $target.hasClass('loaded') ){
				$target.attr('src', $target.data('src'));
			}
		},

		removeModel: function (event) {
			event.preventDefault();
			if ( this.collection.length > 1 ){
				this.collection.remove($(event.target).data('id'));
				WilokeSCTab.init({
					$wrapper: this.$el
				});
			}else{
				alert('You need at least one item');
			}
		},

		// template: _.template($('#wiloke-price-item').html()),
		initialize: function () {
			this.listenTo(this.collection, 'add', this.render);
			this.listenTo(this.collection, 'remove', this.render);
			this.loadIntro();
		},

		addOne: function (event) {
			event.preventDefault();
			let model = new WilokeShortcodes.Models.priceTable();
			this.collection.add(model);

			let itemView = new WilokeShortcodes.Views.priceItem({
				model: model
			});
			this.$el.find('.addlisting-popup__panel').append(itemView.render().el);


			let tabItem = new WilokeShortcodes.Views.priceTab({
				model: model
			});

			$(event.target).before(tabItem.render().el);
			this.$el.find('.addlisting-popup__panel').append(itemView.render().el);

			// referring to tab to know more
			this.$el.trigger('addedNew');
		},

		render: function () {
			// Add Tabs
			let order = 0;

			this.$el.find('.addlisting-popup__panel').empty();
			this.$el.find('.addlisting-popup__nav').empty();
			this.collection.each(function (model) {
				let tabItem = new WilokeShortcodes.Views.priceTab({
					model: model
				});
				this.$el.find('.addlisting-popup__nav').append(tabItem.render(order).el);
				order++;
			}, this);

			// Add Plus button
			this.$el.find('.addlisting-popup__nav').append('<span class="addlisting-popup__plus">+</span>');

			// Add Contents
			this.collection.each(function (model) {
				let itemView = new WilokeShortcodes.Views.priceItem({
					model: model
				});
				this.$el.find('.addlisting-popup__panel').append(itemView.render().el);
			}, this);
		}
	});

	WilokeShortcodes.Views.priceItem = Backbone.View.extend({
		tagName     : 'div',
		className   : 'addlisting-popup__group',
		events: {
			'keypress input': 'changedValue',
			'paste input': 'changedValue',
			'cut input': 'changedValue',
			'keypress textarea': 'changedValue',
			'cut textarea': 'changedValue',
			'paste textarea': 'changedValue'
		},
		initialize: function () {
			this.handling = null;
		},
		template: _.template($('#wiloke-price-item').html()),
		changedValue: function (event) {
			if ( this.handling !== null ){
				clearTimeout(this.handling);
			}

			let self = this;

			let $target = $(event.target);

			this.handling = setTimeout(function () {
				self.model.set($target.attr('name'), $target.val());
				clearTimeout(self.handling);
			}, 500);
		},
		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			this.$el.attr('id', this.model.cid);
			return this;
		}
	});

	WilokeShortcodes.Views.priceTab = Backbone.View.extend({
		tagName     : 'a',
		className: '',
		template: _.template('<span class="title"><%= name %></span><span class="addlisting-popup__nav-remove" data-id="<%= id %>"></span>'),
		initialize: function () {
			this.activateClass = false;
			this.listenTo(this.model, 'change:name', this.updateName);
		},
		updateName: function () {
			this.$el.find('.title').html(this.model.get('name'));
		},
		render: function (order) {
			this.className = order === 0 ? 'active' : '';
			let oSettings = this.model.toJSON();
			oSettings.id = this.model.cid;
			this.$el.html(this.template(oSettings));
			this.$el.attr('href', '#'+this.model.cid);
			return this;
		}
	});


	/**
	 * Title Model
	 */
	WilokeShortcodes.Models.priceTitle = Backbone.Model.extend({
		defaults: {
			title: 'Menu Price'
		}
	});

	WilokeShortcodes.Views.priceTitle = Backbone.View.extend({
		el: '#price-title-wrapper',
		tag: 'div',
		template: _.template($('#price-title-tpl').html()),
		events: {
			'change #price-title': 'changedTitle',
			'cut #price-title': 'changedTitle',
			'paste #price-title': 'changedTitle',
			'keypress #price-title': 'changedTitle'
		},
		initialize: function () {
			this.handling = null;
			this.render();
			// this.listenTo(this.model, 'change:title', this.render);
		},
		changedTitle: function (event) {
			if ( this.handling !== null ){
				clearTimeout(this.handling);
			}

			let self = this;
			this.handling = setTimeout(function () {
				self.model.set('title', $(event.currentTarget).val());
				self.updateEmulateTitle();
				clearTimeout(self.handling);
			}, 400);
		},
		updateEmulateTitle: function () {
			$('#show-price-title').html(this.model.get('title'));
		},
		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			this.updateEmulateTitle();
			return this;
		}
	});

	/**
	 * Accordions
	 * 1.1.8
	 */
	/**
	 * Title Model
	 */
	WilokeShortcodes.Models.accordionTitle = Backbone.Model.extend({
		defaults: {
			title: ''
		}
	});

	WilokeShortcodes.Views.accordionTitle = Backbone.View.extend({
		el: '#accordion-title-wrapper',
		tag: 'div',
		template: _.template($('#accordion-title-tpl').html()),
		events: {
			'change #accordion-title': 'changedTitle',
			'cut #accordion-title': 'changedTitle',
			'paste #accordion-title': 'changedTitle',
			'keypress #accordion-title': 'changedTitle'
		},
		initialize: function () {
			this.handling = null;
			this.render();
			// this.listenTo(this.model, 'change:title', this.render);
		},
		changedTitle: function (event) {
			if ( this.handling !== null ){
				clearTimeout(this.handling);
			}

			let self = this;
			this.handling = setTimeout(function () {
				self.model.set('title', $(event.currentTarget).val());
				self.updateEmulateTitle();
				clearTimeout(self.handling);
			}, 400);
		},
		updateEmulateTitle: function () {
			$('#show-accordion-title').html(this.model.get('title'));
		},
		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			this.updateEmulateTitle();
			return this;
		}
	});

	WilokeShortcodes.Models.accordion = Backbone.Model.extend({
		defaults: {
			'title': 'Einstein',
			'description': 'Life is like riding a bicycle. To keep your balance you must keep moving.'
		},
	});

	WilokeShortcodes.Collections.accordion = Backbone.Collection.extend({
		model: WilokeShortcodes.Models.accordion,
		initialize: function () {
			this.on('add', function (model) {
				// console.log('something got changed')
			});

			this.on('change', function () {
				// console.log('Something changed');
			})
		}
	});

	WilokeShortcodes.Views.accordion = Backbone.View.extend({
		el: '#wiloke-accordion-settings',
		events: {
			'click .addlisting-popup__plus': 'addOne',
			'click .addlisting-popup__nav-remove': 'removeModel'
		},
		tagName     : 'div',
		loadIntro: function () {
			let $target = this.$el.find('.wiloke-sc-intro');
			if ( $target.hasClass('loaded') ){
				$target.attr('src', $target.data('src'));
			}
		},
		removeModel: function (event) {
			event.preventDefault();
			if ( this.collection.length > 1 ){
				this.collection.remove($(event.target).data('id'));
				WilokeSCTab.init({
					$wrapper: $(this.el)
				});
			}else{
				alert('You need at least one item');
			}
		},

		// template: _.template($('#wiloke-price-item').html()),
		initialize: function () {
			this.listenTo(this.collection, 'add', this.render);
			this.listenTo(this.collection, 'remove', this.render);
			this.loadIntro();
		},

		addOne: function (event) {
			event.preventDefault();
			let model = new WilokeShortcodes.Models.accordion();
			this.collection.add(model);

			let itemView = new WilokeShortcodes.Views.accordionItem({
				model: model
			});
			this.$el.find('.addlisting-popup__panel').append(itemView.render().el);


			let tabItem = new WilokeShortcodes.Views.accordionTab({
				model: model
			});

			$(event.target).before(tabItem.render().el);
			this.$el.find('.addlisting-popup__panel').append(itemView.render().el);

			// referring to tab to know more
			this.$el.trigger('addedNew');
		},

		render: function () {
			// Add Tabs
			let order = 0;

			this.$el.find('.addlisting-popup__panel').empty();
			this.$el.find('.addlisting-popup__nav').empty();
			this.collection.each(function (model) {
				let tabItem = new WilokeShortcodes.Views.accordionTab({
					model: model
				});
				this.$el.find('.addlisting-popup__nav').append(tabItem.render(order).el);
				order++;
			}, this);

			// Add Plus button
			this.$el.find('.addlisting-popup__nav').append('<span class="addlisting-popup__plus">+</span>');

			// Add Contents
			this.collection.each(function (model) {
				let itemView = new WilokeShortcodes.Views.accordionItem({
					model: model
				});
				this.$el.find('.addlisting-popup__panel').append(itemView.render().el);
			}, this);
		}
	});

	WilokeShortcodes.Views.accordionItem = Backbone.View.extend({
		tagName     : 'div',
		className   : 'addlisting-popup__group',
		events: {
			'keypress input': 'changedValue',
			'cut input': 'changedValue',
			'paste input': 'changedValue',
			'keypress textarea': 'changedValue',
			'cut textarea': 'changedValue',
			'paste textarea': 'changedValue'
		},
		initialize: function () {
			this.handling = null;
		},
		template: _.template($('#wiloke-accordion-item').html()),
		changedValue: function (event) {
			if ( this.handling !== null ){
				clearTimeout(this.handling);
			}

			let self = this;

			let $target = $(event.target);

			this.handling = setTimeout(function () {
				self.model.set($target.attr('name'), $target.val());
				clearTimeout(self.handling);
			}, 500);
		},
		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			this.$el.attr('id', this.model.cid);
			return this;
		}
	});

	WilokeShortcodes.Views.accordionTab = Backbone.View.extend({
		tagName     : 'a',
		className: '',
		template: _.template('<span class="title"><%= title %></span><span class="addlisting-popup__nav-remove" data-id="<%= id %>"></span>'),
		initialize: function () {
			this.activateClass = false;
			this.listenTo(this.model, 'change:title', this.updateName);
		},
		updateName: function () {
			this.$el.find('.title').html(this.model.get('title'));
			return this;
		},
		render: function (order) {
			this.className = order === 0 ? 'active' : '';
			let oSettings = this.model.toJSON();
			oSettings.id = this.model.cid;

			this.$el.html(this.template(oSettings));
			this.$el.attr('href', '#'+this.model.cid);
			return this;
		}
	});

	/**
	 * List Features
	 * @since 1.1.8
	 */
	WilokeShortcodes.Models.listFeatures = Backbone.Model.extend({
		defaults: {
			'name': 'Wifi',
			'unavailable': ''
		},
	});

	WilokeShortcodes.Collections.listFeatures = Backbone.Collection.extend({
		model: WilokeShortcodes.Models.listFeatures,
		initialize: function () {
			this.on('add', function (model) {
				// console.log('something got changed')
			});

			this.on('change', function () {
				// console.log('Something changed');
			})
		}
	});

	WilokeShortcodes.Views.listFeatures = Backbone.View.extend({
		el: '#wiloke-list-features-settings',
		events: {
			'click .addlisting-popup__plus': 'addOne',
			'click .addlisting-popup__nav-remove': 'removeModel'
		},
		tagName     : 'div',
		loadIntro: function () {
			let $target = this.$el.find('.wiloke-sc-intro');
			if ( $target.hasClass('loaded') ){
				$target.attr('src', $target.data('src'));
			}
		},
		removeModel: function (event) {
			event.preventDefault();
			if ( this.collection.length > 1 ){
				this.collection.remove($(event.target).data('id'));
				WilokeSCTab.init({
					$wrapper: this.$wrapper
				});
			}else{
				alert('You need at least one item');
			}
		},

		// template: _.template($('#wiloke-price-item').html()),
		initialize: function () {
			this.listenTo(this.collection, 'add', this.render);
			this.listenTo(this.collection, 'remove', this.render);
			this.loadIntro();
		},

		addOne: function (event) {
			event.preventDefault();
			let model = new WilokeShortcodes.Models.listFeatures();
			this.collection.add(model);

			let itemView = new WilokeShortcodes.Views.listFeatureItem({
				model: model
			});
			this.$el.find('.addlisting-popup__panel').append(itemView.render().el);


			let tabItem = new WilokeShortcodes.Views.listFeatureTab({
				model: model
			});

			$(event.target).before(tabItem.render().el);
			this.$el.find('.addlisting-popup__panel').append(itemView.render().el);

			// referring to tab to know more
			this.$el.trigger('addedNew');
		},

		render: function () {
			// Add Tabs
			let order = 0;

			this.$el.find('.addlisting-popup__panel').empty();
			this.$el.find('.addlisting-popup__nav').empty();
			this.collection.each(function (model) {
				let tabItem = new WilokeShortcodes.Views.listFeatureTab({
					model: model
				});
				this.$el.find('.addlisting-popup__nav').append(tabItem.render(order).el);
				order++;
			}, this);

			// Add Plus button
			this.$el.find('.addlisting-popup__nav').append('<span class="addlisting-popup__plus">+</span>');

			// Add Contents
			this.collection.each(function (model) {
				let itemView = new WilokeShortcodes.Views.listFeatureItem({
					model: model
				});
				this.$el.find('.addlisting-popup__panel').append(itemView.render().el);
			}, this);
		}
	});

	WilokeShortcodes.Views.listFeatureItem = Backbone.View.extend({
		tagName     : 'div',
		className   : 'addlisting-popup__group',
		events: {
			'keypress input': 'changedValue',
			'cut input': 'changedValue',
			'paste input': 'changedValue',
			'change #feature-unavailable': 'changedValue'
		},
		initialize: function () {
			this.handling = null;
		},
		template: _.template($('#wiloke-list-feature-item').html()),
		changedValue: function (event) {
			if ( this.handling !== null ){
				clearTimeout(this.handling);
			}

			let self = this;

			let $target = $(event.target);

			this.handling = setTimeout(function () {

				if ( $target.attr('type') === 'checkbox' ){

					if ( $target.is(':checked') ){
						self.model.set($target.attr('name'), 'yes');
					}else{
						self.model.set($target.attr('name'), '');
					}
				}else{
					self.model.set($target.attr('name'), $target.val());
				}

				clearTimeout(self.handling);
			}, 500);
		},
		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			this.$el.attr('id', this.model.cid);
			return this;
		}
	});

	WilokeShortcodes.Views.listFeatureTab = Backbone.View.extend({
		tagName     : 'a',
		className: '',
		template: _.template('<span class="title"><%= name %></span><span class="addlisting-popup__nav-remove" data-id=<%= id %>></span>'),
		initialize: function () {
			this.activateClass = false;
			this.listenTo(this.model, 'change:name', this.updateName);
		},
		updateName: function () {
			this.$el.find('.title').html(this.model.get('name'));
		},
		render: function (order) {
			this.className = order === 0 ? 'active' : '';
			let oSettings = this.model.toJSON();
			oSettings.id = this.model.cid;

			this.$el.html(this.template(oSettings));
			this.$el.attr('href', '#'+this.model.cid);
			return this;
		}
	});

	WilokeShortcodes.Models.listFeatureTitle = Backbone.Model.extend({
		defaults: {
			title: ''
		}
	});

	WilokeShortcodes.Views.listFeatureTitle = Backbone.View.extend({
		el: '#list-features-title-wrapper',
		tag: 'div',
		template: _.template($('#list-features-title-tpl').html()),
		initialize: function () {
			this.handling = null;
			this.render();
			// this.listenTo(this.model, 'change:title', this.render);
		},
		events: {
			'change #list-features-title': 'changedTitle',
			'cut #list-features-title': 'changedTitle',
			'paste #list-features-title': 'changedTitle',
			'keypress #list-features-title': 'changedTitle'
		},
		changedTitle: function (event) {
			if ( this.handling !== null ){
				clearTimeout(this.handling);
			}

			let self = this;
			this.handling = setTimeout(function () {
				self.model.set('title', $(event.currentTarget).val());
				self.updateEmulateTitle();
				clearTimeout(self.handling);
			}, 400);
		},
		updateEmulateTitle: function () {
			$('#show-list-features-title').html(this.model.get('title'));
		},
		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			this.updateEmulateTitle();
			return this;
		}
	});

	function getCurrentTimestamp() {
		let d = new Date();
		return d.getTime();
	}

	/**
	 * Price Table Initialize
	 * @since 1.1.8
	 */
	let priceTitleModel = new WilokeShortcodes.Models.priceTitle();
	let priceTitleView = new WilokeShortcodes.Views.priceTitle({
		model: priceTitleModel
	});

	let menuTableCollection = new WilokeShortcodes.Collections.priceTable();
	let menuTableViewCollection = new WilokeShortcodes.Views.priceTable({
		collection: menuTableCollection
	});

	/**
	 * Accordion Initialize
	 * @since 1.1.8
	 */
	let accordionTitleModel = new WilokeShortcodes.Models.accordionTitle();
	let accordionTitleView = new WilokeShortcodes.Views.accordionTitle({
		model: accordionTitleModel
	});

	let accordionCollection = new WilokeShortcodes.Collections.accordion();
	let accordionViewCollection = new WilokeShortcodes.Views.accordion({
		collection: accordionCollection
	});

	/**
	 * List Features Initialize
	 * @since 1.1.8
	 */
	let listFeaturesTitleModel = new WilokeShortcodes.Models.listFeatureTitle();
	let listFeaturesTitleView = new WilokeShortcodes.Views.listFeatureTitle({
		model: listFeaturesTitleModel
	});

	let listFeaturesCollection = new WilokeShortcodes.Collections.listFeatures();
	let listFeaturesViewCollection = new WilokeShortcodes.Views.listFeatures({
		collection: listFeaturesCollection
	});

	/* Register the buttons */
	tinymce.create('tinymce.plugins.WilokeListGoNewShortcodes', {
		init : function(ed, url) {
			ed.addButton( 'listgo_new_accordions', {
				title : WILOKE_LISTGO_SC_TRANSLATION.accordion_btn,
				image : WILOKE_LISTGO_FUNCTIONALITY.url+'public/source/img/icon-accordion.png',
				onclick : function() {

					if ( typeof WILOKE_GLOBAL !== 'undefined' && WILOKE_GLOBAL.toggleListingShortcodes === 'disable' ){
						alert(WILOKE_LISTGO_SC_TRANSLATION.needupdate);
						return false;
					}

					accordionTitleModel.set('title', WILOKE_LISTGO_SC_TRANSLATION.title);
					accordionTitleView.render();

					accordionCollection.reset();
					accordionCollection.add({
						'title': WILOKE_LISTGO_SC_TRANSLATION.accordion_title,
						'description': WILOKE_LISTGO_SC_TRANSLATION.accordion_desc
					});

					let $wrapper = $('#wiloke-accordion-settings');
					$wrapper.removeClass('hidden');

					$('#save-accordion').off().on('click', function (event) {
						let data = accordionCollection.toJSON();
						let title = accordionTitleModel.get('title');
						let content = WilokeShortcodes.emulateSC.accordion.replace('{{valuehere}}', WilokeHelps.encode(JSON.stringify(data)));
						content = content.replace(/{{idhere}}/g, getCurrentTimestamp());
						content = content.replace(/{{titlehere}}/g, title);
						ed.execCommand('mceInsertContent', 0, content+'&nbsp;');

						$closeBtn.trigger('click');
					});

					WilokeSCTab.init({
						$wrapper: $wrapper
					});
				}
			});

			ed.addButton( 'listgo_new_list_features', {
				title : WILOKE_LISTGO_SC_TRANSLATION.list_features_btn,
				image : WILOKE_LISTGO_FUNCTIONALITY.url+'public/source/img/icon-list.png',
				onclick : function() {

					if ( typeof WILOKE_GLOBAL !== 'undefined' && WILOKE_GLOBAL.toggleListingShortcodes === 'disable' ){
						alert(WILOKE_LISTGO_SC_TRANSLATION.needupdate);
						return false;
					}

					listFeaturesTitleModel.set('title', WILOKE_LISTGO_SC_TRANSLATION.title);
					listFeaturesTitleView.render();

					listFeaturesCollection.reset();
					listFeaturesCollection.add({
						'name': 'Wifi',
						'unavailable': ''
					});

					let $wrapper = $('#wiloke-list-features-settings');
					$wrapper.removeClass('hidden');

					$('#save-list-features').off().on('click', function (event) {
						let data = listFeaturesCollection.toJSON();
						let title = listFeaturesTitleModel.get('title');
						let content = WilokeShortcodes.emulateSC.listFeatures.replace('{{valuehere}}', WilokeHelps.encode(JSON.stringify(data)));
						content = content.replace(/{{idhere}}/g, getCurrentTimestamp());
						content = content.replace(/{{titlehere}}/g, title);

						ed.execCommand('mceInsertContent', 0, content+'&nbsp;');

						$closeBtn.trigger('click');
					});

					WilokeSCTab.init({
						$wrapper: $wrapper
					});
				}
			});

			ed.addButton( 'listgo_new_menu_prices', {
				title : WILOKE_LISTGO_SC_TRANSLATION.menu_price_btn,
				image : WILOKE_LISTGO_FUNCTIONALITY.url+'public/source/img/icon-price.png',
				onclick : function() {
					if ( typeof WILOKE_GLOBAL !== 'undefined' && WILOKE_GLOBAL.toggleListingShortcodes === 'disable' ){
						alert(WILOKE_LISTGO_SC_TRANSLATION.needupdate);
						return false;
					}

					priceTitleModel.set('title', WILOKE_LISTGO_SC_TRANSLATION.title);
					priceTitleView.render();

					menuTableCollection.reset();
					menuTableCollection.add({
						'name': WILOKE_LISTGO_SC_TRANSLATION.price_name,
						'price': WILOKE_LISTGO_SC_TRANSLATION.price_cost,
						'description': WILOKE_LISTGO_SC_TRANSLATION.price_desc
					});

					let $priceSettings = $('#wiloke-menu-price-settings');
					$priceSettings.removeClass('hidden');

					$('#save-price-table').off().on('click', function (event) {
						let data = menuTableCollection.toJSON();
						let title = priceTitleModel.get('title');

						let content = WilokeShortcodes.emulateSC.priceTable.replace('{{valuehere}}', WilokeHelps.encode(JSON.stringify(data)));
						content = content.replace(/{{idhere}}/g, getCurrentTimestamp());
						content = content.replace(/{{titlehere}}/g, title);
						ed.execCommand('mceInsertContent', 0, content+'&nbsp;');

						$closeBtn.trigger('click');
					});

					WilokeSCTab.init({
						$wrapper:$priceSettings
					});
				}
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get) {
					// Price

					let crePriceRex = new RegExp('(?:<div) (id="(?:[^\"]*)" class="addlisting-placeholder addlisting-placeholder-prices"(?:[^>]*))>(.+?)(?=(<\/span><\/div><\/div>))<\/span><\/div><\/div>', 'g');
					o.content = o.content.replace(crePriceRex, function (match, wholeSCData) {
						wholeSCData = wholeSCData.trim();
						return '[wiloke_price_table ' + wholeSCData + ' /]';
					});

					// Accordion
					let creAccordionRex = new RegExp('(?:<div) (id="(?:[^\"]*)" class="addlisting-placeholder addlisting-placeholder-accordion"(?:[^>]*))>(.+?)(?=(<\/span><\/div><\/div>))<\/span><\/div><\/div>', 'g');
					o.content = o.content.replace(creAccordionRex, function (match, wholeSCData) {
						wholeSCData = wholeSCData.trim();
						return '[wiloke_accordion ' + wholeSCData + ' /]';
					});

					// List Features
					let creaListFeaturesReg = new RegExp('(?:<div) (id="(?:[^\"]*)" class="addlisting-placeholder addlisting-placeholder-list-features"(?:[^>]*))>(.+?)(?=(<\/span><\/div><\/div>))<\/span><\/div><\/div>', 'g');
					o.content = o.content.replace(creaListFeaturesReg, function (match, wholeSCData) {
						wholeSCData = wholeSCData.trim();
						return '[wiloke_list_features ' + wholeSCData + ' /]';
					});
				}
			});

			ed.onBeforeSetContent.add(function(ed, o){
				// Price
				o.content = o.content.replace(/\[wiloke_price_table id="([^"]*)"([^\/\]]*)\/\]/g, function (match, scID, content) {
					content = content.trim();
					let aFindTitle = content.match(/(?:data-title=")([^\"]*)(?:")/);
					return '<div id="'+scID+'" '+ content + ' draggable="true"><div class="addlisting-placeholder__icon" style="background-image: url('+ WILOKE_LISTGO_FUNCTIONALITY.url + 'public/source/img/icon-price.png' +')"></div><div class="addlisting-placeholder__title">'+aFindTitle[1]+'</div><div class="addlisting-placeholder__actions"><span data-id="'+scID+'" class="addlisting-placeholder__action-edit wiloke-edit-menu-prices">'+WILOKE_LISTGO_SC_TRANSLATION.edit+'</span><span class="addlisting-placeholder__action-remove">'+WILOKE_LISTGO_SC_TRANSLATION.remove+'</span></div></div>';
				});

				// Accordion
				o.content = o.content.replace(/\[wiloke_accordion id="([^"]*)"([^\/\]]*)\/\]/g, function (match, scID, content) {
					content = content.trim();
					let aFindTitle = content.match(/(?:data-title=")([^\"]*)(?:")/);
					return '<div id="'+scID+'" '+ content + ' draggable="true"><div class="addlisting-placeholder__icon" style="background-image: url('+ WILOKE_LISTGO_FUNCTIONALITY.url + 'public/source/img/icon-accordion.png' +')"></div><div class="addlisting-placeholder__title">'+aFindTitle[1]+'</div><div class="addlisting-placeholder__actions"><span data-id="'+scID+'" class="addlisting-placeholder__action-edit wiloke-edit-accordion">'+WILOKE_LISTGO_SC_TRANSLATION.edit+'</span><span class="addlisting-placeholder__action-remove">'+WILOKE_LISTGO_SC_TRANSLATION.remove+'</span></div></div>';
				});

				// List Features
				o.content = o.content.replace(/\[wiloke_list_features id="([^"]*)"([^\/\]]*)\/\]/g, function (match, scID, content) {
					content = content.trim();
					let aFindTitle = content.match(/(?:data-title=")([^\"]*)(?:")/);

					return '<div id="'+scID+'" '+ content + ' draggable="true"><div class="addlisting-placeholder__icon" style="background-image: url('+ WILOKE_LISTGO_FUNCTIONALITY.url + 'public/source/img/icon-list.png' +')"></div><div class="addlisting-placeholder__title">'+aFindTitle[1]+'</div><div class="addlisting-placeholder__actions"><span data-id="'+scID+'" class="addlisting-placeholder__action-edit wiloke-edit-list-features">'+WILOKE_LISTGO_SC_TRANSLATION.edit+'</span><span class="addlisting-placeholder__action-remove">'+WILOKE_LISTGO_SC_TRANSLATION.remove+'</span></div></div>';
				});
			});

			ed.onInit.add(function(ed)
			{
				ed.on('mousedown', function( event ) {
					let $target = $(event.target);

					// Price
					if ( $target.hasClass('wiloke-edit-menu-prices') ){
						let editingID = $target.data('id'),
							$parent   = $target.closest('#'+editingID),
							title     = $parent.data('title'),
							oSettings = $parent.data('settings');

						priceTitleModel.set('title', title);
						priceTitleView.render();

						oSettings = WilokeHelps.decode(oSettings);
						menuTableCollection.reset();
						oSettings = oSettings !== '' ? JSON.parse(oSettings) : oSettings;
						menuTableCollection.add(oSettings);

						let $priceSettings = $('#wiloke-menu-price-settings');
						$priceSettings.removeClass('hidden');

						$('#save-price-table').off().on('click', function (event) {

							if ( typeof WILOKE_GLOBAL !== 'undefined' && WILOKE_GLOBAL.toggleListingShortcodes === 'disable' ){
								$closeBtn.trigger('click');
								return false;
							}

							let string = 'wiloke_price_table id=(\'|")'+editingID+'(\'|")([^\\]]*)',
								data = menuTableCollection.toJSON(),
								getCurrentContent = ed.getContent(),
								creRex = new RegExp('\\['+string+'\\]', 'g'),
								newContent = getCurrentContent.replace(creRex, function (match) {
									let content = WilokeShortcodes.emulateSC.priceTable.replace('{{valuehere}}', WilokeHelps.encode(JSON.stringify(data)));
									content = content.replace(/{{idhere}}/g, getCurrentTimestamp());
									content = content.replace(/{{titlehere}}/g, priceTitleModel.get('title'));
									return content;
								});
							ed.setContent(newContent);
							$closeBtn.trigger('click');
						});

						WilokeSCTab.init({
							$wrapper: $priceSettings
						});
					}

					// Accordion
					if ( $target.hasClass('wiloke-edit-accordion') ){
						let editingID = $target.data('id'),
							$parent   = $target.closest('#'+editingID),
							title     = $parent.data('title'),
							oSettings = $parent.data('settings');
						oSettings = WilokeHelps.decode(oSettings);
						accordionTitleModel.set('title', title);
						accordionTitleView.render();

						accordionCollection.reset();
						oSettings = oSettings !== '' ? JSON.parse(oSettings) : oSettings;
						accordionCollection.add(oSettings);

						let $accordionSettings = $('#wiloke-accordion-settings');
						$accordionSettings.removeClass('hidden');

						$('#save-accordion').off().on('click', function (event) {

							if ( typeof WILOKE_GLOBAL !== 'undefined' && WILOKE_GLOBAL.toggleListingShortcodes === 'disable' ){
								$closeBtn.trigger('click');
								return false;
							}

							let string = 'wiloke_accordion id=(\'|")'+editingID+'(\'|")([^\\]]*)',
								data = accordionCollection.toJSON(),
								getCurrentContent = ed.getContent(),
								creRex = new RegExp('\\['+string+'\\]', 'g'),
								newContent = getCurrentContent.replace(creRex, function (match) {
									let content = WilokeShortcodes.emulateSC.accordion.replace('{{valuehere}}', WilokeHelps.encode(JSON.stringify(data)));
									content = content.replace(/{{idhere}}/g, getCurrentTimestamp());
									content = content.replace(/{{titlehere}}/g, accordionTitleModel.get('title'));
									return content;
								});

							ed.setContent(newContent);
							$closeBtn.trigger('click');
						});

						WilokeSCTab.init({
							$wrapper: $accordionSettings
						});
					}

					// List Features
					if ( $target.hasClass('wiloke-edit-list-features') ){
						let editingID = $target.data('id'),
							$parent   = $target.closest('#'+editingID),
							title     = $parent.data('title'),
							oSettings = $parent.data('settings');
						oSettings = WilokeHelps.decode(oSettings);
						// let menuTableCollection = new WilokeShortcodes.Collections.priceTable(oSettings);
						listFeaturesTitleModel.set('title', title);
						listFeaturesTitleView.render();

						listFeaturesCollection.reset();
						oSettings = oSettings !== '' ? JSON.parse(oSettings) : oSettings;
						listFeaturesCollection.add(oSettings);

						let $listFeaturesSettings = $('#wiloke-list-features-settings');
						$listFeaturesSettings.removeClass('hidden');

						$('#save-list-features').off().on('click', function (event) {

							if ( typeof WILOKE_GLOBAL !== 'undefined' && WILOKE_GLOBAL.toggleListingShortcodes === 'disable' ){
								$closeBtn.trigger('click');
								return false;
							}

							let string = 'wiloke_list_features id=(\'|")'+editingID+'(\'|")([^\\]]*)',
								data = listFeaturesCollection.toJSON(),
								getCurrentContent = ed.getContent(),
								creRex = new RegExp('\\['+string+'\\]', 'g'),
								newContent = getCurrentContent.replace(creRex, function (match) {
									let content = WilokeShortcodes.emulateSC.listFeatures.replace('{{valuehere}}', WilokeHelps.encode(JSON.stringify(data)));
									content = content.replace(/{{idhere}}/g, getCurrentTimestamp());
									content = content.replace(/{{titlehere}}/g, listFeaturesTitleModel.get('title'));
									return content;
								});

							ed.setContent(newContent);
							$closeBtn.trigger('click');
						});

						WilokeSCTab.init({
							$wrapper: $listFeaturesSettings
						});
					}


					// Remove Shortcode
					if ( $target.hasClass('addlisting-placeholder__action-remove') ){
						$target.closest('.addlisting-placeholder').remove();
					}
				})
			})
		},
		createControl : function(n, cm) {
			return null;
		},
	});
	/* Start the buttons */
	tinymce.PluginManager.add( 'listgo_new_shortcodes', tinymce.plugins.WilokeListGoNewShortcodes );

	$(document).ready(function () {
		$('.addlisting-popup__close, .cancel-shortcode').on('click', function (event) {
			event.preventDefault();
			$(this).closest('.addlisting-popup-wrap').addClass('hidden');
		});
	});

	$(window).load(function () {

		$('.wiloke-sc-intro').each(function () {
			$(this).attr('src', $(this).data('src'));
			$(this).addClass('loaded');
		});

		let $toggle = $('.mce-widget[aria-label="Toolbar Toggle"]'),
			$mediaBtn = $('#insert-media-button'),
			toolbarStatus = $toggle.attr('aria-pressed');

			$toggle.on('click', (event)=>{
				if ( toolbarStatus === 'false' || (typeof toolbarStatus === 'undefined') ){
					$mediaBtn.addClass('hidden');
				}else{
					$mediaBtn.removeClass('hidden');
				}
			});

			if (  typeof WILOKE_GLOBAL !== 'undefined' && WILOKE_GLOBAL.toggleListingShortcodes === 'disable' ){
				$('.mce-widget[aria-label="'+WILOKE_LISTGO_SC_TRANSLATION.menu_price_btn+'"]').addClass('disable');
				$('.mce-widget[aria-label="'+WILOKE_LISTGO_SC_TRANSLATION.list_features_btn+'"]').addClass('disable');
				$('.mce-widget[aria-label="'+WILOKE_LISTGO_SC_TRANSLATION.accordion_btn+'"]').addClass('disable');
			}
	});

})(jQuery);