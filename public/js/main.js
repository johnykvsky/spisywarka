$(document).ready(function() {
    $('#loan_item').select2({
      ajax: {
        url: '/api/items/autocomplete',
        dataType: 'json',
        processResults: function (data) {
          // Transforms the top-level key of the response object from 'items' to 'results'
          return {
            results: data.items
          };
        }
        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
      }
    });

    $('#item_categories').select2({
      ajax: {
        url: '/api/categories/autocomplete',
        dataType: 'json',
        processResults: function (data) {
          // Transforms the top-level key of the response object from 'items' to 'results'
          return {
            results: data.categories
          };
        }
        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
      }
    });

    $('#item_collections').select2({
      ajax: {
        url: '/api/collections/autocomplete',
        dataType: 'json',
        processResults: function (data) {
          // Transforms the top-level key of the response object from 'items' to 'results'
          return {
            results: data.collections
          };
        }
        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
      }
    });
});
