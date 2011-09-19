<?php
require_once 'libs/kgsdk/0.3.0/kglib.class.php';

#new
	require 'libs-new/functions.php';
#	$api_key = '4f6b5b32c1c4771df3af44c346f524a5babdca4e';

if(is_file('wp-admin/includes/taxonomy.php')) include_once 'wp-admin/includes/taxonomy.php';
else include_once 'includes/taxonomy.php';

class keyground
{
	public $channels;
	public $kg;
	
	function __construct()
	{
		$this->init();
	}
	
	function init()
	{
		$api_key=get_option('kg_api_key');

		
		if($api_key==''){
			
			add_action( 'admin_notices', array("keyground","adminNotice"));
			
			
		} else {
			$this->kg = new kg(get_option('kg_api_key'));
			#new
			$this->api_key=$api_key;
		}
		
		
	}
	
	function adminNotice()
	{
		echo "<div class='update-nag'>" .'You must provide Keyground API information from "settings" menu.'. "</div>";
	}
	
	
	function showVideo()
	{
	
		$kg = new kg(get_option('kg_api_key'));
		
		if($_GET['vid']!=''){
			$videoId=$_GET['vid'];
		
			$data = $kg->getVideo($videoId);
			$video=$data->video;
		
			include 'html/play.tpl.php';
			
		} else if($_GET['ch']!=''){
			$channel_id=$_GET['ch'];
			$data=$kg->getVideosByChannel($_GET['ch']);
		
			$videos = $data->videos->video;
	
			$page_link=get_page_link(get_option('kg_page_id'));
			
			include 'html/container_channel_videos.tpl.php';			
		} else {
			kg_get_last_videos();
		}
	}
	


	function showAdmin()
	{
		include('admin.php');
	}
	


	function renderTags($attr, $content) 
	{

		$kg = new kg(get_option('kg_api_key'));
	
		if($attr['type']=="video")
		{
			
			//$kg->sendRequest("getVideoDetails",$params);
			$data = $kg->sendRequest("getVideoDetails",array("videoId"=>$attr['id']));
			$video=$data->video->embed_code;
			if($video)//eger ulasip cekemezse ortadaki degeri gosterelim
			{
				return $video;
			}
			else
			{
				return $content;
			}	
				//include 'html/play.tpl.php';
			
			
			
		}
	
		if($attr['type']=="description")
		{
		//description
		
	
			$data = $kg->sendRequest("getVideoDetails",array("videoId"=>$attr['id']));
			$video=$data->video->description;
			
			if($video) //eger ulasip cekemezse ortadaki degeri gosterelim
			{
				return $video;
			}
			else
			{
				return $content;
			}
			//include 'html/play.tpl.php';
		}
		    return "";
	}

	
	
	function checkUpdates() 
	{

		if(!get_option("kg_last_update"))
		{
			add_option('kg_last_update',"00-00-00 00:00:00", '', 'yes'); 
		}
		
		if( !get_option('kg_api_key'))
		{
			return;
		}
		
		$kg = new kg(get_option('kg_api_key'));
		
		
#		$data=sendRequest($api_key, 'getChannels');
#		$channels=$data->channels->channel;
		
		$result=$kg->sendRequest("getChannels",array("lastModified"=>get_option("kg_last_update"),"pagination"=>"no"));
		//kanallara bak
		$updated_channels=$result->channels->channel;
		$i=0;
		foreach ($updated_channels as $channel)
		{
			$channelIds[$i]["id"]=(string)$channel->id;
			$channelIds[$i]["name"]=(string)$channel->name; //detay olarak tek tek cekmeye gerek yok
			$i++;
		}
		
		
		$result=$kg->sendRequest("getVideos",array("lastModified"=>get_option("kg_last_update"),"order"=>"lastModified ASC","pagination"=>"no" )); //hepsini cek
		//videolara bak
		
		/*echo "CHECK";
		var_dump($result->videos);
		echo "/chk <br>";
		*/
		
		$updated_videos=$result->videos->video;
		$i=0;
		foreach ($updated_videos as $video)
		{
			$videoIds[$i]=(string)$video->id;
			$i++;
		}
		
		
		
		
		$updates=array("channels"=>$channelIds,"videos"=>$videoIds);
		
		return $updates;
	}
	

