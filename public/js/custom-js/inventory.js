$(document).ready(function () {
    $.noConflict();

    // Initialize DataTable
    var InventoryTable = $('#InventoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/inventory/data',
            type: 'GET',
            data: function (d) {
                d.search = $('#search').val();
                d.category = $('#category').val();
                d.status = $('#status').val();
                d.stock_status = $('#stock_status').val();
            }
        },
        columns: [
            {
                data: 'sku',
                name: 'sku'
            },
            {
                data: 'name',
                name: 'name',
                render: function (data, type, row) {
                    return data;
                }
            },
            {
                data: 'category',
                name: 'category'
            },
            {
                data: 'cost_price',
                name: 'cost_price'
            },
            {
                data: 'selling_price',
                name: 'selling_price'
            },
            {
                data: 'quantity',
                name: 'quantity',
                orderable: false
            },
            {
                data: 'stock_status',
                name: 'stock_status',
                orderable: false
            },
            {
                data: 'total_value',
                name: 'total_value',
                orderable: false
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                width: '120px'
            }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fa fa-table"></i> Excel',
                className: 'btn btn-success',
                titleAttr: 'Export to Excel',
                filename: 'Inventory_List',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fa-solid fa-file-pdf"></i> PDF',
                className: 'btn bg-purple',
                titleAttr: 'Export to Pdf',
                filename: 'Inventory_List',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-info',
                titleAttr: 'Print Table',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            }
        ],
        responsive: true,
        autoWidth: false,
        language: {
            emptyTable: "No inventory items available",
            zeroRecords: "No matching items found"
        },
        order: [[0, 'desc']]
    });

    // Apply filters
    $('#applyFilters').on('click', function () {
        InventoryTable.ajax.reload();
    });

    // Enter key in search field
    $('#search').on('keyup', function (e) {
        if (e.keyCode === 13) {
            InventoryTable.ajax.reload();
        }
    });

    // Stock Modal Handler
    $('#stockModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var itemId = button.data('item-id');
        var itemName = button.data('item-name');
        var currentStock = button.data('current-stock');

        var modal = $(this);
        modal.find('#adjustmentItemId').val(itemId);
        modal.find('#itemName').val(itemName);
        modal.find('#currentStock').val(currentStock);
        modal.find('#quantity').val('');
        modal.find('#reason').val('');
    });

    // Stock Adjustment Form Submission
    $('#stockAdjustmentForm').on('submit', function (e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var itemId = $('#adjustmentItemId').val();

        $.ajax({
            url: '/inventory/' + itemId + '/update-stock',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    $('#stockModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    InventoryTable.ajax.reload(null, false);
                }
            },
            error: function (xhr) {
                let errorMessage = 'Failed to update stock!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            }
        });
    });

    // Delete Single Item
    $(document).on('click', '.DeleteBtn', function () {
        var ID = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This item will be moved to trash!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: '/inventory/' + ID,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Item moved to trash successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        InventoryTable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let errorMessage = 'Failed to delete item!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                });
            }
        });
    });

    // Delete All Items
    $('#DeleteAllBtn').on('click', function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will move ALL items to trash! This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete all!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: '/inventory/delete-all',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'All items moved to trash successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        InventoryTable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let errorMessage = 'Failed to delete all items!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                });
            }
        });
    });

    // Auto-generate SKU
    $('#name, #category').on('blur', function () {
        if (!$('#sku').val()) {
            var name = $('#name').val();
            var category = $('#category').val();

            if (name && category) {
                var baseSKU = category.substring(0, 3).toUpperCase() +
                    name.replace(/\s+/g, '').substring(0, 5).toUpperCase();
                var random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');

                $('#sku').val(baseSKU + random);
            }
        }
    });

    // Calculate profit margin
    $('#cost_price, #selling_price').on('change', function () {
        var costPrice = parseFloat($('#cost_price').val()) || 0;
        var sellingPrice = parseFloat($('#selling_price').val()) || 0;

        if (costPrice > 0 && sellingPrice > 0) {
            var margin = ((sellingPrice - costPrice) / costPrice) * 100;
            showProfitMargin(margin);
        }
    });

    function showProfitMargin(margin) {
        var marginElement = $('#profit_margin_display');

        if (marginElement.length === 0) {
            $('#selling_price').after('<div class="form-text" id="profit_margin_display"></div>');
            marginElement = $('#profit_margin_display');
        }

        var marginClass = margin >= 0 ? 'text-success' : 'text-danger';
        marginElement.html('Profit Margin: <span class="' + marginClass + '">' + margin.toFixed(2) + '%</span>');
    }
});
