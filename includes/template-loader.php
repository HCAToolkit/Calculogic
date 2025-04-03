<?php
// filepath: /workspaces/Calculogic/includes/template-loader.php
/**
 * Template Loader for Calculogic
 *
 * Ensures custom templates are used for Calculogic post types.
 *
 * @package Calculogic
 */

function calculogic_load_custom_templates( $template ) {
    // Check if the current post type is 'calculator'
    if ( is_singular( 'calculator' ) ) {
        $custom_template = plugin_dir_path( __FILE__ ) . '../templates/single-calculator.php';
        if ( file_exists( $custom_template ) ) {
            return $custom_template;
        }
    }

    // Check if the current post type is 'quiz'
    if ( is_singular( 'quiz' ) ) {
        $custom_template = plugin_dir_path( __FILE__ ) . '../templates/single-quiz.php';
        if ( file_exists( $custom_template ) ) {
            return $custom_template;
        }
    }

    // Check if the current post type is 'template'
    if ( is_singular( 'template' ) ) {
        $custom_template = plugin_dir_path( __FILE__ ) . '../templates/single-template.php';
        if ( file_exists( $custom_template ) ) {
            return $custom_template;
        }
    }

    return $template;
}
add_filter( 'template_include', 'calculogic_load_custom_templates' );