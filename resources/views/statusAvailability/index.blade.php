@include('layout.head', ['title' => 'Status & Availability'])
@include('layout.header')
@include('layout.theme_settings')
@include('layout.sidebar')
<style>
    @media (max-width: 767.98px) {
        .dt-buttons {
            display: none !important;
        }
    }

    #tblAvailability th{
        background:#fcfbfb;
        color:#000000;
        white-space:nowrap;
    }

    #tblAvailability td{
        vertical-align:middle;
    }

    #tblAvailability tbody tr:hover{
        background:#f8f9fa;
    }

    .table-warning td{
        font-weight:bold;
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

</style>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="fw-semibold">Status & Availability</h4>

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
                            {{-- <div class="col-6 col-md-1 mb-2">
                                <label for="aggregation">Tampilan</label>
                                <select class="form-select" name="aggregation" id="aggregation">
                                    <option value="total" selected>Total</option>
                                    <option value="average">Rata-rata</option>
                                </select>
                            </div> --}}
                            <div class="col-6 col-md-1 mb-2 d-flex align-items-end">
                                <button id="cariStatus" class="btn btn-primary w-100 me-2" style="padding-top:10px;padding-bottom:10px;">Tampilkan</button>
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
                        <div id="loadingOverlay">
                            <div class="loading-box">
                                <div class="spinner-border text-primary"></div>
                                <small class="text-muted">
                                    Mohon tunggu sebentar
                                </small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="tblAvailability" class="table table-bordered table-sm align-middle text-center">
                                <thead id="tblHeader"></thead>
                                <tbody id="tblBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tblPA" class="table table-bordered table-sm align-middle text-center w-100">
                                        <thead id="tblPAHeader"></thead>
                                        <tbody id="tblPABody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
            rangeInput.placeholder = `${yyyy}-${mm}-${dd} to ${yyyy}-${mm}-${dd}`;
        }
    });

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
                <th rowspan="2" class="align-middle text-center">Jam</th>
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

                const total = ready + standby + delay + breakdown;

                let pa = null;

                if (total > 0) {
                    pa = ((ready + standby + delay) / total) * 100;
                }

                html += `
                    <td>${pa !== null ? pa.toFixed(1) + '%' : '-'}</td>
                `;
            });

            html += `
                </tr>
            `;
        });

        $('#tblPABody').html(html);
    }

    let availabilityChart = null;
    let availabilityChartData = null;

    function buildChart(res) {
        availabilityChartData = res;
        const unitSelect = $('#chartUnit');

        unitSelect.empty();

        unitSelect.append(`
            <option value="ALL">Semua Unit</option>
        `);

        res.units.forEach(function (unit) {
            unitSelect.append(`
                <option value="${unit.id}">
                    ${unit.id}
                </option>
            `);
        });

        renderAvailabilityChart(res, 'ALL');

        unitSelect.off('change').on('change', function () {
            renderAvailabilityChart(
                availabilityChartData,
                $(this).val()
            );
        });
    }

    function renderAvailabilityChart(res, selectedUnit) {
        const series = [];

        res.statuses.forEach(function (status) {
            const data = [];

            res.hours.forEach(function (hour) {
                let total = 0;

                if (
                    res.pivot[hour] &&
                    res.pivot[hour][status]
                ) {
                    if (selectedUnit === 'ALL') {
                        res.units.forEach(function (unit) {
                            if (
                                res.pivot[hour][status][unit.id] !== undefined
                            ) {
                                total += Number(
                                    res.pivot[hour][status][unit.id]
                                );
                            }
                        });
                    } else {
                        if (
                            res.pivot[hour][status][selectedUnit] !== undefined
                        ) {
                            total = Number(
                                res.pivot[hour][status][selectedUnit]
                            );
                        }
                    }
                }

                data.push(Number(total.toFixed(1)));
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
            xaxis: {
                categories: res.hours,
                title: {
                    text: 'Jam'
                }
            },
            yaxis: {
                min: 0,
                max: selectedUnit === 'ALL' ? undefined : 1,
                title: {
                    text: 'Durasi (Jam)'
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return Number(value).toFixed(1) + ' jam';
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

    let statusPieChart = null;
    let availabilityRateChart = null;

    function getOverallStatusTotals(res) {
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

            res.units.forEach(function (unit) {
                if (res.totals[status][unit.id] !== undefined) {
                    totals[status] += Number(res.totals[status][unit.id] || 0);
                }
            });
        });

        return totals;
    }

    function getHourStatusTotal(res, hour, status, selectedUnit = 'ALL') {
        let total = 0;

        if (!res.pivot[hour] || !res.pivot[hour][status]) {
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

    let statusPieData = null;

    function getOverallStatusTotals(res, selectedUnit = 'ALL') {
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
                    if (res.totals[status][unit.id] !== undefined) {
                        totals[status] += Number(
                            res.totals[status][unit.id] || 0
                        );
                    }
                });
            } else if (res.totals[status][selectedUnit] !== undefined) {
                totals[status] += Number(
                    res.totals[status][selectedUnit] || 0
                );
            }
        });

        return totals;
    }

    function buildStatusPieChart(res) {
        statusPieData = res;

        const unitSelect = $('#statusPieUnit');

        unitSelect.empty();

        unitSelect.append(`
            <option value="ALL">Semua Unit</option>
        `);

        res.units.forEach(function (unit) {
            unitSelect.append(`
                <option value="${unit.id}">
                    ${unit.id}
                </option>
            `);
        });

        renderStatusPieChart(res, 'ALL');

        unitSelect.off('change').on('change', function () {
            renderStatusPieChart(
                statusPieData,
                $(this).val()
            );
        });
    }

    function renderStatusPieChart(res, selectedUnit) {
        const totals = getOverallStatusTotals(
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
                    (totals.Ready +
                        totals.Standby +
                        totals.Delay) /
                    total
                ) * 100
                : 0;

        const useAvailability =
            (totals.Ready +
                totals.Standby +
                totals.Delay) > 0
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
                    Number(totals.Ready.toFixed(1)),
                    Number(totals.Standby.toFixed(1)),
                    Number(totals.Delay.toFixed(1)),
                    Number(totals.Breakdown.toFixed(1))
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
                        ? 'Diagram status All Fuel Truck'
                        : 'Diagram status ' + selectedUnit,
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
                            return Number(value).toFixed(1) + ' jam';
                        }
                    }
                }
            }
        );

        statusPieChart.render();
    }

    let availabilityRateData = null;

    function buildAvailabilityRateChart(res) {
        availabilityRateData = res;

        const unitSelect = $('#availabilityRateUnit');

        unitSelect.empty();

        unitSelect.append(`
            <option value="ALL">Semua Unit</option>
        `);

        res.units.forEach(function (unit) {
            unitSelect.append(`
                <option value="${unit.id}">
                    ${unit.id}
                </option>
            `);
        });

        renderAvailabilityRateChart(
            res,
            'ALL'
        );

        unitSelect.off('change').on('change', function () {
            renderAvailabilityRateChart(
                availabilityRateData,
                $(this).val()
            );
        });
    }

    function renderAvailabilityRateChart(res, selectedUnit) {
        const uaData = [];
        const paData = [];

        res.hours.forEach(function (hour) {
            const ready = getHourStatusTotal(
                res,
                hour,
                'Ready',
                selectedUnit
            );

            const standby = getHourStatusTotal(
                res,
                hour,
                'Standby',
                selectedUnit
            );

            const delay = getHourStatusTotal(
                res,
                hour,
                'Delay',
                selectedUnit
            );

            const breakdown = getHourStatusTotal(
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
                    ? (ready / available) * 100
                    : 0;

            const pa =
                total > 0
                    ? (available / total) * 100
                    : 0;

            uaData.push(
                Number(ua.toFixed(1))
            );

            paData.push(
                Number(pa.toFixed(1))
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
            const number = Number(value || 0);

            return number === 0
                ? '-'
                : number.toFixed(2);
        }

    function loadAvailability() {

        $('#loadingOverlay').css('display', 'flex');

        $.ajax({
            url: "{{ route('statusAvailability.api') }}",
            type: "GET",
            data: {
                tanggalStatus: $('#tanggalStatus').val(),
                shift: $('#shift').val(),
                // aggregation: $('#aggregation').val()
            },

            success: function (res) {

                console.log('API SUCCESS:', res);

                try {

                    buildTable(res);
                    buildPATable(res);

                    // Jalankan hanya jika element chart memang tersedia
                    if (document.querySelector('#availability-chart')) {
                        buildChart(res);
                    }

                    if (document.querySelector('#status-pie-chart')) {
                        buildStatusPieChart(res);
                    }

                    if (document.querySelector('#availability-rate-chart')) {
                        buildAvailabilityRateChart(res);
                    }

                } catch (error) {

                    console.error('ERROR SAAT RENDER DATA:', error);

                }
            },

            error: function (xhr, status, error) {
                console.error('API ERROR');
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                console.error('Error:', error);

            },
            complete: function () {
                $('#loadingOverlay').stop(true, true).fadeOut(200);
            }
        });
    }

    function buildTable(res) {
        let header = '';

        header += `
            <tr>
                <th rowspan="2" class="align-middle text-center">Hour</th>
                <th rowspan="2" class="align-middle text-center">Status</th>
                <th colspan="${res.units.length}" class="text-center">
                    Durasi
                </th>
            </tr>
            <tr>
        `;

        res.units.forEach(function (unit) {
            let statusClass = getUnitStatusClass(unit.status);

            header += `
                <th class="text-center ${statusClass}">
                    ${unit.id}
                </th>
            `;
        });

        header += `
            </tr>
        `;

        $('#tblHeader').html(header);

        buildBody(res);
    }

    function buildBody(res) {

        let html = '';

        // =====================================================
        // PER HOUR SEGMENT
        // SELALU TOTAL DALAM JAM
        // =====================================================

        res.hours.forEach(function (hour) {

            res.statuses.forEach(function (status, index) {

                html += '<tr>';

                if (index === 0) {

                    html += `
                        <td rowspan="${res.statuses.length}">
                            ${hour}
                        </td>
                    `;
                }

                html += `
                    <td class="text-start fw-bold ps-3">
                        ${status}
                    </td>
                `;

                res.units.forEach(function (unit) {

                    let value = 0;

                    if (
                        res.pivot[hour] &&
                        res.pivot[hour][status] &&
                        res.pivot[hour][status][unit.id] !== undefined
                    ) {
                        value = Number(
                            res.pivot[hour][status][unit.id]
                        );
                    }

                    html += `
                        <td>${formatNumber(value)}</td>
                    `;
                });

                html += '</tr>';
            });
        });


        // =====================================================
        // TOTAL
        // =====================================================

        html += buildSummaryRows(
            res,
            'Total',
            res.totals
        );


        // =====================================================
        // RATA-RATA HOURLY BASE
        // =====================================================

        html += buildSummaryRows(
            res,
            'Rata-rata',
            res.averages
        );


        $('#tblBody').html(html);
    }

    function buildSummaryRows(res, title, data) {

    let html = '';

    res.statuses.forEach(function (status, index) {

        html += '<tr class="table-warning">';

        // Label Total / Rata-rata
        if (index === 0) {

            html += `
                <td
                    rowspan="${res.statuses.length}"
                    class="text-start fw-bold ps-3 align-middle"
                >
                    ${title}
                </td>
            `;
        }

        // Status
        html += `
            <td class="text-start fw-bold ps-3">
                ${status}
            </td>
        `;


        // Unit
        res.units.forEach(function (unit) {

            let value = 0;

            if (
                data &&
                data[status] &&
                data[status][unit.id] !== undefined
            ) {
                value = Number(
                    data[status][unit.id]
                );
            }

            html += `
                <td>${formatNumber(value)}</td>
            `;
        });

        html += '</tr>';
    });

    return html;
}

    function buildTotal(html, res) {
        res.statuses.forEach(function (status) {
            html += `
                <tr class="table-warning">
                    <td colspan="2" class="text-start fw-bold ps-3">
                        Total ${status}
                    </td>
            `;

            res.units.forEach(function (unit) {
                let total = 0;

                if (
                    res.totals[status] &&
                    res.totals[status][unit.id] !== undefined
                ) {
                    total = res.totals[status][unit.id];
                }

                html += `
                    <td>${formatNumber(total)}</td>
                `;
            });

            html += '</tr>';
        });

        $('#tblBody').html(html);
    }

    $(document).ready(function () {
        loadAvailability();

        $('#cariStatus').click(function () {
            loadAvailability();
        });
    });
</script>
