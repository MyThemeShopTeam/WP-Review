/**
 * Rank Math SEO Integration
 * Analyze content of 'Review Headline' & 'Description Content' fields
 */
;( function( $ ) {

	/**
	 * RankMath integration class
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
			title: {
				'wp_review_heading' : 'text'
			},
			content: {
				'wp_review_desc': 'editor'
			}
		}
	}

	/**
	 * Hook into Rank Math App eco-system
	 */
	RankMathIntegration.prototype.hooks = function() {
		RankMathApp.registerPlugin( this.pluginName )
		wp.hooks.addFilter( 'rank_math_title', this.pluginName, $.proxy( this.reviewTitle, this ) )
		wp.hooks.addFilter( 'rank_math_content', this.pluginName, $.proxy( this.reviewDescription, this ) )
	}

	/**
	 * Capture events from plugins to refresh Rank Math analysis
	 */
	RankMathIntegration.prototype.events = function() {
		var self = this

		$.each( self.fields.title, function( key, value ) {
			self.bindEvent( key, value, self )
		})

		$.each( self.fields.content, function( key, value ) {
			self.bindEvent( key, value, self )
		})
	}

	RankMathIntegration.prototype.bindEvent = function( id, type, self ) {

		tinymce.on( 'AddEditor', function ( event ) {
			if ( id === event.editor.id ) {
				event.editor.on( 'keyup change', self.debounce( function() {
					RankMathApp.reloadPlugin( self.pluginName )
				}, 500 ) )
			}
		} )
		if ( 'editor' === type && undefined !== tinyMCE.editors[ id ] ) {
			tinyMCE.editors[ id ].on( 'keyup change', self.debounce( function() {
				RankMathApp.reloadPlugin( self.pluginName )
			}, 500 ) )
		}

		$( '#' + id ).on( 'input change', self.debounce( function() {
			RankMathApp.reloadPlugin( self.pluginName )
		}, 500 ) )
	}

	/**
	 * Gather 'Review Heading' field data for analysis
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
	 * Gather 'Description Content' field data for analysis
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
	 * Analyze 'Review Heading' field
	 *
	 * @param {String} title System title.
	 *
	 * @return {String} Our plugin title concatenated
	 */
	RankMathIntegration.prototype.reviewTitle = function( title ) {
		return title + this.getTitle()
	}

	/**
	 * Analyze 'Description Content' field
	 *
	 * @param {String} content System content.
	 *
	 * @return {String} Our plugin content concatenated
	 */
	RankMathIntegration.prototype.reviewDescription = function( content ) {
		return content + this.getContent()
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

	/**
	 * Start Analysing Custom Fields.
	 */
	$( document ).on( 'ready', function () {
		new RankMathIntegration()
	})

})( jQuery )
