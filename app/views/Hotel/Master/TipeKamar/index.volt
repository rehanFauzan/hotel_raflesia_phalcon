{% extends 'template/base.volt' %}

{% block title %}
    Master - Tipe Kamar
{% endblock %}

{% block inline_style %}
	<style>
		table th,
		table td {
			padding: 6px !important;
		}
		#datatables-tipe-kamar tbody tr {
			height: 50px;
		}
		#datatables-tipe-kamar tbody td {
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
			<li class="breadcrumb-item active">Tipe Kamar</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Tipe Kamar</h2>
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
						{% if is_can_insert == '1' %}
							<button class="btn btn-sm btn-primary my-1" id="btn-add">
								<span class="fas fa-plus me-2"></span>Tambah
							</button>
						{% endif %}
						{% if is_can_update == '1' %}
							<button class="btn btn-sm btn-warning my-1" id="btn-edit">
								<span class="fas fa-pencil-alt me-2"></span>Edit
							</button>
						{% endif %}
						{% if is_can_delete == '1' %}
							<button class="btn btn-sm btn-danger my-1" id="btn-delete">
								<span class="fas fa-trash me-2"></span>Hapus
							</button>
						{% endif %}
					</div>
				</div>
			</div>
			<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
				<div class="table-responsive scrollbar-overlay mx-n1 px-1">
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-tipe-kamar">
						<thead>
							<tr class="p-2 text-center">
								<th class="sort px-2" scope="col">#</th>
								<th class="sort px-2" scope="col">Nama</th>
								<th class="sort px-2" scope="col">Deskripsi</th>
								<th class="sort px-2" scope="col">Harga/Malam</th>
								<th class="sort px-2" scope="col">Kapasitas</th>
								<th class="sort px-2" scope="col">Fasilitas</th>
								<th class="sort px-2" scope="col">Ruangan Tersedia</th>
								<th class="sort px-2" scope="col">Ruangan Terpakai</th>
							</tr>
						</thead>
						<tbody class="list" id="tipe-kamar-table-body">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="manageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addModal" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content bg-body-highlight p-6">
				<div class="modal-header justify-content-between border-0 p-0 mb-2">
					<h3 class="mb-0" id="title_modal"></h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="manageForm">
								<input type="hidden" id="input-action" name="input_action" value="store"/>
								<input type="hidden" id="id_edit" name="id_edit"/>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Nama Tipe Kamar</label>
									<input class="form-control" type="text" id="nama" name="nama" placeholder="Masukkan Nama Tipe Kamar ..."/>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Harga per Malam</label>
											<input class="form-control" type="number" id="harga_per_malam" name="harga_per_malam" placeholder="Masukkan Harga ..."/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Kapasitas</label>
											<input class="form-control" type="number" id="kapasitas" name="kapasitas" placeholder="Masukkan Kapasitas ..."/>
										</div>
									</div>
								</div>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Status</label>
									<select class="form-control" id="status" name="status">
										<option value="">Pilih Status</option>
										<option value="active">Aktif</option>
										<option value="maintenance">Maintenance</option>
									</select>
								</div>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Deskripsi</label>
									<textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan Deskripsi ..."></textarea>
								</div>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Fasilitas</label>
									<textarea class="form-control" id="fasilitas" name="fasilitas" rows="3" placeholder="Contoh: AC, TV, WiFi, Kamar Mandi Dalam"></textarea>
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

	<div class="modal fade" id="filterModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="filterModal" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content bg-body-highlight p-6">
				<div class="modal-header justify-content-between border-0 p-0 mb-2">
					<h3 class="mb-0">Filter Data Tipe Kamar</h3>
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

								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Kapasitas</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<div class="form-control">
											<select name="search_kapasitas" id="search_kapasitas" class="form-control">
												<option value="">Pilih Kapasitas</option>
												<option value="1">1 Orang</option>
												<option value="2">2 Orang</option>
												<option value="3">3 Orang</option>
												<option value="4">4 Orang</option>
												<option value="5">5+ Orang</option>
											</select>
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

	{{ flash.output() }}

{% endblock %}

{% block inline_script %}
	{% include "Hotel/Master/TipeKamar/index.js" %}
{% endblock %}