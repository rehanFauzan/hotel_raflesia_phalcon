{% extends 'template/base.volt' %}

{% block title %}
    Laporan - Tamu Harian
{% endblock %}

{% block inline_style %}
	<style>
		table th,
		table td {
			padding: 6px !important;
		}
		#datatables-tamu-harian tbody tr {
			height: 50px;
		}
		#datatables-tamu-harian tbody td {
			padding: 12px 10px;
		}
	</style>
{% endblock %}

{% block content %}
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Laporan</a>
			</li>
			<li class="breadcrumb-item active">Tamu Harian</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Laporan Tamu Harian</h2>
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
						{% if is_can_print == '1' %}
							<button class="btn btn-sm btn-danger my-1" id="btn-print">
								<span class="fas fa-file-pdf me-2"></span>Export PDF
							</button>
						{% endif %}
					</div>
				</div>
			</div>
			<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
				<div class="table-responsive scrollbar-overlay mx-n1 px-1">
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-tamu-harian">
						<thead>
							<tr class="p-2 text-center">
								<th class="sort px-2" scope="col">#</th>
								<th class="sort px-2" scope="col">Nama Tamu</th>
								<th class="sort px-2" scope="col">Jenis Identitas</th>
								<th class="sort px-2" scope="col">No. Identitas</th>
								<th class="sort px-2" scope="col">Jenis Kelamin</th>
								<th class="sort px-2" scope="col">No. Telepon</th>
								<th class="sort px-2" scope="col">No. Kamar</th>
								<th class="sort px-2" scope="col">Tanggal Check-in</th>
								<th class="sort px-2" scope="col">Status</th>
							</tr>
						</thead>
						<tbody class="list" id="tamu-harian-table-body">
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
					<h3 class="mb-0">Filter Laporan Tamu Harian</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="form-filter">
								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Tanggal</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<input class="form-control" type="date" id="search_tanggal" name="search_tanggal"/>
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
												<option value="checkin">Check-in</option>
												<option value="checkout">Check-out</option>
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

	{{ flash.output() }}

{% endblock %}

{% block inline_script %}
	{% include "Hotel/Laporan/TamuPerHari/index.js" %}
{% endblock %}