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

</style>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-3 fw-semibold">Distribusi Frekuensi Fuel Truck</h4>

                    <div class="col-12">
                        <div class="mb-3 row">
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
                            <div class="col-6 col-md-2 mb-2 d-flex align-items-end gap-2">
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
                        <div class="card">
                            <div class="card-body p-2">

                                <div id="chartAllFuelTruck"></div>

                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="card">
                            <div class="card-body p-2">

                                <div id="chartTotalFuelTruck"></div>

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

    function buildPATable(res)
    {
        let header = '';

        header += `
            <tr>
                <th rowspan="2" class="align-middle text-center">
                    Unit
                </th>

                <th colspan="${res.hours.length}" class="text-center">
                    Frekuensi
                </th>

                <th rowspan="2" class="align-middle text-center">
                    Total
                </th>
            </tr>

            <tr>
        `;

        res.hours.forEach(function (hour) {
            header += `
                <th class="text-center">
                    ${hour}
                </th>
            `;
        });
        header += `
            </tr>
        `;
        $('#tblPAHeader').html(header);
        let html = '';
        res.units.forEach(function (unit) {

            html += `
                <tr>
                    <td class="text-start">
                        ${unit}
                    </td>
            `;
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
                        ${value}
                    </td>
                `;
            });

            html += `
                    <td class="text-center fw-bold">
                        ${rowTotal}
                    </td>
                </tr>
            `;
        });

        html += `
            <tr class="table-warning">
                <td class="text-start fw-bold">
                    Total
                </td>
        `;

        let grandTotal = 0;
        res.hours.forEach(function (hour) {
            let total = 0;
            res.units.forEach(function (unit) {
                res.fuelTrucks.forEach(function (fuelTruck) {
                    total += Number(
                        res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0
                    );
                });
            });

            grandTotal += total;
            html += `
                <td class="text-center fw-bold">
                    ${total}
                </td>
            `;
        });

        html += `
                <td class="text-center fw-bold">
                    ${grandTotal}
                </td>
            </tr>
        `;

        $('#tblPABody').html(html);
    }

    function buildFuelTruckCharts(res)
    {
        const container = $('#fuelTruckCharts');
        container.empty();
        res.fuelTrucks.forEach(function (fuelTruck) {
            let seriesData = [];
            res.hours.forEach(function (hour) {
                let total = 0;
                res.units.forEach(function (unit) {
                    total += Number(
                        res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0
                    );
                });

                seriesData.push(total);
            });


            const chartId =
                'chartFuelTruck_' +
                fuelTruck.replace(/[^a-zA-Z0-9]/g, '');

            const html = `
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-body p-2">
                            <div id="${chartId}"></div>
                        </div>
                    </div>
                </div>
            `;

            container.append(html);
            const options = {
                chart: {
                    type: 'bar',
                    height: 360,
                    toolbar: {
                        show: false
                    }
                },

                series: [
                    {
                        name: fuelTruck,
                        data: seriesData
                    }
                ],

                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '55%'
                    }
                },

                dataLabels: {
                    enabled: false
                },

                xaxis: {
                    categories: res.hours,
                    title: {
                        text: 'Frekuensi refuelling'
                    }

                },

                yaxis: {
                    title: {
                        text: 'Jam'
                    }
                },

                title: {
                    text:
                        'Distribusi frekuensi refuelling fuel truck ' +
                        fuelTruck,
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        fontWeight: 600
                    }

                },

                legend: {
                    position: 'bottom'
                },

                tooltip: {
                    y: {
                        formatter: function (value) {
                            return value + ' refuelling';
                        }
                    }

                }

            };


            const chart = new ApexCharts(
                document.querySelector('#' + chartId),
                options
            );

            chart.render();

        });
    }

    function buildAllFuelTruckChart(res)
    {
        const series = [];
        res.fuelTrucks.forEach(function (fuelTruck) {
            const data = [];
            res.hours.forEach(function (hour) {
                let total = 0;
                res.units.forEach(function (unit) {
                    total += Number(
                        res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0
                    );

                });
                data.push(total);
            });

            series.push({
                name: fuelTruck,
                data: data
            });

        });


        const options = {
            chart: {
                type: 'bar',
                height: 550,
                toolbar: {
                    show: false
                }
            },

            series: series,
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '65%'
                }

            },

            dataLabels: {
                enabled: false
            },

            xaxis: {
                categories: res.hours,
                title: {
                    text: 'Frekuensi refuelling'
                }
            },

            yaxis: {
                title: {
                    text: 'Jam'
                }
            },

            title: {
                text: 'Distribusi frekuensi refuelling fuel truck',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 600
                }
            },

            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },

            tooltip: {
                y: {
                    formatter: function (value) {
                        return value + ' refuelling';
                    }
                }
            }
        };


        $('#chartAllFuelTruck').empty();
        const chart = new ApexCharts(
            document.querySelector('#chartAllFuelTruck'),
            options
        );

        chart.render();
    }

    function buildTotalFuelTruckChart(res)
    {
        const data = [];
        res.hours.forEach(function (hour) {
            let total = 0;
            res.fuelTrucks.forEach(function (fuelTruck) {
                res.units.forEach(function (unit) {
                    total += Number(
                        res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0
                    );
                });

            });

            data.push(total);

        });


        const options = {
            chart: {
                type: 'bar',
                height: 550,
                toolbar: {
                    show: false
                }

            },

            series: [
                {
                    name: 'Total',
                    data: data
                }

            ],

            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '55%'
                }

            },

            dataLabels: {
                enabled: false
            },

            xaxis: {
                categories: res.hours,
                title: {
                    text: 'Frekuensi refuelling'
                }
            },

            yaxis: {
                title: {
                    text: 'Jam'
                }

            },

            title: {
                text: 'Distribusi frekuensi refuelling fuel truck',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 600
                }
            },

            legend: {
                position: 'bottom'
            },

            tooltip: {
                y: {
                    formatter: function (value) {
                        return value + ' refuelling';
                    }
                }
            }
        };


        $('#chartTotalFuelTruck').empty();
        const chart = new ApexCharts(
            document.querySelector('#chartTotalFuelTruck'),
            options
        );
        chart.render();
    }

    function formatNumber(value) {
        return Number(value || 0).toFixed(1);
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

    function buildTable(res)
    {
        let header = '';
        header += `
            <tr>
                <th rowspan="2" class="align-middle text-center">
                    Jam
                </th>

                <th rowspan="2" class="align-middle text-center">
                    Unit
                </th>

                <th colspan="${res.fuelTrucks.length}" class="text-center">
                    Frekuensi
                </th>

                <th rowspan="2" class="align-middle text-center">
                    Total
                </th>
            </tr>

            <tr>
        `;

        res.fuelTrucks.forEach(function (fuelTruck) {
            const status = res.fuelTruckStatus?.[fuelTruck] || 'Unknown';
            const statusClass = 'unit-' + status
                .toLowerCase()
                .replace(/\s+/g, '-');

            header += `
                <th class="text-center ${statusClass}">
                    ${fuelTruck}
                </th>
            `;
        });

        header += `
            </tr>
        `;

        $('#tblHeader').html(header);

        let html = '';
        res.hours.forEach(function (hour) {
            res.units.forEach(function (unit, index) {
                html += `<tr>`;
                if (index === 0) {

                    html += `
                        <td
                            rowspan="${res.units.length}"
                            class="align-middle text-center fw-semibold"
                        >
                            ${hour}
                        </td>
                    `;
                }

                html += `
                    <td class="text-start">
                        ${unit}
                    </td>
                `;

                let rowTotal = 0;
                res.fuelTrucks.forEach(function (fuelTruck) {
                    const value = Number(
                        res.pivot?.[hour]?.[unit]?.[fuelTruck] || 0
                    );
                    rowTotal += value;
                    html += `
                        <td class="text-center">
                            ${value}
                        </td>
                    `;
                });

                html += `
                    <td class="text-center fw-bold">
                        ${rowTotal}
                    </td>
                `;

                html += `</tr>`;
            });

            html += `
                <tr class="table-warning">
                    <td colspan="2" class="text-start fw-bold">
                        Total
                    </td>
            `;

            let totalHour = 0;
            res.fuelTrucks.forEach(function (fuelTruck) {
                const value = Number(
                    res.totalsByHour?.[hour]?.[fuelTruck] || 0
                );
                totalHour += value;
                html += `
                    <td class="text-center fw-bold">
                        ${value}
                    </td>
                `;
            });

            html += `
                    <td class="text-center fw-bold">
                        ${totalHour}
                    </td>
                </tr>
            `;
        });

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
