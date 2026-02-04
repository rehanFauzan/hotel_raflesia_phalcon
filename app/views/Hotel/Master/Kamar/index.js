$(document).ready(function() {
    let table;
    let isEdit = false;

    // Initialize DataTable
    table = $('#datatables-kamar').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/panel/hotel/master/kamar/datatable',
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
            { data: 'nomor_kamar', name: 'nomor_kamar' },
            { data: 'tipe_kamar_nama', name: 'tipe_kamar_nama' },
            { data: 'lantai', name: 'lantai' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    let badgeClass = '';
                    let statusText = '';
                    
                    switch(data) {
                        case 'tersedia':
                            badgeClass = 'bg-success';
                            statusText = 'Tersedia';
                            break;
                        case 'terisi':
                            badgeClass = 'bg-danger';
                            statusText = 'Terisi';
                            break;
                        case 'maintenance':
                            badgeClass = 'bg-warning';
                            statusText = 'Maintenance';
                            break;
                        default:
                            badgeClass = 'bg-secondary';
                            statusText = data;
                    }
                    
                    return `<span class="badge ${badgeClass}">${statusText}</span>`;
                }
            },
            { 
                data: 'keterangan', 
                name: 'keterangan',
                render: function(data) {
                    return data ? data : '-';
                }
            }
        ],
        order: [[1, 'asc']]
    });

    // Load tipe kamar options
    function loadTipeKamar() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/panel/hotel/master/kamar/getTipeKamar',
                type: 'GET',
                success: function(response) {
                    if (response.error === 0) {
                        let options = '<option value="">Pilih Tipe Kamar</option>';
                        response.data.forEach(function(item) {
                            options += `<option value="${item.id}">${item.nama}</option>`;
                        });
                        $('#tipe_ruangan_id').html(options);
                        resolve(response);
                    } else {
                        reject(response);
                    }
                },
                error: function(xhr, status, error) {
                    reject(error);
                }
            });
        });
    }

    // Initialize Select2 for filters
    $('#search_status').select2({
        dropdownParent: $('#filterModal'),
        width: '100%'
    });

    // Initialize Select2 for form
    $('#tipe_ruangan_id, #status').select2({
        dropdownParent: $('#kamarModal'),
        width: '100%'
    });

    // Toggle input functionality for filter
    $('.toggle-input').change(function() {
        let inputGroup = $(this).closest('.input-group');
        let input = inputGroup.find('input[type="text"], select');
        
        if ($(this).is(':checked')) {
            input.prop('disabled', false);
        } else {
            input.prop('disabled', true).val('').trigger('change');
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

    // Add button
    $('#btn-tambah').click(function() {
        isEdit = false;
        $('#modal-title').text('Tambah Data Kamar');
        $('#form-kamar')[0].reset();
        $('#id_edit').val('');
        $('#tipe_ruangan_id, #status').val('').trigger('change');
        loadTipeKamar();
        $('#kamarModal').modal('show');
    });

    // Edit button
    $('#btn-edit').click(function() {
        const selectedRows = table.rows('.selected').data();
        if (selectedRows.length === 0) {
            notyf.error('Pilih data yang akan diedit');
            return;
        }
        if (selectedRows.length > 1) {
            notyf.error('Pilih hanya satu data untuk diedit');
            return;
        }
        
        const rowData = selectedRows[0];
        isEdit = true;
        $('#modal-title').text('Edit Data Kamar');
        $('#id_edit').val(rowData.id);
        
        // Load tipe kamar first, then set values
        loadTipeKamar().then(() => {
            $('#nomor_kamar').val(rowData.nomor_kamar);
            $('#tipe_ruangan_id').val(rowData.tipe_ruangan_id).trigger('change');
            $('#lantai').val(rowData.lantai);
            $('#status').val(rowData.status).trigger('change');
            $('#keterangan').val(rowData.keterangan);
        });
        
        $('#kamarModal').modal('show');
    });

    // Delete button
    $('#btn-hapus').click(function() {
        const selectedRows = table.rows('.selected').data();
        if (selectedRows.length === 0) {
            notyf.error('Pilih data yang akan dihapus');
            return;
        }
        if (selectedRows.length > 1) {
            notyf.error('Pilih hanya satu data untuk dihapus');
            return;
        }
        
        const rowData = selectedRows[0];
        $('#id_delete').val(rowData.id);
        $('#deleteModal').modal('show');
    });

    // Row selection - only single selection
    $('#datatables-kamar tbody').on('click', 'tr', function() {
        // Remove selection from all rows
        $('#datatables-kamar tbody tr').removeClass('selected');
        // Add selection to clicked row
        $(this).addClass('selected');
    });

    // Save button
    $('#btn-save').click(function() {
        if (!$('#form-kamar')[0].checkValidity()) {
            $('#form-kamar')[0].reportValidity();
            return;
        }

        const formData = {
            nomor_kamar: $('#nomor_kamar').val(),
            tipe_ruangan_id: $('#tipe_ruangan_id').val(),
            lantai: $('#lantai').val(),
            status: $('#status').val(),
            keterangan: $('#keterangan').val()
        };

        if (isEdit) {
            formData.id_edit = $('#id_edit').val();
        }

        const url = isEdit ? '/panel/hotel/master/kamar/updateData' : '/panel/hotel/master/kamar/saveData';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#btn-save').prop('disabled', true).text('Menyimpan...');
            },
            success: function(response) {
                if (response.error === 0) {
                    notyf.success(response.message);
                    $('#kamarModal').modal('hide');
                    table.ajax.reload();
                } else {
                    notyf.error(response.message);
                }
            },
            error: function() {
                notyf.error('Terjadi kesalahan sistem');
            },
            complete: function() {
                $('#btn-save').prop('disabled', false).text('Simpan');
            }
        });
    });

    // Confirm delete
    $('#btn-delete').click(function() {
        const id = $('#id_delete').val();
        
        $.ajax({
            url: '/panel/hotel/master/kamar/deleteData',
            type: 'POST',
            data: { id_delete: id },
            beforeSend: function() {
                $('#btn-delete').prop('disabled', true).text('Menghapus...');
            },
            success: function(response) {
                if (response.error === 0) {
                    notyf.success(response.message);
                    $('#deleteModal').modal('hide');
                    table.ajax.reload();
                } else {
                    notyf.error(response.message);
                }
            },
            error: function() {
                notyf.error('Terjadi kesalahan sistem');
            },
            complete: function() {
                $('#btn-delete').prop('disabled', false).text('Hapus');
            }
        });
    });

    // Load tipe kamar on page load
    loadTipeKamar();
});