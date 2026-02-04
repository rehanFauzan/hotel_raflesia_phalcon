window.defaultUrl = `${baseUrl}dashboard/`;

const m_periode = '{{ m_periode }}';
const y_periode = '{{ y_periode }}';
const tahun_berjalan = '{{ tahun_berjalan }}';
const fmtID = v => Number(v || 0).toLocaleString('id-ID');

// Format angka untuk axis grafik (disingkat: Juta, Miliar, Triliun)
const fmtAxis = value => {
    const num = Math.abs(value);
    if (num >= 1e12) {
        return Number((value / 1e12).toFixed(1)).toLocaleString('id-ID') + ' T'; // Triliun
    } else if (num >= 1e9) {
        return Number((value / 1e9).toFixed(1)).toLocaleString('id-ID') + ' M'; // Miliar
    } else if (num >= 1e6) {
        return Number((value / 1e6).toFixed(1)).toLocaleString('id-ID') + ' Jt'; // Juta
    } else if (num >= 1e3) {
        return Number((value / 1e3).toFixed(1)).toLocaleString('id-ID') + ' Rb'; // Ribu
    }
    return Number(value.toFixed(0)).toLocaleString('id-ID');
};

$(document).ready(function () {

    // Element 1 Tentang Voucher
    getDataVoucher();

    // Element 2 Tentang Laba Rugi
    getDataLabaRugi();

    // Element 3 Tentang Cash Flow
    getDataCashFlow();

    // Element 4 Realisasi Anggaran Pendapatan Dan Biaya
    getDataRealisasiAnggaranPendapatanDanBiaya();

    // Element 5 Grafik Laba Rugi dan Pendapatan Realiasi
    getDataGrafikLrDanRealisasiBiayaPendInv();

});

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

                $('#lbl_bulan_ini_pendapatan').text(`Rp. ${Number( Number(dataFetch[1].pendapatan).toFixed(2) ).toLocaleString('id-ID')}`);
                $('#lbl_bulan_ini_biaya').text(`Rp. ${Number( Number(dataFetch[1].biaya).toFixed(2) ).toLocaleString('id-ID')}`)

                $('#lbl_sd_bulan_ini_pendapatan').text(`Rp. ${Number(Number(dataFetch[0].pendapatan).toFixed(2)).toLocaleString('id-ID')}`);
                $('#lbl_sd_bulan_ini_biaya').text(`Rp. ${Number(Number(dataFetch[0].biaya).toFixed(2)).toLocaleString('id-ID')}`);

                let bulanIniSelisih = Number(dataFetch[1].selisih).toFixed(2);
                $('#containerBulanIniSelisih').removeClass('bg-secondary');
                $('#lbl_icon_bulan_ini_selisih').removeClass('fa-question');
                if (bulanIniSelisih < 0) {
                    $('#lbl_bulan_ini_selisih').text(`RUGI : ${Number(bulanIniSelisih).toLocaleString('id-ID')}`);
                    $('#containerBulanIniSelisih').addClass('bg-danger');
                    $('#lbl_icon_bulan_ini_selisih').addClass('fa-arrow-trend-down');
                } else {
                    $('#lbl_bulan_ini_selisih').text(`LABA : ${Number(bulanIniSelisih).toLocaleString('id-ID')}`);
                    $('#containerBulanIniSelisih').addClass('bg-success');
                    $('#lbl_icon_bulan_ini_selisih').addClass('fa-arrow-trend-up');
                }

                let sdBulanIniSelisih = Number(dataFetch[0].selisih).toFixed(2);
                $('#containerSdBulanIniSelisih').removeClass('bg-secondary');
                $('#lbl_icon_sd_bulan_ini_selisih').removeClass('fa-question');
                if (sdBulanIniSelisih < 0) {
                    $('#lbl_sd_bulan_ini_selisih').text(`RUGI : ${Number(sdBulanIniSelisih).toLocaleString('id-ID')}`);

                    $('#containerSdBulanIniSelisih').addClass('bg-danger');
                    $('#lbl_icon_sd_bulan_ini_selisih').addClass('fa-arrow-trend-down');
                } else {
                    $('#lbl_sd_bulan_ini_selisih').text(`LABA : ${Number(sdBulanIniSelisih).toLocaleString('id-ID')}`);

                    $('#containerSdBulanIniSelisih').addClass('bg-success');
                    $('#lbl_icon_sd_bulan_ini_selisih').addClass('fa-arrow-trend-up');
                }

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

