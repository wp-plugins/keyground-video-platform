<?=$before_widget; ?>
<?php if ( $title ) echo $before_title . $title . $after_title; ?>
<ul>
<?foreach($channels as $channel):?>
<li class="cat-item">
	<a href="<?=$page_link.'&ch='.$channel->id?>"><?=$channel->title?></a>
</li>
<?endforeach;?>
</ul>
<?=$after_widget; ?>