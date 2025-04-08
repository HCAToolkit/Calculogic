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
$item_title = $item_id ? get_the_title( $item_id ) : '';
?>

<div id="calculogic-builder">
    <h2><?php echo $item_id ? __( 'Edit Item', 'calculogic' ) : __( 'Create New Item', 'calculogic' ); ?></h2>

    <!-- Initial Settings Tab -->
    <div id="initial-settings-tab">
        <form id="initial-settings-form">
            <label for="calculogic-item-title"><?php _e( 'Name/Title:', 'calculogic' ); ?></label>
            <input type="text" id="calculogic-item-title" name="title" value="<?php echo esc_attr( $item_title ); ?>" required>

            <label for="calculogic-item-type"><?php _e( 'Item Type:', 'calculogic' ); ?></label>
            <select id="calculogic-item-type" name="type" required>
                <option value="form" <?php selected( $item_type, 'form' ); ?>><?php _e( 'Form Template', 'calculogic' ); ?></option>
                <option value="calculator" <?php selected( $item_type, 'calculator' ); ?>><?php _e( 'Calculator', 'calculogic' ); ?></option>
                <option value="quiz" <?php selected( $item_type, 'quiz' ); ?>><?php _e( 'Quiz', 'calculogic' ); ?></option>
            </select>

            <button type="submit"><?php echo $item_id ? __( 'Save & Continue', 'calculogic' ) : __( 'Create & Continue', 'calculogic' ); ?></button>
        </form>
    </div>

    <!-- Content Tabs -->
    <div id="content-tabs" style="display: none;">
        <ul class="tab-navigation">
            <li><a href="#build-tab"><?php _e( 'Build', 'calculogic' ); ?></a></li>
            <li><a href="#workflow-tab"><?php _e( 'Workflow', 'calculogic' ); ?></a></li>
            <li><a href="#view-tab"><?php _e( 'View', 'calculogic' ); ?></a></li>
            <li><a href="#results-tab"><?php _e( 'Results', 'calculogic' ); ?></a></li>
        </ul>

        <div id="build-tab" class="tab-content">
            <h3><?php _e( 'Build Tab', 'calculogic' ); ?></h3>
            <!-- Add Build Tab Content Here -->
        </div>

        <div id="workflow-tab" class="tab-content">
            <h3><?php _e( 'Workflow Tab', 'calculogic' ); ?></h3>
            <!-- Add Workflow Tab Content Here -->
        </div>

        <div id="view-tab" class="tab-content">
            <h3><?php _e( 'View Tab', 'calculogic' ); ?></h3>
            <!-- Add View Tab Content Here -->
        </div>

        <div id="results-tab" class="tab-content">
            <h3><?php _e( 'Results Tab', 'calculogic' ); ?></h3>
            <!-- Add Results Tab Content Here -->
        </div>
    </div>
</div>

<script type="text/javascript">
(function($) {
    // Handle Initial Settings Form Submission
    $('#initial-settings-form').on('submit', function(e) {
        e.preventDefault();

        const data = {
            action: '<?php echo $item_id ? 'update_calculogic_item' : 'create_calculogic_item'; ?>',
            nonce: calculogic_nonce,
            id: <?php echo $item_id; ?>,
            title: $('#calculogic-item-title').val(),
            type: $('#calculogic-item-type').val()
        };

        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                alert('<?php echo __( "Settings saved successfully!", "calculogic" ); ?>');
                $('#initial-settings-tab').hide();
                $('#content-tabs').show();
            } else {
                alert(response.data || '<?php echo __( "An error occurred. Please try again.", "calculogic" ); ?>');
            }
        });
    });

    // Tab Navigation
    $('.tab-navigation a').on('click', function(e) {
        e.preventDefault();
        $('.tab-content').hide();
        $($(this).attr('href')).show();
    });

    // Show the first tab by default
    $('.tab-navigation a:first').trigger('click');
})(jQuery);
</script>
