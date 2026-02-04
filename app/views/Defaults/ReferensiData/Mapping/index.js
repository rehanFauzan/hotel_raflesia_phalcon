window.defaultUrl = `${baseUrl}panel/refdata/mapping/`;
var title_modal = `Tambah Data Mapping Akun Jurnal`;
var table;
let rowData;

var manageModal = $('#manageModal');
var filterModal = $('#filterModal');
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
            acc_code: {
                required: true,
            },
            jt_id: {
                required: true
            },
            is_debet: {
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

    renderSelect2Action();

    renderViewDatatableAction();

    $(".toggle-input").each(function () {
        let inputElement = $(this)
            .closest(".input-group")
            .find("input.form-control, select");
        inputElement.prop("disabled", !this.checked);

        $(this).change(function () {
            inputElement.prop("disabled", !this.checked);
        });
    });

    $("#btn-filter").click(function (e) {
        filterModal.modal("show");
    });

    $("#btn-search").click(function (e) {
        e.preventDefault();
        $("#filterModal").modal("hide");
        refreshData();
    });

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
        manageModal.find("#title_modal").text("Tambah Data Mapping Akun Jurnal")

        $('#acc_code').val('').trigger('change');
        $('#jt_id').val('').trigger('change');
        $('#is_debet').val('');
        // formManage.find('#kode_satker').attr('readonly', false);

        formManage.find('#input-action').val('store');

        $("#manageModal").modal("show");
    });


    $("#btn-delete").click(function (e) {
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
                    action: function () {
                        $.ajax({
                            type: "POST",
                            data: {
                                id_delete: rowData.id
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

        if (!rowData || !rowData.id) {
            notyf.open({
                type: "warning",
                message: "Pilih data yang ingin dirubah terlebih dahulu!",
            });
            return false;
        }


        // console.log(rowData);
        // return false;

        formManage.trigger("reset");

        // Clear validation errors
        formManage.find('.is-invalid').removeClass('is-invalid');
        formManage.find('.text-danger').remove();

        // Reset select2 dropdowns

        // Clear any hidden fields that might contain previous data
        $('#id_edit').val('');

        manageModal.find("#title_modal").text("Ubah Data Kelompok")

        formManage.find('#input-action').val('update');
        formManage.find('#id_edit').val(rowData.id);

        formManage.find("#acc_code").select2("trigger", "select", {
            data: {
                id: rowData.acc_code,
                acc_code: rowData.acc_code,
                acc_name: rowData.perk_name,
                text: `(${rowData.acc_code}) ${rowData.perk_name}`,
                selected: true
            }
        });
    

        formManage.find("#jt_id").select2("trigger", "select", {
            data: {
                id: rowData.jt_id,
                jt_id: rowData.jt_id,
                nama_jurnal: rowData.reff_jurnal_nama,
                text: `${rowData.reff_jurnal_nama}`,
                selected: true
            }
        });


        formManage.find('#is_debet').val(rowData.is_debet);

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
                window.location.reload();
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
    table = $("#datatables-mapping").DataTable({
        ajax: {
            url: defaultUrl + "datatable",
            type: "post",
            data: function (d) {
                var formData = $("#form-filter").serializeArray();
                $.each(formData, function (key, val) {
                    d[val.name] = val.value;
                });
            },
            // dataSrc: function (json) {
            //     // Jika data dari server dalam bentuk {k: {...}, gol_code: ..., gol_name: ...}
            //     // maka flatten ke bentuk {acc_code: ..., acc_name: ..., gol_code: ..., gol_name: ...}
            //     if (Array.isArray(json.data)) {
            //         return json.data.map(function (item) {
            //             // Cek jika ada property 'k'
            //             if (item.k) {
            //                 return Object.assign({}, item.k, {
            //                     gol_code: item.gol_code,
            //                     gol_name: item.gol_name
            //                 });
            //             }
            //             return item;
            //         });
            //     }
            //     return json.data;
            // }
        },
        serverSide: true,
        processing: true,
        responsive: true,
        selected: true,
        pageLength: 25,
        aaSorting: [],
        columnDefs: [{
            searchable: false,
            targets: [0],
        }, ],
        columns: [{
                data: 'id',
                orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1 + ".";
                },
            },
            {
                data: "acc_code",
                render: function (data, type, row, meta) {
                    if (_.isEmpty(data)) {
                        return `-`;
                    } else {
                        return data
                    }
                }
            },
            {
                data: "perk_name",
                render: function (data, type, row, meta) {
                    if (_.isEmpty(data)) {
                        return `-`;
                    } else {
                        return data
                    }
                }
            },
            {
                data: "reff_jurnal_nama",
                render: function (data, type, row, meta) {
                    if (_.isEmpty(data)) {
                        return `-`;
                    } else {
                        return data
                    }
                }
            },
            {
                data: "is_debet",
                render: function (data, type, row, meta) {
                    if (_.isEmpty(data)) {
                        return `-`;
                    } else {
                        if (data == '1') {
                            return `DEBET`;
                        } else {
                            return `KREDIT`;
                        }
                    }
                }
            },
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
            $('td', row).eq(1).css({
                'text-align': 'center'
            });
            $('td', row).eq(2).css({
                'text-align': 'left'
            });
            $('td', row).eq(3).css({
                'text-align': 'center'
            });
            $('td', row).eq(4).css({
                'text-align': 'center'
            });
        },
    }).on("click", "tr", function () {
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




function renderSelect2Action() {

    $("#acc_code").select2({
        allowClear: true,
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small", // Gunakan '100%' agar responsif
        dropdownParent: $('#elSelect2Perkiraan'),
        placeholder: "Pilih Kode Perkiraan",
        ajax: {
            url: "{{ url('panel/refselect2/getPerkiraanS2') }}",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                };
            },
            processResults: function (response) {
                var data = JSON.parse(response);
                return {
                    results: data.data.map(function (i) {
                        return {
                            id: i.acc_code,
                            text: `(${i.acc_code}) ${i.acc_name}`,
                        };
                    }),
                    pagination: {
                        more: data.has_more,
                    },
                };
            },
        },
    });

    $("#jt_id").select2({
        allowClear: true,
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small", // Gunakan '100%' agar responsif
        dropdownParent: $('#elSelect2ReffJurnal'),
        placeholder: "Pilih Data Jurnal",
        ajax: {
            url: "{{ url('panel/refselect2/getReffJurnalS2') }}",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                };
            },
            processResults: function (response) {
                var data = JSON.parse(response);
                return {
                    results: data.data.map(function (i) {
                        return {
                            id: i.jt_id,
                            text: `${i.nama_jurnal}`,
                        };
                    }),
                    pagination: {
                        more: data.has_more,
                    },
                };
            },
        },
    });

    $("#search_acc_code").select2({
        allowClear: true,
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small", // Gunakan '100%' agar responsif
        dropdownParent: $('#elFilterPerkiraan'),
        placeholder: "Pilih Kode Perkiraan",
        ajax: {
            url: "{{ url('panel/refselect2/getPerkiraanS2') }}",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                };
            },
            processResults: function (response) {
                var data = JSON.parse(response);
                return {
                    results: data.data.map(function (i) {
                        return {
                            id: i.acc_code,
                            text: `(${i.acc_code}) ${i.acc_name}`,
                        };
                    }),
                    pagination: {
                        more: data.has_more,
                    },
                };
            },
        },
    });

    $("#search_jt_id").select2({
        allowClear: true,
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small", // Gunakan '100%' agar responsif
        dropdownParent: $('#elFilterJurnal'),
        placeholder: "Pilih Data Jurnal",
        ajax: {
            url: "{{ url('panel/refselect2/getReffJurnalS2') }}",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                };
            },
            processResults: function (response) {
                var data = JSON.parse(response);
                return {
                    results: data.data.map(function (i) {
                        return {
                            id: i.jt_id,
                            text: `${i.nama_jurnal}`,
                        };
                    }),
                    pagination: {
                        more: data.has_more,
                    },
                };
            },
        },
    });
}