// Register a custom BuddyPress dashboard tab
function calculogic_register_dashboard_tab() {
    bp_core_new_nav_item( array(
        'name'                    => __( 'Calculogic Dashboard', 'calculogic' ),
        'slug'                    => 'calculogic-dashboard',
        'position'                => 40,
        'parent_url'              => bp_displayed_user_domain(),
        'parent_slug'             => bp_get_member_slug(),
        'screen_function'         => 'calculogic_dashboard_screen',
        'show_for_displayed_user' => true,
        'user_has_access'         => true,
    ) );
}
add_action( 'bp_setup_nav', 'calculogic_register_dashboard_tab', 100 );

// Define the screen function for the custom tab
function calculogic_dashboard_screen() {
    add_action( 'bp_template_content', 'calculogic_dashboard_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

// Output content for the custom dashboard tab
function calculogic_dashboard_content() {
    if ( current_user_can( 'manage_options' ) ) {
        echo '<h2>Administrator Dashboard</h2>';
        echo '<p>Here you can manage and save official Calculogic forms.</p>';
    } else {
        echo '<h2>User Dashboard</h2>';
        echo '<p>Create and manage your personal quizzes here.</p>';
    }
    echo '<div class="calculogic-interface">';
    echo '<p>Welcome to the Calculogic Dashboard!</p>';
    echo '</div>';
}