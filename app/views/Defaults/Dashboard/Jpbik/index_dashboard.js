window.defaultUrl = `${baseUrl}dashboard/`;

const m_periode = '{{ m_periode }}';
const y_periode = '{{ y_periode }}';
const tahun_berjalan = '{{ tahun_berjalan }}';

$(document).ready(function () {

    // Update setiap detik
    updateDateTime(); // Panggil sekali saat halaman load
    setInterval(updateDateTime, 1000); // Update setiap 1 detik



});

// Function untuk update tanggal dan jam
function updateDateTime() {
    const now = new Date();

    // Format tanggal Indonesia
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    const dateString = now.toLocaleDateString('id-ID', options);

    // Format jam dengan detik
    const timeString = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });

    // Update elemen HTML
    document.getElementById('current-date').textContent = dateString;
    document.getElementById('current-time').textContent = `Pukul : ${timeString} WIB`;
}


// Count-up halus untuk angka besar (opsional)
function countUp(el, target) {
    let cur = 0,
        step = Math.max(1, Math.round(target / 40));
    const t = setInterval(() => {
        cur += step;
        if (cur >= target) {
            cur = target;
            clearInterval(t);
        }
        el.textContent = Number(cur).toLocaleString('id-ID');
    }, 20);
}

function getDataVoucher() {

    $.ajax({
        type: "POST",
        data: {
            tahun: y_periode,
            bulan: m_periode
        },
        url: defaultUrl + "getDataVoucher",
        beforeSend: function (xhr, settings) {
            $(".loading").removeClass("hide");
        },
        success: function (response) {
            $(".loading").addClass("hide");
            console.log("Data Voucher ", response);

            let dataFetch = response.dataFetch;

            if (response.error == 0) {
                notyf.success(response.message);

                const total = Number(dataFetch.total);
                const verified = Number(dataFetch.jml_verifikasi);
                const unverified = Number(dataFetch.jml_blm_verifikasi);
                const unpaid = Number(dataFetch.jml_blm_bayar);
                const paid = Number(dataFetch.jml_sudah_bayar);

                // Set teks awal (format lokal)
                const ID = new Intl.NumberFormat('id-ID');
                document.getElementById('totalVoucher').textContent = ID.format(total);
                document.getElementById('sudahText').textContent = ID.format(verified);
                document.getElementById('belumText').textContent = ID.format(unverified);
                document.getElementById('terdataText').textContent = ID.format(total);
                document.getElementById('terbayarText').textContent = ID.format(paid);
                document.getElementById('dariText').textContent = ID.format(verified);
                document.getElementById('sisaText').textContent = ID.format(unpaid);

                // // Progress verifikasi
                const percent = Math.round((verified / total) * 100);
                document.getElementById('percentText').textContent = percent;
                document.getElementById('barVerif').style.width = percent + '%';

                countUp(document.getElementById('totalVoucher'), total);
                countUp(document.getElementById('terbayarText'), paid);
                countUp(document.getElementById('sisaText'), unpaid);
            } else {
                notyf.error(response.message);
            }
        },
        error: function (e) {
            notyf.error("Error, Terjadi Kesalahan");
            $(".loading").addClass("hide");
        },
    });
}

function getDataLabaRugi() {

    $.ajax({
        type: "POST",
        data: {
            tahun: y_periode,
            bulan: m_periode
        },
        url: defaultUrl + "getDataLabaRugi",
        beforeSend: function (xhr, settings) {
            $(".loading").removeClass("hide");
        },
        success: function (response) {
            $(".loading").addClass("hide");
            console.log("Data Laba Rugi ", response);
            let dataFetch = response.dataFetch;

            if (response.error == 0) {
                notyf.success(response.message);

                $('#lbl_bulan_ini_pendapatan').text(`Rp. ${Number(dataFetch[1].pendapatan).toFixed(2).toLocaleString('id-ID')}`);
                $('#lbl_bulan_ini_biaya').text(`Rp. ${Number(dataFetch[1].biaya).toFixed(2).toLocaleString('id-ID')}`)


                $('#lbl_sd_bulan_ini_pendapatan').text(`Rp. ${Number(dataFetch[0].pendapatan).toFixed(2).toLocaleString('id-ID')}`);
                $('#lbl_sd_bulan_ini_biaya').text(`Rp. ${Number(dataFetch[0].biaya).toFixed(2).toLocaleString('id-ID')}`);

            } else {
                notyf.error(response.message);
            }
        },
        error: function (e) {
            notyf.error("Error, Terjadi Kesalahan");
            $(".loading").addClass("hide");
        },
    });
}