	function applyUpdates()
	{
		if(!get_option('kg_api_key')){ return;}
		
		$api = new kg(get_option('kg_api_key'));
				
		$updates=self::checkUpdates();
		
		//ilk versiyonda check updates'i burada kullanırız, ileride checkbox ile kullanabiliriz TODO
		
		//KANALLAR önce update edilmesi önemli
		if($updates["channels"])
		{
			/*var_dump($updates["channels"]);
			echo "<br><br>";
			var_dump($updates["videos"]);
			echo "<br><br>";
			*/
			
			$channelToCategory=json_decode(get_option("kg_channels"),true); //jsondecode
			
			//echo get_option("kg_channels")."<br>";
			//eger root keyground kategorisi yoksa olusturalım
			
			if($channelToCategory["0"]==""){
					
				$result=wp_insert_term(
				  'Keyground Videos', // the term 
				  'category', // the taxonomy
				  array(
				    'description'=> ''//,
				   // 'parent'=> $parent_term_id
				  	)
				);
				if(is_array($result))
				{
					$root_cat_id=$result['term_id'];
					$channelToCategory["0"]=$root_cat_id;
				}
				else
				{
					echo "errors on wp_insert_term(2)<br>";
					print_r($result);
				}
			
			}
			
			
			/* 
			echo "CHANNEL TO CAT:";
			var_dump($channelToCategory);
			echo "<br>JSON:".get_option("kg_channels");
			*/
			
			foreach($updates["channels"] as $ch)
			{
				
				/*echo "<br><br>***".$ch["id"];*/
				//update veya insert
				// array icinde $ch id keyini ara 
				if($termId=$channelToCategory[$ch["id"]]) // json olarak kayitli
				{
					/* echo "--".$termId."--"; */
					$term=get_term( $termId, "category" ); //peki kategorilerden silindi mi ?
					
					if($term && $term->term_id!=1) //get term bulamayinca uncat getiriyor, o yüzden böyle yapmalıyız.
					{
						//var_dump($term);
						wp_update_term($termId,"category",array( //parent update ile degismesine gerek yok.
							'name'=> $ch["name"]
						));
											
						/* echo "kategori degisti->".$termId."---".$ch["name"]; */
					}
					else //kategorilerden silinmis güncelleyelim.
					{
						unset($channelToCategory[$ch["id"]]);
						/* echo "kategori silinmiş options'a uygulandı.->".$termId."---".$ch["name"]; */					}
				}
				else
				{
					$result=wp_insert_term( 
						$ch["name"], // the term //
						'category', // the taxonomy
						array(
							'description'=> '',
					  		 'parent'=> $channelToCategory["0"]
					  	)
					);
					if(is_array($result))
					{
						$channelToCategory[$ch["id"]]=$result['term_id'];
						/* echo "kategori degisti->".$termId."---".$ch["name"]; */
					}
					else
					{
						//ERROR
						/*echo "errors on wp_insert_term<br>"; */
					}
					

					
					
				}
				
			}
			
		//bitmeden once update_options	
		update_option('kg_channels', json_encode($channelToCategory) ,'','yes');
		}

		
		
		if($updates["videos"])
		{ 
			foreach($updates["videos"] as $videoId)
			{
				//video ISE detaylarini alalim.
				$result=$api->sendRequest("getVideoDetails",array("videoId"=>$videoId));
				
				//yeni yazı VS. update (wp_query olabilir)	
				$the_query = new WP_Query( array( 'meta_key' => 'kgvideo_id' , 'meta_value' => $videoId) );
				$posts=$the_query->get_posts();
				
				if(count($posts)>0)  //UPDATE
				{
					
					foreach($posts as $post)
					{
						//$post->ID;	
						
						$i=0;
						$tags=array();
						foreach($result->video->tags->tag as $tag)
						{
							
							if($tag!="")
							{
								$tags[$i]= (string)$tag;
								$i++;
							}

						}
						
						$update_post = array(
							'ID' => $post->ID,
							'post_category' => array($channelToCategory[(string)$result->video->channelId ]),
						//	'post_content' => $content ,
							'post_title' => (string)$result->video->title ,
							'tags_input' => $tags
						);
						
						/*echo "<br><br>TAGLER:";
						var_dump( $tags);
						echo "/tagler";*/
						
						wp_update_post( $update_post );
						
						// Short codelarin icini ileride degistirecegiz. ?
						
						/* echo "<br><br> post lastModified->".(string)$result->video->lastModified; */
						
					}
				}			
				else //INSERT
				{
					

					
					//content insaasi 
					$content='[keyground type="video" id="'.$videoId.'" ] '.(string)$result->video->embed_code."[/keyground] ";
					$content .=' [keyground type="description" id="'.$videoId.'" ] '.(string)$result->video->description."[/keyground] ";
					
					//tag ayari
						/*$tagArray=explode(",", $result->video->tags);
						var_dump($result->video->tags);
						
						foreach($tagArray as $tag){$tag="<".$tag.">";}
						$tagler= implode(",", $tagArray);*/
					
					
					//kategori
					$channelid=(string)$result->video->channelId;
					
					
					
					$i=0;
					foreach($result->video->tags->tag as $tag)
					{
						if($tag!="")
						{
							$tags[$i]= (string)$tag;
							$i++;
						}
		
					}
					
					$new_post = array(
						'post_category' => array($channelToCategory[$channelid]),
						'post_content' => $content ,
						//'post_date' =>  ,
						'post_title' => (string)$result->video->title ,
						'tags_input' => $tags, 
						'post_type' => 'post',
						'post_status' => 'publish'
					);
					
					
					$the_post_id = wp_insert_post( $new_post );
					
					/*var_dump($the_post_id);
					*/
					self::__update_post_meta( $the_post_id, 'kgvideo_id', $videoId );	
					
				}
				
			update_option('kg_last_update',(string)$result->video->lastModified, '', 'yes'); 
			}
		}
		
		
	}
	

