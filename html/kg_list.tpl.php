<script>
function load(divId,action,data)
{
	/*
	jQuery.ajax({
		type: 'POST',
		action: action,
		url:ajaxurl,
		data: data,
		success: function(response) {
			jQuery('#'+divId).html(response);
		}
	});
	*/

	var post_data = {
			action: action,
			data:data
		};

	jQuery.post(ajaxurl, post_data,function(response) {
		jQuery('#'+divId).html(response);
	});
}
</script>

<div class="channel_list">
	<form id="channelSelect">
		<select id="channel" onchange="load('video_list','channelOnChange',jQuery(this).val());">
			<?php foreach($this->kg->channelList as $channel):?>
				<option value="<?=$channel->id?>"><?=$channel->name?></option>
			<?php endforeach;?>
		</select>
	</form>
</div>

<div id="video_list">
	<table class="wp-list-table widefat fixed">
		<thead>
			<tr>
				<th class="manage-column column-title"></th>
				<th class="manage-column column-title">Title</th>
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