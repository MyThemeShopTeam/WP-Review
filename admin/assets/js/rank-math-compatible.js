/* global RankMathApp */
var rankMathCompatible = function() {
	this.init();
	this.hooks();
  setTimeout( this.events.bind( this ), 1500 )
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
	RankMathApp.registerPlugin( this.pluginName );
	wp.hooks.addFilter( 'rank_math_content', this.pluginName, $.proxy( this.reviewDescription, this ) );
  wp.hooks.addFilter( 'rank_math_title', this.pluginName, $.proxy( this.reviewTitle, this ) );
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

rankMathCompatible.prototype.events = function() {
  var self = this
	$.each( this.fields.content, function( key, value ) {
		if ( 'editor' === value && undefined !== tinyMCE.editors[key] ) {
      tinyMCE.editors[key].on( 'keyup change', self.debounce( ( event ) => {
        RankMathApp.reloadPlugin( self.pluginName );
      }, 500 ));
		}
    $( '#' + key ).on( 'change', function() {
    	RankMathApp.reloadPlugin( self.pluginName );
    });
  });
};

rankMathCompatible.prototype.debounce = function( func, wait, immediate ) {
 var timeout;
  return function() {
    var context = this, args = arguments;
    var later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    }
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  }
}

$( document ).on( 'ready', function () {
  new rankMathCompatible();
});