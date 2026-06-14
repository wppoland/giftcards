=== Gift Cards – Store Credit for WooCommerce ===
Contributors: wppoland
Tags: woocommerce, gift card, store credit, gift voucher, gift certificate
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 0.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sell gift cards that email a redeemable code to the recipient and apply as a discount at checkout.

== Description ==

Sell gift cards as ordinary WooCommerce products. Flag any product as a gift card; when the order completes, the plugin generates a unique code, stores its balance, and emails the code to the recipient. Customers redeem a code at checkout and the remaining balance is applied as a discount — partial balances carry over to the next order.

= Features =

* Flag any product as a gift card from the product editor (General tab).
* A unique code is generated and emailed to the recipient on order completion.
* Redeem-code field at checkout applies the balance as a discount.
* Partial redemption — the remaining balance is kept for later use.
* Customisable code prefix, checkout discount label and recipient email subject/body.
* Issued codes shown to the buyer on the order-confirmation page and in order emails.
* WooCommerce HPOS (Custom Order Tables) and Cart/Checkout Blocks compatible.

== Installation ==

1. Upload the plugin to `/wp-content/plugins/giftcards`, or install via Plugins → Add New.
2. Activate it. WooCommerce must be active.
3. Edit a product, tick **Gift card** under the General tab, and set its price (the card value).
4. Tune the code prefix and recipient email under **WooCommerce → Gift Cards**.

== Frequently Asked Questions ==

= Does it require WooCommerce? =

Yes. WooCommerce 8.0 or later must be installed and active.

= How is the gift-card amount set? =

It is the price of the gift-card product. The recipient receives a code for that value; buying multiple quantities issues multiple codes.

= How does redemption work? =

The customer enters their code in the field at checkout. The remaining balance is applied as a discount, and any unused balance is kept for a future order.

== Screenshots ==

1. Gift Cards – Store Credit for WooCommerce in action.

== Changelog ==

= 0.2.0 =
* Recipient email subject and body set in **WooCommerce → Gift Cards** are now applied to the email the recipient receives (previously the stored values were ignored).
* New setting: customise the checkout discount label shown when a code is applied (supports {code}).
* New setting: show the issued gift-card codes to the buyer on the order-confirmation page and in their order emails (on by default).
* Default email/label strings are now translatable.

= 0.1.0 =
* Initial release.
