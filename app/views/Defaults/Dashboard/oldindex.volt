{% extends 'template/dashboard.volt' %}

{% block title %}
	Dashboard
{% endblock %}

{% block content %}
	<style>
		input.ace-switch.ace-switch-onoff::before {
			padding-right: 0.5rem;
			content: "TIDAK";
		}

		input.ace-switch.ace-switch-onoff:checked::before {
			content: "YA";
		}

		input.ace-switch.input-lg {
			width: 5rem;
		}

		.small-card {
			/* Blue 1 */

			background: #2F80ED;
			/* Gray 5 */

			border: 1px solid #E0E0E0;
			box-sizing: border-box;
			border-radius: 15px;
			margin-top: 10px;
		}

		.small-card-secondary {
			/* Blue 1 */
			background: #29B6F6;
			/* Gray 5 */
			border: 1px solid #E0E0E0;
			box-sizing: border-box;
			border-radius: 15px;
			margin-top: 10px;
		}

		.title-text {
			font-style: normal;
			font-weight: 600;
			font-size: 20px;
			line-height: 25px;
			color: #FFFFFF;
		}

		.desc-text {
			font-style: normal;
			font-weight: 600;
			font-size: 12px;
			line-height: 15px;
			color: #FFFFFF;
		}

		.foot-text {
			font-style: normal;
			font-weight: bold;
			font-size: 12px;
			line-height: 15px;
			color: #FFFFFF;
		}

		.count-desc-text {
			font-style: normal;
			font-weight: 600;
			font-size: 16px;
			line-height: 60px;
			color: #41464D;
		}

		.count-text {
			font-style: normal;
			font-weight: 600;
			font-size: 27px;
			line-height: 60px;
			color: #41464D;
		}

		/* .easy-pie-chart,
		.easyPieChart {
			position: relative;
			text-align: center;
		}

		.percentage {
			font-size: 14px;
			display: inline-block;
			vertical-align: top;
		} */
	</style>

	<div class="page-content container-fluid container-plus">
		<div class="row px-3">
			<div class="col-12 p-0">
				<div class="ccard d-flex flex-column mx-1 mb-4 px-2 py-1">
					<div class="flex-grow-1 mb-1 ml-3">
						<div class="row">
							<div class="col-10">
								<div class="text-nowrap text-140 font-bold text-dark-l2 mt-2">
									Dashboard -
									{{ config.appName }}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-4 pos-rel mt-3 mt-sm-0 pt-0 pt-sm-0">
				<div class="card ccard" id="card-4">
					<div class="card-header card-header-lg">
						<h5 class="card-title text-130 text-dark-m3">
							<i class="fa fa-sort-amount-up-alt"></i>
							Efisiensi Penerimaan
						</h5>

						<div class="card-toolbar ">
							<a href="#" data-action="reload" class="card-toolbar-btn btn btn-sm radius-round btn-green">
								<i class="fas fa-sync-alt w-2 mx-1px"></i>
							</a>

							<a href="#" data-action="toggle" class="card-toolbar-btn btn btn-sm radius-round btn-grey">
								<i class="fa fa-chevron-up w-2 mx-1px"></i>
							</a>
						</div>
					</div>
					<!-- /.card-header -->

					<div class="card-body p-0">
						<div class="p-3">
							<div id="pieChart"></div>
						</div>
					</div>
					<!-- /.card-body -->
				</div>
			</div>
			<div class="col-8 pos-rel mt-3 mt-sm-0 pt-0 pt-sm-0">
				<div class="row">
					<div class="col-12 pos-rel mt-3 mt-sm-0 pt-0 pt-sm-0">
						<div class="card ccard" id="card-4">
							<div class="card-header card-header-lg">
								<h5 class="card-title text-130 text-dark-m3">
									<i class="fa fa-book"></i>
									Penerimaan Via Loket
								</h5>

								<div class="card-toolbar ">
									<a href="#" data-action="reload" class="card-toolbar-btn btn btn-sm radius-round btn-green">
										<i class="fas fa-sync-alt w-2 mx-1px"></i>
									</a>

									<a href="#" data-action="toggle" class="card-toolbar-btn btn btn-sm radius-round btn-grey">
										<i class="fa fa-chevron-up w-2 mx-1px"></i>
									</a>
								</div>
							</div>
							<!-- /.card-header -->

							<div class="card-body p-0">
								<div class="p-3">
									<div class="row">
										<div class="col-5 mb-4 pos-rel mt-3 mt-sm-0 pt-0 pt-sm-0">
											<div class="ccard h-100 pt-2 pb-25 px-25 d-flex overflow-hidden">
												<div class="position-br	mb-n5 mr-n5 radius-round bgc-blue-l3 opacity-3" style="width: 5.25rem; height: 5.25rem;"></div>
												<div class="position-br	mb-n5 mr-n5 radius-round bgc-blue-l2 opacity-5" style="width: 4.75rem; height: 4.75rem;"></div>
												<div class="position-br	mb-n5 mr-n5 radius-round bgc-blue-l1 opacity-5" style="width: 4.25rem; height: 4.25rem;"></div>
												<div class="flex-grow-1 pl-25 pos-rel d-flex flex-column">
													<div class="text-secondary-d4 mb-2">
														<span class="text-180 text-success" id="invoice_not_confirmed">Rp. 11.710.583.754</span>
													</div>
													<div class="mt-auto text-nowrap font-bolder text-secondary-d2 text-105 letter-spacing mt-n1">
														Bulan Ini
													</div>
												</div>
												<div class="ml-auto pr-1 align-self-center pos-rel text-125">
													<i class="fa fas fa-hand-holding-usd text-green opacity-1 fa-2x mr-25"></i>
												</div>
											</div>
										</div>
										<div class="col-3 mb-4 pos-rel mt-3 mt-sm-0 pt-0 pt-sm-0">
											<div class="ccard h-100 pt-2 pb-25 px-25 d-flex overflow-hidden">
												<div class="position-br	mb-n5 mr-n5 radius-round bgc-blue-l3 opacity-3" style="width: 5.25rem; height: 5.25rem;"></div>
												<div class="position-br	mb-n5 mr-n5 radius-round bgc-blue-l2 opacity-5" style="width: 4.75rem; height: 4.75rem;"></div>
												<div class="position-br	mb-n5 mr-n5 radius-round bgc-blue-l1 opacity-5" style="width: 4.25rem; height: 4.25rem;"></div>
												<div class="flex-grow-1 pl-25 pos-rel d-flex flex-column">
													<div class="text-secondary-d4">
														<span class="text-180 text-warning" id="invoice_not_confirmed">Rp. 189.887.094</span>
													</div>
													<div class="mt-auto text-nowrap text-secondary-d2 text-105 letter-spacing mt-n1">
														Kemarin
													</div>
												</div>
												<div class="ml-auto pr-1 align-self-center pos-rel text-125">
													<i class="fa fa-dollar-sign text-blue opacity-1 fa-2x mr-25"></i>
												</div>
											</div>
										</div>
										<div class="col-1 mb-4 pos-rel mt-3 mt-sm-0 pt-0 pt-sm-0">
											<span class="d-inline-block bgc-red-d1 p-3 radius-round text-center position-center border-4 brc-green-l2">
												<i class="fa fa-level-down-alt text-white text-170 w-4 h-4"></i>

											</span>
										</div>
										<div class="col-3 mb-4 pos-rel mt-3 mt-sm-0 pt-0 pt-sm-0">
											<div class="ccard h-100 pt-2 pb-25 px-25 d-flex overflow-hidden">
												<div class="position-br	mb-n5 mr-n5 radius-round bgc-blue-l3 opacity-3" style="width: 5.25rem; height: 5.25rem;"></div>
												<div class="position-br	mb-n5 mr-n5 radius-round bgc-blue-l2 opacity-5" style="width: 4.75rem; height: 4.75rem;"></div>
												<div class="position-br	mb-n5 mr-n5 radius-round bgc-blue-l1 opacity-5" style="width: 4.25rem; height: 4.25rem;"></div>
												<div class="flex-grow-1 pl-25 pos-rel d-flex flex-column">
													<div class="text-secondary-d4">
														<span class="text-180 text-green" id="invoice_not_confirmed">Rp. 35.112.450</span>
													</div>
													<div class="mt-auto text-nowrap text-secondary-d2 text-105 letter-spacing mt-n1">
														Hari Ini
													</div>
												</div>
												<div class="ml-auto pr-1 align-self-center pos-rel text-125">
													<i class="fa fa-dollar-sign text-blue opacity-1 fa-2x mr-25"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-6 pos-rel mt-3 mt-sm-4 pt-0 pt-sm-0">
						<div class="card ccard" id="card-4">
							<div class="card-header card-header-lg">
								<h5 class="card-title text-130 text-dark-m3">
									<i class="fa fa-file-invoice-dollar"></i>
									Info Penerimaan
								</h5>

								<div class="card-toolbar ">
									<a href="#" data-action="reload" class="card-toolbar-btn btn btn-sm radius-round btn-green">
										<i class="fas fa-sync-alt w-2 mx-1px"></i>
									</a>

									<a href="#" data-action="toggle" class="card-toolbar-btn btn btn-sm radius-round btn-grey">
										<i class="fa fa-chevron-up w-2 mx-1px"></i>
									</a>
								</div>
							</div>
							<!-- /.card-header -->

							<div class="card-body p-0" style="min-height: 300px;">
								<div
									class="mt-45">
									{# Penerimaan Bulan Berjalan #}
									<div class="bcard h-300 d-flex align-items-center p-3" style="box-shadow: unset;">
										<div>
											<span class="d-inline-block bgc-green-d1 p-3 radius-round text-center border-4 brc-green-l2">
												<i class="fa fa-dollar-sign text-white text-170 w-4 h-4"></i>
											</span>
										</div>

										<div class="ml-3 flex-grow-1">
											<div class="pos-rel">
												<div class="d-flex">
													<div class="flex-grow-1">
														<div class="text-nowrap text-140 text-success-d1">
															Penerimaan Bulan Berjalan
														</div>
													</div>
												</div>
												<span class="text-dark-tp4 text-120 mr-n2px">
													Rp.
												</span>
												<span class="text-success-d3 text-160">
													16.463.038.991
												</span>
												<span class="text-blue-m1 text-600 text-90 ml-15 text-nowrap">
													8%
													<i class="fa fa-arrow-up"></i>
												</span>
											</div>
											<div class="text-dark-tp4 text-90">
												dari Rp. 21.390.759.225
											</div>
										</div>
									</div>

									{# Total Seluruh Penerimaan #}
									<div class="bcard h-300 d-flex align-items-center p-3" style="box-shadow: unset;">
										<div>
											<span class="d-inline-block bgc-warning-d1 p-3 radius-round text-center border-4 brc-warning-l2">
												<i class="fa fa-users text-white text-170 w-4 h-4"></i>
											</span>
										</div>

										<div class="ml-3 flex-grow-1">
											<div class="pos-rel">
												<div class="d-flex">
													<div class="flex-grow-1">
														<div class="text-nowrap text-140 text-warning-d1">
															Total Seluruh Penerimaan
														</div>
													</div>
												</div>
												<span class="text-dark-tp4 text-120 mr-n2px">
													Rp.
												</span>
												<span class="text-warning-d3 text-160">
													19.234.243.870
												</span>
												<span class="text-danger-m2 text-600 text-90 ml-15 text-nowrap">
													2%
													<i class="fa fa-arrow-down"></i>
												</span>
											</div>
											<div class="text-dark-tp4 text-110">
												dari
												<strong>
													PPOB  & LOKET
												</strong>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /.card-body -->
						</div>
					</div>
					<div class="col-6 pos-rel mt-3 mt-sm-4 pt-0 pt-sm-0">
						<div class="card ccard" id="card-4">
							<div class="card-header card-header-lg">
								<h5 class="card-title text-130 text-dark-m3">
									<i class="fa fa-book"></i>
									Penerimaan Via Loket
								</h5>

								<div class="card-toolbar ">
									<a href="#" data-action="reload" class="card-toolbar-btn btn btn-sm radius-round btn-green">
										<i class="fas fa-sync-alt w-2 mx-1px"></i>
									</a>

									<a href="#" data-action="toggle" class="card-toolbar-btn btn btn-sm radius-round btn-grey">
										<i class="fa fa-chevron-up w-2 mx-1px"></i>
									</a>
								</div>
							</div>
							<!-- /.card-header -->

							<div class="card-body p-0">
								<div class="p-3">
									<table class="table brc-grey-l4 mb-2">
										<tbody>
											<tr class="bgc-h-default-l4" role="button">
												<td class="text-dark-tp3 text-95">
													Hari ini (Rp.)
												</td>
												<td class="text-right w-6 ">
													<span class="text-info-d2 text-95 font-bolder">
														43.940.665
													</span>
												</td>
											</tr>
											<tr class="bgc-h-default-l4" role="button">
												<td class="text-dark-tp3 text-95">
													Bulan ini (Rp.)
												</td>
												<td class="text-right w-6">
													<span class="text-info-d2 text-95 font-bolder">
														7.637.879.516
													</span>
												</td>
											</tr>
											<tr class="bgc-h-default-l4" role="button">
												<td class="text-dark-tp3 text-95">
													Bulan ini (Rek. Total)
												</td>
												<td class="text-right w-6">
													<span class="text-info-d2 text-95 font-bolder">
														10.924
													</span>
												</td>
											</tr>
											<tr class="bgc-h-default-l4" role="button">
												<td class="text-dark-tp3 text-95">
													Bulan ini (Rek. Air)
												</td>
												<td class="text-right w-6">
													<span class="text-info-d2 text-95 font-bolder">
														9.124
													</span>
												</td>
											</tr>
											<tr class="bgc-h-default-l4" role="button">
												<td class="text-dark-tp3 text-95">
													Bulan ini (Rek. N-Air)
												</td>
												<td class="text-right w-6">
													<span class="text-info-d2 text-95 font-bolder">
														844
													</span>
												</td>
											</tr>
											<tr class="bgc-h-default-l4" role="button">
												<td class="text-dark-tp3 text-95">
													Bulan ini (Rek. Cicilan)
												</td>
												<td class="text-right w-6">
													<span class="text-info-d2 text-95 font-bolder">
														956
													</span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<!-- /.card-body -->
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row mt-3">

			<div class="col-12 pos-rel mt-3 mt-sm-0 pt-0 pt-sm-0">
				<div class="card ccard" id="card-4">
					<div class="card-header card-header-lg">
						<h5 class="card-title text-130 text-dark-m3">
							<i class="fa fa-book"></i>
							Grafik Penerimaan Per Hari
						</h5>

						<div class="card-toolbar ">
							<a href="#" data-action="reload" class="card-toolbar-btn btn btn-sm radius-round btn-green">
								<i class="fas fa-sync-alt w-2 mx-1px"></i>
							</a>

							<a href="#" data-action="toggle" class="card-toolbar-btn btn btn-sm radius-round btn-grey">
								<i class="fa fa-chevron-up w-2 mx-1px"></i>
							</a>
						</div>
					</div>
					<!-- /.card-header -->

					<div class="card-body p-0">
						<div class="p-3">
							<div id="chart_graph" style="height: 400px;"></div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
{% endblock %}
{% block inline_script %}
	<script src="{{ url('assets') }}/js/src/highcharts/modules/exporting.js"></script>
	<script>
		{% include 'Defaults/Dashboard/oldindex.js' %}
	</script>
{% endblock %}
