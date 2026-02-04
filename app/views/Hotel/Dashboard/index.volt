{% extends 'template/base.volt' %}

{% block title %}
    Dashboard Hotel
{% endblock %}

{% block inline_style %}
<style>
    .stat-card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .chart-container {
        position: relative;
        height: 400px;
    }
</style>
{% endblock %}

{% block content %}
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">Dashboard Hotel</h2>
            <p class="text-muted">Ringkasan informasi hotel dan transaksi</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-body-secondary text-black">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-white bg-opacity-25 text-primary me-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h3 class="mb-0" id="pemesanan-bulan-ini">0</h3>
                            <small>Pemesanan Bulan Ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-body-secondary text-black">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-white bg-opacity-25 text-success me-3">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div>
                            <h3 class="mb-0" id="kamar-tersedia">0</h3>
                            <small>Kamar Tersedia</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-body-secondary text-black">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-white bg-opacity-25 text-danger me-3">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div>
                            <h3 class="mb-0" id="pembatalan-bulan-ini">0</h3>
                            <small>Pembatalan Bulan Ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-body-secondary text-black">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-white bg-opacity-25 text-info me-3">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <h3 class="mb-0" id="pendapatan-bulan-ini">Rp 0</h3>
                            <small>Pendapatan Bulan Ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Grafik Pemesanan Per Bulan</h5>
                </div>
                <div class="card-body">
                    <div class="echart-area-line-chart-example" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Status Kamar</h5>
                </div>
                <div class="card-body">
                    <div class="echart-doughnut-chart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pemesanan Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="recent-bookings">
                            <thead>
                                <tr>
                                    <th>Kode Booking</th>
                                    <th>Tamu</th>
                                    <th>Kamar</th>
                                    <th>Check-in</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan dimuat via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %}

{% block inline_script %}
    {% include "Hotel/Dashboard/index.js" %}
{% endblock %}