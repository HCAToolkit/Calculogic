<?php
/**
 * Custom Post Types for Calculogic
 *
 * This file registers custom post types (CPTs) and taxonomies for managing templates, quizzes, calculators,
 * and configurations. It also includes meta boxes and filters for associating configurations with types.
 *
 * @package Calculogic
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Register the Configurations CPT
 *
 * This CPT is used to store reusable configurations (e.g., JSON data for field definitions, workflows, or styling).
 * Configurations can be associated with builder items (Types CPT) via a meta box.
 */
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
        'show_in_rest'          => true, // Enables REST API support
    );
    register_post_type( 'calculogic_config', $args );
}
add_action( 'init', 'calculogic_register_config_cpt' );

/**
 * Register the Types CPT
 *
 * This unified CPT is used to manage builder items such as calculators, quizzes, and templates.
 * Each type can be associated with one or more configurations via a meta box.
 */
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
        'show_in_rest'          => true, // Enables REST API support
    );
    register_post_type( 'calculogic_type', $args );
}
add_action( 'init', 'calculogic_register_type_cpt' );

/**
 * Add a Meta Box to the Types CPT to Associate Configurations
 *
 * This meta box allows users to associate one or more configurations with a builder item (Types CPT).
 */
function calculogic_add_configurations_meta_box() {
    add_meta_box(
        'calculogic_configurations_meta_box',         // Unique ID for the meta box
        __( 'Associated Configurations', 'calculogic' ), // Title displayed in the meta box
        'calculogic_render_configurations_meta_box',    // Callback function that renders the meta box
        'calculogic_type',                              // CPT where this meta box should appear
        'normal',                                       // Context: normal, side, etc.
        'high'                                          // Priority
    );
}
add_action( 'add_meta_boxes', 'calculogic_add_configurations_meta_box' );

/**
 * Render the Configurations Meta Box
 *
 * This function outputs a list of all available configurations with checkboxes, allowing users to associate
 * configurations with the current builder item.
 */
function calculogic_render_configurations_meta_box( $post ) {
    // Retrieve existing configuration IDs associated with this type
    $config_ids = get_post_meta( $post->ID, 'calculogic_config_ids', true );
    if ( ! is_array( $config_ids ) ) {
        $config_ids = array();
    }

    // Output a nonce field for security
    wp_nonce_field( 'calculogic_save_configurations_meta_box', 'calculogic_meta_box_nonce' );

    // Query all published configurations
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
            // Check if this configuration is already associated
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

/**
 * Save the Configurations Meta Box Data
 *
 * This function saves the selected configuration IDs to the post meta for the current builder item.
 */
function calculogic_save_configurations_meta_box( $post_id ) {
    // Verify the nonce before proceeding
    if ( ! isset( $_POST['calculogic_meta_box_nonce'] ) ||
         ! wp_verify_nonce( $_POST['calculogic_meta_box_nonce'], 'calculogic_save_configurations_meta_box' ) ) {
        return;
    }

    // Avoid interfering with autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Sanitize and update the meta field with the selected configuration IDs
    $config_ids = isset( $_POST['calculogic_config_ids'] ) ? array_map( 'intval', $_POST['calculogic_config_ids'] ) : array();
    update_post_meta( $post_id, 'calculogic_config_ids', $config_ids );
}
add_action( 'save_post', 'calculogic_save_configurations_meta_box' );

/**
 * Register Taxonomy for Calculogic Types
 *
 * This taxonomy allows users to categorize builder items (Types CPT) for better organization and filtering.
 */
function calculogic_register_type_taxonomy() {
    $labels = array(
        'name'              => _x( 'Type Categories', 'taxonomy general name', 'calculogic' ),
        'singular_name'     => _x( 'Type Category', 'taxonomy singular name', 'calculogic' ),
        'search_items'      => __( 'Search Type Categories', 'calculogic' ),
        'all_items'         => __( 'All Type Categories', 'calculogic' ),
        'parent_item'       => __( 'Parent Type Category', 'calculogic' ),
        'parent_item_colon' => __( 'Parent Type Category:', 'calculogic' ),
        'edit_item'         => __( 'Edit Type Category', 'calculogic' ),
        'update_item'       => __( 'Update Type Category', 'calculogic' ),
        'add_new_item'      => __( 'Add New Type Category', 'calculogic' ),
        'new_item_name'     => __( 'New Type Category Name', 'calculogic' ),
        'menu_name'         => __( 'Type Category', 'calculogic' ),
    );

    $args = array(
        'hierarchical'      => true, // Works like categories
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'type-category' ),
    );

    register_taxonomy( 'calculogic_type_category', array( 'calculogic_type' ), $args );
}
add_action( 'init', 'calculogic_register_type_taxonomy' );

/**
 * Register Taxonomy for Calculogic Configurations
 *
 * This taxonomy allows users to categorize configurations for better organization and filtering.
 */
function calculogic_register_config_taxonomy() {
    $labels = array(
        'name'              => _x( 'Configuration Categories', 'taxonomy general name', 'calculogic' ),
        'singular_name'     => _x( 'Configuration Category', 'taxonomy singular name', 'calculogic' ),
        'search_items'      => __( 'Search Configuration Categories', 'calculogic' ),
        'all_items'         => __( 'All Configuration Categories', 'calculogic' ),
        'parent_item'       => __( 'Parent Configuration Category', 'calculogic' ),
        'parent_item_colon' => __( 'Parent Configuration Category:', 'calculogic' ),
        'edit_item'         => __( 'Edit Configuration Category', 'calculogic' ),
        'update_item'       => __( 'Update Configuration Category', 'calculogic' ),
        'add_new_item'      => __( 'Add New Configuration Category', 'calculogic' ),
        'new_item_name'     => __( 'New Configuration Category Name', 'calculogic' ),
        'menu_name'         => __( 'Configuration Category', 'calculogic' ),
    );

    $args = array(
        'hierarchical'      => true, // Works like categories
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'config-category' ),
    );

    register_taxonomy( 'calculogic_config_category', array( 'calculogic_config' ), $args );
}
add_action( 'init', 'calculogic_register_config_taxonomy' );

