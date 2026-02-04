window.defaultUrl = `${baseUrl}panel/setting/pengaturan-ttd-jurnal/`;

var formManage = $('#manageForm');

var csrfKey = "<?php echo $this->security->getTokenKey() ?>";
var csrfValue = "<?php echo $this->security->getToken() ?>";

$(document).ready(function () {

    // Handle jumlah TTD manual (tidak menghapus data kecuali jumlah dikurangi)
    $('#jumlah_ttd').on('change', function () {
        let jumlah = parseInt($(this).val(), 10) || 0;
        let container = $('#ttd_container');

        // Hitung blok yang ada
        let existingCount = container.find('.ttd-block').length;

        // Tambah blok jika perlu
        for (let i = existingCount + 1; i <= jumlah; i++) {
            if ((i - 1) % 2 === 0) {
                container.append('<div class="row"></div>');
            }
            container.find('.row:last').append(renderTTDBlock(i));
        }

        // Hapus blok jika jumlah dikurangi
        if (jumlah < existingCount) {
            container.find('.ttd-block').each(function () {
                let idx = $(this).data('index');
                if (idx > jumlah) $(this).remove();
            });
        }

        renderSelect2Action();
    });

    // Handle pilih jenis laporan
    $('#jenis_lap').on('change', function () {
        let jenis_lap = $(this).val();
        let dokumen = $('#dokumen').val();

        // Ambil option yang terpilih
        let selectedOption = $(this).find('option:selected');
        let jt_id = selectedOption.data('jtid');
        let jt_name = selectedOption.data('jtname');

        console.log("jtId ", jt_id);
        console.log("jtName ", jt_name);

        // Hanya trigger select2 jika ada jt_id dan jt_name
        if (jt_id && jt_name) {
            $("#dokumen").select2("trigger", "select", {
                data: {
                    id: jt_id,
                    jt_id: jt_id,
                    nama_jurnal: jt_name,
                    text: `${jt_id} (${jt_name})`,
                    selected: true
                }
            });
        }

        // Reset TTD container
        $('#ttd_container').html('');
        $('#jumlah_ttd').val('');
        
        // Jika ada jenis_lap dan dokumen, load data TTD
        if (!_.isEmpty(jenis_lap) && !_.isEmpty(dokumen)) {
            loadDataTTD(jenis_lap, dokumen);
        }
    });

    // Handle pilih dokumen (AJAX)
    $('#dokumen').on('change', function () {
        let dokumen = $(this).val();
        let jenis_lap = $('#jenis_lap').val();
        
        // Reset TTD container
        $('#ttd_container').html('');
        $('#jumlah_ttd').val('');

        if (_.isEmpty(jenis_lap)) {
            notyf.warning("Pilih Jenis Laporan terlebih dahulu!");
            $(this).val('').trigger('change');
            return;
        }

        if (_.isEmpty(dokumen)) {
            // kalau dokumen kosong, kembalikan state awal
            $('#jumlah_ttd').val('');
            $('#jumlah_ttd').trigger('change');
            return;
        }

        // Load data TTD
        loadDataTTD(jenis_lap, dokumen);
    });

    // inisialisasi awal
    $('#jumlah_ttd').trigger('change');

    $('#btn-submit').click(function (e) {
        e.preventDefault();

        let valAction = formManage.find('#input-action').val();
        if (_.isEmpty(valAction)) {
            notyf.error("Terjadi kesalahan, silahkan refresh ulang");
            return false;
        }

        let jenis_lap = $.trim($('#jenis_lap').val());
        let dokumen = $.trim($('#dokumen').val());
        let jumlah_ttd = $.trim($('#jumlah_ttd').val());

        if (!jenis_lap) {
            notyf.open({
                type: "warning",
                message: "Pilih Jenis Laporan terlebih dahulu!"
            });
            return;
        }

        if (!dokumen) {
            notyf.open({
                type: "warning",
                message: "Pilih Dokumen terlebih dahulu!"
            });
            return;
        }

        if (!jumlah_ttd) {
            notyf.open({
                type: "warning",
                message: "Pilih Jumlah TTD terlebih dahulu!"
            });
            return;
        }

        let urlActionText = (valAction === "store") ? "menyimpan data" : "mengubah data";

        if (formManage.valid()) {
            $.confirm({
                title: "Konfirmasi",
                theme: "modern",
                content: "Anda yakin ingin " + urlActionText + " yang telah diinput?",
                buttons: {
                    Tidak: {
                        text: "Tidak",
                        btnClass: "btn-warning",
                    },
                    Ya: {
                        text: "Ya",
                        btnClass: "btn-primary",
                        action: function () {
                            ajaxSubmitTTD();
                        }
                    }
                }
            });
        }
    });

});

