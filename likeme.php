<?php
/*
Plugin Name: Like me
*/


/**
 * ShortCode for displaying button */
add_shortcode( 'likemebtn', 'likeme_shortcode' );
function likeme_shortcode() {

  $html = "<button class='btn btn-default' id='likeme-btn'><i class='fa fa-heart'></i></button>";
  $html .= "<div id='totallike' class='btn btn-default'></div>";

  return $html;

} // shortcode()




/**
 * Ajax call for the button */
 function likeme_callback() {  
  global $wpdb; // this is how you get access to the database

	$ip = $_POST['ip'];
  $table_name = $wpdb->prefix . "likeme";

  $exists = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE user_ip='$ip'");

  if($exists == 0) {
    $wpdb->insert($table_name, array(
      'user_ip' => $ip,
      'liked' => 1
    ));
  }

  $totalLikes = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");  
  echo $totalLikes;

	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_likeme', 'likeme_callback' );
add_action( 'wp_ajax_nopriv_likeme', 'likeme_callback' );




function enqeue_scripts() {

  global $wpdb;
  $table_name = $wpdb->prefix . "likeme";
  $totalLikes = $wpdb->get_var('SELECT COUNT(*) FROM ' . $table_name);
  

  
  wp_register_script('likeme-handle', 
    plugins_url('likeme.js', __FILE__), array('jquery')
  );
  wp_localize_script('likeme-handle', 'likeme', 
    array(
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'user_ip' => get_user_ip(),
      'totallikes' => $totalLikes
    )
  );    
  wp_enqueue_script( 'likeme-handle');

}
add_action( 'wp_enqueue_scripts', 'enqeue_scripts' );


function get_user_ip() {
  if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {  
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}




function likeme_install() {
  global $wpdb;

  $table_name = $wpdb->prefix . "likeme"; 
  $sql = "CREATE TABLE $table_name (
    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
    `user_ip` VARCHAR(20),
    `liked` BIT,    
    UNIQUE KEY id(id)
  )";
  
  require_once(ABSPATH . "wp-admin/includes/upgrade.php");
  dbDelta( $sql );
}

function likeme_install_35likes() {
  global $wpdb;
  $table_name = $wpdb->prefix . "likeme";

  for($ip = 1; $ip <= 35; $ip++) {

    $wpdb->insert($table_name, array(
      'user_ip' => $ip,
      'liked' => 1
    ));
  
  }
}

register_activation_hook( __FILE__, 'likeme_install' );
register_activation_hook( __FILE__, 'likeme_install_35likes' );


