@include('layout.head', ['title' => 'Durasi Refuelling Fuel Station'])
@include('layout.header')
@include('layout.theme_settings')
@include('layout.sidebar')
<style>

    /* =====================================================
       PAGE
    ===================================================== */

    .duration-page {
        width: 100%;
    }

    /* =====================================================
       LAYOUT CARD
    ===================================================== */

    .duration-layout {
        display: grid;
        grid-template-columns: minmax(600px, 1fr) minmax(600px, 1fr);
        gap: 20px;
        align-items: start;
    }

    /* =====================================================
       CARD
    ===================================================== */

    .duration-card {
        position: relative;

        background: #ffffff;

        border-radius: 14px;

        border: 1px solid #e5e7eb;

        box-shadow:
            0 8px 20px rgba(0, 0, 0, 0.06);

        overflow: hidden;

        padding: 0;
    }

    /*
     * GARIS WARNA DI ATAS CARD
     */
    .duration-card::before {
        content: "";

        position: absolute;

        top: 0;
        left: 0;
        right: 0;

        height: 3px;

        background: linear-gradient(
            90deg,
            #4f46e5 0%,
            #0ea5e9 100%
        );
    }

    .duration-card-body {
        padding: 18px 18px 14px 18px;
    }


    /* =====================================================
       TABLE
    ===================================================== */

    .duration-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .duration-table {
        width: 100%;

        border-collapse: collapse;

        table-layout: auto;

        font-size: 12px;

        margin: 0;
    }


    /* =====================================================
       TABLE HEADER
    ===================================================== */

    .duration-table th {
        background: #dbe5f1 !important;

        color: #000000 !important;

        font-weight: 600;

        text-align: center;

        vertical-align: middle;

        white-space: nowrap !important;

        border: 1px solid #dee2e6;

        padding: 6px 8px;

        height: 30px;
    }


    /* =====================================================
       TABLE BODY
    ===================================================== */

    .duration-table td {

        vertical-align: middle;

        white-space: nowrap !important;

        border: 1px solid #e5e7eb;

        padding: 4px 7px;

        height: 23px;

        color: #111827;

        background: #ffffff;
    }


    /* =====================================================
       HOVER
    ===================================================== */

    .duration-table tbody tr:hover td {

        background: #f8f9fa;

    }


    /* =====================================================
       JAM
    ===================================================== */

    .duration-table .hour-cell {

        width: 75px !important;

        min-width: 75px !important;

        max-width: 75px !important;

        text-align: center;

        font-weight: 600;

        white-space: nowrap !important;
    }


    /* =====================================================
       UNIT
    ===================================================== */

    .duration-table .unit-cell {

        width: 135px !important;

        min-width: 135px !important;

        max-width: 150px !important;

        text-align: left;

        white-space: nowrap !important;
    }


    /* =====================================================
       FUEL STATION
    ===================================================== */

    .duration-table .station-header {

        min-width: 125px !important;

        width: 125px !important;

        white-space: nowrap !important;

        text-align: center;
    }


    /* =====================================================
       TOTAL / AVG
    ===================================================== */

    .duration-table .avg-col {

        width: 75px !important;

        min-width: 75px !important;

        max-width: 75px !important;

        text-align: center;

        white-space: nowrap !important;
    }


    /* =====================================================
       TOTAL ROW
    ===================================================== */

    .duration-table .total-row td {

        background: #fff2cc !important;

        font-weight: 700;

        white-space: nowrap !important;

    }


    /* =====================================================
       GROUP TITLE
    ===================================================== */

    .duration-section-title {

        font-size: 14px;

        font-weight: 600;

        color: #111827;

        margin-bottom: 8px;

    }


    /* =====================================================
       RIGHT TABLE
    ===================================================== */

    .duration-right-table {

        width: 100%;

        overflow-x: auto;
    }

    .duration-right-table .duration-table {

        min-width: 780px;

    }


    /* =====================================================
       FORM
    ===================================================== */

    .filter-card {

        background: #ffffff;

        border: 1px solid #e5e7eb;

        border-radius: 12px;

        padding: 15px;

        margin-bottom: 20px;

        box-shadow:
            0 4px 14px rgba(0, 0, 0, 0.04);
    }


    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media (max-width: 1400px) {

        .duration-layout {

            grid-template-columns:
                minmax(550px, 1fr)
                minmax(550px, 1fr);

        }

    }


    @media (max-width: 1100px) {

        .duration-layout {

            grid-template-columns: 1fr;

        }

    }


    @media (max-width: 767.98px) {

        .duration-card {

            border-radius: 10px;

        }

        .duration-card-body {

            padding: 12px;

        }

        .duration-table {

            min-width: 700px;

        }

        .duration-right-table {

            overflow-x: auto;

        }

    }

