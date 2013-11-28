=== Responsive Flickr Gallery ===
Contributors: larsschenk
Donate link: http://www.lars-schenk.com/responsive-flickr-gallery-donate/
Tags: responsive, flickr, gallery, cdn, photo, images
Requires at least: 3.7.0
Tested up to: 3.7.1
Stable tag: /trunk/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Responsive galleries from your Flickr photos for WordPress sites.

== Description ==

_Responsive Flickr Gallery_ is a simple, fast and light plugin to create responsive galleries of [Flickr](http://www.flickr.com/) photos. This plugin provides customizable, clean and professional looking Flickr galleries. It sacles perfectly on cloud hosted WordPress sites.

###Features:

* Manage your sets and photos with titles and descriptions on flickr and display them on your WordPress site
* Use Flickr as your organizer for photos
* Accelerate your site by loading images from the Flick.com CDN
* Reduces requests and traffic to your Site and helps to optimize page speed index
* Light: Small footprint keeps slug size compact for cloud hosting (i.e. on heroku)
* Uses caching to load galleries instantly and reduce expensive Fickr API-calls 
* Uses the database for catching to allow cloud hosting and scaling
* Support for both Public and Private photos
* Create multiple galleries with different parameters
* Powerful slideshow with Colorbox
* Select Photos from your Flickr Photostream, a Photoset, a Gallery, a Group or a set of tags
* Multiple sorting options available so that you don't have to rely on Flickr's sorting options
* Different default image sizes
* For better performance Flickr image were used - no creation and storage of custom sizes photos required
* Cloud hosting friendly: no custom image sizes means no need for local image cache
* This plugin is perfect for cloud hosting (i.e. heroku) where no persistent filesystem is garanteed 
* Customizable with custom CSS field * might be removed in future release *
* Fits into a sidebar widget too
* Insert multiple galleries on same page with independent slideshow and pagination
* Fits automatically according to the width of the theme or you can select the width of the gallery yourself
* Galleries are responsive - columns are used as space allows to
* Ability to disable built-in slideshow so that you can use a slideshow plugin of your choice
* Intuitive menu pages with easy configuration options and photo previews
* SEO friendly, all your photos are available to search engine crawlers
* Perfect for heroku hosting and scaling: Stores nothing into the filesystem
* Hosted on [wordpress.org](http://wordpress.org/plugins/responsive-flickr-gallery/) gives you automatic updates
* Hosted on [github.com](https://github.com/schenk/responsive-flickr-gallery/) for development, colaboration and issue tracking and alternate git based deployment
* PSR-2 coding standard
* License: GPLv3 or higher

###Example:

You can see a live demo of this plugin here:

* [Responsive Flickr Gallery Demo Page | Classic-Cadillac.com](http://www.classic-cadillac.com/1958-cadillac-convertible-7721/57711)

###Support:

Found a bug, need a feature?  Head to git issues for solution - [Issues | github.com](https://github.com/schenk/responsive-flickr-gallery/issues)

== Installation ==

- Extract the contents of the zip archive to the `/wp-content/plugins/` directory or install the plugin from your WordPress dashboard -> plugins -> add new menu
- Activate the plugin through the 'Plugins' menu in WordPress
- Configure plugin using Responsive Flickr Gallery settings page
- Place `[RFG_gallery]` in your posts and/or pages to show the default gallery or create new galleries with different settings and insert the generated code


== Frequently Asked Questions ==

#### After upgrade, only one column appears in the gallery. ####

> This happens when you have a cache plugin (like WP Super Cache or W3 All Cache) installed. Old cached CSS file is loaded instead of the new one. Just delete the cached pages from your cache plugin and refresh the gallery page 2-3 times, it will appear fine.

#### I have activated the plugin, but gallery doesn't load. ####

> Make sure your Flickr API key and Flickr User ID are correct.

#### My Flickr API key and User ID are correct but the gallery doesn't load ####

> Make sure you add the shortcode `[RFG_gallery]` to your post or page where you want to load the gallery.  This code is case-sensitive.

#### When I click the photo, it doesn't open full size photo. ####

> Responsive Flickr Gallery uses *Colorbox* to display full size photos.  Most likey you have another plugin enabled, which also uses the colorbox and is overriding the Responsive Flickr Gallery settings.  It is recommended to deactivate any other plugins that uses colorbox.

> Also, some themes have built-in settings to display images using lightbox or colorbox etc.  If your theme has such an option, turn it off.

#### I have created separate galleries with different photosets as Gallery Source, but all the galleries are using Photostream as source. ####

> This typically happens when you are using a plugin for editing your posts/pages. Try to remove the quotes from id parameter of the shortcode and it should work fine. For example, if the shortcode for your gallery is `[RFG_gallery id='1']`, use `[RFG_gallery id=1]` instead.

> Also, some themes have built-in settings to display images using lightbox or colorbox etc. If your theme has such an option, turn it off.

#### I made changes to my Flickr account but they don't reflect on my website. ####

> Responsive Flickr Gallery uses caching to avoid expensive calls to Flickr servers.  It intelligently figures out if cache needs to be updated or not.  However, sometimes it may not work as expected.  You should go to Default Settings and delete all cached data.

#### I created a gallery with source as a Group.  In this gallery, only 500 photos are appearing. ####

> As Flickr Groups have thousands of photos, it becomes very expensive to fetch all the photos from Flickr.  Hence, Groups galleries are limited to latest 500 photos.

#### Pagination does not work as expected. It show only one page of photos but the photoset has more photos. ####

> Check the permissions of the photos. Maybe only some photos were public. So you'll see more photo on flickr in the set than the plugin can access.

#### Does it scale? ####

> Yes, this fork was designed to avoid using a filesystem. In a cloud based environmend the filesystem might be not persistent and therefore shoudn't be used. This plugin has been designed with the cloud in mind. Tested on heroku.

== Screenshots ==

1. Responsive Flickr Gallery with large size photos with white background
2. Default Settings Page

== Changelog ==

= 0.0.5 =
* On WordPress.org: Makes it easy to find, install und keep updated.

= 0.0.4 =
* Removed call home for updates code.

= 0.0.3 =
* All code compatible with GPL.

= 0.0.2 =
* Fixed and tested for heroku.

= 0.0.1 =
* Initial development release.

== Upgrade Notice ==

This is a form from Awesome Flickr Gallery. Both can be used side by side without conflict. 
Upgrade if you need a responsive flickr gallery that 100% compatible with GPL and hostet and maintained on wordpress.org.

