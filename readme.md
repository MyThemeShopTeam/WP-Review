# WP Review by MyThemeShop
Made with <3 from MyThemeShop LLC

* Pro Version [https://mythemeshop.com/plugins/wp-review-pro/](https://mythemeshop.com/plugins/wp-review-pro/)
* Official Download Page [https://wordpress.org/plugins/wp-review/](https://wordpress.org/plugins/wp-review/)

Did you always want to increase the user interaction on your website by rating products/services/anything? We at [MyThemeShop] (http://mythemeshop.com/) understand your need, & created a premium Review plugin. We are now distributing it for FREE to give back to the WordPress community. We have been given so much by the WordPress community, it's time to give back.

**WP Review plugin** is an easy yet powerful way to review content easily, without affecting the speed of your site. If you're a blogger, you probably occasionally review or rank products, services, tools, recipes, or other content on your site. WP Review plugin is a customizable and lightweight way to create reviews, using stars, percentage or point rating, and it includes support for translation, WPMU, Google rich snippets and unlimited colors. Just install it and follow the simple configuration instructions to place it in your desired location.

## Live Demos

See WP Review in action on our demo pages:

* [Star Review Type](http://demo.mythemeshop.com/point/fatebuntur-stoici-haec-omnia-dicta-esse-praeclare/)
* [Point Review Type](http://demo.mythemeshop.com/point/modo-etiam-paulum-ad-dexteram-de-via-declinavi/)
* [Percentage Review Type](http://demo.mythemeshop.com/point/sed-nonne-merninisti-licere-mihi-ista-probare/)

## Why WP Review from MyThemeShop:

* Fastest review plugin.
* Stars, percentage and point rating system.
* Supports Google Rich Snippets(schema.org)
* 100% Fluid Responsive.
* Option to set Global Position.
* Option to change Global Colors.
* Option to change individual review Colors and Positions.
* Included tabbed widget to show recent reviews and popular reviews.
* WP Multisite and Multiuser (WPMU / WPMS / WordPress MU) compatible.
* Design it as you want, unlimited color options.
* Translation Ready.
* Reviews are displayed to visitors in a friendly format.
* Completely customizable, including which fields to ask for, require, and show.
* Minimal design but could be instantly made modern.
* Works with caching plugins and all majority of themes.
* Easy to modify the CSS to better fit your theme style.
* Support for adding your own custom fields.
* Minimalist, lightweight, and efficient code means that your users won’t notice any hiccups.
* Position it above or below the content with ease and no coding.
* Supports Shortcode `[wp-review]` to show review anywhere in post.
* Developer friendly - Useful filters are included! So you can use it in your themes.

## Developer Zone

Yes, this plugin is developer friendly, so you could use it with any theme you develop. Define default CSS, custom position, one line integration in your theme's code.

Show average review in your theme using below function:

```php
<?php if (function_exists('wp_review_show_total')) wp_review_show_total(); ?>
```

You can find full list of the available filters in **filter-list.php** file, a theme developer can decide to set fixed colors for the reviews, and hide selected (or all) color fields in the metabox, to keep it simple for the end user.

If you have new feature suggession or found any bug, please open new thread in Issues section.

## Connect with Us

Consider following us on [Google+](https://plus.google.com/+Mythemeshop/), [Twitter](https://twitter.com/MyThemeShopTeam) and [Facebook](https://www.facebook.com/MyThemeShop).

### Changelog

#### 3.2
* Added Options Panel for Global Option
* Added option to set Global Colors.
* Added option to Global position.
* Added option to add Global Features.
* Added option to change review description title (Summary).
* Added option to hide Description and Total Rating.
* Added option to add your own Total Score.
* Fixed schema tag issue in wp_review_show_total()
* Fixed jQuery issue.
* Fixed post preview issue.
* Fixed conflict with Redux Framework, Visual Composer and Mailpoet.
* Added New Filters.
* Updated No Preview thumbnail.
* Merged Star and Loader icon files in one.
* Fixed many small bugs.
* Optimized code for better performance.

#### 3.1
* Fixed average star rating number issue.
* Added New filter to exclude post types.
* Updated filter list.

#### 3.0
* Major security updates
* New AJAXified Tab widget for Popular and Recent reviews, try it, you gonna love it.
* Language file updated fully.
* Added a nonce (a security token) to ensure that the user actually voted from the review
* More developer possibilities
* Added filters for developers. Using a filter is much better than a function for setting the default colors.
* Compatible with WordPress 3.9 Beta
* Plugin will support the widget customizer coming up in WordPress 3.9

#### 2.0
* Fixed the, `'` switching in to `/` issue ([http://bit.ly/PFMGAq](http://bit.ly/PFMGAq))
* Added `[wp-review]` shortcode to show the ratings anywhere in the content.
* Added an option to not show review automatically in the Review Location dropdown.
* Added support for Custom post types and pages.
* For Developers Added new function for showing only total rating, it could be used in themes' archives. A custom class name can be passed to the function, for easier customization. See `wp_review_show_total()` function in includes/functions.php file. There's also a shortcode for it, just in case: [wp-review-total]
* For Developers Added the default colors which appear in the meta boxes are now stored in an option. It can be modified directly with `update_option()`, or using the new `wp_review_set_default_colors()` function, which is also called on plugin activation to set the plugin's default colors.
* Made small CSS and responsive improvements.

#### 1.0
* Official plugin release.
