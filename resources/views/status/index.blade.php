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

    .total-row td {
        background: #fff2cc !important;
        font-weight: 600;
    }

    .average-row td {
        background: #e8f5e9 !important;
        font-weight: 600;
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
                            <div class="col-6 col-md-1 mb-2">
                                <label for="aggregation">Tampilan</label>
                                <select class="form-select" name="aggregation" id="aggregation">
                                    <option value="total" selected>Total</option>
                                    <option value="average">Rata-rata</option>
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
    document.getElementById('tanggalStatus').flatpickr({ mode: 'range' });

    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const rangeDate = params.get('rangeDate');
        const input = document.getElementById('tanggalStatus');

        if (rangeDate) {
            input.value = rangeDate;
        } else {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            input.placeholder = `${yyyy}-${mm}-${dd} to ${yyyy}-${mm}-${dd}`;
        }
    });

    let statusResponse = null;

    function getUnitStatusClass(status) {
        switch (String(status || '').toLowerCase()) {
            case 'ready': return 'unit-ready';
            case 'standby': return 'unit-standby';
            case 'delay': return 'unit-delay';
            case 'breakdown': return 'unit-breakdown';
            default: return 'unit-unknown';
        }
    }

    function formatNumber(value) {
        return Number(value || 0).toFixed(1);
    }

    function getActivities(res, status) {
        return res.activities && res.activities[status] ? res.activities[status] : [];
    }

    function buildTable(res) {
        const aggregation = $('#aggregation').val();
        const title = aggregation === 'average'
            ? 'Rata-rata Durasi per Kejadian (Menit)'
            : 'Total Durasi (Menit)';

        let header = `
            <tr>
                <th rowspan="2" class="align-middle text-center" style="width:80px;">Jam</th>
                <th rowspan="2" class="align-middle text-center" style="width:110px;">Status</th>
                <th rowspan="2" class="align-middle text-center" style="width:190px;">Activity</th>
                <th colspan="${res.units.length}" class="text-center">${title}</th>
            </tr>
            <tr>`;

        res.units.forEach(unit => {
            header += `
                <th class="text-center ${getUnitStatusClass(unit.status)}"
                    style="min-width:85px;"
                    title="Status: ${unit.status}">
                    ${unit.id}
                </th>`;
        });

        header += '</tr>';
        $('#tblHeader').html(header);
        buildBody(res);
    }

    function buildBody(res) {
        let html = '';
        const aggregation = $('#aggregation').val();
        const source = aggregation === 'average' ? res.averages : res.pivot;

        res.hours.forEach(hour => {
            const hourData = source && source[hour] ? source[hour] : {};
            let totalRows = 0;

            res.statuses.forEach(status => {
                totalRows += getActivities(res, status).length;
            });

            if (!totalRows) {
                html += `<tr>
                    <td class="hour-cell">${hour}</td>
                    <td colspan="2" class="text-center text-muted">-</td>
                    ${res.units.map(() => '<td class="duration-cell">-</td>').join('')}
                </tr>`;
                return;
            }

            let hourRowIndex = 0;

            res.statuses.forEach(status => {
                const activities = getActivities(res, status);
                if (!activities.length) return;

                activities.forEach((activity, activityIndex) => {
                    html += '<tr>';

                    if (hourRowIndex === 0) {
                        html += `<td rowspan="${totalRows}" class="hour-cell">${hour}</td>`;
                    }

                    if (activityIndex === 0) {
                        html += `<td rowspan="${activities.length}" class="status-cell">${status}</td>`;
                    }

                    html += `<td class="activity-cell">${activity}</td>`;

                    res.units.forEach(unit => {
                        const value = hourData[status] &&
                                    hourData[status][activity] &&
                                    hourData[status][activity][unit.id] !== undefined
                            ? Number(hourData[status][activity][unit.id])
                            : 0;

                        html += `<td class="duration-cell">${value > 0 ? formatNumber(value) : '-'}</td>`;
                    });

                    html += '</tr>';
                    hourRowIndex++;
                });
            });
        });

        buildTotal(html, res);
    }

    function buildTotal(html, res) {
        const buildSummary = (label, sourceTotals, isAverage = false) => {
            const rowClass = isAverage ? 'average-row' : 'total-row';

            res.statuses.forEach(status => {
                const statusTotals = sourceTotals?.[status] || {};
                const activities = Object.keys(statusTotals);

                html += `
                    <tr class="${rowClass}">
                        <td colspan="3" class="text-start fw-bold ps-3">${label} ${status}</td>
                `;

                res.units.forEach(unit => {
                    let value = 0;

                    if (isAverage) {
                        value = Number(res.averageStatusTotals?.[status]?.[unit.id] || 0);
                    } else {
                        activities.forEach(activity => {
                            value += Number(statusTotals?.[activity]?.[unit.id] || 0);
                        });
                    }

                    html += `<td class="duration-cell">${value > 0 ? formatNumber(value) : '-'}</td>`;
                });

                html += '</tr>';
            });
        };

        buildSummary('Total', res.totals);
        buildSummary('Rata-rata', res.averageTotals, true);

        $('#tblBody').html(html);
    }

    function loadStatus() {
        $('#loadingOverlay').css('display', 'flex');

        $.ajax({
            url: "{{ route('status.api') }}",
            type: 'GET',
            data: {
                tanggalStatus: $('#tanggalStatus').val(),
                shift: $('#shift').val(),
                vhc_id: $('#choices-single-default').val()
            },
            success: function (res) {
                statusResponse = res;
                buildTable(res);
            },
            error: function (xhr) {
                console.error('Status API Error:', xhr.responseText);
                $('#tblHeader').html('');
                $('#tblBody').html(`
                    <tr>
                        <td colspan="100" class="text-center text-danger py-4">
                            Gagal mengambil data.
                        </td>
                    </tr>
                `);
            },
            complete: function () {
                $('#loadingOverlay').hide();
            }
        });
    }

    $(document).ready(function () {
        loadStatus();

        $('#cariStatus').on('click', function () {
            loadStatus();
        });

        $('#shift').on('change', function () {
            loadStatus();
        });

        $('#aggregation').on('change', function () {
            if (statusResponse) buildTable(statusResponse);
        });
    });

    $('#exportAllExcel').on('click', function () {
        const workbook = XLSX.utils.book_new();
        const table = document.getElementById('tblAvailability');

        if (table) {
            const sheet = XLSX.utils.table_to_sheet(table, { raw: true });

            if (sheet['!ref']) {
                const range = XLSX.utils.decode_range(sheet['!ref']);
                const widths = [];

                for (let c = range.s.c; c <= range.e.c; c++) {
                    let maxWidth = 0;

                    for (let r = range.s.r; r <= range.e.r; r++) {
                        const cell = sheet[XLSX.utils.encode_cell({ r, c })];

                        if (cell && cell.v !== undefined && cell.v !== null) {
                            maxWidth = Math.max(maxWidth, String(cell.v).length);
                        }
                    }

                    widths.push({ wch: Math.min(maxWidth + 2, 50) });
                }

                sheet['!cols'] = widths;
            }

            XLSX.utils.book_append_sheet(workbook, sheet, 'Detail Status');
        }

        const tanggal = $('#tanggalStatus').val().replaceAll(' ', '_').replaceAll('/', '-');
        const aggregation = $('#aggregation').val();
        const shift = $('#shift').val();

        XLSX.writeFile(
            workbook,
            `Detail_Status_Fuel_Truck_${tanggal}_${shift}_${aggregation}.xlsx`
        );
    });
</script>
