=== WP Rollback ===
Contributors: wordimpress, dlocc, drrobotnik, webdevmattcrom
Tags: rollback, revert, downgrade, version, plugins, themes, version, versions, backup, backups, revision, revisions
Requires at least: 4.8
Donate Link: https://wordimpress.com
Tested up to: 4.8
Stable tag: 1.5.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Rollback (or forward) any WordPress.org plugin or theme like a boss.

== Description ==

Quickly and easily rollback any theme or plugin from WordPress.org to any previous (or newer) version without any of the manual fuss. Works just like the plugin updater, except you're rolling back (or forward) to a specific version. No need for manually downloading and FTPing the files or learning Subversion. This plugin takes care of the trouble for you.

= Rollback WordPress.org Plugins and Themes =

While it's considered best practice to always keep your WordPress plugins and themes updated, we understand there are times you may need to quickly revert to a previous version. This plugin makes that process as easy as a few mouse clicks. Simply select the version of the plugin or theme that you'd like to rollback to, confirm, and in a few moments you'll be using the version requested. No more fumbling to find the version, downloading, unzipping, FTPing, learning Subversion or hair pulling.

= Muy Importante (Very Important): Always Test and Backup =

**Important Disclaimer:** This plugin is not intended to be used without first taking the proper precautions to ensure zero data loss or site downtime. Always be sure you have first tested the rollback on a staging or development site prior to using WP Rollback on a live site.

We provide no (zero) assurances, guarantees, or warranties that the plugin, theme, or WordPress version you are downgrading to will work as you expect. Use this plugin at your own risk.

= Translation Ready =

Do you speak another language? Want to contribute in a meaninful way to WP Rollback? There's no better way than to help us translate the plugin. This plugin is translation ready. Simply use the wp-rollback.pot file and your favorite translation tool. Once finished, please reach out to us on the WordPress.org forums or better yet, submit a pull request on the [Github Repo](https://github.com/WordImpress/WP-Rollback/).

= Support and Documentation =

We answer all support requests [on the WordPress.org support forum](https://wordpress.org/support/plugin/wp-rollback).

WP Rollback was created to be as intuitive to the natural WordPress experience as possible. There are is no dedicated setting page or option panels. We believe that once you activate WP Rollback, you'll quickly discover exactly how it works without question.

**BUT!!**

We do have documentation on the plugin [Github Wiki](https://github.com/WordImpress/WP-Rollback/wiki).

== Installation ==

= Minimum Requirements =

* WordPress 4.8 or greater
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

= Is this plugin safe to use? =
Short answer = Yes. Longer answer = It depends on how you use it.

WP Rollback is completely safe because all it does is take publicly available versions of the plugins you already have on your site and install the version that you designate. There is no other kinds of trickery or fancy offsite calls or anything. BUT!!!

Safety largely depends on you. The WordPress website admin. We absolutely do NOT recommend rolling back any plugins or themes on a live site. Test the rollback locally first, have backups, use all the best practice tools available to you. This is intended to make rolling back easier, that's all.

= Why isn't there a rollback button next to X plugin or theme? =

WP Rollback only works with plugins or themes installed from the WordPress Repository. If you don't see the rollback link, then most likely that plugin or theme is not found on WordPress.org. This plugin does not support plugins from Github, ThemeForest, or other sources other than the WordPress.org Repo.

= I rolled my [insert plugin name] back to version X.X and now my site is broken. This is your fault. =

Nope. We warned you in **bold** print several times in many places. And our plugin delivered exactly what it said it would do. May the gods of the Internet pity your broken site's soul.

= Where is the complete documentation located? =

The documentation for this plugin is located on our [Github Wiki](https://github.com/WordImpress/WP-Rollback/wiki). This is where we make regular updates.

= Can this plugin be translated? =

Yes! All strings are internationalized and ready to be translated. Simply use the languages/wp-rollback.pot file and your favorite translation tool. Once finished, please reach out to us on the WordPress.org forums or better yet, submit a pull request on the [Github Repo](https://github.com/WordImpress/WP-Rollback/).

= Did this plugin with WordCamp Orange County's Plugin-Palooza? =

Heck yes it did! The WordImpress team took home the gold.

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

= 1.5.1 =
* Tweak: Added additional information about the importance of Staging and Backups and links to our preferred plugin.

= 1.5 =
* New: You can now view plugin changelogs within the rollback screen. [#7](https://github.com/WordImpress/WP-Rollback/issues/7)
* New: Added support for WordPress Multisite rollbacks for themes and plugins. [#22](https://github.com/WordImpress/WP-Rollback/issues/22)
* New: Rollback button is fixed to the bottom of the page now to prevent long scrolls for rollbacks with many versions. [#23](https://github.com/WordImpress/WP-Rollback/issues/23)
* New: Updated the WP.org plugin header graphic. [#37](https://github.com/WordImpress/WP-Rollback/issues/37)

= 1.4 =
* New: Updated plugin's text domain to the plugin's slug of 'wp-rollback' to support WordPress' GlotPress translations. [#28](https://github.com/WordImpress/WP-Rollback/issues/28)
* New: Gulp automated POT file generation and text domain checker. [#28](https://github.com/WordImpress/WP-Rollback/issues/28)
* Fix: Check the WP install's themes transient is present, if not fetch it to see if a theme can be rolled back. Allows rollbacks for new WP installs or in a case where the transient is not set properly.[#27](https://github.com/WordImpress/WP-Rollback/issues/27)

= 1.3 =
* Tested compatibility with WordPress 4.4 and verified as working; bumped up compatibility
* Fix: Trying to get property of non-object warning. [#20](https://github.com/WordImpress/WP-Rollback/issues/20)
* Improvement: Better version sorting now using usort & version_compare. [#16](https://github.com/WordImpress/WP-Rollback/issues/16)

= 1.2.4 =
* New: Portuguese translations added.
* Fix: Limit HTTP requests to Plugin page only. [Report 1](https://wordpress.org/support/topic/great-plugin-but-small-issue?replies=5) [Report 2](https://wordpress.org/support/topic/great-plugin-but-small-issue?replies=1#post-7234287)

= 1.2.3 =
* Fixed: XSS hardening. Thanks @secupress
* Fixed: CSRF patch regarding missing nonces. Thanks @secupress
* Improvement: escape all of the things.

= 1.2.2 =
* New: Russian translations from @Flector - thanks!
* Fix: Replaced use of wp_json_encode to support older WordPress versions. [Report](https://wordpress.org/support/topic/wordpress-requirement-issue-with-wp_json_encode)

= 1.2.1 =
* Fix: Rollback link appears on non wp.org plugins - thanks @scottopolis. [#14](https://github.com/WordImpress/WP-Rollback/issues/14)
* Removed unnecessary WP_ROLLBACK_VERSION constant.

= 1.2 =
* New: Swedish translation files - Thanks @WPDailyThemes.

= 1.1 =
* Fixed "Cancel" button which was falsely submitting the form.

= 1.0 =

* Initial plugin release. Yippee!
* Adds "Rollback" link to all plugins from the WordPress repo on the plugin screen.
* Adds "Rollback" link to all themes from the WordPress repo inside the modal details screen.
* The "Rollback" page allows you to choose which version you want to rollback to.
