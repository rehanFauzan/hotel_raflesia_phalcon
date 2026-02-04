$(document).ready(function() {
    let table;
    let selectedRowId = null;

    // Initialize DataTable
    table = $('#datatables-harga-kamar').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/panel/hotel/master/harga-kamar/datatable',
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
            { data: 'nama', name: 'nama' },
            { 
                data: 'harga_per_malam', 
                name: 'harga_per_malam',
                render: function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            }
        ],
        order: [[1, 'asc']],
        select: {
            style: 'single'
        }
    });

    // Initialize Select2 for filter
    $('#search_nama').select2({
        dropdownParent: $('#filterModal'),
        width: '100%',
        ajax: {
            url: '/panel/hotel/master/tipe-kamar/getTipeKamarOptions',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.nama
                        };
                    })
                };
            },
            cache: true
        },
        placeholder: 'Pilih Tipe Kamar',
        allowClear: true
    });

    // Toggle input functionality for filter
    $('.toggle-input').change(function() {
        let inputGroup = $(this).closest('.input-group');
        let input = inputGroup.find('input[type="text"], input[type="number"], select');
        
        if ($(this).is(':checked')) {
            input.prop('disabled', false);
        } else {
            input.prop('disabled', true).val('');
        }
    });

    // Initialize all filter inputs as disabled
    $('#form-filter input[type="text"], #form-filter input[type="number"], #form-filter select').prop('disabled', true);

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

    // Row selection
    $('#datatables-harga-kamar tbody').on('click', 'tr', function () {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            selectedRowId = null;
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            selectedRowId = table.row(this).data().id;
        }
    });

    // Refresh button
    $('#btn-perbarui').click(function() {
        table.ajax.reload();
    });

    // Edit button
    $('#btn-edit').click(function() {
        if (!selectedRowId) {
            alert('Pilih data yang akan diedit');
            return;
        }
        
        let rowData = table.row('.selected').data();
        
        $('#id_edit').val(rowData.id);
        $('#id_display').val(rowData.id);
        $('#nama_display').val(rowData.nama);
        $('#harga_per_malam').val(rowData.harga_per_malam);
        
        $('#manageModal').modal('show');
    });

    // Form submit
    $('#btn-submit').click(function(e) {
        e.preventDefault();
        
        let formData = $('#manageForm').serialize();
        
        $.ajax({
            url: '/panel/hotel/master/harga-kamar/updateData',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.error === 0) {
                    alert('Harga berhasil diupdate');
                    $('#manageModal').modal('hide');
                    table.ajax.reload();
                    selectedRowId = null;
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan sistem');
            }
        });
    });
});