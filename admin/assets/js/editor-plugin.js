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
					getReviewTotalButton( ed ),
					getVisitorRatingButton( ed )
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