// Fungsi untuk load data TTD via AJAX
function loadDataTTD(jenis_lap, dokumen) {
    let container = $('#ttd_container');
    
    $.ajax({
        url: defaultUrl + "getDataTTD",
        type: "POST",
        data: {
            jenis_lap: jenis_lap,
            dokumen: dokumen
        },
        beforeSend: function () {
            $(".loading").removeClass("hide");
        },
        success: function (res) {
            $(".loading").addClass("hide");

            if (res.error === 0) {
                let data = res.data || [];

                if (data.length === 0) {
                    // Kalau tidak ada data, set jumlah_ttd ke default dan render 1 blok TTD
                    // notyf.info("Belum ada data TTD untuk kombinasi jenis laporan dan dokumen ini");
                    notyf.open({
                        type: "warning",
                        message: "Belum ada data TTD untuk kombinasi jenis laporan dan dokumen ini!"
                    });
                    $('#jumlah_ttd').val('');
                    $('#ttd_container').html(''); // pastikan kosong
                    $('#jumlah_ttd').trigger('change'); // pakai handler jumlah untuk render 1 blok
                    return;
                }

                // Kalau ada data: set jumlah_ttd sama dengan panjang data, lalu render data
                $('#jumlah_ttd').val(data.length);

                // render data TTD
                data.forEach((item, i) => {
                    let index = i + 1;
                    if ((index - 1) % 2 === 0) {
                        container.append('<div class="row"></div>');
                    }
                    container.find('.row:last').append(renderTTDBlock(index, item));
                });

                renderSelect2Action();

            } else {
                notyf.error(res.message);
            }
        },
        error: function (xhr) {
            $(".loading").addClass("hide");
            notyf.error("Gagal ambil data: " + xhr.statusText);
        }
    });
}

// render block TTD (pakai ini dari kode sebelumnya)
function renderTTDBlock(index, item = {}) {
    return `
    <div class="col-md-6 ttd-block" data-index="${index}">
        <div class="card mb-3 p-2 shadow-sm">
            <h6 class="fw-bold mb-3">${index}.</h6>
            
            <div class="mb-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text fw-bold">Keterangan</span>
                    <input type="text" name="ttd[${index}][keterangan]" class="form-control form-control-sm" value="${item.keterangan ?? ''}">
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text fw-bold">Pegawai</span>
                    <select name="ttd[${index}][nama_temp]" class="form-control form-select form-control-sm select-nama-temp">
                        ${item.nama ? `<option value="${item.nama}" selected>${item.nama}</option>` : ""}
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text fw-bold">Nama</span>
                    <input type="text" name="ttd[${index}][nama]" class="form-control form-control-sm" value="${item.nama ?? ''}">
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text fw-bold">Jabatan</span>
                    <input type="text" name="ttd[${index}][jabatan]" class="form-control form-control-sm" value="${item.jabatan ?? ''}">
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text fw-bold">NUP</span>
                    <input type="text" name="ttd[${index}][nup]" class="form-control form-control-sm" value="${item.nup ?? ''}">
                </div>
            </div>
        </div>
    </div>`;
}

function ajaxSubmitTTD() {
    let valAction = formManage.find('#input-action').val();
    if (_.isEmpty(valAction)) {
        notyf.error("Tidak diketahui aksi");
        return false;
    }

    let urlAction = (valAction === "store") ? "saveData" : "updateData";

    let formData = new FormData(formManage[0]);
    formData.append(csrfKey, csrfValue);

    $.ajax({
        type: "POST",
        url: defaultUrl + urlAction,
        data: formData,
        processData: false, // penting untuk FormData
        contentType: false, // penting untuk FormData
        beforeSend: function () {
            $(".loading").removeClass("hide");
            $('#btn-submit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        },
        success: function (response) {
            $(".loading").addClass("hide");
            $('#btn-submit').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');

            if (response.error == 0) {
                notyf.success(response.message);
                // opsional: reset form atau reload container TTD
                $('#ttd_container').html('');
                $('#jumlah_ttd').trigger('change');
                window.location.href = defaultUrl;
            } else {
                notyf.error(response.message);
            }
        },
        error: function (e) {
            $(".loading").addClass("hide");
            $('#btn-submit').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
            notyf.error("Terjadi kesalahan: " + e.statusText);
        }
    });
}

function renderSelect2Action() {

    $("#dokumen").select2({
        allowClear: true,
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small select2-dropdown-up", // custom class for up orientation
        // dropdownParent: $('#elFilterParentDokumen'),
        placeholder: "Pilih Dokumen",
        dropdownCss: {
            'z-index': '10001 !important'
        },
        ajax: {
            url: "{{ url('panel/refselect2/getReffJurnalS2') }}",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                };
            },
            processResults: function (response) {
                var data = JSON.parse(response);

                var results = data.data.map(function (i) {
                    return {
                        id: i.jt_id,
                        text: `${i.jt_id} (${i.nama_jurnal})`,
                    };
                });

                return {
                    results: results,
                    pagination: {
                        more: data.has_more,
                    },
                };
            },
        },
        dropdownPosition: 'above' // this is the key option for opening upwards
    });


    $(".select-nama-temp").select2({
        allowClear: true,
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small",
        placeholder: "Pilih Nama",
        ajax: {
            url: "{{ url('panel/refselect2/getMasterUserModelS2') }}",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                };
            },
            processResults: function (response) {
                var data = JSON.parse(response);
                var results = data.data.map(function (i) {
                    return {
                        id: i.nama,
                        text: `${i.nup} (${i.nama})`,
                    };
                });
                return {
                    results: results,
                    pagination: {
                        more: data.has_more
                    }
                };
            },
        },
        dropdownPosition: 'beyond' // this is the key option for opening upwards
    }).on('select2:select', function (e) {
        // Ketika nama_temp dipilih, simpan juga ke input nama
        let selectedData = e.params.data;
        let ttdBlock = $(this).closest('.ttd-block');
        let namaInput = ttdBlock.find('input[name*="[nama]"]');
        
        if (selectedData && selectedData.id) {
            namaInput.val(selectedData.id);
        }
    });
}