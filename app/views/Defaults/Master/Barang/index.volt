{% extends 'template/base.volt' %}

{% block title %}
	Master Data - Barang
{% endblock %}

{% block inject_style %}

	<style>
		table th,
		table td {
			padding: 6px !important; /* Mengurangi padding */
		}
		#datatables-barang tbody tr {
			height: 50px; /* Atur tinggi baris */
		}

		#datatables-barang tbody td {
			padding: 12px 10px; /* Atur padding di dalam sel */
		}
	</style>

{% endblock %}

{% block content %}
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Master</a>
			</li>
			<li class="breadcrumb-item active">Barang</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Barang</h2>
			</div>
		</div>
		<div id="products">
			<div class="mb-4">
				<div class="row g-3 justify-content-end gap-2">
					<div class="col-auto">
						<button class="btn btn-sm btn-outline-primary my-1" id="btn-filter-barang">
							<span class="fas fa-search me-2"></span>Filter
						</button>
						<button class="btn btn-sm btn-success my-1" id="btn-perbarui">
							<span class="fas fa-sync me-2"></span>Perbarui
						</button>
						<button class="btn btn-sm btn-primary my-1" id="btn-add-barang">
							<span class="fas fa-plus me-2"></span>Tambah
						</button>
						<button class="btn btn-sm btn-warning my-1" id="btn-edit-barang">
							<span class="fas fa-pencil-alt me-2"></span>Edit
						</button>
						<button class="btn btn-sm btn-danger my-1" id="btn-delete-barang">
							<span class="fas fa-backspace me-2"></span>Hapus
						</button>
					</div>
				</div>
			</div>
			<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
				<div class="table-responsive scrollbar-overlay mx-n1 px-1">
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-barang">
						<thead>
							<tr
								class="p-2">
								<!-- Mengurangi padding -->
								<th class="sort align-middle text-center px-2" scope="col" style="width: 5%">
									NO
								</th>
								<th class="sort align-middle text-center px-2" scope="col" style="width: 15%">
									KODE BARANG
								</th>
								<th class="sort align-middle text-center px-2" scope="col" style="width: 40%">
									NAMA BARANG
								</th>
								<th class="sort align-middle text-center px-2" scope="col" style="width: 20%">
									KATEGORI
								</th>
								<th class="sort align-middle text-center px-2" scope="col" style="width: 20%">
									DESKRIPSI
								</th>
							</tr>
						</thead>
						<tbody
							class="list" id="customers-table-body"><!-- Data akan diisi dari script -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="addModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="add`Modal" aria-hidden="true">
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
							<form id="form-add">
								<div class="mb-4">
									<input type="hidden" id="form-action" value="store"/>
									<input type="hidden" id="barang-id" name="barang_id"/>
									<label class="text-body-highlight fw-bold mb-2">Nama Barang</label>
									<input class="form-control" type="text" name="nama_barang" placeholder="Masukkan Nama Barang"/>
								</div>
								<div class="mb-4">
									<label class="text-body-highlight fw-bold mb-2">Kode Barang</label>
									<input class="form-control" type="text" name="kode_barang" placeholder="Masukkan Kode Barang"/>
								</div>
								<div class="mb-4">
									<label class="text-body-highlight fw-bold mb-2">Kategori Barang</label>
									<select name="kategori_barang" id="kategori_barang" class="form-select"></select>
								</div>
								<div class="mb-4">
									<label class="text-body-highlight fw-bold mb-2">Deskripsi Barang</label>
									<textarea name="deskripsi_barang" id="deskripsi_barang" class="form-control"></textarea>
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
	<div class="modal fade" id="filterModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="add`Modal" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content bg-body-highlight p-6">
				<div class="modal-header justify-content-between border-0 p-0 mb-2">
					<h3 class="mb-0">Filter Barang</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="form-filter">
								<div class="mb-1">
									<label class="text-body-highlight fw-bold mb-2">Nama Barang</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<input class="form-control" type="text" name="nama_barang_search" placeholder="Input Nama Barang"/>
									</div>
								</div>

								<div class="mb-1">
									<label class="text-body-highlight fw-bold mb-2">Kode Barang</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<input class="form-control" type="text" name="kode_barang_search" placeholder="Masukkan Kode Barang"/>
									</div>
								</div>

								<div class="mb-1">
									<label class="text-body-highlight fw-bold mb-2">Kategori Barang</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<select name="kategori_barang_search" class="form-select">
											<option value="">Pilih Kategori</option>
											<option value="elektronik">Elektronik</option>
											<option value="fashion">Fashion</option>
										</select>
									</div>
								</div>

								<div class="mb-1">
									<label class="text-body-highlight fw-bold mb-2">Deskripsi Barang</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<input class="form-control" type="text" name="deskripsi_barang_search" placeholder="Masukkan Deskripsi Barang"/>
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
{% endblock %}


{% block inline_script %}
	{% include "Defaults/Master/Barang/index.js" %}
{% endblock %}
