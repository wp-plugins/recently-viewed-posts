<?php
/*
Plugin Name: Recently Viewed Posts
Version: 2.0.1
Plugin URI: http://www.pinoy.ca/
Description: Show "What others are reading now" links on your page. 
Author: Pinoy.ca 
Author URI: http://www.pinoy.ca/

Copyright ( c ) 2009
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

    This file is part of WordPress.
    WordPress is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    ( at your option ) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

	INSTALL: 
	Unzip Plugin, upload to wp-content/plugins folder, activate, then add this line wherever on your theme:
	<?php if ( function_exists( 'recently_viewed_posts' ) ) recently_viewed_posts(); ?> 

	Plugin settings are set in your widget gallery.
	
	LEGAL:
	GPL Copyright as above, Tested on WP 2.4+, No warranties promised or implied, use at your own risk
	
	TROUBLESHOOT:
	Uncomment the line "recently_viewed_posts_uninstall()" to reset the thing!

*/

// Uncomment the next line to reset the thing!
// recently_viewed_posts_uninstall();

// Max number of links to keep
if ( !defined( MAX_RECENTLY_VIEWED_LINKS ) )
	define( MAX_RECENTLY_VIEWED_LINKS, 16 );

add_action( 'wp_footer', 'add_to_recently_viewed_posts' );

function recently_viewed_posts_cache_set( $value = null ) {
	$value = apply_filters("recently_viewed_posts_cache_set", $value);
	// use WP2.8 Transients API if available
	if ( function_exists( 'set_transient' ) ) 
		set_transient( 'recently_viewed_posts', $value, $expiration = 0 );
	else if ( get_option( 'recently_viewed_posts' ) )
		update_option( 'recently_viewed_posts', $value );
	else
		set_option( 'recently_viewed_posts', $value );
}

function recently_viewed_posts_cache_get() {
	// use WP2.8 Transients API if available
	$get = function_exists( 'get_transient' ) ? get_transient( 'recently_viewed_posts' ) :
		get_option( 'recently_viewed_posts' );
	return apply_filters("recently_viewed_posts_cache_get", $get);
}

function recently_viewed_posts_uninstall() {
	// use WP2.8 Transients API if available
	do_action("recently_viewed_posts_uninstall_pre");
	return function_exists( 'delete_transient' ) ? delete_transient( 'recently_viewed_posts' ) :
		delete_option( 'recently_viewed_posts' );
}

// add [ID, IP, time] set to end of 'recently_viewed_posts' wordpress option, unless already there.
function add_to_recently_viewed_posts() {
	global $post;

	// get existing list
	$recently_viewed_posts = recently_viewed_posts_cache_get();
	if ( !$recently_viewed_posts || !is_array( $recently_viewed_posts ) ) 
		$recently_viewed_posts = array();
	// remove if there, then add
	foreach ( $recently_viewed_posts as $key => $recently_viewed_post ) 
		if ( is_array( $recently_viewed_post ) && $recently_viewed_post[0] == $post->ID ) {
			unset( $recently_viewed_posts[$key] ); 
			break;
		}
	$data = array( $post->ID, recently_viewed_posts_get_remote_IP(), time(), $_SERVER['HTTP_REFERER'] );
	array_unshift( $recently_viewed_posts, apply_filters( "recently_viewed_posts_new", $data ) );

	// make sure we only keep MAX_RECENTLY_VIEWED_LINKS number of links
	if ( count( $recently_viewed_posts ) > MAX_RECENTLY_VIEWED_LINKS ) 
		$recently_viewed_posts = array_slice( $recently_viewed_posts, 0, MAX_RECENTLY_VIEWED_LINKS );
	// save
	recently_viewed_posts_cache_set( $recently_viewed_posts );
}

	
// returns <li> list of recently viewed posts, excluding visits by this visitor
function get_recently_viewed_posts( $max_shown = 10 ) {

	/* Works out the time since the entry post, takes a an argument in unix time ( seconds ) */
	function recently_viewed_posts_time_since( $original ) {
		// array of time period chunks
		$chunks = array( 
			array( 60 * 60 * 24 * 365 , 'year' ),
			array( 60 * 60 * 24 * 30 , 'month' ),
			array( 60 * 60 * 24 * 7, 'week' ),
			array( 60 * 60 * 24 , 'day' ),
			array( 60 * 60 , 'hour' ),
			array( 60 , 'minute' ),
		array( 1, 'second' ),
		 );
		
		$today = time(); /* Current unix time  */
		$since = $today - $original;
		
		// $j saves performing the count function each time around the loop
		for ( $i = 0, $j = count( $chunks ); $i < $j; $i++ ) {
			
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			
			// finding the biggest chunk ( if the chunk fits, break )
			if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
				// DEBUG print "<!-- It's $name -->\n";
				break;
			}
		}
		
		$print = ( $count == 1 ) ? '1 '.$name : "$count {$name}s";
		
		if ( $i + 1 < $j ) {
			// now getting the second item
			$seconds2 = $chunks[$i + 1][0];
			$name2 = $chunks[$i + 1][1];
			
			// add second item if it's greater than 0
			if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
				$print .= ( $count2 == 1 ) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}
		return $print;
	}

	if ( $max_shown + 0 > 0 );
	else $max_shown = 10;
	
	$recently_viewed_posts = recently_viewed_posts_cache_get();
	if ( !$recently_viewed_posts && !is_array( $recently_viewed_posts ) ) 
		return "";
	$html = "";
	$count = 0;
	
	$html .= "<!-- BUFFER:" . count( $recently_viewed_posts ) . "-->";

	// run a WP_Query so that the get_permalinks and get_the_titles don't cause individual queries.
	if ( $max_shown > 5 ) { // guesstimate threshold
		foreach ( $recently_viewed_posts as $item ) 
			if ( $item[1] != recently_viewed_posts_get_remote_IP() ) {
				$post_in[] = $item[0];
				if ( ++$count == $max_shown ) 
					break;  // i've shown enough
			}
		new WP_Query( array( 'post__in' => $post_in, 'posts_per_page' => 10000 ) ); 
	}
	
	$count = 0;
	foreach ( $recently_viewed_posts as $item ) 
		if ( $item[1] != recently_viewed_posts_get_remote_IP() ) {
			$search = array( 
				"%ICON%", 
				"%URL%",
				"%LINK%",
				"%TIME%"
			);
			$replace = array(
				"http://www.gravatar.com/avatar/{$item[1]}.jpg?s=10&amp;d=identicon",
				get_permalink( $item[0] ),
				get_the_title( $item[0] ),
				recently_viewed_posts_time_since( $item[2] )
			);
			$subject = '<li class="recently-viewed-posts-item"><img src="%ICON%" alt=" " width="10" height="10" class="recently-viewed-posts-icon" />&nbsp;<a href="%URL%" class="recently-viewed-posts-link">%LINK%</a> <span class="recently-viewed-posts-timespan">%TIME% ago</span></li>';
			$entry = str_replace( $search, $replace, $subject );
			$html .= apply_filters( "recently_viewed_posts_entry", $entry );
			if ( ++$count == $max_shown ) 
				break;  // i've shown enough
		}
		
	return apply_filters( "recently_viewed_posts", $html );
}

