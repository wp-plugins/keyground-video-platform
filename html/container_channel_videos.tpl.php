<div class="kg-video-container">
<div class="container-title"><?=$channel->title?></div>
<? foreach($videos as $video):?>
<? include('video_box.tpl.php')?>
<? endforeach;?>
</div>