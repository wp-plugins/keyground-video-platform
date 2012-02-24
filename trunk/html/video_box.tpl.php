<div class="video-box">
	<div class="thumb"><a href="<?=$page_link.'&vid='.$video->id?>"><img src="<?=$video->thumb_m?>"></a></div>
	<div class="video-title"><a href='<?=$_SERVER['QUERY_STRING'].'&vid='.$video->id?>'><?=limitStr($video->title,30)?></a></div>
</div>
