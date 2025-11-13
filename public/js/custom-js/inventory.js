// resources/js/inventory.js

$(document).ready(function () {
    'use strict';

    // Initialize DataTable
    const inventoryTable = $('#InventoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/inventory',
            type: 'GET',
            data: function (d) {
                d.search = $('#search').val();
                d.category = $('#category').val();
                d.status = $('#status').val();
                d.stock_status = $('#stock_status').val();
            }
        },
        columns: [
            { data: 'sku', name: 'sku' },
            {
                data: 'name',
                name: 'name',
                render: function (data, type, row) {
                    return `<div class="d-flex align-items-center">
                                ${row.image ?
                            `<img src="/storage/${row.image}" alt="${data}" class="rounded me-3" width="40" height="40">` :
                            `<div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>`
                        }
                                <div>
                                    <strong>${data}</strong>
                                    ${row.brand ? `<br><small class="text-muted">${row.brand}</small>` : ''}
                                </div>
                            </div>`;
                }
            },
            { data: 'category', name: 'category' },
            { data: 'cost_price', name: 'cost_price' },
            { data: 'selling_price', name: 'selling_price' },
            {
                data: 'quantity',
                name: 'quantity',
                render: function (data, type, row) {
                    const quantity = parseInt(data);
                    const minStock = parseInt(row.min_stock_level);
                    let quantityClass = 'text-success';

                    if (quantity === 0) {
                        quantityClass = 'text-danger';
                    } else if (quantity <= minStock) {
                        quantityClass = 'text-warning';
                    }

                    return `<span class="${quantityClass} fw-bold">${quantity.toLocaleString()}</span>
                            ${minStock > 0 ? `<br><small class="text-muted">Min: ${minStock}</small>` : ''}`;
                }
            },
            { data: 'stock_status', name: 'stock_status', orderable: false },
            { data: 'total_value', name: 'total_value', orderable: false },
            { data: 'profit_margin', name: 'profit_margin', orderable: false },
            { data: 'status', name: 'status' },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                width: '140px',
                className: 'text-center'
            }
        ],
        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                className: 'btn btn-success',
                filename: `Inventory_${new Date().toISOString().split('T')[0]}`,
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                className: 'btn btn-danger',
                filename: `Inventory_${new Date().toISOString().split('T')[0]}`,
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print me-1"></i> Print',
                className: 'btn btn-info',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],
        responsive: true,
        autoWidth: false,
        language: {
            emptyTable: "No inventory items found",
            zeroRecords: "No matching items found",
            info: "Showing _START_ to _END_ of _TOTAL_ items",
            infoEmpty: "Showing 0 to 0 of 0 items",
            infoFiltered: "(filtered from _MAX_ total items)"
        },
        order: [[0, 'desc']],
        drawCallback: function () {
            // Update summary cards after table reload
            updateSummaryCards();
        }
    });

    // Event Handlers
    $('#applyFilters').on('click', function () {
        inventoryTable.ajax.reload();
    });

    $('#search').on('keyup', function (e) {
        if (e.keyCode === 13) {
            inventoryTable.ajax.reload();
        }
    });

    // Clear filters
    $('#clearFilters').on('click', function () {
        $('#filterForm').trigger('reset');
        inventoryTable.ajax.reload();
    });

    // Stock Modal
    $('#stockModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const modal = $(this);

        modal.find('#adjustmentItemId').val(button.data('item-id'));
        modal.find('#itemName').val(button.data('item-name'));
        modal.find('#currentStock').val(button.data('current-stock'));
        modal.find('#quantity').val('');
        modal.find('#reason').val('');
        modal.find('#adjustmentType').val('add');
    });

    // Stock Adjustment Form
    $('#stockAdjustmentForm').on('submit', function (e) {
        e.preventDefault();
        adjustStock($(this).serialize());
    });

    // Delete Single Item
    $(document).on('click', '.delete-btn', function () {
        const itemId = $(this).data('id');
        const itemName = $(this).data('name');

        confirmDelete(() => deleteItem(itemId), `Are you sure you want to delete "${itemName}"?`);
    });

    // Bulk Delete
    $('#deleteSelectedBtn').on('click', function () {
        const selectedIds = getSelectedIds();

        if (selectedIds.length === 0) {
            showAlert('Please select at least one item to delete.', 'warning');
            return;
        }

        confirmDelete(() => bulkDelete(selectedIds), `Are you sure you want to delete ${selectedIds.length} selected items?`);
    });

    // Auto-generate SKU
    $('#name, #category').on('blur', function () {
        generateSKU();
    });

    // Calculate profit margin
    $('#cost_price, #selling_price').on('change', function () {
        calculateProfitMargin();
    });

    // Helper Functions
    function adjustStock(formData) {
        const itemId = $('#adjustmentItemId').val();

        $.ajax({
            url: `/inventory/${itemId}/update-stock`,
            type: 'POST',
            data: formData,
            headers: getCSRFHeader(),
            success: function (response) {
                $('#stockModal').modal('hide');
                showAlert(response.message, 'success');
                inventoryTable.ajax.reload(null, false);
            },
            error: handleAjaxError
        });
    }

    function deleteItem(itemId) {
        $.ajax({
            type: 'DELETE',
            url: `/inventory/${itemId}`,
            headers: getCSRFHeader(),
            success: function (response) {
                showAlert(response.message, 'success');
                inventoryTable.ajax.reload(null, false);
            },
            error: handleAjaxError
        });
    }

    function bulkDelete(ids) {
        $.ajax({
            type: 'POST',
            url: '/inventory/bulk-delete',
            data: { ids: ids, _method: 'DELETE' },
            headers: getCSRFHeader(),
            success: function (response) {
                showAlert(response.message, 'success');
                inventoryTable.ajax.reload(null, false);
            },
            error: handleAjaxError
        });
    }

    function generateSKU() {
        if (!$('#sku').val()) {
            const name = $('#name').val();
            const category = $('#category').val();

            if (name && category) {
                const baseSKU = category.substring(0, 3).toUpperCase() +
                    name.replace(/\s+/g, '').substring(0, 5).toUpperCase();
                const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                $('#sku').val(baseSKU + random);
            }
        }
    }

    function calculateProfitMargin() {
        const costPrice = parseFloat($('#cost_price').val()) || 0;
        const sellingPrice = parseFloat($('#selling_price').val()) || 0;

        if (costPrice > 0 && sellingPrice > 0) {
            const margin = ((sellingPrice - costPrice) / costPrice) * 100;
            showProfitMargin(margin);
        }
    }

    function showProfitMargin(margin) {
        let marginElement = $('#profit_margin_display');

        if (marginElement.length === 0) {
            $('#selling_price').after('<div class="form-text" id="profit_margin_display"></div>');
            marginElement = $('#profit_margin_display');
        }

        const marginClass = margin >= 0 ? 'text-success' : 'text-danger';
        const icon = margin >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';

        marginElement.html(
            `<i class="fas ${icon} ${marginClass} me-1"></i>
             Profit Margin: <span class="${marginClass} fw-bold">${margin.toFixed(2)}%</span>`
        );
    }

    function getSelectedIds() {
        return $('input[name="item_ids[]"]:checked').map(function () {
            return $(this).val();
        }).get();
    }

    function confirmDelete(callback, message) {
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }

    function showAlert(message, type) {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Success!' : 'Error!',
            text: message,
            timer: type === 'success' ? 1500 : 3000,
            showConfirmButton: false
        });
    }

    function getCSRFHeader() {
        return {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        };
    }

    function handleAjaxError(xhr) {
        const errorMessage = xhr.responseJSON?.message || 'An error occurred. Please try again.';
        showAlert(errorMessage, 'error');
    }

    function updateSummaryCards() {
        // You can implement AJAX call to update summary cards if needed
        console.log('Summary cards updated');
    }
});
