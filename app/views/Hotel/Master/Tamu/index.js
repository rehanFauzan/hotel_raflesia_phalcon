window.defaultUrl = `${baseUrl}panel/hotel/master/tamu/`;
var table;
let rowData;

var manageModal = $('#manageModal');
var formManage = $('#manageForm');

$(document).ready(function () {

	formManage.validate({
		rules: {
			nama_lengkap: {
				required: true
			},
			jenis_identitas: {
				required: true
			},
			no_identitas: {
				required: true
			},
			jenis_kelamin: {
				required: true
			},
			no_telepon: {
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
		},
		errorPlacement: function(error, element) {
			error.addClass('fs-9');
			error.insertAfter(element);
		}
	});

	// Initialize Select2 for filter dropdowns
	$('#search_jenis_identitas').select2({
		dropdownParent: $('#filterModal'),
		width: '100%'
	});

	$('#search_jenis_kelamin').select2({
		dropdownParent: $('#filterModal'),
		width: '100%'
	});

	// Toggle input functionality for filter
	$('.toggle-input').change(function() {
		let inputGroup = $(this).closest('.input-group');
		let input = inputGroup.find('input[type="text"], select');
		
		if ($(this).is(':checked')) {
			input.prop('disabled', false);
		} else {
			input.prop('disabled', true).val('');
		}
	});

	// Initialize all filter inputs as disabled
	$('#form-filter input[type="text"], #form-filter select').prop('disabled', true);

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

	// Initialize Select2 for form dropdowns
	$('#jenis_identitas').select2({
		dropdownParent: $('#manageModal'),
		width: '100%',
		placeholder: 'Pilih Jenis Identitas'
	});

	$('#jenis_kelamin').select2({
		dropdownParent: $('#manageModal'),
		width: '100%',
		placeholder: 'Pilih Jenis Kelamin'
	});

	// Set Default To Disable
	$('#btn-edit').addClass('disabled');
	$('#btn-delete').addClass('disabled');

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

		// Clear any hidden fields that might contain previous data
		$('#id_edit').val('');
		manageModal.find("#title_modal").text("Tambah Data Tamu")
		formManage.find('#input-action').val('store');
		$('#kebangsaan').val('Indonesia');

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

		// Clear any hidden fields that might contain previous data
		$('#id_edit').val('');

		manageModal.find("#title_modal").text("Ubah Data Tamu")
		formManage.find('#input-action').val('update');
		formManage.find('#id_edit').val(rowData.id);
		
		formManage.find('#nama_lengkap').val(rowData.nama_lengkap);
		formManage.find('#jenis_identitas').val(rowData.jenis_identitas);
		formManage.find('#no_identitas').val(rowData.no_identitas);
		formManage.find('#jenis_kelamin').val(rowData.jenis_kelamin);
		formManage.find('#tanggal_lahir').val(rowData.tanggal_lahir);
		formManage.find('#no_telepon').val(rowData.no_telepon);
		formManage.find('#email').val(rowData.email);
		formManage.find('#kebangsaan').val(rowData.kebangsaan);
		formManage.find('#pekerjaan').val(rowData.pekerjaan);
		formManage.find('#kota').val(rowData.kota);
		formManage.find('#alamat').val(rowData.alamat);
		formManage.find('#catatan').val(rowData.catatan);

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

	renderViewDatatableAction();

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
	table = $("#datatables-tamu").DataTable({
		ajax: {
			url: defaultUrl + "datatable",
			type: "post",
			data: function (d) {
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
		columns: [{
				data: "id",
				orderable: false,
				render: function (data, index, row, meta) {
					return meta.row + meta.settings._iDisplayStart + 1 + ".";
				},
			},
			{
				data: "nama_lengkap",
				render: function (data, index, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return `${data}`;
					}
				},
			},
			{
				data: "jenis_identitas",
				render: function (data, index, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return `${data}`;
					}
				},
			},
			{
				data: "no_identitas",
				render: function (data, index, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return `${data}`;
					}
				},
			},
			{
				data: "jenis_kelamin",
				render: function (data, index, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return data === 'L' ? 'Laki-laki' : 'Perempuan';
					}
				},
			},
			{
				data: "no_telepon",
				render: function (data, index, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						return `${data}`;
					}
				},
			},
			{
				data: "created_at",
				render: function (data, type, row, meta) {
					if (_.isEmpty(data)) {
						return `-`;
					} else {
						if (!_.isEmpty(row.updated_at)) {
							return `
								<span class="mr-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Updated By : ${row.name_create}">
									<i class="fas fa-user"></i>
								</span>
								<span data-bs-toggle="tooltip" data-bs-placement="bottom" title="Updated At : ${row.updated_at}">
									<i class="fas fa-clock"></i>
								</span>
							`;
						} else {
							return `
								<span class="mr-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Created By : ${row.name_create}">
									<i class="fas fa-user"></i>
								</span>
								<span data-bs-toggle="tooltip" data-bs-placement="bottom" title="Created At : ${row.created_at}">
									<i class="fas fa-clock"></i>
								</span>
							`
						}
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

			$("td", row).eq(6).css({
				"text-align": "center"
			});
		},
	}).on("click", "tr", function () {
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