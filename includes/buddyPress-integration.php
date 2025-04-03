<?php
/**
 * BuddyPress Integration for Calculogic
 *
 * This file contains the code to integrate Calculogic with BuddyPress.
 * It adds a new tab to the BuddyPress profile for users to access their
 * Calculogic dashboard. The dashboard allows users to manage templates,
 * quizzes, and other builder items.
 *
 * @package Calculogic
 */

// Ensure BuddyPress is active before proceeding
if ( function_exists( 'bp_core_new_nav_item' ) ) {

    /**
     * Register the Calculogic Dashboard Tab
     *
     * This function adds a new tab to the BuddyPress user profile navigation.
     * The tab is only visible to the logged-in user viewing their own profile
     * or to administrators with the `manage_options` capability.
     */
    function calculogic_register_dashboard_tab() {
        // Check if the current user is viewing their own profile or is an admin
        if ( bp_is_my_profile() || current_user_can( 'manage_options' ) ) {
            bp_core_new_nav_item( array(
                'name'                    => __( 'Calculogic Dashboard', 'calculogic' ), // Tab name
                'slug'                    => 'calculogic-dashboard', // URL slug for the tab
                'default_subnav_slug'     => 'calculogic-dashboard', // Default sub-tab slug
                'position'                => 40, // Position in the navigation menu
                'show_for_displayed_user' => true, // Only show for the profile owner
                'screen_function'         => 'calculogic_dashboard_screen', // Callback for tab content
                'item_css_id'             => 'calculogic-dashboard' // CSS ID for the tab
            ) );
        }
    }
    add_action( 'bp_setup_nav', 'calculogic_register_dashboard_tab', 100 );
}

/**
 * Dashboard Screen Function
 *
 * This function sets up the content for the Calculogic Dashboard tab.
 * It hooks the content into the BuddyPress template system and loads
 * the appropriate template for rendering.
 */
function calculogic_dashboard_screen() {
    // Hook the dashboard content into the BuddyPress template content hook
    add_action( 'bp_template_content', 'calculogic_dashboard_content' );

    // Load the BuddyPress template for custom plugins
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * Dashboard Content Function
 *
 * This function outputs the content for the Calculogic Dashboard tab.
 * It includes role-based messaging, a placeholder for the builder UI,
 * and controls for managing templates, quizzes, and collaborators.
 */
function calculogic_dashboard_content() {
    $current_user_id = get_current_user_id();
    $post_types = array( 'calculator', 'quiz', 'template' );

    echo '<h2>' . __( 'Your Calculogic Items', 'calculogic' ) . '</h2>';
    echo '<button id="calculogic-create-new" type="button">' . __( 'Create New Item', 'calculogic' ) . '</button>';
    echo '<div id="calculogic-dashboard">';

    foreach ( $post_types as $post_type ) {
        $posts = get_posts( array(
            'post_type'   => $post_type,
            'author'      => $current_user_id,
            'post_status' => 'publish',
            'numberposts' => -1,
        ) );

        if ( ! empty( $posts ) ) {
            echo '<h3>' . ucfirst( $post_type ) . 's</h3>';
            echo '<table class="calculogic-table">';
            echo '<thead><tr><th>' . __( 'Title', 'calculogic' ) . '</th><th>' . __( 'Actions', 'calculogic' ) . '</th></tr></thead>';
            echo '<tbody>';
            foreach ( $posts as $post ) {
                echo '<tr>';
                echo '<td>' . esc_html( $post->post_title ) . '</td>';
                echo '<td>';
                echo '<button class="calculogic-edit" data-id="' . esc_attr( $post->ID ) . '">' . __( 'Edit', 'calculogic' ) . '</button>';
                echo '<button class="calculogic-quick-edit" data-id="' . esc_attr( $post->ID ) . '">' . __( 'Quick Edit', 'calculogic' ) . '</button>';
                echo '<button class="calculogic-duplicate" data-id="' . esc_attr( $post->ID ) . '">' . __( 'Duplicate', 'calculogic' ) . '</button>';
                echo '<button class="calculogic-delete" data-id="' . esc_attr( $post->ID ) . '">' . __( 'Delete', 'calculogic' ) . '</button>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>' . sprintf( __( 'No %s found.', 'calculogic' ), $post_type ) . '</p>';
        }
    }

    echo '</div>';

    // Inline JavaScript for dynamic functionality
    ?>
    <script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            // Create new item
            $('#calculogic-create-new').on('click', function() {
                const title = prompt('<?php echo __( "Enter the title for the new item:", "calculogic" ); ?>');
                const type = prompt('<?php echo __( "Enter the type (calculator, quiz, template):", "calculogic" ); ?>');

                if (title && type) {
                    $.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'create_calculogic_item',
                            nonce: calculogic_nonce,
                            title: title,
                            type: type
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('<?php echo __( "Item created successfully!", "calculogic" ); ?>');
                                location.reload();
                            } else {
                                alert(response.data || '<?php echo __( "Failed to create item.", "calculogic" ); ?>');
                            }
                        },
                        error: function() {
                            alert('<?php echo __( "Error creating item. Please try again.", "calculogic" ); ?>');
                        }
                    });
                }
            });

            // Edit, Quick Edit, Duplicate, Delete functionality
            $('.calculogic-edit').on('click', function() {
                const id = $(this).data('id');
                window.location.href = '<?php echo admin_url( "post.php?action=edit&post=" ); ?>' + id;
            });

            $('.calculogic-quick-edit').on('click', function() {
                const id = $(this).data('id');
                const newTitle = prompt('<?php echo __( "Enter the new title:", "calculogic" ); ?>');
                if (newTitle) {
                    $.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'update_calculogic_item',
                            nonce: calculogic_nonce,
                            id: id,
                            title: newTitle
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('<?php echo __( "Item updated successfully!", "calculogic" ); ?>');
                                location.reload();
                            } else {
                                alert(response.data || '<?php echo __( "Failed to update item.", "calculogic" ); ?>');
                            }
                        },
                        error: function() {
                            alert('<?php echo __( "Error updating item. Please try again.", "calculogic" ); ?>');
                        }
                    });
                }
            });

            $('.calculogic-duplicate').on('click', function() {
                const id = $(this).data('id');
                const newTitle = prompt('<?php echo __( "Enter the title for the duplicated item:", "calculogic" ); ?>');
                if (newTitle) {
                    $.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'duplicate_calculogic_item',
                            nonce: calculogic_nonce,
                            id: id,
                            title: newTitle
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('<?php echo __( "Item duplicated successfully!", "calculogic" ); ?>');
                                location.reload();
                            } else {
                                alert(response.data || '<?php echo __( "Failed to duplicate item.", "calculogic" ); ?>');
                            }
                        },
                        error: function() {
                            alert('<?php echo __( "Error duplicating item. Please try again.", "calculogic" ); ?>');
                        }
                    });
                }
            });

            $('.calculogic-delete').on('click', function() {
                const id = $(this).data('id');
                if (confirm('<?php echo __( "Are you sure you want to delete this item?", "calculogic" ); ?>')) {
                    $.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'delete_calculogic_item',
                            nonce: calculogic_nonce,
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('<?php echo __( "Item deleted successfully!", "calculogic" ); ?>');
                                location.reload();
                            } else {
                                alert(response.data || '<?php echo __( "Failed to delete item.", "calculogic" ); ?>');
                            }
                        },
                        error: function() {
                            alert('<?php echo __( "Error deleting item. Please try again.", "calculogic" ); ?>');
                        }
                    });
                }
            });
        });
    })(jQuery);
    </script>
    <?php
}
?>