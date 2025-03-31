<?php
/**
 * Custom Post Types for Calculogic
 *
 * This file registers custom post types for managing templates, quizzes, and calculators.
 *
 * @package Calculogic
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// 1. Register the Configurations CPT
function calculogic_register_config_cpt() {
    $labels = array(
        'name'                  => __( 'Calculogic Configurations', 'calculogic' ),
        'singular_name'         => __( 'Calculogic Configuration', 'calculogic' ),
        'menu_name'             => __( 'Configurations', 'calculogic' ),
        'add_new'               => __( 'Add New Configuration', 'calculogic' ),
        'add_new_item'          => __( 'Add New Configuration', 'calculogic' ),
        'edit_item'             => __( 'Edit Configuration', 'calculogic' ),
        'new_item'              => __( 'New Configuration', 'calculogic' ),
        'view_item'             => __( 'View Configuration', 'calculogic' ),
        'search_items'          => __( 'Search Configurations', 'calculogic' ),
        'not_found'             => __( 'No configurations found', 'calculogic' ),
        'not_found_in_trash'    => __( 'No configurations found in trash', 'calculogic' ),
    );
    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'supports'              => array( 'title', 'editor' ),
        'rewrite'               => array( 'slug' => 'calculogic-config' ),
        'show_in_rest'          => true,
    );
    register_post_type( 'calculogic_config', $args );
}
add_action( 'init', 'calculogic_register_config_cpt' );

// 2. Register the Types CPT
function calculogic_register_type_cpt() {
    $labels = array(
        'name'                  => __( 'Calculogic Types', 'calculogic' ),
        'singular_name'         => __( 'Calculogic Type', 'calculogic' ),
        'menu_name'             => __( 'Types', 'calculogic' ),
        'add_new'               => __( 'Add New Type', 'calculogic' ),
        'add_new_item'          => __( 'Add New Type', 'calculogic' ),
        'edit_item'             => __( 'Edit Type', 'calculogic' ),
        'new_item'              => __( 'New Type', 'calculogic' ),
        'view_item'             => __( 'View Type', 'calculogic' ),
        'search_items'          => __( 'Search Types', 'calculogic' ),
        'not_found'             => __( 'No types found', 'calculogic' ),
        'not_found_in_trash'    => __( 'No types found in trash', 'calculogic' ),
    );
    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'supports'              => array( 'title', 'editor', 'thumbnail' ),
        'rewrite'               => array( 'slug' => 'calculogic-types' ),
        'show_in_rest'          => true,
    );
    register_post_type( 'calculogic_type', $args );
}
add_action( 'init', 'calculogic_register_type_cpt' );

// 3. Add a Meta Box to the Types CPT to Associate Configurations

// Hook to add the meta box on the 'calculogic_type' edit screen.
function calculogic_add_configurations_meta_box() {
    add_meta_box(
        'calculogic_configurations_meta_box',         // Unique ID for the meta box.
        __( 'Associated Configurations', 'calculogic' ), // Title displayed in the meta box.
        'calculogic_render_configurations_meta_box',    // Callback function that renders the meta box.
        'calculogic_type',                              // CPT where this meta box should appear.
        'normal',                                       // Context: normal, side, etc.
        'high'                                          // Priority.
    );
}
add_action( 'add_meta_boxes', 'calculogic_add_configurations_meta_box' );

// Render the meta box: lists all available configurations with checkboxes.
function calculogic_render_configurations_meta_box( $post ) {
    // Retrieve existing configuration IDs associated with this type.
    $config_ids = get_post_meta( $post->ID, 'calculogic_config_ids', true );
    if ( ! is_array( $config_ids ) ) {
        $config_ids = array();
    }

    // Output a nonce field for security.
    wp_nonce_field( 'calculogic_save_configurations_meta_box', 'calculogic_meta_box_nonce' );

    // Query all published configurations.
    $config_query = new WP_Query( array(
        'post_type'      => 'calculogic_config',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ) );

    echo '<p>' . __( 'Select the configurations to associate with this type:', 'calculogic' ) . '</p>';

    if ( $config_query->have_posts() ) {
        while ( $config_query->have_posts() ) {
            $config_query->the_post();
            $id    = get_the_ID();
            $title = get_the_title();
            // Check if this configuration is already associated.
            $checked = in_array( $id, $config_ids ) ? 'checked="checked"' : '';
            echo '<label style="display:block;">';
            echo '<input type="checkbox" name="calculogic_config_ids[]" value="' . esc_attr( $id ) . '" ' . $checked . '> ' . esc_html( $title );
            echo '</label>';
        }
        wp_reset_postdata();
    } else {
        echo '<p>' . __( 'No configurations available.', 'calculogic' ) . '</p>';
    }
}

// 4. Save the Meta Box Data
function calculogic_save_configurations_meta_box( $post_id ) {
    // Verify the nonce before proceeding.
    if ( ! isset( $_POST['calculogic_meta_box_nonce'] ) ||
         ! wp_verify_nonce( $_POST['calculogic_meta_box_nonce'], 'calculogic_save_configurations_meta_box' ) ) {
        return;
    }

    // Avoid interfering with autosave.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Sanitize and update the meta field with the selected configuration IDs.
    $config_ids = isset( $_POST['calculogic_config_ids'] ) ? array_map( 'intval', $_POST['calculogic_config_ids'] ) : array();
    update_post_meta( $post_id, 'calculogic_config_ids', $config_ids );
}
add_action( 'save_post', 'calculogic_save_configurations_meta_box' );

/*
 * Future considerations:
 * - Versioning: Consider including a version field within your configuration JSON.
 * - Migrations: Write migration functions that update older configurations to the latest schema.
 * - Export/Transformation: With unified JSON, you can later build a transformation layer to export this configuration to other programming languages or formats.
 */