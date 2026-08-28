@include('layout.head', ['title' => 'Grafik Status & Availability Fuel Truck'])
@include('layout.header')
@include('layout.theme_settings')
@include('layout.sidebar')

<style>
    @media (max-width: 767.98px) {
        .dt-buttons {
            display: none !important;
        }
    }

    .table-warning td {
        font-weight: bold;
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

    .availability-kpi {
        border: 1px solid #333;
        min-height: 96px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
        font-weight: 700;
    }

    #status-pie-chart,
    #availability-rate-chart {
        min-height: 320px;
    }

    #availabilityLoading {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(255, 255, 255, 0.75);
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        backdrop-filter: blur(2px);
    }

    #availabilityLoading.show {
        display: flex;
    }

    #availabilityLoading .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    #availabilityLoading .loading-text {
        margin-top: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #495057;
    }
</style>
<div id="availabilityLoading">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <div class="loading-text">Memuat data...</div>
</div>
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="fw-semibold">Grafik Status & Availability Fuel Truck</h4>

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
                                <label for="globalUnit">Vehicle</label>
                                <select class="form-select" id="globalUnit">
                                    <option value="ALL">Semua Unit</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-1 mb-2 d-flex align-items-end">
                                <button id="cariStatus" class="btn btn-primary w-100 me-2" style="padding-top:10px;padding-bottom:10px;">
                                    Tampilkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-start">

            <div class="col-md-6">

                <div class="card">
                    <div class="card-body">
                        <div id="status-pie-chart" class="apex-charts"></div>
                    </div>
                </div>

                <div class="row mt-3">

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="mb-3">Physical Availability</h6>

                                <div id="physicalAvailability" class="availability-kpi">
                                    -
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="mb-3">Use of Availability</h6>

                                <div id="useAvailability" class="availability-kpi">
                                    -
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card">
                    <div class="card-body">
                        <div dir="ltr">
                            <div id="availability-chart" class="apex-charts"></div>
                        </div>

                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
{{--
                        <div class="mb-3">
                            <h4 class="card-title mb-0 text-center">
                                Availability
                            </h4>
                        </div> --}}

                        <div id="availability-rate-chart" class="apex-charts"></div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

@include('layout.footer')

