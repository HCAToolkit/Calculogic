/**
 * Calculogic Plugin JavaScript
 * This file contains the JavaScript logic for the Calculogic plugin's builder interface.
 * It handles user interactions, AJAX calls, and dynamic updates to the builder UI.
 *
 * Dependencies: jQuery
 */

jQuery(document).ready(function($) {
    /**
     * Load Items via AJAX
     *
     * This function sends an AJAX request to the WordPress backend to fetch existing builder items.
     * The response is dynamically inserted into the builder interface.
     */
    function loadItems(search = '', filter = 'all') {
        $.ajax({
            url: ajaxurl, // WordPress-provided global variable for AJAX requests
            method: 'POST', // HTTP method for the request
            data: {
                action: 'load_calculogic_items', // Custom action to handle the request in PHP
                search: search,
                filter: filter
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

    // Initialize items on page load
    loadItems();

    /**
     * Event Listener: Search Functionality
     *
     * Triggered when the user types in the search input field. This filters the builder items
     * based on the search query and selected filter.
     */
    $('#calculogic-search').on('input', function() {
        const search = $(this).val();
        const filter = $('#calculogic-filter').val();
        loadItems(search, filter);
    });

    /**
     * Event Listener: Filter Functionality
     *
     * Triggered when the user selects a filter option. This filters the builder items
     * based on the selected filter and search query.
     */
    $('#calculogic-filter').on('change', function() {
        const search = $('#calculogic-search').val();
        const filter = $(this).val();
        loadItems(search, filter);
    });

    /**
     * Event Listener: Create New Item
     *
     * Triggered when the "New" button is clicked. Sends an AJAX request to create a new item.
     */
    $('#calculogic-new').on('click', function() {
        const title = prompt('Enter the title for the new item:');
        const type = prompt('Enter the type (calculator, quiz, template):');

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
                        alert('Item created successfully!');
                        loadItems(); // Reload items
                    } else {
                        alert(response.data || 'Failed to create item.');
                    }
                },
                error: function() {
                    alert('Error creating item. Please try again.');
                }
            });
        }
    });

    /**
     * Event Listener: Read Item
     *
     * Triggered when an item is clicked. Sends an AJAX request to fetch the item details.
     */
    $(document).on('click', '.calculogic-item', function() {
        const itemId = $(this).data('id');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'read_calculogic_item',
                nonce: calculogic_nonce,
                id: itemId
            },
            success: function(response) {
                if (response.success) {
                    alert(`Title: ${response.data.title}\nContent: ${response.data.content}`);
                } else {
                    alert(response.data || 'Failed to fetch item details.');
                }
            },
            error: function() {
                alert('Error fetching item details. Please try again.');
            }
        });
    });

    /**
     * Event Listener: Update Item
     *
     * Triggered when the "Duplicate" button is clicked. Sends an AJAX request to update an item.
     */
    $('#calculogic-duplicate').on('click', function() {
        const itemId = prompt('Enter the ID of the item to duplicate:');
        const newTitle = prompt('Enter the new title for the duplicated item:');

        if (itemId && newTitle) {
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'update_calculogic_item',
                    nonce: calculogic_nonce,
                    id: itemId,
                    title: newTitle
                },
                success: function(response) {
                    if (response.success) {
                        alert('Item updated successfully!');
                        loadItems(); // Reload items
                    } else {
                        alert(response.data || 'Failed to update item.');
                    }
                },
                error: function() {
                    alert('Error updating item. Please try again.');
                }
            });
        }
    });

    /**
     * Event Listener: Delete Item
     *
     * Triggered when the "Delete" button is clicked. Sends an AJAX request to delete an item.
     */
    $('#calculogic-delete').on('click', function() {
        const itemId = prompt('Enter the ID of the item to delete:');

        if (itemId) {
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'delete_calculogic_item',
                    nonce: calculogic_nonce,
                    id: itemId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Item deleted successfully!');
                        loadItems(); // Reload items
                    } else {
                        alert(response.data || 'Failed to delete item.');
                    }
                },
                error: function() {
                    alert('Error deleting item. Please try again.');
                }
            });
        }
    });

    /**
     * Event Listener: Collaborator Settings
     *
     * Triggered when the "Collaborators" button is clicked. This opens a modal or interface
     * to manage collaborators for the selected item.
     */
    $('#calculogic-collaborators').on('click', function() {
        alert('Open collaborator settings.');
        // Additional logic to manage collaborators can be added here
    });
});