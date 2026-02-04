window.defaultUrl = `${baseUrl}panel/setting/otoritas_menu/`;
var table;
var rowData;

$(document).ready(function () {

    renderViewDatatableAction();


    $('#btn-perbarui').click(function (e) {
		e.preventDefault();
		table.ajax.reload();
	});


    $("#btn-hak-akses").click(function (e) {
		if (!rowData || !rowData.id) {
			notyf.open({
				type: "warning",
				message: "Pilih data terlebih dahulu!",
			});
			return false;
		} else {
            window.location.href = defaultUrl + "menuAkses?id=" + rowData.id;
        }
    });

});


function renderViewDatatableAction() {
    table = $("#datatables-otoritas-menu").DataTable({
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
                data: "role",
                render: function (data, type, row, meta) {
                    if (_.isEmpty(data)) {
                        return `-`;
                    } else {
                        return `${data}`;
                    }
                },
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
            $('td', row).eq(2).css({
                'text-align': 'center'
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
            $("#btn-hak-akses").addClass("disabled");
        } else {
            $(this).addClass("selected");
            // Enable buttons when a row is selected
            $("#btn-hak-akses").removeClass("disabled");


            // Store the selected row data
            let selectedData = table.row(this).data();
            if (selectedData) {
                rowData = selectedData;
            }
        }
    });
}