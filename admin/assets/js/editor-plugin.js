( function( tinymce, wpreview, $ ) {
	"use strict";

	var getShortcode = function( name, attrs ) {
		var output = '[' + name;
		for ( var k in attrs ) {
			if ( ! attrs[ k ] ) {
				continue;
			}
			output += ' ' + k + '="' + attrs[ k ] + '"';
		}
		output += ']';
		return output;
	};

	var getYelpSearchButton = function( editor ) {
		return {
			text: wprVars.yelpSearch,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.yelpSearch,
					body: [
						{
							type: 'textbox',
							name: 'term',
							label: wprVars.searchTerm
						},
						{
							type: 'textbox',
							name: 'location',
							label: wprVars.searchLocation,
							value: 'New York'
						},
						{
							type: 'textbox',
							name: 'radius',
							label: wprVars.searchRadius,
							value: ''
						},
						{
							type: 'textbox',
							name: 'categories',
							label: wprVars.searchCategories,
							value: ''
						},
						{
							type: 'listbox',
							name: 'locale',
							label: wprVars.searchLocale,
							value: 'en_US',
							values	: [
								{text: 'Czech Republic', value: 'cs_CZ'},
				        {text: 'Denmark', value: 'da_DK'},
				        {text: 'Austria', value: 'de_AT'},
				        {text: 'Austria', value: 'de_CH'},
				        {text: 'Germany', value: 'de_DE'},
				        {text: 'Australia', value: 'en_AU'},
				        {text: 'Belgium', value: 'en_BE'},
				        {text: 'Canada', value: 'en_CA'},
				        {text: 'Switzerland', value: 'en_CH'},
				        {text: 'United Kingdom', value: 'en_GB'},
				        {text: 'Hong Kong', value: 'en_HK'},
				        {text: 'Republic of Ireland', value: 'en_IE'},
				        {text: 'Malaysia', value: 'en_MY'},
				        {text: 'New Zealand', value: 'en_NZ'},
				        {text: 'Philippines', value: 'en_PH'},
				        {text: 'Singapore', value: 'en_SG'},
				        {text: 'United States', value: 'en_US'},
				        {text: 'Argentina', value: 'es_AR'},
				        {text: 'Chile', value: 'es_CL'},
				        {text: 'Spain', value: 'es_ES'},
				        {text: 'Mexico', value: 'es_MX'},
				        {text: 'Finland', value: 'fi_FI'},
				        {text: 'Philippines', value: 'fil_PH'},
				        {text: 'Belgium', value: 'fr_BE'},
				        {text: 'Canada', value: 'fr_CA'},
				        {text: 'Switzerland', value: 'fr_CH'},
				        {text: 'France', value: 'fr_FR'},
				        {text: 'Switzerland', value: 'it_CH'},
				        {text: 'Italy', value: 'it_IT'},
				        {text: 'Japan', value: 'ja_JP'},
				        {text: 'Malaysia', value: 'ms_MY'},
				        {text: 'Norway', value: 'nb_NO'},
				        {text: 'Belgium', value: 'nl_BE'},
				        {text: 'Netherlands', value: 'nl_NL'},
				        {text: 'Poland', value: 'pl_PL'},
				        {text: 'Brazil', value: 'pt_BR'},
				        {text: 'Portugal', value: 'pt_PT'},
				        {text: 'Finland', value: 'sv_FI'},
				        {text: 'Sweden', value: 'sv_SE'},
				        {text: 'Turkey', value: 'tr_TR'},
				        {text: 'Hong Kong', value: 'zh_HK'},
				        {text: 'Taiwan', value: 'zh_TW'}
							],
						},
						{
							type: 'textbox',
							name: 'offset',
							label: wprVars.searchOffset,
							value: ''
						},
						{
							type: 'textbox',
							name: 'limit',
							label: wprVars.limit,
							value: 3
						},
						{
							type: 'listbox',
							name: 'sort_by',
							label: wprVars.sort_by,
							value: 'best_match',
							values	: [
								{text: 'Best Match', value: 'best_match'},
								{text: 'Rating', value: 'rating'},
								{text: 'Review Count', value: 'review_count'},
								{text: 'Distance', value: 'distance'},
							]
						},
						{
							type: 'textbox',
							name: 'price',
							label: wprVars.searchPrice,
							value: '',
							placeholder: '1,2,3,4',
							multiline: true,
						},
						{
							type: 'listbox',
							name: 'open_now',
							label: wprVars.open_now,
							value: false,
							values	: [
								{text: 'TRUE', value: true},
								{text: 'FALSE', value: false},
							]
						},
						{
							type: 'textbox',
							name: 'attributes',
							label: wprVars.attributes,
							value: '',
							multiline: true,
							placeholder: 'hot_and_new, request_a_quote, reservation, waitlist_reservation, cashback, deals, gender_neutral_restrooms',
						},
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						var name = 'wp-review-yelp-search',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	};

	var getYelpBusinessButton = function( editor ) {
		return {
			text: wprVars.yelpBusiness,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.yelpBusiness,
					body: [
						{
							type: 'textbox',
							name: 'id',
							label: wprVars.businessId
						}
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						var name = 'wp-review-yelp-business',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	};

	var getYelpBusinessReviewsButton = function( editor ) {
		return {
			text: wprVars.yelpBusinessReviews,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.yelpBusinessReviews,
					body: [
						{
							type: 'textbox',
							name: 'id',
							label: wprVars.businessId
						},
						{
							type: 'textbox',
							name: 'title',
							label: wprVars.title
						},
						{
							type: 'listbox',
							name: 'review_num',
							label: wprVars.yelpReviewNum,
							values: [
								{ text: 1, value: 1 },
								{ text: 2, value: 2 },
								{ text: 3, value: 3 }
							],
							value: 3
						}
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						var name = 'wp-review-yelp-business-reviews',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	};

	var getGooglePlaceReviewsButton = function( editor ) {
		return {
			text: wprVars.googlePlaceReviews,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.googlePlaceReviews,
					body: [
						{
							type: 'selectbox',
							label: wprVars.placeType,
							classes: 'wpr-place-type',
							options: [ 'all', 'establishment', 'address', 'geocode' ]
						},
						{
							type: 'textbox',
							label: wprVars.locationLookup,
							classes: 'wpr-location-lookup'
						},
						{
							type: 'textbox',
							name: 'place_id',
							classes: 'wpr-place-id',
							hidden: true
						},
						{
							type: 'listbox',
							name: 'review_num',
							label: wprVars.googleReviewNum,
							values: [
								{ text: 1, value: 1 },
								{ text: 2, value: 2 },
								{ text: 3, value: 3 },
								{ text: 4, value: 4 },
								{ text: 5, value: 5 }
							],
							value: 5
						}
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function( e ) {
								dialog.submit();
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onOpen: function( e ) {
						var $inputs = $( '.mce-wpr-location-lookup' );
						$inputs.each( function( index, el ) {
							wpreview.locationLookup( $inputs[ index ], {
								container: '.mce-panel', // Container element.
								type: '.mce-wpr-place-type', // Place type element.
								placeId: '.mce-wpr-place-id' // Place ID element.
							});
						});
					},
					onsubmit: function( e ) {
						var name = 'wp-review-google-place-reviews',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	};

	var getFacebookReviewsButton = function( editor ) {
		return {
			text: wprVars.facebookReviews,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.facebookReviews,
					body: [
						{
							type: 'textbox',
							name: 'page_id',
							label: wprVars.pageId,
							classes: 'wpr-fb-page-id'
						},
						{
							type: 'textbox',
							name: 'limit',
							label: wprVars.limit,
							value: 5
						}
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function() {
								var $button, $container, $pageId, pageId;
								$button = this.$el;

								function showMessage( message, type ) {
									if ( ! type ) {
										type = 'error';
									}
									$button.closest( '.mce-foot' ).before( '<div class="mce-message ' + type + '">' + message + '</div>' );
								}

								function removeMessage() {
									$button.closest( '.mce-foot' ).prev( '.mce-message' ).remove();
								}

								function generateToken( pageId, accessToken, opts ) {
									wp.ajax.send( 'wp-review-generate-fb-page-token', {
										type: 'post',
										data: {
											page_id: pageId,
											user_token: accessToken,
											_wpnonce: wprVars.generateFBTokenNonce
										},
										error: function( response ) {
											if ( typeof opts.error === 'function' ) {
												opts.error( response );
											}
										},
										success: function( response ) {
											if ( typeof opts.success === 'function' ) {
												opts.success( response );
											}
										}
									});
								}

								$button.addClass( 'mce-disabled' );
								removeMessage();

								pageId = $( '.mce-wpr-fb-page-id' ).val();
								if ( ! pageId ) {
									showMessage( wprVars.emptyFBPageId );
									return;
								}

								if ( typeof FB == 'undefined' ) {
									showMessage( wprVars.fbIsNotLoaded );
									return;
								}

								function onLoginSuccess( data ) {
									generateToken( pageId, data.authResponse.accessToken, {
										success: function( response ) {
											// showMessage( response, 'success' );
											$button.removeClass( 'mce-disabled' );
											dialog.submit();
										},
										error: function( response ) {
											showMessage( response );
											$button.removeClass( 'mce-disabled' );
										},
									});
								}

								FB.getLoginStatus( function( response ) {
									if ( response.status === 'connected' ) {
										onLoginSuccess( response );
										return;
									}

									FB.login( function( response ) {
										if ( response.status !== 'connected' ) {
											console.log( 'Can not login' );
											return;
										}
										onLoginSuccess( response );
									}, { scope: 'manage_pages,pages_show_list' } );
								} );
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						var name = 'wp-review-facebook-reviews',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	};

	var getComparisonTableButton = function( editor ) {
		return {
			text: wprVars.comparisonTable,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.comparisonTable,
					body: [
						{
							type: 'textbox',
							name: 'ids',
							label: wprVars.reviewIds
						}
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						var name = 'wp-review-comparison-table',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	};

	function getPostsButton( editor ) {
		return {
			text: wprVars.reviewPosts,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.reviewPosts,
					body: [
						{
							type: 'listbox',
							name: 'text',
							label: wprVars.queryType,
							values: [
								{ text: wprVars.recentReviews, value: 'recent' },
								{ text: wprVars.topRated, value: 'toprated' },
								{ text: wprVars.mostVoted, value: 'mostvoted' },
								{ text: wprVars.categoryReviews, value: 'cat' }
							],
							value: 'recent'
						},
						{
							type: 'textbox',
							name: 'review_type',
							label: wprVars.reviewTypesText,
							tooltip: wprVars.separateByCommas
						},
						{
							type: 'textbox',
							name: 'cat',
							label: wprVars.categoryIds,
							tooltip: wprVars.separateByCommas
						},
						{
							type: 'checkbox',
							name: 'allow_pagination',
							label: wprVars.allowPagination
						},
						{
							type: 'textbox',
							name: 'post_num',
							label: wprVars.numberOfReviews,
							value: 5
						},
						{
							type: 'textbox',
							name: 'title_length',
							label: wprVars.titleLength
						},
						{
							type: 'checkbox',
							name: 'show_date',
							label: wprVars.showDate
						},
						{
							type: 'listbox',
							name: 'thumb_size',
							label: wprVars.thumbSize,
							values: [
								{ text: wprVars.small, value: 'small' },
								{ text: wprVars.large, value: 'large' },
							],
							value: 'small'
						}
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						var name = 'wp-review-posts',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	}

	function getWPReviewButton( editor ) {
		return {
			text: wprVars.reviewBox,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.reviewBox,
					body: [
						{
							type: 'textbox',
							name: 'id',
							label: wprVars.reviewId,
							tooltip: wprVars.leaveReviewIdEmpty
						}
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						var name = 'wp-review',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	}

	function getReviewTotalButton( editor ) {
		return {
			text: wprVars.reviewTotal,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.reviewTotal,
					body: [
						{
							type: 'textbox',
							name: 'id',
							label: wprVars.reviewId,
							tooltip: wprVars.leaveReviewIdEmpty
						}
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						var name = 'wp-review-total',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	}

	function getVisitorRatingButton( editor ) {
		return {
			text: wprVars.visitorRating,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.visitorRating,
					body: [
						{
							type: 'textbox',
							name: 'id',
							label: wprVars.reviewId,
							tooltip: wprVars.leaveReviewIdEmpty
						}
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						var name = 'wp-review-visitor-rating',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	}

	function getCommentsRatingButton( editor ) {
		return {
			text: wprVars.commentsRating,
			onclick: function() {
				var dialog = editor.windowManager.open({
					title: wprVars.commentsRating,
					body: [
						{
							type: 'textbox',
							name: 'id',
							label: wprVars.reviewId,
							tooltip: wprVars.leaveReviewIdEmpty
						}
					],
					buttons: [
						{
							id: 'wpr-insert-shortcode',
							classes: 'widget btn primary first abs-layout-item',
							text: wprVars.insert,
							onclick: function() {
								dialog.submit();
							}
						},
						{
							id: 'wpr-cancel-shortcode',
							text: wprVars.cancel,
							onclick: function() {
								dialog.close();
							}
						}
					],
					onsubmit: function( e ) {
						var name = 'wp-review-comments-rating',
							attrs = e.data;

						editor.insertContent( getShortcode( name, attrs ) );
					}
				});
			}
		};
	}

	tinymce.create( 'tinymce.plugins.WPReviewPro', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function( ed, url ) {
			ed.addButton( 'wpreviewpro', {
				type: 'menubutton',
				icon: 'dashicons dashicons-before dashicons-star-filled',
				menu: [
					getWPReviewButton( ed ),
					getPostsButton( ed ),
					getReviewTotalButton( ed ),
					getVisitorRatingButton( ed ),
					getCommentsRatingButton( ed ),
					getYelpSearchButton( ed ),
					getYelpBusinessButton( ed ),
					getYelpBusinessReviewsButton( ed ),
					getGooglePlaceReviewsButton( ed ),
					getFacebookReviewsButton( ed ),
					getComparisonTableButton( ed )
				]
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'WP Review Buttons',
				author : 'MTS',
				authorurl : 'https://mythemeshop.com',
				version : '3.0.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add( 'wp_review', tinymce.plugins.WPReviewPro );
})( tinymce, wpreview, jQuery );
