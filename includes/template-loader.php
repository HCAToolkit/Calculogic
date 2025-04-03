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
    // Check if the current post type is 'calculogic_type'
    if ( is_singular( 'calculogic_type' ) ) {
        global $post;

        // Get the item type from the meta field
        $item_type = get_post_meta( $post->ID, 'calculogic_item_type', true );

        // Map item types to their respective templates
        $template_map = array(
            'calculator' => plugin_dir_path( __FILE__ ) . '../templates/single-calculator.php',
            'quiz'       => plugin_dir_path( __FILE__ ) . '../templates/single-quiz.php',
            'template'   => plugin_dir_path( __FILE__ ) . '../templates/single-template.php',
        );

        // Check if a custom template exists for the item type
        if ( isset( $template_map[ $item_type ] ) && file_exists( $template_map[ $item_type ] ) ) {
            return $template_map[ $item_type ];
        }
    }

    return $template;
}
add_filter( 'template_include', 'calculogic_load_custom_templates' );