{% extends 'template/base.volt' %}

{% block title %}
    Setting - Pengaturan TTD Jurnal
{% endblock %}


{% block inject_style %}

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


		.dropdown-indicator-icon {
			right: 1.5rem;
			top: 1.25rem;
		}

		.dropdown-indicator-icon .fa-angle-down {
			-webkit-transition: 0.5s ease transform;
			-o-transition: 0.5s ease transform;
			transition: 0.5s ease transform
		}
		[aria-expanded=true].dropdown-indicator-icon .fa-angle-down {
			-webkit-transform: rotate(180deg);
			-ms-transform: rotate(180deg);
			transform: rotate(180deg)
		}

		.fieldset-custom {
			border: 1px solid #dee2e6;
			border-radius: 0.5rem;
			padding: 1.5rem 1.5rem 1rem;
			margin-bottom: 1.5rem;
			background-color: #fff;
		}

		.fieldset-custom legend {
			font-size: 1.1rem;
			font-weight: 600;
			color: #343a40;
			padding: 0 0.75rem;
			width: auto;
			margin-bottom: 0.5rem;
		}

		@media(max-width: 576px) {
			.fieldset-custom {
				padding: 1rem 0.5rem 0.5rem;
			}
			.fieldset-custom legend {
				font-size: 1rem;
				padding: 0 0.5rem;
			}
		}

		.input-group > .select2-container--default {
			width: auto !important;
			flex: 1 1 auto !important;
		}

		.input-group > .select2-container--default .select2-selection--single {
			height: 100% !important;
			line-height: inherit !important;
		}

		/* Overlay untuk form detail */
		.overlay-form-detail {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: rgba(255, 255, 255, 0.9);
			z-index: 10;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 0.5rem;
		}

		.overlay-content {
			background: white;
			padding: 2rem;
			border-radius: 0.5rem;
			box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
			border: 1px solid #dee2e6;
		}

		#card-form-detail {
			transition: all 0.3s ease;
		}

		#card-form-detail.disabled {
			opacity: 0.6;
		}

		#btn-pengisian-jurnal {
			transition: all 0.3s ease;
		}

		#btn-pengisian-jurnal:disabled {
			opacity: 0.5;
			cursor: not-allowed;
		}

	</style>

{% endblock %}

