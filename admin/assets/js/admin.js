/*
* Plugin Name: WP Review
* Plugin URI: http://mythemeshop.com/plugins/wp-review/
* Description: Create reviews! Choose from Stars, Percentages, Circles or Points for review scores. Supports Retina Display, WPMU & Unlimited Color Schemes.
* Author: MyThemesShop
* Author URI: http://mythemeshop.com/
*/
( function( $ ) {
	"use strict";

	var wpreview = window.wpreview = window.wpreview || {};

	wpreview.initSelect2 = function() {
		if ( 'function' !== typeof $.fn.select2 ) {
			return;
		}

		function addIcon( option ) {
			if ( $( option.element ).attr( 'data-icon' ) ) {
				return $( '<span><i class="' + $( option.element ).attr( 'data-icon' ) + '"></i> ' + option.text + '</span>' );
			}
			return option.text;
		}

		$( '.js-select2' ).select2({
			templateResult: addIcon,
			templateSelection: addIcon
		});
	};

	wpreview.tabs = function( options ) {
		var defaults = {
			wrapper: '[data-tabs]',
			title: '[data-tab-title]',
			content: '[data-tab-content]',
			activeElement: '',
			activeClass: 'active',
			active: '',
			activate: null
		};

		options = $.extend( {}, defaults, options );

		$( options.wrapper + ' ' + options.title ).on( 'click', function( ev ) {
			ev.preventDefault();
			var href, $tab;
			href = $( this ).attr( 'href' );
			$tab = $( this ).closest( options.wrapper );

			$tab.find( options.content ).hide();

			if ( ! options.activeElement || options.activeElement == options.title ) {
				$tab.find( options.title ).removeClass( options.activeClass );
				$( this ).addClass( options.activeClass );
			} else {
				$tab.find( options.activeElement ).removeClass( options.activeClass );
				$( this ).closest( options.activeElement ).addClass( options.activeClass );
			}

			if ( typeof options.activate == 'function' ) {
				options.activate.call( $( this ), href );
			}

			$tab.find( href ).fadeIn();
		});

		// Active a tab.
		if ( options.active ) {
			$( options.wrapper ).find( options.title + '[href="' + options.active + '"]' ).click();
		} else {
			$( options.wrapper ).find( options.title + ':eq(0)' ).click();
		}
	};

	wpreview.initTabs = function() {
		wpreview.tabs({
			wrapper: '.js-tabs',
			title: '.tab-title',
			content: '.tab-content',
			activeClass: 'nav-tab-active'
		});

		wpreview.tabs({
			wrapper: '[data-vertical-tabs]',
			activeElement: 'li',
			active: $( '#setting-error-settings_updated' ).length && 'undefined' !== typeof Cookies ? Cookies.get( 'wpr-last-vtab' ) : '',
			activate: function( tab ) {
				if ( 'undefined' === typeof Cookies ) {
					return;
				}
				Cookies.set( 'wpr-last-vtab', tab );
			}
		});

		wpreview.tabs({
			wrapper: '[data-nav-tabs]',
			title: '.nav-tab',
			content: '.tab-content',
			activeClass: 'nav-tab-active',
			active: $( '#setting-error-settings_updated' ).length && 'undefined' !== typeof Cookies ? Cookies.get( 'wpr-last-htab' ) : '',
			activate: function( tab ) {
				if ( ! $( '#wpr-global-options' ).length ) {
					return;
				}
				if ( 'undefined' === typeof Cookies ) {
					return;
				}
				Cookies.set( 'wpr-last-htab', tab );
			}
		});
	};

	wpreview.pluginOptions = function() {

		// Custom comment color.
		$( '#wp_review_custom_comment_colors' ).on( 'switch-on', function() {
			$('#wp_review_comment_color_wrapper').show();
		});
		$( '#wp_review_custom_comment_colors' ).on( 'switch-off', function() {
			$('#wp_review_comment_color_wrapper').hide();
		});
	};

	wpreview.pluginMetaBoxes = function() {

		// Custom location.
		$( '#wp_review_custom_location' ).on( 'switch-on', function() {
			$('.wp-review-location-options').show();
		});
		$( '#wp_review_custom_location' ).on( 'switch-off', function() {
			$('.wp-review-location-options').hide();
		});

		// Custom colors.
		$( '#wp_review_custom_colors' ).on( 'switch-on', function() {
			$('.wp-review-color-options').show();
		});
		$( '#wp_review_custom_colors' ).on( 'switch-off', function() {
			$('.wp-review-color-options').hide();
		});

		// Custom width.
		$( '#wp_review_custom_width' ).on( 'switch-on', function() {
			$('.wp-review-width-options').show();
		});
		$( '#wp_review_custom_width' ).on( 'switch-off', function() {
			$('.wp-review-width-options').hide();
		});

		// Custom author.
		$( '#wp_review_custom_author' ).on( 'switch-on', function() {
			$('.wp-review-author-options').show();
		});
		$( '#wp_review_custom_author' ).on( 'switch-off', function() {
			$('.wp-review-author-options').hide();
		});

		// Hide description.
		$( '#wp_review_hide_desc' ).on( 'switch-on', function() {
			$( '#wp_review_desc_settings' ).fadeOut();
		});
		$( '#wp_review_hide_desc' ).on( 'switch-off', function() {
			$( '#wp_review_desc_settings' ).fadeIn();
		});

		// Disable features.
		$( '#wp_review_disable_features' ).on( 'switch-on', function() {
			$( '#wpr-review-items-app' ).fadeOut();
		});
		$( '#wp_review_disable_features' ).on( 'switch-off', function() {
			$( '#wpr-review-items-app' ).fadeIn();
		});

		// User reviews.
		$( '#wp-review-userReview-disable' ).on( 'change', function() {
			var $postbox = $( this ).closest( '.postbox' );
			if ( $(this)[0].checked ) {
				$postbox.find( '.show-if-comment, .show-if-visitor, .show-if-both' ).hide();
				$postbox.find( '.show-if-disabled' ).show();
			}
		});
		$( '#wp-review-userReview-visitor' ).on( 'change', function() {
			var $postbox = $( this ).closest( '.postbox' );
			if ( $(this)[0].checked ) {
				$postbox.find( '.show-if-comment, .show-if-disabled' ).hide();
				$postbox.find( '.show-if-visitor, .show-if-both' ).show();
			}
		});
		$( '#wp-review-userReview-comment' ).on( 'change', function() {
			var $postbox = $( this ).closest( '.postbox' );
			if ( $(this)[0].checked ) {
				$postbox.find( '.show-if-visitor, .show-if-disabled' ).hide();
				$postbox.find( '.show-if-comment, .show-if-both' ).show();
			}
		});
		$( '#wp-review-userReview-both' ).on( 'change', function() {
			var $postbox = $( this ).closest( '.postbox' );
			if ( $(this)[0].checked ) {
				$postbox.find( '.show-if-disabled' ).hide();
				$postbox.find( '.show-if-comment, .show-if-visitor, .show-if-both' ).show();
			}
		});

		$( document ).on( 'change', '#wp_review_rating_schema', function() {
			var value = $( this ).val();
			if ( 'author' === value ) {
				$( '#wp_review_schema_author_wrapper' ).show();
			} else {
				$( '#wp_review_schema_author_wrapper' ).hide();
			}
		});

		$( document ).on( 'change', '#wp-review-userReview-disable', function() {
			if ( $( this ).prop( 'checked' ) ) {
				$( '#wp_review_rating_schema' ).val( 'author' ).trigger( 'change' );
			}
		});

		/**
		 * Toggle meta box
		 */
		$( '#wp-review-metabox-item' ).hide();
		$( '#wp-review-metabox-heading' ).hide();
		$( '#wp-review-metabox-desc' ).hide();
		$( '#wp-review-metabox-userReview' ).hide();
		$( '#wp_review_shortcode_hint_field' ).hide();
		$( '#wp_review_id_hint' ).hide();
		$( '#wp_review_heading_group' ).hide();
		$( '#wp-review-metabox-reviewLinks' ).hide();
		$( '#wp_review_schema_options_wrapper' ).hide();
		//$( '#wp_review_schema_rating_group' ).hide();

		$( '#wp_review_type' ).on( 'change', function() {
			var none = 'none';
			var show = false;
			var selected_val = $( this ).val();
			var type = wprVars.reviewTypes[ wprVars.globalReviewType ];
			var $selected_option = $( this ).find( 'option:selected' );
			show = selected_val ? selected_val != none : wprVars.globalReviewType;
			$( '#wp-review-metabox-item' ).toggle( show );
			$( '#wp-review-metabox-heading' ).toggle( show );
			$( '#wp-review-metabox-desc' ).toggle( show );
			$( '#wp-review-metabox-userReview' ).toggle( show );
			$( '#wp_review_id_hint' ).toggle( show );
			$( '#wp_review_schema_options_wrapper' ).toggle( show );
			$( '#wp-review-metabox-reviewLinks' ).toggle( show );
			$( '#wp_review_heading_group' ).toggle( show );
			$( '#wp_review_embed_options_wrapper' ).toggle( show );
			$( '#wp_review_show_schema_data_wrapper' ).toggle( show );

			if ( $( this ).attr( 'data-changed' ) != 1 ) {
				$( this ).attr( 'data-changed', 1 );
				return;
			}

			if ( ! $( this ).next( 'input[name="wp_review_type"]' ).length ) {
				$( this ).after( '<input type="hidden" name="wp_review_type" value="' + selected_val + '">' );
			} else {
				$( this ).next( 'input[name="wp_review_type"]' ).val( selected_val );
			}
		}).change();
		
	};

	wpreview.linkChoices = function( options ) {
		var defaults = {
			callback: null
		};

		options = $.extend( {}, defaults, options );

		$( '.wpr-link-choice' ).on( 'click', function( ev ) {
			ev.preventDefault();
			var target, value;
			target = $( this ).attr( 'data-target' );
			value = $( this ).attr( 'data-value' );
			if ( ! $( target ).length ) {
				return;
			}

			$( target ).val( value );
			$( this ).closest( '.wpr-link-choices' ).find( '.wpr-link-choice' ).removeClass( 'active' );
			$( this ).addClass( 'active' );

			if ( typeof options.callback == 'function' ) {
				options.callback.call( $( this ), value );
			}
		});
	};

	wpreview.boxTemplatesSelect = function() {
		if ( 'function' !== typeof $.fn.select2 ) {
			return;
		}

		var $select = $( 'select#wp_review_box_template' ),
			globalColor = $( '#wpr-review-global-color-value' ).val(),
			globalInactiveColor = $( '#wpr-review-global-inactive-color-value' ).val(),
			postColor, postInactiveColor;

		function onSwitchColor( color ) {
			$( '.wpr-review-item .input-color' ).each( function() {
				var oldVal;
				oldVal = $( this ).val();
				if ( oldVal === globalColor || oldVal === postColor ) {
					$( this ).iris( 'color', color );
				}
			});
		}

		function onSwitchInactiveColor( color ) {
			$( '.wpr-review-item .input-inactive-color' ).each( function() {
				var oldVal;
				oldVal = $( this ).val();
				if ( oldVal === globalInactiveColor || oldVal === postInactiveColor ) {
					$( this ).iris( 'color', color );
				}
			});
		}

		function onChange( value ) {
			var templates = wprVars.boxTemplates,
				template = templates[ value ] || templates['default'];

			postColor = $( '#wp_review_color' ).val();
			postInactiveColor = $( '#wp_review_inactive_color' ).val();

			// Change image preview.
			$( '#wp_review_box_template_img' ).attr( 'src', template.image );

			// Change style options.
			$( '#wp_review_color' ).iris( 'color', template.color );
			$( '#wp_review_fontcolor' ).iris( 'color', template.fontcolor );
			$( '#wp_review_bgcolor1' ).iris( 'color', template.bgcolor1 );
			$( '#wp_review_bgcolor2' ).iris( 'color', template.bgcolor2 );
			$( '#wp_review_bordercolor' ).iris( 'color', template.bordercolor );
			wpreview.turnSwitch( $( '#wp_review_custom_width' ), template.width != 100 );
			$( '#wp_review_width' ).val( template.width ).trigger( 'change' );
			$( '#wp-review-align-' + template.align ).prop( 'checked', true );
			wpreview.turnSwitch( $( '#wp_review_custom_comment_colors' ), template.custom_comment_colors );
			$( '#wp_review_comment_color' ).iris( 'color', template.comment_color );
			$( '#wp_review_rating_icon' ).val( template.rating_icon ).trigger( 'change' );
			$( '#wp_review_inactive_color' ).iris( 'color', template.inactive_color );
			$( '#wp_review_comment_inactive_color' ).iris( 'color', template.comment_inactive_color );
			$( '#wpr-review-color-value' ).val( template.color );
			$( '#wpr-review-inactive-color-value' ).val( template.inactive_color );

			// Feature colors.
			onSwitchColor( template.color );
			onSwitchInactiveColor( template.inactive_color );
		}

		$( '#wp_review_custom_colors' ).on( 'switch-on', function() {
			postColor = $( '#wp_review_color' ).val();
			postInactiveColor = $( '#wp_review_inactive_color' ).val();

			onSwitchColor( $( '#wp_review_color' ).val() );
			onSwitchInactiveColor( $( '#wp_review_inactive_color' ).val() );
		});
		$( '#wp_review_custom_colors' ).on( 'switch-off', function() {
			postColor = $( '#wp_review_color' ).val();
			postInactiveColor = $( '#wp_review_inactive_color' ).val();

			onSwitchColor( $( '#wpr-review-global-color-value' ).val() );
			onSwitchInactiveColor( $( '#wpr-review-global-inactive-color-value' ).val() );
		});

		$( '#wp_review_color' ).on( 'color-change', function( ev, colorEvent, ui ) {
			postColor = $( '#wp_review_color' ).val();
			onSwitchColor( ui.color.toString() );
		});

		$( '#wp_review_inactive_color' ).on( 'color-change', function( ev, colorEvent, ui ) {
			postInactiveColor = $( '#wp_review_inactive_color' ).val();
			onSwitchInactiveColor( ui.color.toString() );
		});

		// Init select2.
		$select.select2({
			width: '250px',
			templateResult: function( option ) {
				if ( ! option.element ) {
					return option.text;
				}
				var value = option.element.value,
					templates = wprVars.boxTemplates,
					template = templates[ value ] || templates['default'];
				return $( '<span data-img="' + template.image + '">' + option.text + '</span>' );
			}
		});

		// On change option.
		$select.on( 'change', function( ev ) {
			onChange( ev.target.value );
		});

		// On hover option.
		$( document ).on( 'mouseenter', '#select2-wp_review_box_template-results li', function() {
			$( '#wp_review_box_template_preview' ).addClass( 'loading' ).show();
			$( '#wp_review_box_template_preview_img' ).attr( 'src', $( this ).find( 'span' ).attr( 'data-img' ) );
			$( '#wp_review_box_template_preview_img' ).imagesLoaded().progress( function( instance, image ) {
				if ( image.isLoaded ) {
					$( '#wp_review_box_template_preview' ).removeClass( 'loading' );
				}
			});
		});

		$select.on( 'select2:close', function() {
			$( '#wp_review_box_template_preview' ).hide();
		});
	};

	wpreview.formSwitchEvents = function() {
		$( document ).on( 'change', '.wpr-switch__on', function() {
			if ( $( this )[0].checked ) {
				$( this ).closest( '.wpr-switch' ).trigger( 'switch-on' );
			}
		});

		$( document ).on( 'change', '.wpr-switch__off', function() {
			if ( $( this )[0].checked ) {
				$( this ).closest( '.wpr-switch' ).trigger( 'switch-off' );
			}
		});
	};

	wpreview.turnSwitch = function( $switch, on ) {
		if ( on ) {
			$switch.find( '.wpr-switch__on' ).prop( 'checked', true ).trigger( 'change' );
		} else {
			$switch.find( '.wpr-switch__off' ).prop( 'checked', true ).trigger( 'change' );
		}
	};

	wpreview.importDemo = function() {
		$( '#wp-review-import-demo-button' ).on( 'click', function() {
			var check = confirm( wprVars.importDemoConfirm ),
				$button = $( this );
			if ( ! check ) {
				return;
			}

			$button.prop( 'disabled', true );

			$.magnificPopup.open({
				items: {
					src: '#wp-review-demo-importer-popup',
					type: 'inline'
				},
				modal: true
			});

			$( '#wp-review-demo-importer-modal-footer-button' ).on( 'click', function() {
				$.magnificPopup.close();
			});

			var data = {
				action: 'wp-review-import-demo',
				nonce: $button.attr( 'data-nonce' )
			};
			$.post( ajaxurl, data, function( response ) {
				$( '#wp-review-demo-importer-modal-content' ).html( response );
				$( '#wp-review-demo-importer-modal-footer-info' ).text( wprVars.importDemoDone );
				$( '#wp-review-demo-importer-modal-header h2' ).text( wprVars.importDemoDone );
				$( '#wp-review-demo-importer-modal-footer-button' ).show();
				$button.prop( 'disabled', false );
			});
		});
	};

	$( document ).ready( function() {
		wpreview.initSelect2();
		wpreview.initTabs();
		wpreview.pluginOptions();
		wpreview.pluginMetaBoxes();
		wpreview.boxTemplatesSelect();
		wpreview.formSwitchEvents();
		wpreview.importDemo();

		$( '[data-remove-ratings]' ).on( 'click', function() {
			var check = confirm( wprVars.confirmPurgeRatings );
			if ( ! check ) {
				return;
			}

			var $button = $( this ),
				options = $button.data(),
				btnText = $button.text();

			$button.text( options.processingText );
			options.action = 'wpr-purge-ratings';
			options.nonce = wprVars.purgeRatingsNonce;

			$.ajax({
				url: ajaxurl,
				type: 'post',
				data: options,
				success: function( res ) {
					$button.text( res.data );
					setTimeout( function() {
						$button.text( btnText );
					}, 2000 );
				},
				error: function( res ) {
					console.log( res );
				}
			})
		});

		// Fix conflicts with Blogging theme. See https://github.com/MyThemeShopTeam/wp-review-pro/issues/277.
		$( document ).off( 'mousewheel', '**' );
		$( document ).on( 'DOMMouseScroll mousewheel', '.select2-results', function( ev ) {
			if ( $( this ).children( '#select2-wp_review_box_template-results' ).length ) {
				return;
			}
			if ( $( this ).children( '#select2-wp_review_popup_animation_in-results' ).length ) {
				return;
			}
			if ( $( this ).children( '#select2-wp_review_popup_animation_out-results' ).length ) {
				return;
			}
			var $this = $( this ),
				scrollTop = this.scrollTop,
				scrollHeight = this.scrollHeight,
				height = $this.height(),
				delta = ev.type == 'DOMMouseScroll' ? ev.originalEvent.detail * -40 : ev.originalEvent.wheelDelta,
				up = delta > 0;

			var prevent = function() {
				ev.stopPropagation();
				ev.preventDefault();
				ev.returnValue = false;
				return false;
			};

			if ( ! up && -delta > scrollHeight - height - scrollTop ) {
				// Scrolling down, but this will take us past the bottom.
				$this.scrollTop( scrollHeight );
				return prevent();
			} else if ( up && delta > scrollTop ) {
				// Scrolling up, but this will take us past the top.
				$this.scrollTop(0);
				return prevent();
			}
		});

		function showProPopup() {
			$.magnificPopup.open({
				items: {
					src: '#wp-review-pro-popup-notice',
					type: 'inline'
				}
			});
		}

		$( document ).on( 'click', '#select2-wp_review_box_template-results li[aria-disabled="true"]', function() {
			$( '#wp_review_box_template' ).select2( 'close' );
			showProPopup();
		});

		$( document ).on( 'click', '#select2-wp_review_rating_icon-results li[aria-disabled="true"]', function() {
			$( '#wp_review_rating_icon' ).select2( 'close' );
			showProPopup();
		});

		// Pro feature popup.
		$( document ).on( 'click', '.wp-review-disabled, option[disabled]', function( ev ) {
			ev.preventDefault();
			showProPopup();
		});

		$( 'select:not(.select2-hidden-accessible)' ).each( function() {
			$( this ).attr( 'data-old-val', $( this ).val() );
		});

		$( document ).on( 'change', 'select:not(.select2-hidden-accessible)', function() {
			var selectedIndex  = $( this ).prop( 'selectedIndex' ),
				selectedOption = $( this ).find( 'option:eq(' + selectedIndex + ')' ),
				oldVal         = $( this ).attr( 'data-old-val' );
			if ( selectedOption.hasClass( 'disabled' ) ) {
				$( this ).val( oldVal );
				showProPopup();
			} else {
				$( this ).attr( 'data-old-val', $( this ).val() );
			}
		});

		// Fix conflict with color picker in Avada theme.
		if ( $( '.pyre_field.avada-color' ).length ) {
			$( '.wp-review-color' ).closest( '.wp-review-field-option' ).addClass( 'pyre_field' );
			$( '.input-color, .input-inactive-color' ).closest( '.col-2' ).addClass( 'pyre_field' );
		}
	});

	$( window ).load( function() {
		// WYSIWYG saving issue when using Gutenberg.
		if ( $( 'body.block-editor-page' ).length ) {
			window.tinyMCE.editors.forEach( function( editor ) {
				editor.on( 'change', function() {
					editor.save();
				});
			});
		}

		$( '#wp_review_type' ).trigger( 'change' );
	});
})( jQuery );

