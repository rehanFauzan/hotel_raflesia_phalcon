window.defaultUrl = `${baseUrl}setting/ganti-password/`;

$(document).ready(function () {    

    $('#btn_submit').on('click', function (ev) {
        ev.preventDefault();

        let pw_lama       = $.trim($('#pw_lama').val());
        let pw_baru       = $.trim($('#pw_baru').val());
        let verifikasi_pw = $.trim($('#verifikasi_pw').val());

        if (!pw_lama) {
            notyf.open({ type: "warning", message: "Password lama harus diisi!" });
            return;
        }

        if (!pw_baru) {
            notyf.open({ type: "warning", message: "Password baru harus diisi!" });
            return;
        }

        if (!verifikasi_pw) {
            notyf.open({ type: "warning", message: "Verifikasi Password harus diisi!" });
            return;
        }

        if (pw_baru !== verifikasi_pw) {
            notyf.open({ type: "warning", message: "Password baru dan verifikasi password tidak sama!" });
            return;
        }

        $.ajax({
            type: "POST",
            data: {
                pw_lama: pw_lama,
                pw_baru: pw_baru,
                verifikasi_pw: verifikasi_pw
            },
            url: defaultUrl + "saveData",
            beforeSend: function () {
                $(".loading").removeClass("hide");
            },
            success: function (response) {
                $(".loading").addClass("hide");
                if (response.error == 0) {
                    notyf.success("Password berhasil diganti.");
                    window.location.href = defaultUrl;
                } else {
                    notyf.error(response.message || "Password gagal diganti!");
                }
            },
            error: function () {
                notyf.error("Terjadi kesalahan saat ganti password!");
                $(".loading").addClass("hide");
            }
        });
    });

});

function togglePassword(inputId, el) {
    const input = document.getElementById(inputId);
    const icon = el.querySelector("i");
    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("far fa-eye");
      icon.classList.add("far fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.remove("far fa-eye-slash");
      icon.classList.add("far fa-eye");
    }
  }