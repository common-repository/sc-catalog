=== SC Catalog ===
Contributors: scil, Salman Aslam
Tags: catalog, products, services, list, details, popup, gallery, catch-phrase
Requires at least: 2.6
Tested up to: 3.5
Stable tag: 1.3.3

Introduce products or services (or anything else) with in a galery with picture, title and catch-phrase. Get more information by clicking on it.

== Description ==

Displays a catalog with picture, title and catch-phrase and shows details in a popup when clicking on it.

Manage items in a dedicated panel, set title, catch-phrase, image, long
description, category and display order.

Use shortcode to display your catalog on your site where you want or just parts of it by categories.

The plugin is available in the following languages:

* English
* French
* Hebrew (thanks to Mac)
* Spanish (thanks to Juanda)
* Italian (thanks to kensh)
* Dutch (thanks to Bouke J. Henstra)
* Hungarian (thanks to Attila Balogh)
* German (thanks to Yanfred)

== Installation ==

1. Upload `sc-catalog` to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' panel in WordPress
1. Setup your catalog in the administration panels
1. Use the shortcode sc-catalog or catalog (in your language) to display it on a page or post:
  * [sc-catalog]
  * [catalog] (English)
  * [catalogue] (French)
  * ...
  * you can display only one category by adding cat="my category" insite de brackets. For exemple [sc-catalog cat="furnitures"]
  * to display 2 or more categories enter them separated by a comma (for example cat="cat1, cat2")

== Frequently Asked Questions ==

= I want to change the background color of items, how can I do it ? =

You will have to modify the css. Get into the plugin directory and open sc-catalog.css.

Change the background color at line 28 (background-color: #eee; in .sc-catalog-list-item)

= I have 150 products. Is there a way to paginate the catalog ? =

There is currently no pagination. If you are interested in it please contact us.

= What if…? =

For any question, feel free to contact the authors.

== Screenshots ==

1. The catalog displayed in a page
2. "Read more" popup
3. Administration panel, overview
4. Administration panel, item edit

== Changelog ==

= 1.3.3 =

* Update of JQuery UI. Fixes some side bugs on drag'n drop in menu and category autocompletion
* German translation (thanks to Yanfred)

= 1.3.2 =

* Fixed warnings in WordPress 3.5
* Fixed admin panel icon
* Hungarian translation (thanks to Attila Balogh)

= 1.3.1=

* Fixed sorting issue
* Dutch Translation (thanks to Bouke J. Henstra)

= 1.3.0 =

* Italian translation (thanks to kensh)
* Display multiple categories at once
* Take shortcode and other parsed content in title, catch and details
* Fixed display order

= 1.2.0 =

* Group items by category

= 1.1.1 =

* Spanish translation (thanks to Juanda)

= 1.1.0 =

* Remove image button
* Plugin help
* Support media inclusion in description
* Support for WordPress 3.3 text editor
* Hebrew translation

= 1.0.2 =

* Deleted empty html tags if item has no image or catch-phrase
* Removed read more link if item has no description
* Removed error message when saving an item without modifying it
* Enhanced database version check

= 1.0.1 =

* Corrected [sc-catalog] shortcode

= 1.0.0 =

* Administration panel
* Add, edit, delete, move up and move down items
* Display by shortcodes

== Upgrade Notice ==

= 1.3.3 =

Fixes autocompletion and some side effect bugs like menu items not dragging when sc-catalog is enabled

= 1.3.2 =

Fixes warnings in Wordpress 3.5 and adds icon and Hungarian translation

= 1.3.1 =

Fixes sorting issue and adds Dutch translation

= 1.3.0 =

Display multiple categories at once and use shortcodes inside your items

= 1.2.0 =

Group items by categories

= 1.1.1 =

Spanish translation

= 1.1.0 =

You may now remove an image and add media in description.
Hebrew translation is provided.

= 1.0.2 =

Minor bug fixes.

= 1.0.1 =

Make [sc-catalog] shortcode work
