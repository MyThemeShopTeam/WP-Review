( function( Backbone, $ ) {
	"use strict";

	if ( ! $( '#wpr-review-items-app' ).length ) {
		return;
	}

	var App = {
		Models: {},
		Collections: {},
		Views: {}
	};

	App.Models.Item = Backbone.Model.extend({
		defaults: function() {
			return {
				id: '',
				item_id: '',
				wp_review_item_title: '',
				wp_review_item_star: 0,
				wp_review_item_color: $( '#wpr-review-color-value' ).val(),
				wp_review_item_inactive_color: $( '#wpr-review-inactive-color-value' ).val(),
				type: $( '#wpr-review-type-2' ).val() || wprVars.globalReviewType
			};
		}
	});

	App.Collections.Items = Backbone.Collection.extend({
		model: App.Models.Item
	});

	App.Views.ItemView = Backbone.View.extend({
		template: wp.template( 'wpr-review-item' ),

		className: 'wpr-review-item',

		initialize: function() {
			this.model.on( 'destroy', this.remove, this );
		},

		events: {
			'click .delete-item': 'remove',
			'change .input-title': 'changeTitle',
			'change .input-star': 'changeScore'
		},

		render: function() {
			var _this = this,
				data;

			data = this.model.toJSON();
			data.itemNameSetting = wp.template( 'wpr-review-item-name' )( data );
			data.itemColorSetting = wp.template( 'wpr-review-item-color' )( data );
			data.itemInactiveColorSetting = wp.template( 'wpr-review-item-inactive-color' )( data );
			if ( $( '#tmpl-wpr-review-item-' + data.type + '-rating' ).length ) {
				data.itemRatingSetting = wp.template( 'wpr-review-item-' + data.type + '-rating' )( data );
			} else {
				data.itemRatingSetting = wp.template( 'wpr-review-item-rating' )( data );
			}
			this.$el.html( this.template( data ) );

			this.$( '.input-color' ).wpColorPicker({
				change: function( ev, ui ) {
					var color = ui.color.toString();
					_this.$( '.wpr-star-input-wrapper' ).css({ color: color });
					_this.$( '.wpr-input-wrapper.ui-slider .ui-slider-range' ).css({ backgroundColor: color });
					_this.model.set( 'wp_review_item_color', color );
				}
			});

			this.$( '.input-inactive-color' ).wpColorPicker({
				change: function( ev, ui ) {
					var color = ui.color.toString();
					_this.$( '.wpr-star-input-wrapper .stars-bg' ).css({ color: color });
					_this.$( '.wpr-input-wrapper.ui-slider' ).css({ backgroundColor: color });
					_this.model.set( 'wp_review_item_inactive_color', color );
				}
			});

			function validateValue( value, min, max ) {
				value = parseFloat( value );
				if ( isNaN( value ) || min !== undefined && value < min || max !== undefined && value > max ) {
					_this.$el.find( '.input-star, .input-star-display' ).addClass( 'review-value-incorrect' );
				} else {
					_this.$el.find( '.input-star, .input-star-display' ).removeClass( 'review-value-incorrect' );
				}
			}

			switch ( this.model.get( 'type' ) ) {
				case 'star':
					var _this = this;

					this.$( '.input-star' ).wprStarInput({
						ready: function( wrapper, options ) {
							this.appendTo( this.closest( '.col-2' ).next() );
						},
						callback: function( value ) {
							validateValue( value, 0, wprVars.reviewTypes.star.max );
							_this.model.set( 'wp_review_item_star', value );
						}
					});
					break;

				case 'percentage':
					this.$( '.input-star' ).wprPercentageInput({
						ready: function( wrapper, options ) {
							this.appendTo( this.closest( '.col-2' ).next() );
						},
						callback: function( value ) {
							this.val( value );
							validateValue( value, 0, wprVars.reviewTypes.percentage.max );
							_this.model.set( 'wp_review_item_star', value );
						}
					});
					break;

				case 'point':
					this.$( '.input-star' ).wprPointInput({
						ready: function( wrapper, options ) {
							this.appendTo( this.closest( '.col-2' ).next() );
						},
						callback: function( value ) {
							this.val( value );
							validateValue( value, 0, wprVars.reviewTypes.point.max );
							_this.model.set( 'wp_review_item_star', value );
						}
					});
					break;
			}

			this.$el.attr( 'data-id', this.model.get( 'id' ) );
			return this;
		},

		remove: function() {
			collection.remove( this.model );
		},

		changeTitle: function( ev ) {
			this.model.set( 'wp_review_item_title', ev.target.value );
		},

		changeScore: function( ev ) {
			this.model.set( 'wp_review_item_star', parseFloat( ev.target.value ) );
		}
	});

	App.Views.AppView = Backbone.View.extend({
		el: '#wpr-review-items-app',

		initialize: function() {
			this.render();
			this.collection.on( 'add', this.renderOne, this );
			this.collection.on( 'remove', this.remove, this );
			this.collection.on( 'add', this.calculateTotal, this );
			this.collection.on( 'change', this.calculateTotal, this );
			this.collection.on( 'remove', this.calculateTotal, this );
			this.collection.on( 'add change remove', this.updateAppAttr, this );
		},

		events: {
			'click .add-item': 'addItem',
			'change #wpr-review-type-2': 'changeType'
		},

		render: function() {
			this.$( '.wpr-review-items' ).html( '' );
			this.collection.each( this.renderOne, this );
			this.calculateTotal();
			this.$( '.wpr-review-items' ).sortable({
				handle: '.wpr-icon-move'
			});
			return this;
		},

		renderOne: function( item ) {
			var view = new App.Views.ItemView({ model: item });
			this.$( '.wpr-review-items' ).append( view.render().el );
		},

		calculateTotal: function() {
			if ( ! this.$el.attr( 'data-changed' ) ) {
				return;
			}

			var total = 0,
				count = this.collection.length;

			if ( count ) {
				_.each( this.collection.models, function( item ) {
					total += ! isNaN( item.get( 'wp_review_item_star' ) ) ? parseFloat( item.get( 'wp_review_item_star' ) * 1 ) : 0;
				});
				total = Math.round( total * 100 / count ) / 100;
			}

			this.$( '.input-total' ).val( total );
		},

		remove: function( item ) {
			this.$el.find( '.wpr-review-item[data-id="' + item.get( 'id' ) + '"]' ).remove();
		},

		addItem: function() {
			var model = new App.Models.Item();
			model.set( 'id', 'a' + parseInt( Math.random() * 100 ) );
			this.collection.add( model );
		},

		changeType: function( ev ) {
			var type = ev.target.value;
			if ( type == 'none' ) {
				return;
			}
			_.each( this.collection.models, function( item ) {
				item.set( 'type', type );
			});
			this.render();
		},

		updateAppAttr: function() {
			this.$el.attr( 'data-changed', '1' );
		}
	});

	function onSelectType() {
		$( '#wpr-review-type-2' ).val( $( this ).val() ).trigger( 'change' );
	}
	$( '#wp_review_type' ).on( 'change', onSelectType );

	// App initialize.
	var items = $( '#wpr-review-items-data' ).val();
	items = items ? JSON.parse( items ) : [];
	items.map( function( item, index ) {
		item.item_id = item.id;
		item.id = index;
		return item;
	});

	var collection = new App.Collections.Items( items );

	var appView = new App.Views.AppView({
		collection: collection
	});
})( Backbone, jQuery );
