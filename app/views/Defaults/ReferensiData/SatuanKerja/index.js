window.defaultUrl = `${baseUrl}panel/refdata/satker/`;
var title_modal = `Tambah Satuan Kerja`;
var table;
let rowData;

var manageModal = $('#manageModal');
var formManage = $('#manageForm');
// Get fresh CSRF token on each request

var csrfKey = "<?php echo $this->security->getTokenKey() ?>";
var csrfValue = "<?php echo $this->security->getToken() ?>";

// Initial refresh

$(document).ready(function () {

    // Update CSRF token for security

    // Make sure all AJAX requests use the same CSRF token
    formManage.validate({
        rules: {
            kode_satker: {
                required: true,
            },
            nama_satker: {
                required: true
            }
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

    $('#btn-perbarui').click(function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btn-add').click(function (e) {
        e.preventDefault();

        // Reset form fields
        formManage.trigger("reset");

        // Clear validation errors
        formManage.find('.is-invalid').removeClass('is-invalid');
        formManage.find('.text-danger').remove();

        // Reset select2 dropdowns

        // Clear any hidden fields that might contain previous data
        $('#id_edit').val('');
        manageModal.find("#title_modal").text("Tambah Data Satuan Kerja")

        // formManage.find('#kode_satker').attr('readonly', false);

        formManage.find('#input-action').val('store');

        $("#manageModal").modal("show");
    });


    $("#btn-delete").click(function (e) {
        if (!rowData || !rowData.kode_satker) {
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
                    action: function () {
                        $.ajax({
                            type: "POST",
                            data: {
                                id_delete: rowData.kode_satker
                            },
                            url: defaultUrl + "deleteData",
                            beforeSend: function (xhr, settings) {
                                $(".loading").removeClass("hide");
                            },
                            success: function (response) {
                                $(".loading").addClass("hide");
                                if (response.error == 0) {
                                    notyf.success("Data Berhasil dihapus");
                                    table.ajax.reload();
                                } else {
                                    notyf.error("Data Gagal dihapus");
                                }
                            },
                            error: function (e) {
                                notyf.error("Error, Terjadi Kesalahan");
                                $(".loading").addClass("hide");
                            },
                        });
                    },
                },
            },
        });
    });

    $('#btn-edit').click(function (e) {
        e.preventDefault();

        if (!rowData || !rowData.kode_satker) {
            notyf.open({
                type: "warning",
                message: "Pilih data yang ingin dirubah terlebih dahulu!",
            });
            return false;
        }

        formManage.trigger("reset");

        // Clear validation errors
        formManage.find('.is-invalid').removeClass('is-invalid');
        formManage.find('.text-danger').remove();

        // Reset select2 dropdowns

        // Clear any hidden fields that might contain previous data
        $('#id_edit').val('');

        manageModal.find("#title_modal").text("Ubah Data Satuan Kerja")

        formManage.find('#input-action').val('update');
        formManage.find('#id_edit').val(rowData.kode_satker);
        formManage.find('#kode_satker').val(rowData.kode_satker);
        // formManage.find('#kode_satker').attr('readonly', true);

        formManage.find('#nama_satker').val(rowData.nama_satker);

        $("#manageModal").modal("show");
    });

    $('#btn-submit').click(function (e) {
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
                        action: function () {
                            ajaxSubmit();
                        }
                    }
                }
            });
        }
    });

    // $("#btn-add-barang").click(function (e) {

    //     $("#form-add").trigger("reset");
    //     $("#form-action").val("store"); // Set mode ke Store
    //     $("#barang-id").val(""); // Kosongkan ID untuk tambah

    // });
});


function refreshData() {
    try {
        table.ajax.reload();
        console.log("Datatable successfully refreshed");
    } catch (err) {
        alert("Error refreshing table");
        console.error("Error refreshing table");
    }
}



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
        beforeSend: function (xhr, settings) {
            $(".loading").removeClass("hide");
        },
        success: function (response) {

            $(".loading").addClass("hide");

            if (response.error == 0) {
                notyf.success(response.message);
                // location.href = defaultUrl;
            } else {
                notyf.error(response.message);
            }

        },
        error: function (e) {
            notyf.error("Error: " + e);
            $(".loading").addClass("hide");
        },
    });
}

function renderViewDatatableAction() {
    table = $("#datatables-satker").DataTable({
        ajax: {
            url: defaultUrl + "datatable",
            type: "post",
            data: function (d) {
                var formData = $("#form-filter").serializeArray();
                $.each(formData, function (key, val) {
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
        }, ],
        columns: [{
                data: "id",
                orderable: false,
                render: function (data, index, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1 + ".";
                },
            },
            {
                data: "kode_satker",
                render: function (data, type, row, meta) {
                    if (_.isEmpty(data)) {
                        return `-`;
                    } else {
                        return data
                    }
                }
            },
            {
                data: "nama_satker",
                render: function (data, type, row, meta) {
                    if (_.isEmpty(data)) {
                        return `-`;
                    } else {
                        return data
                    }
                }
            }
        ],
        createdRow: function (row, data, index) {
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
            //Default
            // $('td', row).eq(1).css({ 'text-align': 'left', 'font-weight': 'normal' , width: "7%"});
            // $('td', row).eq(2).css({ 'text-align': 'left', 'font-weight': 'normal', width: "30%"});
            $('td', row).eq(1).css({
                'text-align': 'center'
            });
            $('td', row).eq(2).css({
                'text-align': 'left'
            });

            // $("td", row).eq(7).css({
            // 	"text-align": "right"
            // });
        },
    }).on("click", "tr", function () {

        // console.log("Event ini diklik");
        // Remove selected class from all rows first
        $(this).siblings().removeClass("selected");

        // Toggle selected class on the clicked row
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
            // Disable buttons when no row is selected
            $("#btn-edit").addClass("disabled");
            $("#btn-delete").addClass("disabled");
        } else {
            $(this).addClass("selected");
            // Enable buttons when a row is selected
            $("#btn-edit").removeClass("disabled");
            $("#btn-delete").removeClass("disabled");

            // Store the selected row data
            let selectedData = table.row(this).data();
            if (selectedData) {
                rowData = selectedData;
            }
        }
    });
}