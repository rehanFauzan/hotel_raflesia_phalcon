<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- ===============================================-->
		<!--    Document Title-->
		<!-- ===============================================-->
        <title>
            <?= $this->config->appName ?>
		</title>
    
		<!-- ===============================================-->
		<!--    Favicons-->
		<!-- ===============================================-->
		<link rel="apple-touch-icon" sizes="180x180" href="<?= $this->url->get('external_img') ?>/favicons/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="<?= $this->url->get('external_img') ?>/favicons/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="<?= $this->url->get('external_img') ?>/favicons/favicon-16x16.png">
		<link rel="shortcut icon" type="image/x-icon" href="<?= $this->url->get('external_img') ?>/favicons/favicon.ico">
		<link rel="manifest" href="<?= $this->url->get('external_img') ?>/favicons/manifest.json">
		<meta name="msapplication-TileImage" content="<?= $this->url->get('external_img') ?>/favicons/mstile-150x150.png">
		<meta name="theme-color" content="#ffffff">
		<script src="<?= $this->url->get('vendors') ?>/simplebar/simplebar.min.js"></script>
		<script src="<?= $this->url->get('assets') ?>/js/config.js"></script>

		<!-- ===============================================-->
		<!--    Stylesheets-->
		<!-- ===============================================-->
		<link rel="preconnect" href="https://fonts.googleapis.com"> <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
		<link href="<?= $this->url->get('lib_independent') ?>/NunitoSans.css" rel="stylesheet">
		<link href="<?= $this->url->get('vendors') ?>/simplebar/simplebar.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
		<link href="<?= $this->url->get('assets') ?>/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
		<link href="<?= $this->url->get('assets') ?>/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
		<link href="<?= $this->url->get('assets') ?>/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
		<link href="<?= $this->url->get('assets') ?>/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
		<link rel="stylesheet" type="text/css" href="<?= $this->url->get('vendors') ?>/bootstrap-datepicker-1.9.0/css/bootstrap-datepicker3.standalone.css">
		<script>
			var phoenixIsRTL = window.config.config.phoenixIsRTL;
