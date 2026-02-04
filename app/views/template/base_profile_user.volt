<!--
<div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
	<div class="card position-relative border-0">
		<div class="card-body p-0">
			<div class="text-center pt-4 pb-3">
				<div class="avatar avatar-xl">
					<img class="rounded-circle" src="{{ url('assets') }}/img/team/72x72/57.webp" alt=""/>
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
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="{{ url('auth/logout') }}">
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
					<img class="rounded-circle " src="{{ url('external_img') }}/<?= 'logo-pdam-'.$this->session->pdam->id.'.png'; ?>" alt=""/>
				</div>
				<h6 class="mt-2 text-body-emphasis">{{ session.pdam.nama_pdam }}</h6>
			</div>
		</div>
		<div class="overflow-auto scrollbar" style="height: 7rem;">
			<ul class="nav d-flex flex-column mb-2 pb-1">
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="{{ url('dashboard') }}">
						<span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="{{ url('setting/profile-aplikasi') }}">
						<span class="me-2 text-body align-bottom" data-feather="aperture"></span>
						<span>Profile Perusahaan</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link px-3 d-block" href="{{ url('setting/ganti-password') }}">
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
				<a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="{{ url('') }}<?= $this->session->pdam->direktori.'/auth/logout'?>">
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
