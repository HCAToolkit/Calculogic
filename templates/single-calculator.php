<?php
/**
 * Template for displaying individual calculator items.
 *
 * This file is used to render the front-end display of a calculator post type.
 *
 * @package Calculogic
 */

// Get the global post object
global $post;

// Ensure the post type is 'calculator'
if ( 'calculator' !== get_post_type( $post ) ) {
    return;
}

// Get the calculator content
$calculator_content = get_the_content( $post->ID );

// Display the calculator title
echo '<h1 class="calculator-title">' . esc_html( get_the_title( $post->ID ) ) . '</h1>';

// Display the calculator content
echo '<div class="calculator-content">' . wp_kses_post( $calculator_content ) . '</div>';

// Optionally, you can add additional functionality here, such as rendering a calculator UI or handling user input.
?>