function getDataCashFlow() {
    $.ajax({
        type: "POST",
        data: {
            tahun: y_periode,
            bulan: m_periode
        },
        url: defaultUrl + "getDataCashFlow",
        beforeSend: function (xhr, settings) {
            $(".loading").removeClass("hide");
        },
        success: function (response) {
            $(".loading").addClass("hide");
            console.log("Data Cash Flow ", response);
            let dataFetch = response.dataFetch;

            if (response.error == 0) {
                notyf.success(response.message);

                let saldoAwal = Number(dataFetch[0].debet).toFixed(2);
                let penerimaan = Number(dataFetch[1].debet).toFixed(2);
                let pengeluaran = Number(dataFetch[1].credit).toFixed(2);

                let totalPenerimaan = 0;
                let totalPengeluaran = 0;
                $.each(dataFetch, function (index, value) {
                    totalPenerimaan += Number(value.debet) || 0;
                    totalPengeluaran += Number(value.credit) || 0;
                });
                let totalSaldoAkhir = Number(totalPenerimaan.toFixed(2)) - Number(totalPengeluaran.toFixed(2));

                $('#lbl_cash_flow_saldo_awal').text(`Rp. ${Number(saldoAwal).toLocaleString('id-ID')}`);
                $('#lbl_cash_flow_penerimaan').text(`Rp. ${Number(penerimaan).toLocaleString('id-ID')}`);
                $('#lbl_cash_flow_pengeluaran').text(`Rp. ${Number(pengeluaran).toLocaleString('id-ID')}`);
                $('#lbl_cash_flow_saldo_akhir').text(`Rp. ${Number(totalSaldoAkhir).toLocaleString('id-ID')}`);

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

function getDataRealisasiAnggaranPendapatanDanBiaya() {
    $.ajax({
        type: "POST",
        data: {
            tahun: y_periode,
            bulan: m_periode
        },
        url: defaultUrl + "getDataRealisasiAnggaranPendapatanDanBiaya",
        beforeSend: function (xhr, settings) {
            $(".loading").removeClass("hide");
        },
        success: function (response) {
            $(".loading").addClass("hide");
            console.log("Data Realisasi Anggaran Pendapatan Dan Biaya ", response);
            let dataFetch1 = response.dataFetch1;
            let dataFetch2 = response.dataFetch2;

            if (response.error == 0) {
                notyf.success(response.message);
                try {
                    const toNumber = v => Number(v || 0);
                    const toPct = v => {
                        const n = Number(v);
                        if (Number.isFinite(n)) return n.toFixed(2) + '%';
                        // fallback if server sends like ".00"
                        const m = /^\.?\d+$/.test(String(v)) ? Number('0' + v) : 0;
                        return m.toFixed(2) + '%';
                    };

                    // Pendapatan
                    const rowsPendapatan = (Array.isArray(dataFetch1) ? dataFetch1 : []).map((it, idx) => {
                        const anggaran = toNumber(it.anggaran);
                        const realisasi = toNumber(it.realisasi);
                        const persentase = toPct(it.persentase);
                        return `<tr class="row-compact fs-9">
                            <td class="text-start fw-semibold fs-9">${idx + 1}.</td>
                            <td class="text-left fs-9">${it.acc_name || ''}</td>
                            <td class="text-end">${Number(anggaran).toLocaleString('id-ID')}</td>
                            <td class="text-end">${Number(realisasi).toLocaleString('id-ID')}</td>
                            <td class="text-center">${persentase}</td>
                        </tr>`;
                    }).join('');
                    $('#tb-anggaran-pendapatan').html(rowsPendapatan || '<tr><td colspan="5" class="text-center text-muted fs-9">Tidak ada data</td></tr>');
                    // Totals Pendapatan
                    const totalAnggaranPend = (Array.isArray(dataFetch1) ? dataFetch1 : []).reduce((s, it) => s + toNumber(it.anggaran), 0);
                    const totalRealisasiPend = (Array.isArray(dataFetch1) ? dataFetch1 : []).reduce((s, it) => s + toNumber(it.realisasi), 0);
                    const totalPersenPend = totalAnggaranPend > 0 ? (totalRealisasiPend / totalAnggaranPend) * 100 : 0;
                    $('#total-anggaran-pendapatan').text(Number(totalAnggaranPend).toLocaleString('id-ID'));
                    $('#total-realisasi-pendapatan').text(Number(totalRealisasiPend).toLocaleString('id-ID'));
                    $('#total-persentase-pendapatan').text(totalPersenPend.toFixed(2) + '%');

                    // Biaya
                    const rowsBiaya = (Array.isArray(dataFetch2) ? dataFetch2 : []).map((it, idx) => {
                        const anggaran = toNumber(it.anggaran);
                        const realisasi = toNumber(it.realisasi);
                        const persentase = toPct(it.persentase);
                        return `<tr class="row-compact fs-9">
                            <td class="text-start fw-semibold fs-9">${idx + 1}.</td>
                            <td class="text-left fs-9">${it.acc_name || ''}</td>
                            <td class="text-end">${Number(anggaran).toLocaleString('id-ID')}</td>
                            <td class="text-end">${Number(realisasi).toLocaleString('id-ID')}</td>
                            <td class="text-center">${persentase}</td>
                        </tr>`;
                    }).join('');
                    $('#tb-anggaran-biaya').html(rowsBiaya || '<tr><td colspan="5" class="text-center text-muted fs-9">Tidak ada data</td></tr>');
                    
                    // Totals Biaya
                    const totalAnggaranBiaya = (Array.isArray(dataFetch2) ? dataFetch2 : []).reduce((s, it) => s + toNumber(it.anggaran), 0);
                    const totalRealisasiBiaya = (Array.isArray(dataFetch2) ? dataFetch2 : []).reduce((s, it) => s + toNumber(it.realisasi), 0);
                    const totalPersenBiaya = totalAnggaranBiaya > 0 ? (totalRealisasiBiaya / totalAnggaranBiaya) * 100 : 0;

                    $('#total-anggaran-biaya').text(Number(totalAnggaranBiaya).toLocaleString('id-ID'));
                    $('#total-realisasi-biaya').text(Number(totalRealisasiBiaya).toLocaleString('id-ID'));
                    $('#total-persentase-biaya').text(totalPersenBiaya.toFixed(2) + '%');
                } catch (err) {
                    console.error('Render Realisasi Anggaran error:', err);
                    notyf.error('Gagal memproses data realisasi anggaran');
                }

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


function getDataGrafikLrDanRealisasiBiayaPendInv() {
    $.ajax({
        type: "POST",
        data: {
            tahun: y_periode,
            bulan: m_periode
        },
        url: defaultUrl + "getDataGrafikLrDanRealisasiBiayaPendInv",
        beforeSend: function (xhr, settings) {
            $(".loading").removeClass("hide");
        },
        success: function (response) {
            $(".loading").addClass("hide");
            console.log("Data Grafik ", response);
            let dataFetch = response.dataFetch;

            if (response.error == 0) {
                notyf.success(response.message);

                // Cek apakah ECharts sudah ter-load
                if (!window.echarts) {
                    console.error('ECharts belum ter-load');
                    return;
                }

                // Nama bulan
                const months = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];

                // Konversi data dari server dan bulatkan ke 2 desimal
                const toNum = s => {
                    const num = Number(s || 0);
                    // Bulatkan ke 2 desimal untuk konsistensi
                    return Number(num.toFixed(2));
                };

                // Ambil data dari dataFetch dan mapping ke array
                const labaRugi = [];
                const pendapatan = [];
                const biaya = [];

                // Urutkan data berdasarkan bulan dan mapping ke array
                for (let i = 1; i <= 12; i++) {
                    const monthData = dataFetch.find(d => d.djo_mperiod == i);
                    if (monthData) {
                        labaRugi.push(toNum(monthData.lr));
                        pendapatan.push(toNum(monthData.pendapatan));
                        biaya.push(toNum(monthData.biaya));
                    } else {
                        labaRugi.push(0);
                        pendapatan.push(0);
                        biaya.push(0);
                    }
                }

                // ------- COMMON OPTIONS -------
                const baseGrid = {
                    left: 14,
                    right: 20,
                    top: 50,
                    bottom: 50,
                    containLabel: true
                };

                const yAxisCommon = {
                    type: 'value',
                    name: 'Nilai (Rp)',
                    nameGap: 30,
                    nameTextStyle: {
                        color: '#64748b',
                        fontSize: 12
                    },
                    axisLabel: {
                        formatter: value => fmtAxis(value),
                        margin: 12
                    },
                    splitLine: {
                        lineStyle: {
                            color: '#e5e7eb'
                        }
                    }
                };

                // ===== GRAFIK 1: LABA RUGI =====
                const plElement = document.getElementById('plChart');
                if (plElement) {
                    const pl = echarts.init(plElement);

                    // Warna hijau gelap untuk garis utama
                    const darkGreen = '#15803d';

                    // Pisahkan data positif dan negatif untuk warna berbeda
                    const lrPositive = Array(labaRugi.length).fill(null);
                    const lrNegative = Array(labaRugi.length).fill(null);
                    
                    for (let i = 1; i < labaRugi.length; i++) {
                        const a = labaRugi[i - 1];
                        const b = labaRugi[i];
                        const isPosSeg = a > 0 && b > 0;
                        const isNegSeg = a <= 0 || b <= 0;
                        
                        if (isPosSeg) {
                            lrPositive[i - 1] = a;
                            lrPositive[i] = b;
                        }
                        if (isNegSeg) {
                            lrNegative[i - 1] = a;
                            lrNegative[i] = b;
                        }
                    }

                    pl.setOption({
                        grid: {
                            left: 8,
                            right: 20,
                            top: 50,
                            bottom: 50,
                            containLabel: true
                        },
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'line'
                            },
                            backgroundColor: 'rgba(17,24,39,.92)',
                            borderWidth: 0,
                            padding: 10,
                            textStyle: {
                                color: '#fff'
                            },
                            formatter: params => {
                                const it = params.find(x => x.value != null);
                                const val = it ? it.value : 0;
                                const label = params[0].axisValue;
                                return `<div style="font-weight:600;margin-bottom:2px">${label}</div>
                                <div>Nilai: <b>Rp ${fmtID(val)}</b></div>`;
                            }
                        },
                        legend: {
                            data: ['Total']
                        },
                        xAxis: {
                            type: 'category',
                            data: months
                        },
                        yAxis: {
                            type: 'value',
                            name: 'Nilai (Rp)',
                            nameGap: 30,
                            nameTextStyle: {
                                color: '#64748b',
                                fontSize: 12
                            },
                            axisLabel: {
                                formatter: v => fmtAxis(v),
                                margin: 12
                            },
                            splitLine: {
                                lineStyle: {
                                    color: '#e5e7eb'
                                }
                            }
                        },
                        series: [{
                                name: 'Total',
                                type: 'line',
                                smooth: true,
                                data: lrPositive,
                                symbol: 'circle',
                                symbolSize: 7,
                                lineStyle: {
                                    width: 3,
                                    color: darkGreen
                                },
                                itemStyle: {
                                    color: darkGreen
                                },
                                connectNulls: false
                            },
                            {
                                name: 'Total',
                                type: 'line',
                                smooth: true,
                                data: lrNegative,
                                symbol: 'circle',
                                symbolSize: 7,
                                lineStyle: {
                                    width: 3,
                                    color: '#ef4444'
                                },
                                itemStyle: {
                                    color: '#ef4444'
                                },
                                connectNulls: false,
                                markLine: {
                                    data: [{
                                        yAxis: 0
                                    }],
                                    lineStyle: {
                                        type: 'dashed',
                                        color: '#94a3b8'
                                    },
                                    symbol: 'none'
                                }
                            }
                        ]
                    });

                    // Resize setelah layout settle
                    setTimeout(() => {
                        pl.resize();
                    }, 100);

                    // Handle resize window
                    window.addEventListener('resize', () => {
                        pl.resize();
                    });
                }

                // ===== GRAFIK 2: PENDAPATAN VS BIAYA (Lazy Init) =====
                let rb = null;

                function initRb() {
                    if (rb) {
                        rb.resize();
                        return;
                    }
                    
                    const rbElement = document.getElementById('rbChart');
                    if (!rbElement) return;
                    
                    rb = echarts.init(rbElement);
                    rb.setOption({
                        grid: baseGrid,
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'shadow'
                            },
                            backgroundColor: 'rgba(17,24,39,.92)',
                            borderWidth: 0,
                            padding: 10,
                            textStyle: {
                                color: '#fff'
                            },
                            formatter: params => {
                                let html = `<div style="font-weight:600;margin-bottom:4px">${params[0].axisValue}</div>`;
                                for (const it of params) {
                                    html += `<div style="display:flex;justify-content:space-between;gap:12px">
                                   <span>${it.marker}${it.seriesName}</span><b>Rp ${fmtID(it.value)}</b>
                                 </div>`;
                                }
                                return html;
                            }
                        },
                        legend: {
                            data: ['Pendapatan dan Investasi', 'Biaya']
                        },
                        xAxis: {
                            type: 'category',
                            data: months,
                            axisLabel: {
                                interval: 0,
                                rotate: 35
                            }
                        },
                        yAxis: {
                            ...yAxisCommon,
                            name: 'Nominal'
                        },
                        series: [{
                                name: 'Pendapatan dan Investasi',
                                type: 'bar',
                                data: pendapatan,
                                itemStyle: {
                                    color: '#22c55e'
                                }
                            },
                            {
                                name: 'Biaya',
                                type: 'bar',
                                data: biaya,
                                itemStyle: {
                                    color: '#ef4444'
                                }
                            }
                        ]
                    });

                    // Handle resize
                    window.addEventListener('resize', () => {
                        rb && rb.resize();
                    });
                }

                // Tampilkan grafik ketika tab dibuka
                const tabTrigger = document.getElementById('realisasibiayapendapatan-tab');
                if (tabTrigger) {
                    tabTrigger.addEventListener('shown.bs.tab', initRb);
                }

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