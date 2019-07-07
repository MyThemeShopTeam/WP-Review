/* global RankMath */
;( function( $ ) {

	/**
	 * RankMath integration class.
	 */
	var RankMathIntegration = function() {
		this.init()
		this.hooks()
		this.events()
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
		RankMathApp.registerPlugin( this.pluginName, { status: 'ready' } )
		RankMathApp.addFilter( 'content', $.proxy( this.reviewDescription, this ), this.pluginName )
		RankMathApp.addFilter( 'title', $.proxy( this.reviewTitle, this ), this.pluginName )
	}

	/**
	 * Capture events from plugins to refresh rank math analysis
	 */
	RankMathIntegration.prototype.events = function() {
		var self = this

		$.each( self.fields.content, function( key, value ) {
			if ( 'editor' === value && undefined !== tinyMCE.editors[ key ] ) {
				tinyMCE.editors[ key ].on( 'change', function() {
					RankMathApp.pluginReloaded( self.pluginName )
				})
			} else {
				$( '#' + key ).on( 'change', function() {
					RankMathApp.pluginReloaded( self.pluginName )
				})
			}
		})
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

	$( window ).on( 'load', function() {
		new RankMathIntegration()
	})

})( jQuery )
