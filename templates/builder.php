<?php
/**
 * Builder Interface for Calculogic
 *
 * This file provides the container for the React-based builder app.
 *
 * @package Calculogic
 */

// Ensure this file is accessed through WordPress
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Pass data to the React app
wp_localize_script( 'calculogic-builder-app', 'CalculogicData', array(
    'ajaxurl'   => admin_url( 'admin-ajax.php' ),
    'nonce'     => wp_create_nonce( 'calculogic_nonce' ),
    'postId'    => isset( $_GET['post'] ) ? intval( $_GET['post'] ) : 0,
) );

// Enqueue the React app script
if ( defined( 'WP_ENV' ) && WP_ENV === 'development' ) {
    // Development: Load from Vite dev server
    wp_enqueue_script( 'calculogic-builder-app', 'http://localhost:5173/src/main.jsx', array(), null, true );
} else {
    // Production: Load from built assets
    $manifest = json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . '../assets/dist/manifest.json' ), true );
    foreach ( $manifest as $file => $meta ) {
        if ( isset( $meta['isEntry'] ) && $meta['isEntry'] ) {
            wp_enqueue_script(
                'calculogic-builder-app',
                plugins_url( 'assets/dist/' . $meta['file'], __FILE__ ),
                [],
                filemtime( plugin_dir_path( __FILE__ ) . '../assets/dist/' . $meta['file'] ),
                true
            );
        }
    }
}
?>

<div id="calculogic-builder">
    <!-- React app will render here -->
</div>