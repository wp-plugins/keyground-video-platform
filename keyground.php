<?php
/*
Plugin Name: Keyground
Plugin URI: http://www.keyground.com/plugins/wordpress
Description: Adds Web TV functionality to your wordpress blog. This plugin integrates your wordpress to your keyground account. 
Version: 0.2
Author: Keyground
Author URI: http://www.keyground.com
License: GPL2
*/


require_once 'keygroundwp.class.php';
require_once 'kgChannelsWidget.class.php';
require_once 'libs/functions.php';

$kg = new keyground();



//if(get_post_meta($_GET['p'], 'kg_video_id',true)!="")
//add_filter( "the_content", array('keyground','showVideo') );



//add_filter( "the_content", array('keyground','renderTags') ); 
add_shortcode('keyground', array('keyground','renderTags'));
add_filter( 'the_excerpt', array('keyground','renderTags'));


//add_filter( "the_content", array('keyground','setupPlugin') );



/*if($_GET['page_id']==get_option('kg_page_id'))
add_filter( "the_content", array('keyground','showVideo') );

add_action( 'widgets_init', 'kg_load_widgets' );
*/


/* Function that registers our widget. 
function kg_load_widgets() {
	register_widget('kgChannelsWidget' );
}

*/

if(get_option("kg_auto_update")=="yes")
{
	add_action('admin_menu', array("keyground","applyUpdates"));
}


if(is_admin()){
add_action('admin_menu', 'kg_menu');
}

function kg_menu()
{
//add_submenu_page( "Options", "Keyground WP Plugin Administration", "Keyground", "admin", "keyground", "keyground::showAdmin");
add_options_page('Keyground WP Plugin Administration', 'Keyground', 'manage_options', 'keyground', 'showAdmin');

//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function ) 
//add_submenu_page('options_general.php','Keyground WP Admin','Keyground','manage_options','keyground','showAdmin');

}

function showAdmin()
{

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	include_once('admin/admin.php');

}

function kg_get_header()
{
	echo '<link rel="stylesheet" type="text/css" media="all" href="wp-content/plugins/keyground/html/css/kg-style.css" />';
}

function kg_get_content($content)
{
	
}



?>
