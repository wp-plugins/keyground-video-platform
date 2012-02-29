<?php
/*
Plugin Name: Keyground Video Platform
Plugin URI: http://wordpress.org/extend/plugins/keyground-video-platform/
Description: Adds Web TV functionality to your wordpress blog. This plugin integrates your wordpress to your keyground account. 
Version: 0.4.5
Author: Keyground
Author URI: http://www.keyground.com
License: GPL2
*/

require_once 'kgsdk/Keyground.php';


new WP_Keyground();

class WP_Keyground
{
	
	public $is_admin;
	public $apiKey;
	public $autoUpdate;
	public $kg;
	public $base_url;
	
	public function __construct()
	{
		//global $current_user;
		
		$this->autoUpdate=get_option("kg_auto_update");
		$this->is_admin = is_admin(); //(bool)current_user_can( 'manage_options' );
		//$this->base_url = plugin_dir_url(__FILE__);
		
		if($this->is_admin){
			$this->initAdmin();
		}
		if(get_option('kg_api_key')){
			$this->apiKey = get_option('kg_api_key');
			$this->initPlugin();
		}
	}
	
	public function initPlugin()
	{
		try {
			$this->kg = new Keyground($this->apiKey);	
		} catch (KeygroundException $e) {
			//echo "Error on Keyground Api : ". $e->getMessage();
		}
		
		
		add_shortcode('keyground', array(&$this,'renderTags'));
		add_filter('the_excerpt','do_shortcode');
		
		
	}
	
	public function initAdmin()
	{
		add_action('save_post', array(&$this,'onSavePost'));
		add_action('admin_menu', array(&$this, 'adminKGMenu'));
		add_action('media_buttons', array(&$this, 'media_buttons'));
		
		//add_filter('media_buttons_context', array(&$this, 'media_buttons'));
		
		add_action('wp_ajax_channelOnChange', array(&$this, 'onChannelRequest'));
		add_action('wp_ajax_onPaginate', array(&$this, 'onPaginate'));
		add_action('wp_ajax_onSearch', array(&$this, 'onSearch'));
		
		if(isset($_GET['action']))
		if($_GET['action']=="videoList"){
			add_action('admin_enqueue_scripts', array (&$this,'load_kg_admin_style'));
		}
	}
	
	public function load_kg_admin_style(){
        wp_register_style( 'keyground', plugins_url('kg-admin.css',__FILE__));
        wp_enqueue_style( 'keyground' );
	}

	
	public function renderTags($attr, $content) 
	{
		$video=$this->kg->getVideo($attr['id']);
		if($attr['type']=="video"){
			//eger ulasip cekemezse ortadaki degeri gosterelim
			if($video){
				return $video->embedCode;
			} else {
				return $content;
			}
		}
	
		if($attr['type']=="description"){
			
			//eger ulasip cekemezse ortadaki degeri gosterelim
			if($video){
				return $video->description;
			} else{
				return $content;
			}
		}
		return "";
	}
	
	public function adminApiNotice()
	{
		echo "<div class='update-nag'>" .'You must provide Keyground API information from "settings" menu.'. "</div>";
	}
	
	public function adminKGMenu()
	{
		add_options_page('Keyground WP Plugin Administration', 'Keyground', 'manage_options', 'keyground', array(&$this, 'adminActions'));
	}
	
	public function media_buttons() {
		$title = __( 'Add Keyground Video', 'keyground' );
		echo "<a href='admin.php?page=keyground&amp;action=videoList&amp;iframe&amp;TB_iframe=true' onclick='return false;' class='thickbox' title='keyground'><img src='".plugins_url('html/images/kg_icon.png',__FILE__)."' alt='keyground' /></a>";
	}
	
	public function adminActions()
	{
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		$q=array();
		parse_str($_SERVER['QUERY_STRING'],$q);
		
		if(array_key_exists('action',$q)){
			if(method_exists($this,$q['action'])){
				call_user_func(array( &$this, $q['action']));
			}
		}
		else $this->showOptions();
	}
	
	public function showOptions()
	{
		include_once('html/admin.tpl.php');
	}
	
	public function saveOptions()
	{
		if($_POST['api_key']!=''){
			if(!get_option('kg_api_key')){
				add_option('kg_api_key',$_POST['api_key'],'','yes');
				echo "Everything ok. All values saved.";
			} else {
				update_option('kg_api_key',$_POST['api_key'],'','yes');
				echo "Everything ok. All values saved.";
			}
			$this->apiKey = $_POST['api_key'];
		}
		
		if(array_key_exists('auto_update', $_POST)){
			if(get_option("kg_auto_update")){
				update_option('kg_auto_update',$_POST['auto_update'],'','yes');
			} else{
				add_option('kg_auto_update',$_POST['auto_update'],'','yes');
			}
			$this->autoUpdate = $_POST['auto_update'];
		} else {
			if(get_option("kg_auto_update")){
				update_option('kg_auto_update',"no",'','yes');
			} else {
				add_option('kg_auto_update',"no",'','yes');
			}
			$this->autoUpdate = '';
		}
		$this->showOptions();
	}
	
	public function resetDialog()
	{
		include_once('html/admin_reset.tpl.php');	
	}
	
	public function reset()
	{
		echo "reset!";
	}
	
	public function videoList()
	{
		include_once('html/kg_list.tpl.php');
	}
	
	public function onChannelRequest()
	{
		if($_POST['channelId']!=''){
			$params = array('channelId' => $_POST['channelId']);
			$this->kg->videoList->find('by_channel_id',$params);
		}
		include_once('html/video_list.tpl.php');
	}
	
	public function onPaginate()
	{
		if($_POST['page']!=''){
			$params = array(
				'channelId' => $_POST['channelId'],
				'page'		=> $_POST['page']
			);
			
			if(isset($_POST['q'])) $params['q'] = $_POST['q']; 
			$this->kg->videoList->find('by_channel_id',$params);
		}
		
		include_once('html/video_list.tpl.php');
	}
	
	public function onSearch()
	{
		if($_POST['q']!=''){
			$params = array(
				'q'		=> $_POST['q'],
				'page'	=> $_POST['page']
			);
			$this->kg->videoList->find('search',$params);
		}
		
		include_once('html/video_list.tpl.php');
	}
	
	public function getShortCode($videoId)
	{
		$video = $this->kg->getVideo($videoId);
		$sc="[keyground type='video' id='".$videoId."' ] ".$video->embed_code."[/keyground] <br/>";
		$sc.=" [keyground type='description' id='".$videoId."' ] ".$video->description."[/keyground]";
		
		return $sc;
	}
	
	public function onSavePost($postId)
	{
		$post = get_post($postId);
		
		$kgAttr = shortcode_parse_atts($post->post_content);
		
		//var_dump(shortcode_parse_atts($post->post_content));
		if($kgAttr["id"]){
			if(wp_is_post_revision($post)){
				//updatePostMeta($post->post_id,'kg_video_id',"hello");
				$video = $this->kg->getVideo($kgAttr["id"]);
				
				$params = array(
					'direct_link' => get_permalink( $post->post_id )
				);
				
				$video->update($params);
				
			}
		}
	}
	
	public function updatePostMeta( $post_id, $field_name, $value = '' )
	{
	    if ( empty( $value ) OR ! $value ){
	        delete_post_meta( $post_id, $field_name );
	    }
	    elseif ( ! get_post_meta( $post_id, $field_name ) ){
	        add_post_meta( $post_id, $field_name, $value );
	    }
	    else{
	        update_post_meta( $post_id, $field_name, $value );
	    }
	}
	
}