{% block content %}
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Setting</a>
			</li>
			<li class="breadcrumb-item active">Pengaturan TTD Jurnal</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Pengaturan TTD Jurnal</h2>
			</div>
		</div>

        <form id="manageForm">
			<div class="row">
				<div class="col-12">
					<div class="container d-flex justify-content-center">
						<div class="card shadow-none border my-4" style="width:100%;">
							<div class="card-header p-4 border-bottom bg-body">
								<div class="row g-3 justify-content-between align-items-center">
									<div class="col-12 col-md">
										<h5 class="text-body mb-0 text-center">Pilih Filter</h5>
									</div>
								</div>
							</div>
							<div class="card-body p-0">
								<div class="p-4 code-to-copy">
								<input type="hidden" id="input-action" name="input_action" value="store"/>	

									<div class="mb-3" style="display: none;">
										<div class="input-group input-group-sm" id="elFilterParentDokumen">
											<span class="input-group-text fw-bold" id="lbl_dokumen">
												Adalah Jurnal
											</span>
											<select name="is_ttd_jurnal" id="is_ttd_jurnal" class="form-control form-select">
												<option value="" disabled>PILIH</option>
												<option value="1" selected>YA</option>
												<option value="0">TIDAK</option>
											</select>
										</div>
									</div>

									<div class="mb-3">
										<div class="input-group input-group-sm" id="elFilterParentDokumen">
											<span class="input-group-text fw-bold" id="lbl_dokumen">
												Jenis Lap
											</span>
											<select name="jenis_lap" id="jenis_lap" class="form-control form-select">
												<option  value="" selected disabled>PILIH</option>
												<!-- Memo -->												
												<option data-jtid="1" data-jtname="DHHD" value="DHHD">Memo</option>
												<option data-jtid="1" data-jtname="DHHD" value="DHHD_AKTIVA">Memo AKTIVA</option>
												<option data-jtid="1" data-jtname="DHHD" value="DHHD_SL">Memo SL</option>
												<!-- Jurnal Rekening -->
												<option data-jtid="2" data-jtname="JR" value="JRA">JRA</option>
												<option data-jtid="2" data-jtname="JR" value="JRNA">JRNA</option>
												<!-- JPK -->
												<option data-jtid="3" data-jtname="JPK" value="JPK_LPP">JPK LPP</option>
												<option data-jtid="3" data-jtname="JPK" value="JPK_SETOR_BANK">JPK SETOR BANK</option>
												<!-- JBK -->
												<option data-jtid="4" data-jtname="JBK" value="JBK">JBK (Single Voucher)</option>
												<option data-jtid="4" data-jtname="JBK" value="JBK_MULTI_CEK">JBK Multi Cek</option>
												<option data-jtid="7" data-jtname="DPH" value="DPH">DVHD</option>
												<!-- JPBIK -->
												<option data-jtid="5" data-jtname="JPBIK" value="JPBIK">JPBIK</option>

												<!-- JU -->
												<option data-jtid="6" data-jtname="JU" value="JU">JU</option>
												<option data-jtid="6" data-jtname="JU" value="JU_AKTIVA">JU AKTIVA</option>
												<option data-jtid="6" data-jtname="JU" value="JU_PENERIMAAN_BARANG">JU PENERIMAAN BARANG</option>
												<option data-jtid="6" data-jtname="JU" value="JU_PENYUSUTAN">JU PENYUSUTAN</option>

												<!-- Jurnal Uang Muka Kerja -->
												<option data-jtid="6" data-jtname="JU" value="JU_UANG_MUKA">JU (Uang Muka Kerja)</option>
												<option data-jtid="6" data-jtname="JU" value="JU_PERTANGGUNGJAWABAN">JU PERTANGGUNGJAWABAN</option>
												<option data-jtid="1" data-jtname="DHHD" value="MEMO_UM">MEMO UM</option>
												<option data-jtid="4" data-jtname="JBK" value="JBK_UM">JBK UM</option>
												<option data-jtid="7" data-jtname="DPH" value="DPH_UM">DVHD UM</option>

												<!-- Kontrak -->
												<option data-jtid="1" data-jtname="DHHD" value="KONTRAK_BERTAHAP">KONTRAK BERTAHAP</option>
												<option data-jtid="1" data-jtname="DHHD" value="KONTRAK_PELUNASAN">KONTRAK PELUNASAN</option>

											</select>
										</div>
									</div>

                                    <div class="mb-3">
										<div class="input-group input-group-sm" id="elFilterParentDokumen">
											<span class="input-group-text fw-bold" id="lbl_dokumen">
												Referensi
											</span>
											<select name="dokumen" id="dokumen" class="form-control form-select"></select>
										</div>
									</div>

                                    <div class="mb-3">
										<div class="input-group input-group-sm" id="elFilterParentJumlahTTD">
											<span class="input-group-text fw-bold" id="lbl_jumlah_ttd">
												Jumlah TTD
											</span>
                                            <select name="jumlah_ttd" id="jumlah_ttd" class="form-control form-control-sm">
                                                <option selected disabled value="">Pilih Jumlah TTD</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>	
                                                <option value="5">5</option>	
                                            </select>
										</div>
									</div>

									<!-- Container untuk menampung inputan TTD -->
									<div id="ttd_container"></div>

								</div>
							</div>

							<div class="card-footer text-end">
								<button type="submit" class="ml-1 btn btn-success btn-sm" id="btn-submit">
									<i class="fas fa-save"></i>
										Simpan
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>      

	</div>

{% endblock %}

{% block inline_script %}
	{% include "Defaults/Setting/PengaturanTTDJurnal/index.js" %}
{% endblock %}
