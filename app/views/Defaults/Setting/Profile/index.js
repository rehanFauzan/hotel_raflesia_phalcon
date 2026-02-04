window.defaultUrl = `${baseUrl}panel/setting/profile/`;

const formManage = $('#formProfileSetting');

$(document).ready(function () {


    formManage.validate({
        rules: {
            nama_aplikasi: {
                required: true
            },
            nama_panjang_aplikasi: {
                required: true
            },
            nama_pdam: {
                required: true
            }
        },
        errorClass: "text-danger",
        errorElement: "div",
        highlight: function (element, errorClass) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element, errorClass) {
            $(element).removeClass("is-invalid");
        }
    });

    $('#btn_simpanData').on('click', function (e) {
        e.preventDefault();
    
        const fd = new FormData();
    
        // Ambil semua inputan dari form
        fd.append('image_logo', $('#image_logo').val());
        fd.append('nama_aplikasi', $('#nama_aplikasi').val());
        fd.append('nama_panjang_aplikasi', $('#nama_panjang_aplikasi').val());
        fd.append('nama_pdam', $('#nama_pdam').val());
        fd.append('pemerintah_kota_kab', $('#pemerintah_kota_kab').val());
        fd.append('kota_kab', $('#kota_kab').val());
        fd.append('alamat', $('#alamat').val());
        fd.append('no_telp_pdam', $('#no_telp_pdam').val());
        fd.append('email', $('#email').val());
        fd.append('latitude', $('#latitude').val());
        fd.append('longitude', $('#longitude').val());
    
        // Nama aplikasi tidak boleh kosong
        if (!$('#nama_aplikasi').val()) {
            notyf.open({
                type: 'warning',
                message: 'Nama Aplikasi tidak boleh kosong',
            });
            $('#nama_aplikasi').addClass('is-invalid');
            return false;
        } else {
            $('#nama_aplikasi').removeClass('is-invalid');
        }
    
        // Konfirmasi dan kirim AJAX
        $.confirm({
            title: "Konfirmasi",
            theme: "modern",
            content: "Simpan perubahan pengaturan Profil Perusahaan?",
            buttons: {
                Tidak: {
                    text: "Tidak",
                    btnClass: "btn-warning"
                },
                Ya: {
                    text: "Ya",
                    btnClass: "btn-primary",
                    action: function () {
                        $.ajax({
                            type: "POST",
                            url: defaultUrl + "/updateData",
                            data: fd,
                            processData: false,
                            contentType: false,
                            beforeSend: function () {
                                $(".loading").removeClass("hide");
                            },
                            success: function (response) {
                                $(".loading").addClass("hide");
                                if (response.error == 0) {
                                    notyf.success(response.message);
                                    setTimeout(() => {
                                        window.location.href = defaultUrl; // Refresh page
                                    }, 1000); // Delay of 1 seconds
                                } else {
                                    notyf.error(response.message);
                                }
                            },
                            error: function (e) {
                                $(".loading").addClass("hide");
                                notyf.error("Terjadi kesalahan saat mengirim data.");
                            }
                        });
                    }
                }
            }
        });
    });    

    $('#upload-avatar').on('change', function () {
        const file = this.files[0];
        if (!file) return false;
    
        const formData = new FormData();
        formData.append('upload_file', file);
    
        $.ajax({
            url: defaultUrl + '/uploadFile',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $(".loading").removeClass("hide");
            },
            success: function (response) {
                $(".loading").addClass("hide");
                if (response.error === 0) {
                    notyf.success(response.message);
                    $('#image_logo').val(response.data.filename); // Simpan nama file ke input hidden
                    $('.avatar img').attr('src', `${baseUrl}external_img/${response.data.filename}`); // Update gambar preview
                } else {
                    notyf.error(response.message);
                }
            },
            error: function () {
                $(".loading").addClass("hide");
                notyf.error("Upload Logo gagal.");
            }
        });
    });

    $('#upload-avatar').on('change', function () {
        const [file] = this.files;
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('.avatar img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    
});
