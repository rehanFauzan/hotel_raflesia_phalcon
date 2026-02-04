{% extends 'template/base.volt' %}

{% block title %}
    Laporan - Pemesanan Kamar Dalam Satu Bulan
{% endblock %}

{% block inline_style %}
	<style>
		table th,
		table td {
			padding: 6px !important;
		}
		#datatables-pemesanan-bulanan tbody tr {
			height: 50px;
		}
		#datatables-pemesanan-bulanan tbody td {
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
			<li class="breadcrumb-item active">Pemesanan Kamar Dalam Satu Bulan</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Pemesanan Kamar Dalam Satu Bulan</h2>
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
						<button class="btn btn-sm btn-danger my-1" id="btn-export-pdf">
							<span class="fas fa-file-pdf me-2"></span>Export PDF
						</button>
					</div>
				</div>
			</div>
			<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
				<div class="table-responsive scrollbar-overlay mx-n1 px-1">
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-pemesanan-bulanan">
						<thead>
							<tr class="p-2 text-center">
								<th class="sort px-2" scope="col">No</th>
								<th class="sort px-2" scope="col">Kode Booking</th>
								<th class="sort px-2" scope="col">Tamu</th>
								<th class="sort px-2" scope="col">Kamar</th>
								<th class="sort px-2" scope="col">Check In</th>
								<th class="sort px-2" scope="col">Check Out</th>
								<th class="sort px-2" scope="col">Malam</th>
								<th class="sort px-2" scope="col">Total Harga</th>
								<th class="sort px-2" scope="col">Status</th>
							</tr>
						</thead>
						<tbody class="list" id="pemesanan-bulanan-table-body">
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
					<h3 class="mb-0">Filter Laporan Pemesanan Bulanan</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="form-filter">
								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Bulan dan Tahun</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<input class="form-control" type="month" id="search_bulan" name="search_bulan"/>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label class="text-body-highlight fw-bold mb-2">Tanggal Mulai</label>
											<div class="input-group">
												<div class="input-group-text">
													<input class="form-check-input toggle-input" type="checkbox"/>
												</div>
												<input class="form-control" type="date" id="search_tanggal_mulai" name="search_tanggal_mulai"/>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="text-body-highlight fw-bold mb-2">Tanggal Selesai</label>
											<div class="input-group">
												<div class="input-group-text">
													<input class="form-check-input toggle-input" type="checkbox"/>
												</div>
												<input class="form-control" type="date" id="search_tanggal_selesai" name="search_tanggal_selesai"/>
											</div>
										</div>
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
												<option value="">Semua Status</option>
												<option value="menunggu">Menunggu</option>
												<option value="dikonfirmasi">Dikonfirmasi</option>
												<option value="checkin">Check In</option>
												<option value="checkout">Check Out</option>
												<option value="dibatalkan">Dibatalkan</option>
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
	{% include "Hotel/Laporan/PemesananBulanan/index.js" %}
{% endblock %}