/**
 * Register the Results CPT
 *
 * This CPT captures user inputs and computed outputs when a calculogic_Type is taken or filled.
 */
function calculogic_register_results_cpt() {
    $labels = array(
        'name'                  => __( 'Calculogic Results', 'calculogic' ),
        'singular_name'         => __( 'Calculogic Result', 'calculogic' ),
        'menu_name'             => __( 'Results', 'calculogic' ),
        'add_new'               => __( 'Add New Result', 'calculogic' ),
        'add_new_item'          => __( 'Add New Result', 'calculogic' ),
        'edit_item'             => __( 'Edit Result', 'calculogic' ),
        'new_item'              => __( 'New Result', 'calculogic' ),
        'view_item'             => __( 'View Result', 'calculogic' ),
        'search_items'          => __( 'Search Results', 'calculogic' ),
        'not_found'             => __( 'No results found', 'calculogic' ),
        'not_found_in_trash'    => __( 'No results found in trash', 'calculogic' ),
    );
    $args = array(
        'labels'                => $labels,
        'public'                => false,
        'has_archive'           => false,
        'supports'              => array( 'title', 'editor' ),
        'rewrite'               => false,
        'show_in_rest'          => false,
    );
    register_post_type( 'calculogic_results', $args );
}
add_action( 'init', 'calculogic_register_results_cpt' );

/**
 * Register the Knowledge CPT
 *
 * This CPT stores domain data entries (e.g., JS classes/objects) that can be referenced by calculogic_Type.
 */
function calculogic_register_knowledge_cpt() {
    $labels = array(
        'name'                  => __( 'Calculogic Knowledge', 'calculogic' ),
        'singular_name'         => __( 'Calculogic Knowledge', 'calculogic' ),
        'menu_name'             => __( 'Knowledge', 'calculogic' ),
        'add_new'               => __( 'Add New Knowledge', 'calculogic' ),
        'add_new_item'          => __( 'Add New Knowledge', 'calculogic' ),
        'edit_item'             => __( 'Edit Knowledge', 'calculogic' ),
        'new_item'              => __( 'New Knowledge', 'calculogic' ),
        'view_item'             => __( 'View Knowledge', 'calculogic' ),
        'search_items'          => __( 'Search Knowledge', 'calculogic' ),
        'not_found'             => __( 'No knowledge entries found', 'calculogic' ),
        'not_found_in_trash'    => __( 'No knowledge entries found in trash', 'calculogic' ),
    );
    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'supports'              => array( 'title', 'editor' ),
        'rewrite'               => array( 'slug' => 'calculogic-knowledge' ),
        'show_in_rest'          => true,
    );
    register_post_type( 'calculogic_knowledge', $args );
}
add_action( 'init', 'calculogic_register_knowledge_cpt' );

/**
 * Add Meta Boxes for JSON Config and Knowledge References
 */
function calculogic_add_json_meta_boxes() {
    add_meta_box(
        'calculogic_json_meta_box',
        __( 'JSON Configuration', 'calculogic' ),
        'calculogic_render_json_meta_box',
        'calculogic_type',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'calculogic_add_json_meta_boxes' );

function calculogic_render_json_meta_box( $post ) {
    $config_json = get_post_meta( $post->ID, '_calc_config_json', true );
    $knowledge_refs = get_post_meta( $post->ID, '_calc_knowledge_refs', true );

    echo '<label for="calc_config_json">' . __( 'Configuration JSON:', 'calculogic' ) . '</label>';
    echo '<textarea id="calc_config_json" name="calc_config_json" rows="10" style="width:100%;">' . esc_textarea( $config_json ) . '</textarea>';

    echo '<label for="calc_knowledge_refs">' . __( 'Knowledge References (comma-separated IDs):', 'calculogic' ) . '</label>';
    echo '<input type="text" id="calc_knowledge_refs" name="calc_knowledge_refs" value="' . esc_attr( implode( ',', (array) $knowledge_refs ) ) . '" style="width:100%;" />';
}

function calculogic_save_json_meta_box( $post_id ) {
    if ( isset( $_POST['calc_config_json'] ) ) {
        update_post_meta( $post_id, '_calc_config_json', sanitize_textarea_field( $_POST['calc_config_json'] ) );
    }
    if ( isset( $_POST['calc_knowledge_refs'] ) ) {
        $refs = array_map( 'intval', explode( ',', sanitize_text_field( $_POST['calc_knowledge_refs'] ) ) );
        update_post_meta( $post_id, '_calc_knowledge_refs', $refs );
    }
}
add_action( 'save_post', 'calculogic_save_json_meta_box' );

/**
 * Handle Front-End Submission to Spawn Results Post
 */
function calculogic_handle_submission( $post_id, $user_inputs, $computed_outputs ) {
    $result_id = wp_insert_post( array(
        'post_type'   => 'calculogic_results',
        'post_title'  => sprintf( __( 'Result for %s', 'calculogic' ), get_the_title( $post_id ) ),
        'post_status' => 'publish',
        'meta_input'  => array(
            '_parent_calculogic_type' => $post_id,
            '_user_inputs'            => $user_inputs,
            '_computed_outputs'       => $computed_outputs,
        ),
    ) );

    return $result_id;
}