@include('layout.head', ['title' => 'Distribusi Frekuensi Fuel Station'])
@include('layout.header')
@include('layout.theme_settings')
@include('layout.sidebar')

<style>
    @media (max-width: 767.98px) {
        .dt-buttons {
            display: none !important;
        }
    }
    .frequency-table th{
        background:#fcfbfb;
        color:#000000;
        white-space:nowrap;
    }

    .frequency-table td{
        vertical-align:middle;
    }

    .frequency-table tbody tr:hover{
        background:#f8f9fa;
    }

    .station-title {
        font-size: 14px;
        font-weight: 700;
        padding: 8px 12px;
        background: #dce6f1;
        border-bottom: 1px solid #dee2e6;
    }

    .left-table-wrapper,
    .right-table-wrapper {
        overflow-x: auto;
    }

    #loadingOverlay {
        position: absolute;
        inset: 0;
        z-index: 20;
        background: rgba(255, 255, 255, 0.75);
        display: none;
        align-items: center;
        justify-content: center;
    }

    .loading-box {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .page-content .card {
        margin-bottom: 0;
    }

    .right-panel-card {
        margin-bottom: 15px !important;
    }

    .right-panel-card:last-child {
        margin-bottom: 0 !important;
    }

    .filter-label {
        font-weight: 500;
        margin-bottom: 5px;
    }

    .summary-row td {
        font-weight: 700;
        background-color: #fff3cd !important;
    }

    .grand-summary-row td {
        font-weight: 700;
        background-color: #ffe9cc !important;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-3 fw-semibold">Distribusi Frekuensi Fuel Station</h4>

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
                            <div class="col-6 col-md-2 mb-2">
                                <label for="summaryMode">Tampilan</label>
                                <select id="summaryMode" class="form-select">
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
                        <div id="fuelStationTables"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('layout.footer')

<script>
    let latestFrequencyResponse = null;

    function getSummaryMode() {
        return $('#summaryMode').val() || 'total';
    }

    function summaryLabel() {
        return getSummaryMode() === 'average'
            ? 'Rata-rata'
            : 'Total';
    }

    function displayValue(value, decimals = 2) {
        const number = Number(value || 0);

        if (number === 0) {
            return '-';
        }

        return Number.isInteger(number)
            ? number
            : number.toFixed(decimals);
    }

    function average(total, count) {
        return count > 0 ? total / count : 0;
    }

    document.getElementById('tanggalStatus').flatpickr({
        mode: 'range',
        dateFormat: 'Y-m-d'
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

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getUnitStatusClass(status) {
        switch (String(status || '').toLowerCase()) {
            case 'ready':
                return 'table-primary';

            case 'standby':
                return 'table-success';

            case 'delay':
                return 'table-warning';

            case 'breakdown':
                return 'table-danger';

            default:
                return '';
        }
    }

    function buildTable(res) {

        const isAverage = getSummaryMode() === 'average';

        let header = `
            <tr>
                <th rowspan="2" class="text-center align-middle">
                    Jam
                </th>

                <th rowspan="2" class="text-center align-middle">
                    Status
                </th>

                <th colspan="${res.fuelStations.length}" class="text-center">
                    Frekuensi
                </th>

                <th rowspan="2" class="text-center align-middle">
                    ${isAverage ? 'Rata-rata' : 'Total'}
                </th>
            </tr>

            <tr>
        `;

        res.fuelStations.forEach(function (fuelStation) {

            header += `
                <th class="text-center">
                    ${escapeHtml(fuelStation)}
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
                        <td
                            rowspan="${res.units.length}"
                            class="text-center align-middle fw-semibold"
                        >
                            ${escapeHtml(hour)}
                        </td>
                    `;

                }

                html += `
                    <td class="text-start">
                        ${escapeHtml(unit)}
                    </td>
                `;

                let rowTotal = 0;

                res.fuelStations.forEach(function (fuelStation) {

                    const value = Number(
                        res.pivot?.[hour]?.[unit]?.[fuelStation] || 0
                    );

                    rowTotal += value;

                    html += `
                        <td class="text-center">
                            ${displayValue(value, 0)}
                        </td>
                    `;

                });

                const rowSummary = isAverage
                    ? average(rowTotal, res.fuelStations.length)
                    : rowTotal;

                html += `
                    <td class="text-center fw-bold">
                        ${displayValue(rowSummary)}
                    </td>
                `;

                html += `</tr>`;

            });

            // Summary per jam mengikuti pilihan Tampilan.
            html += `
                <tr class="summary-row">
                    <td colspan="2" class="text-start">
                        ${isAverage ? 'Rata-rata' : 'Total'}
                    </td>
            `;

            let hourTotal = 0;

            res.fuelStations.forEach(function (fuelStation) {

                const stationHourTotal = Number(
                    res.totalsByHour?.[hour]?.[fuelStation] || 0
                );

                hourTotal += stationHourTotal;

                const value = isAverage
                    ? average(stationHourTotal, res.units.length)
                    : stationHourTotal;

                html += `
                    <td class="text-center">
                        ${displayValue(value)}
                    </td>
                `;

            });

            const hourSummary = isAverage
                ? average(
                    hourTotal,
                    res.units.length * res.fuelStations.length
                )
                : hourTotal;

            html += `
                    <td class="text-center fw-bold">
                        ${displayValue(hourSummary)}
                    </td>
                </tr>
            `;

        });

        // ============================================================
        // GRAND TOTAL PER UNIT
        //
        // Contoh:
        // Grand Total | Hauler | 3 | 16 | 19
        //             | Grader | - | -  | -
        //             | Dozer  | - | 6  | 6
        //
        // TOTAL = SM-B1 + SM-B2
        // ============================================================

        res.units.forEach(function (unit, unitIndex) {

            html += `<tr class="grand-summary-row">`;

            if (unitIndex === 0) {
                html += `
                    <td
                        rowspan="${res.units.length}"
                        class="text-start align-middle fw-bold"
                    >
                        Grand Total
                    </td>
                `;
            }

            html += `
                <td class="text-start fw-bold">
                    ${escapeHtml(unit)}
                </td>
            `;

            let unitTotal = 0;

            res.fuelStations.forEach(function (fuelStation) {

                const stationTotal = Number(
                    res.totalsByUnit?.[unit]?.[fuelStation] || 0
                );

                unitTotal += stationTotal;

                html += `
                    <td class="text-center fw-bold">
                        ${displayValue(stationTotal, 0)}
                    </td>
                `;
            });

            // Total unit HARUS merupakan penjumlahan semua station.
            html += `
                <td class="text-center fw-bold">
                    ${displayValue(unitTotal, 0)}
                </td>
            `;

            html += `</tr>`;
        });


        // ============================================================
        // RATA-RATA PER UNIT
        //
        // Rata-rata dihitung per station:
        // total station / jumlah jam
        //
        // Contoh:
        // Hauler = 3 / 12 = 0.25
        //          16 / 12 = 1.33
        //          19 / 12 = 1.58
        //
        // Jadi BUKAN dibagi jumlah unit x jumlah station.
        // ============================================================

        res.units.forEach(function (unit, unitIndex) {

            html += `<tr class="grand-summary-row">`;

            if (unitIndex === 0) {
                html += `
                    <td
                        rowspan="${res.units.length}"
                        class="text-start align-middle fw-bold"
                    >
                        Rata-rata
                    </td>
                `;
            }

            html += `
                <td class="text-start fw-bold">
                    ${escapeHtml(unit)}
                </td>
            `;

            let unitTotal = 0;

            res.fuelStations.forEach(function (fuelStation) {

                const stationTotal = Number(
                    res.totalsByUnit?.[unit]?.[fuelStation] || 0
                );

                unitTotal += stationTotal;

                // Rata-rata per unit/station = total / jumlah jam.
                const stationAverage = res.hours.length > 0
                    ? stationTotal / res.hours.length
                    : 0;

                html += `
                    <td class="text-center fw-bold">
                        ${displayValue(stationAverage, 2)}
                    </td>
                `;
            });

            // Rata-rata total unit = total seluruh station / jumlah jam.
            const unitAverage = res.hours.length > 0
                ? unitTotal / res.hours.length
                : 0;

            html += `
                <td class="text-center fw-bold">
                    ${displayValue(unitAverage, 2)}
                </td>
            `;

            html += `</tr>`;
        });


        $('#tblBody').html(html);
    }

    function buildFrequencyTable(
        res,
        title,
        fuelStation = null
    ) {

        const isAverage = getSummaryMode() === 'average';

        let html = `
            <div class="card frequency-card right-panel-card">

                <div class="station-title">
                    ${escapeHtml(title)}
                </div>

                <div class="card-body p-2">

                    <div class="right-table-wrapper">

                        <table class="table table-bordered table-sm frequency-table text-center">

                            <thead>

                                <tr>

                                    <th
                                        rowspan="2"
                                        class="text-center align-middle"
                                    >
                                        Unit
                                    </th>

                                    <th
                                        colspan="${res.hours.length}"
                                        class="text-center"
                                    >
                                        Frekuensi
                                    </th>

                                    <th
                                        rowspan="2"
                                        class="text-center align-middle"
                                    >
                                        ${isAverage ? 'Rata-rata' : 'Total'}
                                    </th>

                                </tr>

                                <tr>
        `;

        res.hours.forEach(function (hour) {

            html += `
                <th class="text-center">
                    ${escapeHtml(hour)}
                </th>
            `;

        });

        html += `
                                </tr>

                            </thead>

                            <tbody>
        `;

        // ============================================================
        // DATA PER UNIT
        // ============================================================

        res.units.forEach(function (unit) {

            html += `
                <tr>

                    <td class="text-start">
                        ${escapeHtml(unit)}
                    </td>
            `;

            let rowTotal = 0;

            res.hours.forEach(function (hour) {

                let value = 0;

                if (fuelStation === null) {

                    res.fuelStations.forEach(function (station) {

                        value += Number(
                            res.pivot?.[hour]?.[unit]?.[station] || 0
                        );

                    });

                } else {

                    value = Number(
                        res.pivot?.[hour]?.[unit]?.[fuelStation] || 0
                    );

                }

                rowTotal += value;

                html += `
                    <td class="text-center">
                        ${displayValue(value, 0)}
                    </td>
                `;

            });

            const rowSummary = isAverage
                ? average(rowTotal, res.hours.length)
                : rowTotal;

            html += `
                    <td class="text-center fw-bold">
                        ${displayValue(rowSummary)}
                    </td>

                </tr>
            `;

        });

        // ============================================================
        // SUMMARY AKHIR
        // HANYA SATU: TOTAL ATAU RATA-RATA
        // ============================================================

        let grandTotal = 0;

        res.hours.forEach(function (hour) {

            let hourTotal = 0;

            if (fuelStation === null) {

                res.fuelStations.forEach(function (station) {

                    res.units.forEach(function (unit) {

                        hourTotal += Number(
                            res.pivot?.[hour]?.[unit]?.[station] || 0
                        );

                    });

                });

            } else {

                res.units.forEach(function (unit) {

                    hourTotal += Number(
                        res.pivot?.[hour]?.[unit]?.[fuelStation] || 0
                    );

                });

            }

            grandTotal += hourTotal;

        });

        html += `
            <tr class="grand-summary-row">

                <td class="text-start">
                    ${isAverage ? 'Rata-rata' : 'Grand Total'}
                </td>
        `;

        res.hours.forEach(function (hour) {

            let hourTotal = 0;

            if (fuelStation === null) {

                res.fuelStations.forEach(function (station) {

                    res.units.forEach(function (unit) {

                        hourTotal += Number(
                            res.pivot?.[hour]?.[unit]?.[station] || 0
                        );

                    });

                });

            } else {

                res.units.forEach(function (unit) {

                    hourTotal += Number(
                        res.pivot?.[hour]?.[unit]?.[fuelStation] || 0
                    );

                });

            }

            const value = isAverage
                ? average(hourTotal, res.units.length)
                : hourTotal;

            html += `
                <td class="text-center">
                    ${displayValue(value)}
                </td>
            `;

        });

        const tableSummary = isAverage
            ? average(
                grandTotal,
                res.hours.length * res.units.length
            )
            : grandTotal;

        html += `
                <td class="text-center fw-bold">
                    ${displayValue(tableSummary)}
                </td>

            </tr>
        `;

        html += `
                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        `;

        return html;
    }

    function buildFuelStationTables(res) {
        const container = $('#fuelStationTables');
        container.empty();
        container.append(
            buildFrequencyTable(
                res,
                'All',
                null
            )
        );

        res.fuelStations.forEach(function (fuelStation) {
            container.append(
                buildFrequencyTable(
                    res,
                    fuelStation,
                    fuelStation
                )
            );
        });
    }

    function loadFrequency() {

        $('#loadingOverlay').css(
            'display',
            'flex'
        );

        $.ajax({
            url: "{{ route('distribusiFrekuensiFuelStation.api') }}",
            type: "GET",
            data: {
                tanggalStatus:
                    $('#tanggalStatus').val(),

                shift:
                    $('#shift').val()
            },

            success: function (res) {
                latestFrequencyResponse = res;

                buildTable(res);
                buildFuelStationTables(res);
            },

            error: function (xhr) {
                console.error(xhr);
                alert(
                    'Gagal mengambil data frekuensi Fuel Station.'
                );
            },

            complete: function () {
                $('#loadingOverlay').hide();
            }
        });
    }

    $(document).on('change', '#summaryMode', function () {
        if (!latestFrequencyResponse) {
            return;
        }

        // Tabel tetap tampil. Hanya nilai/label summary yang dibangun ulang.
        buildTable(latestFrequencyResponse);
        buildFuelStationTables(latestFrequencyResponse);
    });

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

            XLSX.utils.book_append_sheet(
                workbook,
                detailSheet,
                'Frekuensi 1'
            );
        }

        const tanggal =
            $('#tanggalStatus').val()
                .replaceAll(' ', '_')
                .replaceAll('/', '-');

        XLSX.writeFile(
            workbook,
            `Distribusi Frekuensi Fuel Station_${tanggal}.xlsx`
        );
    });
</script>
