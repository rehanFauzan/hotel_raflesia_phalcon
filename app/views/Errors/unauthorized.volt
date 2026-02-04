{% extends 'template/dashboard.volt' %}

{% block content %}
	<div class="page-content container container-plus">
		<div class="row justify-content-center pos-rel">

			<div class="pos-rel col-12 col-sm-7 mt-1 mt-sm-3">
				<div class="py-3 px-1 py-lg-4 px-lg-5">

					<div class="text-center fa-4x">
						<span
							class="text-100 text-dark-m3 d-sm-none">
							<!-- smaller text to fit in small devices -->
							¯\_(ツ)_/¯
						</span>
						<span class="text-110 text-dark-m3 d-none d-sm-inline">
							¯\_(ツ)_/¯
						</span>
					</div>

					<div class="text-center fa-4x text-red-d2 letter-spacing-4">
						403
					</div>

					<div class="text-center">
						<span class="text-150 text-primary-d2">
							Anda tidak memiliki izin untuk mengakses menu ini...
						</span>
					</div>

					<div class="text-center mt-4">
						<button id="back-button" type="button" onclick="javascript:history.back();" class="btn btn-bgc-white btn-outline-default px-35 btn-text-slide-x">
							<i class="btn-text-2 fa fa-arrow-left text-110 align-text-bottom mr-2"></i>Go Back
						</button>

						<a class="btn btn-bgc-white btn-outline-primary px-35" href="{{ url('/panel/dashboard') }}">
							<i class="fa fa-home"></i>
							Homepage
						</a>
					</div>

				</div>
			</div>
		</div>
	</div>
{% endblock %}
