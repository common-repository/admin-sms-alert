=== Plugin Name ===
Contributors: sroyalty
Donate link: http://www.premiumdigitalservices.net/blog
Tags: SMS, email, phone, admin, comments, comment, activation, user, new user, google voice, voice, google
Requires at least: 2.3
Tested up to: 3.0.1
Stable tag: 1.1.0

Admin SMS Alert sends a SMS Message (Text) to the provided cell phone on the chosen carrier for selected alerts.

== Description ==

Admin SMS Alert sends a SMS Message (Text) to the provided cell phone on the chosen carrier for selected alerts. 
Alert examples are for comments awaiting approval/spam, approved/disapproved comments, and new pingbacks.

ASMSA is compatible with most US, Canada, and several other countries' cell carriers including Google Voice.
     United States
	 Canada
	 Denmark
	 Germany
	 India
	 Italy
	 Norway
	 Singapore
	 Sweden
	 United Kingdom

== Installation ==

1. Upload zip file contents to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in the WordPress dashboard
3. View the 'Admin SMS' page of the 'Options' menu in the WordPress dashboard
4. Configure ASMSA by specifying a phone number and a carrier and choosing which notifications you wish
   to recieve via SMS (Text) Alert.

== Frequently Asked Questions ==

= Is there any charge for these text messages? =

Unless you have an unlimited text messaging plan, you may be charged by your carrier. All standard carrier text-messaging fees apply. If you are charged for receiving text messages, you will be charged for receiving text messages from ASMSA. Normally, if you have an unlimited texting plan, you will not be charged anything by your carrier for that single text.

= My carrier isn't supported. What do I do? =

ASMSA utilizes a email-to-SMS service that all of the supported carriers provide. If you are able to find this service for your unsupported carrier, it will be happily implemented. Visit the homepage of this plugin to tell me about it or email me directly at sroyalty@premiumdigitalservices.com

= How is it you support Google Voice which has no Gateway for emails? =

ASMSA has implemented a working PHP based form of API for communicating with Google Voice if you have entered valid login credentials for an existing Google Voice account.

= Why do my Google Voice SMS alerts break up into several messages? =

Not only does Google Voice limit the SMS alerts to go to US phone numbers currently, but they also limit messages for the length as well. This is why they are broken down into multiple messages.

= Why do I see 2 messages in my Google Voice inbox? =

This is a side effect of using Google Voice to send a text message to you own Google Voice number. Aside from the double posting in the inbox, this allows it to work as intended and forward this SMS Alert to all phones you need it sent to.