if (phoenixIsRTL) {
    var linkDefault = document.getElementById('style-default');
    var userLinkDefault = document.getElementById('user-style-default');
    linkDefault.setAttribute('disabled', true);
    userLinkDefault.setAttribute('disabled', true);
    document.querySelector('html').setAttribute('dir', 'rtl');
} else {
    var linkRTL = document.getElementById('style-rtl');
    var userLinkRTL = document.getElementById('user-style-rtl');
    linkRTL.setAttribute('disabled', true);
    userLinkRTL.setAttribute('disabled', true);
}
		</script>

        
	<style>
		.body-container {
			background: linear-gradient(to right, #0e0e0ecc, #0e0e0ecc), url("<?= $this->url->get('assets') ?>/image/bg-office.jpg");
			background-attachment: fixed;
			background-repeat: no-repeat;
			background-position: center;
		}
		.auth-title-box-image{
			max-width: 200px;
		}
	</style>

	</head>

	<body>

		<!-- ===============================================-->
		<!--    Main Content-->
		<!-- ===============================================-->
			<main class="main" id="top"> <div class="container-fluid bg-body-tertiary dark__bg-gray-1200">
				

	<div class="bg-holder bg-auth-card-overlay" style="background-image:url(<?= $this->url->get('assets') ?>/img/bg/37.png);"></div>
	<!--/.bg-holder-->
	
	<div class="row flex-center position-relative min-vh-100 g-0 py-5">
		<div class="col-11 col-sm-10 col-xl-8">
			<div class="card border border-translucent auth-card">
				<div class="card-body pe-md-0">
					<div class="row align-items-center gx-0 gy-7">
						<div class="col-auto bg-body-highlight dark__bg-gray-1100 rounded-3 position-relative overflow-hidden auth-title-box">
							<div class="bg-holder" style="background-image:url(<?= $this->url->get('assets') ?>/img/bg/38.png);"></div>
							<!--/.bg-holder-->
							<div class="position-relative px-4 px-lg-7 pt-7 pb-7 pb-sm-5 text-center text-md-start pb-lg-7 pb-md-7">
								<h3 class="mb-3 text-body-emphasis fs-7"><?= $this->config->appName ?></h3>
								<p class="text-body-tertiary fs-9"><?= $this->config->appDescription ?></p>
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
								<img class="auth-title-box-image d-dark-none" src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-13.png' ?>" alt=""/>
								<img class="auth-title-box-image d-light-none" src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-13.png' ?>" alt=""/>
							</div>
						</div>
						<div class="col mx-auto">
							<form autocomplete="off" class="form-row mt-4" method="post">
								<div class="auth-form-box">

									<input type='hidden' name='<?php echo $this->security->getTokenKey() ?>' value='<?php echo $this->security->getToken() ?>'/>
									
									<div class="text-center mb-7">
										<a class="d-flex flex-center text-decoration-none mb-4" href="<?= $this->url->get('home') ?>">
											<div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
												<img src="<?= $this->url->get('external_img') ?>/logo-pdam-13.png" alt="no_image_found" height="90" />
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
											<input required="" class="form-control form-icon-input" id="username" name="username" type="text" placeholder="username" value="<?= $this->cookies->get('remembered_username')->getValue() ?>" />
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
											<input required="" class="form-control form-icon-input" id="username" name="username" type="text" placeholder="username" value="<?= $this->cookies->get('remembered_username')->getValue() ?>" />
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
												<img src="<?= $this->url->get('imagesCaptcha/out.jpg') ?>" alt="captcha" class="img-fluid" style="height: 38px;"/>
											</div>
										</div>
									</div>
									-->
									

									<div class="row flex-between-center mb-7">
										<div class="col-auto">
											<div class="form-check mb-0">
												<input class="form-check-input" id="basic-checkbox" type="checkbox" name="remember_username"
													<?php if ($this->cookies->has('remembered_username')) { ?>checked<?php } ?>/>
												<label class="form-check-label mb-0" for="basic-checkbox">Remember username</label>
											</div>
										</div>

										<!-- 
											<div class="col-auto">
												<a class="fs-9 fw-semibold" href="<?= $this->url->get('auth/forgot-password') ?>">Forgot Password?</a>
											</div>
										-->
									</div>

									<button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
									<!-- 
										<div class="text-center">
											<a class="fs-9 fw-bold" href="<?= $this->url->get('auth/sign-up') ?>">Create an account</a>
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

	<?= $this->flash->output() ?>
	

			</div>

			<script>
                var navbarTopStyle = window.config.config.phoenixNavbarTopStyle;
var navbarTop = document.querySelector('.navbar-top');
if (navbarTopStyle === 'darker') {
    navbarTop.setAttribute('data-navbar-appearance', 'darker');
}

var navbarVerticalStyle = window.config.config.phoenixNavbarVerticalStyle;
var navbarVertical = document.querySelector('.navbar-vertical');
if (navbarVertical && navbarVerticalStyle === 'darker') {
    navbarVertical.setAttribute('data-navbar-appearance', 'darker');
}
			</script>
		</main>
		<!-- ===============================================-->
		<!--    End of Main Content-->
		<!-- ===============================================-->

		<!-- ===============================================-->
		<!--    JavaScripts-->
		<!-- ===============================================-->
        <script src="<?= $this->url->get('vendors') ?>/popper/popper.min.js"> </script>
		<script src="<?= $this->url->get('vendors') ?>/bootstrap/bootstrap.min.js"></script>
		<script src="<?= $this->url->get('vendors') ?>/anchorjs/anchor.min.js"></script>
		<script src="<?= $this->url->get('vendors') ?>/is/is.min.js"></script>
		<script src="<?= $this->url->get('vendors') ?>/fontawesome/all.min.js"></script>
		<script src="<?= $this->url->get('vendors') ?>/lodash/lodash.min.js"></script>
		<script src="<?= $this->url->get('vendors') ?>/list.js/list.min.js"></script>
		<script src="<?= $this->url->get('vendors') ?>/feather-icons/feather.min.js"></script>
		<script src="<?= $this->url->get('vendors') ?>/dayjs/dayjs.min.js"></script>
		<script src="<?= $this->url->get('assets') ?>/js/phoenix.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/jquery-3.7.1.min.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/dataTables.min.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/dataTables.bootstrap5.min.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/select2.min.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/jquery-confirm.min.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/notyf.min.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/jquery.validate.min.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/additional-methods.min.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/flatpickr.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/index.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/id.js"></script>
		<script src="<?= $this->url->get('lib_independent') ?>/moment-with-locales.min.js"></script>
		<script src="<?= $this->url->get('vendors') ?>/summernote-0.9.0/summernote-bs5.min.js"></script>
		<script src="<?= $this->url->get('vendors') ?>/bootstrap-datepicker-1.9.0/js/bootstrap-datepicker.min.js"></script>
		<script src="<?= $this->url->get('vendors') ?>/bootstrap-datepicker-1.9.0/locales/bootstrap-datepicker.id.min.js"></script>
		<script>
			
	function handleRememberUsername(checkbox) {
    if (checkbox.checked) {
        // Menyimpan username ke cookie saat checkbox dicentang
        const username = document.getElementById('username').value;
        document.cookie = `remembered_username=${username}; max-age=2592000; path=/`; // berlaku 30 hari
    } else {
        // Menghapus cookie saat checkbox tidak dicentang
        document.cookie = "remembered_username=; max-age=0; path=/";
    }
}

$('.yearmonth-picker').datepicker({
    format: "yyyymm",
    minViewMode: 'months',
    startView: 'decade',
    autoclose: true,
    language: "id"
});


// Mengecek cookie saat halaman dimuat
window.onload = function () {
    const username = getCookie('remembered_username');
    if (username) {
        document.getElementById('basic-checkbox').checked = true;
    }
}

// Fungsi helper untuk membaca cookie
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

		</script>
	</body>

</html>
