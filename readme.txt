=== Plugin Name ===
Contributors: Pinoy.ca
Donate link: http://wwf.com/
Tags: recently-viewed, recent
Requires at least: 2.1
Tested up to: 2.9
Stable tag: trunk

Displays the titles of the last x number of posts that readers (other than the current reader) visited on your blog, and the amount of time elapsed since they visited it.

== Description ==

Display the titles of the last `x` number of posts that readers visited on your blog, and the amount of time elapsed since they visited it, in a variety of forms:

* return them as a string of list items.
* print a ul list with a h3 heading and enclosed as a div. 
* as a WordPress widget.

= Rationale =

* Readers are curious what other readers have found interesting to read. That's why RVP is very addictive. Put it on your site where readers can see it and watch your traffic increase.

= Features =

* Insanely fast. It has to be because it needs to run on each page load.
* Creates and uses no tables, writes no files, uses no cookies, loads no css or javascript, needs no plugin initialization.
* Produces XHTML-compliant, semantic markup.
* Each IP is identified by and anonymized with a graphical 10x10 icon using Gravatar.
* Can be modified to record and display the posts' publication date, referer data, search keywords or cookies. Whatever you want or what your Privacy Policy allows.
* Comprehensive API to let you do everything you want without touching the plugin source code.

= Technobabbly features =

* IP addresses are hashed before being stored, so nobody can get them from your database backups. Hashing uses your blog's `SECRET_KEY` where available, to protect against rainbow tables.
* Uses `$_SERVER['HTTP_CLIENT_IP']` or `$_SERVER['HTTP_X_FORWARDED_FOR']` instead of `$_SERVER['REMOTE_ADDR']` where available.
* Uses the WordPress Object Cache. If the posts' data were already retrieved earlier, this plugin will use that instead of querying the database.
* If you list more than 5 items, the plugin retrieves the posts' data in one `wp_query`, instead of individually.
* Uses the WordPress 2.8 Transients API where available.

= Usage =

* `get_recently_viewed_posts( $max_shown = 10 )` returns a string of li's.
* `recently_viewed_posts( $max_shown = 10 )` prints a div
* Configure the widget inside your Widget Admin screen

= Sample markup =

`
<div class="recently-viewed-posts"><h3 class="recently-viewed-posts-header">What others are reading right now</h3><div class="recently-viewed-posts-list"><ul class="recently-viewed-posts-list">
	<li class="recently-viewed-posts-item"><img src="http://www.gravatar.com/avatar/1A2B3C4D5E1A2B3C4D5E1A2B3C4D5E1A2B3C4D5E1A2B3C4D5E.jpg?s=10&amp;d=identicon" alt=" " width="10" height="10" f />&nbsp;<a href="http://www.blog.com/foobar-post/" class="recently-viewed-posts-link">Title of Post</a> <span class="recently-viewed-posts-timespan">3 seconds ago</span></li>
	<li class="recently-viewed-posts-item"><img src="http://www.gravatar.com/avatar/1A2B3C4D5E1A2B3C4D5E1A2B3C4D5E1A2B3C4D5E1A2B3C4D5E.jpg?s=10&amp;d=identicon" alt=" " width="10" height="10" class="recently-viewed-posts-icon" />&nbsp;<a href="http://www.blog.com/foobar-page/" class="recently-viewed-posts-link">Name of Page</a> <span class="recently-viewed-posts-timespan">2 minutes, 15 seconds ago</span></li>
</ul></div></div>
`


= Demo =

http://www.pinoy.ca/eharmony/1616 shows two versions of it in action.

== API ==

Here is the complete set of filters and hooks, so you never have to need to edit the plugin.  How's that for service?

= recently_viewed_posts_cache_set =

Filter applied to the data saved to the RVP cache before it is saved.

*Possible use:* Saving the data to file or to a database.

= recently_viewed_posts_cache_get =

Filter applied to the value retrieved from the RVP cache before it is used.

*Possible use:* Retrieving the data from file or from a database.

= recently_viewed_posts_uninstall_pre =

Action run before uninstallation.

= recently_viewed_posts_new =

Filter applied to the visit's data before processing.  For more information, see the code snippet below.

`
	$item = array( $post->ID, recently_viewed_posts_get_remote_IP(), time() );
	$item = apply_filters( "recently_viewed_posts_new", $item );
	if ( empty( $item ) ) return;
	array_unshift( $recently_viewed_posts, $item );
`

*Possible use:* Saving whatever you like about the visit.

= recently_viewed_posts_entry_format =

Filter applied to the display format of each entry.  For more information, see the code snippet below.

`
	$subject = '<li class="recently-viewed-posts-item"><img src="%ICON%" alt=" " width="10" height="10" class="recently-viewed-posts-icon" />&nbsp;<a href="%URL%" class="recently-viewed-posts-link">%LINK%</a> <span class="recently-viewed-posts-timespan">%TIME% ago</span></li>';
	$subject = apply_filters( "recently_viewed_posts_entry_format", $subject, $item );
`

