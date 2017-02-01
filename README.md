# mailorc
A WordPress/MailChimp Integration for Sci-Fi Authors.

## Purpose ##
As a Science Fiction author, you need a way for your subscribers to indicate that they have already read a partiuclar book or books that are being advertised in a MailChimp campaign.  This WordPress plugin provides a convenient way to do that.

## Theory of Operation ##
This plugin offers a subsite settings page so you can specify a MailChimp API key and subsequently select a list from your MailChimp account.  Also, you can select what's called a "Campaign Landing Page" from your WordPress pages.

The settings page then returns a sample url for you to mimic as your write your MailChimp campaigns.  When a subscriber visits this url, they will arrive at the landing page and be added to the interests that you indicate when building the url.

## FAQ ##
1. What about caching?
  * All successful GET requests to the MC API are cached on your server for one hour.  This is why the settings page takes a few seconds to load for the first time.  You can reset the cache by resaving the settings page.

2. What are the scaling limitations?
  * This plugin assumed as maximum of 100 lists, 100 interest categories per list, and 100 interests per interest category.