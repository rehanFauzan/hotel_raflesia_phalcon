{% extends 'template/base.volt' %}

{% block title %}
	Struktur Organisasi - Perusahaan
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
	</style>

{% endblock %}

{% block content %}
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Struktur Organisasi</a>
			</li>
			<li class="breadcrumb-item active">Perusahaan</li>
		</ol>
	</nav>

	<div class="border-bottom border-translucent mb-7 mx-n3 px-2 mx-lg-n6 px-lg-6">
		<div class="row">
			<div class="col-xl-9">
				<div class="d-sm-flex justify-content-between">
					<h4 class="mb-4">Kelola Profile Perusahaan</h4>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xl-9">
			<div class="d-flex align-items-end position-relative mb-7">
				<input class="d-none" id="upload-avatar" type="file" accept=".png,.jpg,.jpeg" onchange="" />
				<div class="hoverbox" style="width: 150px; height: 150px">
					<div class="hoverbox-content rounded-circle d-flex flex-center z-1" style="--phoenix-bg-opacity: .56;">
						<span class="fa-solid fa-camera fs-1 text-body-quaternary"></span>
					</div>
					<div class="position-relative bg-body-quaternary rounded-circle cursor-pointer d-flex flex-center mb-xxl-7">
						<div class="avatar avatar-5xl"><img class="rounded-circle" src="{{ url('external_img') }}/{{ arr_val_pdam['image_logo'] }}" alt=""/></div>
						<label class="w-100 h-100 position-absolute z-1" for="upload-avatar"></label>
					</div>
				</div>
			</div>

			<form id="formProfileSetting" class="mb-4">
				
				<input type="hidden" name="image_logo" id="image_logo" />

				<h4 class="mb-3">Informasi Aplikasi</h4>
				<div class="row g-3 mb-3">
					<div class="col-sm-12 col-md-5">
						<div class="form-floating">
							<input value="{{ arr_val_pdam['nama_aplikasi'] }}" class="form-control" name="nama_aplikasi" id="nama_aplikasi" type="text" placeholder="Nama Aplikasi ...."/>
							<label for="nama_aplikasi">Nama Aplikasi : </label>
						</div>
					</div>
					<div class="col-sm-12 col-md-7">
						<div class="form-floating">
							<input value="{{ arr_val_pdam['nama_panjang_aplikasi'] }}" class="form-control" name="nama_panjang_aplikasi" id="nama_panjang_aplikasi" type="text" placeholder="Nama Panjang Aplikasi ...."/>
							<label for="nama_panjang_aplikasi">Nama Panjang Aplikasi : </label>
						</div>
					</div>
				</div>

				<h4 class="mb-3">Informasi PDAM</h4>
				<div class="row g-3">
					<div class="col-sm-12 col-md-12">
						<div class="form-floating">
							<input value="{{ arr_val_pdam['nama_pdam'] }}" class="form-control" name="nama_pdam" id="nama_pdam" type="text" placeholder="Nama PDAM ...."/>
							<label for="nama_pdam">PDAM : </label>
						</div>
					</div>

					<div class="col-sm-12 col-md-6">
						<div class="form-floating">
							<input value="{{ arr_val_pdam['pemerintah_kota_kab'] }}" class="form-control" name="pemerintah_kota_kab" id="pemerintah_kota_kab" type="text" placeholder="Pemerintah ...."/>
							<label for="pemerintah_kota_kab">Pemerintah Kota Kab : </label>
						</div>
					</div>
					<div class="col-sm-12 col-md-6">
						<div class="form-floating">
							<input value="{{ arr_val_pdam['kota_kab'] }}" class="form-control" name="kota_kab" id="kota_kab" type="text" placeholder="Kota / Kab ...."/>
							<label for="kota_kab">Kota / Kabupaten : </label>
						</div>
					</div>
					<div class="col-sm-12 col-md-12">
						<div class="form-floating">
							<input value="{{ arr_val_pdam['alamat'] }}" class="form-control" name="alamat" id="alamat" type="text" placeholder="Alamat ...."/>
							<label for="alamat">Alamat : </label>
						</div>
					</div>
					<div class="col-sm-12 col-md-6">
						<div class="form-floating">
							<input value="{{ arr_val_pdam['no_telp_pdam'] }}" class="form-control" name="no_telp_pdam" id="no_telp_pdam" type="text" placeholder="No Telepon :  ...."/>
							<label for="no_telp_pdam">No Telpon : </label>
						</div>
					</div>
					<div class="col-sm-12 col-md-6">
						<div class="form-floating">
							<input value="{{ arr_val_pdam['email'] }}" class="form-control" name="email" id="email" type="email" placeholder="Email :  ...."/>
							<label for="email">Email : </label>
						</div>
					</div>
					<div class="col-sm-12 col-md-6">
						<div class="form-floating">
							<input value="{{ arr_val_pdam['latitude'] }}" class="form-control" name="latitude" id="latitude" type="text" placeholder="Latitude :  ...."/>
							<label for="latitude">Latitude : </label>
						</div>
					</div>
					<div class="col-sm-12 col-md-6">
						<div class="form-floating">
							<input  value="{{ arr_val_pdam['longitude'] }}" class="form-control" name="longitude" id="longitude" type="text" placeholder="Longitude :  ...."/>
							<label for="longitude">Longitude : </label>
						</div>
					</div>
				</div>

				<div class="row">
						<div class="col-12 d-flex justify-content-end mt-6">
							<button class="btn btn-primary" id="btn_simpanData">
								<span class="fas fa-pencil-alt me-2"></span> &nbsp;
								Simpan Perubahan
							</button>
						</div>
				</div>

				
			</form>
		</div>
	</div>

	{{ flash.output() }}

{% endblock %}

{% block inline_script %}
	{% include "Defaults/Setting/Profile/index.js" %}
{% endblock %}