*Possible uses:* 

* Rewrite the HTML markup of the plugin to whatever you like.
* Process what you saved with `recently_viewed_posts_new`. 
* Display additional information about the visit.

= recently_viewed_posts_entry = 

Filter applied to each visit entry before it is compiled as a list.

*Possible uses:* same as `recently_viewed_posts_entry_format`

= get_recently_viewed_posts =

Filter applied to the output of the `get_recently_viewed_posts` function.

*Possible uses:* same as `recently_viewed_posts_entry_format`

= recently_viewed_posts_time_since =

Filter applied to the text indicating the time duration.

*Possible use:* Displaying additional information other than time.

= recently_viewed_posts_get_remote_IP =

Filter applied to the visit's IP address *after* it has been anonymized and *before* it is used by the plugin.

*Possible use:* Use another way of identifying visitors, such as cookies, Flash cookies, ISPs, etc.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php recently_viewed_posts(); ?>` in your templates, or install the Widget.

== Frequently Asked Questions ==

= Is this fast? =

We made this with speed foremost in mind.  It ought to be as fast if not faster than any visitor tracking or logging plugin out there.  

= What is recorded on visits to Archive pages (such as Author, Tag, Category pages and date archives) or the blog's front page? =

The first or topmost post in that page is recorded.

= Is this a privacy violation? =

Inasmuch as it lets the public see that a reader represented by a certain green squiggly icon visited articles X, Y and Z around 50 seconds apart, yes, it is.  The IP address is encrypted, and no one except the blog's administrator will be able to brute-force and get the reader's IP address.  In that case, the blog admin will probably use the server logs instead.

= I want to see which pages were visited two days ago, can I do that? =

The plugin remembers only the last `MAX_RECENTLY_VIEWED_LINKS`, which is 16 by default.  There are bigger, more flexible visitor tracking and logging plugins that can do what you want.

You can set `MAX_RECENTLY_VIEWED_LINKS` in your wp-config.php, or just edit the plugin file directly.  For example, `define(MAX_RECENTLY_VIEWED_LINKS, 300);` would slow the plugin down. The best value should be 2 or 3 times how many visits you display.

= Why is nothing showing up? =

Remember that it displays what *other readers* visited.  Tell a friend across the country to visit your blog.

= My traffic didn't increase. That's false advertising! =

Did you put it where readers can see it, like at the end of each post?  Using this as a sidebar widget is for cowards.

= How can the plugin ignore my visits? =

Create a handler for the `recently_viewed_posts_new` filter in your theme's `functions.php` that will return `NULL` on your own visits.  For example,

`
add_filter("recently_viewed_posts_new", "my_rvp_ignore_admin_visits");
function my_rvp_ignore_admin_visits( $item ) {
	return current_user_can('manage_options') ? null : $item;
}
`

= How do I customize the output template? =

Create a handler for the `recently_viewed_posts_entry_format` filter in your theme's `functions.php`.  For example, the following removes the icon and adds javascript interaction:

`
add_filter("recently_viewed_posts_entry_format", "my_rvp_format");
function my_rvp_format( $format, $item ) {
	return '<li onmouseover="javascript:dynamo()"><a href="%URL%">%LINK%</a> %TIME% ago</li>';
}
`

Meanwhile, the following uses the icon as the list item image, instead of a bullet:

`
add_filter("recently_viewed_posts_entry_format", "my_rvp_format_2");
function my_rvp_format_2( $format, $item ) {
	return '<li style="list-style-image:url(%ICON%)"><a href="%URL%">%LINK%</a> %TIME% ago</li>';
}
`

Isn't this more flexible (and uses less memory!) than a configuration screen? ;-)

= Can a visitor masquerade as another visitor? =

Of course.  See http://en.wikipedia.org/wiki/IP_address_spoofing for starters.

= Can a reader trace the other readers' IPs from the icon? =

Hashing with a `SECRET_KEY` salt makes this impossible.

= Does it work with WP Super Cache? =

Since the plugin code needs to run on each page load, this plugin will not run when Super Cache is installed and active.  A future version will run in Super Cache half-on mode and another version after that will run in Super Cache full mode.

== Changelog ==

= 2.1.1 =
* Internationalization

= 2.1.0.1 =
* Small bug-fix to http://wordpress.org/support/topic/369416

= 2.1 =
* Plugin API

= 2.0.1 =
* Classes in markup.

= 2.0.0 =
* Support for WordPress 2.8 Transients API where available.  

= 1.0 =
* Unreleased
 
== Upgrade Notice ==

= 2.1.0.1 =
Bug-fix for http://wordpress.org/support/topic/369416 .

= 2.1 =
This version now has a comprehensive API, so you can readily customize the plugin without ever touching the plugin source code.

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.