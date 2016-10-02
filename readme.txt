=== Frontend Publishing ===
Contributors: khaxan
Tags: guest posting, article directory, new post, manage posts, membership, contributors, writers, front-end, frontend, accept guest posts, shortcodes, submission form, authors
Donate link: http://wpgurus.net/
Requires at least: 3.6
Tested up to: 4.6
Stable tag: 2.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow your registered members to post, edit and delete content from the frontend.

== Description ==

This lightweight frontend posting plugin allows you to accept guest posts/articles without giving your members access to the sensitive WordPress admin area.

The posts that don't meet the submission guidelines of your website are not accepted and an error is shown to the frontend user. This can be a huge time saver if you have a very popular blog and you get a lot of guest post submissions.

In the WordPress admin area you can specify the following:

*   maximum and minimum number of words in title, content and author bio
*   number of tags
*   number of links in article body and author bio
*   whether you want to nofollow the links in article body and/or author bio

You can allow members with a certain user level to publish posts instantly. All other posts are added to the 'pending' queue.

**Usage:**

Add a page with the following shortcode to create a frontend submission form:

`[fep_submission_form]`

Add another page with the following shortcode to create a list of user's posts:

`[fep_article_list]`

**Important:** DO NOT add the two codes on the same post or page

== Installation ==

1. Use the WordPress plugin installer to upload the plugin. Alternatively you can manually create a new folder called 'fepublishing' in the `/wp-content/plugins/` directory and upload all the files there.
2. Activate the plugin from the 'Plugins' menu in WordPress
3. To display the submission form on your website create a new post or page with shortcode [fep_submission_form] in it
4. Similarly create a new post or page with shortcode [fep_article_list]

== Screenshots ==

1. FEP control panel
2. Submission form
3. List of articles submitted by the user

== Changelog ==
= 1.0 =
* Initial release.
= 2.0 =
* Support for non-english characters
* Non-administrators can now add media
* The post list is now paginated
* Interface and security related adjustments
= 2.3.0 =
* Created a POT file for translation
* Changed the settings page to a main page in the admin area
* Small code improvements
* Fixed interface issues
= 2.3.2 =
* Previously translatable strings were being saved in the DB which was causing problems with localization. FIXED.
* Added additional check to enqueue resources in the main query only.