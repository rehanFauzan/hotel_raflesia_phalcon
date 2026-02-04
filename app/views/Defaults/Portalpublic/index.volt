<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/bootstrap/dist/css/bootstrap.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/bootstrap-select/dist/css/bootstrap-select.css"/>
		<link rel="stylesheet" href="{{ url('assets') }}/css/src/colorbox/colorbox.min.css"/>

		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/@fortawesome/fontawesome-free/css/fontawesome.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/@fortawesome/fontawesome-free/css/regular.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/@fortawesome/fontawesome-free/css/brands.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/@fortawesome/fontawesome-free/css/solid.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/smartwizard/dist/css/smart_wizard.min.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/smartwizard/dist/css/smart_wizard_theme_circles.min.css"/>
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/smartwizard@4.4.1/dist/css/smart_wizard_theme_arrows.min.css"/>
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/smartwizard@4.4.1/dist/css/smart_wizard_theme_dots.min.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/jqtree/jqtree.css"/>

		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/datatables.net-buttons-bs4/css/buttons.bootstrap4.css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css" integrity="sha512-TQQ3J4WkE/rwojNFo6OJdyu6G8Xe9z8rMrlF9y7xpFbQfW5g8aSWcygCQ4vqRiJqFsDsE1T6MoAOMJkFXlrI9A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css"/>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/brands.min.css" integrity="sha512-9YHSK59/rjvhtDcY/b+4rdnl0V4LPDWdkKceBl8ZLF5TB6745ml1AfluEU6dFWqwDw9lPvnauxFgpKvJqp7jiQ==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/dist/css/ace-font.css"/>
		<link
		rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css" integrity="sha384-QYIZto+st3yW+o8+5OHfT6S482Zsvz2WfOzpFSXMF9zqeLcFV0/wlZpMtyFcZALm" crossorigin="anonymous">

		<!-- ace.css -->
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/dist/css/ace.css"/>
		<link
		rel="stylesheet" type="text/css" href="{{ url('assets') }}/dist/css/project.css"/>
		<!-- select2.css -->
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/select2/dist/css/select2.min.css"/>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/css/src/bootstrap-datepicker3.min.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/css/src/bootstrap-datetimepicker.min.css"/>
		<link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/node_modules/chosen-js/chosen.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/js/src/pagination/paging.css"/>
		<link rel="stylesheet" type="text/css" href="{{ url('assets') }}/daterangepicker/daterangepicker.css"/>

		<link
		rel="stylesheet" type="text/css" href="{{ url('assets') }}/dist/css/ace-themes.css"/>
		<!-- favicon -->
		<link rel="icon" type="image/png" href="{{ url('assets')}}/image/logo_tbw.jpeg"/>

		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<script src="https://use.fontawesome.com/48047f6809.js"></script>
		<link rel="stylesheet" href="{{ url('assets') }}/css/style/style.css">
		<link rel="icon" type="image/png" href="{{ url('assets')}}/image/logo_tbw.jpeg"/>
		<link rel="stylesheet" href="{{ url('assets') }}/dashboard/css/custom.css">
		<link rel="stylesheet" href="{{ url('assets') }}/dist/css/project.css">
		<link rel="stylesheet" href="{{ url('assets') }}/css/src/project.css">
		
		<title>PORTAL</title>
		<style>
			.page-content {
				padding: 0;
			}
			body {
				font-family: 'Roboto', sans-serif;
				font-size: 16px;
			}
			.body-container {
				background: linear-gradient(0deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url("{{ url('assets') }}/portal/bg-login.jpg");
				background-position: center;
				background-repeat: no-repeat;
				background-size: cover;
			}

			.card-color-gradient {
				background: linear-gradient(270deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 104.42%), linear-gradient(270deg, #37474F -96.46%, rgba(144, 202, 249, 0) 84.96%), #333333;
			}

			.ccard {
				background: radial-gradient(#528cbe, #0e4a80);
				border-radius: 15px;
				padding: 10px;
			}

			.icon-card {
				font-size: 90px;
				margin: 20px;
			}

			.img-card {
				max-width: 110px;
			}

			.ccard:hover,
			.ccard:active,
			.ccard:focus,
			.ccard:visited {
				background: #c5dce9c2 !important;
				.text-card {
					color: #1a446a !important;

				}
			}

			.footer {
				position: fixed;
				width: 100%;
				bottom: 0;
				height: 60px;
			}

			.list-menu {
				margin: 5px 5px 5px 15px !important;
			}
			.list-menu:hover {
				background: #8D99EE;
			}
			.card-color-gradient {
				background: linear-gradient(270deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 104.42%), linear-gradient(270deg, #37474F -96.46%, rgba(144, 202, 249, 0) 84.96%), #333333;
			}
			.menu-title {
				font-size: 16px;
				font-weight: bold;
				color: #8D99EE;
			}
			.sub-menu-title {
				font-size: 14px;
				font-weight: bold;
			}
			.sub-menu-desc {
				font-size: 12px;
			}
			.input-group-text {
				min-width: 120px;
			}
			.list-unstyled {
				border-left: 3px solid #8D99EE;
			}
			.text-purple-new {
				color: #8D99EE !important;
			}
			.icon-color-new {
				color: #1539D4 !important;
			}
			.far {
				font-weight: 600 !important;
			}

			.btn-app {
				background: #ffffff;
			}

			.table-condensed thead tr th {
				background-color: #FFFF;
			}

			.container {
			display: flex;
			flex-wrap: wrap;
			justify-content: space-between;
			align-items: stretch;
			}

			.card {
			height: 90%;
			}

			/* .btn {
			height: 100%;
			}  */
		</style>
	</head>
	<body>
		{#
		{% if id_role == 1 %}
			<button id="btn-menu" style="display: none;" data-toggle="modal" data-target="#modalMenu" class="btn btn-light btn-sm" style="position: fixed;top: 1.6rem;left: calc(2% - 65px);z-index: 10000;transform: rotate(90deg);">
				<i class="fa fa-chevron-down"></i>
				Setting
			</button>
		{% else %}
			
		{% endif %}
		<button id="btn-logout" class="btn btn-danger btn-=lg" onclick="location.href = '{{ url('panel/auth/logout') }}';" style="position: fixed;top: 0.8rem;right: calc(3% - 65px);z-index: 10000; /* transform: rotate(90deg); */">
			<i class="fa fa-sign-out text-180"></i>
		</button>
		#}
		<div class="page-content">
			<div class="body-container">
				<div class="row" style="margin-bottom: 50px;margin-top: 50px;">
					<div class="row">
						<div class="col-md-7 mx-auto">
							<div class="row pl-3">
								<div class="col-12 text-center">
									<h3 class="text-white font-bold">PORTAL APLIKASI TERPADU</h3>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="container" style="margin-bottom: 100px; margin-top: 10px; height: 100%;">
					<div class="col-md-12">
						<div class="row">
							<div class="col">
                                <div class=" card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper mt-1 text-center">
                                            {# <i class="fa fa-list icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/portal/logo_billing.png" alt="" class="img-card" style="width: 80px;height: 80px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card">Billing</h5>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Pembayaran</p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
											<a href="https://dev5.aurorasystem.co.id/billing-v2/auth/login" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;background-color: #1a446a; border-radius:0.9rem;text-wrap:balance;">Buka Aplikasi</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
								<div class=" card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
									<div class="about-us-col">
										<div class="col-icon-wrapper mt-1 text-center">
											<img src="{{ url('assets') }}/portal/logo_ogss.png" alt="" class="img-card" style="width: 80px;height: 80px;">
										</div>
										<h5 class="col-title text-white font-bold text-center text-card">OGSS</h5>
										<div class="col-details text-center">
											<p class="text-white font-bold text-card" style="text-wrap: balance;">One Gate Solution System</p>
										</div>
										<div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
											<a href="https://panel.ogss.co.id/" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap: balance; background-color: #1a446a; border-radius: 0.9rem; display: inline-block; margin: auto;">Buka Aplikasi</a>
										</div>
									</div>
								</div>
							</div>

							<div class="col">
                                <div class="card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper mt-1 text-center">
                                            {# <i class="fa fa-gear icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/portal/logo_inventori.png" alt="" class="img-card mt-3" style="width: 70px;height: 70px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card">Inventori</h5>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Inventori</p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            <a href="https://develop.aurorasystem.co.id/accis-global/auth/simentor/6" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap:balance;background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</a>
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
                                        </div>
                                    </div>
                                </div>
                            </div>

							<div class="col">
                                <div class="card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper mt-1 text-center">
                                            {# <i class="fa fa-gear icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/portal/logowibawa.png" alt="" class="img-card mt-3" style="width: 70px;height: 70px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card">Dashboard</h5>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Rekap Informasi Terintegrasi</p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            <a href="https://dashboard-wibawa.aj-nusantara.com/dashboard" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap:balance;background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</a>
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper text-center mt-1">
                                            {# <i class="fa fa-book icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/image/accis.png" alt="" class="img-card" style="width: 70px;height: 70px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card">AKUNTANSI</h5>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Akuntansi</p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            <a href="https://develop.aurorasystem.co.id/accis-global/auth/form/6" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap:balance;background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</a>
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
                                        </div>
                                    </div>
                                </div>
                            </div>

							<div class="col">
                                <div class="card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper text-center mt-1">
                                            {# <i class="fa fa-book icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/portal/logo_kepegawaian.png" alt="" class="img-card" style="width: 80px;height: 80px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card">KEPEGAWAIAN</h3>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Kepegawaian</p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            <a href="https://devcharisma.aurorasystem.co.id/kepegawaian-wibawa/" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap:balance;background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</a>
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
                                        </div>
                                    </div>
                                </div>
                            </div>

						</div>

						<div class="row">
							<!-- Row 2 -->
							<div class="col">
                                <div class=" card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper mt-1 text-center">
                                            {# <i class="fa fa-list icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/image/sl_logo.png" alt="" class="img-card" style="width: 80px;height: 80px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card" style="text-wrap: balance; font-size: 18px;">SIMPEL</h5>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Pelanggan Baru</p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
											<a href="https://devcharisma.aurorasystem.co.id/billing-v2/panel/auth/login-simpel" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;background-color: #1a446a; border-radius:0.9rem;text-wrap:balance;">Buka Aplikasi</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
								<div class=" card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
									<div class="about-us-col">
										<div class="col-icon-wrapper mt-1 text-center">
											<img src="{{ url('assets') }}/image/aurora.png" alt="" class="img-card" style="width: 80px;height: 80px;">
										</div>
										<h5 class="col-title text-white font-bold text-center text-card" style="text-wrap: balance; font-size: 18px;">BACAMETER WEB</h5>
										<div class="col-details text-center">
											<p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Bacameter</p>
										</div>
										<div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
											<a href="https://dev5.aurorasystem.co.id/bacameter-v2/auth/login" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap: balance; background-color: #1a446a; border-radius: 0.9rem; display: inline-block; margin: auto;">Buka Aplikasi</a>
										</div>
									</div>
								</div>
							</div>

							<div class="col">
                                <div class="card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper mt-1 text-center">
                                            {# <i class="fa fa-gear icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/portal/anggaran.png" alt="" class="img-card mt-3" style="width: 80px;height: 80px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card" style="text-wrap: balance; font-size: 18px;">Anggaran</h5>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Anggaran</p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            <a href="https://develop.aurorasystem.co.id/accis-global/auth/anggaran/6" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap:balance;background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</a>
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
                                        </div>
                                    </div>
                                </div>
                            </div>

							<div class="col">
                                <div class="card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper mt-1 text-center">
                                            {# <i class="fa fa-gear icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/portal/logo_rab.png" alt="" class="img-card mt-3" style="width: 80px;height: 80px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card" style="text-wrap: balance; font-size: 18px;">RAB</h5>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Rencana Anggaran Biaya </p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            <a href="https://devcharisma.aurorasystem.co.id/rab/auth/login" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap:balance;background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</a>
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper text-center mt-1">
                                            {# <i class="fa fa-book icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/image/aurora.png" alt="" class="img-card" style="width: 80px;height: 80px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card" style="text-wrap: balance; font-size: 18px;">BACAMETER APK</h5>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Bacameter</p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            <a href="https://drive.google.com/file/d/1G-ZOFe2DkToIFfXLM-QgYt8G6b4qpO0t/view?usp=sharing" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap:balance;background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</a>
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
                                        </div>
                                    </div>
                                </div>
                            </div>

							<div class="col">
                                <div class="card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper text-center mt-1">
                                            {# <i class="fa fa-book icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/portal/logo_kepegawaian.png" alt="" class="img-card" style="width: 80px;height: 80px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card" style="text-wrap: balance; font-size: 18px;">KEPEGAWAIAN APK</h3>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Kepegawaian</p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            <a href="https://drive.google.com/drive/u/0/folders/1_OwU1gA7dIlMo1EH7EYkzNlNcCSXM87u" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap:balance;background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</a>
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
                                        </div>
                                    </div>
                                </div>
                            </div>
							<!-- End Row 2 -->

						</div>

						<div class="row">
							<!-- Row 3 -->
							<div class="col"></div>

							<div class="col"></div>

                            <div class="col">
								<div class=" card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
									<div class="about-us-col">
										<div class="col-icon-wrapper mt-1 text-center mt-4">
											<img src="{{ url('assets') }}/image/sl_logo.png" alt="" class="img-card" style="width: 60px;height: 60px;">
										</div>
										<h5 class="col-title text-white font-bold text-center text-card" style="text-wrap: balance; font-size: 18px;">PENGADUAN</h5>
										<div class="col-details text-center">
											<p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Pengaduan</p>
										</div>
										<div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
											<a href="https://pengaduan.perumdatbwkotasukabumi.id/pengaduan-wibawa/auth/login" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap: balance; background-color: #1a446a; border-radius: 0.9rem; display: inline-block; margin: auto;">Buka Aplikasi</a>
										</div>
									</div>
								</div>
							</div>

                            <div class="col">
                                <div class="card ccard mt-1 btn" style="padding: 10px; display: flex; flex-direction: column; justify-content: flex-end;">
                                    <div class="about-us-col">
                                        <div class="col-icon-wrapper text-center mt-1">
                                            {# <i class="fa fa-book icon-card text-white"></i> #}
                                            <img src="{{ url('assets') }}/image/sl_logo.png" alt="" class="img-card" style="width: 60px;height: 60px;">
                                        </div>
                                        <h5 class="col-title text-white font-bold text-center text-card" style="text-wrap: balance; font-size: 18px;">PENGADUAN APK</h5>
                                        <div class="col-details text-center">
                                            <p class="text-white font-bold text-card" style="text-wrap: balance;">Sistem Informasi Pengaduan</p>
                                        </div>
                                        <div class="form-group col-sm-10 offset-sm-1" style="margin-top: auto;">
                                            <a href="https://drive.google.com/drive/folders/1CMSqjphNeiJmwXRwOZjLTqtZfQ0Bgg-z?usp=sharing" class="btn btn-block btn-lg px-4 btn-bold text-white shadow" style="font-size:10px;text-wrap:balance;background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</a>
                                            {# <button type="submit" class="btn btn-block btn-lg px-4 btn-bold mt-2 mb-4 text-white shadow" style="background-color: #1a446a; border-radius:0.9rem">Buka Aplikasi</button> #}
                                        </div>
                                    </div>
                                </div>
                            </div>

							<div class="col"></div>

							<div class="col"></div>
							<!-- End Row 3 -->

						</div>

					</div>
				</div>


			</div>
			<footer class="footer" style="position: bottom;">
				<div class="footer-inner" style="background-color: #1a446a;">
					<div class="h-100 pt-3 border-t-1 shadow-md">
						<span class="text-secondary-d2 text-white">Copyright © 2024</span>
					</div>
				</div>
			</footer>
		</div>

		
		<div class="modal fade" id="modalMenu" tabindex="-1" aria-labelledby="ModalMenu" aria-hidden="true">
			<div class="modal-dialog mw-100 w-75">
				<div class="modal-content" style="border: unset !important;">
					<div class="modal-header card-color-gradient" style="border-bottom: 1px solid #27282A;">
						<h5 class="modal-title p-15 text-100 text-white font-bolder" id="exampleModalLabel">
							<b>Pengaturan Hak Akses</b>
						</h5>
						<button type="button" class="close text-white" style="color: #ffffff !important;" data-dismiss="modal" aria-label="Close">
							<span style="color: #ffffff !important;" aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body card-color-gradient" style="">
						<div class="row">

							<div class="col-3">
								<span class="mb-4 menu-title">Pengguna</span>
								<br>
								<ul class="list-unstyled">
									<li class="media list-menu text-white" 
										onclick="(function(){ window.location.href = `{{ url('panel/User/Pengguna') }}`; return false; })(); return false;"
										style="cursor: pointer; align-items : center !important;">

										<span class="btn btn-sm btn-app mr-2"
											style="cursor: not-allowed; pointer-events: none; align-items : center !important;">

											<img src="{{ url('assets') }}/portal/user.png"
												style="width: 50px; height: 50px">
										</span>
										<div class="media-body" style="align-items : center !important;">
											<span class="mt-0 sub-menu-title"> Pengguna </span>
											<p class="sub-menu-desc">Kelola Data Pengguna </p>
										</div>
									</li>
								</ul>
								<ul class="list-unstyled">
									<li class="media list-menu text-white" 
										onclick="(function(){ window.location.href = `{{ url('panel/User/Hakakses') }}`; return false; })(); return false;"
										style="cursor: pointer; align-items : center !important;">

										<span class="btn btn-sm btn-app mr-2"
											style="cursor: not-allowed; pointer-events: none; align-items : center !important;">

											<img src="{{ url('assets') }}/portal/hak_akses.png"
												style="width: 50px; height: 50px">
										</span>
										<div class="media-body" style="align-items : center !important;">
											<span class="mt-0 sub-menu-title"> Hak Akses Aplikasi </span>
											<p class="sub-menu-desc">Setting Hak Akses Pengguna Ke Aplikasi </p>
										</div>
									</li>
								</ul>
							</div>

							<div class="col-3">
								<span class="mb-4 menu-title">SPI</span>
								<br>
								<ul class="list-unstyled">
									<li class="media list-menu text-white" 
										onclick="(function(){ /* window.location.href = `{{ url('spi/otoritas_menu') }}`; */ return false; })(); return false;"
										style="cursor: pointer; align-items : center !important;">

										<span class="btn btn-sm btn-app mr-2"
											style="cursor: not-allowed; pointer-events: none; align-items : center !important;">

											<img src="{{ url('assets') }}/portal/spi.png"
												style="width: 50px; height: 50px">
										</span>
										<div class="media-body" style="align-items : center !important;">
											<span class="mt-0 sub-menu-title"> Otoritas Menu </span>
											<p class="sub-menu-desc">Kelola Otoritas Menu SPI</p>
										</div>
									</li>
								</ul>
							</div>

							<div class="col-3">
								<span class="mb-4 menu-title">ASET</span>
								<br>
								<ul class="list-unstyled">
									<li class="media list-menu text-white" 
										onclick="(function(){ window.location.href = `{{ url('panel/Aset/Otoritasmenu') }}`; return false; })(); return false;"
										style="cursor: pointer; align-items : center !important;">

										<span class="btn btn-sm btn-app mr-2"
											style="cursor: not-allowed; pointer-events: none; align-items : center !important;">

											<img src="{{ url('assets') }}/portal/asset.png"
												style="width: 50px; height: 50px">
										</span>
										<div class="media-body" style="align-items : center !important;">
											<span class="mt-0 sub-menu-title"> Otoritas Menu </span>
											<p class="sub-menu-desc">Kelola Otoritas Menu Aset</p>
										</div>
									</li>
								</ul>
							</div>

							<div class="col-3">
								<span class="mb-4 menu-title" id="parent_1">SIKAP</span>
								<br>
								<ul class="list-unstyled">
									<li class="media list-menu text-white" id="2"
										onclick="(function(){ window.location.href = `{{ url('panel/Sikap/Otoritasmenu') }}`; return false; })(); return false;"
										style="cursor: pointer; align-items : center !important;">

										<span class="btn btn-sm btn-app mr-2"
											style="cursor: not-allowed; pointer-events: none; align-items : center !important;">

											<img src="{{ url('assets') }}/portal/sikap1.png"
												style="width: 50px; height: 50px">
										</span>
										<div class="media-body" style="align-items : center !important;">
											<span class="mt-0 sub-menu-title"> Otoritas Menu </span>
											<p class="sub-menu-desc">Kelola Otoritas Menu Sikap</p>
										</div>
									</li>
								</ul>
								<ul class="list-unstyled">
									<li class="media list-menu text-white" id="2"
										onclick="(function(){ window.location.href = `{{ url('panel/Sikap/Masterppk') }}`; return false; })(); return false;"
										style="cursor: pointer; align-items : center !important;">

										<span class="btn btn-sm btn-app mr-2"
											style="cursor: not-allowed; pointer-events: none; align-items : center !important;">

											<img src="{{ url('assets') }}/portal/logo_master_ppk.png"
												style="width: 50px; height: 50px">
										</span>
										<div class="media-body" style="align-items : center !important;">
											<span class="mt-0 sub-menu-title"> Master PPK </span>
											<p class="sub-menu-desc">Kelola Data Master PPK</p>
										</div>
									</li>
								</ul>
								
							</div>
							{#
							<label for="form-field-select-11 text-100 text-white font-bolder">
								Pilih Aplikasi
							</label>

							<select class="ace-select text-dark-m1 bgc-default-l5 bgc-h-warning-l3 brc-default-m3 brc-h-warning-m1" id="form-field-select-11">
								<option value="" selected disabled>Pilih</option>
								<option value="SPI">SPI</option>
								<option value="ASET">ASET</option>
								<option value="SIKAP">SIKAP</option>
							</select>
							#}
						</div>
					</div>
				</div>
			</div>
		</div>


		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	</body>
</html>
