<?php
/*
Plugin Name: WP-Cirip
Plugin URI: http://ezbitz.com/wp-cirip/
Description: An integration between your WordPress blog and Cirip.ro. Passes your blog posts to Cirip.	
Version: 1.2
Author: The Chef
Author URI: http://ezbitz.com
*/

define ( "cirip_pre", "cirip" );

require_once (ABSPATH . '/wp-config.php');
require_once 'APIclient.php';

function cirip_admin()
{
	include ('wp-cirip-admin.php');
}

function cirip_admin_actions()
{
	add_options_page ( "Cirip settings", "Cirip settings", 1, "Cirip settings", "cirip_admin" );
}
add_action ( 'admin_menu', 'cirip_admin_actions' );

function cirip_post($PostId)
{
	$post = get_post ( $PostId );
	$cirip_user = get_option ( cirip_pre . "_user" );
	$cirip_pass = get_option ( cirip_pre . "_pass" );
	
	$ciripAPI = new ciripAPIclient ( $cirip_user, $cirip_pass );
	$ciripAPI->updateStatus ( "xml", "New blog post:" . $post->post_title . " [" . get_permalink ( $PostId ) . "]" );
}

add_action ( 'publish_post', 'cirip_post' );

?>