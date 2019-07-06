/* global RankMathApp */
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
	RankMathApp.registerPlugin( this.pluginName, { status: 'ready' } );
	wp.hooks.addFilter( 'rank_math_content', this.pluginName, $.proxy( this.reviewDescription, this ) );
  wp.hooks.addFilter( 'rank_math_title', this.pluginName, $.proxy( this.reviewTitle, this ) );
	// RankMathApp.addFilter( 'rank_math_title', this.pluginName, $.proxy( this.reviewTitle, this ) );
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
  console.log('filter called from WP Review plugin...')
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
    		RankMathApp.pluginReloaded( self.pluginName );
			});
		}
    $( '#' + key ).on( 'change', function() {
      console.log('this is changing...')
    	RankMathApp.pluginReloaded( self.pluginName );
    });
  });

};
 $( document ).on( 'ready', function () {
  setTimeout(function(){
    new rankMathCompatible();
  }, 1500);
});