<?php
/*
Plugin Name: Opacity Tags
Plugin URI: http://opacitytags.com
Description: Get a nice tag cloud that uses opacity to demonstrate tag popularity, instead of the old font-size method. Made
Version: 1.0
Author: George Gecewicz
Author URI: http://heyitsgeorge.com/
.
Copyright 2011 George Gecewicz(Email : gecewicz.george@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
.
*/



define('opacity_tags_JS', plugin_dir_url(__FILE__).'js');
/* for 3.2.1 and up */
function opacity_tags_enqueue_scripts_new() {
	wp_enqueue_script( 'opacity-tags-functions', opacity_tags_JS.'/opacity-tags-functions.js', array( 'jquery' ), 1.0, true );
}
function opacity_tags_enqueue_scripts_old() {
	wp_enqueue_script( 'opacity-tags-jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js', array(), 1.6, true);
 	wp_enqueue_script( 'opacity-tags-functions', opacity_tags_JS.'/opacity-tags-functions.js', array( 'jquery' ), 1.0, true );
}
/* for new versions */
if ( version_compare( $GLOBALS['wp_version'], '3.2.1', '>=' ) )
	add_action( 'wp_enqueue_scripts', 'opacity_tags_enqueue_scripts_new' );
/* for older version of wp. loads recent jquery into the footer */
if ( version_compare( $GLOBALS['wp_version'], '3.2.1', '<' ) )
	add_action( 'wp_enqueue_scripts', 'opacity_tags_enqueue_scripts_old' );


add_action('admin_init', 'opacity_tags_farbtastic');
function opacity_tags_farbtastic() {
  wp_enqueue_style( 'farbtastic' );
  wp_enqueue_script( 'farbtastic' );
}


class Opacity_Tags extends WP_Widget {

	function Opacity_Tags() {
		$widget_ops = array(
			'classname' => 'widget_opacity_tags',
			'description' => 'An opacity-based tag cloud that is cleaner and neater than a font-size based one.'
		);
    $control_ops = array('width' => 200, 'height' => 350);
    $this->WP_Widget('opacity_tags', __('Opacity Tags Tag Cloud'), $widget_ops, $control_ops);
	}


	function form($instance) {
   	$instance = wp_parse_args( (array) $instance, array( 'opacity_tags_title' => '', 'opacity_tags_font_size' => '', 'opacity_tags_tag_excludes' => '' ) );
   	$opacity_tags_title = strip_tags($instance['opacity_tags_title']);
		$opacity_tags_num_tags = strip_tags($instance['opacity_tags_num_tags']);
		$opacity_tags_font_size = strip_tags($instance['opacity_tags_font_size']);
		$opacity_tags_font_color = strip_tags($instance['opacity_tags_font_color']);
		?>
	   <p>
			<strong><label for="<?php echo $this->get_field_id('opacity_tags_title'); ?>"><?php _e('Title:'); ?></label></strong>
		  <input class="widefat" id="<?php echo $this->get_field_id('opacity_tags_title'); ?>" name="<?php echo $this->get_field_name('opacity_tags_title'); ?>" type="text" value="<?php echo esc_attr($opacity_tags_title); ?>" />
		 </p>
		 <p>
			<strong><label for="<?php echo $this->get_field_id('opacity_tags_font_size'); ?>"><?php _e('Font Size in Pixels:'); ?></label></strong>
	    <input maxlength="3" class="widefat" id="<?php echo $this->get_field_id('opacity_tags_font_size'); ?>" name="<?php echo $this->get_field_name('opacity_tags_font_size'); ?>" type="text" value="<?php echo esc_attr($opacity_tags_font_size); ?>" />
		 	<small><em>This will default to 14px or another value dictated by your theme.</em></small>
		 </p>
		 <p>
			<strong><label for="<?php echo $this->get_field_id('opacity_tags_num_tags'); ?>"><?php _e('Number of Tags to Display:'); ?></label></strong>
	    <input maxlength="3" class="widefat" id="<?php echo $this->get_field_id('opacity_tags_num_tags'); ?>" name="<?php echo $this->get_field_name('opacity_tags_num_tags'); ?>" type="text" value="<?php echo esc_attr($opacity_tags_num_tags); ?>" />
		 	<small><em>This will default to 45 tags.</em></small>
		 </p>
       <script type="text/javascript">
			 //<![CDATA[
				 jQuery(document).ready(function() {
					 jQuery('.opacity-tags-color-picker').each(function(){
						 id = jQuery(this).attr('rel');
 						 jQuery(this).farbtastic('#' + id);
					 });
				 });
			 //]]>
			 </script>
		<p>
		 <strong><label for="<?php echo $this->get_field_id('opacity_tags_font_color'); ?>"><?php _e('Tag Font Color:'); ?></label></strong>
		 <input class="widefat" id="<?php echo $this->get_field_id('opacity_tags_font_color'); ?>" name="<?php echo $this->get_field_name('opacity_tags_font_color'); ?>" type="text" value="<?php if($opacity_tags_font_color) { echo $opacity_tags_font_color; } else { echo '#ffffff';} ?>" />
		 <small><em>Select your font color here or just type it in. If the color picker isn't working, "save" the widget below and try using the picker again.</em></small>
		 <div class="opacity-tags-color-picker" rel="<?php echo $this->get_field_id('opacity_tags_font_color'); ?>"></div>
	  </p>
<?php
	}

