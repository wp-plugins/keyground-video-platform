<script>
jQuery(document).ready(function() {
	var win = window.dialogArguments || opener || parent || top;  
	jQuery('.embed').click(function () {
	    win.send_to_editor(jQuery(this).attr('id'));
	}); 
});
</script>
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


