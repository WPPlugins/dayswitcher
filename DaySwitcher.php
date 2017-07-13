<?php
/*
 Plugin Name: DaySwitcher
 Plugin URI: http://blog.thomascook.fr/demo/day-switcher/
 Description: Changes theme according to the server's hour
 Version: 1.2
 Author: Thomas Cook
 Author URI: http://blog.thomascook.fr/
 */
?>
<?php
/*  Copyright 2008  ThomasCook  (email : sampaolo@gmail.com)

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
?>
<?php
if (!class_exists("DaySwitcher")) {
	class DaySwitcher {
		function DaySwitcher () {
			$this->__construct();
		}
		
		function __construct() {
			// actions	
			add_action('activate_DaySwitcher/DaySwitcher.php', array(&$this, 'activate'));
			add_action('admin_menu', array(&$this, 'admin_actions'));
			register_activation_hook( __FILE__, array(&$this, 'activate'));

			$offset = date('G') * 60 + date('i');
			$month = date('n');
			
			// selects current override
			global $wpdb;
			$sql = "SELECT * FROM ".$wpdb->prefix."DaySwitcher WHERE (month_start <= $month AND month_end >= $month) AND (hour_start <= $offset AND hour_end > $offset) ORDER BY (hour_end - hour_start) LIMIT 1";
			$a = $wpdb->get_row($sql, ARRAY_A);
			if ($a !== NULL) {
				$themes = get_themes();
				foreach($themes as $theme) {
					if ($a['name'] == md5($theme['Name'])) {
						// if there is an active override, store it in $this->override and register template/css hooks
						$this->override = $theme;
						add_filter('template',array(&$this,'get_template'));
						add_filter('stylesheet',array(&$this,'get_stylesheet'));
					}
				}
			}
		}
		
		function activate () {
			global $wpdb;
			$wpdb->query("drop table $table");
			
		    $table = $wpdb->prefix."DaySwitcher";
		    $structure = "CREATE TABLE $table (
		        id INT(9) NOT NULL AUTO_INCREMENT,
		        name VARCHAR(40),
		        month_start INT (5) NOT NULL,
		        month_end INT (5) NOT NULL,
		        hour_start INT (5) NOT NULL,
		        hour_end INT (5) NOT NULL,
			PRIMARY KEY id (id),
			UNIQUE name (name))";
		    $wpdb->query($structure);		
		}
		
		function admin_actions () {
			add_options_page("DaySwitcher Settings", "DaySwitcher", 1, "day-switcher", array(&$this, "admin_menu"));
		}
		
		function admin_menu () {
			include('admin-menu.php');
		}
		
		function get_template($template) {
			if (!isset($this->override)) {
				return $template;
			}	
			return $this->override['Template'];
 		}
 		
 		function get_stylesheet($stylesheet) {
			if (!isset($this->override)) {
				return $stylesheet;
			}	
			return $this->override['Stylesheet'];
 		}
	}
} //End Class DaySwitcher


/* Instanciation */
if (class_exists("DaySwitcher")) {
	$ts = new DaySwitcher();
} 
?>
