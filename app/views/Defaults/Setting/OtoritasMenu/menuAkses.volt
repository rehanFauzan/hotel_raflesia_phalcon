{% extends 'template/base.volt' %}

{% block title %}
	Setting - Otoritas Menu - Set Menu Akses
{% endblock %}

{% block inline_style %}
	<style>
		.bg-head {
			background-color: #f8f9fa;
			font-weight: 600;
		}
		
		.table {
			margin-bottom: 0;
		}
		
		.table th {
			background-color: #f8f9fa;
			border-bottom: 2px solid #dee2e6;
			padding: 12px 8px;
			font-weight: 600;
		}
		
		.table td {
			padding: 12px 8px;
			vertical-align: middle;
		}
		
		.btn-sm {
			padding: 0.25rem 0.5rem;
			font-size: 0.875rem;
		}
		
		.text-secondary {
			color: #6c757d !important;
		}
		
		/* Feather Icon Styles */
		.feather-icon {
			width: 16px;
			height: 16px;
			vertical-align: text-bottom;
			margin-right: 8px;
		}
		
		.dot-icon {
			width: 8px;
			height: 8px;
			margin-right: 4px;
		}
		
		.btn .feather-icon {
			margin-right: 4px;
		}
		
		.text-danger .feather-icon {
			color: #dc3545;
		}
		
		.text-success .feather-icon {
			color: #198754;
		}

		.batch-actions {
			margin-bottom: 1rem;
		}
	</style>
{% endblock %}

{% block content %}
	<nav class="mb-3" aria-label="breadcrumb">
		<ol class="breadcrumb mb-0">
			<li class="breadcrumb-item">
				<a href="#!">Setting</a>
			</li>
			<li class="breadcrumb-item">Otoritas Menu</li>
			<li class="breadcrumb-item active">Set Menu Akses</li>
		</ol>
	</nav>
	
	<div class="mb-9">
		<div class="row g-2 mb-4">
			<div class="col-auto">
				<h2 class="mb-0">Hak: {{ hak_nama }}</h2>
			</div>
		</div>
		
		<div class="card">
			<div class="card-body">
				<input type="hidden" id="id_hak" name="id_hak" value="{{ id_hak }}" class="form-control">
				
				<div class="batch-actions mb-3">
					<button class="btn btn-success btn-sm" onclick="batchSetAccess()">
						<i data-feather="check" class="feather-icon"></i> Beri Akses Terpilih
					</button>
					<button class="btn btn-danger btn-sm" onclick="batchRemoveAccess()">
						<i data-feather="x" class="feather-icon"></i> Hapus Akses Terpilih
					</button>
				</div>

				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th class="text-center" style="width: 50px">
									<input type="checkbox" id="select-all" onchange="toggleSelectAll()">
								</th>
								<th class="text-center" style="width: 50px">No</th>
								<th>Menu</th>
								<th>URL</th>
								<th class="text-center" style="width: 100px">Akses</th>
								<th class="text-center" style="width: 150px">Aksi</th>
							</tr>
						</thead>
						<tbody id="table-data">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
{% endblock %}

{% block inline_script %}
	{% include "Defaults/Setting/OtoritasMenu/menuAkses.js" %}
{% endblock %}
