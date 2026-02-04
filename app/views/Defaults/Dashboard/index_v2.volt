{% extends 'template/base.volt' %}
{% block title %}
	Dashboard V3
{% endblock %}

{% block inline_style %}{% endblock %}

{% block content %}
	{#
		<div class="d-flex flex-center content-min-h">
			<div class="text-center py-9">
				<img class="img-fluid mb-7 d-dark-none" src="{{ url('external_img/no-data-found.png') }}" width="470" alt=""/><img class="img-fluid mb-7 d-light-none" src="{{ url('external_img/no-data-found-2.png') }}" width="470" alt=""/>
				<h1 class="text-body-secondary fw-normal mb-5">
					Create Something Beautiful.
				</h1>
				<a class="btn btn-lg btn-primary" href="#">Getting Started</a>
			</div>
		</div>
		#}

	<div class="row gy-3 mb-6 justify-content-between">
		<div class="col-md-9 col-auto">
			<h2 class="mb-2 text-body-emphasis">Dashboard</h2>
			<h5 class="text-body-tertiary fw-semibold">Ringkasan Informasi Audit Internal</h5>
		</div>
		<div
			class="col-md-3 col-auto">{#
							<div class="flatpickr-input-container"><input class="form-control ps-6 datetimepicker flatpickr-input" id="datepicker" type="text" data-options="{&quot;dateFormat&quot;:&quot;M j, Y&quot;,&quot;disableMobile&quot;:true,&quot;defaultDate&quot;:&quot;Mar 1, 2022&quot;}" readonly="readonly"><span class="uil uil-calendar-alt flatpickr-icon text-body-tertiary"></span>
							</div>
							#}
		</div>
	</div>


	<div
		class="row gy-3 mb-4 justify-content-between">
		{#
				<div class="col-xxl-6">
					<h2 class="mb-2 text-body-emphasis">CRM Dashboard</h2>
					<h5 class="text-body-tertiary fw-semibold mb-4">Check your business growth in one place</h5>
					<div class="row g-3 mb-3">
						<div class="col-sm-6 col-md-4 col-xl-3 col-xxl-4">
							<div class="card h-100">
								<div class="card-body">
									<div class="d-flex d-sm-block justify-content-between">
										<div class="border-bottom-sm border-translucent mb-sm-4">
											<div class="d-flex align-items-center">
												<div class="d-flex align-items-center icon-wrapper-sm shadow-primary-100" style="transform: rotate(-7.45deg);">
													<span class="fa-solid fa-phone-alt text-primary fs-7 z-1 ms-2"></span>
												</div>
												<p class="text-body-tertiary fs-9 mb-0 ms-2 mt-3">Outgoing call</p>
											</div>
											<p class="text-primary mt-2 fs-6 fw-bold mb-0 mb-sm-4">3
												<span class="fs-8 text-body lh-lg">Leads Today</span>
											</p>
										</div>
										<div class="d-flex flex-column justify-content-center flex-between-end d-sm-block text-end text-sm-start">
											<span class="badge badge-phoenix badge-phoenix-success fs-10 mb-2">+24.5%</span>
											<p class="mb-0 fs-9 text-body-tertiary">Than Yesterday</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-4 col-xl-3 col-xxl-4">
							<div class="card h-100">
								<div class="card-body">
									<div class="d-flex d-sm-block justify-content-between">
										<div class="border-bottom-sm border-translucent mb-sm-4">
											<div class="d-flex align-items-center">
												<div class="d-flex align-items-center icon-wrapper-sm shadow-info-100" style="transform: rotate(-7.45deg);">
													<span class="fa-solid fa-calendar text-info fs-7 z-1 ms-2"></span>
												</div>
												<p class="text-body-tertiary fs-9 mb-0 ms-2 mt-3">Outgoing meeting</p>
											</div>
											<p class="text-info mt-2 fs-6 fw-bold mb-0 mb-sm-4">12
												<span class="fs-8 text-body lh-lg">This Week</span>
											</p>
										</div>
										<div class="d-flex flex-column justify-content-center flex-between-end d-sm-block text-end text-sm-start">
											<span class="badge badge-phoenix badge-phoenix-warning fs-10 mb-2">+24.5%</span>
											<p class="mb-0 fs-9 text-body-tertiary">Than last week</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4 col-xl-6 col-xxl-4 gy-5 gy-md-3">
							<div class="border-bottom border-translucent">
								<h5 class="pb-4 border-bottom border-translucent">Top 5 Lead Sources</h5>
								<ul class="list-group list-group-flush">
									<li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
										<div class="d-flex justify-content-between">
											<span class="fw-normal fs-9 mx-1">
												<span class="fw-bold">1.
												</span>None
											</span>
											<span>(65)</span>
										</div>
									</li>
									<li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
										<div class="d-flex justify-content-between">
											<span class="fw-normal mx-1">
												<span class="fw-bold">2.
												</span>Online Store</span>
											<span>(74)</span>
										</div>
									</li>
									<li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
										<div class="d-flex justify-content-between">
											<span class="fw-normal fs-9 mx-1">
												<span class="fw-bold">3.</span>
												Advertisement</span>
											<span>(32)</span>
										</div>
									</li>
									<li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
										<div class="d-flex justify-content-between">
											<span class="fw-normal fs-9 mx-1">
												<span class="fw-bold">4.</span>
												Seminar Partner</span>
											<span>(25)</span>
										</div>
									</li>
									<li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
										<div class="d-flex justify-content-between">
											<span class="fw-normal fs-9 mx-1">
												<span class="fw-bold">5.</span>
												Partner</span>
											<span>(23)</span>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xxl-6 mb-6">
					<h3>Contacts Created</h3>
					<p class="text-body-tertiary mb-1">Payment received across all channels</p>
					<div class="echart-contacts-created" style="min-height:270px; width:100%"></div>
				</div>
				#}

		<div class="row">
			<!-- Kolom Kiri: Jenis Audit & Risk Assessment -->
			<div class="col-12 col-xxl-6">
				<!-- Jenis Audit -->
				<div class="mb-4">
					<h3>Jenis Audit</h3>
					<p class="text-body-tertiary lh-sm mb-2">Pengelompokan berdasarkan Jenis Audit</p>
					<div id="jenisAuditContainer" class="row g-3"></div>
				</div>
				<hr class="bg-body-secondary mb-5 mt-3"/>
			</div>

			<div class="col-12 col-xxl-6">
				<!-- Jumlah Data -->
				<div class="mb-4">
					<div class="d-flex justify-content-between align-items-start mb-3">
						<div>
							<h3>Jumlah Data</h3>
							<p class="text-body-tertiary lh-sm mb-0">Data yang telah dibuat pada tiap menu</p>
						</div>
						<div class="col-12 col-sm-5">
							<select class="form-select form-select-sm" id="selectJumlahDataPerMenu">
								<option value="PERSIAPAN_AUDIT">Persiapan Audit</option>
								<option value="PELAKSANAAN_AUDIT">Pelaksanaan Audit</option>
								<option value="PELAPORAN_AUDIT">Pelaporan Audit</option>
								<option value="TINDAK_LANJUT_AUDIT">Tindak Lanjut Audit</option>
							</select>
						</div>
					</div>

					<div class="row mb-2">
						<div class="col-sm-7 col-md-8">
							<div
								class="row g-0" id="containerElementJumlahDataPerMenu"><!-- Status cards: Selesai, Draft, Baru -->
							</div>
						</div>
						<div class="col-sm-5 col-md-4">
							<div class="position-relative d-flex flex-center echart-contact-by-source-container">
								<div id="echartJumlahPerMenu" style="min-height:245px;width:100%"></div>
								<div class="position-absolute rounded-circle bg-primary-subtle top-50 start-50 translate-middle d-flex flex-center" style="height:100px; width:100px;">
									<h3 class="mb-0 text-primary-dark fw-bolder" data-label="data-label">11</h3>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12 col-xxl-8">
				<!-- Risk Assessment -->
				<div>
					<h3>Risk Assessment</h3>
					<p class="text-body-tertiary lh-sm mb-5">Pengelompokan berdasarkan Risk Assessment Tahun (2025)</p>
					<div id="riskCardContainer" class="row g-3"></div>
				</div>
			</div>
			<!-- Kolom Kanan: Jumlah Data & Kertas Kerja -->
			<div class="col-12 col-xxl-4">
				<!-- Kertas Kerja Detail -->
				<div>
					<div id="list-kertas-kerja-container" class="gy-5 gy-md-3"></div>
				</div>
			</div>
		</div>

		<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis pt-7 pb-3 border-y">
			<div class="row">
				<div class="col-12 col-xl-7 col-xxl-6">
					<div class="row g-3 mb-3">
						<div class="col-12 col-md-6">
							<h3 class="text-body-emphasis text-nowrap">Program Kerja</h3>
							<p class="text-body-tertiary mb-md-7">Pengelompokan berdasarkan Objek Audit</p>
							<div class="d-flex align-items-center justify-content-between">
								<p class="mb-0 fw-bold">Objek Audit
								</p>
								<p class="mb-0 fs-9">Jumlah
									<span class="fw-bold" id="totalProgramKerjaObjekAudit"></span>
								</p>
							</div>
							<hr class="bg-body-secondary mb-2 mt-2"/>

							<div id="renderElementObjekAuditPerBagian"></div>

							<button class="btn btn-outline-primary mt-5" id="btnLihatProgramKerja">Lihat Detail<span class="fas fa-angle-right ms-2 fs-10 text-center"></span>
							</button>
						</div>
						<div class="col-12 col-md-6">
							<div
								class="position-relative mb-sm-4 mb-xl-0">
								<!-- <div class="echart-issue-chart" style="min-height:390px;width:100%"></div> -->
								<div id="objekAuditChart" style="height:300px;width:100%;margin-bottom:1rem;"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-12 col-xl-5 col-xxl-6">


					<div class="row g-3 mb-3">
						<div class="col-12 col-md-6">
							<h3 class="text-body-emphasis text-nowrap">Temuan Audit</h3>
							<p class="text-body-tertiary mb-md-7">Pengelompokan berdasarkan Objek Audit</p>
							<div class="d-flex align-items-center justify-content-between">
								<p class="mb-0 fw-bold">Temuan Audit
								</p>
								<p class="mb-0 fs-9">Jumlah
									<span class="fw-bold" id="totalTemuanAuditObjekAudit"></span>
								</p>
							</div>
							<hr class="bg-body-secondary mb-2 mt-2"/>

							<div id="renderElementTemuanAuditPerBagian"></div>

							<button class="btn btn-outline-primary mt-5" id="btnLihatTemuan">Lihat Detail<span class="fas fa-angle-right ms-2 fs-10 text-center"></span>
							</button>
						</div>
						<div class="col-12 col-md-6">
							<div
								class="position-relative mb-sm-4 mb-xl-0">
								<!-- <div class="echart-issue-chart" style="min-height:390px;width:100%"></div> -->
								<div id="objekTemuanChart" style="height:300px;width:100%;margin-bottom:1rem;"></div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis pt-6 pb-9 border-top mt-2">
			<div class="row g-6">
				<div class="col-12 col-xl-6">
					<div class="me-xl-4">
						<div>
							<h3>Rencana Biaya</h3>
							<p class="mb-1 text-body-tertiary">Grafik perencanaan biaya tahun berjalan (<?= date('Y') ?>) per jenis biaya</p>
						</div>
						<div id="echartRencanaBiaya" style="height:300px; width:100%"></div>
					</div>
				</div>
				<div class="col-12 col-xl-6">
					<div>
						<h3>Biaya Penugasan</h3>
						<p class="mb-1 text-body-tertiary">Grafik penggunaan biaya untuk penugasan per tahun berjalan (<?= date('Y') ?>)</p>
					</div>
					<div id="echartBiayaPenugasan" style="height:300px;"></div>
				</div>
			</div>
		</div>

	{% endblock %}


	{% block specify_js %}
		<script src="{{ url('vendors') }}/echarts/echarts.min.js"></script>
	{% endblock %}

	{% block inline_script %}
		{% include "Defaults/Dashboard/index.js" %}
	{% endblock %}
