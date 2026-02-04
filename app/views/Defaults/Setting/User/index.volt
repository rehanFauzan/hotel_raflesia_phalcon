{% extends 'template/base.volt' %}

{% block title %}
    Setting - User
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
				<a href="#!">Setting</a>
			</li>
			<li class="breadcrumb-item active">User</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">User</h2>
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
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-user">
						<thead>
							<tr class="p-2 text-center">
								<!-- Mengurangi padding -->
								<th class="sort px-2" scope="col">
									NO
								</th>
								<th class="sort px-2" scope="col">
									NAMA
								</th>
								<th class="sort px-2" scope="col">
									USERNAME
								</th>
                                <th class="sort px-2" scope="col">
									ROLE
								</th>
                                <th class="sort px-2" scope="col">
									STATUS
								</th>
							</tr>
						</thead>
						<tbody class="list" id="users-table-body"><!-- Data akan diisi dari script -->
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
                                    <div class="input-group input-group-sm" id="elFilterParentSatker">
                                        <span class="input-group-text fw-bold" id="lbl_pilih_satker">
                                            Satker
                                        </span>
                                        <select name="satker_id" id="satker_id" class="form-control form-control-sm"></select>
                                    </div>
                                </div>

								<div class="mb-2">
                                    <div class="input-group input-group-sm" id="elFilterParentNama">
                                        <span class="input-group-text fw-bold" id="lbl_pilih_nama">
                                            Nama
                                        </span>
                                        <input name="nama" id="nama" class="form-control form-control-sm" type="text" placeholder="Masukkan Nama ...">
                                    </div>
                                </div>

								<div class="mb-2">
                                    <div class="input-group input-group-sm" id="elFilterParentUsername">
                                        <span class="input-group-text fw-bold" id="lbl_pilih_username">
                                            Username
                                        </span>
                                        <input name="username" id="username" class="form-control form-control-sm" type="text" placeholder="Masukkan Username ...">
                                    </div>
                                </div>

								<div class="mb-2">
                                    <div class="input-group input-group-sm" id="elFilterParentPassword">
                                        <span class="input-group-text fw-bold" id="lbl_pilih_password">
                                            Password
                                        </span>
                                        <input name="password" id="password" class="form-control form-control-sm" type="password" placeholder="Masukkan Password ...">
                                    </div>
                                </div>

								<div class="mb-2">
                                    <div class="input-group input-group-sm" id="elFilterParentRole">
                                        <span class="input-group-text fw-bold" id="lbl_pilih_role">
                                            Role
                                        </span>
                                        <select name="role_id" id="role_id" class="form-control form-control-sm"></select>
                                    </div>
                                </div>

								<div class="mb-2">
                                    <div class="input-group input-group-sm" id="elFilterParentState">
                                        <span class="input-group-text fw-bold" id="lbl_pilih_state">
                                            Status
                                        </span>
                                        <select name="state" id="state" class="form-control form-control-sm">
											<option value="" selected disabled> Pilih Status </option>
											<option value="1"> Aktif </option>
											<option value="0"> Non Aktif </option>
										</select>
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

								<div class="mb-3">
									<div class="input-group input-group-sm">
										<span class="input-group-text">
											<input class="form-check-input toggle-input mr-2" name="chk_satuan_kerja" type="checkbox"/>
										</span>
										<span class="input-group-text fw-bold" id="inputGroup-sizing-sm">
											Satuan Kerja
										</span>
										<select name="search_satker_id" id="search_satker_id" class="form-select"></select>
									</div>
								</div>

								<div class="mb-3">
									<div class="input-group input-group-sm">
										<span class="input-group-text">
											<input class="form-check-input toggle-input mr-2" name="chk_nama" type="checkbox"/>
										</span>
										<span class="input-group-text fw-bold" id="inputGroup-sizing-sm">
											Nama
										</span>
										<input class="form-control form-control-sm" type="text" id="search_nama" name="search_nama" placeholder="Cari berdasarkan Nama"/>
									</div>
								</div>

								<div class="mb-3">
									<div class="input-group input-group-sm">
										<span class="input-group-text">
											<input class="form-check-input toggle-input mr-2" name="chk_username" type="checkbox"/>
										</span>
										<span class="input-group-text fw-bold" id="inputGroup-sizing-sm">
											Username
										</span>
										<input class="form-control form-control-sm" type="text" id="search_username" name="search_username" placeholder="Cari berdasarkan Username"/>
									</div>
								</div>

								<div class="mb-3">
									<div class="input-group input-group-sm">
										<span class="input-group-text">
											<input class="form-check-input toggle-input mr-2" name="chk_role" type="checkbox"/>
										</span>
										<span class="input-group-text fw-bold" id="inputGroup-sizing-sm">
											Role
										</span>
										<select name="search_role_id" id="search_role_id" class="form-select"></select>
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
	{% include "Defaults/Setting/User/index.js" %}
{% endblock %}
