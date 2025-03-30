<?php
/**
 * BuddyPress Integration for Calculogic
 *
 * This file contains the code to integrate Calculogic with BuddyPress.
 * It adds a new tab to the BuddyPress profile for users to access their
 * Calculogic dashboard.
 *
 * @package Calculogic
 */
if ( function_exists( 'bp_core_new_nav_item' ) ) {

    function calculogic_register_dashboard_tab() {
        // Only show the dashboard on the logged-in user's own profile,
        // or add additional checks here if collaborators should see it.
        if ( bp_is_my_profile() || current_user_can( 'manage_options' ) ) {
            bp_core_new_nav_item( array(
                'name'                    => __( 'Calculogic Dashboard', 'calculogic' ),
                'slug'                    => 'calculogic-dashboard',
                'default_subnav_slug'     => 'calculogic-dashboard',
                'position'                => 40,
                'show_for_displayed_user' => true,
                'screen_function'         => 'calculogic_dashboard_screen',
                'item_css_id'             => 'calculogic-dashboard'
            ) );
        }
    }
    add_action( 'bp_setup_nav', 'calculogic_register_dashboard_tab', 100 );
}

function calculogic_dashboard_screen() {
    // Hook our dashboard content into the BuddyPress template content hook.
    add_action( 'bp_template_content', 'calculogic_dashboard_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function calculogic_dashboard_content() {
    // Role-based messaging: for admins show extra info
    if ( current_user_can( 'manage_options' ) ) {
        echo '<h2>Administrator Dashboard</h2>';
        echo '<p>You can manage and save official Calculogic forms/quizzes from here.</p>';
    } else {
        echo '<h2>User Dashboard</h2>';
        echo '<p>Create and manage your personal templates and quizzes here.</p>';
    }
    
    // Dashboard container – the builder UI will be rendered here.
    echo '<div id="calculogic-dashboard">';
        // A placeholder container – your JavaScript will load the builder UI.
        echo '<div id="calculogic-builder"></div>';
        
        // Controls: buttons for New, Duplicate, Delete, Assign Collaborator.
        echo '<div class="calculogic-controls">';
            echo '<button id="calculogic-new" type="button">' . __( 'New Template/Quiz', 'calculogic' ) . '</button>';
            echo '<button id="calculogic-duplicate" type="button">' . __( 'Duplicate', 'calculogic' ) . '</button>';
            echo '<button id="calculogic-delete" type="button">' . __( 'Delete', 'calculogic' ) . '</button>';
            echo '<button id="calculogic-collaborators" type="button">' . __( 'Collaborator Settings', 'calculogic' ) . '</button>';
        echo '</div>';
    echo '</div>';
    
    // Optionally: include inline JavaScript initialization.
    ?>
    <script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            $('#calculogic-new').on('click', function() {
                alert("New builder initiated.");
            });
            $('#calculogic-duplicate').on('click', function() {
                alert("Duplicate functionality goes here.");
            });
            $('#calculogic-delete').on('click', function() {
                alert("Delete functionality goes here.");
            });
            $('#calculogic-collaborators').on('click', function() {
                alert("Open collaborator settings.");
            });
        });
    })(jQuery);
    </script>
    <?php
}
?>