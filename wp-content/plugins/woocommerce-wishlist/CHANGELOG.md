# Changelog
======
1.1.9
======
- NEW:	Added support for our single variations plugin
		https://www.welaunch.io/en/product/woocommerce-single-variations/
- NEW:	Added support for variations on product level
		https://imgur.com/a/ZmQLA5g
- FIX:	Issue with our variations table plugin
- FIX:	Add to Wishlist button shortcode not working
- FIX:	Removed Header tag from modal

======
1.1.8
======
- NEW:	Exlcude / include product categories or products created by specific users (e.g. vendors)
		https://imgur.com/a/JZac4Am
- FIX:	Fatal error on Statistics when product is no longer available
- FIX:	Loading icon not showing on buttons

======
1.1.7
======
- NEW:	Wishlists in WooCommerce My Account section:
		https://imgur.com/a/8YP0IR1
		Resave permalinks after enabling
- FIX:	Added support for store locator
- FIX:	Updated PHP Office library to latest version

======
1.1.6
======
- NEW:	Added classes to wishlist modal
- NEW:	Added font awesome 5 support
- FIX:	Guest Wishlist not working
- FIX:	Added aria label 

======
1.1.5
======
- NEW:	Dropped Redux Framework support and added our own framework 
		Read more here: https://www.welaunch.io/en/2021/01/switching-from-redux-to-our-own-framework
		This ensure auto updates & removes all gutenberg stuff
		You can delete Redux (if not used somewhere else) afterwards
		https://www.welaunch.io/updates/welaunch-framework.zip
		https://imgur.com/a/BIBz6kz
- FIX:	No products added text not shown on my wishlist page

======
1.1.4
======
- FIX:	Long description css class wrong
- FIX:	Switched to custom argument in wishlist for better theme support

======
1.1.3
======
- NEW:	Section in options panel for custom texts

======
1.1.2
======
- NEW:	Excel exports image as file not as URL path now
- NEW:	First rows bold in Excel
- NEW:	Auto wrap description text
- NEW:	Products on Guest Wishlist now show on default as added to wishlist
- FIX:	Added to cart now set by default if a product is in guest wishlist

======
1.1.1
======
- NEW:	Export wishlists as CSV or Excel XLSX
		See data to show you can enable both buttons

======
1.1.0
======
- NEW:	Share Guest Wishlists, the URL is adding a GET Parameter:
		xxx.com/my-wishlist/?wishlist-products=70,234
		70 & 234 are IDs
- NEW:	Option to remove the sidebar with the login info text for guests
- NEW:	Product image & product title are linked now
- NEW:	Print option with additional CSS Print Styling 
		Can be enalbed in Share Settings
- FIX:	Removed Google+ sharing as Google+ is stopped
- FIX:	Facebook Share Button
- FIX:	Twitter Share Button
- FIX:	Pinterest Share Button
- FIX:	Issue when public wishlists showed edit / delete links

======
1.0.11
======
- FIX:	Public / Shared wishlists are visible by guests

======
1.0.10
======
- NEW:	Added support for Guest Wishlist PDF Export Plugin
		https://welaunch.io/plugins/woocommerce-pdf-catalog/

======
1.0.9
======
- FIX:	Set default button position to before meta information
- FIX:	Set wishlist Query to posts_per_page unlimited

======
1.0.8
======
- FIX:	Added span tag to add to wishlist btn
- FIX:	Issue when product was deleted / no longer available

======
1.0.7
======
- FIX:	PHP Error

======
1.0.6
======
- NEW:	Added span tag to add / view wishlist texts
- NEW:	Added an option for custom integration in shop loop
- NEW:	Added btn classes to submit button
- NEW:	Set your own login page URL

======
1.0.5
======
- NEW:	Set Cookie Lifetime in plugin settings for guest wishlist
- FIX:	Translations breaking HTML

======
1.0.4
======
- NEW:	Added translations:
		- de_DE
		- en_US
		- es_ES
		- fi_FI
		- fr_FR
		- hu_HU
		- it_IT
		- nb_NO
		- nl_NL
		- pl_PL
		- pt_PT
		- ru_RU
		- sk_SK

======
1.0.3
======
- NEW:	Products added to a wishlist show in backend

======
1.0.2
======
- FIX:	Wishlist button not working after AJAX loaded products
- FIX:	Wishlist loading icon not disappearing after close

======
1.0.1
======
- NEW:	Integration to our WooCommerce PDF Catalog Plugin:
		https://codecanyon.net/item/woocommerce-pdf-catalog/15310703
- NEW:	Actions:
		woocommerce_wishlist_before_wishlist
		woocommerce_wishlist_before_products
		woocommerce_wishlist_after_products
		woocommerce_wishlist_after_wishlist

======
1.0.0
======
- Inital release