{% extends 'template/dashboard.volt' %}

{% block content %}
<div class="page-content container container-plus">
  <div class="row justify-content-center pos-rel">

    <div class="pos-rel col-12 col-sm-7 mt-1 mt-sm-3">
      <div class="py-3 px-1 py-lg-4 px-lg-5">

        <br>
        <br>
        <br>
        <br>
        <div class="text-center fa-4x mt-5">
          <span
            class="text-100 text-dark-m3 d-sm-none">
            <!-- smaller text to fit in small devices -->
            ¯\_(ツ)_/¯
          </span>
          <span class="text-110 text-dark-m3 d-none d-sm-inline">
            ¯\_(ツ)_/¯
          </span>
        </div>


        <div class="text-center fa-4x text-orange-d2 letter-spacing-4">
          404
        </div>

        <br><br>

        <div class="text-center">
          <span class="text-150 text-primary-d2">
            Halaman tidak ditemukan
          </span>
        </div>

        <div class="text-center mt-4">
          <a class="btn btn-bgc-white btn-outline-primary px-35" href="{{ url('/Dashboard') }}">
            <i class="fa fa-home"></i>
            Dashboard
          </a>
        </div>


      </div>
    </div>
  </div>
</div>
{% endblock %}

{% block inlineScripts %}
<script>
$(document).ready(function() {
  $('#back-button').on('click', function(ev) {
    window.history.back();
  });
});
</script>
{% endblock %}