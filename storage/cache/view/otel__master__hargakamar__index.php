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
			<?= $this->session->pdam->nama_aplikasi ?>
			-
    Master - Harga Kamar

		</title>

		<!-- ===============================================-->
		<!--    Favicons-->
		<!-- ===============================================-->
		<!-- Font Awesome --> <link rel="stylesheet"href="<?= $this->url->get('lib_independent') ?>/font-awesome.min.css"/> <link rel="apple-touch-icon" sizes="180x180" href="<?= $this->url->get('external_img') ?>/favicons/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="<?= $this->url->get('external_img') ?>/favicons/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="<?= $this->url->get('external_img') ?>/favicons/favicon-16x16.png">
		<link rel="shortcut icon" type="image/x-icon" href="<?= $this->url->get('external_img') ?>/favicons/favicon.ico">

		<link
		rel="manifest" href="<?= $this->url->get('external_img') ?>/favicons/manifest.json">
		<!-- Styles -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
		<link
		rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"/>
		<!-- Or for RTL support -->
		<link
		rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css"/>

		<!-- Scripts -->

		<link rel="stylesheet" href="<?= $this->url->get('lib_independent') ?>/jquery-confirm.min.css"/>
		<link rel="stylesheet" href="<?= $this->url->get('lib_independent') ?>/notyf.min.css">
		<meta name="msapplication-TileImage" content="<?= $this->url->get('external_img') ?>/favicons/mstile-150x150.png">
		<meta name="theme-color" content="#ffffff">
		<script src="<?= $this->url->get('vendors') ?>/simplebar/simplebar.min.js"></script>
		<script src="<?= $this->url->get('assets') ?>/js/config.js"></script>

		<!-- ===============================================-->
		<!--    Stylesheets-->
		<!-- ===============================================-->

		<!--- Specify Need Sometime Element -->
		<!------ End ---------------------->


		<link rel="preconnect" href="https://fonts.googleapis.com"> <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
		<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
		<link href="<?= $this->url->get('lib_independent') ?>/css2.css" rel="stylesheet">
		<link href="<?= $this->url->get('vendors') ?>/simplebar/simplebar.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
		<link href="<?= $this->url->get('assets') ?>/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
		<link href="<?= $this->url->get('assets') ?>/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
		<link href="<?= $this->url->get('assets') ?>/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
		<link href="<?= $this->url->get('assets') ?>/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
		<link href="<?= $this->url->get('vendors') ?>/choices/choices.min.css" rel="stylesheet">
		<link href="<?= $this->url->get('lib_independent') ?>/flatpickr.css" rel="stylesheet">
		<link href="<?= $this->url->get('lib_independent') ?>/style.css" rel="stylesheet">
		<link href="<?= $this->url->get('lib_independent') ?>/light.css" rel="stylesheet">
		<link href="<?= $this->url->get('vendors') ?>/summernote-0.9.0/summernote-bs5.min.css" rel="stylesheet">
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
}</script>

		<style>
			.dt-container .dt-processing {
				position: absolute;
				margin-left: auto;
				margin-right: auto;
				left: 0;
				right: 0;
				text-align: center !important;
				width: 100px !important;
				height: 50px !important;
				align-items: center;
				justify-content: center;
			}


			.dt-empty {
				text-align: center !important;
				vertical-align: middle !important;
				height: 200px; /* Sesuaikan tinggi */
				font-size: 16px;
				font-weight: bold;
				color: #888; /* Warna abu-abu */
			}
		</style>

		<style>:root[data-bs-theme="dark"] .select2-container--default .select2-selection--single
		{
			background-color: var(--phoenix-emphasis-bg) !important;
			color: var(--phoenix-body-color) !important;
			border-color: var(--phoenix-border-color) !important;
		}

		:root[data-bs-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered {
			background-color: var(--phoenix-emphasis-bg) !important;
			color: var(--phoenix-body-color) !important;
		}

		:root[data-bs-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__arrow b {
			border-top-color: var(--phoenix-body-color) !important;
		}

		:root[data-bs-theme="dark"] .select2-dropdown {
			background-color: #212529 !important;
			color: var(--phoenix-body-color) !important;
		}

		:root[data-bs-theme="dark"] .select2-results__option {
			color: var(--phoenix-body-color) !important;
			background-color: transparent !important;
		}

		:root[data-bs-theme="dark"] .select2-results__option--highlighted {
			background-color: var(--phoenix-emphasis-bg) !important;
		}

		:root[data-bs-theme="dark"] .select2-container--default .select2-results__option[aria-selected="true"] {
			background-color: var(--phoenix-emphasis-bg) !important;
			color: var(--phoenix-body-color) !important;
		}
		:root[data-bs-theme="dark"] .selected {
			background-color: #4761b6 !important;
		}

		.selected {
			background-color: #f3f3f3 !important;
		}

		.select2-container--default .select2-selection--single {
			height: calc(2.25rem + 2px);
			padding: 0.5rem;
			font-size: 0.8rem;
			border: 1px solid #ced4da;
			background-color: #ffffff;
		}

		.select2-container--default .select2-selection--single .select2-selection__placeholder {
			color: var(--phoenix-tertiary-color);
		}

		.select2-container .select2-selection--single .select2-selection__rendered {
			line-height: 1.5;
			font-size: 0.8rem;
		}

		.select2-dropdown {
			border-radius: 0.375rem;
			font-size: 0.8rem;
		}


		.dt-length {
			margin-top: 1rem !important;
			display: flex;
			gap: 0.5rem !important;
			justify-content: center !important;
			align-items: center !important;
		}

		.popover-lebar {
			max-width: 385px !important;
		}

		.hide {
			display: none !important;
		}

		.loading {
			position: fixed;
			z-index: 9999999;
			display: flex;
			justify-content: center;
			align-items: center;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
		}

		.loading:before {
			content: '';
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.5);
			backdrop-filter: blur(2px);
		}

		.loading:after {
			content: '';
			position: relative;
			width: 40px;
			height: 40px;
			border-radius: 50%;
			border: 3px solid rgba(255, 255, 255, 0.3);
			border-top-color: #fff;
			animation: spin 1s ease-in-out infinite;
		}

		@keyframes spin {
			0% {
				transform: rotate(0deg);
			}
			100% {
				transform: rotate(360deg);
			}
			}/* Dark mode support */:root[data-bs-theme="dark"] .loading:before
			{
				background: rgba(0, 0, 0, 0.7);
			}

			:root[data-bs-theme="dark"] .loading:after {
				border-color: rgba(255, 255, 255, 0.2);
				border-top-color: #fff;
			}

			.flatpickr-monthSelect-months {
				display: flex !important;
				flex-wrap: wrap !important;
				justify-content: space-between;
			}

			.flatpickr-monthSelect-month {
				width: calc(100% / 3 - 4px) !important;
				display: flex !important;
				justify-content: center;
			}

			.flatpickr-calendar {
				min-width: 350px; /* atau lebih besar */
			}

			table.dataTable td.dt-control {
				text-align: center;
				cursor: pointer;
			}
			table.dataTable td.dt-control:before {
				display: inline-block;
				box-sizing: border-box;
				content: "";
				border-top: 5px solid transparent;
				border-left: 10px solid rgba(0, 0, 0, 0.5);
				border-bottom: 5px solid transparent;
				border-right: 0 solid transparent;
			}
			table.dataTable tr.dt-hasChild td.dt-control:before {
				border-top: 10px solid rgba(0, 0, 0, 0.5);
				border-left: 5px solid transparent;
				border-bottom: 0 solid transparent;
				border-right: 5px solid transparent;
			}
			table.dataTable tfoot:empty {
				display: none;
			}

			html.dark table.dataTable td.dt-control:before,:root[data-bs-theme=dark] table.dataTable td.dt-control:before,:root[data-theme=dark] table.dataTable td.dt-control:before {
				border-left-color: rgba(255, 255, 255, 0.5);
			}
			html.dark table.dataTable tr.dt-hasChild td.dt-control:before,:root[data-bs-theme=dark] table.dataTable tr.dt-hasChild td.dt-control:before,:root[data-theme=dark] table.dataTable tr.dt-hasChild td.dt-control:before {
				border-top-color: rgba(255, 255, 255, 0.5);
				border-left-color: transparent;
			}

			.select2-container .select2-search--inline .select2-search__field {
				font-size: 70% !important;
			}

			.select2-container .select2-selection__clear {
				position: absolute !important;
				right: 0.75rem !important;
				left: auto !important;
				top: 50%;
				transform: translateY(-50%);
				z-index: 2;
			}
			.select2-container .select2-selection--single {
				position: relative;
			}


			.separator-dashed {
				border-bottom: 1px dashed #ebedf3;
			}
	</style>

	

</head>

<body>
	<!-- ===============================================-->
	<!--    Main Content-->
	<!-- ===============================================-->
		<div class="loading hide"> Loading&#8230;</div>
	<main class="main" id="top">
		<nav class="navbar navbar-vertical navbar-expand-lg" style="display:none;">
			<div
				class="collapse navbar-collapse" id="navbarVerticalCollapse">
				<!-- scrollbar removed-->
				<div class="navbar-vertical-content">

					
					

						
					<?php $this->macros['renderSubmenu'] = function($__p = null) { if (isset($__p[0])) { $menuList = $__p[0]; } else { if (array_key_exists("menuList", $__p)) { $menuList = $__p["menuList"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: menuList");  } } if (isset($__p[1])) { $parentId = $__p[1]; } else { if (array_key_exists("parentId", $__p)) { $parentId = $__p["parentId"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: parentId");  } }  ?><?php foreach ($menuList as $menu) { ?>
        <?php if ($menu->has_children) { ?>
            <li class="nav-item">
                <a class="nav-link dropdown-indicator" href="#nv-<?= $menu->id_menu ?>" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-<?= $menu->id_menu ?>">
                    <div class="d-flex align-items-center">
                        <div class="dropdown-indicator-icon-wrapper">
                            <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                        </div>
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="nav-link-icon">
                                <span data-feather="<?= $menu->icon ?>"></span>
                            </span>
                        <?php } ?>
                        <span class="nav-link-text"><?= $menu->nama_menu ?></span>
                    </div>
                </a>
                <div class="parent-wrapper">
                    <ul class="nav collapse parent" data-bs-parent="#<?= $parentId ?>" id="nv-<?= $menu->id_menu ?>">
                        <li class="collapsed-nav-item-title d-none"><?= $menu->nama_menu ?></li>
                        <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                    </ul>
                </div>
            </li>
        <?php } else { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="d-flex align-items-center">
                        <span class="nav-link-text"><?= $menu->nama_menu ?></span>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?><?php }; $this->macros['renderSubmenu'] = \Closure::bind($this->macros['renderSubmenu'], $this); ?>

<ul class="navbar-nav flex-column" id="navbarVerticalNav">
    <?php foreach ($this->menuModel->getUserMenuList() as $menu) { ?>
        <?php if ($menu->jenis == 0) { ?>
            <?php if (empty($menu->link_menu)) { ?>
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label"><?= $menu->nama_menu ?></p>
                    <hr class="navbar-vertical-line"/>
                    <?php if ($menu->has_children) { ?>
                        <!-- parent pages-->
                        <?php foreach ($menu->children as $mainMenu) { ?>
                            <div class="nav-item-wrapper">
                                <?php if ($mainMenu->has_children) { ?>
                                    <a class="nav-link dropdown-indicator label-1" href="#nv-<?= $mainMenu->id_menu ?>" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-<?= $mainMenu->id_menu ?>">
                                        <div class="d-flex align-items-center">
                                            <div class="dropdown-indicator-icon-wrapper">
                                                <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                            </div>
                                            <?php if (!empty($mainMenu->icon)) { ?>
                                                <span class="nav-link-icon">
                                                    <span data-feather="<?= $mainMenu->icon ?>"></span>
                                                </span>
                                            <?php } ?>
                                            <span class="nav-link-text-wrapper">
                                                <span class="nav-link-text"><?= $mainMenu->nama_menu ?></span>
                                            </span>
                                        </div>
                                    </a>
                                    <div class="parent-wrapper label-1">
                                        <ul class="nav collapse parent" data-bs-parent="#<?= $menu->id_menu ?>" id="nv-<?= $mainMenu->id_menu ?>">
                                            <li class="collapsed-nav-item-title d-none"><?= $mainMenu->nama_menu ?></li>
                                            <?= $this->callMacro('renderSubmenu', [$mainMenu->children, $mainMenu->id_menu]) ?>
                                        </ul>
                                    </div>
                                <?php } else { ?>
                                    <a class="nav-link label-1" href="<?= $this->url->get($mainMenu->link_menu) ?>" role="button">
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($mainMenu->icon)) { ?>
                                                <span class="nav-link-icon">
                                                    <span data-feather="<?= $mainMenu->icon ?>"></span>
                                                </span>
                                            <?php } ?>
                                            <span class="nav-link-text-wrapper">
                                                <span class="nav-link-text"><?= $mainMenu->nama_menu ?></span>
                                            </span>
                                        </div>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </li>
            <?php } else { ?>
                <li class="nav-item">
                    <!-- parent pages-->
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="<?= $this->url->get($menu->link_menu) ?>" role="button">
                            <div class="d-flex align-items-center">
                                <?php if (!empty($menu->icon)) { ?>
                                    <span class="nav-link-icon">
                                        <span data-feather="<?= $menu->icon ?>"></span>
                                    </span>
                                <?php } ?>
                                <span class="nav-link-text-wrapper">
                                    <span class="nav-link-text"><?= $menu->nama_menu ?></span>
                                </span>
                            </div>
                        </a>
                    </div>
                </li>
            <?php } ?>
        <?php } ?>
    <?php } ?>
</ul>

<script>

(function() {
    // Find all navigation links in the vertical nav
    let elements = document.querySelectorAll('#navbarVerticalNav a.nav-link');
    let found = null;

    // Find the best matching link based on current URL
    for (let el of elements) {
        const url = window.location.toString();
        if (url.startsWith(el.href)) {
            if (found == null || found.href.length < el.href.length) {
                found = el;
            }
        }
    }

    function setActiveNavigation(element) {
        let parent = element.parentElement;
        let firstItemFound = false;

        // Traverse up the DOM tree until we reach the main nav
        while (parent != null && !parent.matches('#navbarVerticalNav')) {
            // Handle nav items
            if (parent.classList.contains('nav-item')) {
                parent.classList.add('active');
                if (firstItemFound) {
                    parent.classList.add('show');
                }
                firstItemFound = true;
            }
            
            // Handle collapsible menus
            if (parent.classList.contains('collapse')) {
                parent.classList.add('show');
                // Find and update the dropdown indicator if it exists
                const indicator = parent.parentElement.querySelector('.dropdown-indicator');
                if (indicator) {
                    indicator.setAttribute('aria-expanded', 'true');
                }
            }
            
            parent = parent.parentElement;
        }
    }

    // Apply active classes if a matching link was found
    if (found) {
        found.classList.add('active');
        setActiveNavigation(found);
    }
})();

</script>
				

			</div>
		</div>
		</div>
			<div class="navbar-vertical-footer"> <button class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center">
				<span class="uil uil-left-arrow-to-left fs-8"></span>
				<span class="uil uil-arrow-from-right fs-8"></span>
				<span class="navbar-vertical-footer-text ms-2">Collapsed View</span>
			</button>
		</div>
	</nav>
	<nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault" style="display:none;">
		<div class="collapse navbar-collapse justify-content-between">
			<div class="navbar-logo">
				<button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
					<span class="navbar-toggle-icon">
						<span class="toggle-line"></span>
					</span>
				</button>
				<a class="navbar-brand me-1 me-sm-3" href="<?= $this->url->get('dashboard') ?>">
					<div class="d-flex align-items-center">
						<div class="d-flex align-items-center"><img src="<?= $this->url->get('external_img') ?>/logo-pdam-13.png" alt="<?= $this->session->pdam->nama_aplikasi ?>" width="27"/>
							<h5 class="logo-text ms-2 d-none d-sm-block"><?= $this->session->pdam->nama_aplikasi ?></h5>
							<h6 class="logo-text fs-8 fw-bold">
								&nbsp; &nbsp; Login :
								<?= $this->session->periode ?>
							</h6>
						</div>
					</div>
				</a>
			</div>
			
			<ul class="navbar-nav navbar-nav-icons flex-row">
				<li class="nav-item">
					<div class="theme-control-toggle fa-icon-wait px-2"><input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle"/><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;">
							<span class="icon" data-feather="moon"></span>
						</label>
						<label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;">
							<span class="icon" data-feather="sun"></span>
						</label>
					</div>
				</li>
				
				<li class="nav-item dropdown">
					<a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
						<div class="avatar avatar-l ">
							<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
						</div>
					</a>
					<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
						<div class="card position-relative border-0">
							<div class="card-body p-0">
								<div class="text-center pt-4 pb-3">
									<div class="avatar avatar-xl ">
										<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
									</div>
									<h6 class="mt-2 text-body-emphasis"><?= $this->session->pdam->nama_pdam ?></h6>
								</div>
							</div>
							<div class="overflow-auto scrollbar" style="height: 7rem;">
								<ul class="nav d-flex flex-column mb-2 pb-1">
									<li class="nav-item">
										<a class="nav-link px-3 d-block" href="<?= $this->url->get('dashboard') ?>">
											<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
									</li>
									<li class="nav-item">
										<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/profile-aplikasi') ?>">
											<span class="me-2 text-body align-bottom" data-feather="aperture"></span>
											<span>Profile Perusahaan</span>
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/ganti-password') ?>">
											<span class="me-2 text-body align-bottom" data-feather="lock"></span>Ganti Password</a>
									</li>
									<!-- <li class="nav-item">
																																								<a class="nav-link px-3 d-block" href="#!">
																																									<span class="me-2 text-body align-bottom"
																																										data-feather="settings"></span>Settings &amp; Privacy
																																								</a>
																																							</li>
																																							<li class="nav-item">
																																								<a class="nav-link px-3 d-block" href="#!">
																																									<span class="me-2 text-body align-bottom"
																																										data-feather="help-circle"></span>Help Center</a>
																																							</li>
																																							<li class="nav-item">
																																								<a class="nav-link px-3 d-block" href="#!">
																																									<span class="me-2 text-body align-bottom"
																																										data-feather="globe"></span>Language</a>
																																							</li> -->
								</ul>
							</div>
							<div
								class="card-footer p-0 border-top border-translucent">
								<!-- <ul class="nav d-flex flex-column my-3">
																																		<li class="nav-item">
																																			<a class="nav-link px-3 d-block" href="#!">
																																				<span class="me-2 text-body align-bottom"
																																					data-feather="user-plus"></span>Add another account</a>
																																		</li>
																																	</ul>
																																	<hr /> -->
								<div class="px-3 my-3">
									<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('') ?><?= $this->session->pdam->direktori.'/auth/logout'?>">
										<span class="me-2" data-feather="log-out"></span>Sign out</a>
								</div>
								<div
									class="my-2 text-center fw-bold fs-10 text-body-quaternary"><!--
																																								<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a
																																									class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a
																																									class="text-body-quaternary ms-1" href="#!">Cookies</a>
																																								-->
								</div>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</nav>

	<nav class="navbar navbar-top navbar-slim fixed-top navbar-expand" id="topNavSlim" style="display:none;">
		<div class="collapse navbar-collapse justify-content-between">
			<div class="navbar-logo">
				<button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
					<span class="navbar-toggle-icon">
						<span class="toggle-line"></span>
					</span>
				</button>
				<a class="navbar-brand navbar-brand" href="../index.html">phoenix topNavSlim
					<span class="text-body-highlight d-none d-sm-inline">slim</span>
				</a>
			</div>
			<ul class="navbar-nav navbar-nav-icons flex-row">
				<li class="nav-item">
					<div class="theme-control-toggle fa-ion-wait pe-2 theme-control-toggle-slim"><input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="phoenixTheme" value="dark"/><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme">
							<span class="d-none d-sm-flex flex-center" style="height:16px;width:16px;">
								<span class="me-1 icon" data-feather="moon"></span>
							</span>
							<span class="fs-9 fw-bold">Dark</span>
						</label>
						<label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme">
							<span class="d-none d-sm-flex flex-center" style="height:16px;width:16px;">
								<span class="me-1 icon" data-feather="sun"></span>
							</span>
							<span class="fs-9 fw-bold">Light</span>
						</label>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link lh-1 pe-0 white-space-nowrap" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false">Olivia
						<span class="d-inline-block" style="height:10.2px;width:10.2px;">
							<span class="fa-solid fa-chevron-down fs-10"></span>
						</span>
					</a>
					<!--
<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl">
					<img class="rounded-circle" src="<?= $this->url->get('assets') ?>/img/team/72x72/57.webp" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis">Jerry Seinfield</h6>
			</div>
			<div class="mb-3 mx-3">
				<input class="form-control form-control-sm" id="statusUpdateInput" type="text" placeholder="Update your status"/>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 10rem">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user"></span>
						<span>Profile</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Posts &amp; Activity</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="settings"></span>Settings &amp; Privacy
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="help-circle"></span>Help Center</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="globe"></span>Language</a>
				</li>
			</ul>
		</div>
		<div class="card-footer p-0 border-top border-translucent">
			<ul class="nav d-flex flex-column my-3">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user-plus"></span>Add another account</a>
				</li>
			</ul>
			<hr/>
			<div class="px-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('auth/logout') ?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div class="my-2 text-center fw-bold fs-10 text-body-quaternary">
				<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a class="text-body-quaternary ms-1" href="#!">Cookies</a>
			</div>
		</div>
	</div>
</div>
-->

<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl ">
					<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis"><?= $this->session->pdam->nama_pdam ?></h6>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 7rem;">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('dashboard') ?>">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/profile-aplikasi') ?>">
						<span class="me-2 text-body align-bottom" data-feather="aperture"></span>
						<span>Profile Perusahaan</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/ganti-password') ?>">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Ganti Password</a>
				</li>
				<!-- <li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="settings"></span>Settings &amp; Privacy
																	</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="help-circle"></span>Help Center</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="globe"></span>Language</a>
																</li> -->
			</ul>
		</div>
		<div
			class="card-footer p-0 border-top border-translucent">
			<!-- <ul class="nav d-flex flex-column my-3">
													<li class="nav-item">
														<a class="nav-link px-3 d-block" href="#!">
															<span class="me-2 text-body align-bottom"
																data-feather="user-plus"></span>Add another account</a>
													</li>
												</ul>
												<hr /> -->
			<div class="px-3 my-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('') ?><?= $this->session->pdam->direktori.'/auth/logout'?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div
				class="my-2 text-center fw-bold fs-10 text-body-quaternary"><!--
															<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a
																class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a
																class="text-body-quaternary ms-1" href="#!">Cookies</a>
															-->
			</div>
		</div>
	</div>
</div>

				</li>
			</ul>
		</div>
	</nav>

	<nav class="navbar navbar-top fixed-top navbar-expand-lg" id="navbarTop" style="display:none;">
		<div class="navbar-logo">
			<button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse" aria-expanded="false" aria-label="Toggle Navigation">
				<span class="navbar-toggle-icon">
					<span class="toggle-line"></span>
				</span>
			</button>
			<a class="navbar-brand me-1 me-sm-3" href="<?= $this->url->get('dashboard') ?>">
				<div class="d-flex align-items-center">
					<div class="d-flex align-items-center"><img src="<?= $this->url->get('external_img') ?>/logo-pdam-13.png" alt="<?= $this->session->pdam->nama_aplikasi ?>" width="27"/>
						<h5 class="logo-text ms-2 d-none d-sm-block">
							<?= $this->session->pdam->nama_aplikasi ?>
						</h5>
						<h6 class="logo-text fs-8 fw-bold">
							&nbsp; &nbsp; Login :
							<?= $this->session->periode ?>
						</h6>
					</div>
				</div>
			</a>
		</div>
		<div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center" id="navbarTopCollapse">
			<?php $this->macros['renderSubmenu'] = function($__p = null) { if (isset($__p[0])) { $menuList = $__p[0]; } else { if (array_key_exists("menuList", $__p)) { $menuList = $__p["menuList"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: menuList");  } } if (isset($__p[1])) { $parentId = $__p[1]; } else { if (array_key_exists("parentId", $__p)) { $parentId = $__p["parentId"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: parentId");  } }  ?><?php foreach ($menuList as $menu) { ?>
        <?php if ($menu->has_children) { ?>
            <li class="dropdown">
                <a class="dropdown-item dropdown-toggle" id="<?= $menu->id_menu ?>" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <div class="dropdown-item-wrapper">
                        <span class="uil fs-8 uil-angle-right lh-1 dropdown-indicator-icon"></span>
                        <span>
                            <?php if (!empty($menu->icon)) { ?>
                                <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                            <?php } ?>
                            <?= $menu->nama_menu ?>
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                </ul>
            </li>
        <?php } else { ?>
            <li>
                <a class="dropdown-item" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?><?php }; $this->macros['renderSubmenu'] = \Closure::bind($this->macros['renderSubmenu'], $this); ?><ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
    <?php foreach ($this->menuModel->getUserMenuList() as $menu) { ?>
        <?php if ($menu->jenis == 0 && empty($menu->link_menu)) { ?>
            <?php if ($menu->has_children) { ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="uil fs-8 me-2 uil-<?= $menu->icon_uil ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                    </ul>
                </li>
            <?php } ?>
        <?php } elseif ($menu->jenis == 0 && !empty($menu->link_menu)) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?>
</ul>

<script>
(function() {
    // Find all navigation links in the horizontal nav
    let elements = document.querySelectorAll('.navbar-nav-top a.nav-link, .navbar-nav-top a.dropdown-item');
    let found = null;

    // Find the best matching link based on current URL
    for (let el of elements) {
        // Ignore links with href="#!" or href="#"
        if (!el.href || el.getAttribute('href') === '#!' || el.getAttribute('href') === '#') continue;
        const url = window.location.toString();
        if (url.startsWith(el.href)) {
            if (found == null || found.href.length < el.href.length) {
                found = el;
            }
        }
    }

    function setActiveNavigation(element) {
        let parent = element.parentElement;
        let firstItemFound = false;

        // Traverse up the DOM tree until we reach the main nav
        while (parent != null && !parent.classList.contains('navbar-nav-top')) {
            // Handle nav items
            if (parent.classList.contains('nav-item')) {
                parent.classList.add('active');
                if (firstItemFound) {
                    // parent.classList.add('show');
                }
                firstItemFound = true;
            }
            
            // Handle dropdown menus
            if (parent.classList.contains('dropdown')) {
                // parent.classList.add('show');
                // Find the dropdown-toggle and set aria-expanded
                let toggle = parent.querySelector('.dropdown-toggle');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'true');
                }
            }
            
            // Handle dropdown-menu - add 'show' class for 3-level support
            if (parent.classList.contains('dropdown-menu')) {
                // parent.classList.add('show');
            }
            
            parent = parent.parentElement;
        }
    }

    // Apply active classes if a matching link was found
    if (found) {
        found.classList.add('active');
        setActiveNavigation(found);
    }
})();
</script>

		</div>
		<ul class="navbar-nav navbar-nav-icons flex-row">
			<li class="nav-item">
				<div class="theme-control-toggle fa-icon-wait px-2">
					<input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle"/>
					<label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;">
						<span class="icon" data-feather="moon"></span>
					</label>
					<label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;">
						<span class="icon" data-feather="sun"></span>
					</label>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
					<div class="avatar avatar-l ">
						<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
					</div>
				</a>
				<!--
<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl">
					<img class="rounded-circle" src="<?= $this->url->get('assets') ?>/img/team/72x72/57.webp" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis">Jerry Seinfield</h6>
			</div>
			<div class="mb-3 mx-3">
				<input class="form-control form-control-sm" id="statusUpdateInput" type="text" placeholder="Update your status"/>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 10rem">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user"></span>
						<span>Profile</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Posts &amp; Activity</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="settings"></span>Settings &amp; Privacy
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="help-circle"></span>Help Center</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="globe"></span>Language</a>
				</li>
			</ul>
		</div>
		<div class="card-footer p-0 border-top border-translucent">
			<ul class="nav d-flex flex-column my-3">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user-plus"></span>Add another account</a>
				</li>
			</ul>
			<hr/>
			<div class="px-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('auth/logout') ?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div class="my-2 text-center fw-bold fs-10 text-body-quaternary">
				<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a class="text-body-quaternary ms-1" href="#!">Cookies</a>
			</div>
		</div>
	</div>
</div>
-->

<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl ">
					<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis"><?= $this->session->pdam->nama_pdam ?></h6>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 7rem;">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('dashboard') ?>">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/profile-aplikasi') ?>">
						<span class="me-2 text-body align-bottom" data-feather="aperture"></span>
						<span>Profile Perusahaan</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/ganti-password') ?>">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Ganti Password</a>
				</li>
				<!-- <li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="settings"></span>Settings &amp; Privacy
																	</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="help-circle"></span>Help Center</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="globe"></span>Language</a>
																</li> -->
			</ul>
		</div>
		<div
			class="card-footer p-0 border-top border-translucent">
			<!-- <ul class="nav d-flex flex-column my-3">
													<li class="nav-item">
														<a class="nav-link px-3 d-block" href="#!">
															<span class="me-2 text-body align-bottom"
																data-feather="user-plus"></span>Add another account</a>
													</li>
												</ul>
												<hr /> -->
			<div class="px-3 my-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('') ?><?= $this->session->pdam->direktori.'/auth/logout'?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div
				class="my-2 text-center fw-bold fs-10 text-body-quaternary"><!--
															<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a
																class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a
																class="text-body-quaternary ms-1" href="#!">Cookies</a>
															-->
			</div>
		</div>
	</div>
</div>

			</li>
		</ul>
	</nav>

	<nav class="navbar navbar-top navbar-slim justify-content-between fixed-top navbar-expand-lg" id="navbarTopSlim" style="display:none;">
		<div class="navbar-logo">
			<button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse" aria-expanded="false" aria-label="Toggle Navigation">
				<span class="navbar-toggle-icon">
					<span class="toggle-line"></span>
				</span>
			</button>
			<a class="navbar-brand navbar-brand" href="../index.html">phoenix navbarTopSlim
				<span class="text-body-highlight d-none d-sm-inline">slim</span>
			</a>
		</div>
		<div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center" id="navbarTopCollapse">
			<?php $this->macros['renderSubmenu'] = function($__p = null) { if (isset($__p[0])) { $menuList = $__p[0]; } else { if (array_key_exists("menuList", $__p)) { $menuList = $__p["menuList"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: menuList");  } } if (isset($__p[1])) { $parentId = $__p[1]; } else { if (array_key_exists("parentId", $__p)) { $parentId = $__p["parentId"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: parentId");  } }  ?><?php foreach ($menuList as $menu) { ?>
        <?php if ($menu->has_children) { ?>
            <li class="dropdown">
                <a class="dropdown-item dropdown-toggle" id="<?= $menu->id_menu ?>" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <div class="dropdown-item-wrapper">
                        <span class="uil fs-8 uil-angle-right lh-1 dropdown-indicator-icon"></span>
                        <span>
                            <?php if (!empty($menu->icon)) { ?>
                                <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                            <?php } ?>
                            <?= $menu->nama_menu ?>
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                </ul>
            </li>
        <?php } else { ?>
            <li>
                <a class="dropdown-item" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?><?php }; $this->macros['renderSubmenu'] = \Closure::bind($this->macros['renderSubmenu'], $this); ?><ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
    <?php foreach ($this->menuModel->getUserMenuList() as $menu) { ?>
        <?php if ($menu->jenis == 0 && empty($menu->link_menu)) { ?>
            <?php if ($menu->has_children) { ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="uil fs-8 me-2 uil-<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                    </ul>
                </li>
            <?php } ?>
        <?php } elseif ($menu->jenis == 0 && !empty($menu->link_menu)) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?>
</ul>

		</div>
		<ul class="navbar-nav navbar-nav-icons flex-row">
			<li class="nav-item">
				<div class="theme-control-toggle fa-ion-wait pe-2 theme-control-toggle-slim"><input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="phoenixTheme" value="dark"/><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme">
						<span class="d-none d-sm-flex flex-center" style="height:16px;width:16px;">
							<span class="me-1 icon" data-feather="moon"></span>
						</span>
						<span class="fs-9 fw-bold">Dark</span>
					</label>
					<label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme">
						<span class="d-none d-sm-flex flex-center" style="height:16px;width:16px;">
							<span class="me-1 icon" data-feather="sun"></span>
						</span>
						<span class="fs-9 fw-bold">Light</span>
					</label>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link lh-1 pe-0 white-space-nowrap" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false">Olivia
					<span class="d-inline-block" style="height:10.2px;width:10.2px;">
						<span class="fa-solid fa-chevron-down fs-10"></span>
					</span>
				</a>
				<!--
<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl">
					<img class="rounded-circle" src="<?= $this->url->get('assets') ?>/img/team/72x72/57.webp" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis">Jerry Seinfield</h6>
			</div>
			<div class="mb-3 mx-3">
				<input class="form-control form-control-sm" id="statusUpdateInput" type="text" placeholder="Update your status"/>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 10rem">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user"></span>
						<span>Profile</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Posts &amp; Activity</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="settings"></span>Settings &amp; Privacy
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="help-circle"></span>Help Center</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="globe"></span>Language</a>
				</li>
			</ul>
		</div>
		<div class="card-footer p-0 border-top border-translucent">
			<ul class="nav d-flex flex-column my-3">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user-plus"></span>Add another account</a>
				</li>
			</ul>
			<hr/>
			<div class="px-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('auth/logout') ?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div class="my-2 text-center fw-bold fs-10 text-body-quaternary">
				<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a class="text-body-quaternary ms-1" href="#!">Cookies</a>
			</div>
		</div>
	</div>
</div>
-->

<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl ">
					<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis"><?= $this->session->pdam->nama_pdam ?></h6>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 7rem;">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('dashboard') ?>">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/profile-aplikasi') ?>">
						<span class="me-2 text-body align-bottom" data-feather="aperture"></span>
						<span>Profile Perusahaan</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/ganti-password') ?>">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Ganti Password</a>
				</li>
				<!-- <li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="settings"></span>Settings &amp; Privacy
																	</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="help-circle"></span>Help Center</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="globe"></span>Language</a>
																</li> -->
			</ul>
		</div>
		<div
			class="card-footer p-0 border-top border-translucent">
			<!-- <ul class="nav d-flex flex-column my-3">
													<li class="nav-item">
														<a class="nav-link px-3 d-block" href="#!">
															<span class="me-2 text-body align-bottom"
																data-feather="user-plus"></span>Add another account</a>
													</li>
												</ul>
												<hr /> -->
			<div class="px-3 my-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('') ?><?= $this->session->pdam->direktori.'/auth/logout'?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div
				class="my-2 text-center fw-bold fs-10 text-body-quaternary"><!--
															<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a
																class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a
																class="text-body-quaternary ms-1" href="#!">Cookies</a>
															-->
			</div>
		</div>
	</div>
</div>

			</li>
		</ul>
	</nav>

	<nav class="navbar navbar-top fixed-top navbar-expand-lg" id="navbarCombo" data-navbar-top="combo" data-move-target="#navbarVerticalNav" style="display:none;">
		<div class="navbar-logo">
			<button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
				<span class="navbar-toggle-icon">
					<span class="toggle-line"></span>
				</span>
			</button>
			<a class="navbar-brand me-1 me-sm-3" href="<?= $this->url->get('dashboard') ?>">
				<div class="d-flex align-items-center">
					<div class="d-flex align-items-center"><img src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdamid-'.$this->session->pdam->id.'.png'; ?>" alt="<?= $this->session->pdam->nama_aplikasi ?>" width="27"/>
						<h5 class="logo-text ms-2 d-none d-sm-block"><?= $this->session->pdam->nama_aplikasi ?></h5>
						<h6 class="logo-text fs-8 fw-bold">
							&nbsp; &nbsp; Login :
							<?= $this->session->periode ?>
						</h6>
					</div>
				</div>
			</a>
		</div>
		<div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center" id="navbarTopCollapse">
			<?php $this->macros['renderSubmenu'] = function($__p = null) { if (isset($__p[0])) { $menuList = $__p[0]; } else { if (array_key_exists("menuList", $__p)) { $menuList = $__p["menuList"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: menuList");  } } if (isset($__p[1])) { $parentId = $__p[1]; } else { if (array_key_exists("parentId", $__p)) { $parentId = $__p["parentId"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: parentId");  } }  ?><?php foreach ($menuList as $menu) { ?>
        <?php if ($menu->has_children) { ?>
            <li class="dropdown">
                <a class="dropdown-item dropdown-toggle" id="<?= $menu->id_menu ?>" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <div class="dropdown-item-wrapper">
                        <span class="uil fs-8 uil-angle-right lh-1 dropdown-indicator-icon"></span>
                        <span>
                            <?php if (!empty($menu->icon)) { ?>
                                <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                            <?php } ?>
                            <?= $menu->nama_menu ?>
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                </ul>
            </li>
        <?php } else { ?>
            <li>
                <a class="dropdown-item" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?><?php }; $this->macros['renderSubmenu'] = \Closure::bind($this->macros['renderSubmenu'], $this); ?><ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
    <?php foreach ($this->menuModel->getUserMenuList() as $menu) { ?>
        <?php if ($menu->jenis == 0 && empty($menu->link_menu)) { ?>
            <?php if ($menu->has_children) { ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="uil fs-8 me-2 uil-<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                    </ul>
                </li>
            <?php } ?>
        <?php } elseif ($menu->jenis == 0 && !empty($menu->link_menu)) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?>
</ul>

		</div>
		<ul class="navbar-nav navbar-nav-icons flex-row">
			<li class="nav-item">
				<div class="theme-control-toggle fa-icon-wait px-2">
					<input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle"/>
					<label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;">
						<span class="icon" data-feather="moon"></span>
					</label>
					<label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;">
						<span class="icon" data-feather="sun"></span>
					</label>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
					<div class="avatar avatar-l ">
						<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
					</div>
				</a>
				<!--
<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl">
					<img class="rounded-circle" src="<?= $this->url->get('assets') ?>/img/team/72x72/57.webp" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis">Jerry Seinfield</h6>
			</div>
			<div class="mb-3 mx-3">
				<input class="form-control form-control-sm" id="statusUpdateInput" type="text" placeholder="Update your status"/>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 10rem">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user"></span>
						<span>Profile</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Posts &amp; Activity</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="settings"></span>Settings &amp; Privacy
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="help-circle"></span>Help Center</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="globe"></span>Language</a>
				</li>
			</ul>
		</div>
		<div class="card-footer p-0 border-top border-translucent">
			<ul class="nav d-flex flex-column my-3">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user-plus"></span>Add another account</a>
				</li>
			</ul>
			<hr/>
			<div class="px-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('auth/logout') ?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div class="my-2 text-center fw-bold fs-10 text-body-quaternary">
				<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a class="text-body-quaternary ms-1" href="#!">Cookies</a>
			</div>
		</div>
	</div>
</div>
-->

<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl ">
					<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis"><?= $this->session->pdam->nama_pdam ?></h6>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 7rem;">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('dashboard') ?>">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/profile-aplikasi') ?>">
						<span class="me-2 text-body align-bottom" data-feather="aperture"></span>
						<span>Profile Perusahaan</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/ganti-password') ?>">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Ganti Password</a>
				</li>
				<!-- <li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="settings"></span>Settings &amp; Privacy
																	</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="help-circle"></span>Help Center</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="globe"></span>Language</a>
																</li> -->
			</ul>
		</div>
		<div
			class="card-footer p-0 border-top border-translucent">
			<!-- <ul class="nav d-flex flex-column my-3">
													<li class="nav-item">
														<a class="nav-link px-3 d-block" href="#!">
															<span class="me-2 text-body align-bottom"
																data-feather="user-plus"></span>Add another account</a>
													</li>
												</ul>
												<hr /> -->
			<div class="px-3 my-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('') ?><?= $this->session->pdam->direktori.'/auth/logout'?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div
				class="my-2 text-center fw-bold fs-10 text-body-quaternary"><!--
															<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a
																class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a
																class="text-body-quaternary ms-1" href="#!">Cookies</a>
															-->
			</div>
		</div>
	</div>
</div>

			</li>
		</ul>
	</nav>

	<nav class="navbar navbar-top fixed-top navbar-slim justify-content-between navbar-expand-lg" id="navbarComboSlim" data-navbar-top="combo" data-move-target="#navbarVerticalNav" style="display:none;">
		<div class="navbar-logo">
			<button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
				<span class="navbar-toggle-icon">
					<span class="toggle-line"></span>
				</span>
			</button>
			<a class="navbar-brand navbar-brand" href="../index.html">phoenix navbarVerticalNav
				<span class="text-body-highlight d-none d-sm-inline">slim</span>
			</a>
		</div>
		<div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center" id="navbarTopCollapse">
			<?php $this->macros['renderSubmenu'] = function($__p = null) { if (isset($__p[0])) { $menuList = $__p[0]; } else { if (array_key_exists("menuList", $__p)) { $menuList = $__p["menuList"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: menuList");  } } if (isset($__p[1])) { $parentId = $__p[1]; } else { if (array_key_exists("parentId", $__p)) { $parentId = $__p["parentId"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: parentId");  } }  ?><?php foreach ($menuList as $menu) { ?>
        <?php if ($menu->has_children) { ?>
            <li class="dropdown">
                <a class="dropdown-item dropdown-toggle" id="<?= $menu->id_menu ?>" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <div class="dropdown-item-wrapper">
                        <span class="uil fs-8 uil-angle-right lh-1 dropdown-indicator-icon"></span>
                        <span>
                            <?php if (!empty($menu->icon)) { ?>
                                <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                            <?php } ?>
                            <?= $menu->nama_menu ?>
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                </ul>
            </li>
        <?php } else { ?>
            <li>
                <a class="dropdown-item" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?><?php }; $this->macros['renderSubmenu'] = \Closure::bind($this->macros['renderSubmenu'], $this); ?><ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
    <?php foreach ($this->menuModel->getUserMenuList() as $menu) { ?>
        <?php if ($menu->jenis == 0 && empty($menu->link_menu)) { ?>
            <?php if ($menu->has_children) { ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="uil fs-8 me-2 uil-<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                    </ul>
                </li>
            <?php } ?>
        <?php } elseif ($menu->jenis == 0 && !empty($menu->link_menu)) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?>
</ul>

		</div>
		<ul class="navbar-nav navbar-nav-icons flex-row">
			<li class="nav-item">
				<div class="theme-control-toggle fa-ion-wait pe-2 theme-control-toggle-slim"><input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="phoenixTheme" value="dark"/><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme">
						<span class="d-none d-sm-flex flex-center" style="height:16px;width:16px;">
							<span class="me-1 icon" data-feather="moon"></span>
						</span>
						<span class="fs-9 fw-bold">Dark</span>
					</label>
					<label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme">
						<span class="d-none d-sm-flex flex-center" style="height:16px;width:16px;">
							<span class="me-1 icon" data-feather="sun"></span>
						</span>
						<span class="fs-9 fw-bold">Light</span>
					</label>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link lh-1 pe-0 white-space-nowrap" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false">Olivia
					<span class="d-inline-block" style="height:10.2px;width:10.2px;">
						<span class="fa-solid fa-chevron-down fs-10"></span>
					</span>
				</a>
				<!--
<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl">
					<img class="rounded-circle" src="<?= $this->url->get('assets') ?>/img/team/72x72/57.webp" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis">Jerry Seinfield</h6>
			</div>
			<div class="mb-3 mx-3">
				<input class="form-control form-control-sm" id="statusUpdateInput" type="text" placeholder="Update your status"/>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 10rem">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user"></span>
						<span>Profile</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Posts &amp; Activity</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="settings"></span>Settings &amp; Privacy
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="help-circle"></span>Help Center</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="globe"></span>Language</a>
				</li>
			</ul>
		</div>
		<div class="card-footer p-0 border-top border-translucent">
			<ul class="nav d-flex flex-column my-3">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user-plus"></span>Add another account</a>
				</li>
			</ul>
			<hr/>
			<div class="px-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('auth/logout') ?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div class="my-2 text-center fw-bold fs-10 text-body-quaternary">
				<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a class="text-body-quaternary ms-1" href="#!">Cookies</a>
			</div>
		</div>
	</div>
</div>
-->

<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl ">
					<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis"><?= $this->session->pdam->nama_pdam ?></h6>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 7rem;">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('dashboard') ?>">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/profile-aplikasi') ?>">
						<span class="me-2 text-body align-bottom" data-feather="aperture"></span>
						<span>Profile Perusahaan</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/ganti-password') ?>">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Ganti Password</a>
				</li>
				<!-- <li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="settings"></span>Settings &amp; Privacy
																	</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="help-circle"></span>Help Center</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="globe"></span>Language</a>
																</li> -->
			</ul>
		</div>
		<div
			class="card-footer p-0 border-top border-translucent">
			<!-- <ul class="nav d-flex flex-column my-3">
													<li class="nav-item">
														<a class="nav-link px-3 d-block" href="#!">
															<span class="me-2 text-body align-bottom"
																data-feather="user-plus"></span>Add another account</a>
													</li>
												</ul>
												<hr /> -->
			<div class="px-3 my-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('') ?><?= $this->session->pdam->direktori.'/auth/logout'?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div
				class="my-2 text-center fw-bold fs-10 text-body-quaternary"><!--
															<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a
																class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a
																class="text-body-quaternary ms-1" href="#!">Cookies</a>
															-->
			</div>
		</div>
	</div>
</div>

			</li>
		</ul>
	</nav>

	<nav class="navbar navbar-top fixed-top navbar-expand-lg" id="dualNav" style="display:none;">
		<div class="w-100">
			<div class="d-flex flex-between-center dual-nav-first-layer">
				<div class="navbar-logo">
					<button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse" aria-expanded="false" aria-label="Toggle Navigation">
						<span class="navbar-toggle-icon">
							<span class="toggle-line"></span>
						</span>
					</button>
					<a class="navbar-brand me-1 me-sm-3" href="<?= $this->url->get('dashboard') ?>">
						<div class="d-flex align-items-center">
							<div class="d-flex align-items-center"><img src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdamid-'.$this->session->pdam->id.'.png'; ?>" alt="<?= $this->session->pdam->nama_aplikasi ?>" width="27"/>
								<h5 class="logo-text ms-2 d-none d-sm-block"><?= $this->session->pdam->nama_aplikasi ?></h5>
								<h6 class="logo-text fs-8 fw-bold">
									&nbsp; &nbsp; Login :
									<?= $this->session->periode ?>
								</h6>
							</div>
						</div>
					</a>
				</div>
				<ul class="navbar-nav navbar-nav-icons flex-row">
					<li class="nav-item">
						<div class="theme-control-toggle fa-icon-wait px-2"><input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle"/><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;">
								<span class="icon" data-feather="moon"></span>
							</label>
							<label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;">
								<span class="icon" data-feather="sun"></span>
							</label>
						</div>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
							<div class="avatar avatar-l ">
								<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
							</div>
						</a>
						<!--
<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl">
					<img class="rounded-circle" src="<?= $this->url->get('assets') ?>/img/team/72x72/57.webp" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis">Jerry Seinfield</h6>
			</div>
			<div class="mb-3 mx-3">
				<input class="form-control form-control-sm" id="statusUpdateInput" type="text" placeholder="Update your status"/>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 10rem">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user"></span>
						<span>Profile</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Posts &amp; Activity</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="settings"></span>Settings &amp; Privacy
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="help-circle"></span>Help Center</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="globe"></span>Language</a>
				</li>
			</ul>
		</div>
		<div class="card-footer p-0 border-top border-translucent">
			<ul class="nav d-flex flex-column my-3">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="#!">
						<span class="me-2 text-body align-bottom" data-feather="user-plus"></span>Add another account</a>
				</li>
			</ul>
			<hr/>
			<div class="px-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('auth/logout') ?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div class="my-2 text-center fw-bold fs-10 text-body-quaternary">
				<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a class="text-body-quaternary ms-1" href="#!">Cookies</a>
			</div>
		</div>
	</div>
</div>
-->

<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl ">
					<img class="rounded-circle " src="<?= $this->url->get('external_img') ?>/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis"><?= $this->session->pdam->nama_pdam ?></h6>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 7rem;">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('dashboard') ?>">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/profile-aplikasi') ?>">
						<span class="me-2 text-body align-bottom" data-feather="aperture"></span>
						<span>Profile Perusahaan</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="<?= $this->url->get('setting/ganti-password') ?>">
						<span class="me-2 text-body align-bottom" data-feather="lock"></span>Ganti Password</a>
				</li>
				<!-- <li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="settings"></span>Settings &amp; Privacy
																	</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="help-circle"></span>Help Center</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link px-3 d-block" href="#!">
																		<span class="me-2 text-body align-bottom"
																			data-feather="globe"></span>Language</a>
																</li> -->
			</ul>
		</div>
		<div
			class="card-footer p-0 border-top border-translucent">
			<!-- <ul class="nav d-flex flex-column my-3">
													<li class="nav-item">
														<a class="nav-link px-3 d-block" href="#!">
															<span class="me-2 text-body align-bottom"
																data-feather="user-plus"></span>Add another account</a>
													</li>
												</ul>
												<hr /> -->
			<div class="px-3 my-3">
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="<?= $this->url->get('') ?><?= $this->session->pdam->direktori.'/auth/logout'?>">
					<span class="me-2" data-feather="log-out"></span>Sign out</a>
			</div>
			<div
				class="my-2 text-center fw-bold fs-10 text-body-quaternary"><!--
															<a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;<a
																class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;<a
																class="text-body-quaternary ms-1" href="#!">Cookies</a>
															-->
			</div>
		</div>
	</div>
</div>

					</li>
				</ul>
			</div>

			<div class="collapse navbar-collapse navbar-top-collapse justify-content-center" id="navbarTopCollapse">
				<?php $this->macros['renderSubmenu'] = function($__p = null) { if (isset($__p[0])) { $menuList = $__p[0]; } else { if (array_key_exists("menuList", $__p)) { $menuList = $__p["menuList"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: menuList");  } } if (isset($__p[1])) { $parentId = $__p[1]; } else { if (array_key_exists("parentId", $__p)) { $parentId = $__p["parentId"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: parentId");  } }  ?><?php foreach ($menuList as $menu) { ?>
        <?php if ($menu->has_children) { ?>
            <li class="dropdown">
                <a class="dropdown-item dropdown-toggle" id="<?= $menu->id_menu ?>" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <div class="dropdown-item-wrapper">
                        <span class="uil fs-8 uil-angle-right lh-1 dropdown-indicator-icon"></span>
                        <span>
                            <?php if (!empty($menu->icon)) { ?>
                                <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                            <?php } ?>
                            <?= $menu->nama_menu ?>
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                </ul>
            </li>
        <?php } else { ?>
            <li>
                <a class="dropdown-item" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?><?php }; $this->macros['renderSubmenu'] = \Closure::bind($this->macros['renderSubmenu'], $this); ?><ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
    <?php foreach ($this->menuModel->getUserMenuList() as $menu) { ?>
        <?php if ($menu->jenis == 0 && empty($menu->link_menu)) { ?>
            <?php if ($menu->has_children) { ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="uil fs-8 me-2 uil-<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                    </ul>
                </li>
            <?php } ?>
        <?php } elseif ($menu->jenis == 0 && !empty($menu->link_menu)) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?>
</ul>

			</div>
		</div>
	</nav>
	<script>
		var navbarTopShape = window.config.config.phoenixNavbarTopShape;
var navbarPosition = window.config.config.phoenixNavbarPosition;
var body = document.querySelector('body');
var navbarDefault = document.querySelector('#navbarDefault');
var navbarTop = document.querySelector('#navbarTop');
var topNavSlim = document.querySelector('#topNavSlim');
var navbarTopSlim = document.querySelector('#navbarTopSlim');
var navbarCombo = document.querySelector('#navbarCombo');
var navbarComboSlim = document.querySelector('#navbarComboSlim');
var dualNav = document.querySelector('#dualNav');

var documentElement = document.documentElement;
var navbarVertical = document.querySelector('.navbar-vertical');

if (navbarPosition === 'dual-nav') {
    topNavSlim?.remove();
    navbarTop?.remove();
    navbarTopSlim?.remove();
    navbarCombo?.remove();
    navbarComboSlim?.remove();
    navbarDefault?.remove();
    navbarVertical?.remove();
    dualNav.removeAttribute('style');
    document.documentElement.setAttribute('data-navigation-type', 'dual');

} else if (navbarTopShape === 'slim' && navbarPosition === 'vertical') {
    navbarDefault?.remove();
    navbarTop?.remove();
    navbarTopSlim?.remove();
    navbarCombo?.remove();
    navbarComboSlim?.remove();
    topNavSlim.style.display = 'block';
    navbarVertical.style.display = 'inline-block';
    document.documentElement.setAttribute('data-navbar-horizontal-shape', 'slim');

} else if (navbarTopShape === 'slim' && navbarPosition === 'horizontal') {
    navbarDefault?.remove();
    navbarVertical?.remove();
    navbarTop?.remove();
    topNavSlim?.remove();
    navbarCombo?.remove();
    navbarComboSlim?.remove();
    dualNav?.remove();
    navbarTopSlim.removeAttribute('style');
    document.documentElement.setAttribute('data-navbar-horizontal-shape', 'slim');
} else if (navbarTopShape === 'slim' && navbarPosition === 'combo') {
    navbarDefault?.remove();
    navbarTop?.remove();
    topNavSlim?.remove();
    navbarCombo?.remove();
    navbarTopSlim?.remove();
    dualNav?.remove();
    navbarComboSlim.removeAttribute('style');
    navbarVertical.removeAttribute('style');
    document.documentElement.setAttribute('data-navbar-horizontal-shape', 'slim');
} else if (navbarTopShape === 'default' && navbarPosition === 'horizontal') {
    navbarDefault?.remove();
    topNavSlim?.remove();
    navbarVertical?.remove();
    navbarTopSlim?.remove();
    navbarCombo?.remove();
    navbarComboSlim?.remove();
    dualNav?.remove();
    navbarTop.removeAttribute('style');
    document.documentElement.setAttribute('data-navigation-type', 'horizontal');
} else if (navbarTopShape === 'default' && navbarPosition === 'combo') {
    topNavSlim?.remove();
    navbarTop?.remove();
    navbarTopSlim?.remove();
    navbarDefault?.remove();
    navbarComboSlim?.remove();
    dualNav?.remove();
    navbarCombo.removeAttribute('style');
    navbarVertical.removeAttribute('style');
    document.documentElement.setAttribute('data-navigation-type', 'combo');
} else {
    topNavSlim?.remove();
    navbarTop?.remove();
    navbarTopSlim?.remove();
    navbarCombo?.remove();
    navbarComboSlim?.remove();
    dualNav?.remove();
    navbarDefault.removeAttribute('style');
    navbarVertical.removeAttribute('style');
}

var navbarTopStyle = window.config.config.phoenixNavbarTopStyle;
var navbarTop = document.querySelector('.navbar-top');
if (navbarTopStyle === 'darker') {
    navbarTop.setAttribute('data-navbar-appearance', 'darker');
}

var navbarVerticalStyle = window.config.config.phoenixNavbarVerticalStyle;
var navbarVertical = document.querySelector('.navbar-vertical');
if (navbarVerticalStyle === 'darker') {
    navbarVertical.setAttribute('data-navbar-appearance', 'darker');
}</script>
	<div
		class="content">
		<!--
															<div class="d-flex flex-center content-min-h">
																<div class="text-center py-9"><img class="img-fluid mb-7 d-dark-none" src="<?= $this->url->get('assets') ?>/img/spot-illustrations/2.png" width="470" alt=""/>
																	<img class="img-fluid mb-7 d-light-none" src="<?= $this->url->get('assets') ?>/img/spot-illustrations/dark_2.png" width="470" alt=""/>
																	<h1 class="text-body-secondary fw-normal mb-5">Create Something Beautiful.</h1>
																	<a class="btn btn-lg btn-primary" href="../documentation/getting-started.html">Getting Started</a>
																</div>
															</div>
														-->
		
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Master</a>
			</li>
			<li class="breadcrumb-item active">Harga Kamar</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Harga Kamar</h2>
			</div>
		</div>
		<div id="products">
			<div class="mb-4">
				<div class="row g-3 justify-content-end gap-2">
					<div class="col-auto">
						<button class="btn btn-sm btn-outline-primary my-1" id="btn-filter">
							<span class="fas fa-search me-2"></span>Filter
						</button>
						<button class="btn btn-sm btn-success my-1" id="btn-perbarui">
							<span class="fas fa-sync me-2"></span>Perbarui
						</button>
						<?php if ($is_can_update == '1') { ?>
							<button class="btn btn-sm btn-warning my-1" id="btn-edit">
								<span class="fas fa-pencil-alt me-2"></span>Edit Harga
							</button>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
				<div class="table-responsive scrollbar-overlay mx-n1 px-1">
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-harga-kamar">
						<thead>
							<tr class="p-2 text-center">
								<th class="sort px-2" scope="col">No</th>
								<th class="sort px-2" scope="col">ID</th>
								<th class="sort px-2" scope="col">Nama Tipe</th>
								<th class="sort px-2" scope="col">Harga per Malam</th>
							</tr>
						</thead>
						<tbody class="list" id="harga-kamar-table-body">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="filterModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="filterModal" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content bg-body-highlight p-6">
				<div class="modal-header justify-content-between border-0 p-0 mb-2">
					<h3 class="mb-0">Filter Data Harga Kamar</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="form-filter">
								<div class="mb-3">
									<label class="text-body-highlight fw-bold mb-2">Nama Tipe Kamar</label>
									<div class="input-group">
										<div class="input-group-text">
											<input class="form-check-input toggle-input" type="checkbox"/>
										</div>
										<div class="form-control">
											<select name="search_nama" id="search_nama" class="form-control">
												<option value="">Pilih Tipe Kamar</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label class="text-body-highlight fw-bold mb-2">Harga Minimum</label>
											<div class="input-group">
												<div class="input-group-text">
													<input class="form-check-input toggle-input" type="checkbox"/>
												</div>
												<input class="form-control" type="number" id="search_harga_min" name="search_harga_min" placeholder="Harga minimum..."/>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="text-body-highlight fw-bold mb-2">Harga Maksimum</label>
											<div class="input-group">
												<div class="input-group-text">
													<input class="form-check-input toggle-input" type="checkbox"/>
												</div>
												<input class="form-control" type="number" id="search_harga_max" name="search_harga_max" placeholder="Harga maksimum..."/>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="modal-footer border-0 pt-6 px-0 pb-0">
					<button class="btn btn-lighter-grey px-3 my-0" data-bs-dismiss="modal" aria-label="Close">
						Batal
					</button>
					<button class="btn btn-primary my-0" id="btn-search">Cari Data</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="manageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addModal" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content bg-body-highlight p-6">
				<div class="modal-header justify-content-between border-0 p-0 mb-2">
					<h3 class="mb-0" id="title_modal">Edit Harga Kamar</h3>
					<button class="btn btn-sm btn-phoenix-secondary" data-bs-dismiss="modal" aria-label="Close">
						<span class="fas fa-times text-danger"></span>
					</button>
				</div>
				<div class="modal-body px-0">
					<div class="row g-4">
						<div class="col-lg-12">
							<form id="manageForm">
								<input type="hidden" id="id_edit" name="id_edit"/>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">ID Tipe Kamar</label>
									<input class="form-control" type="text" id="id_display" readonly/>
								</div>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Nama Tipe Kamar</label>
									<input class="form-control" type="text" id="nama_display" readonly/>
								</div>

								<div class="mb-2">
									<label class="text-body-highlight fw-bold mb-2">Harga per Malam</label>
									<input class="form-control" type="number" id="harga_per_malam" name="harga_per_malam" placeholder="Masukkan Harga ..."/>
								</div>

							</form>
						</div>
					</div>
				</div>
				<div class="modal-footer border-0 pt-6 px-0 pb-0">
					<button class="btn btn-lighter-grey px-3 my-0" data-bs-dismiss="modal" aria-label="Close">
						Batal
					</button>
					<button class="btn btn-primary my-0" id="btn-submit">Simpan</button>
				</div>
			</div>
		</div>
	</div>



		<footer class="footer position-absolute">
			<div class="row g-0 justify-content-between align-items-center h-100">
				<div class="col-12 col-sm-auto text-center">
					<p class="mb-0 mt-2 mt-sm-0 text-body"><?= $this->session->pdam->nama_aplikasi ?><span class="d-none d-sm-inline-block"></span>
						<span class="d-none d-sm-inline-block mx-1">|</span><br class="d-sm-none"/>2025 &copy;<a class="mx-1" href="#">Copyright</a>
					</p>
				</div>
				<div class="col-12 col-sm-auto text-center">
					<p class="mb-0 text-body-tertiary text-opacity-85">v1.00.0</p>
				</div>
			</div>
		</footer>
	</div>
</body></html></main><!-- ===============================================--><!--    End of Main Content--><!-- ===============================================--><div class="offcanvas offcanvas-end settings-panel border-0" id="settings-offcanvas" tabindex="-1" aria-labelledby="settings-offcanvas"> <div class="offcanvas-header align-items-start border-bottom flex-column border-translucent">
<div class="pt-1 w-100 mb-6 d-flex justify-content-between align-items-start">
	<div>
		<h5 class="mb-2 me-2 lh-sm">
			<span class="fas fa-palette me-2 fs-8"></span>Theme Customizer</h5>
		<p class="mb-0 fs-9">Explore different styles according to your preferences</p>
	</div>
	<button class="btn p-1 fw-bolder" type="button" data-bs-dismiss="offcanvas" aria-label="Close">
		<span class="fas fa-times fs-8"></span>
	</button>
</div>
<button class="btn btn-phoenix-secondary w-100" data-theme-control="reset">
	<span class="fas fa-arrows-rotate me-2 fs-10"></span>Reset to default</button></div><div class="offcanvas-body scrollbar px-card" id="themeController">
<div class="setting-panel-item mt-0">
	<h5 class="setting-panel-item-title">Color Scheme</h5>
	<div class="row gx-2">
		<div class="col-4"><input class="btn-check" id="themeSwitcherLight" name="theme-color" type="radio" value="light" data-theme-control="phoenixTheme"/><label class="btn d-inline-block btn-navbar-style fs-9" for="themeSwitcherLight">
				<span class="mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="<?= $this->url->get('assets') ?>/img/generic/default-light.png" alt=""/></span>
				<span class="label-text">Light</span>
			</label>
		</div>
		<div class="col-4"><input class="btn-check" id="themeSwitcherDark" name="theme-color" type="radio" value="dark" data-theme-control="phoenixTheme"/><label class="btn d-inline-block btn-navbar-style fs-9" for="themeSwitcherDark">
				<span class="mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="<?= $this->url->get('assets') ?>/img/generic/default-dark.png" alt=""/></span>
				<span class="label-text">
					Dark</span>
			</label>
		</div>
		<div class="col-4"><input class="btn-check" id="themeSwitcherAuto" name="theme-color" type="radio" value="auto" data-theme-control="phoenixTheme"/><label class="btn d-inline-block btn-navbar-style fs-9" for="themeSwitcherAuto">
				<span class="mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="<?= $this->url->get('assets') ?>/img/generic/auto.png" alt=""/></span>
				<span class="label-text">
					Auto</span>
			</label>
		</div>
	</div>
</div>
<div class="setting-panel-item">
	<h5 class="setting-panel-item-title">Navigation Type</h5>
	<div class="row gx-2">
		<div class="col-6"><input class="btn-check" id="navbarPositionVertical" name="navigation-type" type="radio" value="vertical" data-theme-control="phoenixNavbarPosition"/><label class="btn d-inline-block btn-navbar-style fs-9" for="navbarPositionVertical">
				<span class="rounded d-block"><img class="img-fluid img-prototype d-dark-none" src="<?= $this->url->get('assets') ?>/img/generic/default-light.png" alt=""/><img class="img-fluid img-prototype d-light-none" src="<?= $this->url->get('assets') ?>/img/generic/default-dark.png" alt=""/></span>
				<span class="label-text">Vertical</span>
			</label>
		</div>
		<div class="col-6"><input class="btn-check" id="navbarPositionHorizontal" name="navigation-type" type="radio" value="horizontal" data-theme-control="phoenixNavbarPosition"/><label class="btn d-inline-block btn-navbar-style fs-9" for="navbarPositionHorizontal">
				<span class="rounded d-block"><img class="img-fluid img-prototype d-dark-none" src="<?= $this->url->get('assets') ?>/img/generic/top-default.png" alt=""/><img class="img-fluid img-prototype d-light-none" src="<?= $this->url->get('assets') ?>/img/generic/top-default-dark.png" alt=""/></span>
				<span class="label-text">
					Horizontal</span>
			</label>
		</div>
		<div class="col-6"><input class="btn-check" id="navbarPositionCombo" name="navigation-type" type="radio" value="combo" data-theme-control="phoenixNavbarPosition"/><label class="btn d-inline-block btn-navbar-style fs-9" for="navbarPositionCombo">
				<span class="rounded d-block"><img class="img-fluid img-prototype d-dark-none" src="<?= $this->url->get('assets') ?>/img/generic/nav-combo-light.png" alt=""/><img class="img-fluid img-prototype d-light-none" src="<?= $this->url->get('assets') ?>/img/generic/nav-combo-dark.png" alt=""/></span>
				<span class="label-text">
					Combo</span>
			</label>
		</div>
		<!-- 
											<div class="col-6"><input class="btn-check" id="navbarPositionTopDouble" name="navigation-type" type="radio" value="dual-nav" data-theme-control="phoenixNavbarPosition"/><label class="btn d-inline-block btn-navbar-style fs-9" for="navbarPositionTopDouble">
													<span class="rounded d-block"><img class="img-fluid img-prototype d-dark-none" src="<?= $this->url->get('assets') ?>/img/generic/dual-light.png" alt=""/><img class="img-fluid img-prototype d-light-none" src="<?= $this->url->get('assets') ?>/img/generic/dual-dark.png" alt=""/></span>
													<span class="label-text">
														Dual
																		nav</span>
												</label>
											</div>
											-->
	</div>
</div>
<!-- 
			<div class="setting-panel-item">
				<h5 class="setting-panel-item-title">Vertical Navbar Appearance</h5>
				<div class="row gx-2">
					<div class="col-6"><input class="btn-check" id="navbar-style-default" type="radio" name="config.name" value="default" data-theme-control="phoenixNavbarVerticalStyle"/><label class="btn d-block w-100 btn-navbar-style fs-9" for="navbar-style-default">
							<img class="img-fluid img-prototype d-dark-none" src="<?= $this->url->get('assets') ?>/img/generic/default-light.png" alt=""/><img class="img-fluid img-prototype d-light-none" src="<?= $this->url->get('assets') ?>/img/generic/default-dark.png" alt=""/><span class="label-text d-dark-none">
								Default</span>
							<span class="label-text d-light-none">Default</span>
						</label>
					</div>
					<div class="col-6"><input class="btn-check" id="navbar-style-dark" type="radio" name="config.name" value="darker" data-theme-control="phoenixNavbarVerticalStyle"/><label class="btn d-block w-100 btn-navbar-style fs-9" for="navbar-style-dark">
							<img class="img-fluid img-prototype d-dark-none" src="<?= $this->url->get('assets') ?>/img/generic/vertical-darker.png" alt=""/><img class="img-fluid img-prototype d-light-none" src="<?= $this->url->get('assets') ?>/img/generic/vertical-lighter.png" alt=""/><span class="label-text d-dark-none">
								Darker</span>
							<span class="label-text d-light-none">Lighter</span>
						</label>
					</div>
				</div>
			</div>
			-->
<!-- 
			<div class="setting-panel-item">
				<h5 class="setting-panel-item-title">Horizontal Navbar Shape</h5>
				<div class="row gx-2">
					<div class="col-6"><input class="btn-check" id="navbarShapeDefault" name="navbar-shape" type="radio" value="default" data-theme-control="phoenixNavbarTopShape"/><label class="btn d-inline-block btn-navbar-style fs-9" for="navbarShapeDefault">
							<span class="mb-2 rounded d-block"><img class="img-fluid img-prototype d-dark-none mb-0" src="<?= $this->url->get('assets') ?>/img/generic/top-default.png" alt=""/><img class="img-fluid img-prototype d-light-none mb-0" src="<?= $this->url->get('assets') ?>/img/generic/top-default-dark.png" alt=""/></span>
							<span class="label-text">Default</span>
						</label>
					</div>
					<div class="col-6"><input class="btn-check" id="navbarShapeSlim" name="navbar-shape" type="radio" value="slim" data-theme-control="phoenixNavbarTopShape"/><label class="btn d-inline-block btn-navbar-style fs-9" for="navbarShapeSlim">
							<span class="mb-2 rounded d-block"><img class="img-fluid img-prototype d-dark-none mb-0" src="<?= $this->url->get('assets') ?>/img/generic/top-slim.png" alt=""/><img class="img-fluid img-prototype d-light-none mb-0" src="<?= $this->url->get('assets') ?>/img/generic/top-slim-dark.png" alt=""/></span>
							<span class="label-text">
								Slim</span>
						</label>
					</div>
				</div>
			</div>
			-->
<div class="setting-panel-item">
	<h5 class="setting-panel-item-title">Horizontal Navbar Appearance</h5>
	<div class="row gx-2">
		<div class="col-6"><input class="btn-check" id="navbarTopDefault" name="navbar-top-style" type="radio" value="default" data-theme-control="phoenixNavbarTopStyle"/><label class="btn d-inline-block btn-navbar-style fs-9" for="navbarTopDefault">
				<span class="mb-2 rounded d-block"><img class="img-fluid img-prototype d-dark-none mb-0" src="<?= $this->url->get('assets') ?>/img/generic/top-default.png" alt=""/><img class="img-fluid img-prototype d-light-none mb-0" src="<?= $this->url->get('assets') ?>/img/generic/top-style-darker.png" alt=""/></span>
				<span class="label-text">Default</span>
			</label>
		</div>
		<div class="col-6"><input class="btn-check" id="navbarTopDarker" name="navbar-top-style" type="radio" value="darker" data-theme-control="phoenixNavbarTopStyle"/><label class="btn d-inline-block btn-navbar-style fs-9" for="navbarTopDarker">
				<span class="mb-2 rounded d-block"><img class="img-fluid img-prototype d-dark-none mb-0" src="<?= $this->url->get('assets') ?>/img/generic/navbar-top-style-light.png" alt=""/><img class="img-fluid img-prototype d-light-none mb-0" src="<?= $this->url->get('assets') ?>/img/generic/top-style-lighter.png" alt=""/></span>
				<span class="label-text d-dark-none">Darker</span>
				<span class="label-text d-light-none">Lighter</span>
			</label>
		</div>
	</div>
</div>
<!-- <a class="bun btn-primary d-grid mb-3 text-white mt-5 btn btn-primary" href="https://themes.getbootstrap.com/product/phoenix-admin-dashboard-webapp-template/" target="_blank">Purchase template</a> --></div></div><a class="card setting-toggle" href="#settings-offcanvas" data-bs-toggle="offcanvas"><div class="card-body d-flex align-items-center px-2 py-1">
<div class="position-relative rounded-start" style="height:34px;width:28px">
	<div class="settings-popover">
		<span class="ripple">
			<span class="fa-spin position-absolute all-0 d-flex flex-center">
				<span class="icon-spin position-absolute all-0 d-flex flex-center">
					<svg width="20" height="20" viewbox="0 0 20 20" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
						<path d="M19.7369 12.3941L19.1989 12.1065C18.4459 11.7041 18.0843 10.8487 18.0843 9.99495C18.0843 9.14118 18.4459 8.28582 19.1989 7.88336L19.7369 7.59581C19.9474 7.47484 20.0316 7.23291 19.9474 7.03131C19.4842 5.57973 18.6843 4.28943 17.6738 3.20075C17.5053 3.03946 17.2527 2.99914 17.0422 3.12011L16.393 3.46714C15.6883 3.84379 14.8377 3.74529 14.1476 3.3427C14.0988 3.31422 14.0496 3.28621 14.0002 3.25868C13.2568 2.84453 12.7055 2.10629 12.7055 1.25525V0.70081C12.7055 0.499202 12.5371 0.297594 12.2845 0.257272C10.7266 -0.105622 9.16879 -0.0653007 7.69516 0.257272C7.44254 0.297594 7.31623 0.499202 7.31623 0.70081V1.23474C7.31623 2.09575 6.74999 2.8362 5.99824 3.25599C5.95774 3.27861 5.91747 3.30159 5.87744 3.32493C5.15643 3.74527 4.26453 3.85902 3.53534 3.45302L2.93743 3.12011C2.72691 2.99914 2.47429 3.03946 2.30587 3.20075C1.29538 4.28943 0.495411 5.57973 0.0322686 7.03131C-0.051939 7.23291 0.0322686 7.47484 0.242788 7.59581L0.784376 7.8853C1.54166 8.29007 1.92694 9.13627 1.92694 9.99495C1.92694 10.8536 1.54166 11.6998 0.784375 12.1046L0.242788 12.3941C0.0322686 12.515 -0.051939 12.757 0.0322686 12.9586C0.495411 14.4102 1.29538 15.7005 2.30587 16.7891C2.47429 16.9504 2.72691 16.9907 2.93743 16.8698L3.58669 16.5227C4.29133 16.1461 5.14131 16.2457 5.8331 16.6455C5.88713 16.6767 5.94159 16.7074 5.99648 16.7375C6.75162 17.1511 7.31623 17.8941 7.31623 18.7552V19.2891C7.31623 19.4425 7.41373 19.5959 7.55309 19.696C7.64066 19.7589 7.74815 19.7843 7.85406 19.8046C9.35884 20.0925 10.8609 20.0456 12.2845 19.7729C12.5371 19.6923 12.7055 19.4907 12.7055 19.2891V18.7346C12.7055 17.8836 13.2568 17.1454 14.0002 16.7312C14.0496 16.7037 14.0988 16.6757 14.1476 16.6472C14.8377 16.2446 15.6883 16.1461 16.393 16.5227L17.0422 16.8698C17.2527 16.9907 17.5053 16.9504 17.6738 16.7891C18.7264 15.7005 19.4842 14.4102 19.9895 12.9586C20.0316 12.757 19.9474 12.515 19.7369 12.3941ZM10.0109 13.2005C8.1162 13.2005 6.64257 11.7893 6.64257 9.97478C6.64257 8.20063 8.1162 6.74905 10.0109 6.74905C11.8634 6.74905 13.3792 8.20063 13.3792 9.97478C13.3792 11.7893 11.8634 13.2005 10.0109 13.2005Z" fill="#2A7BE4"></path>
					</svg>
				</span>
			</span>
		</span>
	</div>
</div>
<small class="text-uppercase text-body-tertiary fw-bold py-2 pe-2 ps-1 rounded-end">customize</small></div></a><!-- ===============================================--><!--    JavaScripts--><!-- ===============================================--><script src="<?= $this->url->get('vendors') ?>/popper/popper.min.js"> </script><script src="<?= $this->url->get('vendors') ?>/bootstrap/bootstrap.min.js"></script><script src="<?= $this->url->get('vendors') ?>/anchorjs/anchor.min.js"></script><script src="<?= $this->url->get('vendors') ?>/is/is.min.js"></script><script src="<?= $this->url->get('vendors') ?>/fontawesome/all.min.js"></script><script src="<?= $this->url->get('vendors') ?>/lodash/lodash.min.js"></script><script src="<?= $this->url->get('vendors') ?>/list.js/list.min.js"></script><script src="<?= $this->url->get('vendors') ?>/feather-icons/feather.min.js"></script><script src="<?= $this->url->get('vendors') ?>/dayjs/dayjs.min.js"></script><script src="<?= $this->url->get('vendors') ?>/choices/choices.min.js"></script><script src="<?= $this->url->get('vendors') ?>/prism/prism.js"></script><script src="<?= $this->url->get('assets') ?>/js/phoenix.js"></script><script src="<?= $this->url->get('lib_independent') ?>/jquery-3.7.1.min.js"></script><script src="<?= $this->url->get('lib_independent') ?>/dataTables.min.js"></script><script src="<?= $this->url->get('lib_independent') ?>/dataTables.bootstrap5.min.js"></script><script src="<?= $this->url->get('lib_independent') ?>/select2.min.js"></script><script src="<?= $this->url->get('lib_independent') ?>/jquery-confirm.min.js"></script><script src="<?= $this->url->get('lib_independent') ?>/notyf.min.js"></script><script src="<?= $this->url->get('lib_independent') ?>/jquery.validate.min.js"></script><script src="<?= $this->url->get('lib_independent') ?>/additional-methods.min.js"></script><script src="<?= $this->url->get('lib_independent') ?>/flatpickr.js"></script><script src="<?= $this->url->get('lib_independent') ?>/index.js"></script><script src="<?= $this->url->get('lib_independent') ?>/id.js"></script><script src="<?= $this->url->get('lib_independent') ?>/moment-with-locales.min.js"></script><script src="<?= $this->url->get('vendors') ?>/summernote-0.9.0/summernote-bs5.min.js"></script><script src="<?= $this->url->get('vendors') ?>/bootstrap-datepicker-1.9.0/js/bootstrap-datepicker.min.js"></script><script src="<?= $this->url->get('vendors') ?>/bootstrap-datepicker-1.9.0/locales/bootstrap-datepicker.id.min.js"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.8/autoNumeric.min.js" integrity="sha512-mD/vCchTqwSnIslMzOI7zbNeTNDqosvT7VvrNHo5xJDvg5hCpCvvct0QtHtrHouaWsI3ofL4B7nNaF8pDfg3Yw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js" integrity="sha512-KaIyHb30iXTXfGyI9cyKFUIRSSuekJt6/vqXtyQKhQP6ozZEGY8nOtRS6fExqE4+RbYHus2yGyYg1BrqxzV6YA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js" integrity="sha512-d4KkQohk+HswGs6A1d6Gak6Bb9rMWtxjOa0IiY49Q3TeFd5xAzjWXDCBW9RS7m86FQ4RzM2BdHmdJnnKRYknxw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script><script src="https://js.sentry-cdn.com/acc8669eb2c41a158d9ebd151d0aef12.min.js" crossorigin="anonymous"></script><!-- <script src="https://cdn.jsdelivr.net/npm/summernote-cleaner@1.0.0/summernote-cleaner.min.js"></script> --><script src="<?= $this->url->get('lib_independent') ?>/shortcut.js"></script><script src="https://cdn.jsdelivr.net/npm/chart.js"></script><script>// Terapkan lokal Indonesia
// flatpickr.l10ns.id.firstDayOfWeek = 1;
// flatpickr.localize(flatpickr.l10ns.id);

window.isPeriodeAktifGlobal = <?= $this->session->isPeriodeAktifGlobal ?>;

moment.locale('id');

window.notyf = new Notyf({
duration: 1000,
position: {
x: "right",
y: "top"
},
types: [
{
type: "warning",
duration: 4000,
background: "orange",
icon: {
className: "material-icons",
tagName: "i",
text: "warning"
}
}, {
type: "error",
background: "indianred",
duration: 4000,
dismissible: true
},
]
});
window.baseUrl = "<?= $this->url->get() ?>";

$.extend(true, $.fn.dataTable.defaults, {
language: {
processing: "<span class='fa-stack fa-lg text-center' style='display:flex; justify-content:center; align-items:center; width:100%; margin:auto;'><i class='fa fa-spinner fa-spin fa-stack fa-fw'></i>&emsp;",
searchPlaceholder: "Cari...",
sSearch: "",
lengthMenu: "_MENU_ Data/halaman ",
lengthMenu: "_MENU_ Data",
info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
infoEmpty: "Data tidak ditemukan",
infoFiltered: "",
zeroRecords: "Data tidak ditemukan",
emptyTable: "Data tidak ditemukan",
paginate: {
first: "Awal",
previous: "<i class='fa fa-angle-left'></i>",
next: "<i class='fa fa-angle-right'></i>",
last: "Akhir"
},
select: {
rows: "%d Baris Dipilih"
}
}
});

$('.date-picker-v2').datepicker({
format: "yyyy-mm-dd",
language: "id",
autoclose: true,
todayHighlight: true,
orientation: "bottom" // muncul di bawah
});

$('.year-picker').datepicker({
format: "yyyy", // Format hanya untuk tahun
minViewMode: 'years', // Membatasi hanya pemilihan tahun
startView: 'years', // Menampilkan tahun dari awal
autoclose: true // Menutup otomatis setelah memilih tahun
}).on('changeYear', function (e) { // $(this).valid();
$(e.currentTarget).data('datepicker').hide(); // Menyembunyikan setelah pemilihan tahun
});

// $('.yearmonth-picker').datepicker({
// format: "yyyymm",
// minViewMode: 'months', // or 1, 月选择
// startView: 'decade', // or 2, 10年选择
// orientation: 'bottom'
// }).on('changeMonth', function (e) {
// $(e.currentTarget).data('datepicker').hide();
// });

$('.yearmonth-picker').datepicker({
format: "yyyymm",
minViewMode: 'months',
startView: 'decade',
autoclose: true,
language: "id"
});


$('.month-picker').datepicker({
format: "mm", // Format hanya untuk bulan (angka 01–12)
minViewMode: 'months', // Membatasi hanya pemilihan bulan
startView: 'months', // Menampilkan bulan dari awal
autoclose: true // Menutup otomatis setelah memilih bulan
}).on('changeMonth', function (e) { // $(this).valid(); // Bisa diaktifkan kalau pakai jQuery Validation
$(e.currentTarget).data('datepicker').hide(); // Sembunyikan setelah pilih bulan
});

function escapeHtml(str) {
return str.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}</script><script>
	$(document).ready(function() {
    let table;
    let selectedRowId = null;

    // Initialize DataTable
    table = $('#datatables-harga-kamar').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/panel/hotel/master/harga-kamar/datatable',
            type: 'POST',
            data: function(d) {
                var formData = $("#form-filter").serializeArray();
                $.each(formData, function(key, val) {
                    d[val.name] = val.value;
                });
            }
        },
        columns: [
            { 
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                orderable: false,
                searchable: false
            },
            { data: 'id', name: 'id' },
            { data: 'nama', name: 'nama' },
            { 
                data: 'harga_per_malam', 
                name: 'harga_per_malam',
                render: function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            }
        ],
        order: [[1, 'asc']],
        select: {
            style: 'single'
        }
    });

    // Initialize Select2 for filter
    $('#search_nama').select2({
        dropdownParent: $('#filterModal'),
        width: '100%',
        ajax: {
            url: '/panel/hotel/master/tipe-kamar/getTipeKamarOptions',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.nama
                        };
                    })
                };
            },
            cache: true
        },
        placeholder: 'Pilih Tipe Kamar',
        allowClear: true
    });

    // Toggle input functionality for filter
    $('.toggle-input').change(function() {
        let inputGroup = $(this).closest('.input-group');
        let input = inputGroup.find('input[type="text"], input[type="number"], select');
        
        if ($(this).is(':checked')) {
            input.prop('disabled', false);
        } else {
            input.prop('disabled', true).val('');
        }
    });

    // Initialize all filter inputs as disabled
    $('#form-filter input[type="text"], #form-filter input[type="number"], #form-filter select').prop('disabled', true);

    // Filter button
    $('#btn-filter').click(function(e) {
        e.preventDefault();
        $('#filterModal').modal('show');
    });

    // Search button
    $('#btn-search').click(function(e) {
        e.preventDefault();
        table.ajax.reload();
        $('#filterModal').modal('hide');
        notyf.success('Filter diterapkan');
    });

    // Row selection
    $('#datatables-harga-kamar tbody').on('click', 'tr', function () {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            selectedRowId = null;
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            selectedRowId = table.row(this).data().id;
        }
    });

    // Refresh button
    $('#btn-perbarui').click(function() {
        table.ajax.reload();
    });

    // Edit button
    $('#btn-edit').click(function() {
        if (!selectedRowId) {
            alert('Pilih data yang akan diedit');
            return;
        }
        
        let rowData = table.row('.selected').data();
        
        $('#id_edit').val(rowData.id);
        $('#id_display').val(rowData.id);
        $('#nama_display').val(rowData.nama);
        $('#harga_per_malam').val(rowData.harga_per_malam);
        
        $('#manageModal').modal('show');
    });

    // Form submit
    $('#btn-submit').click(function(e) {
        e.preventDefault();
        
        let formData = $('#manageForm').serialize();
        
        $.ajax({
            url: '/panel/hotel/master/harga-kamar/updateData',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.error === 0) {
                    alert('Harga berhasil diupdate');
                    $('#manageModal').modal('hide');
                    table.ajax.reload();
                    selectedRowId = null;
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan sistem');
            }
        });
    });
});
</script><script>// FontAwesome initialization and class management
document.addEventListener('DOMContentLoaded', function () { // Ensure FontAwesome classes are present
function ensureFontAwesomeClasses() {
if (!document.documentElement.classList.contains('fontawesome-i2svg-active')) {
document.documentElement.classList.add('fontawesome-i2svg-active');
}
if (!document.documentElement.classList.contains('fontawesome-i2svg-complete')) {
document.documentElement.classList.add('fontawesome-i2svg-complete');
}
}

// Try to trigger FontAwesome processing
if (window.FontAwesome && window.FontAwesome.dom && window.FontAwesome.dom.i2svg) {
window.FontAwesome.dom.i2svg();
}

// Fallback: add classes manually after a delay
setTimeout(ensureFontAwesomeClasses, 500);
setTimeout(ensureFontAwesomeClasses, 1000);
setTimeout(ensureFontAwesomeClasses, 2000);
});

// Also check when FontAwesome script loads
window.addEventListener('load', function () {
setTimeout(function () {
if (!document.documentElement.classList.contains('fontawesome-i2svg-active')) {
document.documentElement.classList.add('fontawesome-i2svg-active');
}
if (!document.documentElement.classList.contains('fontawesome-i2svg-complete')) {
document.documentElement.classList.add('fontawesome-i2svg-complete');
}
}, 100);
});</script></body></html>