	function setupPlugin()
	{
		$api = new kg(get_option('kg_api_key'));
		#new
		$api_key= get_option('kg_api_key');
		
		
		//api key api user kontrolu
		$result = $api->sendRequest("getVideos",$params=null);
		if ($result->error)
		{
			return $content=$result->error;
		}
		
		if(get_option('kg_last_update'))
		{
			delete_option('kg_last_update');
		}
		
	
		
		
		
		if($kg_channels_json=get_option('kg_channels'))
		{
			$kg_channels=json_decode($kg_channels_json,true);
			
			/*var_dump($kg_channels);
			*/
			
			foreach($kg_channels as $category)
			{
				wp_delete_term( $category, 'category' );
			}
			
		}

		
		//RESETLENDI
		
		//videolar ana category'i olusturmaliyiz
		$result=wp_insert_term(
		  'Keyground Videos', // the term 
		  'category', // the taxonomy
		  array(
		    'description'=> ''//,
		   // 'parent'=> $parent_term_id
		  	)
		);
		if(is_array($result))
		{
			$root_cat_id=$result['term_id'];
			$categories["0"]=$root_cat_id;
		}
		else
		{
			echo "errors on wp_insert_term(3)<br>";
			print_r($result);
		}
		
		//channelları alt kategori olarak ekleyelim.
	
		#old
		#$result=$api->sendRequest("getChannels",""); //id name s_description description getirecek
		#$kanallar=$result->channels->channel;
		
		$data=sendRequest($api_key, 'getChannels');
		$channels=$data->channels->channel;
		
		foreach($channels as $channel)
		{ 
						
			
			$result=wp_insert_term(
			  (string)$channel->name, // the term 
			  'category', // the taxonomy
			  array(
			    'description'=> (string)$channel->description,
			    'parent'=> $categories["0"]
			  	)
			);

			if(is_array($result))
			{
				$cat_id=$result['term_id'];
				$categories[(string)$channel->id]=$cat_id;
			}
			else
			{
				echo "errors on wp_insert_term(1)<br>";
				print_r($result);
			}
			
	
				
		}
		
		
		$value=json_encode($categories);
		
		/*echo "***".$value."***";
		*/
		
		if(get_option('kg_channels')) { update_option('kg_channels', $value ,'','yes'); }
		else {add_option('kg_channels', $value ,'','yes');}
		
	
		//kanal verisini degiskene atayip, kanal ile ilgili islemleri bitiriyoruz.
		$categories=json_decode(get_option('kg_channels'),true);
		
		// + cache temizlemek gerekiyormus
		clean_term_cache($categories, "category");
		_get_term_hierarchy('category');
		/*echo "category cache cleaned?";
		*/
		
		
		
		
		/*
		echo "<br><br>";
		var_dump($categories);
		echo "<br><br>";
		*/
		
		//wordpressteki iliskili tum postlari bulalim.
		$the_query = new WP_Query( array( 'meta_key' => 'kgvideo_id' , 'posts_per_page' => '-1') );
		$posts=$the_query->get_posts();
		
		if(count($posts)>0)
		{
			$i=0;
			foreach($posts as $post)
			{
				$postIds[$i]=$post->ID;
				$i++;
			}
		}
		//debug
		//print_r($postIds);
		
		if(count($posts)>0)
		{
			foreach($postIds as $postId)
			{
				wp_delete_post( $postId, true );
			}

			//var_dump($postIds);
		}


		//keygrounddan postlari ekle  // HEPSİNİ ÇEK
		$kgVideos = $api->sendRequest("getVideos",array("order"=>"lastModified ASC","pagination"=>"no"));
		
		$videos=$kgVideos->videos->video; //xmltoObject calisma mantigi
		
		//var_dump($videos);
		
		$i=0;
		foreach($videos as $obj)
		{
			$videoIds[$i]= $obj->id ;
			$i++;
		}
		
		/*var_dump($videoIds);
		*/
		
		
		
		add_option('kg_last_update',"00-00-00 00:00:00", '', 'yes'); 
		
		foreach($videoIds as $videoId)
		{
			$videoId=(string)$videoId;
			
			$result=$api->sendRequest("getVideoDetails",array("videoId"=>$videoId));
			$video= $result->$video;
		
			
			//var_dump($result->video->id[0]);
			/*echo "<br><br>***".(string)$result->video->id;
			*/
			
			//content insaasi 
			$content='[keyground type="video" id="'.$videoId.'" ] '.(string)$result->video->embed_code."[/keyground] ";
			$content .=' [keyground type="description" id="'.$videoId.'" ] '.(string)$result->video->description."[/keyground] ";
			
			//tag ayari
				/*$tagArray=explode(",", $result->video->tags);
				var_dump($result->video->tags);
				
				foreach($tagArray as $tag){$tag="<".$tag.">";}
				$tagler= implode(",", $tagArray);*/
			
			
			//kategori
			$cat=(string)$result->video->channelId;
			
			
			$tags=array();
			$i=0;
			foreach($result->video->tags->tag as $tag)
			{
				if($tag!="")
				{
					$tags[$i]= (string)$tag;
					$i++;
				}

			}
			
			$new_post = array(
				'post_category' => array($categories[$cat]),
				'post_content' => $content ,
				//'post_date' =>  ,
				'post_title' => (string)$result->video->title ,
				'tags_input' => $tags, 
				'post_type' => 'post',
				'post_status' => 'publish'
			);
			
			
			$the_post_id = wp_insert_post( $new_post );
			
			/*var_dump($the_post_id);
			*/
			self::__update_post_meta( $the_post_id, 'kgvideo_id', $videoId );
			
			update_option('kg_last_update',(string)$result->video->lastModified, '', 'yes'); 
		}
		
//"2011-07-26 15:37:35"
		
		
	//	add_option('kg_last_update',date("Y-m-d H:i:s"), '', 'yes'); 
	//	echo "---".date("Y-m-d H:i:s");
		
		return;
	}
	
	
	

/*
	function WPQuery($content = '')
	{
		$query = new WP_Query(array( 'meta_key' => 'kgvideo_id') );
		$posts= $query->get_posts();
		return var_export($posts, true);
	}
*/



	function mktimestamp($date)
	{
		
		$date_array = explode(" ",$date);
		
		$day=explode('-',$date_array[0]);
		$time=explode(':',$date_array[1]);
		
		//echo "time: ".$time[0].' '.$time[1].' '.$time[2].' '.$day[1].' '.$day[2].' '.$day[0];
		
		return mktime($time[0],$time[1],$time[2],$day[1],$day[2],$day[0]);
		
	}
	
	
	
	public function __update_post_meta( $post_id, $field_name, $value = '' )
	{
	    if ( empty( $value ) OR ! $value )
	    {
	        delete_post_meta( $post_id, $field_name );
	    }
	    elseif ( ! get_post_meta( $post_id, $field_name ) )
	    {
	        add_post_meta( $post_id, $field_name, $value );
	    }
	    else
	    {
	        update_post_meta( $post_id, $field_name, $value );
	    }
	}
	
	public function object_to_array($object)
	{
		if(is_array($object) || is_object($object))
		{
			$array = array();
			foreach($object as $key => $value)
		 	{
		 		$array[$key] = self::object_to_array($value);
			}
			return $array;
		}
		return $object;
	}
		

}

