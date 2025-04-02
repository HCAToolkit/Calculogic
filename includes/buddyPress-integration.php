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
    // Display role-based messaging
    if ( current_user_can( 'manage_options' ) ) {
        echo '<h2>Administrator Dashboard</h2>';
        echo '<p>You can manage and save official Calculogic forms/quizzes from here.</p>';
    } else {
        echo '<h2>User Dashboard</h2>';
        echo '<p>Create and manage your personal templates and quizzes here.</p>';
    }

    // Dashboard container – the builder UI will be rendered here
    echo '<div id="calculogic-dashboard">';
        // Placeholder container – your JavaScript will load the builder UI
        echo '<div id="calculogic-builder"></div>';

        // Controls: buttons for New, Duplicate, Delete, Assign Collaborator
        echo '<div class="calculogic-controls">';
            echo '<button id="calculogic-new" type="button">' . __( 'New Template/Quiz', 'calculogic' ) . '</button>';
            echo '<button id="calculogic-duplicate" type="button">' . __( 'Duplicate', 'calculogic' ) . '</button>';
            echo '<button id="calculogic-delete" type="button">' . __( 'Delete', 'calculogic' ) . '</button>';
            echo '<button id="calculogic-collaborators" type="button">' . __( 'Collaborator Settings', 'calculogic' ) . '</button>';
        echo '</div>';
    echo '</div>';

    // Inline JavaScript for button functionality
    ?>
    <script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            // Event listener for the "New" button
            $('#calculogic-new').on('click', function() {
                alert("New builder initiated.");
            });

            // Event listener for the "Duplicate" button
            $('#calculogic-duplicate').on('click', function() {
                alert("Duplicate functionality goes here.");
            });

            // Event listener for the "Delete" button
            $('#calculogic-delete').on('click', function() {
                alert("Delete functionality goes here.");
            });

            // Event listener for the "Collaborators" button
            $('#calculogic-collaborators').on('click', function() {
                alert("Open collaborator settings.");
            });
        });
    })(jQuery);
    </script>
    <?php
}
?>