=== Tapcha ===
Contributors: tapcha
Tags: captcha, contact form 7
Requires at least: 4.9
Tested up to: 5.3.2
Stable tag: trunk
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A gesture based CAPTCHA scheme. Integrates with Contact Form 7

== Description ==

Adds a tapcha form tag to Contact Form 7. You can use it by adding [tapcha* name] to your form in Contact Form 7.
This plugin makes use of the API from tapcha.co.uk to generate Tapcha challenges and to check whether the response is correct.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/tapcha` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the `Settings -> Tapcha` screen to configure the plugin

== Frequently Asked Questions ==

= Where do I get a site key / admin key? =

Visit http://tapcha.co.uk and sign up for an account. Once you have created a site you can then copy and paste the keys across and save.

= My Tapcha shows: Error! Please contact the site administrator =

Please check the site key in `Settings` -> `Tapcha` matches the one for your site at http://tapcha.co.uk

== Tapcha API ==

This plugin will make requests to our API hosted at api.tapcha.co.uk. A request will be made to it in the following circumstances:
- When a short code / form tag is used, a request will be made to generate the challenge to display on page load
- When users respond to a challenge, a request is made to the API to verify whether the response is correct
- If you have entered an admin key in the settings page, a request will be made to gather statistics that will be shown below

For more information please read our:
- Terms of service at http://tapcha.co.uk/terms-of-service
- Privacy policy at http://tapcha.co.uk/privacy-policy

== Changelog ==

= 0.3.0 =
* Initial MVP release