<script>
    document.getElementById('tanggalStatus').flatpickr({
        mode: "range"
    });

    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const rangeDate = urlParams.get('rangeDate');
        const rangeInput = document.getElementById('tanggalStatus');

        if (rangeDate) {
            rangeInput.value = rangeDate;
        } else {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');

            rangeInput.placeholder =
                `${yyyy}-${mm}-${dd} to ${yyyy}-${mm}-${dd}`;
        }
    });

    let globalUnit = 'ALL';
    let pendingGlobalUnit = 'ALL';

    let availabilityChart = null;
    let availabilityChartData = null;

    let statusPieChart = null;
    let statusPieData = null;

    let availabilityRateChart = null;
    let availabilityRateData = null;

    function showAvailabilityLoading() {
        $('#availabilityLoading').addClass('show');
    }

    function hideAvailabilityLoading() {
        $('#availabilityLoading').removeClass('show');
    }

    function getUnitStatusClass(status) {
        switch (String(status || '').toLowerCase()) {
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

    function buildPATable(res) {
        let header = '';

        header += `
            <tr>
                <th rowspan="2" class="align-middle text-center">
                    Jam
                </th>
                <th colspan="${res.units.length}" class="text-center">
                    Physical Availability (PA)
                </th>
            </tr>
            <tr>
        `;

        res.units.forEach(function (unit) {
            header += `
                <th class="text-center ${getUnitStatusClass(unit.status)}">
                    ${unit.id}
                </th>
            `;
        });

        header += `
            </tr>
        `;

        $('#tblPAHeader').html(header);

        let html = '';

        res.hours.forEach(function (hour) {

            html += `
                <tr>
                    <td>${hour}</td>
            `;

            res.units.forEach(function (unit) {

                let ready = 0;
                let standby = 0;
                let delay = 0;
                let breakdown = 0;

                if (res.pivot[hour]) {

                    if (
                        res.pivot[hour]['Ready'] &&
                        res.pivot[hour]['Ready'][unit.id] !== undefined
                    ) {
                        ready = Number(
                            res.pivot[hour]['Ready'][unit.id]
                        );
                    }

                    if (
                        res.pivot[hour]['Standby'] &&
                        res.pivot[hour]['Standby'][unit.id] !== undefined
                    ) {
                        standby = Number(
                            res.pivot[hour]['Standby'][unit.id]
                        );
                    }

                    if (
                        res.pivot[hour]['Delay'] &&
                        res.pivot[hour]['Delay'][unit.id] !== undefined
                    ) {
                        delay = Number(
                            res.pivot[hour]['Delay'][unit.id]
                        );
                    }

                    if (
                        res.pivot[hour]['Breakdown'] &&
                        res.pivot[hour]['Breakdown'][unit.id] !== undefined
                    ) {
                        breakdown = Number(
                            res.pivot[hour]['Breakdown'][unit.id]
                        );
                    }
                }

                const total =
                    ready +
                    standby +
                    delay +
                    breakdown;

                let pa = null;

                if (total > 0) {
                    pa =
                        (
                            (ready + standby + delay) /
                            total
                        ) * 100;
                }

                html += `
                    <td>
                        ${pa !== null
                            ? pa.toFixed(1) + '%'
                            : '-'}
                    </td>
                `;
            });

            html += `
                </tr>
            `;
        });

        $('#tblPABody').html(html);
    }

    function buildChart(res) {
        availabilityChartData = res;
        renderAvailabilityChart(
            res,
            globalUnit
        );
    }

    function renderAvailabilityChart(res, selectedUnit) {

        const series = [];

        // ============================================================
        // SUMBER DATA KHUSUS GRAFIK
        //
        // chartPivot:
        //   durasi normalized per unit, per periode = max 60 menit.
        //
        // chartAverage:
        //   rata-rata semua unit, per periode = max 60 menit.
        // ============================================================
        const chartPivot = res.chartPivot || {};
        const chartAverage = res.chartAverage || {};

        res.statuses.forEach(function (status) {

            const data = [];

            res.hours.forEach(function (hour) {

                let value = 0;

                if (selectedUnit === 'ALL') {

                    // Semua unit = RATA-RATA antar unit.
                    value = Number(
                        chartAverage?.[hour]?.[status] || 0
                    );

                } else {

                    // Satu unit = durasi unit tersebut.
                    value = Number(
                        chartPivot
                            ?. [hour]
                            ?. [status]
                            ?. [selectedUnit]
                        || 0
                    );
                }

                // Satu status dalam satu periode tidak boleh
                // berkontribusi lebih dari 60 menit.
                value = Math.min(
                    60,
                    Math.max(0, value)
                );

                data.push(
                    Number(value.toFixed(2))
                );
            });

            series.push({
                name: status,
                data: data
            });
        });

        if (availabilityChart) {
            availabilityChart.destroy();
        }

        const options = {

            series: series,

            colors: [
                '#008FFB',
                '#00E396',
                '#FEB019',
                '#FF4560'
            ],

            chart: {
                type: 'bar',
                height: 380,
                stacked: true,
                toolbar: {
                    show: false
                }
            },

            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 3
                }
            },

            title: {
                text: selectedUnit === 'ALL'
                    ? 'Durasi Status Rata-rata Semua Fuel Truck'
                    : 'Durasi Status ' + selectedUnit,
                align: 'center',
                style: {
                    fontSize: '18px',
                    fontWeight: 600
                }
            },

            xaxis: {
                categories: res.hours,
                title: {
                    text: 'Jam'
                }
            },

            yaxis: {
                min: 0,

                // PENTING:
                // Baik satu unit maupun semua unit selalu
                // menggunakan satu periode = 60 menit.
                max: 60,

                tickAmount: 6,

                title: {
                    text: 'Durasi (Menit)'
                },

                labels: {
                    formatter: function (value) {
                        return Number(value).toFixed(0);
                    }
                }
            },

            tooltip: {
                y: {
                    formatter: function (value) {
                        return Number(value).toFixed(2) + ' menit';
                    }
                }
            },

            legend: {
                position: 'top',
                horizontalAlign: 'center'
            },

            dataLabels: {
                enabled: false
            },

            stroke: {
                width: 1
            }
        };

        availabilityChart = new ApexCharts(
            document.querySelector('#availability-chart'),
            options
        );

        availabilityChart.render();
    }

    function getHourStatusTotal(
        res,
        hour,
        status,
        selectedUnit = 'ALL'
    ) {

        let total = 0;

        if (
            !res.pivot[hour] ||
            !res.pivot[hour][status]
        ) {
            return 0;
        }

        if (selectedUnit === 'ALL') {

            res.units.forEach(function (unit) {

                total += Number(
                    res.pivot[hour][status][unit.id] || 0
                );

            });

        } else {

            total = Number(
                res.pivot[hour][status][selectedUnit] || 0
            );

        }

        return total;
    }

    function getOverallStatusTotals(
        res,
        selectedUnit = 'ALL'
    ) {

        const totals = {
            Ready: 0,
            Standby: 0,
            Delay: 0,
            Breakdown: 0
        };

        res.statuses.forEach(function (status) {

            if (!res.totals[status]) {
                return;
            }

            if (selectedUnit === 'ALL') {

                res.units.forEach(function (unit) {

                    if (
                        res.totals[status][unit.id] !== undefined
                    ) {
                        totals[status] += Number(
                            res.totals[status][unit.id] || 0
                        );
                    }

                });

            } else if (
                res.totals[status][selectedUnit] !== undefined
            ) {

                totals[status] += Number(
                    res.totals[status][selectedUnit] || 0
                );

            }
        });

        return totals;
    }

    function buildStatusPieChart(res) {
        statusPieData = res;

        renderStatusPieChart(
            res,
            globalUnit
        );
    }

    function renderStatusPieChart(
        res,
        selectedUnit
    ) {

        const totals =
            getOverallStatusTotals(
                res,
                selectedUnit
            );

        const total =
            totals.Ready +
            totals.Standby +
            totals.Delay +
            totals.Breakdown;

        const physicalAvailability =
            total > 0
                ? (
                    (
                        totals.Ready +
                        totals.Standby +
                        totals.Delay
                    ) /
                    total
                ) * 100
                : 0;

        const useAvailability =
            (
                totals.Ready +
                totals.Standby +
                totals.Delay
            ) > 0
                ? (
                    totals.Ready /
                    (
                        totals.Ready +
                        totals.Standby +
                        totals.Delay
                    )
                ) * 100
                : 0;

        $('#physicalAvailability').text(
            physicalAvailability.toFixed(0) + '%'
        );

        $('#useAvailability').text(
            useAvailability.toFixed(0) + '%'
        );

        if (statusPieChart) {
            statusPieChart.destroy();
        }

        statusPieChart = new ApexCharts(
            document.querySelector('#status-pie-chart'),
            {
                series: [
                    Number(
                        totals.Ready.toFixed(1)
                    ),
                    Number(
                        totals.Standby.toFixed(1)
                    ),
                    Number(
                        totals.Delay.toFixed(1)
                    ),
                    Number(
                        totals.Breakdown.toFixed(1)
                    )
                ],

                labels: [
                    'Ready',
                    'Standby',
                    'Delay',
                    'Breakdown'
                ],

                colors: [
                    '#008FFB',
                    '#00E396',
                    '#FEB019',
                    '#FF4560'
                ],

                chart: {
                    type: 'pie',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },

                title: {
                    text: selectedUnit === 'ALL'
                        ? 'Diagram Status All Fuel Truck'
                        : 'Diagram Status ' + selectedUnit,
                    align: 'center',
                    style: {
                        fontSize: '18px',
                        fontWeight: 600
                    }
                },

                dataLabels: {
                    enabled: true,
                    formatter: function (value) {
                        return value.toFixed(0) + '%';
                    }
                },

                legend: {
                    position: 'right'
                },

                tooltip: {
                    y: {
                        formatter: function (value) {
                            return Number(value).toFixed(1) + ' menit';
                        }
                    }
                }
            }
        );

        statusPieChart.render();
    }

    function buildAvailabilityRateChart(res) {

        availabilityRateData = res;

        renderAvailabilityRateChart(
            res,
            globalUnit
        );
    }

    function renderAvailabilityRateChart(
        res,
        selectedUnit
    ) {

        const uaData = [];
        const paData = [];

        res.hours.forEach(function (hour) {

            const ready =
                getHourStatusTotal(
                    res,
                    hour,
                    'Ready',
                    selectedUnit
                );

            const standby =
                getHourStatusTotal(
                    res,
                    hour,
                    'Standby',
                    selectedUnit
                );

            const delay =
                getHourStatusTotal(
                    res,
                    hour,
                    'Delay',
                    selectedUnit
                );

            const breakdown =
                getHourStatusTotal(
                    res,
                    hour,
                    'Breakdown',
                    selectedUnit
                );

            const total =
                ready +
                standby +
                delay +
                breakdown;

            const available =
                ready +
                standby +
                delay;

            const ua =
                available > 0
                    ? (
                        ready /
                        available
                    ) * 100
                    : 0;

            const pa =
                total > 0
                    ? (
                        available /
                        total
                    ) * 100
                    : 0;

            uaData.push(
                Number(
                    ua.toFixed(1)
                )
            );

            paData.push(
                Number(
                    pa.toFixed(1)
                )
            );
        });

        if (availabilityRateChart) {
            availabilityRateChart.destroy();
        }

        availabilityRateChart = new ApexCharts(
            document.querySelector('#availability-rate-chart'),
            {
                series: [
                    {
                        name: 'UA',
                        data: uaData
                    },
                    {
                        name: 'PA',
                        data: paData
                    }
                ],

                colors: [
                    '#ed7d31',
                    '#5b9bd5'
                ],

                chart: {
                    type: 'bar',
                    height: 520,
                    toolbar: {
                        show: false
                    }
                },

                title: {
                    text: selectedUnit === 'ALL'
                        ? 'Availability All Fuel Truck'
                        : 'Availability ' + selectedUnit,
                    align: 'center',
                    style: {
                        fontSize: '18px',
                        fontWeight: 600
                    }
                },

                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '55%'
                    }
                },

                dataLabels: {
                    enabled: true,
                    formatter: function (value) {
                        return value.toFixed(0) + '%';
                    },
                    offsetX: 5,
                    style: {
                        fontSize: '11px'
                    }
                },

                xaxis: {
                    categories: res.hours,
                    min: 0,
                    max: 100,
                    tickAmount: 10,
                    title: {
                        text: 'Availability (%)'
                    },
                    labels: {
                        formatter: function (value) {
                            return value.toFixed(0) + '%';
                        }
                    }
                },

                yaxis: {
                    title: {
                        text: 'Jam'
                    }
                },

                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center'
                },

                tooltip: {
                    y: {
                        formatter: function (value) {
                            return Number(value).toFixed(1) + '%';
                        }
                    }
                }
            }
        );

        availabilityRateChart.render();
    }

    function formatNumber(value) {

        const number = Number(
            value || 0
        );

        return number === 0
            ? '-'
            : number.toFixed(1);
    }

    function loadAvailability() {
        showAvailabilityLoading();
        $('#loadingOverlay').css(
            'display',
            'flex'
        );

        $.ajax({
            url: "{{ route('statusAvailability.api') }}",
            type: "GET",

            data: {
                tanggalStatus:
                    $('#tanggalStatus').val(),

                shift:
                    $('#shift').val()
            },

            success: function (res) {

                buildPATable(res);

                const unitSelect =
                    $('#globalUnit');

                const currentUnit =
                    pendingGlobalUnit;

                unitSelect.empty();

                unitSelect.append(`
                    <option value="ALL">
                        Semua Unit
                    </option>
                `);

                res.units.forEach(function (unit) {

                    unitSelect.append(`
                        <option value="${unit.id}">
                            ${unit.id}
                        </option>
                    `);

                });

                if (
                    currentUnit !== 'ALL' &&
                    res.units.some(
                        unit =>
                            unit.id === currentUnit
                    )
                ) {
                    globalUnit =
                        currentUnit;
                } else {
                    globalUnit = 'ALL';
                }

                unitSelect.val(
                    globalUnit
                );

                buildChart(res);

                buildStatusPieChart(res);

                buildAvailabilityRateChart(res);
            },

            complete: function () {

                hideAvailabilityLoading();

            }
        });
    }

    $(document).ready(function () {

        loadAvailability();

        $('#cariStatus').click(
            function () {
                loadAvailability();
            }
        );

        $('#globalUnit').change(function () {

            pendingGlobalUnit = $(this).val();
            globalUnit = pendingGlobalUnit;

            if (availabilityChartData) {
                renderAvailabilityChart(
                    availabilityChartData,
                    globalUnit
                );
            }

            if (statusPieData) {
                renderStatusPieChart(
                    statusPieData,
                    globalUnit
                );
            }

            if (availabilityRateData) {
                renderAvailabilityRateChart(
                    availabilityRateData,
                    globalUnit
                );
            }
        });
    });
</script>
