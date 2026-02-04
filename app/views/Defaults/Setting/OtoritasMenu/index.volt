{% extends 'template/base.volt' %}

{% block title %}
    Setting - Otoritas Menu
{% endblock %}

{% block inline_style %}

	<style>
		table th,
		table td {
			padding: 6px !important; /* Mengurangi padding */
		}
		#datatables-otoritas-menu tbody tr {
			height: 50px; /* Atur tinggi baris */
		}

		#datatables-otoritas-menu tbody td {
			padding: 12px 10px; /* Atur padding di dalam sel */
		}
	</style>

{% endblock %}
{% block content %}
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Setting</a>
			</li>
			<li class="breadcrumb-item active">Otoritas Menu</li>
		</ol>
	</nav>
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Otoritas Menu</h2>
			</div>
		</div>
		<div id="otoritas_menu">
			<div class="mb-4">
				<div class="row g-3 justify-content-end gap-2">
					<div class="col-auto">

						<button class="btn btn-sm btn-success my-1" id="btn-perbarui">
							<span class="fas fa-sync me-2"></span>Perbarui
						</button>

                        <button class="btn btn-sm btn-primary my-1" id="btn-hak-akses">
							<span class="fas fa-chevron-right me-2"></span> Hak Akses Menu
						</button>

					</div>
				</div>
			</div>
			<div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
				<div class="table-responsive scrollbar-overlay mx-n1 px-1">
					<table class="table table-sm fs-9 mb-0 table-striped table-bordered" id="datatables-otoritas-menu">
						<thead>
							<tr class="p-2 text-center">
								<!-- Mengurangi padding -->
								<th class="sort px-2" scope="col">
									NO
								</th>
                                <th class="sort px-2" scope="col">
									ROLE / HAK AKSES
								</th>
							</tr>
						</thead>
						<tbody class="list" id="otoritas-menu-table-body"><!-- Data akan diisi dari script -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

{% endblock %}

{% block inline_script %}
	{% include "Defaults/Setting/OtoritasMenu/index.js" %}
{% endblock %}
