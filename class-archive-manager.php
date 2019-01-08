<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Archive_Manager {

    protected $post_types;

    public function __construct() {

        // Load Admin Management
        if ( is_admin() ) {
            new Archive_Manager_Admin();
        }

        // Get list of post types
        $this->post_types = (array) get_option( 'archive_manager_settings' );
        $this->post_types = array_filter( $this->post_types );

        add_filter( 'register_post_type_args', array( $this, 'update_rewrite' ), 10, 2 );
        add_action( 'admin_bar_menu', array( $this, 'edit_link' ), 80 );

    }

    /**
     * Update post type slugs.
     */
    public function update_rewrite( $args, $post_type ) {

        if ( ! isset( $this->post_types[ $post_type ] ) ) {
            return $args;
        }

        $page_id = $this->post_types[ $post_type ];
        $page = get_post( $page_id );

        if ( $page ) {
            $args['rewrite']['slug'] = $page->post_name;
        }

        return $args;

    }

    /**
     * Add the "Edit Page" link to the adminbar.
     */
    public function edit_link( $wp_admin_bar ) {

        global $wp_query;

        if ( ! is_admin() && is_post_type_archive() && $wp_query->queried_object->show_ui && current_user_can( $wp_query->queried_object->cap->edit_posts ) ) {
            $slug = $wp_query->queried_object->rewrite['slug'];
            $archive_page = get_page_by_path( $slug );

            $wp_admin_bar->add_menu(array(
                'id'    => 'edit',
                'title' => 'Edit Page',
                'href'  => admin_url( 'post.php?post=' . $archive_page->ID . '&action=edit' ),
            ));
        }

    }

}
