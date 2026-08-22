@include('layout.head', ['title' => 'Detail Status'])
@include('layout.header')
@include('layout.theme_settings')
@include('layout.sidebar')

<style>
    @media (max-width: 767.98px) {
        .dt-buttons {
            display: none !important;
        }
    }

    .page-title-box label {
        font-weight: 500;
        margin-bottom: 5px;
    }


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

    #tblAvailability .hour-cell {
        vertical-align: middle !important;
        text-align: center !important;
        white-space: nowrap;
        font-weight: 500;
        background: #f8f9fa;
        min-width: 80px;
    }

    #tblAvailability .status-cell {
        text-align: left !important;
        white-space: nowrap;
        font-weight: 600;
        min-width: 110px;
    }

    #tblAvailability .activity-cell {
        text-align: left !important;
        white-space: nowrap;
        font-weight: 400;
        min-width: 190px;
    }

    #tblAvailability .duration-cell {
        text-align: center !important;
        white-space: nowrap;
        min-width: 85px;
    }

    #tblAvailability .total-row td {
        background: #fff2cc !important;
        font-weight: 600;
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

    .status-table-card {
        overflow: hidden;
    }

    .status-table-wrapper {
        overflow-x: auto;
        width: 100%;
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

    #loadingOverlay {
        display: none;
        position: absolute;
        inset: 0;
        z-index: 20;
        background: rgba(255, 255, 255, .75);
        align-items: center;
        justify-content: center;
    }

    .table-wrapper {
        position: relative;
    }

    .loading-box {
        text-align: center;
    }

    @media (max-width: 767.98px) {

        #tblAvailability {
            font-size: 12px;
        }

        #tblAvailability th,
        #tblAvailability td {
            padding: 4px 6px;
        }

        #tblAvailability .activity-cell {
            min-width: 160px;
        }

        #tblAvailability .duration-cell {
            min-width: 75px;
        }

    }
</style>


