window.defaultUrl = `${baseUrl}panel/master/meter_produksi/`;
var table;
let rowData;

var manageModal = $('#manageModal');
var filterModal = $('#filterModal');
var formManage = $('#manageForm');

$(document).ready(function () {

	formManage.validate({
		rules: {
			nama_instalasi: {
				required: true
			},
            nama_meter_produksi: {
				required: true
			},
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
	renderSelect2Action();


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
		window.location.href = defaultUrl;
	});

	$('#btn-add').click(function (e) {
		e.preventDefault();

		// Reset form fields
		formManage.trigger("reset");

		// Clear validation errors
		formManage.find('.is-invalid').removeClass('is-invalid');
		formManage.find('.text-danger').remove();

		// Reset select2 dropdowns
		$('#nama_instalasi').val(null).trigger('change');
		$('#nama_meter_produksi').val(null).trigger('change');

		// Clear any hidden fields that might contain previous data
		$('#id_edit').val('');
		manageModal.find("#title_modal").text("Tambah Data Meter Produksi")
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
			content: `Anda yakin ingin menghapus meter produksi ${rowData.nama_meter_produksi}?`,
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

	$('#btn-edit').click(function (e) {
		e.preventDefault();

		console.log(rowData); 

		if (!rowData || !rowData.id) {
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
		$('#kode_instalasi').val(null).trigger('change');
		$('#nama_instalasi').val(null).trigger('change');

		// Clear any hidden fields that might contain previous data
		$('#id_edit').val('');

		manageModal.find("#title_modal").text("Ubah Data Meter Produksi")
		formManage.find('#input-action').val('update');
		formManage.find('#id_edit').val(rowData.id);
		
		formManage.find("#nama_instalasi").select2("trigger", "select", {
			data: {
				id: rowData.id_installasi,
				text: rowData.nama_installasi,
				selected: true
			}
		});
		formManage.find('#nama_meter_produksi').val(rowData.nama_meter_produksi);

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
				location.href = defaultUrl;
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
	table = $("#datatables-meter-produksi").DataTable({
		ajax: {
			url: defaultUrl + "datatable",
			type: "post",
			data: function (d) {
				var formData = $("#form-filter").serializeArray();
				$.each(formData, function (key, val) {
					d[val.name] = val.value;
				});

				// Add search term explicitly for server-side handling
				d.search = d.search.value;
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
				data: "nama_installasi",
				render: function (data, index, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return data;
					}
				},
			},
            {
				data: "nama_meter_produksi",
				render: function (data, index, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return data;
					}
				},
			},
			// {
            //     data: "created_at",
            //     render: function (data, type, row, meta) {
            //         if (_.isEmpty(data)) {
            //             return `-`;
            //         } else {
            //             if (!_.isEmpty(row.updated_at)) {
            //                 return `
            //                     <span class="mr-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Updated By : ${row.name_create}">
            //                         <i class="fas fa-user"></i>
            //                     </span>
            //                     <span data-bs-toggle="tooltip" data-bs-placement="bottom" title="Updated At : ${row.updated_at}">
            //                         <i class="fas fa-clock"></i>
            //                     </span>
            //                 `;
            //             } else {
            //                 return `
            //                     <span class="mr-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Created By : ${row.name_create}">
            //                         <i class="fas fa-user"></i>
            //                     </span>
            //                     <span data-bs-toggle="tooltip" data-bs-placement="bottom" title="Created At : ${row.created_at}">
            //                         <i class="fas fa-clock"></i>
            //                     </span>
            //                 `
            //             }
            //         }
            //     }
            // }
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

			$("td", row).eq(6).css({
				"text-align": "center"
			});
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

function renderSelect2Action() {
	$("#nama_instalasi").select2({
		allowClear: true,
		theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small", // Gunakan '100%' agar responsif
		dropdownParent: manageModal,
		placeholder: "Pilih Instalasi",
		ajax: {
			url: "{{ url('panel/referensi/getInstalasi') }}",
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
							id: i.id,
							text: `(${i.kode_installasi}) ${i.nama_installasi}`
						};
					}),
					pagination: {
						more: data.has_more,
					},
				};
			},
		},
	});

	$("#search_id_installasi").select2({
		allowClear: true,
		theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small",
		dropdownParent: $('#elFilterNamaInstalasi'),
		placeholder: "Pilih Nama Instalasi",
		ajax: {
			url: "{{ url('panel/referensi/getInstalasi') }}",
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
							id: i.id,
							text: `(${i.kode_installasi}) ${i.nama_installasi}`
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