window.defaultUrl = `${baseUrl}panel/hotel/referensi-data/pembayaran/`;
var title_modal = `Tambah Data Pembayaran`;
var table;
let rowData;

var manageModal = $('#manageModal');
var formManage = $('#manageForm');

$(document).ready(function() {
    // Form validation
    formManage.validate({
        rules: {
            pemesanan_id: { required: true },
            metode_pembayaran: { required: true },
            jumlah_bayar: { required: true, min: 1 },
            tanggal_bayar: { required: true }
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
    $('#search_pemesanan').select2({
        dropdownParent: $('#filterModal'),
        width: '100%',
        ajax: {
            url: defaultUrl + 'getPemesananOptions',
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
        placeholder: 'Pilih Pemesanan',
        allowClear: true
    });

    $('#search_metode').select2({
        dropdownParent: $('#filterModal'),
        width: '100%'
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
    $('#btn-print').addClass('disabled');

    renderViewDatatableAction();

    // Auto-update status based on payment amount
    let totalHargaPemesanan = 0;
    
    function updatePaymentStatus() {
        let jumlahBayar = parseFloat($('#jumlah_bayar').val()) || 0;
        
        if (totalHargaPemesanan > 0 && jumlahBayar > 0) {
            let status = '';
            let statusInfo = '';
            
            if (jumlahBayar < totalHargaPemesanan) {
                status = 'pending';
                statusInfo = `Kurang bayar: Rp ${new Intl.NumberFormat('id-ID').format(totalHargaPemesanan - jumlahBayar)}`;
            } else if (jumlahBayar >= totalHargaPemesanan) {
                status = 'lunas';
                if (jumlahBayar > totalHargaPemesanan) {
                    statusInfo = `Kelebihan bayar: Rp ${new Intl.NumberFormat('id-ID').format(jumlahBayar - totalHargaPemesanan)}`;
                } else {
                    statusInfo = 'Pembayaran sesuai';
                }
            }
            
            $('#status').val(status).trigger('change');
            
            // Remove existing status info
            $('#jumlah_bayar').next('.payment-status-info').remove();
            
            // Add status info
            $('#jumlah_bayar').after(`<small class="text-muted payment-status-info">${statusInfo}</small>`);
        } else {
            $('#status').val('').trigger('change');
            $('#jumlah_bayar').next('.payment-status-info').remove();
        }
    }
    
    // Event listeners for auto-status update (backup - will be overridden in loadDropdownOptions)
    $(document).on('change', '#pemesanan_id', function() {
        let pemesananId = $(this).val();
        console.log('Pemesanan changed:', pemesananId); // Debug
        
        // Always remove existing info first
        $('.booking-info, .payment-status-info').remove();
        
       
    });
    
    $('#jumlah_bayar').on('input change', updatePaymentStatus);

    // Load dropdown options
    function loadDropdownOptions(isEdit = false) {
        // Initialize Select2 for pemesanan dropdown
        $('#pemesanan_id').select2({
            dropdownParent: $('#manageModal'),
            width: '100%',
            ajax: {
                url: defaultUrl + (isEdit ? 'getAllPemesanan' : 'getPemesanan'),
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
                                text: item.kode_booking + ' - ' + item.tamu_nama + ' (Rp ' + new Intl.NumberFormat('id-ID').format(item.total_harga) + ')'
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Pilih Pemesanan',
            allowClear: true
        });

        // Re-attach event listener after Select2 initialization
        $('#pemesanan_id').off('change.totalHarga').on('change.totalHarga', function() {
            let pemesananId = $(this).val();
            
            // Always remove existing info first
            $('.booking-info, .payment-status-info').remove();
            
            if (pemesananId) {
                // Get booking details
                $.ajax({
                    url: defaultUrl + 'getPemesananDetail',
                    type: 'GET',
                    data: { pemesanan_id: pemesananId },
                    success: function(response) {
                        if (response.error === 0 && response.data) {
                            totalHargaPemesanan = parseFloat(response.data.total_harga) || 0;
                            
                            // Always show total amount info when pemesanan is selected
                            if (totalHargaPemesanan > 0) {
                                $('#pemesanan_id').parent().after(`<div class="booking-info mt-2"><small class="text-info"><strong>Total yang harus dibayar: Rp ${new Intl.NumberFormat('id-ID').format(totalHargaPemesanan)}</strong></small></div>`);
                            }
                            
                            // Update status if payment amount is already filled
                            updatePaymentStatus();
                        }
                    },
                    error: function() {
                        console.log('Error getting booking details');
                        totalHargaPemesanan = 0;
                    }
                });
            } else {
                totalHargaPemesanan = 0;
                $('#status').val('').trigger('change');
            }
        });

        // Initialize Select2 for metode pembayaran
        $('#metode_pembayaran').select2({
            dropdownParent: $('#manageModal'),
            width: '100%',
            placeholder: 'Pilih Metode Pembayaran'
        });

        // Initialize Select2 for status
        $('#status').select2({
            dropdownParent: $('#manageModal'),
            width: '100%',
            placeholder: 'Pilih Status'
        });
    }

    $('#btn-perbarui').click(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btn-add').click(function(e) {
        e.preventDefault();
        
        formManage.trigger("reset");
        formManage.find('.is-invalid').removeClass('is-invalid');
        formManage.find('.text-danger').remove();
        $('.booking-info, .payment-status-info').remove();
        
        $('#id_edit').val('');
        manageModal.find("#title_modal").text("Tambah Data Pembayaran");
        formManage.find('#input-action').val('store');
        
        totalHargaPemesanan = 0;
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
        
        manageModal.find("#title_modal").text("Ubah Data Pembayaran");
        formManage.find('#input-action').val('update');
        formManage.find('#id_edit').val(rowData.id);
        
        loadDropdownOptions(true);
        
        // Set data pemesanan dengan option baru untuk Select2
        if (rowData.pemesanan_id && rowData.kode_booking && rowData.tamu_nama) {
            // Create the option immediately without AJAX call
            var pemesananText = rowData.kode_booking + ' - ' + rowData.tamu_nama;
            var pemesananOption = new Option(pemesananText, rowData.pemesanan_id, true, true);
            $('#pemesanan_id').append(pemesananOption).trigger('change');
            
            // Get additional details for total harga calculation
            $.ajax({
                url: defaultUrl + 'getPemesananDetail',
                type: 'GET',
                data: { pemesanan_id: rowData.pemesanan_id },
                success: function(response) {
                    if (response.error === 0 && response.data) {
                        // Update the option text with total harga
                        var updatedText = response.data.kode_booking + ' - ' + response.data.tamu_nama + ' (Rp ' + new Intl.NumberFormat('id-ID').format(response.data.total_harga) + ')';
                        $('#pemesanan_id option[value="' + rowData.pemesanan_id + '"]').text(updatedText);
                        
                        // Set total harga for auto-status calculation
                        totalHargaPemesanan = parseFloat(response.data.total_harga) || 0;
                        
                        // Always show total amount info immediately with better styling
                        $('.booking-info').remove();
                        if (totalHargaPemesanan > 0) {
                            $('#pemesanan_id').parent().after(`<div class="booking-info mt-2"><small class="text-info"><strong>Total yang harus dibayar: Rp ${new Intl.NumberFormat('id-ID').format(totalHargaPemesanan)}</strong></small></div>`);
                        }
                    }
                }
            });
        }
        
        setTimeout(function() {
            $('#metode_pembayaran').val(rowData.metode_pembayaran).trigger('change');
            $('#jumlah_bayar').val(rowData.jumlah_bayar);
            
            // Format datetime for input
            let tanggalBayar = new Date(rowData.tanggal_bayar);
            let formattedDate = tanggalBayar.getFullYear() + '-' + 
                               String(tanggalBayar.getMonth() + 1).padStart(2, '0') + '-' + 
                               String(tanggalBayar.getDate()).padStart(2, '0') + 'T' + 
                               String(tanggalBayar.getHours()).padStart(2, '0') + ':' + 
                               String(tanggalBayar.getMinutes()).padStart(2, '0');
            $('#tanggal_bayar').val(formattedDate);
            
            $('#status').val(rowData.status).trigger('change');
            $('#keterangan').val(rowData.keterangan);
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

    $('#btn-print').click(function(e) {
        e.preventDefault();
        
        if (!rowData || !rowData.id) {
            notyf.open({
                type: "warning",
                message: "Pilih data pembayaran yang ingin dicetak terlebih dahulu!",
            });
            return false;
        }
        
        // Open PDF in new window
        window.open(defaultUrl + 'cetakPdf/' + rowData.id, '_blank');
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
    table = $("#datatables-pembayaran").DataTable({
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
                data: "metode_pembayaran",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data.replace('_', ' ').toUpperCase();
                }
            },
            {
                data: "jumlah_bayar",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: "tanggal_bayar",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : new Date(data).toLocaleString('id-ID');
                }
            },
            {
                data: "status",
                render: function(data, type, row, meta) {
                    if (!data || data === null || data === '') return `-`;
                    
                    let badgeClass = '';
                    let statusText = '';
                    
                    switch(data.toLowerCase()) {
                        case 'pending':
                            badgeClass = 'bg-warning';
                            statusText = 'Pending';
                            break;
                        case 'lunas':
                            badgeClass = 'bg-success';
                            statusText = 'Lunas';
                            break;
                        case 'gagal':
                            badgeClass = 'bg-danger';
                            statusText = 'Gagal';
                            break;
                        case 'refund':
                            badgeClass = 'bg-info';
                            statusText = 'Refund';
                            break;
                        default:
                            badgeClass = 'bg-secondary';
                            statusText = data.charAt(0).toUpperCase() + data.slice(1);
                    }
                    return `<span class="badge ${badgeClass}">${statusText}</span>`;
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
            $('td', row).eq(4).css({ 'text-align': 'right' });
            $('td', row).eq(5).css({ 'text-align': 'center' });
            $('td', row).eq(6).css({ 'text-align': 'center' });
        },
    }).on("click", "tr", function() {
        $(this).siblings().removeClass("selected");
        
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
            $("#btn-edit").addClass("disabled");
            $("#btn-delete").addClass("disabled");
            $("#btn-print").addClass("disabled");
        } else {
            $(this).addClass("selected");
            $("#btn-edit").removeClass("disabled");
            $("#btn-delete").removeClass("disabled");
            $("#btn-print").removeClass("disabled");
            
            let selectedData = table.row(this).data();
            if (selectedData) {
                rowData = selectedData;
            }
        }
    });
}