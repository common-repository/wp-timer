<?php
/*
Plugin Name: WP-Timer
Plugin URI: http://traq.devbert.co.uk/index.php/WP-timer
Description: A simple timer for wordpress, inspired by http://xkcd.com/363/
Author: Dan Warden
Version: 1.03
Author URI: http://www.nonamelan.co.uk
*/

/*  Copyright 2009  Daniel Warden  (email : philbert@nonamelan.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_action("widgets_init", array('WP_timer', 'register'));
register_activation_hook(__FILE__, array('WP_timer', 'activate'));
register_deactivation_hook(__FILE__, array('WP_timer', 'deactivate'));

class WP_timer {

	function activate()
		{
		$data = array( 'option1' => time(), 'title' => "WP_timer", 'interval' => "86400");
		if ( ! get_option('WP_timer_option'))
		{ add_option('WP_timer_option' , $data); }
		else
		{ update_option('WP_timer_option' , $data); }
		}
		
	function deactivate()
		{
		delete_option('WP_timer_option');
		}
		
	function control()
		{
		$data = get_option('WP_timer_option');
		?>
		<p><lable>Timer title<input name="WP_timer_option" type="text" value="<?php echo $data['title']; ?>" /></lable></p>
		<p><lable>Timer Interval<select name="WP_timer_interval">
			<option value="86400" <?php if ($data['interval'] == "86400") {echo selected;} ?>>days</option>
			<option value="3600" <?php if ($data['interval'] == "3600") {echo selected;} ?>>hours</option>
			<option value="60" <?php if ($data['interval'] == "60") {echo selected;} ?>>minutes</option>
			</select>
		<?php
		if (isset($_POST['WP_timer_option']))
			{
			$data['title'] = attribute_escape($_POST['WP_timer_option']);
			$data['interval'] = attribute_escape($_POST['WP_timer_interval']);
			update_option('WP_timer_option', $data);
			}
		}
		
	function resetcount()
		{
		$data = get_option('WP_timer_option');
		
		$data['interval'] = attribute_escape($_POST['interval']);
		$data['title'] = attribute_escape($_POST['title']);
		
		update_option('WP_timer_option', $data);

		}

	function widget($args)
		{
		
		function resetcount()
			{
			$data = get_option('WP_timer_option');
		
			$data['interval'] = attribute_escape($_POST['interval']);
			$data['title'] = attribute_escape($_POST['title']);
			$data['option1'] = time();
		
			update_option('WP_timer_option', $data); 
		}
		
		if (isset($_POST['action']))
			{
			if ($_POST['action'] == "true" )
				{
				resetcount();
				}
			}
		
		echo $args['before_widget'];
		$title=get_option('WP_timer_option');
		echo $args['before_title'] . $title['title'] . $args['after_title'];
		$option=get_option('WP_timer_option');
		$now=time();
		$diff=$now - $option['option1'];
		$interval=get_option('WP_timer_option');
		$units=$diff / $interval['interval'];
		$floor = floor($units);
		
		if ($interval['interval']=="60") 
			{
			$time="minute";
			}
		elseif ($interval['interval']=="3600")
			{
			$time="hour";
			}
		elseif ($interval['interval']=="86400")
			{
			$time="day";
			}
			
		?><p>It has been <?php echo $floor; ?> <?php echo $time; ?><?php if ($floor == '1')
			{
			echo '';
			}
		else
			{
			echo 's';
			}
		?> since someone last reset this sign.</p>
		<p><form method='post' action='<?php echo $_SERVER['PHP_SELF']?>'>
		<input name='interval' type='hidden' value='<?php echo $interval['interval']; ?>'/>
		<input name='title' type='hidden' value='<?php echo $title['title']; ?>'/>
		<input name='action' type='hidden' value='true'/>
		<input type='submit' value='Reset'/></p><?php
		
		echo $args['after_widget'];
		
		}
	
	function register()
		{
		register_sidebar_widget('WP_timer', array('WP_timer', 'widget'));
		register_widget_control('WP_timer', array('WP_timer', 'control'));
		}
		
}

?>
