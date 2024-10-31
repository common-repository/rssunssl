<?php
/**
 * @package RSSUnSSL
 * @version 1.0
 */
/*
Plugin Name: RSSUnSSL
Description: Leitet Seiten, die kein RSS-Feed sind, auf HTTPS um.
Author: Nova GmbH
Version: 1.0
Author URI: http://nova-web.de
*/
// Disable automatic feeds
remove_action('wp_head', 'feed_links', 2 );
remove_action('wp_head', 'feed_links_extra', 3 );


add_action ( 'wp_head', 'rsusl_hook_inHeader' );
function rsusl_hook_inHeader() {
    if ( !current_theme_supports('automatic-feed-links') )
        return;
 
    $defaults = array(
        'separator' => _x('&raquo;', 'feed link'),
        'feedtitle' => __('%1$s %2$s Feed'),
        'comstitle' => __('%1$s %2$s Comments Feed'),
    );
 
    $args = wp_parse_args( $args, $defaults );
 
    if ( apply_filters( 'feed_links_show_posts_feed', true ) ) {
        $url = esc_url( get_feed_link() );
        $url = str_replace( 'https://', 'http://', $url );
        echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr( sprintf( $args['feedtitle'], get_bloginfo( 'name' ), $args['separator'] ) ) . '" href="' . $url . "\" />\n";
    }

    if ( apply_filters( 'feed_links_show_comments_feed', true ) ) {
        $url = esc_url( get_feed_link( 'comments_' . get_default_feed() ) );
        $url = str_replace( 'https://', 'http://', $url );
        echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr( sprintf( $args['comstitle'], get_bloginfo( 'name' ), $args['separator'] ) ) . '" href="' . $url . "\" />\n";
    }
}


add_action('template_redirect', 'rsusl_force_ssl');

function rsusl_force_ssl(){
    if ( !is_ssl () ) {  
        if('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] != esc_url( get_feed_link() ) && 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] != esc_url( get_feed_link( 'comments_' . get_default_feed() ) )){

            wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301 );
        }
    }
}


?>
