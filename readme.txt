=== WP Rollback ===
Contributors: wordimpress, dlocc, drrobotnik, webdevmattcrom
Tags: rollback, revert, downgrade, version, plugins, themes, 
Requires at least: 3.8
Donate Link: https://wordimpress.com
Tested up to: 4.2.2
Stable tag: 1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Rollback any WordPress.org plugin or theme like a boss.

== Description ==

Quickly and easily rollback any theme or plugin from WordPress.org to a previous version without any of the manual fuss. Works just like the plugin updater, except you're rolling back (or forward) to a specific version. No need for manually downloading and FTPing the files or learning Subversion. This plugin takes care of the trouble for you.

= Rollback WordPress Plugins, Themes =

It is best practice to always keep your WordPress plugins and themes updated, we understand that there's times you may need to quickly revert to a previous version. This plugin makes that process as easy as a few mouse clicks. Simply select the version of the plugin or theme that you'd like to rollback to, confirm, and in a few moments you'll be using the version you requested. No fumbling to find the version, FTPing, learning Subversion or hair pulling.

= Muy Imporante: Always Test and Backup =

**Important Disclaimer:** This plugin is not intended to be used without first taking the proper precautions to ensure zero data loss or site downtime. Always be sure you have first tested the rollback on a staging or development site and prior to using WP Rollback on a live site.

We provide no (zero) assurances, guarantees, or warrenties that the plugin, theme, or WordPress version you are downgrading to will work as you expect. Use this plugin at your own risk.

= Support and Documentation =
We answer all support requests [here](https://wordpress.org/support/plugin/wp-rollback).

WP Rollback was created to be as intuitive to the natural WordPress experience as possible. There are no settings at all. We believe that once you activate WP Rollback you'll quickly discover exactly how it works without question.

**BUT!!**

We do have documentation [here](https://github.com/WordImpress/WP-Rollback/wiki).

== Installation ==

= Minimum Requirements =

* WordPress 3.8 or greater
* PHP version 5.3 or greater
* MySQL version 5.0 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of WP Rollback, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type "WP Rollback" and click Search Plugins. Once you have found the plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking "Install Now".

= Manual installation =

The manual installation method involves downloading our donation plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Frequently Asked Questions ==

= Is this safe? =
Short answer = Yes. Longer answer = It depends.

WP Rollback is completely safe because all it does is take publicly available versions of the plugins you already have on your site and install the version that you designate. There is no other kinds of trickery or fancy offsite calls or anything.

**BUT!!!**

It depends on you. We absolutely do NOT recommend rolling back any plugins or themes on a live site. Test the rollback locally first, have backups, use all the best practice tools available to you. This is intended to make rolling back easier, that's all.

= Why isn't there a rollback button next to X plugin or theme? =
WP Rollback only works with plugins or themes installed from the WordPress Repository. If you don't see the rollback link, then most likely that plugin or theme was installed from a third party website or Github, or manually installed, or one of many other possibilities besides the only way this plugin works -- from the Repo.

= I rolled my WooCommerce back to version 1.5 and now my site is broken. Can I sue you? =
Nope. We warned you in **bold** print several times in many places. And our plugin delivered exactly what it said it would do. May the gods of the internet pity your broken site's soul.

== Screenshots ==

1. The Rollback link on the Plugins page.
2. The Rollback Versions page for a plugin.
3. The Update plugin screen.
4. The Rollback button on the Theme Modal popup.
5. The Rollback Versions page for a theme.
6. The Rollback modal confirmation popup.
7. The Update theme screen.

== Upgrade Notice ==
This is the first version of this plugin. It is a tool for your convenience. Rollback at your own risk!

== Changelog ==

= 1.0 =

* Initial plugin release. Yippee!
* Adds "Rollback" link to all plugins from the WordPress repo on the plugin screen.
* Adds "Rollback" link to all themes from the WordPress repo inside the modal details screen.
* The "Rollback" page allows you to choose which version you want to rollback to.