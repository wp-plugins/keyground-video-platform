<?php
class kgChannelsWidget extends WP_Widget 
{
    
    function kgChannelsWidget() 
    {
        parent::WP_Widget(false, $name = 'Keyground Channels');
    }

    function widget($args, $instance) 
    {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $kg = new kg(get_option('kg_api_user'),get_option('kg_api_key'));
		$data = $kg->getChannels();
		$channels=$data->channels->channel;
        $page_link=get_page_link(get_option('kg_page_id'));
       	include 'html/channelWidget.tpl.php';
    }


    function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }


    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
    }

}
?>