// Okay, now define as widget
function widget_recently_viewed_posts_init() {
	// Direct from "Google Search widget" template 1.0 by Automattic, Inc.
	if ( !function_exists( 'register_sidebar_widget' ) ) 
		return;

	function widget_recently_viewed_posts( $args ) {
		extract( $args );

		$options = get_option( 'widget_recently_viewed_posts' );
		$title = $options['title'];
		$max_shown = $options['max_shown'];

		echo $before_widget . $before_title . $title . $after_title;
		echo '<ul>'.get_recently_viewed_posts( $max_shown ).'</ul>';
		echo $after_widget;
	}

	function widget_recently_viewed_posts_control() {
		$options = get_option( 'widget_recently_viewed_posts' );
		if ( !is_array( $options ) )
			$options = array( 'title'=>'What people are reading right now', 'max_shown'=>10 );
		if ( $_POST['recently_viewed_posts-submit'] ) {
			$options['title'] = strip_tags( stripslashes( $_POST['recently_viewed_posts-title'] ) );
			$options['max_shown'] = strip_tags( stripslashes( $_POST['recently_viewed_posts-max_shown'] ) );
			update_option( 'widget_recently_viewed_posts', $options );
		}

		$title = htmlspecialchars( $options['title'], ENT_QUOTES );
		$max_shown = htmlspecialchars( $options['max_shown'], ENT_QUOTES );
		
		echo '<p style="text-align:right;"><label for="recently_viewed_posts-title">' . __( 'Title:' ) . ' <input style="width: 200px;" id="recently_viewed_posts-title" name="recently_viewed_posts-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="recently_viewed_posts-max_shown">' . __( 'Number to display:' ) . ' <input style="width: 200px;" id="recently_viewed_posts-max_shown" name="recently_viewed_posts-max_shown" type="text" value="'.$max_shown.'" /></label></p>';
		echo '<input type="hidden" id="recently_viewed_posts-submit" name="recently_viewed_posts-submit" value="1" />';
	}

	register_sidebar_widget( array( 'Recently Viewed Posts', 'widgets' ), 'widget_recently_viewed_posts' );

	register_widget_control( array( 'Recently Viewed Posts', 'widgets' ), 'widget_recently_viewed_posts_control', 300, 100 );
}
add_action( 'widgets_init', 'widget_recently_viewed_posts_init' );

// Finally, not as a widget. 
function recently_viewed_posts( $max_shown = 5 ) {
	$html = get_recently_viewed_posts( $max_shown );
	if ( $html && ( $html != "" ) ) {
		echo '<div class="recently-viewed-posts"><h3 class="recently-viewed-posts-header">What others are reading right now</h3><div class="recently-viewed-posts-list"><ul class="recently-viewed-posts-list">';
		echo $html;
		echo "</ul></div></div>";
	}
}

// Get visitor's real IP, hashed
function recently_viewed_posts_get_remote_IP() {
	if ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) )   //check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'].$SECRET_KEY;
	if ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )   //to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$ip = $_SERVER['REMOTE_ADDR'];
	if ( defined(SECRET_KEY) )
		$md5 = md5( ip2long( $ip ) . SECRET_KEY );
	else
		$md5 = md5( ip2long( $ip ) );
	return apply_filters( "recently_viewed_posts_get_remote_IP", $md5 );
}

?>