</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="fw-semibold">Distribusi Refueling Fuel Station</h4>

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
                            <div class="col-6 col-md-1 mb-2 d-flex align-items-end">
                                <button id="cariDurasi" class="btn btn-primary w-100 me-2" style="padding-top:10px;padding-bottom:10px;">Tampilkan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="loadingOverlayDuration">
            <div class="duration-loading-box">
                <div class="spinner-border text-primary mb-2"></div>
                <div>Mohon tunggu sebentar...</div>
            </div>
        </div>

        <div class="duration-layout">
            <div class="left-duration-panel">
                <div class="duration-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="duration-table">
                                <thead id="durationLeftHeader"></thead>
                                <tbody id="durationLeftBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-duration-panel">
                <div class="right-section">
                    <div class="section-title">All</div>
                    <div class="duration-card">
                        <div class="card-body">
                            <div class="right-table-wrap">
                                <table class="duration-table">
                                    <thead id="durationAllHeader"></thead>
                                    <tbody id="durationAllBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="durationStationTables"></div>
            </div>
        </div>
    </div>
</div>

@include('layout.footer')

<script>
    document.getElementById('tanggalStatus').flatpickr({ mode:'range', dateFormat:'Y-m-d' });

    document.addEventListener('DOMContentLoaded', function () {
        const rangeDate = new URLSearchParams(window.location.search).get('rangeDate');
        const input = document.getElementById('tanggalStatus');
        if (rangeDate) input.value = rangeDate;
        else {
            const d = new Date();
            input.placeholder = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')} to ${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        }
    });

    function durationNumber(value) { return Number(value || 0).toFixed(1); }

    function loadDuration() {
        $('#loadingOverlayDuration').css('display','flex');
        $.ajax({
            url: "{{ route('distribusiFrekuensiFuelStation.durasi.api') }}",
            type:'GET',
            data:{ tanggalStatus:$('#tanggalStatus').val(), shift:$('#shift').val() },
            success:function(res){
                buildLeftDurationTable(res);
                buildAllDurationTable(res);
                buildStationDurationTables(res);
            },
            error:function(xhr){ console.error(xhr); alert('Gagal mengambil data durasi refuelling Fuel Station.'); },
            complete:function(){ $('#loadingOverlayDuration').hide(); }
        });
    }

    // Kiri: Jam | Status(Unit) | FS SM-B1 | FS SM-B2 | Total
    function buildLeftDurationTable(res) {
        let header = `<tr><th rowspan="2" class="hour-cell">Jam</th><th rowspan="2" class="unit-cell">Status</th><th colspan="${res.fuelStations.length}">Durasi rata-rata</th><th rowspan="2" class="avg-col">Total</th></tr><tr>`;
        res.fuelStations.forEach(s => header += `<th>${s}</th>`);
        header += `</tr>`;
        $('#durationLeftHeader').html(header);

        let html = '';
        res.hours.forEach(hour => {
            res.units.forEach((unit,index) => {
                html += '<tr>';
                if(index === 0) html += `<td rowspan="${res.units.length+1}" class="hour-cell">${hour}</td>`;
                html += `<td class="unit-cell">${unit}</td>`;
                let rowTotal = 0;
                res.fuelStations.forEach(station => {
                    const value = Number(res.averageDuration?.[unit]?.[hour]?.[station] || 0);
                    rowTotal += value;
                    html += `<td class="text-center">${durationNumber(value)}</td>`;
                });
                html += `<td class="text-center fw-bold">${durationNumber(rowTotal)}</td></tr>`;
            });
            html += `<tr class="total-row"><td class="unit-cell">Total</td>`;
            let totalHour = 0;
            res.fuelStations.forEach(station => {
                let stationTotal = 0;
                res.units.forEach(unit => stationTotal += Number(res.averageDuration?.[unit]?.[hour]?.[station] || 0));
                totalHour += stationTotal;
                html += `<td class="text-center">${durationNumber(stationTotal)}</td>`;
            });
            html += `<td class="text-center">${durationNumber(totalHour)}</td></tr>`;
        });
        $('#durationLeftBody').html(html);
    }

    // Kanan: ALL
    function buildAllDurationTable(res) {
        let header = `<tr><th rowspan="2" class="unit-cell">Unit</th><th colspan="${res.hours.length}">Durasi rata-rata</th><th rowspan="2" class="avg-col">Avg</th></tr><tr>`;
        res.hours.forEach(h => header += `<th>${h}</th>`);
        header += '</tr>';
        $('#durationAllHeader').html(header);

        let html = '';
        res.units.forEach(unit => {
            html += `<tr><td class="unit-cell">${unit}</td>`;
            res.hours.forEach(hour => {
                let totalDuration=0, totalFrequency=0;
                res.fuelStations.forEach(station => {
                    totalDuration += Number(res.durationPivot?.[hour]?.[unit]?.[station] || 0);
                    totalFrequency += Number(res.frequencyPivot?.[hour]?.[unit]?.[station] || 0);
                });
                const avg = totalFrequency > 0 ? totalDuration/totalFrequency : 0;
                html += `<td class="text-center">${durationNumber(avg)}</td>`;
            });
            html += `<td class="text-center avg-col">${durationNumber(res.averageDurationTotal?.[unit] || 0)}</td></tr>`;
        });
        $('#durationAllBody').html(html);
    }

    // Kanan: masing-masing lokasi Fuel Station
    function buildStationDurationTables(res) {
        const container = $('#durationStationTables');
        container.empty();

        res.fuelStations.forEach(station => {
            let header = `<tr><th rowspan="2" class="unit-cell">Unit</th><th colspan="${res.hours.length}">Durasi rata-rata</th><th rowspan="2" class="avg-col">Avg</th></tr><tr>`;
            res.hours.forEach(h => header += `<th>${h}</th>`);
            header += '</tr>';

            let body = '';
            res.units.forEach(unit => {
                body += `<tr><td class="unit-cell">${unit}</td>`;
                res.hours.forEach(hour => {
                    const duration = Number(res.durationPivot?.[hour]?.[unit]?.[station] || 0);
                    const frequency = Number(res.frequencyPivot?.[hour]?.[unit]?.[station] || 0);
                    const avg = frequency > 0 ? duration/frequency : 0;
                    body += `<td class="text-center">${durationNumber(avg)}</td>`;
                });

                let totalDuration=0, totalFrequency=0;
                res.hours.forEach(hour => {
                    totalDuration += Number(res.durationPivot?.[hour]?.[unit]?.[station] || 0);
                    totalFrequency += Number(res.frequencyPivot?.[hour]?.[unit]?.[station] || 0);
                });
                const stationAvg = totalFrequency > 0 ? totalDuration/totalFrequency : 0;
                body += `<td class="text-center avg-col">${durationNumber(stationAvg)}</td></tr>`;
            });

            container.append(`<div class="right-section"><div class="section-title">${station}</div><div class="duration-card"><div class="card-body"><div class="right-table-wrap"><table class="duration-table"><thead>${header}</thead><tbody>${body}</tbody></table></div></div></div></div>`);
        });
    }

    $(document).ready(function(){
        loadDuration();
        $('#cariDurasi').on('click', loadDuration);
    });
</script>
