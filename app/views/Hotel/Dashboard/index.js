window.defaultUrl = `${baseUrl}panel/hotel/dashboard/`;

$(document).ready(function() {
    console.log('Dashboard URL:', defaultUrl);
    
    // Load ECharts library if not available
    if (typeof echarts === 'undefined') {
        $.getScript('https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js')
            .done(function() {
                console.log('ECharts loaded successfully');
                initializeDashboard();
            })
            .fail(function() {
                console.error('Failed to load ECharts');
                initializeDashboard(); // Continue with fallback
            });
    } else {
        initializeDashboard();
    }
});

function initializeDashboard() {
    loadDashboardData();
    loadCharts();
    loadRecentBookings();
}

function loadDashboardData() {
    $.ajax({
        url: defaultUrl + 'stats',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                // Update statistics cards
                $('#pemesanan-bulan-ini').text(data.pemesanan_bulan_ini || 0);
                $('#kamar-tersedia').text(data.kamar_tersedia || 0);
                $('#pembatalan-bulan-ini').text(data.pembatalan_bulan_ini || 0);
                $('#pendapatan-bulan-ini').text('Rp ' + formatCurrency(data.pendapatan_bulan_ini || 0));
            } else {
                console.error('Error loading stats:', response.message);
                notyf.error('Gagal memuat data statistik: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr.responseText);
            notyf.error('Gagal memuat data statistik: ' + error);
        }
    });
}

function loadCharts() {
    // Load monthly bookings chart
    $.ajax({
        url: defaultUrl + 'chart-pemesanan',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                createBookingChart(response.data);
            }
        }
    });

    // Load room status chart
    $.ajax({
        url: defaultUrl + 'chart-kamar',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                createRoomChart(response.data);
            }
        }
    });
}

function createBookingChart(data) {
    const chartDom = document.querySelector('.echart-area-line-chart-example');
    if (!chartDom) {
        console.error('Chart container not found');
        return;
    }
    
    // Check if echarts is available
    if (typeof echarts === 'undefined') {
        console.error('ECharts library not found');
        chartDom.innerHTML = '<div class="text-center p-4"><p>Chart data loaded successfully</p><p>Labels: ' + data.labels.join(', ') + '</p><p>Values: ' + data.values.join(', ') + '</p></div>';
        return;
    }
    
    const myChart = echarts.init(chartDom);
    
    const option = {
        tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(239, 242, 246, 0.95)',
            borderColor: '#cbd0dd',
            textStyle: {
                color: '#14182c'
            },
            formatter: function(params) {
                return `
                    <div>
                        <h6 class="fs-9 text-body-tertiary mb-0">
                            <svg class="svg-inline--fa fa-circle me-1" style="color: #3874ff;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512z"></path>
                            </svg>
                            ${params[0].name} : ${params[0].value}
                        </h6>
                    </div>
                `;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: data.labels,
            axisLine: {
                lineStyle: {
                    color: '#cbd0dd'
                }
            },
            axisLabel: {
                color: '#6c757d'
            }
        },
        yAxis: {
            type: 'value',
            axisLine: {
                lineStyle: {
                    color: '#cbd0dd'
                }
            },
            axisLabel: {
                color: '#6c757d'
            },
            splitLine: {
                lineStyle: {
                    color: '#f1f3f6'
                }
            }
        },
        series: [{
            name: 'Pemesanan',
            type: 'line',
            stack: 'Total',
            smooth: true,
            areaStyle: {
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: 'rgba(56, 116, 255, 0.3)'
                    }, {
                        offset: 1, color: 'rgba(56, 116, 255, 0.05)'
                    }]
                }
            },
            lineStyle: {
                color: '#3874ff',
                width: 3
            },
            itemStyle: {
                color: '#3874ff'
            },
            data: data.values
        }]
    };
    
    myChart.setOption(option);
    
    // Make chart responsive
    window.addEventListener('resize', function() {
        myChart.resize();
    });
}

function createRoomChart(data) {
    const chartDom = document.querySelector('.echart-doughnut-chart');
    if (!chartDom) {
        console.error('Chart container not found');
        return;
    }
    
    // Check if echarts is available
    if (typeof echarts === 'undefined') {
        console.error('ECharts library not found');
        chartDom.innerHTML = '<div class="text-center p-4"><p>Room Status Data:</p><p>Ditempati: ' + data.ditempati + '</p><p>Tersedia: ' + data.tersedia + '</p><p>Maintenance: ' + data.maintenance + '</p></div>';
        return;
    }
    
    const myChart = echarts.init(chartDom);
    
    const option = {
        tooltip: {
            trigger: 'item',
            backgroundColor: 'rgba(239, 242, 246, 0.95)',
            borderColor: '#cbd0dd',
            textStyle: {
                color: '#14182c'
            }
        },
        legend: {
            bottom: '5%',
            left: 'center',
            textStyle: {
                color: '#6c757d'
            }
        },
        series: [{
            name: 'Status Kamar',
            type: 'pie',
            radius: ['40%', '70%'],
            center: ['50%', '45%'],
            avoidLabelOverlap: false,
            itemStyle: {
                borderRadius: 10,
                borderColor: '#fff',
                borderWidth: 2
            },
            label: {
                show: false,
                position: 'center'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: '18',
                    fontWeight: 'bold'
                }
            },
            labelLine: {
                show: false
            },
            data: [
                { value: data.ditempati, name: 'Ditempati', itemStyle: { color: '#dc3545' } },
                { value: data.tersedia, name: 'Tersedia', itemStyle: { color: '#28a745' } },
                { value: data.maintenance, name: 'Maintenance', itemStyle: { color: '#ffc107' } }
            ]
        }]
    };
    
    myChart.setOption(option);
    
    // Make chart responsive
    window.addEventListener('resize', function() {
        myChart.resize();
    });
}

function loadRecentBookings() {
    $.ajax({
        url: defaultUrl + 'recent-bookings',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                let tbody = $('#recent-bookings tbody');
                tbody.empty();
                
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(booking) {
                        let statusBadge = getStatusBadge(booking.status);
                        let row = `
                            <tr>
                                <td>${booking.kode_booking || '-'}</td>
                                <td>${booking.tamu_nama || '-'}</td>
                                <td>${booking.nomor_kamar || '-'}</td>
                                <td>${booking.tanggal_checkin || '-'}</td>
                                <td>${statusBadge}</td>
                                <td>Rp ${formatCurrency(booking.total_harga || 0)}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="6" class="text-center">Belum ada data pemesanan</td></tr>');
                }
            } else {
                console.error('Error loading recent bookings:', response.message);
                notyf.error('Gagal memuat data pemesanan terbaru: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr.responseText);
            notyf.error('Gagal memuat data pemesanan terbaru: ' + error);
        }
    });
}

function getStatusBadge(status) {
    const badges = {
        'menunggu': '<span class="badge bg-warning">Menunggu</span>',
        'dikonfirmasi': '<span class="badge bg-info">Dikonfirmasi</span>',
        'checkin': '<span class="badge bg-success">Check-in</span>',
        'checkout': '<span class="badge bg-secondary">Check-out</span>',
        'dibatalkan': '<span class="badge bg-danger">Dibatalkan</span>'
    };
    return badges[status] || '<span class="badge bg-light">-</span>';
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(amount);
}