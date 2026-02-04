{% extends 'template/base.volt' %}
{% block title %}
	Dashboard V3
{% endblock %}

{% block inject_style %}

	<style>
		.bg-gradient-cardAccis {
			background: linear-gradient(90deg,var(--c1, #111),var(--c2,#555)) !important;
		}

		.card.chart-soft {
			border: 0;
			border-radius: 16px;
			background: radial-gradient(120px 80px at 95% 0%, rgba(102, 156, 245, 0.22), rgba(102, 156, 245, 0) 60%), #fff;
		}

		/* Fixed height scroll area to keep cards aligned */
		.table-scroll--fixed {
			height: 280px;
			overflow-y: auto;
			overflow-x: auto;
		}
	</style>
{% endblock %}

{% block content %}

	<!-- 
		<div class="d-flex flex-center content-min-h">
			<div class="text-center py-9">
				<img class="img-fluid mb-7 d-dark-none" src="{{ url('external_img/no-data-found.png') }}" width="470" alt=""/><img class="img-fluid mb-7 d-light-none" src="{{ url('external_img/no-data-found-2.png') }}" width="470" alt=""/>
				<h1 class="text-body-secondary fw-normal mb-5">
					Create Something Beautiful.
				</h1>
				<a class="btn btn-lg btn-primary" href="#">Getting Started</a>
			</div>
		</div>
	-->

	<div
		class="container-fluid py-2">
		<!-- ROW 1 -->
		<div
			class="row">
			<!-- tambahkan class chart-soft ke card -->
			<div class="col-9">
				<div class="row">
					<div style="" class="col-md-7 col-xs-12">
						<div class="card border-0 rounded-4 overflow-hidden shadow-sm mb-4 chart-soft">
							<div class="card-body p-2 p-lg-3">
								<div
									class="row g-2 align-items-center">
									<!-- Kolom kiri -->
									<div class="col-12 col-lg-8" style="padding-left: 8px;">
										<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
											<h5 class="mb-0">Data Voucher</h5>
											<span class="badge rounded-pill bg-primary-subtle text-primary">
												Periode:
												<span class="fw-semibold ms-1">Agustus 2025</span>
											</span>
										</div>

										<div class="d-flex flex-wrap align-items-end gap-4">
											<div>
												<div class="text-secondary small mb-1">Total Voucher</div>
												<div class="display-4 fw-bold lh-1" id="totalVoucher">1.763</div>
											</div>

											<div class="vr d-none d-md-block"></div>

											<div>
												<div class="text-secondary small mb-1">Status Verifikasi</div>
												<div class="d-flex flex-wrap gap-2">
													<span class="badge bg-success-subtle text-success">
														<i class="fa-solid fa-check me-1"></i>
														Sudah:
														<span id="sudahText" class="fw-semibold">1.691</span>
													</span>
													<span class="badge bg-warning-subtle text-warning">
														<i class="fa-solid fa-hourglass-half me-1"></i>
														Belum:
														<span id="belumText" class="fw-semibold">72</span>
													</span>
												</div>
											</div>
										</div>

										<!-- Progress -->
										<div class="mt-4">
											<div class="d-flex justify-content-between small mb-1">
												<span class="text-secondary">Progress Verifikasi</span>
												<span class="fw-semibold">
													<span id="percentText">0</span>%</span>
											</div>
											<div class="progress" style="height:10px;">
												<div id="barVerif" class="progress-bar bg-success" role="progressbar" style="width:0%"></div>
											</div>
										</div>

										<!-- Ringkasan -->
										<div class="d-flex flex-wrap gap-3 mt-4">
											<div class="d-flex align-items-center gap-2">
												<span class="rounded-3 p-2 bg-primary-subtle text-primary">
													<i class="fa-solid fa-ticket"></i>
												</span>
												<span class="small text-secondary">Terdata
													<strong id="terdataText">1.763</strong>
													tiket</span>
											</div>
											<div class="d-flex align-items-center gap-2">
												<span class="rounded-3 p-2 bg-secondary-subtle text-secondary">
													<i class="fa-solid fa-database"></i>
												</span>
												<span class="small text-secondary">Sumber: Sistem Voucher</span>
											</div>
										</div>
									</div>

									<!-- Kolom kanan -->
									<div class="col-12 col-lg-4">
										<div class="d-grid gap-3">
											<div class="card border-0 rounded-4 shadow-sm">
												<div class="card-body d-flex align-items-center justify-content-between">
													<div>
														<div class="text-secondary small mb-1">Terbayar</div>
														<div class="h4 fw-bold mb-0" id="terbayarText">1.674</div>
														<div class="small text-secondary">Dari
															<span class="fw-semibold" id="dariText">1.691</span>
															Voucher</div>
													</div>
													<span class="rounded-3 p-3 bg-success-subtle text-success">
														<i class="fa-solid fa-money-bill-wave fa-lg"></i>
													</span>
												</div>
											</div>

											<div class="card border-0 rounded-4 shadow-sm">
												<div class="card-body d-flex align-items-center justify-content-between">
													<div>
														<div class="text-secondary small mb-1">Sisa Voucher</div>
														<div class="h2 fw-bold mb-0" id="sisaText">17</div>
														<div class="small text-secondary">Belum dibayar</div>
													</div>
													<span class="rounded-3 p-3 bg-dark-subtle text-dark">
														<i class="fa-solid fa-receipt fa-lg"></i>
													</span>
												</div>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5 col-xs-12">
						<div class="card border-0 shadow-sm rounded-4 mb-4 chart-soft">
							<div class="card-body p-4">
								<div class="row g-4">
									<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
										<h5 class="mb-0">Laba Rugi</h5>
										<span class="badge rounded-pill bg-primary-subtle text-primary">
											Periode:
											<span class="fw-semibold ms-1">{{ numToBulan(session.m_periode) }}
												{{ session.y_periode }}</span>
										</span>
									</div>

									<!-- BULAN INI -->
									<div class="col-12 col-lg-6">
										<div class="d-flex align-items-center gap-2 mb-2">
											<i class="fa-solid fa-chart-line text-danger"></i>
											<h6 class="mb-0">Bulan Ini ({{ numToBulan(session.m_periode) }}
												{{ session.y_periode }})</h6>
										</div>
										<div class="ms-4">
											<div class="text-secondary small">Pendapatan</div>
											<div class="fw-bold fs-9" id="lbl_bulan_ini_pendapatan">Rp. 0</div>
											<div class="text-secondary small mt-2">Biaya</div>
											<div class="fw-bold fs-9" id="lbl_bulan_ini_biaya">Rp. 0</div>
										</div>

										<!-- Highlight rugi + ICON -->
										<div id="containerBulanIniSelisih" class="fs-9 alert bg-secondary text-white fw-bold text-center rounded-3 mt-3 mb-0 p-2 d-flex align-items-center justify-content-center gap-2">
											<i id="lbl_icon_bulan_ini_selisih" class="fa-solid fas fa-question"></i>
											<span id="lbl_bulan_ini_selisih">RUGI: 0</span>
										</div>
									</div>

									<!-- S.D BULAN INI -->
									<div class="col-12 col-lg-6">
										<div class="d-flex align-items-center gap-2 mb-2">
											<i class="fa-solid fa-arrow-trend-up text-success"></i>
											<h6 class="mb-0 text-nowrap" style="font-size: 0.75rem !important;">S.D Bulan Ini ({{ numToBulan(session.m_periode) }}
												{{ session.y_periode }})</h6>
										</div>
										<div class="ms-4">
											<div class="text-secondary small">Pendapatan</div>
											<div class="fw-bold fs-9" id="lbl_sd_bulan_ini_pendapatan">Rp. 0</div>
											<div class="text-secondary small mt-2">Biaya</div>
											<div class="fw-bold fs-9" id="lbl_sd_bulan_ini_biaya">Rp. 0</div>
										</div>

										<!-- Highlight laba + ICON -->
										<div id="containerSdBulanIniSelisih" class="fs-9 alert bg-secondary text-white fw-bold text-center rounded-3 mt-3 mb-0 p-2 d-flex align-items-center justify-content-center gap-2">
											<i id="lbl_icon_sd_bulan_ini_selisih" class="fa-solid fa-question"></i>
											<span id="lbl_sd_bulan_ini_selisih">LABA: 0</span>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div style="" class="col-8">
						<div class="card border-0 rounded-4 shadow-sm chart-soft">
							<div class="card-body p-3 p-lg-4">
								<div class="row g-3">
									<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
										<h5 class="mb-0">Grafik</h5>
										<span class="badge rounded-pill bg-primary-subtle text-primary">
											Periode:
											<span class="fw-semibold ms-1">{{ numToBulan(session.m_periode) }}
												{{ session.y_periode }}</span>
										</span>
									</div>
									<!-- Grafik 1: Posisi Laba (Rugi) -->

									<div class="col-12">
										<ul class="nav nav-underline fs-9" id="tabGrafik1" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" id="labarugigrafik-tab" data-bs-toggle="tab" href="#tab-labarugigrafik" role="tab" aria-controls="tab-labarugigrafik" aria-selected="true">Laba Rugi</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" id="realisasibiayapendapatan-tab" data-bs-toggle="tab" href="#tab-realisasibiayapendapatan" role="tab" aria-controls="tab-realisasibiayapendapatan" aria-selected="false">Realisasi Biaya, Pendapatan dan Investasi</a>
											</li>
										</ul>
									</div>
									<div class="tab-content mt-3" id="tabGrafik1Content">
										<div class="tab-pane fade show active" id="tab-labarugigrafik" role="tabpanel" aria-labelledby="labarugigrafik-tab">
											<div class="row">
												<div class="col-12">
													<div class="card border-0 rounded-4 shadow-sm">
														<div class="card-header bg-transparent border-0 pb-0">
															<div class="fw-semibold">Statistik Posisi Laba (Rugi)</div>
														</div>
														<div class="card-body pt-2">
															<div id="plChart" style="height:340px;"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane fade" id="tab-realisasibiayapendapatan" role="tabpanel" aria-labelledby="realisasibiayapendapatan-tab">
											<div class="col-12">
												<div class="card border-0 rounded-4 shadow-sm">
													<div class="card-header bg-transparent border-0 pb-0">
														<div class="fw-semibold">Statistik Realisasi Biaya dan Pendapatan</div>
													</div>
													<div class="card-body pt-2">
														<div id="rbChart" style="height:360px;"></div>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
					<div style="" class="col-4">
						<div class="card border-0 rounded-4 shadow-sm chart-soft">
							<div class="card-body p-2">
								<div class="card-body pt-2">
									<div class="row g-3">
										<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
											<h5 class="mb-0">Info Tambahan</h5>
											<span class="badge rounded-pill bg-primary-subtle text-primary">
												Periode:
												<span class="fw-semibold ms-1">{{ numToBulan(session.m_periode) }}
													{{ session.y_periode }}</span>
											</span>
										</div>


										<div class="col-12">
											<ul class="nav nav-underline fs-9" id="tabInfoLainnya1" role="tablist">
												<li class="nav-item">
													<a class="nav-link active" id="sinkrondata-tab" data-bs-toggle="tab" href="#tab-sinkrondata" role="tab" aria-controls="tab-sinkrondata" aria-selected="true">Info Sinkron Data</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" id="anomalidata-tab" data-bs-toggle="tab" href="#tab-anomalidata" role="tab" aria-controls="tab-anomalidata" aria-selected="false">Info Anomali Data</a>
												</li>
											</ul>
										</div>
										<div class="tab-content mt-3" id="tabInfoLainnya1Content">
											<div class="tab-pane fade show active" id="tab-sinkrondata" role="tabpanel" aria-labelledby="sinkrondata-tab">
												<div class="row">
													<div class="col">
														<div class="table-responsive table-scroll table-scroll--fixed">
															<table class="table table-sm align-middle mb-0 soft-table soft-table--compact">
																<thead>
																	<tr>
																		<th class="text-uppercase small text-muted">#</th>
																		<th class="text-uppercase small text-muted">Nama</th>
																		<th class="text-uppercase small text-muted text-center">
																			Selesai / Total
																		</th>
																	</tr>
																</thead>
																<tbody id="tb-anomali-belum">
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			1.
																		</td>
																		<td class="text-left fs-9">
																			Progress terjurnal untuk barang masuk
																		</td>
																		<td class="text-end">0/0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			2.
																		</td>
																		<td class="text-left fs-9">
																			Progress terjurnal untuk barang keluar
																		</td>
																		<td class="text-end">0/0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			3.
																		</td>
																		<td class="text-left fs-9">
																			Progress Sisa ATDP yg sudah di proses BAST cabang
																		</td>
																		<td class="text-end">0/0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			4.
																		</td>
																		<td class="text-left fs-9">
																			Progress BAST yang sudah di AKTIVA
																		</td>
																		<td class="text-end">0/0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			5.
																		</td>
																		<td class="text-left fs-9">
																			Progress AKTIVA yang sudah terjunal
																		</td>
																		<td class="text-end">0/0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			6.
																		</td>
																		<td colspan="2" class="text-left fs-9">
																			Daftar Stok Barang
																		</td>
																	</tr>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
											<div class="tab-pane fade" id="tab-anomalidata" role="tabpanel" aria-labelledby="anomalidata-tab">
												<div class="row">
													<div class="col">
														<div class="table-responsive table-scroll table-scroll--fixed">
															<table class="table table-sm align-middle mb-0 soft-table soft-table--compact">
																<thead>
																	<tr>
																		<th class="text-uppercase small text-muted">#</th>
																		<th class="text-uppercase small text-muted">Jenis</th>
																		<th class="text-uppercase small text-muted text-center">
																			Jumlah
																		</th>
																	</tr>
																</thead>
																<tbody id="tb-anomali-belum">
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9 text-danger">
																			1.
																		</td>
																		<td class="text-left fs-9 text-danger">
																			Ada kelompok kode belum terdaftar di arus kas tidak langsung
																		</td>
																		<td class="text-center text-danger">0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			2.
																		</td>
																		<td class="text-left fs-9">
																			Ada salah jrna tapi is air =1s
																		</td>
																		<td class="text-center">0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			3.
																		</td>
																		<td class="text-left fs-9">
																			Ada salah jurnal di tahun 2024
																		</td>
																		<td class="text-center">0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			4.
																		</td>
																		<td class="text-left fs-9">
																			Ada kode saketap biaya pendapatan yg belum terdaftar
																		</td>
																		<td class="text-center">0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			5.
																		</td>
																		<td class="text-left fs-9">
																			Ada kode akun yg aneh di djournal
																		</td>
																		<td class="text-center">0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			6.
																		</td>
																		<td class="text-left fs-9">
																			Ada JBK_ID yg tidak sesuai dengan mjo _id
																		</td>
																		<td class="text-center">0</td>
																	</tr>
																	<tr class="row-compact">
																		<td class="text-start fw-semibold fs-9">
																			7.
																		</td>
																		<td class="text-left fs-9">
																			Kemungkinan salah tanggal, karena mjo_code dan tanggal beda
																		</td>
																		<td class="text-center">0</td>
																	</tr>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-3">
				<div style="" class="col-md-12 col-xs-12">
					<div class="card border-0 rounded-4 shadow-sm chart-soft">
						<div class="card-body p-3">
							<div class="row g-2 text-center">
								<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
									<h5 class="mb-0">Cash Flow</h5>
									<span class="badge rounded-pill bg-primary-subtle text-primary">
										Periode:
										<span class="fw-semibold ms-1">{{ numToBulan(session.m_periode) }}
											{{ session.y_periode }}</span>
									</span>
								</div>
								<!-- Saldo Awal -->
								<div class="col-12">
									<div class="p-3 rounded-3 bg-info-subtle">
										<div class="d-flex flex-column align-items-center">
											<i class="fa-solid fa-wallet text-info mb-1"></i>
											<div class="small text-secondary fw-bold">Saldo Awal</div>
											<div class="fw-semibold fs-9" id="lbl_cash_flow_saldo_awal">Rp. 0</div>
										</div>
									</div>
								</div>
								<!-- Penerimaan -->
								<div class="col-6">
									<div class="p-3 rounded-3 bg-primary-subtle">
										<div class="d-flex flex-column align-items-center">
											<i class="fa-solid fa-circle-down text-primary mb-1"></i>
											<div class="small text-secondary fw-bold">Penerimaan</div>
											<div class="fw-semibold fs-9" id="lbl_cash_flow_penerimaan">Rp. 0</div>
										</div>
									</div>
								</div>
								<!-- Pengeluaran -->
								<div class="col-6">
									<div class="p-3 rounded-3 bg-secondary-subtle">
										<div class="d-flex flex-column align-items-center">
											<i class="fa-solid fa-circle-up text-secondary mb-1"></i>
											<div class="small text-secondary fw-bold">Pengeluaran</div>
											<div class="fw-semibold fs-9" id="lbl_cash_flow_pengeluaran">Rp. 0</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="card-footer border-0 rounded-bottom-4 bg-success-subtle text-center py-3">
							<div class="d-flex flex-column align-items-center">
								<i class="fa-solid fa-sack-dollar text-success mb-1"></i>
								<div class="small text-secondary fw-bold">Saldo Akhir</div>
								<div class="fw-bold fs-9 text-success" id="lbl_cash_flow_saldo_akhir">Rp. 0</div>
							</div>
						</div>

					</div>
				</div>

				<div style="" class="col-md-12 col-xs-12 mt-4">
					<div class="card border-0 rounded-4 shadow-sm chart-soft">
						<div class="card-body p-2">
							<div class="card-body pt-2">
								<div class="row g-3">
									<div class="d-flex flex-wrap align-items-center gap-3 mb-2">
										<h5 class="mb-0">Realisasi Anggaran Pendapatan dan Biaya</h5>
										<span class="badge rounded-pill bg-primary-subtle text-primary">
											Periode:
											<span class="fw-semibold ms-1">{{ numToBulan(session.m_periode) }} {{ session.y_periode }}</span>
										</span>
									</div>
									<div class="col-12">

										<div class="col-12">
											<ul class="nav nav-underline fs-9" id="tabRealisasiAnggaran1" role="tablist">
												<li class="nav-item">
													<a class="nav-link active" id="pendapatan-tab" data-bs-toggle="tab" href="#tab-pendapatan" role="tab" aria-controls="tab-pendapatan" aria-selected="true">Pendapatan</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" id="biaya-tab" data-bs-toggle="tab" href="#tab-biaya" role="tab" aria-controls="tab-biaya" aria-selected="false">Biaya</a>
												</li>
											</ul>
										</div>
										<div class="tab-content mt-3" id="tabRealisasiAnggaran1Content">
											<div class="tab-pane fade show active" id="tab-pendapatan" role="tabpanel" aria-labelledby="pendapatan-tab">
												<div class="row">
													<div class="col-12">
														<div class="table-responsive table-scroll table-scroll--fixed">
															<table class="table table-sm align-middle mb-0 soft-table soft-table--compact">
																<thead>
																	<tr>
																		<th class="text-uppercase small text-muted">#</th>
																		<th class="text-uppercase small text-muted"></th>
																		<th class="text-uppercase small text-muted text-center">
																			Anggaran
																		</th>
																		<th class="text-uppercase small text-muted text-center">
																			Realisasi
																		</th>
																		<th class="text-uppercase small text-muted text-center">
																			%
																		</th>
																	</tr>
																</thead>
																<tbody id="tb-anggaran-pendapatan"></tbody>
																<tfoot>
																	<tr class="row-compact fs-9 fw-semibold">
																		<td class="text-center fs-9">Total</td>
																		<td class="text-left fs-9"></td>
																		<td class="text-end"><span id="total-anggaran-pendapatan">0</span></td>
																		<td class="text-end"><span id="total-realisasi-pendapatan">0</span></td>
																		<td class="text-center"><span id="total-persentase-pendapatan">0.00%</span></td>
																	</tr>
																</tfoot>
															</table>
														</div>
													</div>
												</div>
											</div>
											<div class="tab-pane fade" id="tab-biaya" role="tabpanel" aria-labelledby="biaya-tab">
												<div class="row">
													<div class="col-12">
														<div class="table-responsive table-scroll table-scroll--fixed">
															<table class="table table-sm align-middle mb-0 soft-table soft-table--compact">
																<thead>
																	<tr>
																		<th class="text-uppercase small text-muted">#</th>
																		<th class="text-uppercase small text-muted"></th>
																		<th class="text-uppercase small text-muted text-center">
																			Anggaran
																		</th>
																		<th class="text-uppercase small text-muted text-center">
																			Realisasi
																		</th>
																		<th class="text-uppercase small text-muted text-center">
																			%
																		</th>
																	</tr>
																</thead>
																<tbody id="tb-anggaran-biaya"></tbody>
																<tfoot>
																	<tr class="row-compact fs-9 fw-semibold">
																		<td class="text-center fs-9">Total</td>
																		<td class="text-left fs-9"></td>
																		<td class="text-end"><span id="total-anggaran-biaya">0</span></td>
																		<td class="text-end"><span id="total-realisasi-biaya">0</span></td>
																		<td class="text-center"><span id="total-persentase-biaya">0.00%</span></td>
																	</tr>
																</tfoot>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>

	<!-- FontAwesome trigger element -->
	<div class="fa-icon-wait" style="display: none;"></div>

{% endblock %}


{% block specify_js %}
	<script src="{{ url('vendors') }}/echarts/echarts.min.js"></script>
{% endblock %}

{% block inline_script %}
	{% include "Defaults/Dashboard/index.js" %}
{% endblock %}
