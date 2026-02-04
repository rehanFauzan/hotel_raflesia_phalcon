{% extends 'template/base.volt' %}

{% block title %}
	Setting - Log Akses
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

		.loading {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.5);
			z-index: 9999;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.hide {
			display: none;
		}
	</style>

{% endblock %}

{% block content %}
	<style>
		.highlight-row {
			background-color: #e0f7fa !important;
			font-weight: bold;
			ont-weight: bold;
		}

		.rowuser {
			cursor: pointer;
		}

		td:nth-child(5) {
			max-width: 150px;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	</style>
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Setting</a>
			</li>
			<li class="breadcrumb-item active">Log Akses</li>
		</ol>
	</nav>
	<div class="mb-1">
		<div class="row g-2 mb-1">
			<div class="col-auto">
				<h2 class="mb-0">Log Akses</h2>
			</div>
		</div>

		<div class="row">
			<div class="col-12 col-xl-4 order-1 order-xl-0">
				<div class="card border my-5">
					<div class="card-header border-bottom">
						<h4>
							<span class="far fa-user"></span>
							User
						</h4>
					</div>
					<div class="card-body">
						<div class="table-responsive scrollbar-overlay mx-n1 px-1">
							<table class="table table-sm fs-9 mb-0 table-bordered table-hover" id="datatables-logakses-user">
								<thead>
									<tr
										class="p-2 text-center">
										<!-- Mengurangi padding -->
										<th class="sort px-2" scope="col">
											NO
										</th>
										<th class="sort px-2" scope="col">
											Nama
										</th>
										<th class="sort px-2" scope="col">
											ROLE / HAK AKSES
										</th>
									</tr>
								</thead>
								<tbody
									class="list" id="user-logakses-table-body"><!-- Data akan diisi dari script -->
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-xl-8">
				<div class="card border my-5">
					<div class="card-header border-bottom">
						<h4>
							<span class="far fa-user"></span>
							<span id="fullname">
								User
							</span>
						</h4>
					</div>
					<div class="card-body">
						<div class="mb-2">
							<label class="form-label" for="exampleTextarea">Keterangan
							</label>
							<textarea class="form-control" id="filter_keterangan" name="filter_keterangan" rows="1" placeholder="Keterangan"></textarea>
						</div>
						<div class="input-group row mb-2">
							<div class="col-12 col-xl-6">
								<div class="flatpickr-input-container">
									<div class="form-floating">
										<input class="form-control" name="filter_tanggal_awal" id="filter_tanggal_awal" type="text" placeholder="Tanggal Awal" data-options="{'disableMobile':true}">
										<label class="ps-6" for="tgl_penilaian">Tanggal Awal</label>
										<span class="uil uil-calendar-alt flatpickr-icon text-body-tertiary"></span>
									</div>
								</div>
							</div>
							<div class="col-12 col-xl-6">
								<div class="flatpickr-input-container">
									<div class="form-floating">
										<input class="form-control" name="filter_tanggal_akhir" id="filter_tanggal_akhir" type="text" placeholder="Tanggal Akhir" data-options="{'disableMobile':true}">
										<label class="ps-6" for="tgl_penilaian">Tanggal Akhir</label>
										<span class="uil uil-calendar-alt flatpickr-icon text-body-tertiary"></span>
									</div>
								</div>
							</div>
						</div>

						<div class="d-flex justify-content-end gap-2 mb-3">
							<button class="btn btn-sm btn-outline-primary my-1" id="btn-filter">
								<span class="fas fa-search me-2"></span>Filter
							</button>
							<button class="btn btn-sm btn-success my-1" id="btn-reset">
								<span class="fas fa-sync me-2"></span>Perbarui
							</button>
						</div>										

						<div class="table-responsive scrollbar-overlay mx-n1 px-1">
							<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-logakses">
								<thead>
									<tr
										class="p-2 text-center">
										<!-- Mengurangi padding -->
										<th class="sort px-2" scope="col">
											#
										</th>
										<th class="sort px-2" scope="col">
											NAMA
										</th>
										<th class="sort px-2" scope="col">
											WAKTU
										</th>
										<th class="sort px-2" scope="col">
											KETERANGAN
										</th>
										<th class="sort px-2" scope="col">
											PARAMETER
										</th>
										<th class="sort px-2" scope="col">
											STATUS
										</th>
									</tr>
								</thead>
								<tbody
									class="list" id="logakses-table-body"><!-- Data akan diisi dari script -->
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form class="form-horizontal" style="display: none;" id="form-filter">
		<input type="text" name="id_user" id="id_user" value="{{ session.user['id'] }}">
		<input type="text" name="filter_tanggal_awal_temp" id="filter_tanggal_awal_temp">
		<input type="text" name="filter_tanggal_akhir_temp" id="filter_tanggal_akhir_temp">
		<input type="text" name="filter_keterangan_temp" id="filter_keterangan_temp">
	</form>

	{{ flash.output() }}

{% endblock %}

{% block inline_script %}
	{% include "Defaults/Setting/LogAkses/index.js" %}
{% endblock %}
