@include('layout.head', ['title' => 'Distribusi Frekuensi Fuel Truck'])
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

    .chart-card {
        border: 1px solid #dee2e6;
        background: #fff;
        padding: 10px;
        min-height: 350px;
    }

    .summary-card { border: 1px solid #dee2e6; border-radius: 6px; background: #fff; height: 100%; }
    .summary-label { font-size: 12px; color: #6c757d; margin-bottom: 4px; }
    .summary-value { font-size: 22px; font-weight: 700; line-height: 1.2; }
    .summary-unit { font-size: 11px; color: #6c757d; }
    .table-grand-total td { font-weight: 700; background: #e9ecef !important; }
    .table-average td { font-weight: 600; background: #f8f9fa !important; }
    .chart-fixed-height {
        height: 600px !important;
        min-height: 600px !important;
        width: 100%;
    }

    .chart-card-fixed {
        height: 634px !important;
        min-height: 634px !important;
        overflow: hidden;
    }

    .chart-card-fixed .card-body {
        height: 100% !important;
        min-height: 0 !important;
    }

    .chart-card-fixed .chart-fixed-height {
        height: 600px !important;
        min-height: 600px !important;
    }

    .chart-card-fixed .apexcharts-canvas,
    .chart-card-fixed .apexcharts-svg {
        height: 600px !important;
        min-height: 600px !important;
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
                    <h4 class="fw-semibold">Distribusi Frekuensi Fuel Truck</h4>

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

        <div class="row g-2 mb-3" id="frequencySummary" style="display:none;">
            <div class="col-6 col-md-3"><div class="summary-card p-3"><div class="summary-label">Grand Total Shift</div><div class="summary-value" id="summaryGrandTotal">-</div><div class="summary-unit">refuelling</div></div></div>
            <div class="col-6 col-md-3"><div class="summary-card p-3"><div class="summary-label">Rata-rata / Jam</div><div class="summary-value" id="summaryAveragePerHour">-</div><div class="summary-unit">refuelling / jam</div></div></div>
            <div class="col-6 col-md-3"><div class="summary-card p-3"><div class="summary-label">Rata-rata / Fuel Truck</div><div class="summary-value" id="summaryAveragePerFuelTruck">-</div><div class="summary-unit">refuelling / truck</div></div></div>
            <div class="col-6 col-md-3"><div class="summary-card p-3"><div class="summary-label">Rata-rata / Unit</div><div class="summary-value" id="summaryAveragePerUnit">-</div><div class="summary-unit">refuelling / unit</div></div></div>
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
                                    <table id="tblPA"
                                        class="table table-bordered table-sm align-middle text-center w-100">
                                        <thead id="tblPAHeader"></thead>
                                        <tbody id="tblPABody"></tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="card chart-card-fixed">
                            <div class="card-body p-2">
                                <div id="chartAllFuelTruck" class="chart-fixed-height"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="card chart-card-fixed">
                            <div class="card-body p-2">
                                <div id="chartTotalFuelTruck" class="chart-fixed-height"></div>
                            </div>
                        </div>
                    </div>
                    <div id="fuelTruckCharts" class="col-12"></div>

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

    function displayValue(value, decimals = 0) {
        const number = Number(value || 0);
        if (number === 0) return '-';
        return decimals > 0 ? number.toFixed(decimals) : Math.round(number).toLocaleString('id-ID');
    }

    let latestFrequencyResponse = null;

    function updateSummaryVisibility() {
        const mode = $('#summaryMode').val();

        if (!latestFrequencyResponse) {
            return;
        }

        // Summary card tetap mengikuti pilihan.
        $('#frequencySummary').show();

        if (mode === 'total') {
            $('#summaryGrandTotal').text(
                displayValue(latestFrequencyResponse.grandTotal)
            );
            // $('#summaryAveragePerHour').text('-');
            // $('#summaryAveragePerFuelTruck').text('-');
            // $('#summaryAveragePerUnit').text('-');
            $('#summaryAveragePerHour').text(
                displayValue(latestFrequencyResponse.averagePerHour, 2)
            );
            $('#summaryAveragePerFuelTruck').text(
                displayValue(latestFrequencyResponse.averagePerFuelTruck, 2)
            );
            $('#summaryAveragePerUnit').text(
                displayValue(latestFrequencyResponse.averagePerUnit, 2)
            );
        } else {
            // $('#summaryGrandTotal').text('-');
            $('#summaryGrandTotal').text(
                displayValue(latestFrequencyResponse.grandTotal)
            );
            $('#summaryAveragePerHour').text(
                displayValue(latestFrequencyResponse.averagePerHour, 2)
            );
            $('#summaryAveragePerFuelTruck').text(
                displayValue(latestFrequencyResponse.averagePerFuelTruck, 2)
            );
            $('#summaryAveragePerUnit').text(
                displayValue(latestFrequencyResponse.averagePerUnit, 2)
            );
        }

        // Bangun ulang tabel agar kolom terakhir hanya menampilkan
        // Total ATAU Rata-rata sesuai pilihan.
        buildTable(latestFrequencyResponse);
        buildPATable(latestFrequencyResponse);
    }

    function updateFrequencySummary(res) {
        latestFrequencyResponse = res;

        // Default pilihan: tidak menampilkan summary.
        updateSummaryVisibility();
    }

    $(document).on('change', '#summaryMode', function () {
        updateSummaryVisibility();
    });

    function buildPATable(res) {
        const mode = $('#summaryMode').val() || 'total';
        const isAverage = mode === 'average';
        const lastColumnTitle = isAverage ? 'Rata-rata' : 'Total';

        let header = `
            <tr>
                <th rowspan="2" class="align-middle text-center">Unit</th>
                <th colspan="${res.hours.length}" class="text-center">Frekuensi</th>
                <th rowspan="2" class="align-middle text-center">${lastColumnTitle}</th>
            </tr>
            <tr>
        `;

        res.hours.forEach(function (hour) {
            header += `<th class="text-center">${hour}</th>`;
        });

        header += `</tr>`;
        $('#tblPAHeader').html(header);

        let html = '';

        res.units.forEach(function (unit) {
            html += `<tr><td class="text-start">${unit}</td>`;

            let rowTotal = 0;

            res.hours.forEach(function (hour) {
                let value = 0;

                res.fuelTrucks.forEach(function (fuelTruck) {
                    value += Number(
                        res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0
                    );
                });

                rowTotal += value;

                html += `
                    <td class="text-center">
                        ${displayValue(value)}
                    </td>
                `;
            });

            const rowAverage = res.hours.length > 0
                ? rowTotal / res.hours.length
                : 0;

            const lastValue = isAverage ? rowAverage : rowTotal;

            html += `
                <td class="text-center fw-bold">
                    ${displayValue(lastValue, isAverage ? 2 : 0)}
                </td>
            </tr>
            `;
        });

        let grandTotal = 0;

        res.hours.forEach(function (hour) {
            res.units.forEach(function (unit) {
                res.fuelTrucks.forEach(function (fuelTruck) {
                    grandTotal += Number(
                        res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0
                    );
                });
            });
        });

        const grandAverage = res.hours.length > 0
            ? grandTotal / res.hours.length
            : 0;

        html += `
            <tr class="${isAverage ? 'table-average' : 'table-grand-total'}">
                <td class="text-start">
                    ${isAverage ? 'Rata-rata Shift' : 'Grand Total Shift'}
                </td>
        `;

        res.hours.forEach(function (hour) {
            let hourTotal = 0;

            res.units.forEach(function (unit) {
                res.fuelTrucks.forEach(function (fuelTruck) {
                    hourTotal += Number(
                        res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0
                    );
                });
            });

            const hourAverage = (
                res.units.length * res.fuelTrucks.length
            ) > 0
                ? hourTotal / (res.units.length * res.fuelTrucks.length)
                : 0;

            const value = isAverage ? hourAverage : hourTotal;

            html += `
                <td class="text-center">
                    ${displayValue(value, isAverage ? 2 : 0)}
                </td>
            `;
        });

        html += `
                <td class="text-center">
                    ${displayValue(
                        isAverage ? grandAverage : grandTotal,
                        isAverage ? 2 : 0
                    )}
                </td>
            </tr>
        `;

        $('#tblPABody').html(html);
    }

    const FIXED_CHART_HEIGHT = 700;

    function normalizeHorizontalBars(chartId, barHeight = 60) {
        const root = document.querySelector('#' + chartId);
        if (!root) return;

        const bars = root.querySelectorAll('.apexcharts-bar-area');
        bars.forEach(function (bar) {
            const tag = bar.tagName.toLowerCase();

            if (tag === 'path') {
                const d = bar.getAttribute('d');
                if (!d) return;

                // ApexCharts horizontal bars are rendered as paths. Use the
                // bounding box to calculate the center and rewrite the path
                // only when it is a simple horizontal rectangle.
                try {
                    const box = bar.getBBox();
                    const y = box.y + (box.height / 2);
                    const x = box.x;
                    const w = box.width;
                    const h = barHeight;

                    bar.setAttribute(
                        'd',
                        `M ${x} ${y - h / 2} L ${x + w} ${y - h / 2} ` +
                        `L ${x + w} ${y + h / 2} L ${x} ${y + h / 2} Z`
                    );
                } catch (e) {
                    // Ignore SVG elements that cannot be measured yet.
                }
            }
        });
    }

    function chartDataLabelFormatter(value) {
        const number = Number(value || 0);
        return number === 0 ? '' : Math.round(number).toLocaleString('id-ID');
    }

    function buildFuelTruckCharts(res) {
        const container = $('#fuelTruckCharts');
        container.empty();
        res.fuelTrucks.forEach(function (fuelTruck) {
            const seriesData = res.hours.map(function (hour) {
                let total = 0;
                res.units.forEach(unit => total += Number(res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0));
                return total;
            });
            const chartId = 'chartFuelTruck_' + fuelTruck.replace(/[^a-zA-Z0-9]/g, '');
            container.append(`
                <div class="col-12 mb-3">
                    <div class="card chart-card-fixed">
                        <div class="card-body p-2">
                            <div id="${chartId}" class="chart-fixed-height"></div>
                        </div>
                    </div>
                </div>
            `);
            const chart = new ApexCharts(document.querySelector('#' + chartId), {
                chart: {
                        type: 'bar',
                        height: FIXED_CHART_HEIGHT,
                        parentHeightOffset: 0,
                        redrawOnParentResize: true,
                        toolbar: { show: false }
                    },
                series: [{ name: fuelTruck, data: seriesData }],
                plotOptions: { bar: { horizontal: true, barHeight: '80%' } },
                dataLabels: { enabled: true, formatter: chartDataLabelFormatter, offsetX: 8, style: { fontSize: '11px', fontWeight: 600 } },
                xaxis: { categories: res.hours, title: { text: 'Frekuensi refuelling' } },
                yaxis: { title: { text: 'Jam' } },
                title: { text: 'Distribusi frekuensi refuelling fuel truck ' + fuelTruck, align: 'center', style: { fontSize: '16px', fontWeight: 600 } },
                legend: { position: 'bottom' },
                tooltip: { y: { formatter: value => Number(value || 0).toLocaleString('id-ID') + ' refuelling' } }
            });
            chart.render().then(function () {
                normalizeHorizontalBars(chartId, 60);
            });
        });
    }

    function buildAllFuelTruckChart(res) {
        const series = res.fuelTrucks.map(function (fuelTruck) {
            return { name: fuelTruck, data: res.hours.map(function (hour) {
                let total = 0;
                res.units.forEach(unit => total += Number(res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0));
                return total;
            }) };
        });
        $('#chartAllFuelTruck').empty();
        new ApexCharts(document.querySelector('#chartAllFuelTruck'), {
            chart: {
                type: 'bar',
                height: FIXED_CHART_HEIGHT,
                parentHeightOffset: 0,
                redrawOnParentResize: true,
                toolbar: { show: false }
            },
            series: series,
            plotOptions: { bar: { horizontal: true, barHeight: '100%' } },
            dataLabels: { enabled: true, formatter: chartDataLabelFormatter, offsetX: 8, style: { fontSize: '16px', fontWeight: 600, colors: ['#000000'] } },
            xaxis: { categories: res.hours, title: { text: 'Frekuensi refuelling' } },
            yaxis: { title: { text: 'Jam' } },
            title: { text: 'Distribusi frekuensi refuelling seluruh fuel truck', align: 'center', style: { fontSize: '20px', fontWeight: 600 } },
            legend: { position: 'bottom', horizontalAlign: 'center' },
            tooltip: { y: { formatter: value => Number(value || 0).toLocaleString('id-ID') + ' refuelling' } }
        }).render();
    }

    function buildTotalFuelTruckChart(res) {
        const data = res.hours.map(function (hour) {
            let total = 0;
            res.fuelTrucks.forEach(fuelTruck => res.units.forEach(unit => total += Number(res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0)));
            return total;
        });
        $('#chartTotalFuelTruck').empty();
        new ApexCharts(document.querySelector('#chartTotalFuelTruck'), {
            chart: {
                type: 'bar',
                height: FIXED_CHART_HEIGHT,
                parentHeightOffset: 0,
                redrawOnParentResize: true,
                toolbar: { show: false }
            },
            series: [{ name: 'Total', data: data }],
            plotOptions: { bar: { horizontal: true, barHeight: '80%' } },
            dataLabels: { enabled: true, formatter: chartDataLabelFormatter, offsetX: 8, style: { fontSize: '11px', fontWeight: 600 } },
            xaxis: { categories: res.hours, title: { text: 'Frekuensi refuelling' } },
            yaxis: { title: { text: 'Jam' } },
            title: { text: 'Total frekuensi refuelling per jam', align: 'center', style: { fontSize: '16px', fontWeight: 600 } },
            legend: { position: 'bottom' },
            tooltip: { y: { formatter: value => Number(value || 0).toLocaleString('id-ID') + ' refuelling' } }
        }).render();
    }

    function loadFrequency()
    {
        $('#loadingOverlay').css('display', 'flex');
        $.ajax({
            url: "{{ route('distribusiFrekuensiFuelTruck.api') }}",
            type: "GET",

            data: {
                tanggalStatus: $('#tanggalStatus').val(),
                shift: $('#shift').val()
            },

            success: function (res) {
                buildTable(res);
                buildPATable(res);
                updateFrequencySummary(res);
                buildFuelTruckCharts(res);
                buildAllFuelTruckChart(res);
                buildTotalFuelTruckChart(res);
            },

            error: function (xhr) {
                console.error(xhr);
                alert('Gagal mengambil data frekuensi Fuel Truck.');
            },

            complete: function () {
                $('#loadingOverlay').hide();
            }
        });
    }

    function buildTable(res) {
        const mode = $('#summaryMode').val() || 'total';
        const isAverage = mode === 'average';
        const lastColumnTitle = isAverage ? 'Rata-rata' : 'Total';

        let header = `
            <tr>
                <th rowspan="2" class="align-middle text-center">Jam</th>
                <th rowspan="2" class="align-middle text-center">Unit</th>
                <th colspan="${res.fuelTrucks.length}" class="text-center">Frekuensi</th>
                <th rowspan="2" class="align-middle text-center">${lastColumnTitle}</th>
            </tr>
            <tr>
        `;

        res.fuelTrucks.forEach(function (fuelTruck) {
            const status = res.fuelTruckStatus?.[fuelTruck] || 'Unknown';
            const statusClass = 'unit-' + status.toLowerCase().replace(/\s+/g, '-');

            header += `
                <th class="text-center ${statusClass}">
                    ${fuelTruck}
                </th>
            `;
        });

        header += `</tr>`;
        $('#tblHeader').html(header);

        let html = '';

        res.hours.forEach(function (hour) {
            res.units.forEach(function (unit, index) {
                html += `<tr>`;

                if (index === 0) {
                    html += `
                        <td rowspan="${res.units.length}"
                            class="align-middle text-center fw-semibold">
                            ${hour}
                        </td>
                    `;
                }

                html += `<td class="text-start">${unit}</td>`;

                let rowTotal = 0;

                res.fuelTrucks.forEach(function (fuelTruck) {
                    const value = Number(
                        res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0
                    );

                    rowTotal += value;

                    html += `
                        <td class="text-center">
                            ${displayValue(value)}
                        </td>
                    `;
                });

                const rowAverage = res.fuelTrucks.length > 0
                    ? rowTotal / res.fuelTrucks.length
                    : 0;

                const lastValue = isAverage ? rowAverage : rowTotal;

                html += `
                    <td class="text-center fw-bold">
                        ${displayValue(lastValue, isAverage ? 2 : 0)}
                    </td>
                `;

                html += `</tr>`;
            });

            // Total / rata-rata untuk setiap jam.
            let totalHour = 0;

            res.fuelTrucks.forEach(function (fuelTruck) {
                totalHour += Number(
                    res.totalsByHour?.[hour]?.[fuelTruck] || 0
                );
            });

            const averageHour = (
                res.units.length * res.fuelTrucks.length
            ) > 0
                ? totalHour / (res.units.length * res.fuelTrucks.length)
                : 0;

            const hourValue = isAverage ? averageHour : totalHour;

            html += `
                <tr class="table-warning">
                    <td colspan="2" class="text-start fw-bold">
                        ${isAverage ? 'Rata-rata' : 'Total'} ${hour}
                    </td>
            `;

            // Saat mode rata-rata, setiap fuel truck juga ditampilkan
            // sebagai rata-rata terhadap jumlah unit.
            res.fuelTrucks.forEach(function (fuelTruck) {
                const fuelTruckHourTotal = Number(
                    res.totalsByHour?.[hour]?.[fuelTruck] || 0
                );

                const fuelTruckHourAverage = res.units.length > 0
                    ? fuelTruckHourTotal / res.units.length
                    : 0;

                const value = isAverage
                    ? fuelTruckHourAverage
                    : fuelTruckHourTotal;

                html += `
                    <td class="text-center fw-bold">
                        ${displayValue(value, isAverage ? 2 : 0)}
                    </td>
                `;
            });

            html += `
                    <td class="text-center fw-bold">
                        ${displayValue(hourValue, isAverage ? 2 : 0)}
                    </td>
                </tr>
            `;
        });

        // BAGIAN PALING BAWAH SELALU MENAMPILKAN KEDUANYA:
        // Grand Total Shift dan Rata-rata Shift.
        // Select Total/Rata-rata hanya mengatur kolom terakhir dan
        // baris per jam, bukan dua baris ringkasan paling bawah ini.
        const grandTotal = Number(res.grandTotal || 0);

        // Grand Total Shift
        html += `
            <tr class="table-grand-total">
                <td colspan="2" class="text-start fw-bold">
                    Grand Total Shift
                </td>
        `;

        res.fuelTrucks.forEach(function (fuelTruck) {
            const truckTotal = Number(
                res.fuelTruckGrandTotal?.[fuelTruck] || 0
            );

            html += `
                <td class="text-center fw-bold">
                    ${displayValue(truckTotal, 0)}
                </td>
            `;
        });

        html += `
                <td class="text-center fw-bold">
                    ${displayValue(grandTotal, 0)}
                </td>
            </tr>
        `;

        // Rata-rata Shift
        html += `
            <tr class="table-average">
                <td colspan="2" class="text-start fw-bold">
                    Rata-rata Shift
                </td>
        `;

        res.fuelTrucks.forEach(function (fuelTruck) {
            const truckTotal = Number(
                res.fuelTruckGrandTotal?.[fuelTruck] || 0
            );

            const truckAverage = res.hours.length > 0
                ? truckTotal / res.hours.length
                : 0;

            html += `
                <td class="text-center fw-bold">
                    ${displayValue(truckAverage, 2)}
                </td>
            `;
        });

        html += `
                <td class="text-center fw-bold">
                    ${displayValue(res.averagePerHour || 0, 2)}
                </td>
            </tr>
        `;

        $('#tblBody').html(html);
    }

    function buildBody(res) {
        let html = '';
        res.hours.forEach(function (hour) {
            res.statuses.forEach(function (status, index) {
                html += '<tr>';
                if (index == 0) {
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
                        value = res.pivot[hour][status][unit.id];
                    }
                    html += `
                        <td>${formatNumber(value)}</td>
                    `;
                });
                html += '</tr>';
            });
        });
        buildTotal(html, res);
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
        loadFrequency();
        $('#cariStatus').click(function () {
            loadFrequency();
        });

    });

    $('#exportAllExcel').on('click', function () {

        const workbook = XLSX.utils.book_new();
        const detailTable = document.getElementById('tblAvailability');

        if (detailTable) {

            const detailSheet = XLSX.utils.table_to_sheet(
                detailTable,
                {
                    raw: true
                }
            );

            // Lebar kolom
            detailSheet['!cols'] = [
                { wch: 10 }, // Jam
                { wch: 15 }, // Status
                { wch: 30 }, // Activity
                { wch: 14 }, // Unit 1
                { wch: 14 }, // Unit 2
                { wch: 14 }, // Unit 3
                { wch: 14 }, // Unit 4
                { wch: 14 }, // dst
            ];

            XLSX.utils.book_append_sheet(
                workbook,
                detailSheet,
                'Detail Status'
            );
        }

        const frequencyTable = document.getElementById('tblPA');
        if (frequencyTable) {
            const frequencySheet =
                XLSX.utils.table_to_sheet(
                    frequencyTable,
                    {
                        raw: true
                    }
                );

            frequencySheet['!cols'] = [
                { wch: 12 }, // Jam
                { wch: 14 },
                { wch: 14 },
                { wch: 14 },
                { wch: 14 },
                { wch: 14 },
            ];

            XLSX.utils.book_append_sheet(
                workbook,
                frequencySheet,
                'Physical Availability'
            );
        }

        let tanggal = $('#tanggalStatus').val();
        if (!tanggal) {
            tanggal = 'All_Date';
        } else {
            tanggal = tanggal
                .replaceAll(' ', '_')
                .replaceAll('/', '-');
        }

        XLSX.writeFile(
            workbook,
            `Distribusi_Frekuensi_Fuel_Truck_${tanggal}.xlsx`
        );
    });
</script>