	function update($new_instance, $old_instance)  {
		$instance = $old_instance;
		$instance['opacity_tags_title'] = strip_tags($new_instance['opacity_tags_title']);
		$instance['opacity_tags_font_size']= strip_tags($new_instance['opacity_tags_font_size']);
		$instance['opacity_tags_num_tags']= strip_tags($new_instance['opacity_tags_num_tags']);
		$instance['opacity_tags_font_color']= strip_tags($new_instance['opacity_tags_font_color']);
		return $instance;
	}

	function widget( $args, $instance ) {
      extract($args);
      $opacity_tags_title = apply_filters('widget_opacity_tags_title', empty($instance['opacity_tags_title']) ? '' : $instance['opacity_tags_title'], $instance);
      $opacity_tags_font_size = apply_filters('widget_opacity_tags_font_size', empty($instance['opacity_tags_font_size']) ? '14' : $instance['opacity_tags_font_size'], $instance);
      $opacity_tags_num_tags = apply_filters('widget_opacity_tags_num_tags', empty($instance['opacity_tags_num_tags']) ? '0' : $instance['opacity_tags_num_tags'], $instance);
      $opacity_tags_font_color = apply_filters('widget_opacity_tags_font_color', empty($instance['opacity_tags_font_color']) ? '' : $instance['opacity_tags_font_color'], $instance);

			echo $before_widget;

     		$opacity_tags_title = $opacity_tags_title;
		 		if ( !empty( $opacity_tags_title ) ) { echo $before_title . $opacity_tags_title . $after_title; }
						 # before I close off the PHP tag, I just want to mention that I know the wp_tag_cloud(...) arguments
						 # are kinda long winded and messy-looking. I spent days trying to get a nice, lovely array() to store
						 # all these arguments like the Codex recommends, but that only worked with WP versions 3.1 and up
							?>
							<div id="opacity-tags-list">
								<?php wp_tag_cloud('smallest='.$opacity_tags_font_size.'&largest='.$opacity_tags_font_size.'&unit=px&number='.$opacity_tags_num_tags.'&format=flat&separator= &orderby=count&order=DESC&topic_count_text_callback=default_topic_count_text&link=view&taxonomy=post_tag&echo=true'); ?>
							</div>
							<style type="text/css">
								#opacity-tags-list a {color: <?php print $opacity_tags_font_color; ?> !important;}
								#opacity-tags-list a:hover {opacity:1.0 !important;filter: alpha(opacity=100) !important;}
              </style>
						 <?php

      echo $after_widget;
  }

}

function opacity_tags_init() {register_widget('Opacity_Tags');}
add_action('widgets_init', 'opacity_tags_init');
?>