<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Update the get_the_archive_title() function
 * to pull data from the page post.
 */
add_filter( 'get_the_archive_title', function ( $title ) {
    $archive_page = get_archive_page();
    return $archive_page ? $archive_page->post_title : $title;
} );

/**
 * Update the get_the_archive_description() function
 * to pull data from the page post.
 */
add_filter( 'get_the_archive_description', function ( $description ) {
    $archive_page = get_archive_page();
    return $archive_page ? $archive_page->post_content : $description;
} );

/**
 * Return archive page object.
 */
function get_archive_page( $post_type = false ) {
    $archive_page = false;
    $archive_page_id = get_archive_page_id( $post_type );

    if ( $archive_page_id ) {
        $archive_page = get_post ( $archive_page_id );
    }

    return $archive_page;
}

/**
 * Get archive page id by post type.
 */
function get_archive_page_id( $post_type = false ) {
    $object = get_queried_object();

    if ( ! empty( $object->name ) && ! $post_type ) {
        $post_type = $object->name;
    }

    $archive_pages = get_option( 'archive_manager_settings' );
    $page_id = empty( $archive_pages[ $post_type ] ) ? false : $archive_pages[ $post_type ];

    return $page_id;

}

/**
 * Yoast - title support
 */
add_filter( 'wpseo_title', function ( $title ) {
	if ( $page_id = get_archive_page_id() ) {
		$title = get_post_meta( $page_id, '_yoast_wpseo_title', true );
    }
    return $title;
} );

/**
 * Yoast - metadesc support
 */
add_filter( 'wpseo_metadesc', function ( $metadesc ) {
	if ( $page_id = get_archive_page_id() ) {
		$metadesc =  get_post_meta( $page_id, '_yoast_wpseo_metadesc', true );
    }
    return $metadesc;
} );