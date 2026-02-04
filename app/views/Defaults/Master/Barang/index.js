window.defaultUrl = `${baseUrl}panel/master/barang/`;
var table;
let rowData;

$(document).ready(function () {

	viewDatatable();

	select2Data();

	$('#deskripsi_barang').summernote({
		placeholder: 'Deskripsi Barang',
		blockquoteBreakingLevel: 1,
		codeviewFilter: false,
		codeviewIframeFilter: false,
		dialogsInBody: true,
		disableDragAndDrop: true,
		shortcuts: false,
		tabDisable: false,
		tabsize: 2,
		height: 150,
		lang: 'id-ID',
		toolbar: [
			['style', ['style']],
			['font', ['bold', 'underline', 'clear']],
			['fontname', ['fontname']],
			['color', ['color']],
			['font', ['strikethrough', 'superscript', 'subscript']],
			['para', ['ul', 'ol', 'paragraph']],
			['table', ['table']],
			['insert', ['link', 'picture',]],
			['misc', ['codeview']],
		  ],	
	});	

	// // Set Default To Disable
	// $('#btn-edit').addClass('disabled');
	// $('#btn-delete').addClass('disabled');


	$(".toggle-input").each(function () {
		let inputElement = $(this)
			.closest(".input-group")
			.find("input.form-control, select");
		inputElement.prop("disabled", !this.checked);

		$(this).change(function () {
			inputElement.prop("disabled", !this.checked);
		});
	});

	$("#btn-filter-barang").click(function (e) {
		filterModal.modal("show");
	});

	$("#btn-search").click(function (e) {
		e.preventDefault();
		$("#filterModal").modal("hide");
		refreshData();
	});

	$('#btn-perbarui').click(function (e) {
		e.preventDefault();
		window.location.href = defaultUrl;
	});

	$("#btn-add-barang").click(function (e) {
		$("#title_modal").text("Tambah Barang");
		$("#form-add").trigger("reset");
		$("#form-action").val("store"); // Set mode ke Store
		$("#barang-id").val(""); // Kosongkan ID untuk tambah
		$("#addModal").modal("show");
	});


	$("#btn-delete-barang").click(function (e) {
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
								id: rowData.id
							},
							url: defaultUrl + "delete",
							beforeSend: function () {
								$(".loading").removeClass("hide");
							},
							success: function (response) {
								$(".loading").addClass("hide");
								if (response.error == 0) {
									notyf.success("Data Berhasil dihapus");
									window.location.href = defaultUrl;
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

	$("#btn-edit-barang").click(function () {
		if (!rowData || !rowData.id) {
			notyf.open({
				type: "warning",
				message: "Pilih data yang ingin dihapus terlebih dahulu!",
			});
			return false;
		}

		$("#title_modal").text("Edit Barang");
		$("#form-action").val("update");
		$("#barang-id").val(rowData.id);
		$("#form-add").find("input[name=kode_barang]").val(rowData.kode);
		$("#form-add").find("input[name=nama_barang]").val(rowData.nama);
		$("#form-add").find("input[name=deskripsi_barang]").val(rowData.deskripsi);
		let kategoriSelect = $("#kategori_barang");
		let newOption = new Option(rowData.kategori, rowData.kategori, true, true);
		kategoriSelect.append(newOption).trigger("change");
		$("#addModal").modal("show");
	});

	$("#btn-submit").click(function () {
		let action = $("#form-action").val();
		let formData = $("#form-add").serialize();
		let id = $("#barang-id").val();

		let url =
			action === "store" ?
			`${baseUrl}panel/master/barang/store` :
			`${baseUrl}panel/master/barang/update/${id}`;

		$.ajax({
			type: "POST",
			url: url,
			data: formData,
			success: function (response) {
				if (response.error === 0) {
					notyf.success(response.message);
					$("#addModal").modal("hide");
					refreshData();
					rowData = null;
				} else {
					notyf.error(response.message);
					$("#addModal").modal("hide");
					rowData = null;
				}
			},
			error: function () {
				notyf.error("Error, Terjadi Kesalahan");
			},
		});
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


function viewDatatable() {
	table = $("#datatables-barang").DataTable({
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
				data: "kode",
				render: function (data, type, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return data
					}
				}
			},
			{
				data: "nama",
				render: function (data, index, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return data;
					}
				},
			},
			{
				data: "kategori",
				render: function (data, index, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return data;
					}
				},
			},
			{
				data: "deskripsi",
				render: function (data, index, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return data;
					}
				},
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

			//Default
			// $('td', row).eq(1).css({ 'text-align': 'left', 'font-weight': 'normal' , width: "7%"});
			// $('td', row).eq(2).css({ 'text-align': 'left', 'font-weight': 'normal', width: "30%"});

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
			$("#btn-edit-barang").addClass("disabled");
			$("#btn-delete-barang").addClass("disabled");
		} else {
			$(this).addClass("selected");
			// Enable buttons when a row is selected
			$("#btn-edit-barang").removeClass("disabled");
			$("#btn-delete-barang").removeClass("disabled");

			// Store the selected row data
			let selectedData = table.row(this).data();
			if (selectedData) {
				rowData = selectedData;
			}
		}
	});
}

function select2Data() {
	$("#kategori_barang").select2({
		allowClear: true,
		theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small", // Gunakan '100%' agar responsif
		dropdownParent: $("#form-add"),
		placeholder: "Pilih Kategori Barang",
		ajax: {
			url: "{{ url('panel/referensi/getKategori') }}",
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
							id: i.nama,
							text: i.nama
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