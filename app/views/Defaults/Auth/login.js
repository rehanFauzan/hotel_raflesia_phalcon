function handleRememberUsername(checkbox) {
    if (checkbox.checked) {
        // Menyimpan username ke cookie saat checkbox dicentang
        const username = document.getElementById('username').value;
        document.cookie = `remembered_username=${username}; max-age=2592000; path=/`; // berlaku 30 hari
    } else {
        // Menghapus cookie saat checkbox tidak dicentang
        document.cookie = "remembered_username=; max-age=0; path=/";
    }
}

$('.yearmonth-picker').datepicker({
    format: "yyyymm",
    minViewMode: 'months',
    startView: 'decade',
    autoclose: true,
    language: "id"
});


// Mengecek cookie saat halaman dimuat
window.onload = function () {
    const username = getCookie('remembered_username');
    if (username) {
        document.getElementById('basic-checkbox').checked = true;
    }
}

// Fungsi helper untuk membaca cookie
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}