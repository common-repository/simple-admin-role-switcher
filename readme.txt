=== Simple Admin Role Switcher ===
Contributors: ashbrentnall, abcodeuk
Donate link: https://abcode.co.uk/
Tags: user roles, role switching, frontend testing, admin testing, dynamic field testing 
Requires at least: 4.6
Tested up to: 6.6
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily switch and view the WordPress site as different user roles for frontend testing.

== Description ==

Simple User Role Switcher allows administrators to seamlessly switch between different user roles in WordPress to see how their site looks for each role. Ideal for WooCommerce sites, membership sites, or any scenario where it's important to test how content and features appear to different roles.

This plugin simplifies the testing process by allowing quick switching between user roles directly from the admin toolbar without logging out. It also supports viewing the site as a guest user for better testing of logged-out experiences.

== Features ==

* **View as Guest:** Easily switch to a guest user to see the experience of logged-out users without logging out of the site.
* **View as Different User Roles:** Test the site as different roles, such as Subscribers, Customers, Editors, etc.
* **Simple Switching:** Use the admin toolbar to switch roles quickly and revert to the original role easily.
* **Compatibility:** Works with major plugins such as WooCommerce, BuddyPress, and popular page builders.
* **Admin Toolbar Integration:** Role switcher integrated directly into the admin toolbar for convenience.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/simple-user-role-switcher` directory or install the plugin via the WordPress plugin screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the "View as" option in the admin toolbar to switch between different user roles.

== Frequently Asked Questions ==

= Does this plugin work with WordPress Multisite? =

Currently, Simple User Role Switcher is designed for single-site installations only.

= Can I use this plugin with WooCommerce? =

Yes, you can switch to customer or shop manager roles to see how your WooCommerce store appears to these users.

= Does this plugin work with BuddyPress? =

Yes, you can switch between member roles in BuddyPress to test different experiences.

= What user capability is required to switch roles? =

Only users with the `edit_users` capability (typically administrators) can switch roles.

== Screenshots ==

1. **Admin Toolbar Role Switcher**  
   ![Admin Toolbar Role Switcher](.wordpress-org/screenshot-1.png)  
   Easily switch roles from the admin toolbar.

== Changelog ==

= 1.0 (15 October 2024) =
* Added support for clearing cookies and resetting roles during plugin uninstallation.
* Improved handling of nonce checks for role switching.
* Updated admin toolbar behavior for role switching in the frontend versus backend.
* Added functionality to open a new tab when switching roles from the admin area.
* Enhanced handling of the guest role to maintain admin bar visibility.
* Improved accessibility to ensure compliance with WCAG 2.0 standards.
* Fixed an issue where the role switching persisted incorrectly after logging out.
* Initial release of Simple User Role Switcher.

== Upgrade Notice ==

= 1.0 =
Initial release of the Simple User Role Switcher, providing essential role-switching capabilities for administrators.

== License ==

This plugin is licensed under GPL v2 or later. Contributions are welcome, and the plugin remains open source to benefit the WordPress community.

== Privacy Statement ==

Simple User Role Switcher uses a temporary cookie to manage role switching for testing purposes. This data is never shared with third parties. The cookie is cleared when logging out or switching back to Administrator.

== Ethical Open Source ==

Simple User Role Switcher follows the principles of ethical open source. It respects users' privacy and does not collect any data. We believe in transparency and providing tools that support developers and administrators while maintaining data safety.
