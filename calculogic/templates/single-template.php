<?php
/**
 * Template for displaying individual template items.
 *
 * This file defines the layout and structure for displaying
 * individual template items in the front-end.
 *
 * @package Calculogic
 */

// Get the global post object
global $post;

// Ensure the post type is 'template'
if ( 'template' !== get_post_type( $post ) ) {
    return;
}

// Display the template title
echo '<h1>' . esc_html( get_the_title( $post ) ) . '</h1>';

// Display the content of the template
echo '<div class="calculogic-template-content">';
    echo apply_filters( 'the_content', $post->post_content );
echo '</div>';

// Optionally, display custom fields or metadata related to the template
$template_meta = get_post_meta( $post->ID );
if ( ! empty( $template_meta ) ) {
    echo '<div class="calculogic-template-meta">';
        foreach ( $template_meta as $key => $value ) {
            echo '<p><strong>' . esc_html( $key ) . ':</strong> ' . esc_html( implode( ', ', $value ) ) . '</p>';
        }
    echo '</div>';
}
?>