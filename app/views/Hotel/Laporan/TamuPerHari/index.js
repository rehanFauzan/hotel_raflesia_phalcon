window.defaultUrl = `${baseUrl}panel/hotel/laporan/tamu-harian/`;
var table;

$(document).ready(function() {
    // Initialize Select2 for filter dropdowns
    $('#search_status').select2({
        dropdownParent: $('#filterModal'),
        width: '100%'
    });

    $('#search_jenis_kelamin').select2({
        dropdownParent: $('#filterModal'),
        width: '100%'
    });

    // Toggle input functionality for filter
    $('.toggle-input').change(function() {
        let inputGroup = $(this).closest('.input-group');
        let input = inputGroup.find('input[type="date"], select');
        
        if ($(this).is(':checked')) {
            input.prop('disabled', false);
        } else {
            input.prop('disabled', true).val('');
        }
    });

    // Initialize all filter inputs as disabled
    $('#form-filter input[type="date"], #form-filter select').prop('disabled', true);

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

    $('#btn-perbarui').click(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btn-print').click(function(e) {
        e.preventDefault();
        
        // Get current filter values
        var formData = $('#form-filter').serialize();
        var pdfUrl = defaultUrl + 'pdf';
        
        if (formData) {
            pdfUrl += '?' + formData;
        }
        
        // Open PDF in new window
        window.open(pdfUrl, '_blank');
        notyf.success('PDF sedang diunduh...');
    });

    renderViewDatatableAction();
});

function renderViewDatatableAction() {
    table = $("#datatables-tamu-harian").DataTable({
        ajax: {
            url: defaultUrl + "datatable",
            type: "post",
            data: function(d) {
                var formData = $("#form-filter").serializeArray();
                $.each(formData, function(key, val) {
                    d[val.name] = val.value;
                });
            },
        },
        serverSide: true,
        processing: true,
        responsive: true,
        aaSorting: [],
        columnDefs: [{
            searchable: false,
            targets: [0],
        }],
        columns: [
            {
                data: "id",
                orderable: false,
                render: function(data, index, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1 + ".";
                },
            },
            {
                data: "nama_lengkap",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data;
                }
            },
            {
                data: "jenis_identitas",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data.toUpperCase();
                }
            },
            {
                data: "no_identitas",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data;
                }
            },
            {
                data: "jenis_kelamin",
                render: function(data, type, row, meta) {
                    if (_.isEmpty(data)) return `-`;
                    return data === 'L' ? 'Laki-laki' : 'Perempuan';
                }
            },
            {
                data: "no_telepon",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data;
                }
            },
            {
                data: "nomor_kamar",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data;
                }
            },
            {
                data: "tanggal_checkin",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : new Date(data).toLocaleDateString('id-ID');
                }
            },
            {
                data: "status",
                render: function(data, type, row, meta) {
                    if (_.isEmpty(data)) return `-`;
                    
                    let badgeClass = '';
                    let statusText = '';
                    
                    switch(data) {
                        case 'checkin':
                            badgeClass = 'bg-success';
                            statusText = 'Check-in';
                            break;
                        case 'checkout':
                            badgeClass = 'bg-secondary';
                            statusText = 'Check-out';
                            break;
                        default:
                            badgeClass = 'bg-light';
                            statusText = data.charAt(0).toUpperCase() + data.slice(1);
                    }
                    return `<span class="badge ${badgeClass}">${statusText}</span>`;
                }
            }
        ],
        createdRow: function(row, data, index) {
            $("thead").css({
                "vertical-align": "middle",
                "text-align": "center",
            });
            $("td", row).css({
                "vertical-align": "middle"
            });
            $("td", row).first().css({
                width: "3%",
                "text-align": "center",
            });
            $('td', row).eq(1).css({ 'text-align': 'left' });
            $('td', row).eq(2).css({ 'text-align': 'center' });
            $('td', row).eq(3).css({ 'text-align': 'center' });
            $('td', row).eq(4).css({ 'text-align': 'center' });
            $('td', row).eq(5).css({ 'text-align': 'center' });
            $('td', row).eq(6).css({ 'text-align': 'center' });
            $('td', row).eq(7).css({ 'text-align': 'center' });
            $('td', row).eq(8).css({ 'text-align': 'center' });
        },
    });
}