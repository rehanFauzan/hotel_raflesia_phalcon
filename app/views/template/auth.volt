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
            {{ config.appName }}
		</title>
    
		<!-- ===============================================-->
		<!--    Favicons-->
		<!-- ===============================================-->
		<link rel="apple-touch-icon" sizes="180x180" href="{{ url('external_img') }}/favicons/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="{{ url('external_img') }}/favicons/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="{{ url('external_img') }}/favicons/favicon-16x16.png">
		<link rel="shortcut icon" type="image/x-icon" href="{{ url('external_img') }}/favicons/favicon.ico">
		<link rel="manifest" href="{{ url('external_img') }}/favicons/manifest.json">
		<meta name="msapplication-TileImage" content="{{ url('external_img') }}/favicons/mstile-150x150.png">
		<meta name="theme-color" content="#ffffff">
		<script src="{{ url('vendors') }}/simplebar/simplebar.min.js"></script>
		<script src="{{ url('assets') }}/js/config.js"></script>

		<!-- ===============================================-->
		<!--    Stylesheets-->
		<!-- ===============================================-->
		<link rel="preconnect" href="https://fonts.googleapis.com"> <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
		<link href="{{ url('lib_independent')}}/NunitoSans.css" rel="stylesheet">
		<link href="{{ url('vendors') }}/simplebar/simplebar.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
		<link href="{{ url('assets') }}/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
		<link href="{{ url('assets') }}/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
		<link href="{{ url('assets') }}/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
		<link href="{{ url('assets') }}/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
		<link rel="stylesheet" type="text/css" href="{{ url('vendors') }}/bootstrap-datepicker-1.9.0/css/bootstrap-datepicker3.standalone.css">
		<script>
			{% include "template/auth_header.js" %}
		</script>

        {% block inline_style %}
		{% endblock %}
	</head>

	<body>

		<!-- ===============================================-->
		<!--    Main Content-->
		<!-- ===============================================-->
			<main class="main" id="top"> <div class="container-fluid bg-body-tertiary dark__bg-gray-1200">
				{% block content %}

				{% endblock %}
			</div>

			<script>
                {% include "template/auth_footer.js" %}
			</script>
		</main>
		<!-- ===============================================-->
		<!--    End of Main Content-->
		<!-- ===============================================-->

		<!-- ===============================================-->
		<!--    JavaScripts-->
		<!-- ===============================================-->
        <script src="{{ url('vendors') }}/popper/popper.min.js"> </script>
		<script src="{{ url('vendors') }}/bootstrap/bootstrap.min.js"></script>
		<script src="{{ url('vendors') }}/anchorjs/anchor.min.js"></script>
		<script src="{{ url('vendors') }}/is/is.min.js"></script>
		<script src="{{ url('vendors') }}/fontawesome/all.min.js"></script>
		<script src="{{ url('vendors') }}/lodash/lodash.min.js"></script>
		<script src="{{ url('vendors') }}/list.js/list.min.js"></script>
		<script src="{{ url('vendors') }}/feather-icons/feather.min.js"></script>
		<script src="{{ url('vendors') }}/dayjs/dayjs.min.js"></script>
		<script src="{{ url('assets') }}/js/phoenix.js"></script>
		<script src="{{ url('lib_independent') }}/jquery-3.7.1.min.js"></script>
		<script src="{{ url('lib_independent') }}/dataTables.min.js"></script>
		<script src="{{ url('lib_independent') }}/dataTables.bootstrap5.min.js"></script>
		<script src="{{ url('lib_independent') }}/select2.min.js"></script>
		<script src="{{ url('lib_independent') }}/jquery-confirm.min.js"></script>
		<script src="{{ url('lib_independent') }}/notyf.min.js"></script>
		<script src="{{ url('lib_independent') }}/jquery.validate.min.js"></script>
		<script src="{{ url('lib_independent') }}/additional-methods.min.js"></script>
		<script src="{{ url('lib_independent') }}/flatpickr.js"></script>
		<script src="{{ url('lib_independent') }}/index.js"></script>
		<script src="{{ url('lib_independent') }}/id.js"></script>
		<script src="{{ url('lib_independent') }}/moment-with-locales.min.js"></script>
		<script src="{{ url('vendors') }}/summernote-0.9.0/summernote-bs5.min.js"></script>
		<script src="{{ url('vendors') }}/bootstrap-datepicker-1.9.0/js/bootstrap-datepicker.min.js"></script>
		<script src="{{ url('vendors') }}/bootstrap-datepicker-1.9.0/locales/bootstrap-datepicker.id.min.js"></script>
		<script>
			{% block inline_script %}
			{% endblock %}
		</script>
	</body>

</html>
