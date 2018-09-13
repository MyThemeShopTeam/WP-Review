( function( $ ) {
	"use strict";

	var defaults = {
		value: 0,
		color: '',
		callback: null
	};

	function triggerCallback( options, value ) {
		if ( typeof options.callback == 'function' ) {
			options.callback.call( options._this, value );
		}
	}

	// Star input.
	$.fn.wprStarInput = function( method ) {
		var methods = {
			init: function( options ) {
				var _this = this, wrapper, html, starHtml, width;
				options = $.extend( {}, defaults, this.data(), options );
				if ( this.val() ) {
					options.value = this.val() ? parseFloat( this.val() ) : 0;
				}
				options._this = this;

				width = parseFloat( options.value ) * 20;

				starHtml = '';
				for ( var i = 0; i < 5; i++ ) {
					starHtml += '<span class="star-icon dashicons dashicons-star-filled"></span>';
				}

				html = '<div class="wpr-star-input-wrapper wpr-input-wrapper" data-value="' + options.value + '" data-width="' + width + '" style="color: ' + options.color + '">\
							<div class="stars-bg" style="color: ' + options.inactiveColor + '">' + starHtml + '</div>\
							<div class="stars-result" style="width: ' + width + '%">' + starHtml + '</div>\
						</div>';

				wrapper = $( html );
				this.before( wrapper );

				if ( typeof options.ready == 'function' ) {
					options.ready.call( this, wrapper, options )
				}

				wrapper.on( 'mousemove', function( ev ) {
					var newWidth;
					newWidth = ev.pageX - $( this ).offset().left;
					newWidth = newWidth / $( this ).width() * 100;
					newWidth = Math.ceil( newWidth / 10 ) * 10;
					$( this ).attr( 'data-value', newWidth / 20 ); // width / 100 * 5
					$( this ).find( '.stars-result' ).css( 'width', newWidth + '%' );
					$( this ).attr( 'data-width', newWidth );
				}).on( 'mouseleave', function() {
					// Reset to old width.
					$( this ).find( '.stars-result' ).css( 'width', width + '%' );
					$( this ).attr( 'data-width', width );
					$( this ).attr( 'data-value', options.value );
				}).on( 'click', function() {
					width = $( this ).attr( 'data-width' );
					options.value = parseFloat( $( this ).attr( 'data-value' ) );
					_this.val( options.value );
					triggerCallback( options, options.value );
				});

				wrapper.css({ color: options.color });

				this.on( 'change', function() {
					var value = $( this ).val() ? parseFloat( $( this ).val() ) : 0;
					wrapper.attr( 'data-value', value );
					wrapper.attr( 'data-width', value * 20 );
					wrapper.find( '.stars-result' ).css( 'width', ( value * 20 ) + '%' );
					triggerCallback( options, value );
				});
			}
		};

		if ( ! method || typeof method == 'object'  ) {
			methods.init.apply( this, arguments );
			return;
		}
		if ( methods[ method ] ) {
			methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
			return;
		}
	};

	// Percentage input.
	$.fn.wprPercentageInput = function( method ) {
		var methods = {
			init: function( options ) {
				var _this = this, wrapper;
				options = $.extend( {}, defaults, this.data(), options );
				if ( this.val() ) {
					options.value = this.val();
				}
				options._this = this;

				if ( ! options.inactiveColor ) {
					options.inactiveColor = '#ccc';
				}

				wrapper = $( '<div class="wpr-percentage-input-wrapper wpr-input-wrapper"></div>' );
				this.before( wrapper );

				if ( typeof options.ready == 'function' ) {
					options.ready.call( this, wrapper, options )
				}

				wrapper.slider({
					min: 0,
					max: 100,
					step: 1,
					range: 'min',
					value: options.value,
					create: function( ev, ui ) {
						wrapper.css( 'backgroundColor', options.inactiveColor );
						wrapper.find( '.ui-slider-range' ).css( 'backgroundColor', options.color );
					},
					slide: function( ev, ui ) {
						triggerCallback( options, ui.value );
					}
				});

				this.on( 'change', function() {
					var value = $( this ).val();
					wrapper.slider( 'value', value );
					triggerCallback( options, value );
				});
			}
		};

		if ( ! method || typeof method == 'object'  ) {
			methods.init.apply( this, arguments );
			return;
		}
		if ( methods[ method ] ) {
			methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
			return;
		}
	};

	// Point input.
	$.fn.wprPointInput = function( method ) {
		var methods = {
			init: function( options ) {
				var _this = this, wrapper;
				options = $.extend( {}, defaults, this.data(), options );
				if ( this.val() ) {
					options.value = this.val();
				}
				options._this = this;

				if ( ! options.inactiveColor ) {
					options.inactiveColor = '#ccc';
				}

				wrapper = $( '<div class="wpr-percentage-input-wrapper wpr-input-wrapper"></div>' );
				this.before( wrapper );

				if ( typeof options.ready == 'function' ) {
					options.ready.call( this, wrapper, options )
				}

				wrapper.slider({
					min: 0,
					max: 10,
					step: 0.1,
					range: 'min',
					value: options.value,
					create: function( ev, ui ) {
						wrapper.css( 'backgroundColor', options.inactiveColor );
						wrapper.find( '.ui-slider-range' ).css( 'backgroundColor', options.color );
					},
					slide: function( ev, ui ) {
						triggerCallback( options, ui.value );
					}
				});

				this.on( 'change', function() {
					var value = $( this ).val();
					wrapper.slider( 'value', value );
					triggerCallback( options, value );
				});
			}
		};

		if ( ! method || typeof method == 'object'  ) {
			methods.init.apply( this, arguments );
			return;
		}
		if ( methods[ method ] ) {
			methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
			return;
		}
	};

})( jQuery );
