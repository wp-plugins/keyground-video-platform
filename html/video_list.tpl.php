<div class="tablenav top">
	<div class="tablenav-pages" style="margin-right:15px;">
		<span class="displaying-num"><?=$this->kg->videoList->objectCount?> Items</span>
		
		<span class="pagination-links">
			<a class="first-page <?php if($this->kg->videoList->page==1) echo "disabled"; ?>" title="Go to the first page" href="#" 
				onClick="load('video_list','onPaginate','<?=$this->kg->videoList->params['channelId']?>','1');">«</a>
				
			<a class="prev-page <?php if($this->kg->videoList->page==1) echo "disabled"; ?>" title="Go to the previous page" href="#" 
				onClick="load('video_list','onPaginate','<?=$this->kg->videoList->params['channelId']?>','<?=$this->kg->videoList->page-1?>');">‹</a>
				
			<span class="paging-input"><?=$this->kg->videoList->page?> of <span class="total-pages"><?=$this->kg->videoList->page_count?></span></span>
			
			<a class="next-page <?php if($this->kg->videoList->page==$this->kg->videoList->page_count) echo "disabled"; ?>" title="Go to the next page" href="#" 
				onClick="load('video_list','onPaginate','<?=$this->kg->videoList->params['channelId']?>','<?=$this->kg->videoList->page+1?>');">›</a>
				
			<a class="last-page <?php if($this->kg->videoList->page==$this->kg->videoList->page_count) echo "disabled"; ?>" title="Go to the last page" href="#" 
				onClick="load('video_list','onPaginate','<?=$this->kg->videoList->params['channelId']?>','<?=$this->kg->videoList->page_count?>');">»</a></span>
	</div>
</div>
					
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

<div class="tablenav top">
	<div class="tablenav-pages" style="margin-right:15px;">
		<span class="displaying-num"><?=$this->kg->videoList->objectCount?> Items</span>
		
		<span class="pagination-links">
			<a class="first-page <?php if($this->kg->videoList->page==1) echo "disabled"; ?>" title="Go to the first page" href="#" 
				onClick="load('video_list','onPaginate','<?=$this->kg->videoList->params['channelId']?>','1');">«</a>
				
			<a class="prev-page <?php if($this->kg->videoList->page==1) echo "disabled"; ?>" title="Go to the previous page" href="#" 
				onClick="load('video_list','onPaginate','<?=$this->kg->videoList->params['channelId']?>','<?=$this->kg->videoList->page-1?>');">‹</a>
				
			<span class="paging-input"><?=$this->kg->videoList->page?> of <span class="total-pages"><?=$this->kg->videoList->page_count?></span></span>
			
			<a class="next-page <?php if($this->kg->videoList->page==$this->kg->videoList->page_count) echo "disabled"; ?>" title="Go to the next page" href="#" 
				onClick="load('video_list','onPaginate','<?=$this->kg->videoList->params['channelId']?>','<?=$this->kg->videoList->page+1?>');">›</a>
				
			<a class="last-page <?php if($this->kg->videoList->page==$this->kg->videoList->page_count) echo "disabled"; ?>" title="Go to the last page" href="#" 
				onClick="load('video_list','onPaginate','<?=$this->kg->videoList->params['channelId']?>','<?=$this->kg->videoList->page_count?>');">»</a></span>
	</div>
</div>


