=== Plugin Name ===
Contributors: Pinoy.ca
Donate link: http://wwf.com/
Tags: recently-viewed, recent
Requires at least: 2.1
Tested up to: 2.8.9
Stable tag: trunk

Here is a short description of the plugin.  This should be no more than 150 characters.  No markup here.

== Description ==

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

A few notes about the sections above:

*   "Contributors" is a comma separated list of wp.org/wp-plugins.org usernames
*   "Tags" is a comma separated list of tags that apply to the plugin
*   "Requires at least" is the lowest version that the plugin will work on
*   "Tested up to" is the highest version that you've *successfully used to test the plugin*. Note that it might work on
higher versions... this is just the highest one you've verified.
*   Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
stable.

    Note that the `readme.txt` of the stable tag is the one that is considered the defining one for the plugin, so
if the `/trunk/readme.txt` file says that the stable tag is `4.3`, then it is `/tags/4.3/readme.txt` that'll be used
for displaying information about the plugin.  In this situation, the only thing considered from the trunk `readme.txt`
is the stable tag pointer.  Thus, if you develop in trunk, you can update the trunk `readme.txt` to reflect changes in
your in-development version, without having that information incorrectly disclosed about the current stable version
that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

    If no stable tag is provided, it is assumed that trunk is stable, but you should specify "trunk" if that's where
you put the stable version, in order to eliminate any doubt.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php recently_viewed_posts(); ?>` in your templates

== Frequently Asked Questions ==

= Is this fast? =

We made this with speed foremost in mind.  It ought to be as fast if not faster than any visitor tracking or logging plugin out there.  

= What is recorded on visits to Archive pages (such as Author, Tag, Category pages and date archives) or the blog's front page? =

The first post in that page is recorded.

= Is this a privacy violation? =

Inasmuch as it lets the public see that a reader represented by a certain green squiggly icon visited articles X, Y and Z around 50 seconds apart, yes, it is.  The IP address is encrypted, and no one except the blog's administrator will be able to brute-force and get the reader's IP address.  In that case, the blog admin will probably use the server logs instead.

= I want to see which pages were visited two days ago, can I do that? =

The plugin remembers only the last MAX_RECENTLY_VIEWED_LINKS, which is 16 by default.  There are bigger, more flexible visitor tracking and logging plugins that can do this for you.

You can set MAX_RECENTLY_VIEWED_LINKS in your wp-config.php, or just edit the plugin file directly.  For example,

`define(MAX_RECENTLY_VIEWED_LINKS, 300);`

would slow the plugin down. The best value should be 2 or 3 times how many visits you display.

= Why is nothing showing up? =

Remember that it displays what OTHER READERS visited.  Tell a friend across the country to visit your blog.

= Can a visitor masquerade as another visitor? =

Of course.  See (http://en.wikipedia.org/wiki/IP_address_spoofing http://en.wikipedia.org/wiki/IP_address_spoofing) for starters.

= Does it work with WP Super Cache? =

Since the plugin code needs to run on each page load, this plugin will not run when Super Cache is installed and active.  A future version will run in Super Cache half-on mode and another version after that will run in Super Cache full mode.

== Changelog ==

= 2.0.0 =
* Support for WordPress 2.8 Transients API where available.

= 1.0 =
* Unreleased
 
== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`