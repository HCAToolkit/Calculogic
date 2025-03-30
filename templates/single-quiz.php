<?php
/**
 * Template for displaying individual quiz items.
 *
 * This file defines the layout and structure for displaying a single quiz.
 *
 * @package Calculogic
 */

// Get the current quiz post
$quiz_id = get_the_ID();
$quiz_title = get_the_title( $quiz_id );
$quiz_content = apply_filters( 'the_content', get_post_field( 'post_content', $quiz_id ) );
$quiz_meta = get_post_meta( $quiz_id );

// Display the quiz title
echo '<h1>' . esc_html( $quiz_title ) . '</h1>';

// Display the quiz content
echo '<div class="quiz-content">' . $quiz_content . '</div>';

// Optionally display any custom meta fields related to the quiz
if ( ! empty( $quiz_meta ) ) {
    echo '<div class="quiz-meta">';
    foreach ( $quiz_meta as $key => $value ) {
        echo '<p><strong>' . esc_html( $key ) . ':</strong> ' . esc_html( implode( ', ', (array) $value ) ) . '</p>';
    }
    echo '</div>';
}

// Add any additional functionality or UI elements for the quiz here
?>