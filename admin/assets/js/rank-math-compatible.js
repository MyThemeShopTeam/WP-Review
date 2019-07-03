/* global RankMath */
$( document ).ready( function() {

  rankMathCompatible = function() {
    RankMath.app.registerPlugin( 'rank-math-review-analysis', { status: 'ready' } );
    RankMath.app.addFilter( 'content', this.reviewDescription, 'rank-math-review-analysis' );
    RankMath.app.addFilter( 'title', this.reviewTitle, 'rank-math-review-analysis' );

    this.events();
  }

  // Analyze Review fields content.
  rankMathCompatible.prototype.reviewDescription = function( content ) {
    return content + $( '#wp_review_desc' ).val();
  };

  // Analyze Review fields title.
  rankMathCompatible.prototype.reviewTitle = function( title ) {
    return title + $( '#wp_review_desc_title' ).val();
  };

  new rankMathCompatible();
});
