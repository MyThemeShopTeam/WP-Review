/* global RankMath */
;( function( $ ) {

	/**
	 * RankMath integration class.
	 */
	var RankMathIntegration = function() {
		this.init()
		this.hooks()
		setTimeout( this.events.bind( this ), 1500 )
	}

	/**
	 * Init the plugin
	 */
	RankMathIntegration.prototype.init = function() {
		this.pluginName = 'rank-math-review-analysis'
		this.fields     = {
			content: {
				'wp_review_desc': 'editor'
			},
			title: {
				'wp_review_desc_title' : 'text'
			}
		}
	}

	/**
	 * Hook into rank math app eco-system
	 */
	RankMathIntegration.prototype.hooks = function() {
		RankMathApp.registerPlugin( this.pluginName )
		wp.hooks.addFilter( 'rank_math_content', this.pluginName, $.proxy( this.reviewDescription, this ) )
		wp.hooks.addFilter( 'rank_math_title', this.pluginName, $.proxy( this.reviewTitle, this ) )
	}

	/**
	 * Capture events from plugins to refresh rank math analysis
	 */
	RankMathIntegration.prototype.events = function() {
		var self = this

		$.each( self.fields.content, function( key, value ) {
			self.bindEvent( key, value, self )
		})

		$.each( self.fields.title, function( key, value ) {
			self.bindEvent( key, value, self )
		})
	}

	RankMathIntegration.prototype.bindEvent = function( id, type, self ) {
		if ( 'editor' === type && undefined !== tinyMCE.editors[ id ] ) {
			tinyMCE.editors[ id ].on( 'keyup change', self.debounce( function() {
				RankMathApp.reloadPlugin( self.pluginName )
			}, 500 ) )
		} else {
			$( '#' + id ).on( 'change', self.debounce( function() {
				RankMathApp.reloadPlugin( self.pluginName )
			}, 500 ) )
		}
	}

	/**
	 * Analyze Review fields content
	 *
	 * @param {String} content System content.
	 *
	 * @return {String} Our plugin content concatenated
	 */
	RankMathIntegration.prototype.reviewDescription = function( content ) {
		return content + this.getContent()
	}

	/**
	 * Analyze Review fields title
	 *
	 * @param {String} title System title.
	 *
	 * @return {String} Our plugin title concatenated
	 */
	RankMathIntegration.prototype.reviewTitle = function( title ) {
		return title + this.getTitle()
	}

	/**
	 * Gather content from plugin fields for analysis.
	 *
	 * @return {String}
	 */
	RankMathIntegration.prototype.getContent = function() {
		var content = ''
		$.each( this.fields.content, function( key, value ) {
			content += 'editor' === value && undefined !== tinyMCE.editors[ key ] ? tinyMCE.editors[ key ].getContent() : $( '#' + key ).val()
		})

		return content
	}

	/**
	 * Gather title from plugin fields for analysis.
	 *
	 * @return {String}
	 */
	RankMathIntegration.prototype.getTitle = function() {
		var title = ''
		$.each( this.fields.title, function( key, value ) {
			title = '\n' + $( '#' + key ).val()
		})

		return title
	}

	/**
	 * Debounce function
	 *
	 * @param {Callback} func
	 * @param {Integer}  wait
	 * @param {Boolean}  immediate
	 */
	RankMathIntegration.prototype.debounce = function( func, wait, immediate ) {
		var timeout
		return function() {
			var context = this,
				args = arguments;

			var later = function() {
				timeout = null
				if ( ! immediate ) {
					func.apply( context, args )
				}
			}

			var callNow = immediate && ! timeout
			clearTimeout( timeout )
			timeout = setTimeout( later, wait )
			if ( callNow ) {
				func.apply( context, args )
			}
		}
	}

	$( document ).on( 'ready', function () {
		new RankMathIntegration()
	})

})( jQuery )
