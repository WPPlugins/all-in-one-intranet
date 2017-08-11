=== Plugin Name ===
Contributors: danlester
Tags: authentication, company, intranet, extranet, private, privacy, network, security, visibility, secure
Requires at least: 3.5
Tested up to: 4.8
Stable tag: 1.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Instantly turn your WordPress installation into a private corporate intranet

== Description ==

WordPress is a popular platform for creating corporate intranets. The only problem is that it's built primarily for public-facing websites.

There are plenty of free plugins available to add privacy and other requirements - but you'll need to cherry-pick the functionality you need, and make sure they all work well together.

All-In-One Intranet gives you everything you need in one plugin to lock down your site and start building your intranet.

Features:

*  **Privacy** - one checkbox to make your entire site private to anyone not logged in. Also displays warnings if any core WordPress settings are currently allowing unauthorized users to register.
*  **Login Redirect** - your staff are logging in to read information as well as write it, so WordPress' default of logging users in to their profile page is unhelpful. Set any site URL as their new landing page.
*  **Auto Logout** - set a time interval for inactivity, after which users will be automatically logged out, protecting your sensitive company information.

This basic plugin is designed to work on single-site WordPress installations. If you have a 
[multisite installation](http://codex.wordpress.org/Create_A_Network), please see our premium version, below.

= Support and Premium features =

Full support and premium features are also available for purchase:

*  All the features of the basic version plus...
*  Designed for Multisite WordPress
*  **Sub-site Membership** - set a default role to be applied to all sub-sites when a new user (or site) is created. Saves having to manually add new users to sub-sites, or existing users to new sub-sites.
*  **Sub-site Privacy** - decide whether users need to be members of a sub-site in order to view it (presuming you already restricted the whole site to logged-in users only, in 'Privacy').
*  Full support and updates

See [All-In-One Intranet Premium](http://wp-glogin.com/all-in-one-intranet/?utm_source=AllInOne%20Readme&utm_medium=freemium&utm_campaign=AllInOneFreemium)

= Google Apps =

Does your organization use Google Apps?

Our [Google Apps Login](http://wp-glogin.com/google-apps-login-premium/?utm_source=AllInOne%20Readme%20GAL&utm_medium=freemium&utm_campaign=AllInOneFreemium) 
plugin enables Google Apps domain admins to manage WordPress user accounts entirely from Google Apps. 
This saves time and increases security - giving peace of mind that only authorized employees have access to the company's websites and intranet. 

And our [Google Drive Embedder]((http://wp-glogin.com/drive/?utm_source=AllInOne%20Readme%20GDE&utm_medium=freemium&utm_campaign=AllInOneFreemium)) 
plugin allows post/page authors to easily embed documents directly from Google Drive throughout your site.

= Website =

Please see our website [http://wp-glogin.com/](http://wp-glogin.com/?utm_source=AllInOne%20Readme%20Website&utm_medium=freemium&utm_campaign=AllInOneFreemium) 
for more information about all our products, and to join our mailing list. 

== Screenshots ==

1. Settings page to configure intranet settings.

== Frequently Asked Questions ==

= How can I obtain support for this product? =

Full support is available if you purchase the appropriate license from the author via:
[http://wp-glogin.com/all-in-one-intranet/](http://wp-glogin.com/all-in-one-intranet/?utm_source=AllInOne%20Readme%20Premium&utm_medium=freemium&utm_campaign=AllInOneFreemium)

Please feel free to email [contact@wp-glogin.com](mailto:contact@wp-glogin.com) with any questions,
as we may be able to help, but you may be required to purchase a support license if the problem
is specific to your installation or requirements.

We may occasionally be able to respond to support queries posted on the 'Support' forum here on the wordpress.org
plugin page, but we recommend sending us an email instead if possible.

= Is it secure? =

Care has been taken to ensure the plugin offers the level of security promised for a standard WordPress installation.

Note that your media uploads (e.g. photos) will still be accessible to anyone who knows their direct URLs. This the way most 
privacy plugins work.

However, the author does not accept liability or offer any guarantee of security or functionality,
and it is your responsibility to ensure that your site is secure and functions in the way you require.

In particular, other plugins may conflict with each other, and different WordPress versions and configurations
may render your site insecure.

= What are the system requirements? =

*  PHP 5.2.x or higher
*  Wordpress 3.5 or above

== Installation ==

Easiest way:

1. Go to your WordPress admin control panel's plugin page
1. Search for 'All-In-One Intranet'
1. Click Install
1. Click Activate on the plugin
1. Go to 'All-In-One Intranet' under Settings in your Wordpress admin area to configure

If you cannot install from the WordPress plugins directory for any reason, and need to install from ZIP file:

1. Upload `allinoneintranet` directory and contents to the `/wp-content/plugins/` directory, or upload the ZIP file directly in
the Plugins section of your Wordpress admin
1. Go to Plugins page in your WordPress admin
1. Follow the instructions from step 4 above


== Changelog ==

= 1.3 =

Changed which WordPress hooks are used to check for auto-logout. This is to widen compatibility with certain Themes.

= 1.2 =

On non-multisite WordPress, now restricts access to users who have no role, as well as those who aren't logged in at all.

= 1.1 =

Ready for public release
