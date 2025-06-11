=== WooCommerce Invoice Me Gateway ===
Contributors: anasbinmukim
Donate link: http://rmweblab.com/donate/
Tags: 	buy first, checkout, group customers, invoice, invoice me, known customers, manual invoice, pay later, selected customers, woocommerce
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 4.8
WC tested up to: 3.2.3
WC requires at least: 3.2.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WooCommere Invoice Me Payment Gateway Extends WooCommerce Payment Gateway allow selected customer to checkout without payment.

== Description ==

WooCommerce Checkout without making payment rather select Invoice Me to make manual payment or payment later for known customers. This option is available only for selected and logged in customers.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/woocommerce-gateway-invoiceme` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Enable this gateway from WooCommerce settings /wp-admin/admin.php?page=wc-settings&tab=checkout&section=invoiceme


== Frequently Asked Questions ==


= Will it work for all user? =
Yes, this plugin(version > 2.0) will work for visitor or non logged user. For selected customers/users or user group by user role. Need to allow from user edit page.


== Screenshots ==

1. The screenshot (assets/screenshot-1.png) show gateway common settings, here all fields are self descriptive.
2. The screenshot (assets/screenshot-2.png) show user edit page where admin can allow selected customers
3. The screenshot (assets/screenshot-3.png) Show front end payment gateway select to pay using Invoice Me.
4. The screenshot (assets/screenshot-4.png) Show admin order list with Invoice Me. Where admin can mark as Invoice Sent.
5. The screenshot (assets/screenshot-5.png) Show new features like allow visitor and manage stock settings

== Changelog ==

= 2.1 =
* Enable for visitor(New)
* Stock Reduce settings features(New)


= 2.0 =
* Compatibility check for latest wordpress and woocommerce
* Update security features
* Added new features to mark invoice as paid
* Add default language file for English .pot, .po and .mo


= 1.1 =
* Allow group of customers use Invoice Me option by selecting user Role

= 1.0 =
* Initial development.

== Upgrade Notice ==

= 1.0 =
Should work perfectly.
