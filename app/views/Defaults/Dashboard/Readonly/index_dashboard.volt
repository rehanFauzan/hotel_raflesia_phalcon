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

	<div class="container-fluid py-2">
		<!-- ROW 1 -->
		<div
			class="row">
			<!-- tambahkan class chart-soft ke card -->
			<div class="col-12">
				<div class="card shadow-sm border-0 mb-4" style="min-height: 200px;">
					<div class="card-body d-flex flex-column justify-content-center align-items-center">
						<div class="row w-100">
							<div class="col-12 text-center">
								<img src="{{ url('external_img/logo-pdam-13.png') }}" alt="Aurora Logo" style="max-width: 120px; margin-bottom: 16px;">
							</div>
							<div class="col-12 text-center">
								<h3 class="fw-semibold mb-1" style="color: #428ec9;">
									Selamat Datang,
									<span style="color: #222">{{ session.user['nama'] }}</span>
								</h3>
								<p class="mb-0 text-muted" style="font-size: 14px;">
									<strong>Hak Anda : {{ session.user['role_nama'] }}</strong>
									<br>
								</p>
								<p class="mb-0 text-muted" style="font-size: 13px;">
									<strong>Periode Login : {{ session.user['periode'] }}</strong>
									<br>
								</p>
								<!-- Tanggal dan Jam -->
								<div class="mt-3">
									<div id="current-date" class="fw-bold text-primary mb-1" style="font-size: 16px;"></div>
									<div id="current-time" class="fw-bold text-success" style="font-size: 18px;"></div>
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
	{% include "Defaults/Dashboard/Admin/index_dashboard.js" %}
{% endblock %}
