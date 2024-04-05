<?php

/*
 * Plugin Name:       YouTube_Videos_4_Wordpress
 * Description:       Polls From Youtube and displays videos on wordpress.
 * Author:            641Tech LLC
 * Author URI:        https://641tech.com
 * License:           GNU General Public License v3.0
 * Version:           1.0
 */

 

// Poll Funtion
function poll_videos() {
    
    $api_key = 'YOUR KEY';
    $channel_id = 'YOUR CHANNEL ID';
    $api_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=". $channel_id ."&type=video&order=date&key=" . $api_key ."&maxResults=20";
    

    global $wpdb;

    $table_name = $wpdb->prefix . 'youtubevideos';
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);

    $videos = [];
    
    foreach ($data['items'] as $item) {
        $video_id = $item['id']['videoId'];
        $video_title = $item['snippet']['title'];
        $video_datetime = new DateTime($item['snippet']['publishedAt']);
        $video_url = "https://www.youtube.com/embed/$video_id";

        $video_date = $video_datetime->format('Y/m/d');


        $result = $wpdb->get_results("SELECT title, url FROM ". $table_name ." WHERE url='" . $video_url . "'" );
        if (!$result){
        $wpdb->insert($table_name, array('title' => $video_title, 'date' => $video_date, 'url' => $video_url));
        }

    }

}

// Display Funtion

function get_video($atts) {

    $atts = shortcode_atts(
		array(
			'recent' => '0',
		),
		$atts
	);

    global $wpdb;
    $table_name = $wpdb->prefix . 'youtubevideos';
    $videos = $wpdb->get_results("SELECT title, url FROM ". $table_name ." ORDER BY date DESC");
    $videoarray = json_decode(json_encode($videos), true);
    return '<div class="resizable-iframe"><h3>' . $videoarray[$atts["recent"]]["title"] . '</h3><div><iframe width="560" height="315" src="'. $videoarray[$atts["recent"]]["url"] .'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div></div>';
    }
add_shortcode( 'ytvideo', 'get_video' );



//create DB elements

function ytdb_install() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'youtubevideos';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		date date NOT NULL,
		title tinytext NOT NULL,
		url tinytext NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

    poll_videos();
}



register_activation_hook( __FILE__, 'ytdb_install' );

add_action('update_youtube_videos', 'poll_videos');

if ( ! wp_next_scheduled( 'update_youtube_videos' ) ) {
    wp_schedule_event( time(), 'daily', 'update_youtube_videos' );
}

add_action('init', 'register_script');
function register_script(){
	wp_register_style( 'new_style', plugins_url('/css/test.css', __FILE__), false, '0.1.0', 'all');
}

add_action('wp_enqueue_scripts', 'enqueue_style');
function enqueue_style(){
	wp_enqueue_style( 'new_style' );
}
?>