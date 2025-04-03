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
        // Search and filter controls
        echo '<div class="calculogic-search-filter">';
            echo '<input type="text" id="calculogic-search" placeholder="' . __( 'Search items...', 'calculogic' ) . '">';
            echo '<select id="calculogic-filter">';
                echo '<option value="all">' . __( 'All Types', 'calculogic' ) . '</option>';
                echo '<option value="calculator">' . __( 'Calculators', 'calculogic' ) . '</option>';
                echo '<option value="quiz">' . __( 'Quizzes', 'calculogic' ) . '</option>';
                echo '<option value="template">' . __( 'Templates', 'calculogic' ) . '</option>';
            echo '</select>';
        echo '</div>';

        // Placeholder container – items will be dynamically loaded here
        echo '<div id="calculogic-items"></div>';

        // Controls: buttons for New, Duplicate, Delete, Assign Collaborator
        echo '<div class="calculogic-controls">';
            echo '<button id="calculogic-new" type="button">' . __( 'New Template/Quiz', 'calculogic' ) . '</button>';
            echo '<button id="calculogic-duplicate" type="button">' . __( 'Duplicate', 'calculogic' ) . '</button>';
            echo '<button id="calculogic-delete" type="button">' . __( 'Delete', 'calculogic' ) . '</button>';
            echo '<button id="calculogic-collaborators" type="button">' . __( 'Collaborator Settings', 'calculogic' ) . '</button>';
        echo '</div>';
    echo '</div>';

    // Inline JavaScript for dynamic functionality
    ?>
    <script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            // Load items dynamically
            function loadItems(search = '', filter = 'all') {
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'load_calculogic_items',
                        search: search,
                        filter: filter
                    },
                    success: function(response) {
                        $('#calculogic-items').html(response);
                    },
                    error: function() {
                        alert('<?php echo __( "Error loading items. Please try again.", "calculogic" ); ?>');
                    }
                });
            }

            // Initialize items on page load
            loadItems();

            // Search functionality
            $('#calculogic-search').on('input', function() {
                const search = $(this).val();
                const filter = $('#calculogic-filter').val();
                loadItems(search, filter);
            });

            // Filter functionality
            $('#calculogic-filter').on('change', function() {
                const search = $('#calculogic-search').val();
                const filter = $(this).val();
                loadItems(search, filter);
            });

            // Event listeners for buttons
            $('#calculogic-new').on('click', function() {
                alert('<?php echo __( "New builder initiated.", "calculogic" ); ?>');
            });

            $('#calculogic-duplicate').on('click', function() {
                alert('<?php echo __( "Duplicate functionality goes here.", "calculogic" ); ?>');
            });

            $('#calculogic-delete').on('click', function() {
                alert('<?php echo __( "Delete functionality goes here.", "calculogic" ); ?>');
            });

            $('#calculogic-collaborators').on('click', function() {
                alert('<?php echo __( "Open collaborator settings.", "calculogic" ); ?>');
            });
        });
    })(jQuery);
    </script>
    <?php
}
?>