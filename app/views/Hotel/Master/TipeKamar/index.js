window.defaultUrl = `${baseUrl}panel/hotel/master/tipe-kamar/`;
var title_modal = `Tambah Data Tipe Kamar`;
var table;
let rowData;

var manageModal = $('#manageModal');
var formManage = $('#manageForm');

$(document).ready(function() {
    // Form validation
    formManage.validate({
        rules: {
            nama: { required: true },
            harga_per_malam: { required: true, min: 1 },
            kapasitas: { required: true, min: 1 }
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

    // Set Default To Disable
    $('#btn-edit').addClass('disabled');
    $('#btn-delete').addClass('disabled');

    renderViewDatatableAction();

    // Initialize Select2 for form
    $('#status').select2({
        dropdownParent: $('#manageModal'),
        width: '100%'
    });

    // Initialize Select2 for filter
    $('#search_kapasitas').select2({
        dropdownParent: $('#filterModal'),
        width: '100%'
    });

    // Initialize Select2 for nama tipe kamar filter
    $('#search_nama').select2({
        dropdownParent: $('#filterModal'),
        width: '100%',
        ajax: {
            url: defaultUrl + 'getTipeKamarOptions',
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

    $('#btn-perbarui').click(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btn-filter').click(function(e) {
        e.preventDefault();
        $('#filterModal').modal('show');
    });

    $('#btn-search').click(function(e) {
        e.preventDefault();
        table.ajax.reload();
        $('#filterModal').modal('hide');
        notyf.success('Filter diterapkan');
    });

    $('#btn-add').click(function(e) {
        e.preventDefault();
        
        formManage.trigger("reset");
        formManage.find('.is-invalid').removeClass('is-invalid');
        formManage.find('.text-danger').remove();
        
        $('#id_edit').val('');
        manageModal.find("#title_modal").text("Tambah Data Tipe Kamar");
        formManage.find('#input-action').val('store');
        
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
        
        manageModal.find("#title_modal").text("Ubah Data Tipe Kamar");
        formManage.find('#input-action').val('update');
        formManage.find('#id_edit').val(rowData.id);
        formManage.find('#nama').val(rowData.nama);
        formManage.find('#deskripsi').val(rowData.deskripsi);
        formManage.find('#harga_per_malam').val(rowData.harga_per_malam);
        formManage.find('#kapasitas').val(rowData.kapasitas);
        formManage.find('#fasilitas').val(rowData.fasilitas);
        
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
    table = $("#datatables-tipe-kamar").DataTable({
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
                data: "nama",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data;
                }
            },
            {
                data: "deskripsi",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data;
                }
            },
            {
                data: "harga_per_malam",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: "kapasitas",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data + ' orang';
                }
            },
            {
                data: "fasilitas",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `-` : data;
                }
            },
            {
                data: "jumlah_ruangan_tersedia",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `0 kamar` : data + ' kamar';
                }
            },
            {
                data: "jumlah_ruangan_terpakai",
                render: function(data, type, row, meta) {
                    return _.isEmpty(data) ? `0 kamar` : data + ' kamar';
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
            $('td', row).eq(1).css({ 'text-align': 'left' });
            $('td', row).eq(2).css({ 'text-align': 'left' });
            $('td', row).eq(3).css({ 'text-align': 'right' });
            $('td', row).eq(4).css({ 'text-align': 'center' });
            $('td', row).eq(5).css({ 'text-align': 'left' });
            $('td', row).eq(6).css({ 'text-align': 'center' });
            $('td', row).eq(7).css({ 'text-align': 'center' });
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