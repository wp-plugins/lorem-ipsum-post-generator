<?php
/*
Plugin Name: Lorem Ipsum
Plugin URI: http://jonefox.com/blog
Description: Generate Lorem Ipsum posts and comments for quick content.  Primarily useful for test blogs.  Start generating posts <a href='options-general.php?page=lorem-ipsum'>here</a> once activated.
Version: .1
Author: Jon Fox
Author URI: http://jonefox.com/blog
*/

include_once( ABSPATH . '/wp-includes/class-snoopy.php' );

add_action('admin_menu', 'loremipsum_plugin_menu');

function loremipsum_plugin_menu() {
	add_options_page('Lorem Ipsum Generator', 'Lorem Ipsum', 8, 'lorem-ipsum', 'lorem_ipsum_options');
}

function lorem_ipsum_options() {
	if( $_GET['generate-ipsum'] ) {
		$num_posts = intval( $_GET['num_posts'] );
		$max_paras = intval( $_GET['max_paras'] );
		$min_paras = intval( $_GET['min_paras'] );
		$max_comments = intval( $_GET['max_comments'] );
		$min_comments = intval( $_GET['min_comments'] );
		
		// Create post objects
		for( $j = 0; $j < $num_posts; $j++ ) {
			$my_post = array();
			$my_post['post_title'] = get_lorem_ipsum_text( rand( 3, 7 ), 'words' );
			$my_post['post_content'] = get_lorem_ipsum_text( rand( $min_paras, $max_paras ), 'paras' );
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$new_post_id = wp_insert_post( $my_post );
			
			$comment_count = rand( $min_comments, $max_comments );
			for( $i = 0; $i < $comment_count; $i++ ) {
				$time = current_time('mysql', $gmt = 0); 

				$data = array(
				    'comment_post_ID' => $new_post_id,
				    'comment_author' => get_lorem_ipsum_text( rand( 2, 4 ), 'words' ),
				    'comment_author_email' => 'admin@admin.com',
				    'comment_author_url' => 'http://',
				    'comment_content' => get_lorem_ipsum_text( rand( 1, 5 ), 'paras' ),
				    'comment_type' => '',
				    'comment_parent' => 0,
				    'user_ID' => 0,
				    'comment_author_IP' => '127.0.0.1',
				    'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
				    'comment_date' => $time,
				    'comment_date_gmt' => $time,
				    'comment_approved' => 1,
				);

				wp_insert_comment($data);
			}
		}
		
		echo '<div id="message" class="updated fade"><p><strong>' . $num_posts . ' posts created</strong></p></div>';
	}
	echo '<h2>Lorem Ipsum Post Generator</h2>';
	echo '<form class="ui-tabs-panel" action="options-general.php?page=lorem-ipsum&generate-ipsum=1" method="get">';
	echo '<table>';
	echo '<tr><td>Number of posts:</td><td><input type="text" name="num_posts" value="5" size="2" /></td><tr>';
	echo '<tr><td>Minimum number of paragraphs per post:</td><td><input type="text" name="min_paras" value="1" size="2" /></td><tr>';
	echo '<tr><td>Maximum number of paragraphs per post:</td><td><input type="text" name="max_paras" value="5" size="2" /></td><tr>';	
	echo '<tr><td>Minimum number of comments per post:</td><td><input type="text" name="min_comments" value="1" size="2" /></td><tr>';
	echo '<tr><td>Maximum number of comments per post:</td><td><input type="text" name="max_comments" value="5" size="2" /></td><tr>';
	echo '<tr><td colspan=2 align=right><input type="hidden" name="page" value="lorem-ipsum" /><input type="hidden" name="generate-ipsum" value="1" /><input type="submit" value="Make some posts" /></td></tr>';
	echo '</table>';
	echo '</form>';
}

function get_lorem_ipsum_text( $num, $type ) {
	$obj = json_decode( preg_replace( '/\n|\t/', '<p>', lorem_http_query( 'http://www.lipsum.com/feed/json', array( 'amount' => $num, 'what' => $type, 'start' => 'no' ) ) ) );
	return $obj->feed->lipsum;
}

function lorem_http_query( $url, $fields ) {
	$results = '';
	$snoopy = new Snoopy;

	if ( $snoopy->fetch( $url .= '?' . http_build_query( $fields ) ) )
		$results = $snoopy->results;
		
	return $results;
}
?>
