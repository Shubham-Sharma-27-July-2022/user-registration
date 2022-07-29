<?php
/*
Plugin Name: OTP Login
Description: A simple plugin that allow you to Secure login with One Time Password(OTP) into wordpress site. Allow woocommerce customers to login on your website with OTP. An OTP login is more secure than a static password, especially a user-created password.
Author: WP Experts Team
Author URI: https://www.wp-experts.in
Version: 1.2
License GPL2
Copyright 2021  WP-Experts.IN  (email  developer.0087@gmail.com)

This program is free software; you can redistribute it andor modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists( 'OtpLogin' ) ) {
	
    class OtpLogin
    {
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
			// Installation and uninstallation hooks
			register_activation_hook(__FILE__, array(&$this, 'otpl_activate'));
			register_deactivation_hook(__FILE__, array(&$this, 'otpl_deactivate'));
			//backend hooks action
			add_filter("plugin_action_links_".plugin_basename(__FILE__), array(&$this,'otpl_settings_link'));
			add_action('admin_init', array(&$this, 'otpl_admin_init'));
			add_action('admin_menu', array(&$this, 'otpl_add_menu'));
			add_action( 'admin_bar_menu', array(&$this,'toolbar_link_to_otpl'), 999 );
            
        } // END public function __construct
		/**
		 * hook to add link under adminmenu bar
		 */		
		public function toolbar_link_to_otpl( $wp_admin_bar ) {
			
			$user = wp_get_current_user();
			if ( !current_user_can( 'administrator' ) && is_admin() ) {
				return;
			}
			
			$args = array(
				'id'    => 'otpl_menu_bar',
				'title' => 'OTP Login',
				'href'  => admin_url('options-general.php?page=otp-login'),
				'meta'  => array( 'class' => 'otpl-toolbar-page' )
			);
			$wp_admin_bar->add_node( $args );
			//second lavel
			$wp_admin_bar->add_node( array(
				'id'    => 'otpl-second-sub-item',
				'parent' => 'otpl_menu_bar',
				'title' => 'Settings',
				'href'  => admin_url('options-general.php?page=otp-login'),
				'meta'  => array(
					'title' => __('Settings'),
					'target' => '_self',
					'class' => 'otpl_menu_item_class'
				),
			));
		}
		/**
		 * hook into WP's admin_init action hook
		 */
		public function otpl_admin_init()
		{
			// Set up the settings for this plugin
			$this->otpl_init_settings();
			// Possibly do additional admin_init tasks
		} // END public static function activate
		/**
		 * Initialize some custom settings
		 */     
		public function otpl_init_settings()
		{
			// register the settings for this plugin
			register_setting('otpl', 'otpl_enable');
			register_setting('otpl', 'otpl_redirect_url');
			register_setting('otpl', 'otpl_message');
		} // END public function otpl_init_settings()
		/**
		 * add a menu
		 */     
		public function otpl_add_menu()
		{
			add_options_page('OTP Login Settings', 'OTP Login', 'manage_options', 'otp-login', array(&$this, 'otpl_settings_page'));
		} // END public function add_menu()

		/**
		 * Menu Callback
		 */     
		public function otpl_settings_page()
		{
			if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			// Render the settings template
			include(sprintf("%s/lib/settings.php", dirname(__FILE__)));
			//include(sprintf("%s/css/admin.css", dirname(__FILE__)));
			// Style Files
			wp_register_style( 'otpl_admin_style', plugins_url( 'css/otpl-admin.css',__FILE__ ) );
			wp_enqueue_style( 'otpl_admin_style' );
			// JS files
			wp_register_script('otpl_admin_script', plugins_url('/js/otpl-admin.js',__FILE__ ), array('jquery'));
            wp_enqueue_script('otpl_admin_script');
		} // END public function plugin_settings_page()
        /**
         * Activate the plugin
         */
        public function otpl_activate()
        {
            // Do nothing
        } // END public static function activate
    
        /**
         * Deactivate the plugin
         */     
        public function otpl_deactivate()
        {
            // Do nothing
        } // END public static function deactivate
        // Add the settings link to the plugins page
		public function otpl_settings_link($links)
		{ 
			$settings_link = '<a href="options-general.php?page=otp-login">Settings</a>'; 
			array_unshift($links, $settings_link); 
			return $links; 
		}
    } // END class wp_optimize_site
} // END if(!class_exists('OtpLogin'))

if(class_exists('OtpLogin'))
{
    // instantiate the plugin class
    $OtpLogintemplate = new OtpLogin();
}
// Render the hooks functions
include(sprintf("%s/lib/otpl-class.php", dirname(__FILE__)));
