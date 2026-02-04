window.defaultUrl = `${baseUrl}panel/setting/log_akses/`;
var table;

$(document).ready(function () {
    viewDatatable();
    renderTableUserData();

    // Klik tombol filter
    $("#btn-filter").click(function () {
        let ket = $('#filter_keterangan').val();
        let tgl_awal = $('#filter_tanggal_awal').val();
        let tgl_akhir = $('#filter_tanggal_akhir').val();
    
        // validasi: kalau salah satu tanggal diisi, wajib dua-duanya
        if ((tgl_awal && !tgl_akhir) || (!tgl_awal && tgl_akhir)) {
            notyf.open({
                type: "warning",
                message: "Tanggal Awal dan Tanggal Akhir harus diisi keduanya!"
            });
            return; // hentikan eksekusi
        }
    
        // isi ke hidden input supaya ikut serialize di form
        $('#form-filter').find('input[name=filter_keterangan_temp]').val(ket);
        $('#form-filter').find('input[name=filter_tanggal_awal_temp]').val(tgl_awal);
        $('#form-filter').find('input[name=filter_tanggal_akhir_temp]').val(tgl_akhir);
    
        // reload datatable
        table.ajax.reload();
    });    

    // Klik tombol reset
    $("#btn-reset").click(function () {
        $('#filter_keterangan').val('');
        $('#filter_tanggal_awal').val('');
        $('#filter_tanggal_akhir').val('');

        $('#form-filter').find('input[name=filter_keterangan_temp]').val('');
        $('#form-filter').find('input[name=filter_tanggal_awal_temp]').val('');
        $('#form-filter').find('input[name=filter_tanggal_akhir_temp]').val('');

        table.ajax.reload();
    });

    // datepicker tetap sama
    $('#filter_tanggal_awal').datepicker({
        format: "yyyy-mm-dd",
        language: "id",
        autoclose: true
    });
    $('#filter_tanggal_akhir').datepicker({
        format: "yyyy-mm-dd",
        language: "id",
        autoclose: true
    });
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


function renderTableUserData() {
    $.ajax({
        type: "GET",
        data: {},
        url: defaultUrl + "getUserdata",
        beforeSend: function (xhr, settings) {
            $(".loading").removeClass("hide");
        },
        success: function (response) {
            $(".loading").addClass("hide");
            console.log(response);

            if (response.length > 0) {
                let tableBody = '';
                let numericIndex = 1;
                response.forEach(function (user) {
                    tableBody += `
                        <tr class="rowuser" data-id="${user.id}" data-name="${user.nama}" style="cursor: pointer;">
                            <td>${numericIndex++}</td>
                            <td>${user.nama}</td>
                            <td>${user.role_nama || '-   '}</td>
                        </tr>
                    `;
                });

                $("#user-logakses-table-body").html(tableBody);

                $('#user-logakses-table-body tr').click(function () {
                    $('#user-logakses-table-body tr').removeClass('highlight-row');

                    // Add highlight to clicked row
                    $(this).addClass('highlight-row');

                    $("#fullname").text($(this).data('name'));
                    $("#id_user").val($(this).data('id'));
                    refreshData();
                });

            } else {
                notyf.open({
                    type: "warning",
                    message: "Tidak ada data yang tersedia"
                });
                $("#user-logakses-table-body").html(`
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data yang tersedia</td>
                    </tr>
                `);
            }

        },
        error: function (e) {
            notyf.error("Error, Terjadi Kesalahan");
            $(".loading").addClass("hide");
        },
    });
}


function viewDatatable() {
    table = $("#datatables-logakses").DataTable({
        ajax: {
            url: defaultUrl + "datatable",
            "type": "post",
            "data": function (d) {
                var formData = $("#form-filter").serializeArray();
                $.each(formData, function (key, val) {
                    d[val.name] = val.value;
                });
            }
        },
        serverSide: true,
        processing: true,
        responsive: true,
        selected: false,
        aaSorting: [],
        columnDefs: [{
            searchable: false,
            targets: [0]
        }],
        order: [
            [2, 'desc']
        ],
        columns: [{
                data: 'id',
                orderable: false,
                render: function (data, index, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1 + ".";
                },
            },
            {
                data: 'username'
            },
            {
                data: 'times'
            },
            {
                data: 'message'
            },
            {
                data: 'data_after',
                width: '3%',
                render: function (data) {
                    if (data === 'null') {
                        return '-';
                    } else {
                        if (!data) return '';
                        let str = data.toString();
                        let shortStr = str.length > 50 ? str.substring(0, 50) + '...' : str;
                        return `<span title="${str.replace(/"/g, '&quot;')}">${shortStr}</span>`;
                    }
                }
            },
            {
                data: 'response',
                render: function (data) {
                    if (data == 'true' || !_.isEmpty(data)) {
                        return '<span class="badge text-bg-success">BERHASIL</span>'
                    } else {
                        return '<span class="badge text-bg-danger">GAGAL</span>'
                    }
                }
            },
        ],
        "createdRow": function (row, data, index) {
            $(row).attr('data-value', encodeURIComponent(JSON.stringify(data)));
            $("thead").css({
                "vertical-align": "middle",
                "text-align": "center",
            });
            $("td", row).css({
                "vertical-align": "middle",
                padding: "0.5em",
                'cursor': 'pointer'
            });
            $("td", row).first().css({
                width: "3%",
                "text-align": "center",
            });
            //Default
            $('td', row).eq(1).css({
                'text-align': 'left',
                'font-weight': 'normal'
            });

            $('td', row).eq(4).css({
                'width': '3%'
            });

            $('td', row).eq(5).css({
                'text-align': 'center'
            });

        }

    }).on('click', 'tr', function () {
        if ($(this).hasClass('selected')) {
        } else {

        }
    });

}