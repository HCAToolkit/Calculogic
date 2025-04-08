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

// Redirect to dashboard if item_id is invalid
if ( ! $item_id && isset( $_GET['item_id'] ) ) {
    wp_redirect( home_url( '/calculogic-dashboard/' ) );
    exit;
}
?>
<div id="calculogic-builder">
    <h2><?php echo $item_id ? __( 'Edit Item', 'calculogic' ) : __( 'Create New Item', 'calculogic' ); ?></h2>

    <!-- Initial Settings Tab -->
    <div id="initial-settings-tab" style="<?php echo $item_id ? 'display: none;' : ''; ?>">
        <form id="initial-settings-form">
            <label for="calculogic-item-title"><?php _e( 'Name/Title:', 'calculogic' ); ?></label>
            <input type="text" id="calculogic-item-title" name="title" aria-required="true" value="<?php echo esc_attr( $item_title ); ?>" required>

            <label for="calculogic-item-type"><?php _e( 'Item Type:', 'calculogic' ); ?></label>
            <select id="calculogic-item-type" name="type" aria-required="true" required>
                <option value="form" <?php selected( $item_type, 'form' ); ?>><?php _e( 'Form Template', 'calculogic' ); ?></option>
                <option value="calculator" <?php selected( $item_type, 'calculator' ); ?>><?php _e( 'Calculator', 'calculogic' ); ?></option>
                <option value="quiz" <?php selected( $item_type, 'quiz' ); ?>><?php _e( 'Quiz', 'calculogic' ); ?></option>
            </select>

            <button type="submit"><?php echo $item_id ? __( 'Save & Continue', 'calculogic' ) : __( 'Create & Continue', 'calculogic' ); ?></button>
        </form>
    </div>

    <!-- Success Message -->
    <div id="success-message" style="display: none; color: green; text-align: center; margin-top: 20px;">
        <?php _e( 'Settings saved successfully! You can now configure your item.', 'calculogic' ); ?>
    </div>

    <!-- Content Tabs -->
    <div id="content-tabs" style="<?php echo $item_id ? 'display: block;' : 'display: none;'; ?>">
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
    // Ensure calculogic_data is defined
    if (typeof calculogic_data === 'undefined') {
        console.error('calculogic_data is not defined. Ensure wp_localize_script is working correctly.');
        alert('<?php echo __( "A critical error occurred. Please contact the administrator.", "calculogic" ); ?>');
        return;
    }

    // Handle Initial Settings Form Submission
    $('#initial-settings-form').on('submit', function(e) {
        e.preventDefault();

        const title = $('#calculogic-item-title').val();
        const type = $('#calculogic-item-type').val();

        if (!title || !type) {
            alert('<?php echo __( "Please fill out all required fields.", "calculogic" ); ?>');
            return;
        }

        const data = {
            action: '<?php echo $item_id ? 'update_calculogic_item' : 'create_calculogic_item'; ?>',
            nonce: calculogic_data.nonce,
            id: <?php echo $item_id; ?>,
            title: title,
            type: type
        };

        $.post(calculogic_data.ajaxurl, data, function(response) {
            if (response.success) {
                $('#success-message').show();
                setTimeout(function() {
                    $('#success-message').fadeOut();
                }, 3000);

                if (!<?php echo $item_id; ?>) {
                    const newUrl = '<?php echo home_url( "/calculogic-builder/" ); ?>?item_id=' + response.data.id;
                    window.history.pushState({ path: newUrl }, '', newUrl);
                }

                $('#initial-settings-tab').hide();
                $('#content-tabs').show();
            } else {
                console.error('Error:', response.data || 'Unknown error occurred.');
                alert(response.data || '<?php echo __( "An error occurred. Please try again.", "calculogic" ); ?>');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            alert('<?php echo __( "Failed to communicate with the server. Please try again.", "calculogic" ); ?>');
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