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
                            <div class="col-6 col-md-1 mb-2 d-flex align-items-end">
                                <button id="cariStatus" class="btn btn-primary w-100 me-2" style="padding-top:10px;padding-bottom:10px;">Tampilkan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
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

            {{-- RIGHT: All + masing-masing Fuel Station --}}
            <div class="col-md-6">
<div class="row">
                    <div class="col-12">
                        <div class="card">

                <div id="fuelStationTables">

                    {{-- Diisi melalui JavaScript --}}

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
    /*
    |--------------------------------------------------------------------------
    | Flatpickr
    |--------------------------------------------------------------------------
    */
    document.getElementById('tanggalStatus').flatpickr({
        mode: 'range',
        dateFormat: 'Y-m-d'
    });

    /*
    |--------------------------------------------------------------------------
    | Set tanggal dari query string
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */
    function escapeHtml(value) {

        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /*
    |--------------------------------------------------------------------------
    | Status class
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | LEFT TABLE
    |
    | Jam | Status | FS SM-B1 | FS SM-B2 | Total
    |--------------------------------------------------------------------------
    */
    function buildTable(res) {

        let header = `
            <tr>
                <th rowspan="2" class="text-center align-middle">
                    Jam
                </th>

                <th rowspan="2" class="text-center align-middle">
                    Status
                </th>

                <th
                    colspan="${res.fuelStations.length}"
                    class="text-center"
                >
                    Frekuensi
                </th>

                <th rowspan="2" class="text-center align-middle">
                    Total
                </th>
            </tr>

            <tr>
        `;

        res.fuelStations.forEach(function (fuelStation) {

            header += `
                <th class="text-center">
                    ${fuelStation}
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

                html += '<tr>';

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
                            ${value}
                        </td>
                    `;
                });

                html += `
                    <td class="text-center fw-bold">
                        ${rowTotal}
                    </td>
                `;

                html += '</tr>';
            });

            /*
            |--------------------------------------------------------------------------
            | Total per jam
            |--------------------------------------------------------------------------
            */
            html += `
                <tr class="total-row">
                    <td colspan="2" class="text-start">
                        Total
                    </td>
            `;

            let totalHour = 0;

            res.fuelStations.forEach(function (fuelStation) {

                const value = Number(
                    res.totalsByHour?.[hour]?.[fuelStation] || 0
                );

                totalHour += value;

                html += `
                    <td class="text-center">
                        ${value}
                    </td>
                `;
            });

            html += `
                    <td class="text-center">
                        ${totalHour}
                    </td>
                </tr>
            `;
        });

        $('#tblBody').html(html);
    }

    /*
    |--------------------------------------------------------------------------
    | RIGHT TABLE
    |
    | All
    |   Unit | 07-08 | ... | Total
    |
    | FS SM-B1
    |   Unit | 07-08 | ... | Total
    |
    | FS SM-B2
    |   Unit | 07-08 | ... | Total
    |--------------------------------------------------------------------------
    */
    function buildFrequencyTable(
        res,
        title,
        fuelStation = null
    ) {

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
                                    <th rowspan="2" class="text-center align-middle">
                                        Unit
                                    </th>

                                    <th
                                        colspan="${res.hours.length}"
                                        class="text-center"
                                    >
                                        Frekuensi
                                    </th>

                                    <th rowspan="2" class="text-center align-middle">
                                        Total
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

        /*
        |--------------------------------------------------------------------------
        | Row unit
        |--------------------------------------------------------------------------
        */
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

                    /*
                    |--------------------------------------------------------------------------
                    | ALL
                    |--------------------------------------------------------------------------
                    */
                    res.fuelStations.forEach(function (station) {

                        value += Number(
                            res.pivot?.[hour]?.[unit]?.[station] || 0
                        );
                    });

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | SATU FUEL STATION
                    |--------------------------------------------------------------------------
                    */
                    value = Number(
                        res.pivot?.[hour]?.[unit]?.[fuelStation] || 0
                    );
                }

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

        /*
        |--------------------------------------------------------------------------
        | Total
        |--------------------------------------------------------------------------
        */
        html += `
            <tr class="total-row">
                <td class="text-start">
                    Total
                </td>
        `;

        let grandTotal = 0;

        res.hours.forEach(function (hour) {

            let total = 0;

            if (fuelStation === null) {

                res.fuelStations.forEach(function (station) {

                    res.units.forEach(function (unit) {

                        total += Number(
                            res.pivot?.[hour]?.[unit]?.[station] || 0
                        );
                    });
                });

            } else {

                res.units.forEach(function (unit) {

                    total += Number(
                        res.pivot?.[hour]?.[unit]?.[fuelStation] || 0
                    );
                });
            }

            grandTotal += total;

            html += `
                <td class="text-center">
                    ${total}
                </td>
            `;
        });

        html += `
                <td class="text-center">
                    ${grandTotal}
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

    /*
    |--------------------------------------------------------------------------
    | Build semua tabel kanan
    |--------------------------------------------------------------------------
    */
    function buildFuelStationTables(res) {

        const container = $('#fuelStationTables');

        container.empty();

        /*
        |--------------------------------------------------------------------------
        | 1. ALL
        |--------------------------------------------------------------------------
        */
        container.append(
            buildFrequencyTable(
                res,
                'All',
                null
            )
        );

        /*
        |--------------------------------------------------------------------------
        | 2. MASING-MASING FUEL STATION
        |--------------------------------------------------------------------------
        */
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

    /*
    |--------------------------------------------------------------------------
    | Load data
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Document ready
    |--------------------------------------------------------------------------
    */
    $(document).ready(function () {

        loadFrequency();

        $('#cariStatus').click(function () {

            loadFrequency();
        });
    });
</script>
