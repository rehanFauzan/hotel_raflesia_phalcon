{% extends 'template/dashboardPublic.volt' %}

{% block title %}
	Pelanggan Baru
{% endblock %}

{% block content %}

	<div class="page-content container-fluid">
		<div class="card ccard">
			<div class="card-header pb-1 align-middle border-t-3 brc-primary-tp3" style="border-top-left-radius: 0.4rem; border-top-right-radius: 0.4rem;border-bottom: 1px solid #e0e5e8 !important;">
				<h4 class="card-title text-dark-m3 mt-2">
					Pelanggan Baru
				</h4>
			</div>

			<div class="card-body p-3">
				<div class="row">
					<div class="col-md-12">
						<center>
							<div class="card" style="width: 45rem; color: #01b200;" >
								<div class="card-body text-center" >
								  <p class="card-title" style="font-size: 80px; color: #01b200;">
									<i class="far fa-check-circle"></i>
								  </p>
								  <br>
								  <h3>Pelanggan Baru Berhasil Di input!</h3>
								  <h3>Dengan Nomor Registrasi
									( {{ pb['no_reg'] }} )
								</h3>
								</div>
							</div>
						</center>
					</div>
				</div>
			</div>
		</div>
	</div>

{% endblock %}

{% block inline_script %}
	<script>
		{% include 'Tbw/Pelanggan/Daftarpublic/done.js' %}</script>
{% endblock %}
