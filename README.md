# Responsive Flickr Gallery #

_Responsive Flickr Gallery_ is an easy to use, fast and light plugin to create responsive galleries of [Flickr](http://www.flickr.com/) photos. This plugin provides customizable, clean and professional looking Flickr galleries. It scales perfectly on cloud hosted WordPress sites.

##Features:

* Manage your sets and photos with titles and descriptions on flickr and display them on your WordPress site
* Use Flickr as your organizer for photos
* Accelerate your site by loading images from the Flick.com CDN
* Reduces requests and traffic to your Site and helps to optimize page speed index
* Light: Small footprint keeps slug size compact for cloud hosting (i.e. on heroku)
* Uses caching to load galleries instantly and reduce expensive Fickr API-calls 
* Uses the database for caching to allow cloud hosting and scaling
* Support for both Public and Private photos
* Create multiple galleries with different parameters
* Powerful slideshow with Colorbox
* Select Photos from your Flickr Photostream, a Photoset, a Gallery, a Group or a set of tags
* Multiple sorting options available so that you don't have to rely on Flickr's sorting options
* Different default image sizes
* For better performance Flickr image were used - no creation and storage of custom sizes photos required
* Cloud hosting friendly: no custom image sizes means no need for local image cache
* This plugin is perfect for cloud hosting (i.e. heroku) where no persistent filesystem is garanteed 
* Fits into a sidebar widget too
* Insert multiple galleries on same page with independent slideshow and pagination
* Fits automatically according to the width of the theme or you can select the width of the gallery yourself
* Galleries are responsive - columns are used as space allows to
* Ability to disable built-in slideshow so that you can use a slideshow plugin of your choice
* Intuitive menu pages with easy configuration options and photo previews
* Highly configureable. Change defaults (i.e. Cache TTL) for all galleries or edit settings per gallery
* SEO friendly, all your photos are available to search engine crawlers
* Perfect for heroku hosting and scaling: Stores nothing into the filesystem
* Compatible with Flickr API going SSL-Only since June 27th, 2014
* Available on [wordpress.org](http://wordpress.org/plugins/responsive-flickr-gallery/) for automatic updates
* Available on [github.com](https://github.com/schenk/responsive-flickr-gallery/) for development, colaboration, feature requests, issue tracking and alternate git based deployment
* PSR-2 coding standard
* License: GPLv3 or higher
* Compatible with WordPress plugin guidelines
* Monetize your Flickr galleries: Optional support for responsive Google AdSense. To opt-on to use AdSense you have to enter your publisher ID
* Donations welcome by Bitcoin 1LY77g2LpxX6QC3xu9EUEponwKgvZfvFWb, Litecoin LMYPtmBS2fP6pa12iUT2szYkWDR36KNmRv or PayPal
* A license key is mandatory for businesses and commercial sites. For personal blogs the license is optional
* A license key enables [PRO] features. Simply enter your key. No new installation required
* [PRO] You are allowed to use this plugin on commercial sites and for business with a valid license.
* [PRO] Monetize your Flickr galleries with 100% ad impressions for your publisher ID

###Example:

You can see a live demo of this plugin here:

* [Responsive Flickr Gallery Demo Page | Lars-Schenk.com](http://www.lars-schenk.com/responsive-flickr-gallery)

###Support:

Found a bug, need a feature?
Head to git issues for solution - [Issues | github.com](https://github.com/schenk/responsive-flickr-gallery/issues)
Contributors and feature requests welcome. Bounties may speed up the development process and help to decide which feature will be included next.

##Installation:

- Extract the contents of the zip archive to the `/wp-content/plugins/` directory or install the plugin from your WordPress dashboard -> plugins -> add new menu
- Activate the plugin through the 'Plugins' menu in WordPress
- Configure plugin using Responsive Flickr Gallery settings page
- Place `[RFG_gallery]` in your posts and/or pages to show the default gallery or create new galleries with different settings and insert the generated code

##Screenshots:

![Responsive Flickr Gallery with large size photos with white background](https://dl.dropboxusercontent.com/u/4421587/responsive-flickr-gallery-demo-screenshot-wordpress.jpg "Responsive Flickr Gallery with large size photos with white background")
![Default Settings Page](https://dl.dropboxusercontent.com/u/4421587/responsive-flickr-gallery-admin-screenshot-wordpress.jpg "Default Settings Page")

##Frequently Asked Questions:

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

#### How to customize the CSS? ####

> You'll barley want to change the existing CSS because it's so generic. But you can do with a CSS editor that lets you customize your site design without modifying your theme as you can find in the Jetpack plugin. Check rfg.css to see existing classes and properties for gallery which you can redefine. 
