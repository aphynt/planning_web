@include('layout.head', ['title' => 'Durasi Refueling Fuel Station'])
@include('layout.header')
@include('layout.theme_settings')
@include('layout.sidebar')

<style>
    @media (max-width: 767.98px) {
        .dt-buttons {
            display: none !important;
        }
    }

    #tblDuration {
        width: 100% !important;
        margin-bottom: 0;
    }

    #tblDuration th {
        background: #fcfbfb;
        color: #000000;
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
    }

    #tblDuration td {
        vertical-align: middle;
    }

    #tblDuration tbody tr:hover {
        background: #f8f9fa;
    }
    #tblAverageDuration {
        width: 100% !important;
        margin-bottom: 0;
    }

    #tblAverageDuration th {
        background: #fcfbfb;
        color: #000000;
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
    }

    #tblAverageDuration td {
        vertical-align: middle;
    }

    #tblAverageDuration tbody tr:hover {
        background: #f8f9fa;
    }
    .table-warning td {
        font-weight: bold;
    }

    .duration-total-row td,
    .average-total-row td {
        background: #fff3cd !important;
        color: #000 !important;
        font-weight: bold !important;
    }

    .unit-ready {
        background-color: #008FFB !important;
        color: #fff !important;
    }

    .unit-standby {
        background-color: #00E396 !important;
        color: #fff !important;
    }

    .unit-delay {
        background-color: #FEB019 !important;
        color: #fff !important;
    }

    .unit-breakdown {
        background-color: #FF4560 !important;
        color: #fff !important;
    }

    .unit-unknown {
        background-color: #6c757d !important;
        color: #fff !important;
    }
    #tblDuration thead th.unit-ready,
    #tblDuration thead th.unit-standby,
    #tblDuration thead th.unit-delay,
    #tblDuration thead th.unit-breakdown,
    #tblDuration thead th.unit-unknown {
        color: #fff !important;
        font-weight: 600 !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    #tblAverageDuration thead th.unit-ready,
    #tblAverageDuration thead th.unit-standby,
    #tblAverageDuration thead th.unit-delay,
    #tblAverageDuration thead th.unit-breakdown,
    #tblAverageDuration thead th.unit-unknown {
        color: #fff !important;
        font-weight: 600 !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .duration-header-title {
        background: #fcfbfb !important;
        color: #000 !important;
        font-weight: 600 !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .average-title {
        background: #fcfbfb !important;
        color: #000 !important;
        font-weight: 600 !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .hour-cell {
        font-weight: 600;
        text-align: center;
        background: #fcfbfb;
        min-width: 65px;
        white-space: nowrap;
    }

    .unit-name {
        text-align: left;
        min-width: 135px;
        padding-left: 8px !important;
        white-space: nowrap;
    }

    .duration-cell {
        text-align: center;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    .average-cell {
        text-align: center;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    .chart-card {
        border: 1px solid #dee2e6;
        background: #fff;
        padding: 10px;
        min-height: 350px;
    }

    .fuel-truck-chart-card {
        border: 1px solid #dee2e6;
        background: #fff;
        margin-bottom: 20px;
    }

    .fuel-truck-chart-title {
        font-size: 16px;
        font-weight: 600;
        padding: 12px 15px 0 15px;
    }

    .duration-filter label {
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 5px;
    }

    .duration-filter .form-control,
    .duration-filter .form-select {
        height: 36px;
        font-size: 13px;
    }

    .duration-filter .btn {
        height: 36px;
        font-size: 13px;
        font-weight: 500;
    }

    #durationLoading {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, 0.65);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .duration-loading-box {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px 30px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .08);
        font-size: 13px;
        color: #666;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .dataTables_wrapper {
        width: 100%;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        font-size: 12px;
    }

    .dt-buttons .btn {
        font-size: 12px;
    }

    @media (max-width: 767.98px) {

        #tblDuration,
        #tblAverageDuration {
            font-size: 11px;
        }

        #tblDuration th,
        #tblDuration td,
        #tblAverageDuration th,
        #tblAverageDuration td {
            padding: 6px 8px;
        }

        .unit-name {
            min-width: 120px;
        }

        .hour-cell {
            min-width: 60px;
        }

        .duration-filter {
            width: 100%;
        }

        .duration-filter .form-control,
        .duration-filter .form-select,
        .duration-filter .btn {
            width: 100%;
        }

        .fuel-truck-chart-title {
            font-size: 14px;
        }
    }

    .status-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
        font-size: 12px;
    }

    .status-legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .status-legend-color {
        width: 35px;
        height: 16px;
        border-radius: 2px;
        border: 1px solid rgba(0, 0, 0, .15);
    }

    .legend-ready {
        background: #008FFB;
    }

    .legend-standby {
        background: #00E396;
    }

    .legend-delay {
        background: #FEB019;
    }

    .legend-breakdown {
        background: #FF4560;
    }

    .average-duration-block {
        margin-bottom: 22px;
    }

    .average-duration-title {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .average-duration-table {
        width: 100% !important;
        margin-bottom: 0;
    }

    .average-duration-table th {
        background: #fcfbfb;
        color: #000;
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
    }

    .average-duration-table td {
        vertical-align: middle;
    }

    .average-duration-table .unit-name {
        text-align: left;
        min-width: 110px;
        white-space: nowrap;
    }

    .duration-summary-label {
        background: #fff3cd !important;
        font-weight: 700 !important;
    }

</style>


<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="fw-semibold">Durasi Refueling Fuel Station</h4>

                    <div class="col-12">
                        <div class="row">
                            <div class="col-6 col-md-2 mb-2">
                                <label for="tanggalStatus">Tanggal</label>
                                <input type="text" id="tanggalStatus" class="form-control" name="tanggalStatus">
                            </div>

                            <div class="col-6 col-md-1 mb-2">
                                <label for="shift">Shift</label>
                                <select class="form-select" name="shift" id="shift">
                                    <option value="6" selected>Siang</option>
                                    <option value="7">Malam</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-2 mb-2">
                                <label for="summaryMode">Tampilan</label>
                                <select class="form-select" id="summaryMode">
                                    <option value="total" selected>Total</option>
                                    <option value="average">Rata-rata</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-3 mb-2 d-flex align-items-end gap-2">
                                <button id="cariStatus"
                                        class="btn btn-primary flex-fill"
                                        style="padding-top:10px;padding-bottom:10px;">
                                    Tampilkan
                                </button>

                                <button type="button"
                                        id="exportAllExcel"
                                        class="btn btn-success flex-fill"
                                        style="padding-top:10px;padding-bottom:10px;">
                                    <i class="ri-file-excel-2-line"></i> Export Excel
                                </button>
                            </div>

                            <div class="col-12 col-md-4 mb-2 d-flex align-items-end justify-content-end">
                                <div class="status-legend d-flex align-items-center gap-3">
                                    <div class="status-legend-item">
                                        <span class="status-legend-color legend-ready"></span>
                                        <span>Ready</span>
                                    </div>

                                    <div class="status-legend-item">
                                        <span class="status-legend-color legend-standby"></span>
                                        <span>Standby</span>
                                    </div>

                                    <div class="status-legend-item">
                                        <span class="status-legend-color legend-delay"></span>
                                        <span>Delay</span>
                                    </div>

                                    <div class="status-legend-item">
                                        <span class="status-legend-color legend-breakdown"></span>
                                        <span>Breakdown</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tblDuration" class="table table-bordered table-sm align-middle text-center w-100" >
                                <thead id="tblDurationHeader"></thead>
                                <tbody id="tblDurationBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div id="averageDurationTables"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="durationLoading">
    <div class="duration-loading-box">
        <div class="spinner-border text-primary mb-2"></div>
        <div>Mohon tunggu sebentar...</div>
    </div>
</div>

@include('layout.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tanggalInput = document.getElementById('tanggalStatus');
        const shiftInput = document.getElementById('shift');
        const cariButton = document.getElementById('cariStatus');
        const loading = document.getElementById('durationLoading');

        let latestDurationResponse = null;

        if (typeof flatpickr !== 'undefined') {
            flatpickr(tanggalInput, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                allowInput: true
            });
        }

        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');

        tanggalInput.placeholder = `${yyyy}-${mm}-${dd} to ${yyyy}-${mm}-${dd}`;
        tanggalInput.value = `${yyyy}-${mm}-${dd}`;

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatDuration(value) {
            const number = Number(value || 0);

            if (!Number.isFinite(number) || number === 0) {
                return '-';
            }

            return number.toFixed(2);
        }

        function getStationData(res, station, hour, unit) {
            if (station === 'All') {
                return {
                    duration: Number(res.allDurationPivot?.[hour]?.[unit] || 0),
                    frequency: Number(res.allFrequencyPivot?.[hour]?.[unit] || 0)
                };
            }

            return {
                duration: Number(res.durationPivot?.[station]?.[hour]?.[unit] || 0),
                frequency: Number(res.frequencyPivot?.[station]?.[hour]?.[unit] || 0)
            };
        }

        function getAverage(duration, frequency) {
            return frequency > 0 ? duration / frequency : 0;
        }

        // Tampilan: Total = jumlah durasi, Rata-rata = durasi / frekuensi
        function getDisplayDuration(duration, frequency, mode) {
            return mode === 'total'
                ? Number(duration || 0)
                : getAverage(duration, frequency);
        }

        // =========================================================
        // TABEL KIRI
        // Format sesuai referensi:
        // Jam | Status | SM-B1 | SM-B2 | Total
        // =========================================================
        function renderDurationTable(res) {
            const hours = res.hours || [];
            const units = res.units || [];
            const mode = $('#summaryMode').val() || 'total';
            const modeLabel = mode === 'total' ? 'Total' : 'Rata-rata';

            let header = `
                <tr>
                    <th rowspan="2" class="text-center align-middle">Jam</th>
                    <th rowspan="2" class="text-center align-middle">Status</th>
                    <th colspan="3" class="duration-header-title">
                        ${mode === 'total' ? 'Durasi total (menit)' : 'Durasi rata-rata (menit)'}
                    </th>
                </tr>
                <tr>
                    <th class="text-center">SM-B1</th>
                    <th class="text-center">SM-B2</th>
                    <th class="text-center">Total</th>
                </tr>
            `;

            document.getElementById('tblDurationHeader').innerHTML = header;

            let body = '';

            // =========================================================
            // DATA PER JAM
            // =========================================================
            hours.forEach(function (hour) {
                units.forEach(function (unit, unitIndex) {
                    body += '<tr>';

                    if (unitIndex === 0) {
                        body += `
                            <td rowspan="${units.length + 1}" class="hour-cell">
                                ${escapeHtml(hour)}
                            </td>
                        `;
                    }

                    body += `
                        <td class="unit-name">
                            ${escapeHtml(unit)}
                        </td>
                    `;

                    const b1 = getStationData(res, 'SM-B1', hour, unit);
                    const b2 = getStationData(res, 'SM-B2', hour, unit);

                    const totalDuration = b1.duration + b2.duration;
                    const totalFrequency = b1.frequency + b2.frequency;

                    body += `
                        <td class="text-center">
                            ${formatDuration(
                                getDisplayDuration(b1.duration, b1.frequency, mode)
                            )}
                        </td>
                        <td class="text-center">
                            ${formatDuration(
                                getDisplayDuration(b2.duration, b2.frequency, mode)
                            )}
                        </td>
                        <td class="text-center fw-bold">
                            ${formatDuration(
                                getDisplayDuration(totalDuration, totalFrequency, mode)
                            )}
                        </td>
                    `;

                    body += '</tr>';
                });

                // Total per jam
                let hourDurationB1 = 0;
                let hourFrequencyB1 = 0;
                let hourDurationB2 = 0;
                let hourFrequencyB2 = 0;

                units.forEach(function (unit) {
                    const b1 = getStationData(res, 'SM-B1', hour, unit);
                    const b2 = getStationData(res, 'SM-B2', hour, unit);

                    hourDurationB1 += b1.duration;
                    hourFrequencyB1 += b1.frequency;
                    hourDurationB2 += b2.duration;
                    hourFrequencyB2 += b2.frequency;
                });

                const hourDurationTotal = hourDurationB1 + hourDurationB2;
                const hourFrequencyTotal = hourFrequencyB1 + hourFrequencyB2;

                body += `
                    <tr class="duration-total-row">
                        <td class="text-start fw-bold">Total</td>
                        <td class="text-center fw-bold">
                            ${formatDuration(
                                getDisplayDuration(hourDurationB1, hourFrequencyB1, mode)
                            )}
                        </td>
                        <td class="text-center fw-bold">
                            ${formatDuration(
                                getDisplayDuration(hourDurationB2, hourFrequencyB2, mode)
                            )}
                        </td>
                        <td class="text-center fw-bold">
                            ${formatDuration(
                                getDisplayDuration(hourDurationTotal, hourFrequencyTotal, mode)
                            )}
                        </td>
                    </tr>
                `;
            });

            // =========================================================
            // GRAND TOTAL SHIFT PER UNIT
            // Struktur sesuai gambar:
            // Grand Total | Hauler | B1 | B2 | Total
            //             | Grader | B1 | B2 | Total
            //             | Dozer  | B1 | B2 | Total
            //             | Total  | B1 | B2 | Total
            // =========================================================
            let grandTotalDurationB1 = 0;
            let grandTotalFrequencyB1 = 0;
            let grandTotalDurationB2 = 0;
            let grandTotalFrequencyB2 = 0;

            units.forEach(function (unit, unitIndex) {
                body += `<tr class="duration-total-row">`;

                if (unitIndex === 0) {
                    body += `
                        <td rowspan="${units.length + 1}"
                            class="align-middle text-start fw-bold">
                            Grand Total
                        </td>
                    `;
                }

                body += `
                    <td class="text-start fw-bold">
                        ${escapeHtml(unit)}
                    </td>
                `;

                let unitDurationB1 = 0;
                let unitFrequencyB1 = 0;
                let unitDurationB2 = 0;
                let unitFrequencyB2 = 0;

                hours.forEach(function (hour) {
                    const b1 = getStationData(res, 'SM-B1', hour, unit);
                    const b2 = getStationData(res, 'SM-B2', hour, unit);

                    unitDurationB1 += b1.duration;
                    unitFrequencyB1 += b1.frequency;
                    unitDurationB2 += b2.duration;
                    unitFrequencyB2 += b2.frequency;
                });

                const unitDurationTotal = unitDurationB1 + unitDurationB2;
                const unitFrequencyTotal = unitFrequencyB1 + unitFrequencyB2;

                grandTotalDurationB1 += unitDurationB1;
                grandTotalFrequencyB1 += unitFrequencyB1;
                grandTotalDurationB2 += unitDurationB2;
                grandTotalFrequencyB2 += unitFrequencyB2;

                body += `
                    <td class="text-center">
                        ${formatDuration(
                            getDisplayDuration(unitDurationB1, unitFrequencyB1, mode)
                        )}
                    </td>
                    <td class="text-center">
                        ${formatDuration(
                            getDisplayDuration(unitDurationB2, unitFrequencyB2, mode)
                        )}
                    </td>
                    <td class="text-center fw-bold">
                        ${formatDuration(
                            getDisplayDuration(unitDurationTotal, unitFrequencyTotal, mode)
                        )}
                    </td>
                </tr>`;
            });

            const grandTotalDuration = grandTotalDurationB1 + grandTotalDurationB2;
            const grandTotalFrequency = grandTotalFrequencyB1 + grandTotalFrequencyB2;

            body += `
                <tr class="duration-total-row">
                    <td class="text-start fw-bold">Total</td>
                    <td class="text-center fw-bold">
                        ${formatDuration(
                            getDisplayDuration(grandTotalDurationB1, grandTotalFrequencyB1, mode)
                        )}
                    </td>
                    <td class="text-center fw-bold">
                        ${formatDuration(
                            getDisplayDuration(grandTotalDurationB2, grandTotalFrequencyB2, mode)
                        )}
                    </td>
                    <td class="text-center fw-bold">
                        ${formatDuration(
                            getDisplayDuration(grandTotalDuration, grandTotalFrequency, mode)
                        )}
                    </td>
                </tr>
            `;

            // =========================================================
            // RATA-RATA SHIFT PER UNIT
            // Struktur sesuai gambar:
            // Rata-rata | Hauler | B1 | B2 | Total
            //           | Grader | B1 | B2 | Total
            //           | Dozer  | B1 | B2 | Total
            //           | Total  | B1 | B2 | Total
            // =========================================================
            let averageDurationB1 = 0;
            let averageFrequencyB1 = 0;
            let averageDurationB2 = 0;
            let averageFrequencyB2 = 0;

            units.forEach(function (unit, unitIndex) {
                body += `<tr class="average-total-row">`;

                if (unitIndex === 0) {
                    body += `
                        <td rowspan="${units.length + 1}"
                            class="align-middle text-start fw-bold">
                            Rata-rata
                        </td>
                    `;
                }

                body += `
                    <td class="text-start fw-bold">
                        ${escapeHtml(unit)}
                    </td>
                `;

                let unitDurationB1 = 0;
                let unitFrequencyB1 = 0;
                let unitDurationB2 = 0;
                let unitFrequencyB2 = 0;

                hours.forEach(function (hour) {
                    const b1 = getStationData(res, 'SM-B1', hour, unit);
                    const b2 = getStationData(res, 'SM-B2', hour, unit);

                    unitDurationB1 += b1.duration;
                    unitFrequencyB1 += b1.frequency;
                    unitDurationB2 += b2.duration;
                    unitFrequencyB2 += b2.frequency;
                });

                const unitDurationTotal = unitDurationB1 + unitDurationB2;
                const unitFrequencyTotal = unitFrequencyB1 + unitFrequencyB2;

                averageDurationB1 += unitDurationB1;
                averageFrequencyB1 += unitFrequencyB1;
                averageDurationB2 += unitDurationB2;
                averageFrequencyB2 += unitFrequencyB2;

                body += `
                    <td class="text-center">
                        ${formatDuration(getAverage(unitDurationB1, unitFrequencyB1))}
                    </td>
                    <td class="text-center">
                        ${formatDuration(getAverage(unitDurationB2, unitFrequencyB2))}
                    </td>
                    <td class="text-center fw-bold">
                        ${formatDuration(getAverage(unitDurationTotal, unitFrequencyTotal))}
                    </td>
                </tr>`;
            });

            const averageDurationTotal = averageDurationB1 + averageDurationB2;
            const averageFrequencyTotal = averageFrequencyB1 + averageFrequencyB2;

            body += `
                <tr class="average-total-row">
                    <td class="text-start fw-bold">Total</td>
                    <td class="text-center fw-bold">
                        ${formatDuration(getAverage(averageDurationB1, averageFrequencyB1))}
                    </td>
                    <td class="text-center fw-bold">
                        ${formatDuration(getAverage(averageDurationB2, averageFrequencyB2))}
                    </td>
                    <td class="text-center fw-bold">
                        ${formatDuration(getAverage(averageDurationTotal, averageFrequencyTotal))}
                    </td>
                </tr>
            `;

            document.getElementById('tblDurationBody').innerHTML = body;
        }

        // =========================================================
        // TABEL KANAN
        // All, SM-B1, SM-B2
        // =========================================================
        function renderAverageDurationTable(res) {
            const hours = res.hours || [];
            const units = res.units || [];
            const stations = ['All', 'SM-B1', 'SM-B2'];
            const mode = $('#summaryMode').val() || 'total';
            const container = document.getElementById('averageDurationTables');

            let html = '';

            stations.forEach(function (station) {
                const title = station === 'All' ? 'All' : `${station}`;
                const tableId = station === 'All'
                    ? 'tblAverageAll'
                    : `tblAverage${station.replace('-', '')}`;

                html += `
                    <div class="average-duration-block">
                        <div class="average-duration-title">
                            ${escapeHtml(title)}
                        </div>
                        <div class="table-responsive">
                            <table id="${tableId}" class="table table-bordered table-sm align-middle text-center average-duration-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle">Unit</th>
                                        <th colspan="${hours.length}" class="average-title">
                                            ${mode === 'total' ? 'Durasi total (menit)' : 'Durasi rata-rata (menit)'}
                                        </th>
                                        <th rowspan="2" class="text-center align-middle">Avg</th>
                                    </tr>
                                    <tr>
                `;

                hours.forEach(function (hour) {
                    html += `<th>${escapeHtml(hour)}</th>`;
                });

                html += `
                                    </tr>
                                </thead>
                                <tbody>
                `;

                units.forEach(function (unit) {
                    html += `
                        <tr>
                            <td class="unit-name">${escapeHtml(unit)}</td>
                    `;

                    let totalDuration = 0;
                    let totalFrequency = 0;

                    hours.forEach(function (hour) {
                        const item = getStationData(res, station, hour, unit);
                        totalDuration += item.duration;
                        totalFrequency += item.frequency;

                        html += `
                            <td class="text-center">
                                ${formatDuration(
                                    getDisplayDuration(item.duration, item.frequency, mode)
                                )}
                            </td>
                        `;
                    });

                    html += `
                            <td class="text-center fw-bold">
                                ${formatDuration(
                                    getDisplayDuration(totalDuration, totalFrequency, mode)
                                )}
                            </td>
                        </tr>
                    `;
                });

                // Total baris pada masing-masing tabel kanan
                html += `
                    <tr class="duration-total-row">
                        <td class="text-start fw-bold">Total</td>
                `;

                let stationTotalDuration = 0;
                let stationTotalFrequency = 0;

                hours.forEach(function (hour) {
                    let hourDuration = 0;
                    let hourFrequency = 0;

                    units.forEach(function (unit) {
                        const item = getStationData(res, station, hour, unit);
                        hourDuration += item.duration;
                        hourFrequency += item.frequency;
                    });

                    stationTotalDuration += hourDuration;
                    stationTotalFrequency += hourFrequency;

                    html += `
                        <td class="text-center fw-bold">
                            ${formatDuration(
                                getDisplayDuration(hourDuration, hourFrequency, mode)
                            )}
                        </td>
                    `;
                });

                html += `
                        <td class="text-center fw-bold">
                            ${formatDuration(
                                getDisplayDuration(
                                    stationTotalDuration,
                                    stationTotalFrequency,
                                    mode
                                )
                            )}
                        </td>
                    </tr>
                `;

                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function loadDuration() {
            loading.style.display = 'flex';

            const params = new URLSearchParams({
                tanggalStatus: tanggalInput.value,
                shift: shiftInput.value
            });

            fetch("{{ route('distribusiFrekuensiFuelStation.durasi.api') }}?" + params.toString())
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('HTTP Error ' + response.status);
                    }

                    return response.json();
                })
                .then(function (res) {
                    latestDurationResponse = res;

                    renderDurationTable(res);
                    renderAverageDurationTable(res);
                })
                .catch(function (error) {
                    console.error('Duration Error:', error);

                    document.getElementById('tblDurationBody').innerHTML = `
                        <tr>
                            <td colspan="5" class="text-danger text-center">
                                Gagal mengambil data durasi.
                            </td>
                        </tr>
                    `;

                    document.getElementById('averageDurationTables').innerHTML = `
                        <div class="text-danger text-center py-3">
                            Gagal mengambil data durasi.
                        </div>
                    `;
                })
                .finally(function () {
                    loading.style.display = 'none';
                });
        }

        cariButton.addEventListener('click', function () {
            loadDuration();
        });

        shiftInput.addEventListener('change', function () {
            loadDuration();
        });

        // Dropdown Tampilan Total / Rata-rata langsung mengubah tabel
        // tanpa perlu request API ulang.
        $('#summaryMode').on('change', function () {
            if (!latestDurationResponse) return;

            renderDurationTable(latestDurationResponse);
            renderAverageDurationTable(latestDurationResponse);
        });

        loadDuration();
    });

    // =========================================================
    // EXPORT EXCEL
    // Export tabel kiri + 3 tabel kanan.
    // =========================================================
    $('#exportAllExcel').on('click', function () {
        const workbook = XLSX.utils.book_new();

        function autoWidthColumns(sheet) {
            if (!sheet['!ref']) return;

            const range = XLSX.utils.decode_range(sheet['!ref']);
            const columnWidths = [];

            for (let C = range.s.c; C <= range.e.c; C++) {
                let maxWidth = 0;

                for (let R = range.s.r; R <= range.e.r; R++) {
                    const cellAddress = XLSX.utils.encode_cell({
                        r: R,
                        c: C
                    });

                    const cell = sheet[cellAddress];

                    if (cell && cell.v !== undefined && cell.v !== null) {
                        maxWidth = Math.max(maxWidth, String(cell.v).length);
                    }
                }

                columnWidths.push({
                    wch: Math.min(maxWidth + 2, 50)
                });
            }

            sheet['!cols'] = columnWidths;
        }

        const tables = [
            { id: 'tblDuration', name: 'Durasi' },
            { id: 'tblAverageAll', name: 'All' },
            { id: 'tblAverageSMB1', name: 'SM-B1' },
            { id: 'tblAverageSMB2', name: 'SM-B2' }
        ];

        tables.forEach(function (item) {
            const table = document.getElementById(item.id);

            if (!table) return;

            const sheet = XLSX.utils.table_to_sheet(table, {
                raw: true
            });

            autoWidthColumns(sheet);

            XLSX.utils.book_append_sheet(
                workbook,
                sheet,
                item.name
            );
        });

        const tanggal = ($('#tanggalStatus').val() || 'tanggal')
            .replaceAll(' ', '_')
            .replaceAll('/', '-');

        XLSX.writeFile(
            workbook,
            `Durasi Refueling Fuel Station_${tanggal}.xlsx`
        );
    });
</script>
