(function( $ ) {
	'use strict';

	// Create the defaults once
	var pluginName = "wishlist",
	defaults = {
		trans : {
			'btnAddText' : 'Add to Wishlist',
			'btnAddedText' : 'Added to Wishlist',
			'selectWishlist' : 'Select a Wishlist',
			'createWishlist' : 'Create a new Wishlist',
		}
	};

	// The actual plugin constructor
	function Plugin ( element, options ) {
		this.element = element;
		
		this.settings = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this.trans = this.settings.trans;
		this._name = pluginName;
		this.init();
	}

	// Avoid Plugin.prototype conflicts
	$.extend( Plugin.prototype, {
		init: function() {
			this.window = $(window);
			this.documentHeight = $( document ).height();
			this.windowHeight = this.window.height();
			this.currentURL = window.location.href.split('?')[0];

			this.product = {};
			this.wishlist = {};
			this.products = {};
			this.elements = {};

			this.elements.wishlistAddProduct = '.woocommerce-wishlist-add-product';
			this.elements.wishlistExportButton = '.woocommerce-wishlist-export-button';
			this.elements.wishlistRemoveProduct = $('.woocommerce-wishlist-remove-product');
			this.elements.wishlistModal = $('.woocommerce-wishlist-modal');
			this.elements.wishlistModalContent = $('.woocommerce-wishlist-modal-content');
			this.elements.wishlistItems = $('.woocommerce-wishlist-items');
			this.elements.wishlistContainer = $('.woocommerce-wishlist-container');
			this.elements.wishlistList = $('.woocommerce-wishlist-your-wishlists ul');

			this.elements.wishlistCreate = $('#woocommerce-wishlist-create');
			this.elements.wishlistDelete = $('.woocommerce-wishlist-delete');
			this.elements.wishlistEdit = $('.woocommerce-wishlist-edit');
			this.elements.wishlistView = $('.woocommerce-wishlist-view');
			this.elements.wishlistSearch = $('#woocommerce-wishlist-search');
			this.elements.wishlistSearchItems = $('.woocommerce-wishlist-search-items');
			this.elements.wishlistSearchMessage = $('.woocommerce-wishlist-search-message');

			this.productAdded = false;
			this.loggedIn = false;
			if ($('body').hasClass('logged-in')) {
				this.loggedIn =  true;
			}

			this.wishlistSetProductsByCookie();

			this.wishlistAddProductButton();
			this.wishlistAddProduct();
			this.wishlistExportButtons();

			this.wishlistCreateButton();
			this.wishlistCreate();

			if(this.elements.wishlistContainer.length > 0) {
				this.wishlistEdit();
				this.wishlistEditButton();
				this.wishlistViewButton();
				this.wishlistDelete();
				this.wishlistRemoveProduct();
				this.guestWishlist();
			}

			this.wishlistSearch();
			this.variationSupport();
		},
		wishlistSetProductsByCookie : function() {

			var that = this;
			var cookieProducts = that.readCookie('woocommerce_wishlist_products');
			if(!that.loggedIn && !that.isEmpty(cookieProducts)) {
				that.products = cookieProducts;
				$.each(cookieProducts, function(i, index) {
					$('.woocommerce-wishlist-add-product[data-product-id="' + index + '"]').html(that.trans.btnAddedText).prop('href', that.settings.wishlistPage);
				});
			}
		},
		variationSupport : function() {

			var variableProductId = $('.woocommerce-wishlist-add-product').data('product-id');
			$( ".single-product .single_variation_wrap" ).on( "show_variation", function ( event, variation ) {

				if(variation.variation_id > 0) {
					$('.single-product .woocommerce-wishlist-add-product').attr('data-product-id', variation.variation_id);
				} else {
					$('.single-product .woocommerce-wishlist-add-product').attr('data-product-id', variableProductId);
				}
			} );

		},
		wishlistAddProductButton : function() {

			var that = this;
			var product_id;

			$('body').on('click', that.elements.wishlistAddProduct, function(e) {

				var $this = $(this);
				var checkAdded = $this.attr("href");
				if(checkAdded == that.settings.wishlistPage) {
					return true;
				}

				e.preventDefault();

				$this.html('<i class="fa fa-spinner fa-spin"></i>')
				that.elements.wishlistModalContent.html('');

				product_id = $this.data('product-id');
                 
                if( jQuery('body').hasClass('single-product') ){
					product_id = jQuery(this).attr('data-product-id');
				}

				if(product_id == "") {
					$this.html(that.trans.btnAddText);
					return;
				}

				that.product = product_id;
				if(that.loggedIn) {
					that.getWishlistsSelect(product_id);
				} else {
					if(product_id !== "") {
						that.products[product_id] = product_id;
						that.saveCookie('woocommerce_wishlist_products', that.products, that.settings.cookieLifetime);
						$this.prop('href', that.settings.wishlistPage).html(that.trans.btnAddedText);
					}
				}
			});
		},
		wishlistExportButtons : function() {

			var that = this;
			var product_id;

			$('body').on('click', that.elements.wishlistExportButton, function(e) {
				
				var type = $(this).data('type');
				var wishlist = $(this).data('wishlist');

				jQuery.ajax({
					url: that.settings.ajax_url,
					type: 'post',
					data: {
						type : type,
						wishlist : wishlist,
						action: 'wishlist_export',
					},
					success : function( data ) {					
						if(type == "csv") {
							var uri = 'data:text/csv;charset=UTF-8,' + encodeURIComponent(data);
							window.open(uri, 'wishlist_export.csv');
						} else {
							var uri = 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8,' + encodeURIComponent(data);
							window.open(uri, 'wishlist_export.xls');
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
					}
				});

			});

		},
		wishlistAddProduct : function() {
			var that = this;

			that.elements.wishlistModal.on('change', '#woocommerce-wishlist-select', function(e) {

				var wishlist = $(this).val();
				if(!wishlist) {
					return false;
				}

				jQuery.ajax({
					url: that.settings.ajax_url,
					type: 'post',
					dataType: 'html',
					data: {
						product : that.product,
						wishlist : wishlist,
						action: 'add_product',
					},
					success : function( response ) {
						that.productAdded = true;
						that.elements.wishlistModalContent.html(response);
						$('.woocommerce-wishlist-add-product[data-product-id="' + that.product + '"]').html(that.trans.btnAddedText).prop('href', that.settings.wishlistPage);
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$('.woocommerce-wishlist-add-product[data-product-id="' + that.product + '"]').html(that.trans.btnText);
						console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
					}
				});
			});
		},
		wishlistRemoveProduct : function() {
			var that = this;

			that.elements.wishlistItems.on('click', '.woocommerce-wishlist-remove-product', function(e) {
				e.preventDefault();

				var product = $(this).data('product');

				if(!product) {
					return;
				}

				var $this = $(this);

				if(that.loggedIn) {
					jQuery.ajax({
						url: that.settings.ajax_url,
						type: 'post',
						dataType: 'html',
						data: {
							product : product,
							wishlist : that.wishlist,
							action: 'remove_product',
						},
						success : function( response ) {
							$this.parent('.woocommerce-wishlist-item').slideUp();
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$('.woocommerce-wishlist-add-product[data-product-id="' + product + '"]').html(that.trans.btnText);
							console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
						}
					});
				} else {
					var cookieProducts = that.readCookie('woocommerce_wishlist_products');
					delete cookieProducts[product];
					that.saveCookie('woocommerce_wishlist_products', cookieProducts, that.settings.cookieLifetime);
					$this.parent('.woocommerce-wishlist-item').slideUp();
				}
			});
		},
		getWishlistsSelect : function() {
			var that = this;
		
			jQuery.ajax({
				url: that.settings.ajax_url,
				type: 'post',
				dataType: 'json',
				data: {
					action: 'get_wishlists',
				},
				success : function( response ) {

					var html = "";
					if(!that.isEmpty(response)) {
						html += '<div class="wishlistmodal-select-wishlist-title" >' + that.trans.selectWishlist + '</div>';
						html += '<select name="wishlist_select" id="woocommerce-wishlist-select">';
						html += '<option value="">' + that.trans.selectWishlist + '</option>';
						$.each(response, function(i, index) {
							html += '<option value="' + index.id + '">' + index.name + '</option>';
						});
						html += '</select>';
					} 

					html += '<div class="wishlistmodal-new-wishlist-title">' + that.trans.createWishlist + '</div>';
					html += 
					'<select name="wishlist_visibility" id="woocommerce-wishlist-visibility">' +
					'<option value="private">' + that.trans.private + '</option>' +
					'<option value="shared">' + that.trans.shared + '</option>' +
					'<option value="public">' + that.trans.public + '</option>' +
					'</select>';

					html += '<input type="text" name="wishlist_name" id="woocommerce-wishlist-name" placeholder="' + that.trans.createWishlistName + '" />';
					html += '<input type="hidden" name="product" value="' + that.product + '" id="woocommerce-wishlist-product" />';
					html += '<button id="woocommerce-wishlist-create-button" class="button btn theme-button theme-btn btn-primary" type="button">' + that.trans.createWishlist + '</button>';
					that.elements.wishlistModalContent.html(html);
					that.elements.wishlistModal.wishlistmodal();

					that.elements.wishlistModal.on($.wishlistmodal.CLOSE, function(event, modal) {
						if(!that.productAdded) {
							$('.woocommerce-wishlist-add-product[data-product-id="' + that.product + '"]').html(that.trans.btnAddText);
						}
					});

				},
				error: function(jqXHR, textStatus, errorThrown) {
					$('.woocommerce-wishlist-add-product[data-product-id="' + that.product + '"]').html(that.trans.btnText);
					console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
				}
			});
		},
		getSingleProduct: function(product_id, callback, that) {

			var that = this;

			that.product = product_id;

			jQuery.ajax({
				url: that.settings.ajax_url,
				type: 'post',
				dataType: 'html',
				data: {
					action: 'wishlist_get_product',
					product: product_id
				},
				success : function( response ) {
					$('.woocommerce-wishlist-add-product[data-product-id="' + that.product + '"]').html(that.trans.btnText);
					callback(response, that);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$('.woocommerce-wishlist-add-product[data-product-id="' + that.product + '"]').html(that.trans.btnText);
					console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
				}
			});
		},
		wishlistCreateButton : function() {
			var that = this;

			that.elements.wishlistCreate.on('click', function(e) {
				e.preventDefault();

				var html = '<h2>' + that.trans.createWishlist + '</h2>';

				html += 
				'<select name="wishlist_visibility" id="woocommerce-wishlist-visibility">' +
				'<option value="private">' + that.trans.private + '</option>' +
				'<option value="shared">' + that.trans.shared + '</option>' +
				'<option value="public">' + that.trans.public + '</option>' +
				'</select>';

				html += '<input type="text" name="wishlist_name" id="woocommerce-wishlist-name" placeholder="' + that.trans.createWishlistName + '" />';
				html += '<button id="woocommerce-wishlist-create-button" class="button btn btn-default theme-button theme-btn btn-primary" type="button">' + that.trans.createWishlist + '</button>';

				that.elements.wishlistModalContent.html(html);
				that.elements.wishlistModal.wishlistmodal();
			});

			that.elements.wishlistModal.on('keypress', '#woocommerce-wishlist-name', function(e) {
				if(e.which == 13) {
					that.elements.wishlistModal.find('#woocommerce-wishlist-create-button').trigger('click');
				}
			});
		},
		wishlistCreate : function() {
			var that = this;

			that.elements.wishlistModal.on('click', '#woocommerce-wishlist-create-button', function(e) {

				var visibility = that.elements.wishlistModal.find('#woocommerce-wishlist-visibility').val();
				var name = that.elements.wishlistModal.find('#woocommerce-wishlist-name').val();
				var product = that.elements.wishlistModal.find('#woocommerce-wishlist-product').val();

				if(!name) {
					return;
				}

				var $this = $(this);

				jQuery.ajax({
					url: that.settings.ajax_url,
					type: 'post',
					dataType: 'json',
					data: {
						visibility : visibility,
						name : name,
						product : product,
						action: 'create_wishlist',
					},
					success : function( response ) {
						
						if(typeof product !== "undefined" && product !== "") {
							$('.close-wishlistmodal').trigger('click');
							$('.woocommerce-wishlist-add-product[data-product-id="' + product + '"]').html(that.trans.btnAddedText).prop('href', that.settings.wishlistPage);
						} else {
							var visibility = "";
							if(response.visibility == "private") {
								visibility = '<i class="fa fa-lock"></i>';
							}
							if(response.visibility == "shared") {
								visibility = '<i class="fa fa-link"></i>';
							}
							if(response.visibility == "public") {
								visibility = '<i class="fa fa-globe"></i>';
							}

							var html = 
							'<li>' +
							visibility + ' <a href="#" data-id="' + response.ID + '" class="woocommerce-wishlist-view">' + response.name + '</a>' +
							'</li>';
							that.elements.wishlistList.append(html);
							$('.close-wishlistmodal').trigger('click');
							that.wishlistView(response.ID);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
					}
				});
			});
		},
		wishlistDelete : function() {
			var that = this;

			that.elements.wishlistContainer.on('click', '.woocommerce-wishlist-delete', function(e) {
				e.preventDefault();
				var wishlist = $(this).data('id');

				jQuery.ajax({
					url: that.settings.ajax_url,
					type: 'post',
					dataType: 'html',
					data: {
						action: 'delete_wishlist',
						wishlist: wishlist
					},
					success : function( response ) {
						that.elements.wishlistContainer.find('.woocommerce-wishlist-items').html(that.trans.wishlistDeleted);
						that.elements.wishlistList.find('li#wishlist-li-' + wishlist).fadeOut();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
					}
				});
			});
		},
		wishlistEditButton : function() {
			var that = this;

			that.elements.wishlistContainer.on('click', '.woocommerce-wishlist-edit', function(e) {
				e.preventDefault();
				var wishlist = $(this).data('id');

				jQuery.ajax({
					url: that.settings.ajax_url,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'get_wishlist',
						wishlist: wishlist
					},
					success : function( response ) {

						var html = '<h2>' + that.trans.editWishlist + '</h2>';

						html += 
						'<select name="wishlist_visibility" id="woocommerce-wishlist-visibility">';

						if(response.visibility == "private") {
							html += '<option selected="selected" value="private">' + that.trans.private + '</option>';
						} else {
							html += '<option value="private">' + that.trans.private + '</option>';
						}

						if(response.visibility == "shared") {
							html += '<option selected="selected" value="shared">' + that.trans.shared + '</option>';
						} else {
							html += '<option value="shared">' + that.trans.shared + '</option>'
						}

						if(response.visibility == "public") {
							html += '<option selected="selected" value="public">' + that.trans.public + '</option>';
						} else {
							html += '<option value="public">' + that.trans.public + '</option>';
						}

						html += '</select>';

						html += '<input type="text" name="wishlist_name" id="woocommerce-wishlist-name" value="' + response.name + '" />';
						html += '<input type="hidden" name="wishlist_id" id="woocommerce-wishlist-id" value="' + response.id + '" />';
						html += '<button id="woocommerce-wishlist-edit-button" class="button btn btn-default theme-button theme-btn btn-primary type="button">' + that.trans.editWishlist + '</button>';
						
						that.elements.wishlistModalContent.html(html);
						that.elements.wishlistModal.wishlistmodal();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
					}
				});
			});

			that.elements.wishlistModal.on('keypress', '#woocommerce-wishlist-name', function(e) {
				if(e.which == 13) {
					that.elements.wishlistModal.find('#woocommerce-wishlist-edit-button').trigger('click');
				}
			});
		},
		wishlistEdit : function() {
			var that = this;

			that.elements.wishlistModal.on('click', '#woocommerce-wishlist-edit-button', function(e) {

				var visibility = that.elements.wishlistModal.find('#woocommerce-wishlist-visibility').val();
				var name = that.elements.wishlistModal.find('#woocommerce-wishlist-name').val();
				var wishlist = that.elements.wishlistModal.find('#woocommerce-wishlist-id').val();

				if(!name) {
					return;
				}

				jQuery.ajax({
					url: that.settings.ajax_url,
					type: 'post',
					dataType: 'json',
					data: {
						visibility : visibility,
						name : name,
						wishlist : wishlist,
						action: 'edit_wishlist',
					},
					success : function( response ) {
						that.elements.wishlistContainer.find('.woocommerce-wishlist-header-title').text(response.name);
						that.elements.wishlistList.find('li#wishlist-li-' + wishlist + ' a').text(response.name);
						$('.close-wishlistmodal').trigger('click');
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
					}
				});
			});
		},
		wishlistViewButton : function() {
			var that = this;

			that.elements.wishlistContainer.on('click', '.woocommerce-wishlist-view', function(e) {
				e.preventDefault();

				var wishlist = $(this).data('id');
				that.wishlistView(wishlist);
			});

			var predefinedWishlist = that.getParameterByName('wishlist');
			if(predefinedWishlist) {
				that.wishlistView(predefinedWishlist);
			} else {
				that.elements.wishlistView.first().trigger('click');
			}
		},
		guestWishlist : function () {
			var that = this;

			if(!that.isEmpty( that.getParameterByName('wishlist') )) {
				return;
			}

			var cookieProducts = that.readCookie('woocommerce_wishlist_products');
			if(that.isEmpty(cookieProducts)) {

				var queryURLProducts = that.getParameterByName('wishlist-products');
				if(!that.isEmpty(queryURLProducts)) {

					var jsonStrig = '{';
					var items = queryURLProducts.split(',');
					for (var i = 0; i < items.length; i++) {
						jsonStrig += '"' + items[i] + '":"' + items[i] + '",';
					}
					jsonStrig = jsonStrig.substr(0, jsonStrig.length - 1);
					jsonStrig += '}';
					cookieProducts = JSON.parse(jsonStrig);
					that.saveCookie('woocommerce_wishlist_products', cookieProducts, that.settings.cookieLifetime);
				} else {
					that.elements.wishlistItems.html('<h2>' + that.trans.noProducts + '</h2>');
					return;
				}
			}
			
			jQuery.ajax({
				url: that.settings.ajax_url,
				type: 'post',
				dataType: 'html',
				data: {
					action : 'get_cookie_wishlist',
					products : cookieProducts
				},
				success : function( response ) {
					that.elements.wishlistItems.html(response);
					that.buildReplaceState();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
				}
			});
		},
		wishlistView : function(wishlist) {
			var that = this;

			if(!wishlist) {
				return;
			}

			jQuery.ajax({
				url: that.settings.ajax_url,
				type: 'post',
				dataType: 'html',
				data: {
					action: 'view_wishlist',
					wishlist: wishlist
				},
				success : function( response ) {
					that.wishlist = wishlist;
					that.elements.wishlistItems.html(response);
					that.buildReplaceState();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
				}
			});

		},
		wishlistSearch : function() {

			var that = this;
			var delayTimer;
			var resultContainer = this.elements.wishlistSearchItems;
			var resultMessageContainer = this.elements.wishlistSearchMessage;

			$(that.elements.wishlistSearch).on('keyup', function(e) {
				resultContainer.fadeOut().html('');

				var $this = $(this);
				var term = $this.val();

				if(term.length > 2) {

					resultMessageContainer.html('<i class="fas fa-spinner fa-spin"></i>');
					clearTimeout(delayTimer);
					delayTimer = setTimeout(function() {
						term = $this.val();
						$.ajax({
							type : 'post',
							url : that.settings.ajax_url,
							dataType : 'json',
							data : {
								term : term,
								action : 'search_wishlists'
							},
							success : function( response ) {
								console.log(response);
								resultMessageContainer.html(response.message);
								if( response.count == 0){
									return false;
								} else {
									var html = "";
									$.each(response.wishlists, function(i, index) {
										html += that.wishlistSearchGetItem(index);
									})
									resultContainer.fadeIn().html(html);
								}
							}
						});
					}, 1200);
				}
			});
		},
		wishlistSearchGetItem : function(wishlist) {
			var that = this;
			var html = "";

			html += '<div class="woocommerce-wishlist-search-item">';
			html += '<div class="woocommerce-wishlist-search-item-title">';
			html += wishlist.name;
			html += '</div>';
			html += '<div class="woocommerce-wishlist-search-item-meta">';
			html += '<div class="woocommerce-wishlist-search-item-author">';
			html += wishlist.author;
			html += '</div>';
			html += '<div class="woocommerce-wishlist-search-item-products">';
			html += wishlist.products;
			html += '</div>';
			html += '</div>';
			html += '<div class="woocommerce-wishlist-search-item-view">';
			html += '<a href="' + wishlist.url + '" class="btn button theme-button">' + that.trans.viewWishlist + '</a>';
			html += '</div>';
			html += '</div>';

			return html;
		},
		buildReplaceState : function() {

			var that = this;
			var url = "";

			if( Number.isInteger(that.wishlist) || !that.isEmpty(that.getParameterByName('wishlist')) ) {
				url += '?wishlist=' + that.wishlist;
			}

			var cookieProducts = that.readCookie('woocommerce_wishlist_products');
			if(!that.isEmpty(cookieProducts) && !Number.isInteger(that.wishlist) && !that.loggedIn) {
				url += '?wishlist-products=' + Object.values(cookieProducts).join(",");
			}

			window.history.replaceState('test', 'WooCommerce Wishlist', that.currentURL + url);
		},
		//////////////////////
		///Helper Functions///
		//////////////////////
		isEmpty: function(obj) {

			if (obj == null)		return true;
			if (obj.length > 0)		return false;
			if (obj.length === 0)	return true;

			for (var key in obj) {
				if (hasOwnProperty.call(obj, key)) return false;
			}

			return true;
		},
		getQuerystringData : function (name) {
			var data = { };
			var parameters = window.location.search.substring(1).split("&");
			for (var i = 0, j = parameters.length; i < j; i++) {
				var parameter = parameters[i].split("=");
				var parameterName = decodeURIComponent(parameter[0]);
				var parameterValue = typeof parameter[1] === "undefined" ? parameter[1] : decodeURIComponent(parameter[1]);
				var dataType = typeof data[parameterName];
				if (dataType === "undefined") {
					data[parameterName] = parameterValue;
				} else if (dataType === "array") {
					data[parameterName].push(parameterValue);
				} else {
					data[parameterName] = [data[parameterName]];
					data[parameterName].push(parameterValue);
				}
			}
			return typeof name === "string" ? data[name] : data;
		},
		saveCookie: function(name, value, minutes) {

			var expires = "";
			if (minutes) {
				var date = new Date();
				date.setTime(date.getTime() + (minutes * 60 * 1000));
				expires = "; expires=" + date.toGMTString();
			}

			var cookie = name + '=' + JSON.stringify(value) + expires + '; path=/;';
			document.cookie = cookie;
		},
		readCookie: function(name) {
			var result = document.cookie.match(new RegExp(name + '=([^;]+)'));
			result && (result = JSON.parse(result[1]));
			return result;
		},
		deleteCookie: function(name) {
			document.cookie = [name, '=; expires=Thu, 01-Jan-1970 00:00:01 GMT; path=/; domain=.', window.location.host.toString()].join('');
		},
		getObjectSize : function(obj) {
			var size = 0, key;
			for (key in obj) {
				if (obj.hasOwnProperty(key)) size++;
			}
			return size;
		},
		getParameterByName : function (name, url) {
			if (!url) url = window.location.href;
			name = name.replace(/[\[\]]/g, "\\$&");
			var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
			if (!results) return null;
			if (!results[2]) return '';
			return decodeURIComponent(results[2].replace(/\+/g, " "));
		},
	} );

	// Constructor wrapper
	$.fn[ pluginName ] = function( options ) {
		return this.each( function() {
			if ( !$.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" +
					pluginName, new Plugin( this, options ) );
			}
		} );
	};

	$.fn.emulateTransitionEnd = function (duration) {
		var called = false
		var $el = this
		$(this).one('bsTransitionEnd', function () { called = true })
		var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
		setTimeout(callback, duration)
		return this
	}

	$(document).ready(function() {

		$( "body" ).wishlist( 
			woocommerce_wishlist_options
			);

	} );

})( jQuery );