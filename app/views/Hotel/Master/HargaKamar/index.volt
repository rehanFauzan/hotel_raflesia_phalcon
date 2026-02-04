{% extends 'template/base.volt' %}

{% block title %}
    Master - Harga Kamar
{% endblock %}

{% block inline_style %}
	<style>
		table th,
		table td {
			padding: 6px !important;
		}
		#datatables-harga-kamar tbody tr {
			height: 50px;
		}
		#datatables-harga-kamar tbody td {
			padding: 12px 10px;
		}
	</style>
{% endblock %}

{% block content %}
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Master</a>
			</li>
			<li class="breadcrumb-item active">Harga Kamar</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Harga Kamar</h2>
			</div>
		</div>
		<div id="products">
			<div class="mb-4">
				<div class="row g-3 justify-content-end gap-2">
					<div class="col-auto">
						<button class="btn btn-sm btn-outline-primary my-1" id="btn-filter">
							<span class="fas fa-search me-2"></span>Filter
						</button>
						<button class="btn btn-sm btn-success my-1" id="btn-perbarui">
							<span class="fas fa-sync me-2"></span>Perbarui
						</button>
						{% if is_can_update == '1' %}
							<button class="btn btn-sm btn-warning my-1" id="btn-edit">
								<span class="fas fa-pencil-alt me-2"></span>Edit Harga
							</button>
						{% endif %}
					</div>
				</div>
			</div>
			<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
				<div class="table-responsive scrollbar-overlay mx-n1 px-1">
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-harga-kamar">
						<thead>
							<tr class="p-2 text-center">
								<th class="sort px-2" scope="col">No</th>
								<th class="sort px-2" scope="col">ID</th>
								<th class="sort px-2" scope="col">Nama Tipe</th>
								<th class="sort px-2" scope="col">Harga per Malam</th>
							</tr>
						</thead>
						<tbody class="list" id="harga-kamar-table-body">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="filterModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="filterModal" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content bg-body-highlight p-6">
				<div class="modal-header justify-content-between border-0 p-0 mb-2">
					<h3 class="mb-0">Filter Data Harga Kamar</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="form-filter">
								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Nama Tipe Kamar</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<div class="form-control">
											<select name="search_nama" id="search_nama" class="form-control">
												<option value="">Pilih Tipe Kamar</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label class="text-body-highlight fw-bold mb-2">Harga Minimum</label>
											<div class="input-group">
												<div class="input-group-text">
													<input class="form-check-input toggle-input" type="checkbox"/>
												</div>
												<input class="form-control" type="number" id="search_harga_min" name="search_harga_min" placeholder="Harga minimum..."/>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="text-body-highlight fw-bold mb-2">Harga Maksimum</label>
											<div class="input-group">
												<div class="input-group-text">
													<input class="form-check-input toggle-input" type="checkbox"/>
												</div>
												<input class="form-control" type="number" id="search_harga_max" name="search_harga_max" placeholder="Harga maksimum..."/>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="modal-footer border-0 pt-6 px-0 pb-0">
					<button class="btn btn-lighter-grey px-3 my-0" data-bs-dismiss="modal" aria-label="Close">
						Batal
					</button>
					<button class="btn btn-primary my-0" id="btn-search">Cari Data</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="manageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addModal" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content bg-body-highlight p-6">
				<div class="modal-header justify-content-between border-0 p-0 mb-2">
					<h3 class="mb-0" id="title_modal">Edit Harga Kamar</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="manageForm">
								<input type="hidden" id="id_edit" name="id_edit"/>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">ID Tipe Kamar</label>
									<input class="form-control" type="text" id="id_display" readonly/>
								</div>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Nama Tipe Kamar</label>
									<input class="form-control" type="text" id="nama_display" readonly/>
								</div>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Harga per Malam</label>
									<input class="form-control" type="number" id="harga_per_malam" name="harga_per_malam" placeholder="Masukkan Harga ..."/>
								</div>

							</form>
						</div>
					</div>
				</div>
				<div class="modal-footer border-0 pt-6 px-0 pb-0">
					<button class="btn btn-lighter-grey px-3 my-0" data-bs-dismiss="modal" aria-label="Close">
						Batal
					</button>
					<button class="btn btn-primary my-0" id="btn-submit">Simpan</button>
				</div>
			</div>
		</div>
	</div>

{% endblock %}

{% block inline_script %}
	{% include "Hotel/Master/HargaKamar/index.js" %}
{% endblock %}