@include('layout.head', ['title' => 'Durasi Refueling Fuel Truck'])
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
</style>


<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="fw-semibold">Durasi Refueling Fuel Truck</h4>

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
                        <div class="table-responsive">
                            <table id="tblAverageDuration" class="table table-bordered table-sm align-middle text-center w-100" >
                                <thead id="tblAverageDurationHeader"></thead>
                                <tbody id="tblAverageDurationBody"></tbody>
                            </table>
                        </div>
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

        if (typeof flatpickr !== 'undefined') {
            flatpickr(tanggalInput,
                {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    allowInput: true
                }
            );

        }

        const today = new Date();
        const yyyy = today.getFullYear();
        const mm =
            String(
                today.getMonth() + 1
            ).padStart(2, '0');

        const dd =
            String(
                today.getDate()
            ).padStart(2, '0');

        tanggalInput.placeholder = `${yyyy}-${mm}-${dd} to ${yyyy}-${mm}-${dd}`;

        function formatDuration(value) {
            value = parseFloat(value || 0);
            if (
                isNaN(value) ||
                value === 0
            ) {
                return '-';
            }
            return value.toFixed(2);
        }

        function getStatusClass(status) {
            status =
                String(status || '')
                    .trim()
                    .toLowerCase();

            switch (status) {
                case 'ready':
                    return 'unit-ready';

                case 'standby':
                    return 'unit-standby';

                case 'delay':
                    return 'unit-delay';

                case 'breakdown':
                    return 'unit-breakdown';

                default:
                    return 'unit-unknown';
            }

        }
        let latestDurationResponse = null;

        function getSummaryMode() {
            const el = document.getElementById('summaryMode');
            return el ? el.value : 'total';
        }

        function formatDuration(value) {
            const number = parseFloat(value || 0);

            if (!Number.isFinite(number) || number === 0) {
                return '-';
            }

            return number.toFixed(2);
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderDurationTable(res) {

            const mode = getSummaryMode();
            const isAverage = mode === 'average';

            const hours = res.hours || [];
            const units = res.units || [];
            const fuelTrucks = res.fuelTrucks || [];

            const durationPivot =
                res.durationPivot || {};

            const frequencyPivot =
                res.frequencyPivot || {};

            const fuelTruckStatus =
                res.fuelTruckStatus || {};

            let header = `
                <tr>
                    <th rowspan="2" class="text-center align-middle">
                        Jam
                    </th>

                    <th rowspan="2" class="text-center align-middle">
                        Unit
                    </th>

                    <th
                        colspan="${fuelTrucks.length}"
                        class="duration-header-title"
                    >
                        Durasi (menit)
                    </th>

                    <th rowspan="2" class="text-center align-middle">
                        ${isAverage ? 'Rata-rata' : 'Total'}
                    </th>
                </tr>

                <tr>
            `;

            fuelTrucks.forEach(function (fuelTruck) {

                const status =
                    fuelTruckStatus[fuelTruck] ||
                    'Unknown';

                const statusClass =
                    getStatusClass(status);

                header += `
                    <th
                        class="${statusClass}"
                        title="Status: ${escapeHtml(status)}"
                    >
                        ${escapeHtml(fuelTruck)}
                    </th>
                `;
            });

            header += `
                </tr>
            `;

            document.getElementById(
                'tblDurationHeader'
            ).innerHTML = header;


            let body = '';

            // ========================================================
            // DATA PER JAM / UNIT
            // ========================================================
            hours.forEach(function (hour) {

                units.forEach(function (unit, unitIndex) {

                    body += '<tr>';

                    if (unitIndex === 0) {

                        body += `
                            <td
                                rowspan="${units.length}"
                                class="hour-cell"
                            >
                                ${escapeHtml(hour)}
                            </td>
                        `;
                    }

                    body += `
                        <td class="unit-name">
                            ${escapeHtml(unit)}
                        </td>
                    `;

                    let rowTotal = 0;
                    let rowFrequency = 0;

                    fuelTrucks.forEach(function (fuelTruck) {

                        const duration = Number(
                            durationPivot
                                ?. [hour]
                                ?. [unit]
                                ?. [fuelTruck]
                            || 0
                        );

                        const frequency = Number(
                            frequencyPivot
                                ?. [hour]
                                ?. [unit]
                                ?. [fuelTruck]
                            || 0
                        );

                        rowTotal += duration;
                        rowFrequency += frequency;

                        body += `
                            <td class="text-center">
                                ${formatDuration(duration)}
                            </td>
                        `;
                    });

                    const summaryValue = isAverage
                        ? (
                            rowFrequency > 0
                                ? rowTotal / rowFrequency
                                : 0
                        )
                        : rowTotal;

                    body += `
                        <td class="text-center fw-bold">
                            ${formatDuration(summaryValue)}
                        </td>
                    `;

                    body += '</tr>';
                });


                // ====================================================
                // SUMMARY PER JAM
                // Hanya Total ATAU Rata-rata
                // ====================================================
                body += `
                    <tr class="duration-total-row">
                        <td colspan="2" class="text-start">
                            ${isAverage ? 'Rata-rata' : 'Total'}
                        </td>
                `;

                let hourTotal = 0;
                let hourFrequency = 0;

                fuelTrucks.forEach(function (fuelTruck) {

                    let total = 0;
                    let frequency = 0;

                    units.forEach(function (unit) {

                        total += Number(
                            durationPivot
                                ?. [hour]
                                ?. [unit]
                                ?. [fuelTruck]
                            || 0
                        );

                        frequency += Number(
                            frequencyPivot
                                ?. [hour]
                                ?. [unit]
                                ?. [fuelTruck]
                            || 0
                        );
                    });

                    hourTotal += total;
                    hourFrequency += frequency;

                    const value = isAverage
                        ? (
                            frequency > 0
                                ? total / frequency
                                : 0
                        )
                        : total;

                    body += `
                        <td class="text-center">
                            ${formatDuration(value)}
                        </td>
                    `;
                });

                const hourSummary = isAverage
                    ? (
                        hourFrequency > 0
                            ? hourTotal / hourFrequency
                            : 0
                    )
                    : hourTotal;

                body += `
                    <td class="text-center fw-bold">
                        ${formatDuration(hourSummary)}
                    </td>
                </tr>
                `;
            });


            // ========================================================
            // GRAND SUMMARY
            // Hanya satu: Grand Total ATAU Rata-rata
            // ========================================================
            let grandTotal = 0;
            let grandFrequency = 0;

            fuelTrucks.forEach(function (fuelTruck) {

                units.forEach(function (unit) {

                    hours.forEach(function (hour) {

                        grandTotal += Number(
                            durationPivot
                                ?. [hour]
                                ?. [unit]
                                ?. [fuelTruck]
                            || 0
                        );

                        grandFrequency += Number(
                            frequencyPivot
                                ?. [hour]
                                ?. [unit]
                                ?. [fuelTruck]
                            || 0
                        );
                    });
                });
            });

            body += `
                    <tr class="duration-total-row">

                        <td
                            colspan="2"
                            class="text-start"
                        >
                            Grand Total
                        </td>
                `;

                fuelTrucks.forEach(function (fuelTruck) {

                    let total = 0;

                    units.forEach(function (unit) {

                        hours.forEach(function (hour) {

                            total += Number(
                                durationPivot
                                    ?. [hour]
                                    ?. [unit]
                                    ?. [fuelTruck]
                                || 0
                            );
                        });
                    });

                    body += `
                        <td class="text-center">
                            ${formatDuration(total)}
                        </td>
                    `;
                });

                body += `
                        <td class="text-center fw-bold">
                            ${formatDuration(grandTotal)}
                        </td>
                    </tr>
                `;

                body += `
                    <tr class="duration-total-row">

                        <td
                            colspan="2"
                            class="text-start"
                        >
                            Rata-rata
                        </td>
                `;

                fuelTrucks.forEach(function (fuelTruck) {

                    let total = 0;
                    let frequency = 0;

                    units.forEach(function (unit) {

                        hours.forEach(function (hour) {

                            total += Number(
                                durationPivot
                                    ?. [hour]
                                    ?. [unit]
                                    ?. [fuelTruck]
                                || 0
                            );

                            frequency += Number(
                                frequencyPivot
                                    ?. [hour]
                                    ?. [unit]
                                    ?. [fuelTruck]
                                || 0
                            );
                        });
                    });

                    const average =
                        frequency > 0
                            ? total / frequency
                            : 0;

                    body += `
                        <td class="text-center">
                            ${formatDuration(average)}
                        </td>
                    `;
                });

                const grandAverage =
                    grandFrequency > 0
                        ? grandTotal / grandFrequency
                        : 0;

                body += `
                        <td class="text-center fw-bold">
                            ${formatDuration(grandAverage)}
                        </td>
                    </tr>
                `;

            document.getElementById(
                'tblDurationBody'
            ).innerHTML = body;
        }


        // ============================================================
        // TABEL KEDUA
        // Tetap menampilkan ringkasan sesuai pilihan summaryMode.
        // ============================================================
        function renderAverageDurationTable(res) {

            const mode = getSummaryMode();
            const isAverage = mode === 'average';

            const hours = res.hours || [];
            const units = res.units || [];
            const fuelTrucks = res.fuelTrucks || [];

            const durationPivot =
                res.durationPivot || {};

            const frequencyPivot =
                res.frequencyPivot || {};

            let header = `
                <tr>
                    <th rowspan="2">
                        Unit
                    </th>

                    <th
                        colspan="${hours.length}"
                        class="average-title"
                    >
                        ${isAverage
                            ? 'Durasi rata-rata refuelling semua fuel truck (menit)'
                            : 'Total durasi refuelling semua fuel truck (menit)'
                        }
                    </th>

                    <th rowspan="2">
                        ${isAverage ? 'Rata-rata' : 'Total'}
                    </th>
                </tr>

                <tr>
            `;

            hours.forEach(function (hour) {

                header += `
                    <th>
                        ${escapeHtml(hour)}
                    </th>
                `;
            });

            header += `
                </tr>
            `;

            document.getElementById(
                'tblAverageDurationHeader'
            ).innerHTML = header;


            let body = '';

            units.forEach(function (unit) {

                body += '<tr>';

                body += `
                    <td class="unit-name">
                        ${escapeHtml(unit)}
                    </td>
                `;

                let unitTotal = 0;
                let unitFrequency = 0;

                hours.forEach(function (hour) {

                    let total = 0;
                    let frequency = 0;

                    fuelTrucks.forEach(function (fuelTruck) {

                        total += Number(
                            durationPivot
                                ?. [hour]
                                ?. [unit]
                                ?. [fuelTruck]
                            || 0
                        );

                        frequency += Number(
                            frequencyPivot
                                ?. [hour]
                                ?. [unit]
                                ?. [fuelTruck]
                            || 0
                        );
                    });

                    unitTotal += total;
                    unitFrequency += frequency;

                    const value = isAverage
                        ? (
                            frequency > 0
                                ? total / frequency
                                : 0
                        )
                        : total;

                    body += `
                        <td class="text-center">
                            ${formatDuration(value)}
                        </td>
                    `;
                });

                const unitSummary = isAverage
                    ? (
                        unitFrequency > 0
                            ? unitTotal / unitFrequency
                            : 0
                    )
                    : unitTotal;

                body += `
                    <td class="text-center fw-bold">
                        ${formatDuration(unitSummary)}
                    </td>
                `;

                body += '</tr>';
            });


            // ========================================================
            // SUMMARY AKHIR
            // ========================================================
            body += `
                <tr class="average-total-row">

                    <td class="fw-bold">
                        ${isAverage ? 'Rata-rata' : 'Grand Total'}
                    </td>
            `;

            let grandTotal = 0;
            let grandFrequency = 0;

            hours.forEach(function (hour) {

                let hourTotal = 0;
                let hourFrequency = 0;

                units.forEach(function (unit) {

                    fuelTrucks.forEach(function (fuelTruck) {

                        hourTotal += Number(
                            durationPivot
                                ?. [hour]
                                ?. [unit]
                                ?. [fuelTruck]
                            || 0
                        );

                        hourFrequency += Number(
                            frequencyPivot
                                ?. [hour]
                                ?. [unit]
                                ?. [fuelTruck]
                            || 0
                        );
                    });
                });

                grandTotal += hourTotal;
                grandFrequency += hourFrequency;

                const value = isAverage
                    ? (
                        hourFrequency > 0
                            ? hourTotal / hourFrequency
                            : 0
                    )
                    : hourTotal;

                body += `
                    <td class="text-center">
                        ${formatDuration(value)}
                    </td>
                `;
            });

            const grandValue = isAverage
                ? (
                    grandFrequency > 0
                        ? grandTotal / grandFrequency
                        : 0
                )
                : grandTotal;

            body += `
                    <td class="text-center fw-bold">
                        ${formatDuration(grandValue)}
                    </td>
                </tr>
            `;

            document.getElementById(
                'tblAverageDurationBody'
            ).innerHTML = body;
        }


        function loadDuration() {
            const tanggalStatus = tanggalInput.value.trim();
            const shift = shiftInput.value;

            if (!tanggalStatus) {
                alert(
                    'Silakan pilih tanggal terlebih dahulu.'
                );
                return;

            }

            loading.style.display = 'flex';
            const params =
                new URLSearchParams({
                    tanggalStatus: tanggalStatus,
                    shift: shift,
                    summaryMode: getSummaryMode()
                });

            const apiUrl = "{{ route('distribusiFrekuensiFuelStation.durasi.api') }}" + '?'
                + params.toString();
            fetch(
                apiUrl,
                {
                    method: 'GET',
                    headers: {
                        'Accept':
                        'application/json'
                    }
                }
            )

            .then(
                function (response) {
                    if (!response.ok) {
                        throw new Error(
                            'HTTP Error ' +
                            response.status
                        );
                    }
                    return response.json();
                }
            )

            .then(
                function (res) {

                    latestDurationResponse = res;

                    renderDurationTable(res);
                    renderAverageDurationTable(res);
                }
            )

            .catch(
                function (error) {
                    console.error(
                        'Duration Error:',
                        error
                    );

                    document.getElementById(
                        'tblDurationBody'
                    ).innerHTML = `
                        <tr>
                            <td colspan="20" class="text-danger text-center">
                                Gagal mengambil data durasi.
                            </td>
                        </tr>
                    `;

                    document.getElementById(
                        'tblAverageDurationBody'
                    ).innerHTML = `
                        <tr>
                            <td colspan="20" class="text-danger text-center" >
                                Gagal mengambil data rata-rata durasi.
                            </td>
                        </tr>
                    `;
                }
            )

            .finally(
                function () {
                    loading.style.display = 'none';
                }
            );

        }

        cariButton.addEventListener(
            'click',
            function () {
                loadDuration();
            }
        );

        shiftInput.addEventListener(
            'change',
            function () {
            }
        );

        const summaryModeInput =
            document.getElementById('summaryMode');

        if (summaryModeInput) {

            summaryModeInput.addEventListener(
                'change',
                function () {

                    if (!latestDurationResponse) {
                        return;
                    }

                    // Tidak perlu request ulang.
                    // Cukup bangun ulang tabel dengan mode baru.
                    renderDurationTable(
                        latestDurationResponse
                    );

                    renderAverageDurationTable(
                        latestDurationResponse
                    );
                }
            );
        }

        const defaultDate = `${yyyy}-${mm}-${dd}`;
        tanggalInput.value = defaultDate;
        loadDuration();
    });

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

                    if (
                        cell &&
                        cell.v !== undefined &&
                        cell.v !== null
                    ) {
                        const value = String(cell.v);

                        maxWidth = Math.max(
                            maxWidth,
                            value.length
                        );
                    }
                }

                columnWidths.push({
                    wch: Math.min(maxWidth + 2, 50)
                });
            }

            sheet['!cols'] = columnWidths;
        }

        const detailTable = document.getElementById('tblDuration');

        if (detailTable) {
            const detailSheet = XLSX.utils.table_to_sheet(
                detailTable,
                {
                    raw: true
                }
            );

            autoWidthColumns(detailSheet);

            XLSX.utils.book_append_sheet(
                workbook,
                detailSheet,
                'Durasi'
            );
        }

        const frequencyTable = document.getElementById('tblAverageDuration');

        if (frequencyTable) {
            const frequencySheet = XLSX.utils.table_to_sheet(
                frequencyTable,
                {
                    raw: true
                }
            );

            autoWidthColumns(frequencySheet);

            XLSX.utils.book_append_sheet(
                workbook,
                frequencySheet,
                'Rata-rata'
            );
        }

        const tanggal =
            $('#tanggalStatus').val()
                .replaceAll(' ', '_')
                .replaceAll('/', '-');

        XLSX.writeFile(
            workbook,
            `Durasi Refueling Fuel Truck_${tanggal}.xlsx`
        );
    });

</script>
