{% extends 'template/auth.volt' %}

{% block title %}
	Login - {{ config.appName }} <!-- Halaman Login -->
{% endblock %}

{% block inline_style %}
	<style>
		.body-container {
			background: linear-gradient(to right, #0e0e0ecc, #0e0e0ecc), url("{{ url('assets') }}/image/bg-office.jpg");
			background-attachment: fixed;
			background-repeat: no-repeat;
			background-position: center;
		}
		.auth-title-box-image{
			max-width: 200px;
		}
	</style>
{% endblock %}

{% block content %}

	<div class="bg-holder bg-auth-card-overlay" style="background-image:url({{ url('assets') }}/img/bg/37.png);"></div>
	<!--/.bg-holder-->
	
	<div class="row flex-center position-relative min-vh-100 g-0 py-5">
		<div class="col-11 col-sm-10 col-xl-8">
			<div class="card border border-translucent auth-card">
				<div class="card-body pe-md-0">
					<div class="row align-items-center gx-0 gy-7">
						<div class="col-auto bg-body-highlight dark__bg-gray-1100 rounded-3 position-relative overflow-hidden auth-title-box">
							<div class="bg-holder" style="background-image:url({{ url('assets') }}/img/bg/38.png);"></div>
							<!--/.bg-holder-->
							<div class="position-relative px-4 px-lg-7 pt-7 pb-7 pb-sm-5 text-center text-md-start pb-lg-7 pb-md-7">
								<h3 class="mb-3 text-body-emphasis fs-7">{{ config.appName }}</h3>
								<p class="text-body-tertiary fs-9">{{ config.appDescription }}</p>
								<ul class="list-unstyled mb-0 w-max-content w-md-auto">
									<li class="d-flex align-items-center">
										<span class="uil uil-check-circle text-success me-2"></span>
										<span class="text-body-tertiary fw-semibold">...</span>
									</li>
									<li class="d-flex align-items-center">
										<span class="uil uil-check-circle text-success me-2"></span>
										<span class="text-body-tertiary fw-semibold">...</</span>
									</li>
									<li class="d-flex align-items-center">
										<span class="uil uil-check-circle text-success me-2"></span>
										<span class="text-body-tertiary fw-semibold">...</</span>
									</li>
								</ul>
							</div>
							<div class="position-relative z-n1 mb-6 d-none d-md-block text-center mt-md-15">
								<img class="auth-title-box-image d-dark-none" src="{{ url('external_img') }}/<?= 'logo-pdam-13.png' ?>" alt=""/>
								<img class="auth-title-box-image d-light-none" src="{{ url('external_img') }}/<?= 'logo-pdam-13.png' ?>" alt=""/>
							</div>
						</div>
						<div class="col mx-auto">
							<form autocomplete="off" class="form-row mt-4" method="post">
								<div class="auth-form-box">

									<input type='hidden' name='<?php echo $this->security->getTokenKey() ?>' value='<?php echo $this->security->getToken() ?>'/>
									
									<div class="text-center mb-7">
										<a class="d-flex flex-center text-decoration-none mb-4" href="{{ url('home') }}">
											<div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
												<img src="{{ url('external_img') }}/logo-pdam-13.png" alt="no_image_found" height="90" />
											</div>
										</a>
										<h3 class="text-body-highlight"><?= (!empty($pdam->nama_aplikasi)) ? $pdam->nama_aplikasi : 'Log In' ?></h3>
										<p class="text-body-tertiary"><?= (!empty($pdam->nama_panjang_aplikasi)) ? $pdam->nama_panjang_aplikasi : 'Untuk masuk aplikasi' ?></p>
									</div>
									
									<!-- <div class="mb-3 text-start">
										<label class="form-label" for="username">Username</label>
										<div class="form-icon-container">
											<input class="form-control form-icon-input" id="pdamid" name="pdamid" type="hidden" value="<?= (!empty($pdam->id)) ? $pdam->id : '' ?>" />
											<input class="form-control form-icon-input" id="pdamid_dir" name="pdamid_dir" type="hidden" value="<?= (!empty($pdam->direktori)) ? $pdam->direktori : '' ?>" />
											<input required="" class="form-control form-icon-input" id="username" name="username" type="text" placeholder="username" value="{{ cookies.get('remembered_username').getValue() }}" />
											<span class="fas fa-user text-body fs-9 form-icon"></span>
										</div>
									</div>

									<div class="mb-3 text-start">
										<label class="form-label" for="password">Password</label>
										<div class="form-icon-container" data-password="data-password">
											<input required="" class="form-control form-icon-input pe-6" id="password" name="password" type="password" placeholder="password" data-password-input="data-password-input" required/><span class="fas fa-key text-body fs-9 form-icon"></span>
											<button class="btn px-3 py-0 h-100 position-absolute top-0 end-0 fs-7 text-body-tertiary" data-password-toggle="data-password-toggle">
												<span class="uil uil-eye show"></span>
												<span class="uil uil-eye-slash hide"></span>
											</button>
										</div>
									</div>

									<div class="mb-3 text-start">
										<label class="form-label" for="username">Periode</label>
										<div class="form-icon-container">
											<input required="" class="form-control form-icon-input yearmonth-picker" id="periode" name="periode" type="text" placeholder="Pilih periode" value="" />
											<span class="fas fa-calendar text-body fs-9 form-icon"></span>
										</div>
									</div> -->


									<div class="mb-3">
										<div class="input-group input-group-sm" id="elFilterParentUsername">
											<span class="input-group-text fw-bold" id="lbl_username">
												Username
											</span>
											<input class="form-control form-icon-input" id="pdamid" name="pdamid" type="hidden" value="<?= (!empty($pdam->id)) ? $pdam->id : '' ?>" />
											<input class="form-control form-icon-input" id="pdamid_dir" name="pdamid_dir" type="hidden" value="<?= (!empty($pdam->direktori)) ? $pdam->direktori : '' ?>" />
											<input required="" class="form-control form-icon-input" id="username" name="username" type="text" placeholder="username" value="{{ cookies.get('remembered_username').getValue() }}" />
										</div>
									</div>

									<div class="mb-3">
										<div class="input-group input-group-sm" id="elFilterParentPassword">
											<span class="input-group-text fw-bold" id="lbl_password">
												Password
											</span>
											<input required="" class="form-control form-icon-input pe-6" id="password" name="password" type="password" placeholder="password" data-password-input="data-password-input" required/>
											<!-- <button class="btn px-3 py-0 h-100 position-absolute top-0 end-0 fs-7 text-body-tertiary" data-password-toggle="data-password-toggle">
												<span class="uil uil-eye show"></span>
												<span class="uil uil-eye-slash hide"></span>
											</button> -->
										</div>
									</div>

									<div class="mb-3">
										<div class="input-group input-group-sm" id="elFilterParentPeriode">
											<span class="input-group-text fw-bold" id="lbl_periode">
												Periode
											</span>
											<input required="" class="form-control form-icon-input yearmonth-picker" id="periode" name="periode" type="text" placeholder="Pilih periode" value="" />
										</div>
									</div>

									
									<!--
									<div class="row">
										<div class="col-md-8">
											<div class="mb-3 text-start">
												<label class="form-label" for="username">Captcha</label>
												<div class="form-icon-container">
													<input class="form-control form-icon-input" id="captcha" name="captcha" type="text" placeholder="captcha" />
													<span class="fas fa-key text-body fs-9 form-icon"></span>
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="mb-3 text-start">
												<label class="form-label">&nbsp;</label>
												<img src="{{ url('imagesCaptcha/out.jpg') }}" alt="captcha" class="img-fluid" style="height: 38px;"/>
											</div>
										</div>
									</div>
									-->
									

									<div class="row flex-between-center mb-7">
										<div class="col-auto">
											<div class="form-check mb-0">
												<input class="form-check-input" id="basic-checkbox" type="checkbox" name="remember_username"
													{% if cookies.has('remembered_username') %}checked{% endif %}/>
												<label class="form-check-label mb-0" for="basic-checkbox">Remember username</label>
											</div>
										</div>

										<!-- 
											<div class="col-auto">
												<a class="fs-9 fw-semibold" href="{{ url('auth/forgot-password') }}">Forgot Password?</a>
											</div>
										-->
									</div>

									<button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
									<!-- 
										<div class="text-center">
											<a class="fs-9 fw-bold" href="{{ url('auth/sign-up') }}">Create an account</a>
										</div>
									-->
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{ flash.output() }}
	
{% endblock %}

{% block inline_script %}
	{% include "Defaults/Auth/login.js" %}
{% endblock %}
