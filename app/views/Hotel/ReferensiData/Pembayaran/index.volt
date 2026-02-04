{% extends 'template/base.volt' %}

{% block title %}
    Referensi Data - Pembayaran
{% endblock %}

{% block inline_style %}
	<style>
		table th,
		table td {
			padding: 6px !important;
		}
		#datatables-pembayaran tbody tr {
			height: 50px;
		}
		#datatables-pembayaran tbody td {
			padding: 12px 10px;
		}
	</style>
{% endblock %}

{% block content %}
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Referensi Data</a>
			</li>
			<li class="breadcrumb-item active">Pembayaran</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Pembayaran</h2>
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
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-pembayaran">
						<thead>
							<tr class="p-2 text-center">
								<th class="sort px-2" scope="col">No</th>
								<th class="sort px-2" scope="col">Kode Booking</th>
								<th class="sort px-2" scope="col">Tamu</th>
								<th class="sort px-2" scope="col">Metode</th>
								<th class="sort px-2" scope="col">Jumlah Bayar</th>
								<th class="sort px-2" scope="col">Tanggal Bayar</th>
								<th class="sort px-2" scope="col">Status</th>
							</tr>
						</thead>
						<tbody class="list" id="pembayaran-table-body">
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
					<h3 class="mb-0">Filter Data Pembayaran</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="form-filter">
								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Pemesanan</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<div class="form-control">
											<select name="search_pemesanan" id="search_pemesanan" class="form-control">
												<option value="">Pilih Pemesanan</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label class="text-body-highlight fw-bold mb-2">Metode Pembayaran</label>
											<div class="input-group">
												<div class="input-group-text">
													<input class="form-check-input toggle-input" type="checkbox"/>
												</div>
												<div class="form-control">
													<select name="search_metode" id="search_metode" class="form-control">
														<option value="">Pilih Metode</option>
														<option value="tunai">Tunai</option>
														<option value="kartu_kredit">Kartu Kredit</option>
														<option value="debit">Debit</option>
														<option value="transfer">Transfer</option>
														<option value="qris">QRIS</option>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="text-body-highlight fw-bold mb-2">Status</label>
											<div class="input-group">
												<div class="input-group-text">
													<input class="form-check-input toggle-input" type="checkbox"/>
												</div>
												<div class="form-control">
													<select name="search_status" id="search_status" class="form-control">
														<option value="">Pilih Status</option>
														<option value="pending">Pending</option>
														<option value="lunas">Lunas</option>
														<option value="gagal">Gagal</option>
														<option value="refund">Refund</option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Tanggal Bayar</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<input class="form-control" type="date" id="search_tanggal" name="search_tanggal"/>
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

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Pemesanan</label>
									<select class="form-control" id="pemesanan_id" name="pemesanan_id">
										<option value="">Pilih Pemesanan</option>
									</select>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Metode Pembayaran</label>
											<select class="form-control" id="metode_pembayaran" name="metode_pembayaran">
												<option value="">Pilih Metode</option>
												<option value="tunai">Tunai</option>
												<option value="kartu_kredit">Kartu Kredit</option>
												<option value="debit">Debit</option>
												<option value="transfer">Transfer</option>
												<option value="qris">QRIS</option>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Jumlah Bayar</label>
											<input class="form-control" type="number" id="jumlah_bayar" name="jumlah_bayar"/>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Tanggal Bayar</label>
											<input class="form-control" type="datetime-local" id="tanggal_bayar" name="tanggal_bayar"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-2">
											<label class="text-body-highlight fw-bold mb-2">Status</label>
											<select class="form-control" id="status" name="status">
												<option value="">Pilih Status</option>
												<option value="pending">Pending</option>
												<option value="lunas">Lunas</option>
												<option value="gagal">Gagal</option>
												<option value="refund">Refund</option>
											</select>
											<small class="text-muted">Status akan otomatis terisi berdasarkan jumlah bayar</small>
										</div>
									</div>
								</div>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Keterangan</label>
									<textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
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
	{% include "Hotel/ReferensiData/Pembayaran/index.js" %}
{% endblock %}