/* global RankMath */
$( window ).on( 'load', function() {
	var rankMathCompatible = function() {
		this.init();
		this.hooks();
		this.events( this );
	}

	rankMathCompatible.prototype.init = function() {
    this.pluginName = 'rank-math-review-analysis';
    this.fields = {
      content: {
        'wp_review_desc' : 'editor'
      },
      'title' : {
        'wp_review_desc_title' : 'text',
      }
    };
  }

  rankMathCompatible.prototype.hooks = function() {
  	RankMath.app.registerPlugin( this.pluginName, { status: 'ready' } );
		RankMath.app.addFilter( 'content', $.proxy( this.reviewDescription, this ), this.pluginName );
		RankMath.app.addFilter( 'title', $.proxy( this.reviewTitle, this ), this.pluginName );
  }

  rankMathCompatible.prototype.getContent = function() {
    var content = '';
    $.each( this.fields.content, function( key, value ) {
      content += 'editor' === value && undefined !== tinyMCE.editors[key] ? tinyMCE.editors[key].getContent() : $( '#' + key ).val();
    });
    return content;
  }

  rankMathCompatible.prototype.getTitle = function() {
    var title = '';
    $.each( this.fields.title, function( key, value ) {
      title = '\n' + $( '#' + key ).val();
    });
    return title;
  }

	// Analyze Review fields content.
	rankMathCompatible.prototype.reviewDescription = function( content ) {
		return content + this.getContent();
	};

	// Analyze Review fields title.
	rankMathCompatible.prototype.reviewTitle = function( title ) {
		return title + this.getTitle();
	};

	rankMathCompatible.prototype.events = function( self ) {
		$.each( self.fields.content, function( key, value ) {
			if ( 'editor' === value && undefined !== tinyMCE.editors[key] ) {
				tinyMCE.editors[key].on( 'change', function() {
      		RankMath.app.pluginReloaded( self.pluginName );
				});
			}
      $( '#' + key ).on( 'change', function() {
      	RankMath.app.pluginReloaded( self.pluginName );
      });
    });

	};

	new rankMathCompatible();
});