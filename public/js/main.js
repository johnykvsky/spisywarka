$(document).ready(function() {
    $('#confirmModal').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });

    $('#loan_item').select2({
      ajax: {
        url: '/autocomplete/items',
        dataType: 'json',
        processResults: function (data) {
          return {
            results: data.items
          };
        }
      }
    });

    $('#item_category').select2({
      ajax: {
        url: '/autocomplete/categories',
        dataType: 'json',
        processResults: function (data) {
          return {
            results: data.categories
          };
        }
      }
    });

    $('#item_collections').select2({
      ajax: {
        url: '/autocomplete/collections',
        dataType: 'json',
        processResults: function (data) {
          return {
            results: data.collections
          };
        }
      }
    });
});
