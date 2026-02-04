window.defaultUrl = `${baseUrl}panel/hotel/referensi-data/pemesanan/`;
var title_modal = `Tambah Data Pemesanan`;
var table;
let rowData;
let kamarData = [];

var manageModal = $('#manageModal');
var formManage = $('#manageForm');

$(document).ready(function() {
    // Form validation
    formManage.validate({
        rules: {
            tamu_id: { required: true },
            ruangan_id: { required: true },
            tanggal_checkin: { required: true },
            tanggal_checkout: { required: true },
            status: { required: true }
        },
        errorClass: "text-danger",
        errorElement: "div",
        highlight: function (element, errorClass) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element, errorClass) {
            $(element).removeClass("is-invalid");
        }
    });

    // Initialize Select2 for filter dropdowns
    $('#search_tamu').select2({
        dropdownParent: $('#filterModal'),
        width: '100%',
        ajax: {
            url: defaultUrl + 'getTamuOptions',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        placeholder: 'Pilih Tamu',
        allowClear: true
    });

    $('#search_status').select2({
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

    // Set Default To Disable
    $('#btn-edit').addClass('disabled');
    $('#btn-delete').addClass('disabled');

    renderViewDatatableAction();

    // Calculate total price based on room and dates
    function calculateTotalPrice() {
        let ruanganId = $('#ruangan_id').val();
        let checkinDate = $('#tanggal_checkin').val();
        let checkoutDate = $('#tanggal_checkout').val();
        
        if (ruanganId && checkinDate && checkoutDate) {
            // Get room price from server
            $.ajax({
                url: defaultUrl + 'getKamarHarga',
                type: 'GET',
                data: { kamar_id: ruanganId },
                success: function(response) {
                    if (response.error === 0 && response.data.harga_per_malam) {
                        let checkin = new Date(checkinDate);
                        let checkout = new Date(checkoutDate);
                        let timeDiff = checkout.getTime() - checkin.getTime();
                        let nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                        
                        if (nights > 0) {
                            let totalPrice = parseFloat(response.data.harga_per_malam) * nights;
                            $('#total_harga').val(totalPrice);
                            
                            // Show calculation info
                            let info = `${nights} malam × Rp ${new Intl.NumberFormat('id-ID').format(response.data.harga_per_malam)} = Rp ${new Intl.NumberFormat('id-ID').format(totalPrice)}`;
                            
                            // Remove existing info if any
                            $('#total_harga').next('.calculation-info').remove();

                            // Add calculation info
                            $('#total_harga').after(`<small class="text-muted calculation-info">${info}</small>`);
                        } else {
                            $('#total_harga').val(0);
                            $('#total_harga').next('.calculation-info').remove();
                            
                            if (nights < 0) {
                                notyf.error('Tanggal checkout harus setelah tanggal checkin');
                            }
                        }
                    }
                },
                error: function() {
                    console.log('Error getting room price');
                }
            });
        } else {
            $('#total_harga').val('');
            $('#total_harga').next('.calculation-info').remove();
        }
    }

    // Load dropdown options
    function loadDropdownOptions(isEdit = false) {
        // Initialize Select2 for tamu dropdown
        $('#tamu_id').select2({
            dropdownParent: $('#manageModal'),
            width: '100%',
            ajax: {
                url: defaultUrl + (isEdit ? 'getAllTamu' : 'getTamu'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.nama_lengkap
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Pilih Tamu',
            allowClear: true
        });

        // Initialize Select2 for kamar dropdown
        $('#ruangan_id').select2({
            dropdownParent: $('#manageModal'),
            width: '100%',
            ajax: {
                url: defaultUrl + 'getKamar',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.nomor_kamar + ' - ' + item.tipe_nama + ' (Rp ' + new Intl.NumberFormat('id-ID').format(item.harga_per_malam) + '/malam)'
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Pilih Kamar',
            allowClear: true
        });

        // Initialize Select2 for status
        $('#status').select2({
            dropdownParent: $('#manageModal'),
            width: '100%',
            placeholder: 'Pilih Status'
        });
    }

    $('#ruangan_id').on('change', function() {
        calculateTotalPrice();
        // Clear previous calculation info when room changes
        $('#total_harga').next('.calculation-info').remove();
    });
    $('#tanggal_checkin').on('change', calculateTotalPrice);
    $('#tanggal_checkout').on('change', calculateTotalPrice);

    $('#btn-perbarui').click(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btn-add').click(function(e) {
        e.preventDefault();
        
        formManage.trigger("reset");
        formManage.find('.is-invalid').removeClass('is-invalid');
        formManage.find('.text-danger').remove();
        
        $('#id_edit').val('');
        manageModal.find("#title_modal").text("Tambah Data Pemesanan");
        formManage.find('#input-action').val('store');
        
        loadDropdownOptions();
        $("#manageModal").modal("show");
    });

    $('#btn-edit').click(function(e) {
        e.preventDefault();
        
        if (!rowData || !rowData.id) {
            notyf.open({
                type: "warning",
                message: "Pilih data yang ingin dirubah terlebih dahulu!",
            });
            return false;
        }
        
        formManage.trigger("reset");
        formManage.find('.is-invalid').removeClass('is-invalid');
        formManage.find('.text-danger').remove();
        
        manageModal.find("#title_modal").text("Ubah Data Pemesanan");
        formManage.find('#input-action').val('update');
        formManage.find('#id_edit').val(rowData.id);
        
        loadDropdownOptions(true);
        
        // Set data tamu dengan option baru untuk Select2
        if (rowData.tamu_id && rowData.tamu_nama) {
            var tamuOption = new Option(rowData.tamu_nama, rowData.tamu_id, true, true);
            $('#tamu_id').append(tamuOption).trigger('change');
        }
        
        // Set data kamar dengan option baru untuk Select2
        if (rowData.ruangan_id && rowData.nomor_kamar) {
            // Get kamar details for proper display
            $.ajax({
                url: defaultUrl + 'getKamarHarga',
                type: 'GET',
                data: { kamar_id: rowData.ruangan_id },
                success: function(response) {
                    if (response.error === 0 && response.data) {
                        var kamarText = response.data.nomor_kamar + ' - ' + response.data.tipe_nama + ' (Rp ' + new Intl.NumberFormat('id-ID').format(response.data.harga_per_malam) + '/malam)';
                        var kamarOption = new Option(kamarText, rowData.ruangan_id, true, true);
                        $('#ruangan_id').append(kamarOption).trigger('change');
                    }
                }
            });
        }
        
        setTimeout(function() {
            $('#tanggal_checkin').val(rowData.tanggal_checkin);
            $('#tanggal_checkout').val(rowData.tanggal_checkout);
            $('#jumlah_tamu').val(rowData.jumlah_tamu);
            $('#total_harga').val(rowData.total_harga);
            $('#status').val(rowData.status).trigger('change');
            $('#catatan_khusus').val(rowData.catatan_khusus);
        }, 300);
        
        $("#manageModal").modal("show");
    });

    $("#btn-delete").click(function(e) {
        if (!rowData || !rowData.id) {
            notyf.open({
                type: "warning",
                message: "Pilih data yang ingin dihapus terlebih dahulu!",
            });
            return false;
        }
        
        $.confirm({
            title: "Konfirmasi",
            theme: "modern",
            content: "Anda yakin ingin menghapus data?",
            buttons: {
                Tidak: {
                    text: "Tidak",
                    btnClass: "btn-warning",
                },
                Ya: {
                    text: "Ya",
                    btnClass: "btn-primary",
                    action: function() {
                        $.ajax({
                            type: "POST",
                            data: { id_delete: rowData.id },
                            url: defaultUrl + "deleteData",
                            beforeSend: function(xhr, settings) {
                                $(".loading").removeClass("hide");
                            },
                            success: function(response) {
                                $(".loading").addClass("hide");
                                if (response.error == 0) {
                                    notyf.success("Data Berhasil dihapus");
                                    table.ajax.reload();
                                } else {
                                    notyf.error("Data Gagal dihapus");
                                }
                            },
                            error: function(e) {
                                notyf.error("Error, Terjadi Kesalahan");
                                $(".loading").addClass("hide");
                            },
                        });
                    },
                },
            },
        });
    });

    $('#btn-submit').click(function(e) {
        e.preventDefault();
        
        let valAction = formManage.find('#input-action').val();
        if (_.isEmpty(valAction)) {
            notyf.error("Terjadi kesalahan silahkan refresh ulang");
            return false;
        }
        
        let urlAction = "";
        if (valAction == "store") {
            urlAction = "menyimpan data";
        } else {
            urlAction = "mengubah data";
        }
        
        if (formManage.valid()) {
            $.confirm({
                title: "Konfirmasi",
                theme: "modern",
                content: "Anda yakin ingin " + urlAction + " yang telah diinput?",
                buttons: {
                    Tidak: {
                        text: "Tidak",
                        btnClass: "btn-warning",
                    },
                    Ya: {
                        text: "Ya",
                        btnClass: "btn-primary",
                        action: function() {
                            ajaxSubmit();
                        }
                    }
                }
            });
        }
    });

});

function ajaxSubmit() {
    let valAction = formManage.find('#input-action').val();
    if (_.isEmpty(valAction)) {
        notyf.error("Tidak diketahui aksi");
        return false;
    }
    
    let urlAction = "";
    if (valAction == "store") {
        urlAction = "saveData";
    } else {
        urlAction = "updateData";
    }
    
    $.ajax({
        type: "POST",
        data: formManage.serialize(),
        url: defaultUrl + urlAction,
        beforeSend: function(xhr, settings) {
            $(".loading").removeClass("hide");
        },
        success: function(response) {
            $(".loading").addClass("hide");
            
            if (response.error == 0) {
                notyf.success(response.message);
                window.location.reload();
            } else {
                notyf.error(response.message);
            }
        },
        error: function(e) {
            notyf.error("Error: " + e);
            $(".loading").addClass("hide");
        },
    });
}

function renderViewDatatableAction() {
    table = $("#datatables-pemesanan").DataTable({
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
        selected: true,
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
                data: "kode_booking",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data;
                }
            },
            {
                data: "tamu_nama",
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
                    return _.isEmpty(data) ? `-` : data;
                }
            },
            {
                data: "tanggal_checkout",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data;
                }
            },
            {
                data: "jumlah_malam",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data + ' malam';
                }
            },
            {
                data: "total_harga",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: "status",
                render: function(data, type, row, meta) {
                    if (_.isEmpty(data)) return `-`;
                    
                    let badgeClass = '';
                    switch(data) {
                        case 'menunggu':
                            badgeClass = 'bg-warning';
                            break;
                        case 'dikonfirmasi':
                            badgeClass = 'bg-info';
                            break;
                        case 'checkin':
                            badgeClass = 'bg-success';
                            break;
                        case 'checkout':
                            badgeClass = 'bg-secondary';
                            break;
                        case 'dibatalkan':
                            badgeClass = 'bg-danger';
                            break;
                        default:
                            badgeClass = 'bg-light';
                    }
                    return `<span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            }
        ],
        createdRow: function(row, data, index) {
            $(row).attr("data-value", encodeURIComponent(JSON.stringify(data)));
            $("thead").css({
                "vertical-align": "middle",
                "text-align": "center",
            });
            $("td", row).css({
                "vertical-align": "middle",
                'cursor': 'pointer'
            });
            $("td", row).first().css({
                width: "3%",
                "text-align": "center",
            });
            $('td', row).eq(1).css({ 'text-align': 'center' });
            $('td', row).eq(2).css({ 'text-align': 'left' });
            $('td', row).eq(3).css({ 'text-align': 'center' });
            $('td', row).eq(4).css({ 'text-align': 'center' });
            $('td', row).eq(5).css({ 'text-align': 'center' });
            $('td', row).eq(6).css({ 'text-align': 'center' });
            $('td', row).eq(7).css({ 'text-align': 'right' });
            $('td', row).eq(8).css({ 'text-align': 'center' });
        },
    }).on("click", "tr", function() {
        $(this).siblings().removeClass("selected");
        
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
            $("#btn-edit").addClass("disabled");
            $("#btn-delete").addClass("disabled");
        } else {
            $(this).addClass("selected");
            $("#btn-edit").removeClass("disabled");
            $("#btn-delete").removeClass("disabled");
            
            let selectedData = table.row(this).data();
            if (selectedData) {
                rowData = selectedData;
            }
        }
    });
}