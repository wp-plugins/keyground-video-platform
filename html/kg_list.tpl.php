<script>
jQuery(document).ready(function() {
	jQuery("div#video_list").ajaxStart(function(){
		jQuery(this).html('<div id="loading"><img src="<?=plugins_url('images/1loading.gif',__FILE__);?>"/> <br/> Loading...</div>');
	});

	var win = window.dialogArguments || opener || parent || top;  
	jQuery('.embed').click(function () {
	    win.send_to_editor(jQuery(this).attr('id'));
	}); 
});
		
function load(divId,action,channelId,page,q)
{
	var post_data = {
			action: action,
			channelId:channelId,
			page:page,
			q:q,
		};

	jQuery.post(ajaxurl, post_data,function(response) {
		jQuery('#'+divId).html(response);
	});
}
</script>

<style>
	#videoList{width:620px;}
	.tablenav-pages{margin-right:20px;}
	.widefat td {padding:4px 4px 4px;}
	td h3{margin-bottom:10px;}
	td input{margin:5px;}
	#loading{width:100px; margin:auto;}
</style>

<div class="tablenav top">
	<div class="alignleft actions">
		<form id="channelSelect">
			<select id="channel" onchange="load('video_list','channelOnChange',jQuery(this).val(),1,'');">
				<option value="0">Select A Channel</option>
				<?php foreach($this->kg->channelList as $channel):?>
					<option value="<?=$channel->id?>"><?=$channel->name?></option>
				<?php endforeach;?>
			</select>
		</form>
	</div>
	<!-- 
	<div class="alignleft actions">
		<form id="channelSelect">
			<input id="search" name="q" value="" />
			<input type="button" name="" id="search-submit" onClick="load('video_list','onSearch','',1,jQuery('#search').val());" class="button-secondary" value="Search">
		</form>
	</div>
	 -->
</div>


<div id="video_list">
	<table class="wp-list-table widefat">
		<thead>
			<tr>
				<th class="manage-column column-title"></th>
				<th class="manage-column column-title"></th>
				<th class="manage-column column-title"></th>
			</tr>
		</thead>
		<?php foreach($this->kg->videoList as $video):?>
		<tr>
			<td><img src="<?=$video->thumb_m?>" width="120" height="68"></td>
			<td><h3><?=$video->title?></h3><p><?=substr(trim($video->description),0,100)?></p></td>
			<td><input type="button" class="embed" id="<?=$this->getShortCode($video->id)?>" value="Embed" /></td>
		</tr>
		<?php endforeach;?>
	</table>
</div>