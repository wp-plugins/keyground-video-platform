<?php
/*
 * Api Version: 0.3
 * Lib Version: 0.4
 * 
 * @
 * 
 */


class kg
{

	private $api_key;
	private $api_url;
	
	function __construct($api_key)
	{
		
		//Change this params with yours

		$this->api_key=$api_key;
		$this->api_url='http://api.keyground.net/0.3.0/api.php';
	}
	










	
	function sendRequest($cmd,$params = null)
	{
			
		$post_data = array (

			'api_key'	=> $this->api_key,
			'cmd'		=> $cmd
		);
		
		if(is_array($params)){
			$post_data=array_merge($post_data, $params);
			
			
			/*
			foreach($params as $key => $value){
				$post_data[$key]=$value;
			}
			*/
		}
		//var_dump($post_data);
		
		$ch = @curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$response       = curl_exec($ch);	
		$errno          = curl_errno($ch);
		$error          = curl_error($ch);
		
		if($error!=''){
			//echo $error;
			return $response;
		} else {
			//echo $response;
			return $this->xmlToObject($response);	
		}	
	}
	
	function xmlToObject($xml){
	
		$obj=simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);
		return $obj;
	}
}




//$kg = new kg("");




//$kgVideos = $kg->sendRequest("getVideos","");
//$kgVideos = $kg->sendRequest("getChannels","");
//$kg->sendRequest("getVideoDetails",array("videoId"=>"4cb1e068b0f55_4db8a813791fa"));

//$kg->sendRequest("getVideos",array("lastModified"=>"2010-07-26 15:37:35"));

//$kg->sendRequest("getVideos",array("lastModified"=>"2010-07-26 15:37:35","order"=>"lastModified ASC","pagination"=>"no" ));

//$kg->sendRequest("getVideos", array("lastModified"=>"2010-07-26 15:37:35"));
//$kg->sendRequest("getVideos",array("pagination"=>"no" ));


//echo $result;



/*
echo "========  Channels ==============<br/>";


$kg = new kg();
$data=$kg->getChannels();
var_dump($data);
if($data->error==''){
	foreach($data->channels->channel as $channel){
		echo $channel->id.':'.$channel->title.'<br>';
	}
}
else echo " ERROR: ". $data->error;


*/
/*
echo "========  Videos of Channel 162 ==============<br/>";
$kg = new kg();
$data=$kg->getVideos("162");
var_dump($data);
if($data->error==''){
	foreach($data->videos->video as $video){
		echo $video->id.':'.$video->title.'<br>';
	}
}
else echo $data->error;
*/
/*
echo "========  Info of video 4cb1e068b0f55_4cb1f83eda1c1 ==============<br/>";

$data=getVideo($api_user, $api_key, "4cb1e068b0f55_4cb1f83eda1c1");
//pre_print($data);

if($data->error==''){

	echo $data->video->id.':'.$data->video->title.'<br/>';
	echo $data->video->embed_code;

}
else echo $data->error;

*/
