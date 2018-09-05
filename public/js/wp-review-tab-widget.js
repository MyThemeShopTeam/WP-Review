/*
* Plugin Name: WP Review
* Plugin URI: http://mythemeshop.com/plugins/wp-review/
* Description: Create reviews! Choose from Stars, Percentages, Circles or Points for review scores. Supports Retina Display, WPMU & Unlimited Color Schemes.
* Author: MyThemesShop
* Author URI: http://mythemeshop.com/
*/
function wp_review_tab_loadTabContent( tab_name, page_num, container, args_obj ) {

    var container = jQuery( container );
    var tab_content = container.find( '#' + tab_name + '-tab-content' );

    // only load content if it wasn't already loaded
    var isLoaded = tab_content.data('loaded');

    if ( ! isLoaded || page_num != 1 ) {
        if ( ! container.hasClass( 'wp-review-tab-loading' ) ) {
            container.addClass( 'wp-review-tab-loading' );

            tab_content.load(wp_review_tab.ajax_url, {
                    action: 'wp_review_tab_widget_content',
                    tab: tab_name,
                    page: page_num,
                    args: args_obj
                }, function() {
                    container.removeClass( 'wp-review-tab-loading' );
                    tab_content.data( 'loaded', 1 ).hide().fadeIn().siblings().hide();
                }
            );
        }
    } else {
        tab_content.fadeIn().siblings().hide();
    }
}

jQuery(document).ready(function() {
    jQuery('.wp_review_tab_widget_content').each(function() {
        var $this = jQuery(this);
        var widget_id = this.id;
        var args = $this.data('args');

        // load tab content on click
        $this.find('.wp-review-tabs a').click(function(e) {
            e.preventDefault();
            jQuery(this).parent().addClass('selected').siblings().removeClass('selected');
            var tab_name = this.id.slice(0, -4); // -tab
            wp_review_tab_loadTabContent(tab_name, 1, $this, args);
        });

        // pagination
        $this.on('click', '.wp-review-tab-pagination a', function(e) {
            e.preventDefault();
            var $this_a = jQuery(this);
            var tab_name = $this_a.closest('.tab-content').attr('id').slice(0, -12); // -tab-content
            var page_num = parseInt($this_a.parents('.tab-content').find('.page_num').val());

            if ($this_a.hasClass('next')) {
                wp_review_tab_loadTabContent(tab_name, page_num + 1, $this, args);
            } else {
                $this.find('#'+tab_name+'-tab-content').data('loaded', 0);
                wp_review_tab_loadTabContent(tab_name, page_num - 1, $this, args);
            }

        });

        // load first tab now
        $this.find('.wp-review-tabs a').first().click();
    });

});
