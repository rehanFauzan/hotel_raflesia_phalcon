$(document).ready(function() {
    let table;

    // Initialize DataTable
    table = $('#datatables-user').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/panel/hotel/master/user/datatable',
            type: 'POST',
            data: function(d) {
                var formData = $("#form-filter").serializeArray();
                $.each(formData, function(key, val) {
                    d[val.name] = val.value;
                });
            }
        },
        columns: [
            { 
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                orderable: false,
                searchable: false
            },
            { data: 'id', name: 'id' },
            { data: 'username', name: 'username' },
            { data: 'nama', name: 'nama' },
            { 
                data: 'state', 
                name: 'state',
                render: function(data) {
                    return data == 1 ? 
                        '<span class="badge bg-success">Aktif</span>' : 
                        '<span class="badge bg-danger">Non Aktif</span>';
                }
            }
        ],
        order: [[1, 'asc']]
    });

    // Initialize Select2 for status filter
    $('#search_status').select2({
        dropdownParent: $('#filterModal'),
        width: '100%'
    });

    // Toggle input functionality for filter
    $('.toggle-input').change(function() {
        let inputGroup = $(this).closest('.input-group');
        let input = inputGroup.find('input[type="text"], select');
        
        if ($(this).is(':checked')) {
            input.prop('disabled', false);
        } else {
            input.prop('disabled', true).val('');
        }
    });

    // Initialize all filter inputs as disabled
    $('#form-filter input[type="text"], #form-filter select').prop('disabled', true);

    // Filter button
    $('#btn-filter').click(function(e) {
        e.preventDefault();
        $('#filterModal').modal('show');
    });

    // Search button
    $('#btn-search').click(function(e) {
        e.preventDefault();
        table.ajax.reload();
        $('#filterModal').modal('hide');
        notyf.success('Filter diterapkan');
    });

    // Refresh button
    $('#btn-perbarui').click(function() {
        table.ajax.reload();
    });
});