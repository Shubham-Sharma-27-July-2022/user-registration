<?php


function load_stylesheet()
{

	// wp_register_style('css', get_template_directory_uri() . '/style/style.css', array(), 1, 'all');
	// wp_enqueue_style('css');
	wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . 'http://localhost/user-registration/wp-content/themes/news/css/custom.css', false, '1.0', 'all' ); 

}

add_action('wp_enqueue_style', 'load_stylesheet');

?>