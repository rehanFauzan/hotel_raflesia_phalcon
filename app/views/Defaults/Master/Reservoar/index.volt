{% extends 'template/base.volt' %}

{% block title %}
    Referensi Master - Reservoar
{% endblock %}


{% block inline_style %}

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
				<a href="#!">Referensi Data</a>
			</li>
			<li class="breadcrumb-item active">Reservoar</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Reservoar</h2>
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

						<!-- {% if is_can_insert == '1' %} -->
						<button class="btn btn-sm btn-primary my-1" id="btn-add">
							<span class="fas fa-plus me-2"></span>Tambah
						</button>
						<!-- {% endif %} -->
						
						<!-- {% if is_can_update == '1' %} -->
						<button class="btn btn-sm btn-warning my-1" id="btn-edit">
							<span class="fas fa-pencil-alt me-2"></span>Edit
						</button>
						<!-- {% endif %} -->

						<!-- {% if is_can_delete == '1' %} -->
						<button class="btn btn-sm btn-danger my-1" id="btn-delete">
							<span class="fas fas fa-backspace me-2"></span>Hapus
						</button>
						<!-- {% endif %} -->
						
					</div>
				</div>
			</div>
			<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
				<div class="table-responsive scrollbar-overlay mx-n1 px-1">
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-reservoar">
						<thead>
							<tr class="p-2 text-center">
								<!-- Mengurangi padding -->
								<th class="sort px-2" scope="col">
									#
								</th>
								<th class="sort px-2" scope="col">
									Nama Instalasi
								</th>
                                <th class="sort px-2" scope="col">
									Nama Reservoar
								</th>
							</tr>
						</thead>
						<tbody class="list" id="reservoar-table-body"><!-- Data akan diisi dari script -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="manageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="add`Modal" aria-hidden="true">
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
									<label class="text-body-highlight fw-bold mb-2">Nama Instalasi</label>
									<select name="nama_instalasi" id="nama_instalasi" class="form-select"></select>
								</div>

                                <div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Nama Reservoar</label>
									<input class="form-control" type="text" id="nama_reservoar" name="nama_reservoar" placeholder="Masukkan Nama Reservoar ..."/>
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
					<h3 class="mb-0">Filter Data</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="form-filter">

                                <div class="mb-1">
									<label class="text-body-highlight fw-bold mb-2">Nama Instalasi</label>
									<div class="input-group" id="elFilterNamaInstalasi">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<div class="form-control">
											<select name="search_id_installasi" id="search_id_installasi" class="form-control"></select>
										</div>
									</div>
								</div>

                                <div class="mb-1">
									<label class="text-body-highlight fw-bold mb-2">Nama Reservoar</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<input class="form-control" type="text" id="search_nama_reservoar" name="search_nama_reservoar" placeholder="Input Nama Reservoar"/>
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
	{% include "Defaults/Master/Reservoar/index.js" %}
{% endblock %}