<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="fw-semibold">
                        Status
                    </h4>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6 col-md-2 mb-2">
                                <label for="tanggalStatus">Tanggal</label>
                                <input type="text" id="tanggalStatus" class="form-control" name="tanggalStatus" >
                            </div>
                            <div class="col-6 col-md-1 mb-2">
                                <label for="shift">Shift</label>
                                <select class="form-select" name="shift" id="shift" >
                                    <option value="Semua">Semua</option>
                                    <option value="6" selected>Siang</option>
                                    <option value="7">Malam</option>
                                </select>

                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label for="choices-single-default">Unit ID</label>
                                <select class="form-control" data-choices name="vhc_id" id="choices-single-default" >
                                    <option value="Semua">Semua</option>
                                    @foreach ($vehicle as $vhc)
                                        <option value="{{ $vhc->VHC_ID }}" >
                                            {{ $vhc->VHC_ID }}
                                        </option>
                                    @endforeach
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
        <div class="card status-table-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-1">
                            Detail Status Fuel Truck
                        </h4>
                        <small class="text-muted">
                            Durasi aktivitas per jam dan unit
                        </small>
                    </div>
                </div>
                <div class="table-wrapper">
                    <div id="loadingOverlay">
                        <div class="loading-box">
                            <div class="spinner-border text-primary"></div>
                            <h6 class="mt-3 mb-1">
                                Memuat Data...
                            </h6>
                            <small class="text-muted">
                                Mohon tunggu sebentar
                            </small>
                        </div>
                    </div>
                    <div class="status-table-wrapper">
                        <table id="tblAvailability" class="table table-bordered table-sm align-middle text-center" >
                            <thead id="tblHeader"></thead>
                            <tbody id="tblBody"></tbody>
                        </table>
                    </div>
                    <div class="status-legend">
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
            const mm = String(
                    today.getMonth() + 1
                ).padStart(2, '0');
            const dd = String(
                    today.getDate()
                ).padStart(2, '0');
            rangeInput.placeholder = `${yyyy}-${mm}-${dd} to ${yyyy}-${mm}-${dd}`;
        }
    });

    function getUnitStatusClass(status) {
        switch (
            String(status || '').toLowerCase()
        ) {
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

    function formatNumber(value) {
        return Number(
            value || 0
        ).toFixed(1);

    }


    function buildTable(res) {
        let header = '';
        header += `
            <tr>

                <th
                    rowspan="2"
                    class="align-middle text-center"
                    style="width:80px;"
                >
                    Jam
                </th>


                <th
                    rowspan="2"
                    class="align-middle text-center"
                    style="width:110px;"
                >
                    Status
                </th>


                <th
                    rowspan="2"
                    class="align-middle text-center"
                    style="width:190px;"
                >
                    Activity
                </th>


                <th
                    colspan="${res.units.length}"
                    class="text-center"
                >
                    Durasi (Menit)
                </th>

            </tr>


            <tr>

        `;

        res.units.forEach(function (unit) {
            const statusClass =
                getUnitStatusClass(
                    unit.status
                );
            header += `

                <th
                    class="text-center ${statusClass}"
                    style="min-width:85px;"
                    title="Status: ${unit.status}"
                >
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
        res.hours.forEach(function (hour) {
            const hourData = res.pivot[hour] || {};
            let totalRows = 0;
            res.statuses.forEach(function (status) {

                const activities =
                    res.activities &&
                    res.activities[status]
                        ? res.activities[status]
                        : [];

                totalRows += activities.length;
            });

            if (totalRows === 0) {
                html += `
                    <tr>

                        <td class="hour-cell">
                            ${hour}
                        </td>

                        <td
                            colspan="2"
                            class="text-center text-muted"
                        >
                            -
                        </td>
                `;

                res.units.forEach(function () {
                    html += `
                        <td class="duration-cell">
                            -
                        </td>
                    `;
                });

                html += `
                    </tr>
                `;

                return;
            }

            let hourRowIndex = 0;
            res.statuses.forEach(function (status) {
                const activities =
                    res.activities &&
                    res.activities[status]
                        ? res.activities[status]
                        : [];

                if (activities.length === 0) {
                    return;
                }

                activities.forEach(function (
                    activity,
                    activityIndex
                ) {
                    html += '<tr>';
                    if (hourRowIndex === 0) {

                        html += `
                            <td
                                rowspan="${totalRows}"
                                class="hour-cell"
                            >
                                ${hour}
                            </td>
                        `;

                    }

                    if (activityIndex === 0) {
                        html += `
                            <td
                                rowspan="${activities.length}"
                                class="status-cell"
                            >
                                ${status}
                            </td>
                        `;

                    }
                    html += `
                        <td class="activity-cell">
                            ${activity}
                        </td>
                    `;

                    res.units.forEach(function (unit) {
                        let value = 0;

                        if (
                            hourData[status] &&
                            hourData[status][activity] &&
                            hourData[status][activity][unit.id] !== undefined
                        ) {

                            value = Number(
                                hourData[status][activity][unit.id]
                            );

                        }

                        html += `
                            <td class="duration-cell">
                                ${
                                    value > 0
                                        ? formatNumber(value)
                                        : '-'
                                }
                            </td>
                        `;

                    });
                    html += '</tr>';
                    hourRowIndex++;
                });

            });

        });

        buildTotal(
            html,
            res
        );
    }

    function buildTotal(
        html,
        res
    ) {
        res.statuses.forEach(function (status) {
            const statusTotals =
                res.totals[status] || {};
            const activities =
                Object.keys(
                    statusTotals
                );
            if (
                activities.length === 0
            ) {
                return;
            }
            html += `
                <tr class="total-row">
                    <td
                        colspan="3"
                        class="text-start fw-bold ps-3"
                    >
                        Total ${status}
                    </td>
            `;
            res.units.forEach(function (unit) {
                let total = 0;
                activities.forEach(function (activity) {
                    if (
                        statusTotals[activity] &&
                        statusTotals[activity][unit.id]
                            !== undefined
                    ) {
                        total += Number(
                            statusTotals[
                                activity
                            ][
                                unit.id
                            ]
                        );
                    }
                });
                html += `
                    <td class="duration-cell">
                        ${
                            total > 0
                                ? formatNumber(total)
                                : '-'
                        }
                    </td>
                `;
            });
            html += '</tr>';
        });
        $('#tblBody').html(html);

    }
    function loadStatus() {
        $('#loadingOverlay')
            .css('display', 'flex');
        $.ajax({
            url: "{{ route('status.api') }}",
            type: "GET",
            data: {
                tanggalStatus:
                    $('#tanggalStatus').val(),
                shift:
                    $('#shift').val(),
                vhc_id:
                    $('#choices-single-default').val()
            },
            success: function (res) {
                buildTable(res);
            },


            error: function (xhr) {
                console.error(
                    'Status API Error:',
                    xhr.responseText
                );
                $('#tblHeader').html('');
                $('#tblBody').html(`
                    <tr>
                        <td
                            colspan="100"
                            class="text-center text-danger py-4"
                        >
                            Gagal mengambil data.
                        </td>
                    </tr>
                `);
            },
            complete: function () {
                $('#loadingOverlay')
                    .hide();
            }
        });

    }
    $(document).ready(function () {
        loadStatus();

        $('#cariStatus').click(function () {
            loadStatus();
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
            const range = XLSX.utils.decode_range(detailSheet['!ref']);
            const columnWidths = [];

            for (let C = range.s.c; C <= range.e.c; C++) {
                let maxWidth = 0;

                for (let R = range.s.r; R <= range.e.r; R++) {
                    const cellAddress = XLSX.utils.encode_cell({
                        r: R,
                        c: C
                    });

                    const cell = detailSheet[cellAddress];

                    if (cell && cell.v !== undefined && cell.v !== null) {
                        const value = String(cell.v);

                        // Hitung panjang isi cell
                        maxWidth = Math.max(maxWidth, value.length);
                    }
                }

                // Tambahkan sedikit ruang
                columnWidths.push({
                    wch: Math.min(maxWidth + 2, 50)
                });
            }

            detailSheet['!cols'] = columnWidths;

            XLSX.utils.book_append_sheet(
                workbook,
                detailSheet,
                'Detail Status'
            );
        }

        const tanggal =
            $('#tanggalStatus').val()
                .replaceAll(' ', '_')
                .replaceAll('/', '-');

        XLSX.writeFile(
            workbook,
            `Detail Status Fuel Truck_${tanggal}.xlsx`
        );
    });

</script>
