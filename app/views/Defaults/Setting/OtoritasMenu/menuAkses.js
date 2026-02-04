window.defaultUrl = `${baseUrl}panel/setting/otoritas_menu/`;
var table;

$(document).ready(function () {
    loadMenu();

    $("#btn_back").click(function (e) {
        e.preventDefault();
        window.location.href = defaultUrl;
    });
});

function toggleSelectAll() {
    const isChecked = $('#select-all').prop('checked');
    $('.menu-checkbox').prop('checked', isChecked);
}

function getSelectedMenuIds() {
    const selectedMenus = [];
    $('.menu-checkbox:checked').each(function () {
        selectedMenus.push($(this).data('menu-id'));
    });
    return selectedMenus;
}

function batchSetAccess() {
    const selectedMenus = getSelectedMenuIds();
    if (selectedMenus.length === 0) {
        notyf.warning("Pilih menu yang akan diberi akses.");
        return false;
    }

    $.confirm({
        title: "Konfirmasi",
        theme: "modern",
        content: `Beri akses untuk ${selectedMenus.length} menu yang dipilih?`,
        buttons: {
            Tidak: {
                text: "Tidak",
                btnClass: "btn-warning",
            },
            Ya: {
                text: "Ya",
                btnClass: "btn-primary",
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: defaultUrl + "/setAksesBatch",
                        data: {
                            roleid: $("#id_hak").val(),
                            menuids: selectedMenus,
                            value: 1
                        },
                        dataType: "JSON",
                        success: function (data) {
                            if (data == 1) {
                                notyf.success("Berhasil memberikan akses untuk menu yang dipilih.");
                                loadMenu();
                            } else {
                                notyf.danger("Gagal memberikan akses.");
                            }
                        }
                    });
                }
            }
        }
    });
}

function batchRemoveAccess() {
    const selectedMenus = getSelectedMenuIds();
    if (selectedMenus.length === 0) {
        notyf.warning("Pilih menu yang akan dihapus aksesnya.");
        return false;
    }

    $.confirm({
        title: "Konfirmasi",
        theme: "modern",
        content: `Hapus akses untuk ${selectedMenus.length} menu yang dipilih?`,
        buttons: {
            Tidak: {
                text: "Tidak",
                btnClass: "btn-warning",
            },
            Ya: {
                text: "Ya",
                btnClass: "btn-primary",
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: defaultUrl + "/setAksesBatch",
                        data: {
                            roleid: $("#id_hak").val(),
                            menuids: selectedMenus,
                            value: 0
                        },
                        dataType: "JSON",
                        success: function (data) {
                            if (data == 1) {
                                notyf.success("Berhasil menghapus akses untuk menu yang dipilih.");
                                loadMenu();
                            } else {
                                notyf.error("Gagal menghapus akses.");
                            }
                        }
                    });
                }
            }
        }
    });
}

function doInsert(roleid, menuid) {
    $.confirm({
        title: "Konfirmasi",
        theme: "modern",
        content: "Beri Akses untuk Hak Akses ini ?",
        buttons: {
            Tidak: {
                text: "Tidak",
                btnClass: "btn-warning",
            },
            Ya: {
                text: "Ya",
                btnClass: "btn-primary",
                action: function () {
                    $.ajax({
                        type: "POST",
                        data: {
                            roleid: $("#id_hak").val(),
                            menuid: menuid,
                            value: 1,
                        },
                        url: defaultUrl + "/setAkses",
                        dataType: "JSON",
                        beforeSend: function (xhr) {
                            // beforeRequesting(code);
                        },
                        success: function (data) {
                            if (data == 1) {
                                notyf.success("Berhasil ditambahkan akses.");
                                loadMenu();
                                // location.reload();
                            } else {
                                notyf.error("Gagal ditambahkan akses.");
                                loadMenu();

                            }
                        },
                    });
                }
            }
        }
    });
}

function doDelete(roleid, menuid) {
    $.confirm({
        title: "Konfirmasi",
        theme: "modern",
        content: "Hapus Akses untuk Hak Akses ini ?",
        buttons: {
            Tidak: {
                text: "Tidak",
                btnClass: "btn-warning",
            },
            Ya: {
                text: "Ya",
                btnClass: "btn-primary",
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: defaultUrl + "/setAkses",
                        data: {
                            roleid: $("#id_hak").val(),
                            menuid: menuid,
                            value: 0,
                        },
                        dataType: "JSON",
                        beforeSend: function (xhr) {
                            // beforeRequesting(code);
                        },
                        success: function (data) {
                            if (data == 1) {
                                notyf.success("Berhasil dihapus akses.");
                                loadMenu();
                                // location.reload();
                            } else {
                                notyf.error("Gagal dihapus akses.");
                                loadMenu();

                            }
                        },
                    });
                }
            }
        }
    });
}

function loadMenu() {
    $.ajax({
        url: defaultUrl + "loadMenu",
        type: "POST",
        data: {
            id: $('#id_hak').val()
        },
        beforeSend: function () {
            $(".loading").removeClass('hide');
        },
        success: function (dt) {
            $("#table-data").empty();
            var dataMenu = JSON.parse(dt);
            var i = 1;

            $.each(dataMenu, function (index, value) {
                var listData = "<tr class='" + (value.parent == 0 ? "bg-head" : "") + "'>";

                // Checkbox column
                listData += "<td class='text-center'>";
                listData += "<input type='checkbox' class='menu-checkbox' data-menu-id='" + value.menu_id + "'>";
                listData += "</td>";

                // Number column
                listData += "<td class='text-center'>" + i + "</td>";

                // Menu name with proper indentation and icon
                listData += "<td>";
                var indent = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".repeat(value.parent_level);
                listData += indent;
                // Icon logic: use icon if available, else folder/file
                if (value.icon) {
                    listData += "<i data-feather='" + value.icon + "' class='feather-icon'></i> ";
                } else if (value.parent == 0) {
                    listData += "<i data-feather='folder' class='feather-icon'></i> ";
                } else {
                    listData += "<i data-feather='file' class='feather-icon'></i> ";
                }
                listData += value.nama;
                listData += "</td>";

                // URL column
                listData += "<td>" + (value.link || "-") + "</td>";

                // Access status column
                listData += "<td class='text-center'>";
                listData += value.id == 0 ?
                    "<i data-feather='x' class='feather-icon text-danger'></i>" :
                    "<i data-feather='check' class='feather-icon text-success'></i>";
                listData += "</td>";

                // Action column
                listData += "<td class='text-center'>";
                if (value.id == 0) {
                    listData += "<button class='btn btn-sm btn-success' onclick='doInsert(" + value.id + "," + value.menu_id + ")'><i data-feather='check' class='feather-icon'></i> Beri Akses</button>";
                } else {
                    listData += "<button class='btn btn-sm btn-danger' onclick='doDelete(" + value.id + "," + value.menu_id + ")'><i data-feather='x' class='feather-icon'></i> Hapus Akses</button>";
                }
                listData += "</td>";

                listData += "</tr>";
                $("#table-data").append(listData);
                i++;
            });

            // Initialize Feather icons
            feather.replace();
            $(".loading").addClass('hide');
        },
        error: function (e) {
            alert('Error: ' + e);
            $(".loading").addClass('hide');
        }
    });
}