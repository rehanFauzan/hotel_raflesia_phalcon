{% extends 'template/base.volt' %}

{% block title %}
    Master Data - Tamu
{% endblock %}

{% block inline_style %}
	<style>
		table th,
		table td {
			padding: 6px !important;
		}
		#datatables-tamu tbody tr {
			height: 50px;
		}
		#datatables-tamu tbody td {
			padding: 12px 10px;
		}
	</style>
{% endblock %}

{% block content %}
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Master Data</a>
			</li>
			<li class="breadcrumb-item active">Tamu</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Tamu</h2>
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
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-tamu">
						<thead>
							<tr class="p-2 text-center">
								<th class="sort px-2" scope="col">#</th>
								<th class="sort px-2" scope="col">Nama Lengkap</th>
								<th class="sort px-2" scope="col">Jenis Identitas</th>
								<th class="sort px-2" scope="col">No. Identitas</th>
								<th class="sort px-2" scope="col">Jenis Kelamin</th>
								<th class="sort px-2" scope="col">No. Telepon</th>
								<th class="sort px-2" scope="col">#</th>
							</tr>
						</thead>
						<tbody class="list" id="tamu-table-body">
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
					<h3 class="mb-0">Filter Data Tamu</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="form-filter">
								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Nama Lengkap</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<input class="form-control" type="text" id="search_nama" name="search_nama" placeholder="Cari berdasarkan nama..."/>
									</div>
								</div>

								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Jenis Identitas</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<div class="form-control">
											<select name="search_jenis_identitas" id="search_jenis_identitas" class="form-control">
												<option value="">Pilih Jenis Identitas</option>
												<option value="ktp">KTP</option>
												<option value="sim">SIM</option>
												<option value="passport">Passport</option>
											</select>
										</div>
									</div>
								</div>

								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Jenis Kelamin</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<div class="form-control">
											<select name="search_jenis_kelamin" id="search_jenis_kelamin" class="form-control">
												<option value="">Pilih Jenis Kelamin</option>
												<option value="L">Laki-laki</option>
												<option value="P">Perempuan</option>
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

								<div class="row">
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Nama Lengkap</label>
											<input class="form-control" type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan Nama Lengkap ..."/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Jenis Identitas</label>
											<select class="form-control" id="jenis_identitas" name="jenis_identitas">
												<option value="">Pilih Jenis Identitas</option>
												<option value="ktp">KTP</option>
												<option value="sim">SIM</option>
												<option value="passport">Passport</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">No. Identitas</label>
											<input class="form-control" type="text" id="no_identitas" name="no_identitas" placeholder="Masukkan No. Identitas ..."/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Jenis Kelamin</label>
											<select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
												<option value="">Pilih Jenis Kelamin</option>
												<option value="L">Laki-laki</option>
												<option value="P">Perempuan</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Tanggal Lahir</label>
											<input class="form-control" type="date" id="tanggal_lahir" name="tanggal_lahir"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">No. Telepon</label>
											<input class="form-control" type="text" id="no_telepon" name="no_telepon" placeholder="Masukkan No. Telepon ..."/>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Email</label>
											<input class="form-control" type="email" id="email" name="email" placeholder="Masukkan Email ..."/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Kebangsaan</label>
											<input class="form-control" type="text" id="kebangsaan" name="kebangsaan" value="Indonesia" placeholder="Masukkan Kebangsaan ..."/>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Pekerjaan</label>
											<input class="form-control" type="text" id="pekerjaan" name="pekerjaan" placeholder="Masukkan Pekerjaan ..."/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Kota</label>
											<input class="form-control" type="text" id="kota" name="kota" placeholder="Masukkan Kota ..."/>
										</div>
									</div>
								</div>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Alamat</label>
									<textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan Alamat ..."></textarea>
								</div>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Catatan</label>
									<textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Masukkan Catatan ..."></textarea>
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

	{{ flash.output() }}

{% endblock %}

{% block inline_script %}
	{% include "Hotel/Master/Tamu/index.js" %}
{% endblock %}