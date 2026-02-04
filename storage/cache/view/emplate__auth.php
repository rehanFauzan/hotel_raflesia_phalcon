a:7:{i:0;s:3413:"<!DOCTYPE html>
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

        ";s:12:"inline_style";a:1:{i:0;a:4:{s:4:"type";i:357;s:5:"value";s:4:"
		";s:4:"file";s:99:"/Applications/XAMPP/xamppfiles/htdocs/hotel_reservasi_raflesia_bandung/app/views/template/auth.volt";s:4:"line";i:46;}}i:1;s:273:"
	</head>

	<body>

		<!-- ===============================================-->
		<!--    Main Content-->
		<!-- ===============================================-->
			<main class="main" id="top"> <div class="container-fluid bg-body-tertiary dark__bg-gray-1200">
				";s:7:"content";a:1:{i:0;a:4:{s:4:"type";i:357;s:5:"value";s:8:"

				";s:4:"file";s:99:"/Applications/XAMPP/xamppfiles/htdocs/hotel_reservasi_raflesia_bandung/app/views/template/auth.volt";s:4:"line";i:57;}}i:2;s:3105:"
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
			";s:13:"inline_script";a:1:{i:0;a:4:{s:4:"type";i:357;s:5:"value";s:5:"
			";s:4:"file";s:99:"/Applications/XAMPP/xamppfiles/htdocs/hotel_reservasi_raflesia_bandung/app/views/template/auth.volt";s:4:"line";i:98;}}i:3;s:36:"
		</script>
	</body>

</html>
";}