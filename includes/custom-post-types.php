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

// Register Custom Post Types
function calculogic_register_post_types() {
    // Template Post Type
    register_post_type( 'calculogic_template', array(
        'labels' => array(
            'name' => __( 'Templates', 'calculogic' ),
            'singular_name' => __( 'Template', 'calculogic' ),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'rewrite' => array( 'slug' => 'templates' ),
    ) );

    // Quiz Post Type
    register_post_type( 'calculogic_quiz', array(
        'labels' => array(
            'name' => __( 'Quizzes', 'calculogic' ),
            'singular_name' => __( 'Quiz', 'calculogic' ),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'rewrite' => array( 'slug' => 'quizzes' ),
    ) );

    // Calculator Post Type
    register_post_type( 'calculogic_calculator', array(
        'labels' => array(
            'name' => __( 'Calculators', 'calculogic' ),
            'singular_name' => __( 'Calculator', 'calculogic' ),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'rewrite' => array( 'slug' => 'calculators' ),
    ) );
}

// Hook into the 'init' action
add_action( 'init', 'calculogic_register_post_types' );
?>