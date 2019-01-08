<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


class Archive_Manager_Admin {

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_init', array( $this, 'flush_rewrite_rules' ) );
		add_action( 'updated_option', array( $this, 'updated_option' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );

	}

	/**
	 * Flush permalinks.
	 */
	public function flush_rewrite_rules() {

		if ( get_option( 'am_flush_rewrite' ) ) {
			flush_rewrite_rules();
			update_option( 'am_flush_rewrite', false );
		}

	}

	/**
	 * Inform WordPress to flush permalinks after the options save.
	 */
	public function updated_option( $option_name ) {

		if ( $option_name == 'archive_manager_settings' ) {
			update_option( 'am_flush_rewrite', true );
		}

	}

	/**
	 * Inform WordPress to flush permalinks after post save.
	 */
	public function save_post( $post_id ) {

		$post_types = get_option( 'archive_manager_settings' );

		if ( in_array( $post_id, (array) $post_types ) ) {
			update_option( 'am_flush_rewrite', true );
		}

	}

	/**
	 * Add the options page to the WordPress admin menu.
	 */
	public function admin_menu() {

		add_options_page(
			'Archive Manager',
			'Archive Manager',
			'manage_options',
			'archive-manager',
			array( $this, 'options_page' )
		);

	}

	/**
	 * Settings for the options page.
	 */
	public function settings_init() {

		register_setting( 'archive_manager', 'archive_manager_settings' );

		add_settings_section(
			'archive_manager_section',
			__( 'Available Post Types', 'archive_manager' ),
			'',
			'archive_manager'
		);

		$post_types = get_post_types(
			array(
				'public'             => true,
				'_builtin'           => false,
				'publicly_queryable' => true,
				'has_archive'        => true
			),
			'object'
		);
		$pages = get_pages();

		foreach ( $post_types as $post_type ) {

			add_settings_field(
				'page_for_' . $post_type->name,
				__( $post_type->label, 'archive_manager' ),
				array( $this, 'select_field_render' ),
				'archive_manager',
				'archive_manager_section',
				array(
					'post_type_name' => $post_type->name,
					'pages'          => $pages
				)
			);

		}

	}

	/**
	 * Render select field.
	 */
	public function select_field_render( $args ) {

		$options = get_option( 'archive_manager_settings' );
		?>

		<select name='archive_manager_settings[<?php echo $args['post_type_name']; ?>]'>

			<option value="">&mdash; Select &mdash;</option>

			<?php foreach ( $args['pages'] as $page ) : ?>

				<option value="<?php echo $page->ID; ?>" <?php selected( $options[ $args['post_type_name'] ], $page->ID ); ?>>
					<?php echo $page->post_title; ?>
				</option>

			<?php endforeach; ?>
		</select>

		<?php

	}

	/**
	 * Display options page.
	 */
	public function options_page() {

		?>
		<div class="wrap">

			<h1><?php echo get_admin_page_title(); ?></h1>

			<form action='options.php' method='post'>

				<?php
				settings_fields( 'archive_manager' );
				do_settings_sections( 'archive_manager' );
				submit_button();
				?>

			</form>

		</div>
		<?php

	}

}
