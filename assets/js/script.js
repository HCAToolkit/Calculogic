// filepath: /calculogic/assets/js/script.js
jQuery(document).ready(function($) {
    // Initialize the Calculogic builder UI
    function initBuilder() {
        // Load existing templates, quizzes, or calculators via Ajax
        loadItems();
    }

    function loadItems() {
        // Example Ajax call to fetch items
        $.ajax({
            url: ajaxurl, // WordPress AJAX URL
            method: 'POST',
            data: {
                action: 'load_calculogic_items' // Custom action to load items
            },
            success: function(response) {
                // Handle successful response
                $('#calculogic-builder').html(response);
            },
            error: function() {
                alert('Error loading items.');
            }
        });
    }

    $('#calculogic-new').on('click', function() {
        // Launch the builder in "new" mode
        alert("New builder initiated.");
        // Additional logic to create a new item
    });

    $('#calculogic-duplicate').on('click', function() {
        // Logic for duplicating an item
        alert("Duplicate functionality goes here.");
    });

    $('#calculogic-delete').on('click', function() {
        // Logic for deleting an item
        alert("Delete functionality goes here.");
    });

    $('#calculogic-collaborators').on('click', function() {
        // Open collaborator settings
        alert("Open collaborator settings.");
    });

    // Initialize the builder on document ready
    initBuilder();
});