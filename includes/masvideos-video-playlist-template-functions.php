<?php
/**
 * MasVideos Video Playlist Template
 *
 * Functions for the templating system.
 *
 * @package  MasVideos\Functions
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * When the_post is called, put video playlist data into a global.
 *
 * @param mixed $post Post Object.
 * @return MasVideos_Video_Playlist
 */
function masvideos_setup_video_playlist_data( $post ) {
    unset( $GLOBALS['video_playlist'] );

    if ( is_int( $post ) ) {
        $the_post = get_post( $post );
    } else {
        $the_post = $post;
    }

    if ( empty( $the_post->post_type ) || ! in_array( $the_post->post_type, array( 'video_playlist' ), true ) ) {
        return;
    }

    $GLOBALS['video_playlist'] = masvideos_get_video_playlist( $the_post );

    return $GLOBALS['video_playlist'];
}
add_action( 'the_post', 'masvideos_setup_video_playlist_data' );

/**
 * Sets up the masvideos_video_playlists_loop global from the passed args or from the main query.
 *
 * @since 1.0.0
 * @param array $args Args to pass into the global.
 */
function masvideos_setup_video_playlists_loop( $args = array() ) {
    $default_args = array(
        'loop'         => 0,
        'columns'      => 5,
        'name'         => '',
        'is_shortcode' => false,
        'is_paginated' => true,
        'is_search'    => false,
        // 'is_filtered'  => false,
        'total'        => 0,
        'total_pages'  => 0,
        'per_page'     => 0,
        'current_page' => 1,
    );

    // If this is a main WC query, use global args as defaults.
    if ( $GLOBALS['wp_query']->get( 'masvideos_video_playlist_query' ) ) {
        $default_args = array_merge( $default_args, array(
            'is_search'    => $GLOBALS['wp_query']->is_search(),
            // 'is_filtered'  => is_filtered(),
            'total'        => $GLOBALS['wp_query']->found_posts,
            'total_pages'  => $GLOBALS['wp_query']->max_num_pages,
            'per_page'     => $GLOBALS['wp_query']->get( 'posts_per_page' ),
            'current_page' => max( 1, $GLOBALS['wp_query']->get( 'paged', 1 ) ),
        ) );
    }

    // Merge any existing values.
    if ( isset( $GLOBALS['masvideos_video_playlists_loop'] ) ) {
        $default_args = array_merge( $default_args, $GLOBALS['masvideos_video_playlists_loop'] );
    }

    $GLOBALS['masvideos_video_playlists_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'masvideos_before_video_playlists_loop', 'masvideos_setup_video_playlists_loop' );

/**
 * Resets the masvideos_video_playlists_loop global.
 *
 * @since 1.0.0
 */
function masvideos_reset_video_playlists_loop() {
    unset( $GLOBALS['masvideos_video_playlists_loop'] );
}
add_action( 'masvideos_after_video_playlists_loop', 'masvideos_reset_video_playlists_loop', 999 );

/**
 * Gets a property from the masvideos_video_playlists_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function masvideos_get_video_playlists_loop_prop( $prop, $default = '' ) {
    masvideos_setup_video_playlists_loop(); // Ensure shop loop is setup.

    return isset( $GLOBALS['masvideos_video_playlists_loop'], $GLOBALS['masvideos_video_playlists_loop'][ $prop ] ) ? $GLOBALS['masvideos_video_playlists_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the masvideos_video_playlists_loop global.
 *
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function masvideos_set_video_playlists_loop_prop( $prop, $value = '' ) {
    if ( ! isset( $GLOBALS['masvideos_video_playlists_loop'] ) ) {
        masvideos_setup_video_playlists_loop();
    }
    $GLOBALS['masvideos_video_playlists_loop'][ $prop ] = $value;
}

/**
 * Display the classes for the video playlist div.
 *
 * @since 1.0.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|MasVideos_Video_Playlists_Query $video_playlist_id Video Playlist ID or video playlist object.
 */
function masvideos_video_playlist_class( $class = '', $video_playlist_id = null ) {
    // echo 'class="' . esc_attr( join( ' ', wc_get_video_class( $class, $video_playlist_id ) ) ) . '"';
    post_class();
}

/**
 * Loop
 */

if ( ! function_exists( 'masvideos_video_playlist_loop_start' ) ) {

    /**
     * Output the start of a video playlist loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_video_playlist_loop_start( $echo = true ) {
        ob_start();

        masvideos_set_video_playlists_loop_prop( 'loop', 0 );

        ?><div class="video-playlists columns-<?php echo esc_attr( masvideos_get_video_playlists_loop_prop( 'columns' ) ); ?>"><div class="video-playlists__inner"><?php

        $loop_start = apply_filters( 'masvideos_video_playlist_loop_start', ob_get_clean() );

        if ( $echo ) {
            echo $loop_start; // WPCS: XSS ok.
        } else {
            return $loop_start;
        }
    }
}

if ( ! function_exists( 'masvideos_video_playlist_loop_end' ) ) {

    /**
     * Output the end of a video playlist loop. By default this is a UL.
     *
     * @param bool $echo Should echo?.
     * @return string
     */
    function masvideos_video_playlist_loop_end( $echo = true ) {
        ob_start();

        ?></div></div><?php

        $loop_end = apply_filters( 'masvideos_video_playlist_loop_end', ob_get_clean() );

        if ( $echo ) {
            echo $loop_end; // WPCS: XSS ok.
        } else {
            return $loop_end;
        }
    }
}
