/* global RankMath */
$( window ).on( 'load', function() {

  rankMathCompatible = function() {
    RankMath.app.registerPlugin( 'rank-math-review-analysis', { status: 'ready' } );
    RankMath.app.addFilter( 'content', this.reviewDescription, 'rank-math-review-analysis' );
    RankMath.app.addFilter( 'title', this.reviewTitle, 'rank-math-review-analysis' );

    this.events();
  }

  // Analyze Review fields content.
  rankMathCompatible.prototype.reviewDescription = function( content ) {
    return content + tinyMCE.editors['wp_review_desc'].getContent();
  };

  // Analyze Review fields title.
  rankMathCompatible.prototype.reviewTitle = function( title ) {
    return title + $( '#wp_review_desc_title' ).val();
  };

  rankMathCompatible.prototype.events = function(data) {
    tinyMCE.editors.wp_review_desc.on( 'change', function() {
      RankMath.app.refresh()
    });
  };

  new rankMathCompatible();
});