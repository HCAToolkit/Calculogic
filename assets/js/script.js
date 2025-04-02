/**
 * Calculogic Plugin JavaScript
 * This file contains the JavaScript logic for the Calculogic plugin's builder interface.
 * It handles user interactions, AJAX calls, and dynamic updates to the builder UI.
 *
 * Dependencies: jQuery
 */

jQuery(document).ready(function($) {
    /**
     * Initialize the Calculogic Builder UI
     *
     * This function sets up the builder interface by loading existing items (templates, quizzes, calculators)
     * and attaching event listeners to the builder controls.
     */
    function initBuilder() {
        // Load existing items via an AJAX call
        loadItems();
    }

    /**
     * Load Items via AJAX
     *
     * This function sends an AJAX request to the WordPress backend to fetch existing builder items.
     * The response is dynamically inserted into the builder interface.
     */
    function loadItems() {
        $.ajax({
            url: ajaxurl, // WordPress-provided global variable for AJAX requests
            method: 'POST', // HTTP method for the request
            data: {
                action: 'load_calculogic_items' // Custom action to handle the request in PHP
            },
            success: function(response) {
                // Handle successful response
                // Populate the builder interface with the fetched items
                $('#calculogic-builder').html(response);
            },
            error: function() {
                // Handle errors during the AJAX request
                alert('Error loading items. Please try again.');
            }
        });
    }

    /**
     * Event Listener: Create New Item
     *
     * Triggered when the "New" button is clicked. This launches the builder in "new" mode,
     * allowing the user to create a new template, quiz, or calculator.
     */
    $('#calculogic-new').on('click', function() {
        alert("New builder initiated.");
        // Additional logic to create a new item can be added here
    });

    /**
     * Event Listener: Duplicate Item
     *
     * Triggered when the "Duplicate" button is clicked. This duplicates an existing item
     * and adds it to the builder interface.
     */
    $('#calculogic-duplicate').on('click', function() {
        alert("Duplicate functionality goes here.");
        // Additional logic to duplicate an item can be added here
    });

    /**
     * Event Listener: Delete Item
     *
     * Triggered when the "Delete" button is clicked. This deletes an existing item
     * from the builder interface and the database.
     */
    $('#calculogic-delete').on('click', function() {
        alert("Delete functionality goes here.");
        // Additional logic to delete an item can be added here
    });

    /**
     * Event Listener: Collaborator Settings
     *
     * Triggered when the "Collaborators" button is clicked. This opens a modal or interface
     * to manage collaborators for the selected item.
     */
    $('#calculogic-collaborators').on('click', function() {
        alert("Open collaborator settings.");
        // Additional logic to manage collaborators can be added here
    });

    // Initialize the builder on document ready
    initBuilder();
});