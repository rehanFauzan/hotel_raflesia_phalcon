window.defaultUrl = `${baseUrl}panel/refdata/kelompok/`;
var title_modal = `Tambah Data Kelompok`;
var table;
let rowData;

var manageModal = $('#manageModal');
var filterModal = $('#filterModal');
var formManage = $('#manageForm');

// Get fresh CSRF token on each request
var csrfKey = "<?php echo $this->security->getTokenKey() ?>";
var csrfValue = "<?php echo $this->security->getToken() ?>";



var el_acc_code = new Cleave('#acc_code', {
    delimiters: ['.'],
    blocks: [2, 2],
    uppercase: true
});

// Initial refresh
$(document).ready(function () {

    // Update CSRF token for security

    // Make sure all AJAX requests use the same CSRF token
    formManage.validate({
        rules: {
            acc_code: {
                required: true,
            },
            acc_name: {
                required: true
            },
            acc_parent_gol: {
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
        manageModal.find("#title_modal").text("Tambah Data Kelompok")

        // formManage.find('#kode_satker').attr('readonly', false);

        formManage.find('#input-action').val('store');

        $("#manageModal").modal("show");
    });


    $("#btn-delete").click(function (e) {
        if (!rowData || !rowData.acc_code) {
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
                                id_delete: rowData.acc_code
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

        if (!rowData || !rowData.acc_code) {
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

        manageModal.find("#title_modal").text("Ubah Data Kelompok")

        formManage.find('#input-action').val('update');
        formManage.find('#id_edit').val(rowData.acc_code);

        formManage.find('#acc_code').val(rowData.acc_code);
        formManage.find('#acc_name').val(rowData.acc_name);
        formManage.find("#acc_parent_gol").select2("trigger", "select", {
            data: {
                id: rowData.gol_code,
                nama: rowData.gol_name,
                text: `(${rowData.gol_code}) ${rowData.gol_name}`,
                selected: true
            }
        });

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
    table = $("#datatables-kelompok").DataTable({
        ajax: {
            url: defaultUrl + "datatable",
            type: "post",
            data: function (d) {
                var formData = $("#form-filter").serializeArray();
                $.each(formData, function (key, val) {
                    d[val.name] = val.value;
                });
            },
            dataSrc: function (json) {
                // Jika data dari server dalam bentuk {k: {...}, gol_code: ..., gol_name: ...}
                // maka flatten ke bentuk {acc_code: ..., acc_name: ..., gol_code: ..., gol_name: ...}
                if (Array.isArray(json.data)) {
                    return json.data.map(function (item) {
                        // Cek jika ada property 'k'
                        if (item.k) {
                            return Object.assign({}, item.k, {
                                gol_code: item.gol_code,
                                gol_name: item.gol_name
                            });
                        }
                        return item;
                    });
                }
                return json.data;
            }
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
        columns: [
            {
                data: null,
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
                data: "acc_name",
                render: function (data, type, row, meta) {
                    if (_.isEmpty(data)) {
                        return `-`;
                    } else {
                        return data
                    }
                }
            },
            {
                data: "gol_code",
                render: function (data, type, row, meta) {
                    if (_.isEmpty(data)) {
                        return `-`;
                    } else {
                        return `(${data}) ${row.gol_name}`;
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
            $('td', row).eq(1).css({
                'text-align': 'center'
            });
            $('td', row).eq(2).css({
                'text-align': 'left'
            });
            $('td', row).eq(3).css({
                'text-align': 'left'
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
    $("#acc_parent_gol").select2({
        allowClear: true,
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small", // Gunakan '100%' agar responsif
        dropdownParent: $('#elSelect2Golongan'),
        placeholder: "Pilih Golongan",
        ajax: {
            url: "{{ url('panel/refselect2/getGolonganS2') }}",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    // Ambil dua angka di depan dari acc_code, jika kosong nilainya 'xxx'
                    kode_gol: (function() {
                        let val = $('#acc_code').val() || '';
                        let match = val.match(/^(\d{2})/);
                        return match ? match[1] : 'xxx';
                    })(),
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

    $("#search_acc_parent_gol").select2({
        allowClear: true,
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small", // Gunakan '100%' agar responsif
        dropdownParent: $('#elFilterParentGol'),
        placeholder: "Pilih Golongan",
        ajax: {
            url: "{{ url('panel/refselect2/getGolonganS2') }}",
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
}