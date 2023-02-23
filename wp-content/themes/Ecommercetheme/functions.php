<?php

function learningWordPress_resources() {
	
	wp_enqueue_style('style', get_stylesheet_uri());
	
}

add_action('wp_enqueue_scripts', 'learningWordPress_resources');

// Navigation Menus
register_nav_menus(array(
	'primary' => __( 'Primary Menu'),
	'footer' => __( 'Footer Menu'),
));

// Get top ancestor
function get_top_ancestor_id() {
	
	global $post;
	
	if ($post->post_parent) {
		$ancestors = array_reverse(get_post_ancestors($post->ID));
		return $ancestors[0];
		
	}
	
	return $post->ID;
	
}

// Does page have children?
function has_children() {
	
	global $post;
	
	$pages = get_pages('child_of=' . $post->ID);
	return count($pages);
	
}


add_action( 'um_after_form_submission', 'um_store_form_data', 10, 2 );

function um_store_form_data( $args ) {

    global $wpdb;

    $table_name = $wpdb->prefix . 'um_form_data_user';

    $data = array(
		'username' => $args['data']['username'],
        'name' => $args['data']['first_name'] . ' ' . $args['data']['last_name'],
        'email' => $args['data']['user_email'],
        'password' => $args['data']['password']
    );

    $wpdb->insert( $table_name, $data );
}






