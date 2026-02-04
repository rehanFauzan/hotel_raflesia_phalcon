{% extends 'template/base.volt' %}

{% block title %}
	Setting - Ganti Password
{% endblock %}

{% block content %}
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Setting</a>
			</li>
			<li class="breadcrumb-item active">Ganti Password</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Ganti Password</h2>
			</div>
		</div>

		<form id="manageForm">
			<div class="row">
				<div class="col-8 offset-2">
					<div class="container d-flex justify-content-center">
						<div class="card shadow-none border my-4" style="width:100%;">
							<div class="card-header p-4 border-bottom bg-body">
								<div class="row g-3 justify-content-between align-items-center">
									<div class="col-12 col-md">
										
										<h5 class="text-body mb-0 text-center"><i class="fas fa-lock"></i> Ganti Password</h5>
									</div>
								</div>
							</div>
							<div class="card-body p-0">
								<div class="p-4 code-to-copy">

                                    <div class="mb-3">
										<div class="input-group">
										  <span class="input-group-text">Password Lama</span>
										  <input class="form-control" type="password" id="pw_lama" name="pw_lama" placeholder="Masukkan Password Lama ...">
										  <span class="input-group-text" onclick="togglePassword('pw_lama', this)">
											<i class="far fa-eye"></i>
										  </span>
										</div>
									  </div>
									  
									  <div class="mb-3">
										<div class="input-group">
										  <span class="input-group-text">Password Baru</span>
										  <input class="form-control" type="password" id="pw_baru" name="pw_baru" placeholder="Masukkan Password Baru ...">
										  <span class="input-group-text" onclick="togglePassword('pw_baru', this)">
											<i class="far fa-eye"></i>
										  </span>
										</div>
									  </div>
									  
									  <div class="mb-3">
										<div class="input-group">
										  <span class="input-group-text">Verifikasi Password</span>
										  <input class="form-control" type="password" id="verifikasi_pw" name="verifikasi_pw" placeholder="Masukkan Verifikasi Password ...">
										  <span class="input-group-text" onclick="togglePassword('verifikasi_pw', this)">
											<i class="far fa-eye"></i>
										  </span>
										</div>
									  </div>									  

								</div>
							</div>

							<div class="card-footer text-end">
								<button type="submit" class="ml-1 btn btn-success btn-sm" id="btn_submit">
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
	{% include "Defaults/Setting/Password/index.js" %}
{% endblock %}