jQuery(document).ready(function($) {

	/**
	 * Repeatable field
	 */
	$('.add-row').on('click', function(e) {
		e.preventDefault();
		var $target = $($(this).data('target'));
		var row = $target.find('.empty-row').clone(true);
		var input = row.find('input');
		if (typeof input.data('name') !== 'undefined' && input.data('name')) input.prop('name', input.data('name'));
		input.filter('[name="wp_review_item_star[]"]').addClass('review-star');
		row.removeClass('empty-row screen-reader-text');
		row.insertBefore($target.find('tbody>tr:last'));
		row.find(".focus-on-add").focus();
	});

	$('.remove-row').on('click', function(e) {
		e.preventDefault();
		$(this).closest('tr').remove();
	});

	$('#wp-review-metabox-item tbody').sortable({ handle: '.handle', revert: 100, containment: '#wp-review-metabox-item' });
	$('#wp-review-metabox-reviewLinks tbody').sortable({ handle: '.handle', revert: 100, containment: '#wp-review-metabox-reviewLinks' });

	// $('#wp-wp_review_desc-wrap').toggle(! $('#wp_review_hide_desc').is(':checked'));
	$('#wp_review_location').on('change', function() {
		$('#wp_review_shortcode_hint_field').toggle($(this).val() == 'custom');
	});

	if ($('#wp_review_location').val() == 'custom') {
		$('#wp_review_shortcode_hint_field').show();
	}

	$( '#wp-review-width-slider' ).slider({
		range: 'min',
		value: $( '#wp_review_width' ).val(),
		step: 1,
		min: 1,
		max: 100,
		disabled: true
	});

	$('.wp-review-userReview-options').change(function(event) {
		$('#wp-review-through-comment-option').toggle(!!parseInt($(this).val()));
	});

	/**
	 * Color picker setup
	 */
	$('.wp-review-color').wpColorPicker({
		change: function( event, ui ) {
			$( event.target ).trigger( 'color-change', [ event, ui ] );
		}
	});

	$('.wp-review-theme-defaults-msg .close-notice').click(function() {
		$('.wp-review-theme-defaults-msg').remove();
	});
	$('.wp-review-theme-defaults-msg a.button').click(function() {
		return confirm('Are you sure? This may override the current settings.');
	});

	$('[name=wp_review_userReview]').change(function(e){
		var val = $(this).val();
		var $type = $('#wp_review_comment_rating_type');
		if(wprVars.ratingPermissionsCommentOnly === val || wprVars.ratingPermissionsBoth === val){
			$type.show();
		} else {
			$type.hide();
		}
	});

	$('td.wp_review_comment_rating, #wp-review-comment-rating').each(function(){
		var $features = $(this).find('.wp-review-rating-feature');
		var maxWidth = 0;
		$features.each(function(){
			var width = $(this).outerWidth();
			if ( width > maxWidth ) maxWidth = width;
		});
		$features.width( maxWidth + 10 );
	});

	if ($('#wp-review-migrate-log').length) {
		var $migrate_log = $('#wp-review-migrate-log');
		var migrate_started = false;
		var rows_left = parseInt($('#migrate-items-num').text());
		var migrated_rows = $('#start-migrate').data('start');
		var migrate_finished = false;
		var updatelog = function( text ) {
			$migrate_log.css('display', 'block').val(function(index, old) { return text + "\n" + old });
		}
		var ajax_migrate = function( startindex ) {
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: { action: 'wp_review_migrate_ratings', start: startindex },
			})
			.done(function( data ) {
				$('#migrate-items-num').text(data.rowsleft);
				updatelog( 'Imported ratings: ' + (startindex + 1) + ' - ' + data.lastrow + '...' );
				if ( ! data.finished )
					ajax_migrate( data.lastrow );
				else
					updatelog('Import complete.');
			});

		}
		$('#start-migrate').click(function(event) {
			event.preventDefault();
			if (migrate_started)
				return false;

			migrate_started = true;
			updatelog('Import started, please wait...');

			ajax_migrate(migrated_rows);
		});

	}

	// Image Uploader
	wprImageField = {
		uploader : function( id ) {
			var frame = wp.media({
				title : wprVars.imgframe_title,
				multiple : false,
				frame:    'post',
				library : { type : 'image' },
				button : { text : wprVars.imgbutton_title }
			});

			frame.on( 'insert', function(selection) {

				var state = frame.state();
			selection = selection || state.get('selection');
			if (! selection) return;
			// We set multiple to false so only get one image from the uploader
			var attachment = selection.first();
			var display = state.display(attachment).toJSON();  // <-- additional properties
			attachment = attachment.toJSON();
			// Do something with attachment.id and/or attachment.url here
			var imgurl = attachment.sizes[display.size].url;
				var attachments = frame.state().get( 'selection' ).toJSON();
				if ( attachments[0] ) {
					$( '#' + id + '-preview' ).html( '<img src="' + imgurl + '" class="wpr_image_upload_img" />' );
					$( '#' + id + '-id' ).val( attachments[0].id );
					$( '#' + id + '-url' ).val( imgurl );

					if ( $( '#' + id + '-upload+.clear-image' ).length == 0 ) {
						$( '#' + id + '-upload' ).after( '<a href="#" class="button button-link clear-image">' + wprVars.imgremove_title + '</a>' );
					}
				}
			});

			frame.open();
			return false;
		}
	};

	$( document ).on( 'click', '.clear-image', function( e ) {
		e.preventDefault();
		var $this = $( this ),
			id = $this.prev().data( 'id' );

		$( '#' + id + '-preview' ).html( '' );
		$( '#' + id + '-id' ).val( '' );
		$( '#' + id + '-url' ).val( '' );
		$this.remove();
	});

	$( '.wpr-datepicker' ).datepicker({
		dateFormat: 'yy-mm-dd',
	});

	if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
		$(document).on('click', '.wp-review-rating-image .set_rating_image, .wp-review-rating-image .img-wrapper i', function(e) {
			e.preventDefault();
			var button = $('.wp-review-rating-image .set_rating_image'),
			custom_uploader = wp.media({
				title: 'Insert image',
				library : {
					type : 'image'
				},
				button: {
					text: 'Use this image'
				},
				multiple: false
			}).on('select', function() {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				$(button).parent().find('.img-wrapper').removeClass('hide').find('img').attr('src', attachment.url);
				$(button).removeClass('button').prev().val(attachment.id).next().show();
				$(button).hide();
			}).open();
		});
	}

	if($(document).find('#multisite_settings').length > 0) {
		$(document).on('change', '#wp-review-select-site', function(){
			var site = $(this).val();
			$('.wp-review-subsite-wrapper').hide();
			$(document).find('#wp-review-site-'+site+'-fields').show();
		});

		// Multisite general settings.
		$( '.wp-review-multisite-general-settings div.wpr-switch' ).on( 'switch-on', function() {
			$('.wp-review-multisite-global-options').fadeOut();
		});
		$( '.wp-review-multisite-general-settings div.wpr-switch' ).on( 'switch-off', function() {
			$('.wp-review-multisite-global-options').fadeIn();
		});

		// Multisite post settings.
		$( '.wp-review-multisite-posts-options div.wpr-switch' ).on( 'switch-on', function() {
			$(this).parents('.wp-review-multisite-posts-options').next('#wp-review-multisite-posts-options').fadeOut();
		});
		$( '.wp-review-multisite-posts-options div.wpr-switch' ).on( 'switch-off', function() {
			$(this).parents('.wp-review-multisite-posts-options').next('#wp-review-multisite-posts-options').fadeIn();
		});
	}

});
