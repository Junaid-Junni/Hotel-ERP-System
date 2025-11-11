$(document).ready(function () {
    $.noConflict();

    // Initialize DataTable
    var TransactionList = $('#TransactionList').DataTable({
        dom: 'CBrfltip',
        serverSide: true,
        processing: true,
        ajax: {
            url: '/transactions',
            type: 'GET',
        },
        columns: [
            { data: 'reference_number' },
            {
                data: 'type',
                render: function (data) {
                    var badgeClass = data == 'Income' ? 'bg-success' : 'bg-danger';
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            { data: 'category' },
            { data: 'description' },
            {
                data: 'amount',
                render: function (data, type, row) {
                    var textClass = row.type == 'Income' ? 'text-success' : 'text-danger';
                    return '<span class="' + textClass + '">$' + parseFloat(data).toFixed(2) + '</span>';
                }
            },
            {
                data: 'transaction_date',
                render: function (data) {
                    return new Date(data).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }
            },
            { data: 'payment_method' },
            {
                data: 'booking.reference_number',
                render: function (data, type, row) {
                    return data ?
                        '<a href="/bookings/' + row.booking_id + '" class="text-primary">' + data + '</a>' :
                        '<span class="text-muted">N/A</span>';
                }
            },
            {
                data: 'action',
                orderable: false,
                searchable: false,
                width: '100px'
            }
        ],
        buttons: [
            {
                extend: 'excel',
                text: '<button class="btn btn-success"><i class="fa fa-table"></i></button>',
                titleAttr: 'Export to Excel',
                filename: 'Transaction_List',
            },
            {
                extend: 'pdf',
                text: '<button class="btn bg-purple"><i class="fa-solid fa-file-pdf"></i></button>',
                titleAttr: 'Export to Pdf',
                filename: 'Transaction_List',
            },
        ]
    });

    // Delete Single Transaction
    $('body').on('click', '.DeleteBtn', function (e) {
        e.preventDefault();
        var ID = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: '/transactions/' + ID,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Transaction has been moved to trash.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        TransactionList.ajax.reload(null, false);
                    },
                    error: function (error) {
                        Swal.fire('Error!', 'Delete failed!', 'error');
                    }
                });
            }
        });
    });
});
