{% extends 'template/base.volt' %}

{% block title %}
    Master - Kamar
{% endblock %}

{% block inline_style %}
	<style>
		table th,
		table td {
			padding: 6px !important;
		}
		#datatables-kamar tbody tr {
			height: 50px;
		}
		#datatables-kamar tbody td {
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
			<li class="breadcrumb-item active">Kamar</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Kamar</h2>
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
						{% if is_can_insert %}
						<button class="btn btn-sm btn-primary my-1" id="btn-tambah">
							<span class="fas fa-plus me-2"></span>Tambah
						</button>
						{% endif %}
						{% if is_can_update %}
						<button class="btn btn-sm btn-warning my-1" id="btn-edit">
							<span class="fas fa-pencil-alt me-2"></span>Edit
						</button>
						{% endif %}
						{% if is_can_delete %}
						<button class="btn btn-sm btn-danger my-1" id="btn-hapus">
							<span class="fas fa-trash me-2"></span>Hapus
						</button>
						{% endif %}
					</div>
				</div>
			</div>
			<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
				<div class="table-responsive scrollbar-overlay mx-n1 px-1">
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-kamar">
						<thead>
							<tr class="p-2 text-center">
								<th class="sort px-2" scope="col">No</th>
								<th class="sort px-2" scope="col">Nomor Kamar</th>
								<th class="sort px-2" scope="col">Tipe Kamar</th>
								<th class="sort px-2" scope="col">Lantai</th>
								<th class="sort px-2" scope="col">Status</th>
								<th class="sort px-2" scope="col">Keterangan</th>
							</tr>
						</thead>
						<tbody class="list" id="kamar-table-body">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Filter Modal -->
	<div class="modal fade" id="filterModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="filterModal" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content bg-body-highlight p-6">
				<div class="modal-header justify-content-between border-0 p-0 mb-2">
					<h3 class="mb-0">Filter Data Kamar</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="form-filter">
								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Nomor Kamar</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<input class="form-control" type="text" id="search_nomor_kamar" name="search_nomor_kamar" placeholder="Cari berdasarkan nomor kamar..."/>
									</div>
								</div>

								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Status</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<div class="form-control">
											<select name="search_status" id="search_status" class="form-control">
												<option value="">Pilih Status</option>
												<option value="tersedia">Tersedia</option>
												<option value="terisi">Terisi</option>
												<option value="maintenance">Maintenance</option>
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

	<!-- Add/Edit Modal -->
	<div class="modal fade" id="kamarModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="kamarModal" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content bg-body-highlight p-6">
				<div class="modal-header justify-content-between border-0 p-0 mb-2">
					<h3 class="mb-0" id="modal-title">Tambah Data Kamar</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<form id="form-kamar">
						<input type="hidden" id="id_edit" name="id_edit">
						
						<div class="mb-3">
							<label class="text-body-highlight fw-bold mb-2">Nomor Kamar <span class="text-danger">*</span></label>
							<input class="form-control" type="text" id="nomor_kamar" name="nomor_kamar" placeholder="Masukkan nomor kamar" required/>
						</div>

						<div class="mb-3">
							<label class="text-body-highlight fw-bold mb-2">Tipe Kamar <span class="text-danger">*</span></label>
							<select class="form-control" id="tipe_ruangan_id" name="tipe_ruangan_id" required>
								<option value="">Pilih Tipe Kamar</option>
							</select>
						</div>

						<div class="mb-3">
							<label class="text-body-highlight fw-bold mb-2">Lantai <span class="text-danger">*</span></label>
							<input class="form-control" type="number" id="lantai" name="lantai" placeholder="Masukkan lantai" min="1" required/>
						</div>

						<div class="mb-3">
							<label class="text-body-highlight fw-bold mb-2">Status <span class="text-danger">*</span></label>
							<select class="form-control" id="status" name="status" required>
								<option value="">Pilih Status</option>
								<option value="tersedia">Tersedia</option>
								<option value="terisi">Terisi</option>
								<option value="maintenance">Maintenance</option>
							</select>
						</div>

						<div class="mb-3">
							<label class="text-body-highlight fw-bold mb-2">Keterangan</label>
							<textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan (opsional)"></textarea>
						</div>
					</form>
				</div>
				<div class="modal-footer border-0 pt-6 px-0 pb-0">
					<button class="btn btn-lighter-grey px-3 my-0" data-bs-dismiss="modal" aria-label="Close">
						Batal
					</button>
					<button class="btn btn-primary my-0" id="btn-save">Simpan</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Delete Modal -->
	<div class="modal fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content bg-body-highlight p-6">
				<div class="modal-header justify-content-between border-0 p-0 mb-2">
					<h3 class="mb-0">Konfirmasi Hapus</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<p>Apakah Anda yakin ingin menghapus data kamar ini?</p>
					<input type="hidden" id="id_delete" name="id_delete">
				</div>
				<div class="modal-footer border-0 pt-6 px-0 pb-0">
					<button class="btn btn-lighter-grey px-3 my-0" data-bs-dismiss="modal" aria-label="Close">
						Batal
					</button>
					<button class="btn btn-danger my-0" id="btn-delete">Hapus</button>
				</div>
			</div>
		</div>
	</div>

{% endblock %}

{% block inline_script %}
	{% include "Hotel/Master/Kamar/index.js" %}
{% endblock %}