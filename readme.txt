=== Auto SEO ===
Contributors: Phillip.Gooch
Tags: pages, seo, meta-tags, admin
Requires at least: 3.4
Tested up to: 3.9.1
Stable tag: 2.0.0
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Auto SEO is a quick, simple way to add title, meta keywords, and meta descriptions to your site all at one from a single page.

== Description ==

Auto SEO is a simple way t add all your SEO header tags from a single interface. It will generate new meta tags, replaceing any old ones your theme may add already, fully customized to target the audience you want. Don’t want to override everything? No problem, you choose what to override and on what post types to do it. Take the tedidum out of SEO.

_Note: because Auto SEO is designed to override existing meta tags when needed it works a bit differently than other SEO plugins and as such may not work on every theme. While it has been tested with a wide variety of different themes naturally it would be impossible to test them all. If your having trouble getting it to active on your site I’m more than willing to help, just let me know what theme your using and I’ll take a look, contact information inside the plugin.

== Installation ==

1. Upload the `auto-seo` directory to your `/wp-content/plugins/` directory.
2. Active the plugin.
3. Go to the new “Auto SEO” settings page and review/adjust the settings and keywords as desired.

The settings page has notes on their functions and should be self explanatory, but here are the highlights.

- **Active Post Types** determines where Auto SEO inserts/replaces meta data.

- **Keyword Sets** lets you define sets of keywords to be automagically inserted into the description where you want them, also shows the poo, from which meta keyweords and chosen.

- **Meta Tag Options** let you control what tags are enabled, and set up new title tags, meta descriptions, and the number of keywords as well as override the robots if desired.

== Frequently Asked Questions ==

= I got this great idea, can you implement it? = 

Probably, let me know and I'll see if I can work it in there.

== Screenshots ==

1. Auto SEO settings page.

== Changelog ==

#### 2.0.0
 + Completly rewritten from the ground up, bigger, better, faster, stronger.
 + Now supports completly custom keyword sets.
 + Added the ability to changed what is inserted/overitten and where.
#### 1.3.8
 + Fixed a bug that could under some strange circumstances cause a notice level error to appear on installations with debug turned on.
#### 1.3.7
 + Filled in some additional information in the read me, just to try and avoid any confusion.
 + Changed author information.
#### 1.3.6
 + Fixed a bug that would cause keywords to fail if the page/post had a very large (50,000+) id.
 + Cleaned up the location output so it couldn't add extraneous line breaks in meta tags.
 + Added site title back to the front of the keywords list.
#### 1.3.5
 + Added an appropriate robots meta tag into plugin (currently same as default wordpress).
 + Fixed a bug in which it would only pull 2 keywords.
 + Fixed a bug in which it wouldent remove old meta tags before adding new ones.
#### 1.3.4
 + Fixed a bug that would cause the plugin to fail when your sites home page was it's blog.
 + Reversed the order of the change log to newest version is always at the  top.
#### 1.3.3
 + Fixed the broken menu icon (thanks wordpress extend rename).
 + Fixed a bug that would cause pages to not load if the AutoSEO settings were missing or blank.
#### 1.3.2
 + Changed Licensing for Worpdres Extend Submission.
 + Removed changelog from main file and placed it into readme.
#### 1.2.2
 + Fixed a bug where a line of code would occasionally show at top of page.
#### 1.2.1
 + Homepage now always uses first item from each list.
#### 1.1.1
 + Gave the back end a custom icon.
 + Changed when the output buffer was cahced for better compatibility to old/bad themes (I'm looking at you fast and quick).
 + Fixed a rare bug where the title would not display - _Allegedly_
#### 1.0.0
 + Initial Release