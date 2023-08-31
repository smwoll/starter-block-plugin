<?php

/**
 * Plugin Name: Starter Block Plugin
 * Plugin URI: https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/
 * Description: This is an example plugin for setting up WordPress editor blocks using the WP Scripts package.
 * Version: 1.0.0
 * Author: Sky
 *
 * @package starter-block-plugin
 */

defined( 'ABSPATH' ) || exit;

// Add custom block category.
function gutenberg_examples_block_categories( $categories ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'starter-block-plugin-examples',
				'title' => 'Starter Block Plugin Examples',
			),
		)
	);
}
add_action( 'block_categories', 'gutenberg_examples_block_categories', 10, 2 );

/**
 * Class to set up plugin blocks.
 */
class Starter_Block_Plugin_Block {

	/**
	 * block.json file path.
	 *
	 * @var string
	 */
	public $block_json;

	/**
	 * Block directory.
	 *
	 * @var string
	 */
	public $block_dir;

    /**
     * Get the template file for the block. Uses the directory of the block's block.json file to find the template.
     */
	public function get_template() {
		return $this->block_dir . 'template.php';
	}

	/**
	 * Render the block on the frontend.
	 *
	 * @param array    $attributes     The array of attributes for this block.
	 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return string Rendered block output.
	 */
	public function render( $attributes, $content, $block_instance ) {
		ob_start();
		require $this->get_template();
		return ob_get_clean();
	}

    /**
     * Register the block with WordPress. 
     * Always dynamic, uses template.php in the same directory as the block.json file.
     * 
     * @return void
     */
	public function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		register_block_type(
			$this->block_json,
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);
	}

	/**
	 * Constructor.
	 *
	 * @param string $block_json Path to the block.json file. Will look for template.php in the same directory.
	 */
	public function __construct( $block_json ) {
		$this->block_json = $block_json;
		$this->block_dir  = plugin_dir_path( $block_json );
		$this->register_block( $this );
	}
}

/**
 * Register each plugin block available in the build directory.
 */
function starter_block_plugin_set_up_blocks() {
	// Require each index.php file once in the build directory.
	foreach ( glob( plugin_dir_path( __FILE__ ) . 'build/blocks/*/block.json' ) as $file ) {
		if ( ! is_readable( $file ) ) {
			continue;
		}

		new Starter_Block_Plugin_Block( $file );
	}
}

// Set up blocks.
add_action( 'init', 'starter_block_plugin_set_up_blocks' );
