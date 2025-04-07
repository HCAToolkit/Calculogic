<?php
/**
 * Builder Interface for Calculogic
 *
 * This file provides the UI for creating or editing items of the `calculogic_type` CPT.
 *
 * @package Calculogic
 */

// Ensure this file is accessed through WordPress
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get the item ID and type (if editing an existing item)
$item_id = isset( $_GET['item_id'] ) ? intval( $_GET['item_id'] ) : 0;
$item_type = $item_id ? get_post_meta( $item_id, 'calculogic_item_type', true ) : '';

// Get the item title and content (if editing)
$item_title = $item_id ? get_the_title( $item_id ) : '';
$item_content = $item_id ? get_post_field( 'post_content', $item_id ) : '';

// Display the builder interface
?>
<div id="calculogic-builder">
    <h2><?php echo $item_id ? __( 'Edit Item', 'calculogic' ) : __( 'Create New Item', 'calculogic' ); ?></h2>
    <form id="calculogic-builder-form">
        <label for="calculogic-item-title"><?php _e( 'Title:', 'calculogic' ); ?></label>
        <input type="text" id="calculogic-item-title" name="title" value="<?php echo esc_attr( $item_title ); ?>" required>

        <label for="calculogic-item-type"><?php _e( 'Type:', 'calculogic' ); ?></label>
        <select id="calculogic-item-type" name="type" required>
            <option value="calculator" <?php selected( $item_type, 'calculator' ); ?>><?php _e( 'Calculator', 'calculogic' ); ?></option>
            <option value="quiz" <?php selected( $item_type, 'quiz' ); ?>><?php _e( 'Quiz', 'calculogic' ); ?></option>
            <option value="template" <?php selected( $item_type, 'template' ); ?>><?php _e( 'Template', 'calculogic' ); ?></option>
        </select>

        <label for="calculogic-item-content"><?php _e( 'Content:', 'calculogic' ); ?></label>
        <textarea id="calculogic-item-content" name="content" rows="10"><?php echo esc_textarea( $item_content ); ?></textarea>

        <button type="submit"><?php echo $item_id ? __( 'Save Changes', 'calculogic' ) : __( 'Create Item', 'calculogic' ); ?></button>
    </form>
</div>

<script type="text/javascript">
(function($) {
    $('#calculogic-builder-form').on('submit', function(e) {
        e.preventDefault();

        const data = {
            action: 'create_calculogic_item',
            nonce: calculogic_nonce,
            title: $('#calculogic-item-title').val(),
            type: $('#calculogic-item-type').val(),
            content: $('#calculogic-item-content').val()
        };

        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                alert('<?php echo __( "Item created successfully!", "calculogic" ); ?>');
                window.location.href = '<?php echo home_url( "/calculogic-dashboard/" ); ?>';
            } else {
                alert(response.data || '<?php echo __( "An error occurred. Please try again.", "calculogic" ); ?>');
            }
        });
    });
})(jQuery);
</script>
