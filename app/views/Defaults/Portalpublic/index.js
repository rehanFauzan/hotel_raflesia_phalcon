window.defaultUrl = `${baseUrl}Portalpublic/`;


$(document).ready(function () {
    $('#btn-list').click(function (e) {
        e.preventDefault();
        window.location.href = defaultUrl;
    });

    collectionS2Form();

    $('#btn-refresh-data').click(function (e) {
        e.preventDefault();
        window.location.reload();
    });

    $("#form_Personal").validate({
        rules: {
            tgl_daftar: {
                required: true,
            },
            nama_pemohon: {
                required: true,
            },
            nomor_hp: {
                required: true,
            },
            nomor_ktp: {
                required: true,
            },
            alamat_ktp: {
                required: true
            },
            addressSearch: {
                required: true
            },
            pekerjaan_pemohon: {
                required: true
            },
        }
    });


    $('#biaya').priceFormat({
        prefix: '',
        clearPrefix: true,
        centsLimit: 0
    });

    $('#btnSavePelangganBaru').click(function (e) {
        e.preventDefault();
        if ($("#form_Personal").valid()) {

            let checkNilaiKec = $('#cabang').val();
            if (_.isEmpty(checkNilaiKec)) {
                notification("danger", "Harap pilih kecamatan/cabang !");
                return false;
            }

            let checkNilaiWil = $('#kecamatan').val();
            if (_.isEmpty(checkNilaiWil)) {
                notification("danger", "Harap pilih wilayah !");
                return false;
            }

            let checkNilaiRute = $('#kelurahan').val();
            if (_.isEmpty(checkNilaiRute)) {
                notification("danger", "Harap pilih rute !");
                return false;
            }

            $.ajax({
                type: "POST",
                url: defaultUrl + "saveData",
                dataType: "JSON",
                data: $("#form_Personal").serialize(),
                beforeSend: function () {
                    $('.loading').removeClass('hide');
                },
                success: function (response) {
                    let dataResult = response.data;
                    $('.loading').addClass('hide');
                    console.log(dataResult);
                    if (response.error == 0) {
                        notification("success", "Simpan Data Pelanggan Baru Berhasil !");
                        
                        var params = $("#form_Personal").serialize();
                        // window.location.href = "{{ url('pdam-tbw/Portalpublic/done') }}?" + params;
                    } else {
                        notification("danger", "Terjadi Kesalahan !");
                        return false;
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    $('.loading').removeClass('hide');
                    notification("danger", "Terjadi Kesalahan : " + errorThrown)
                }
            });
        } else {
            notification("danger", "Harap cek nilai inputan !");
        }
    });

    $('#nomor_hp').on('input', function (event) { 
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    $('#nomor_ktp').on('input', function (event) { 
        this.value = this.value.replace(/[^0-9]/g, '');
    });


    $('#cabang').select2("trigger", "select", {
        data: {
            id: 123,
            text: 'PUSAT',
            state: 'Active',
            selected: false,
        }
    });


//////////////////////////////////////////// GOOGLE MAPS
	var map;
	var centerLat = -6.9126655560342956;
	var centerLon = 106.93092266819349;
	var marker;
	
	
	initialize(centerLat, centerLon);
	
	function initialize(centerLat, centerLon) {
		document.getElementById("lat_long").value = centerLat+', '+centerLon;
	  var myLatlng = new google.maps.LatLng(centerLat, centerLon);
	  var myOptions = {
		zoom: 13,
		center: myLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
	  };
	
	  map = new google.maps.Map(document.getElementById("map"), myOptions);
	
	  marker = new google.maps.Marker({
		position: myLatlng,
		map: map,
		title: "Pilih Titik Koordinat",
		draggable: true,
	  });
	
	  google.maps.event.addListener(map, "click", function (event) {
		marker.setPosition(event.latLng);
	
		var latitude = event.latLng.lat();
		var longitude = event.latLng.lng();
		var lat_long = latitude + "," + longitude;
	
		document.getElementById("lat_long").value = lat_long;

		var geocoder = new google.maps.Geocoder();
		var latlng = { lat: latitude, lng: longitude };
		geocoder.geocode({ location: latlng }, function (results, status) {
			if (status === google.maps.GeocoderStatus.OK) {
				if (results[0]) {
                    var alamat = results[0].formatted_address;
                    var cleanedAddress = alamat.replace(/(\s*[\+]\s*|\s*[\+])\S*/g, '');
                    $('#addressSearch').val(cleanedAddress);
                    $('#lat_long').val(lat_long);

					// console.log("Formatted Address: " + cleanedAddress);
				} else {
					console.log("No results found");
				}
			} else {
				console.log("Geocoder failed due to: " + status);
			}
		});
	
		console.log("Latitude: " + latitude + ", Longitude: " + longitude);
	  });
	
	  google.maps.event.addListener(marker, "dragend", function (event) {
			var latitude = event.latLng.lat();
			var longitude = event.latLng.lng();
			var lat_long = latitude + "," + longitude;
	
			document.getElementById("lat_long").value = lat_long;
	
			console.log("Latitude: " + latitude + ", Longitude: " + longitude);
		});
	
	  document.getElementById("addressSearch").addEventListener("keyup", function (event) {
		if (event.which === 13) {
		  searchAddress();
		}
	  });
	}
	
	function searchAddress() {
	  var geocoder = new google.maps.Geocoder();
	  var address = document.getElementById("addressSearch").value;
	
	  // Menghapus karakter '+' dan karakter di dekatnya yang menempel tanpa spasi
	  var cleanedAddress = address.replace(/(\s*[\+]\s*|\s*[\+])\S*/g, '');
	  geocoder.geocode({ address: cleanedAddress }, function (results, status) {
		if (status === google.maps.GeocoderStatus.OK) {
		  var location = results[0].geometry.location;
		  map.setCenter(location);
		  marker.setPosition(location);
	
		  var latitude = location.lat();
		  var longitude = location.lng();
		  var lat_long = latitude + "," + longitude;
	
		  document.getElementById("lat_long").value = lat_long;
	
		  console.log("Latitude: " + latitude + ", Longitude: " + longitude);
		} else {
			notification("danger", "Nama lokasi tidak sesuai !");
		}
	  });
	}
//////////////////////////////////////////// GOOGLE MAPS


});

function collectionS2Form() {

    $('select[name=cabang]').select2({
        allowClear: true,
        theme: "bootstrap4",
        width: 'auto',
        placeholder: '',
        //  height: '35px',
        ajax: {
            url: "{{ url('panel/referensi/getCabangtbw') }}",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (response) {
                var data = JSON.parse(response);
                return {
                    results: data.data.map(function (i) {
                        i.id = i.of_id;
                        i.text = i.of_name;
                        return i;
                    }),
                    pagination: {
                        more: data.has_more
                    }
                }
            }
        }
    });

    $('select[name=kecamatan]').select2({
        allowClear: true,
        theme: "bootstrap4",
        width: 'auto',
        placeholder: '',
        //  height: '35px',
        ajax: {
            url: "{{ url('panel/referensi/getMasterKecamatantbw') }}",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (response) {
                var data = JSON.parse(response);
                return {
                    results: data.data.map(function (i) {
                        i.id = i.id;
                        i.text = i.nama_kec;
                        return i;
                    }),
                    pagination: {
                        more: data.has_more
                    }
                }
            }
        }
    });

    $('select[name=kelurahan]').select2({
        allowClear: true,
        theme: "bootstrap4",
        width: 'auto',
        placeholder: '',
        //  height: '35px',
        ajax: {
            url: "{{ url('panel/referensi/getMasterKelurahantbw') }}",
            data: function (params) {
                return {
                    id_kec: $('select[name=kecamatan]').val(),
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (response) {
                var data = JSON.parse(response);
                return {
                    results: data.data.map(function (i) {
                        i.id = i.id;
                        i.text = i.nama_kel;
                        return i;
                    }),
                    pagination: {
                        more: data.has_more
                    }
                }
            }
        }
    });
}

function addFile() {
    ++i;
    ++x;

    var cloned = $("#cloned").clone();
    cloned.removeClass("document0");
    cloned.addClass("document" + i);

    cloned.find(".card-header").find('.card-title').text("Bukti Hasil Pekerjaan " + i);

    cloned.find("input[name='userfile']").attr("data-index", i).data("index", i);

    cloned.find("div[name='container_image']").attr("data-index", i).data("index", i);
    cloned.find(`div[name='container_image'][data-index='${i}']`).children().html(`<a href="#" name='image_container' class="show-lightbox"></a>`);

    cloned.find(`input[name='userfile'][data-index='${i}']`).val("");
    cloned.find("input[name='nama_dokumen[]']").val("");

    // cloned.find("#add_BuktiPengerjaan").html('<button type="button" class="btn btn-danger btn-sm btn-block btn-flat" onclick="deleteFile(' + i + ')" role="button">Hapus</button>');
    cloned.find("#add_BuktiPengerjaan").html(`
        <a href="#" class="btn mx-1 btn-danger mb-1" id="delete_document" onclick="deleteFile(this, ${i})">
            <i class="fa fa-times text-danger align-text-bottom mr-2"></i>
            Hapus Bukti
        </a>    
    `);

    $("#many_document").append(cloned);

    // cloned.find("select").select2();
    // $("#manyFile").append('<div class="col-md-12 mb-4"><div class="row"><div class="col-md-7 col-12 order-md-1"><input type="file" name="userfile" class="form-control" onchange="fileUpload(this)"><input type="hidden" class="form-control" name="filename[]"></div><div class="col-md-2 col-12 text-center order-md-2"><div id="fileState"></div></div><div class="col-md-3 col-12 text-right order-md-3 order-4"><button type="button" class="btn btn-danger btn-block btn-flat" onclick="deleteFile(this)" role="button">Hapus</button></div><div class="col-12 order-md-4 order-3"><textarea class="form-control" name="keterangan[]" placeholder="Keterangan"></textarea></div></div>');
    // $(".btn").addClass("btn-flat");
}

function deleteFile(el, x) {
    // $(identifier).closest("div").prev().closest("div").prev().remove();
    // $(identifier).closest("div").prev().remove();
    // $(identifier).closest("div").next().remove();
    $(".document" + x).remove();
    i--;
    el.preventDefault();
}


function fileUploadKTP(file) {
    // var type_message = $('input[type=radio][name="jenis_pesan"]').val();
    // var uploadFile = $('#form').find('#uploadFile')[0].files[0];
    var postData = new FormData();
    postData.append('userfile', $('#form_Personal').find('#foto_ktp')[0].files[0]);

    $.ajax({
        url: defaultUrl + "/uploadFotoKTP",
        cache: false,
        contentType: false,
        processData: false,
        data: postData,
        type: 'POST',
        beforeSend: function (data, textStatus, jqXHR) {
            $(".loading").removeClass("hide");
        },
        success: function (data, textStatus, jqXHR) {
            console.log(data);
            let result = JSON.parse(data);
            $('#foto_ktp_text').val(result.data.filename);
            $(".loading").addClass("hide");
            notification("success", "Foto KTP Berhasil Di Upload");

            $('#foto-ktp').attr('src', "{{ url('tbw/ktp') }}/" + result.data.filename  + "?cleancache=" + Math.floor(Math.random() * 1001));

            $('#elPreview_fotoktp').show();
        },
        error: function (data, textStatus, jqXHR) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);

            notification('danger', 'Error: ' + data);
            $(".loading").addClass("hide");
        }
    });
}

function fileUploadKK(file) {
    // var type_message = $('input[type=radio][name="jenis_pesan"]').val();
    // var uploadFile = $('#form').find('#uploadFile')[0].files[0];
    var postData = new FormData();
    postData.append('userfile', $('#form_Personal').find('#foto_kk')[0].files[0]);

    $.ajax({
        url: defaultUrl + "/uploadFotoKK?noreg=" + $('#no_registrasi').val(),
        cache: false,
        contentType: false,
        processData: false,
        data: postData,
        type: 'POST',
        beforeSend: function (data, textStatus, jqXHR) {
            $(".loading").removeClass("hide");
        },
        success: function (data, textStatus, jqXHR) {
            console.log(data);
            let result = JSON.parse(data);
            $('#foto_kk_text').val(result.data.filename);
            $(".loading").addClass("hide");
            notification("success", "Foto KK Berhasil Di Upload");

            $('#foto-kk').attr('src', "{{ url('tbw/kk') }}/" + result.data.filename   + "?cleancache=" + Math.floor(Math.random() * 1001));

            $('#elPreview_fotokk').show();
        },
        error: function (data, textStatus, jqXHR) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);

            notification('danger', 'Error: ' + data);
            $(".loading").addClass("hide");
        }
    });
}