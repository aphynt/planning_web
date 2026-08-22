@include('layout.head', ['title' => 'Overspeed'])
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

    #tblFrequency th,
    #tblDuration th {
        background: #fcfbfb;
        color: #000000;
        white-space: nowrap;
        font-weight: 600;
    }


    #tblFrequency td,
    #tblDuration td {
        vertical-align: middle;
    }

    #tblFrequency tbody tr:hover,
    #tblDuration tbody tr:hover {
        background: #f8f9fa;
    }

    #tblFrequency .unit-cell,
    #tblDuration .unit-cell {
        text-align: left !important;
        white-space: nowrap;
        font-weight: 600;
        min-width: 90px;
    }

    #tblFrequency .hour-cell,
    #tblDuration .hour-cell {
        vertical-align: middle !important;
        text-align: center !important;
        white-space: nowrap;
        font-weight: 500;
        background: #f8f9fa;
        min-width: 70px;
    }

    #tblFrequency .total-cell,
    #tblDuration .total-cell {
        vertical-align: middle !important;
        text-align: center !important;
        white-space: nowrap;
        font-weight: 600;
        min-width: 75px;
    }
    #tblFrequency .total-row td,
    #tblDuration .total-row td {
        background: #fff2cc !important;
        font-weight: 600;
    }
    .frequency-card,
    .duration-card {
        overflow-x: auto;
    }

    #tblFrequency .report-title,
    #tblDuration .report-title {
        text-align: center;
        font-weight: 600;
    }

</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="fw-semibold">Overspeed</h4>
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
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <div id="loadingOverlay">
                            <div class="loading-box">
                                <div class="spinner-border text-primary"></div>
                                <small class="text-muted">Mohon tunggu sebentar</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="dataStatus" class="table table-striped table-hover table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Unit ID</th>
                                        <th rowspan="2">Waktu Kejadian</th>
                                        <th colspan="2">Speed</th>
                                        <th rowspan="2">Lokasi</th>
                                        <th rowspan="2">Longitude</th>
                                        <th rowspan="2">Latitude</th>
                                        <th rowspan="2">Altitude</th>
                                        <th colspan="2">Operator</th>
                                    </tr>
                                    <tr>
                                        <th>Actual</th>
                                        <th>Limit</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive frequency-card">
                                    <table id="tblFrequency" class="table table-bordered table-sm align-middle text-center report-table w-100" >
                                        <thead id="tblFrequencyHeader"></thead>
                                        <tbody id="tblFrequencyBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive duration-card">
                                    <table id="tblDuration" class="table table-bordered table-sm align-middle text-center report-table w-100">
                                        <thead id="tblDurationHeader"></thead>
                                        <tbody id="tblDurationBody"></tbody>
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
        const urlParams = new URLSearchParams(
            window.location.search
        );
        const rangeDate = urlParams.get('rangeDate');
        const rangeInput = document.getElementById('tanggalStatus');
        if (rangeDate) {
            rangeInput.value = rangeDate;
        } else {
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

            rangeInput.placeholder = `${yyyy}-${mm}-${dd} to ${yyyy}-${mm}-${dd}`;
        }

    });

    var table;
    $(document).ready(function() {
        var userRole = "{{ Auth::user()->role }}";
        table = $('#dataStatus').DataTable({
            dom:
                "<'row align-items-center mb-3'<'col-md-6'B><'col-md-6 d-flex justify-content-end'f>>" +
                "rt" +
                "<'row align-items-center mt-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end'p>>",

            buttons: [
                'copy',
                'excel',
                'pdf',
                'colvis'
            ],

            processing: true,
            serverSide: true,

            ajax: {
                url: '{{ route('overspeed.api') }}',
                method: 'GET',

                beforeSend: function () {
                    $('#loadingOverlay').css('display', 'flex');
                },

                complete: function () {
                    $('#loadingOverlay').hide();
                },

                error: function (xhr, error, thrown) {
                    console.error('AJAX ERROR');
                    console.error('Status:', xhr.status);
                    console.error('Error:', error);
                    console.error('Thrown:', thrown);
                    console.error('Response:', xhr.responseText);
                },
                data: function(d) {
                    d.tanggalStatus = $('#tanggalStatus').val();
                    d.shift = $('#shift').val();
                    delete d.columns;
                    delete d.order;
                },

                dataSrc: function(json) {
                    if (json.frequency) {
                        loadFrequencyTable(json.frequency);
                    }
                    if (json.duration) {
                        loadDurationTable(json.duration);
                    }
                    return json.data || [];
                }
            },
            columns: [
                {
                    data: 'ID'
                },
                {
                    data: 'VHC_ID'
                },
                {
                    data: 'OPR_REPORTTIME',
                    render: function(data) {
                        if (!data) {
                            return '-';
                        }
                        return data.substring(
                            0,
                            19
                        );
                    }
                },
                {
                    data: 'VHC_SPEED',
                    render: function(data) {
                        return parseFloat(
                            data || 0
                        ).toFixed(1);
                    }
                },
                {
                    data: 'VHC_REFMAXSPEED'
                },
                {
                    data: 'LOC_NAME',
                    defaultContent: ''
                },
                {
                    data: 'GPS_LON',
                    render: function(data) {
                        if (data == null || data === '') return '';
                        return Number(data).toFixed(6);
                    }
                },
                {
                    data: 'GPS_LAT',
                    render: function(data) {
                        if (data == null || data === '') return '';
                        return Number(data).toFixed(6);
                    }
                },
                {
                    data: 'GPS_ALT',
                    render: function(data) {
                        if (data == null || data === '') return '';
                        return Number(data).toFixed(2);
                    }
                },
                {
                    data: 'OPR_NRP',
                    defaultContent: ''
                },
                {
                    data: 'OPR_NAME',
                    defaultContent: ''
                },

            ],

            order: [
                [0, 'asc']
            ],
            pageLength: 25,
            lengthMenu: [
                10,
                15,
                25,
                50
            ]

        });
        $('#cariStatus').click(function() {
            table.ajax.reload();
        });

    });
    function formatHourRange(hour) {
        let nextHour = parseInt(hour) + 1;
        if (nextHour >= 24) {
            nextHour = 0;
        }
        return String(hour).padStart(2, '0')
            + '-'
            + String(nextHour).padStart(2, '0');

    }
    function loadFrequencyTable(frequency) {
        if (!frequency) {
            return;
        }
        let hours = frequency.hours || [];
        let rows = frequency.rows || [];
        let total = frequency.total || { total: 0 };
        let header = `
            <tr>

                <th rowspan="2">Unit</th>
                <th colspan="${hours.length}" class="report-title">Frekuensi Overspeed</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
        `;
        hours.forEach(function(hour) {
            header += `
                <th>
                    ${formatHourRange(hour)}
                </th>
            `;
        });
        header += `
            </tr>

        `;
        $('#tblFrequencyHeader').html(header);
        let body = '';
        rows.forEach(function(row) {
            body += `
                <tr>
                    <td class="text-start">
                        ${row.unit}
                    </td>
            `;
            hours.forEach(function(hour) {
                body += `
                    <td>
                        ${row['hour_' + hour] ?? 0}
                    </td>
                `;
            });
            body += `
                    <td>
                        <strong>
                            ${row.total ?? 0}
                        </strong>
                    </td>
                </tr>
            `;
        });

        body += `
            <tr class="total-row">
                <td class="text-start">
                    Total
                </td>
        `;
        hours.forEach(function(hour) {
            body += `
                <td>
                    ${total['hour_' + hour] ?? 0}
                </td>
            `;
        });
        body += `
                <td>
                    ${total.total ?? 0}
                </td>
            </tr>
        `;
        $('#tblFrequencyBody').html(body);
    }

    function loadDurationTable(duration) {
        if (!duration) {
            return;
        }
        let hours = duration.hours || [];
        let rows = duration.rows || [];
        let total =
            duration.total || {
                total: 0
            };
        let header = `
            <tr>
                <th rowspan="2">
                    Unit
                </th>
                <th
                    colspan="${hours.length}"
                    class="report-title"
                >
                    Durasi Overspeed (menit)
                </th>
                <th rowspan="2">
                    Total
                </th>
            </tr>
            <tr>
        `;

        hours.forEach(function(hour) {
            header += `
                <th>
                    ${formatHourRange(hour)}
                </th>
            `;
        });

        header += `
            </tr>
        `;


        $('#tblDurationHeader').html(header);

        let body = '';
        rows.forEach(function(row) {
            body += `
                <tr>
                    <td class="text-start">
                        ${row.unit}
                    </td>
            `;

            hours.forEach(function(hour) {
                let value =
                    parseFloat(
                        row['hour_' + hour] || 0
                    );

                body += `
                    <td>
                        ${value.toFixed(2)}
                    </td>
                `;
            });

            let totalValue =
                parseFloat(
                    row.total || 0
                );
            body += `
                    <td>
                        <strong>
                            ${totalValue.toFixed(2)}
                        </strong>
                    </td>
                </tr>
            `;
        });

        body += `
            <tr class="total-row">
                <td class="text-start">
                    Total
                </td>
        `;


        hours.forEach(function(hour) {
            let value =
                parseFloat(
                    total['hour_' + hour] || 0
                );

            body += `
                <td>
                    ${value.toFixed(2)}
                </td>
            `;
        });


        let grandTotal =
            parseFloat(
                total.total || 0
            );

        body += `
                <td>
                    ${grandTotal.toFixed(2)}
                </td>
            </tr>
        `;


        $('#tblDurationBody').html(body);
    }

    $('#exportAllExcel').on('click', function () {
        const workbook = XLSX.utils.book_new();
        const detailTable = document.getElementById('dataStatus');
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
                'Detail Overspeed'
            );
        }
        const frequencyTable = document.getElementById('tblFrequency');
        if (frequencyTable) {
            const frequencySheet =
                XLSX.utils.table_to_sheet(
                    frequencyTable,
                    {
                        raw: true
                    }
                );

            XLSX.utils.book_append_sheet(
                workbook,
                frequencySheet,
                'Frekuensi'
            );
        }

        const durationTable = document.getElementById('tblDuration');
        if (durationTable) {
            const durationSheet =
                XLSX.utils.table_to_sheet(
                    durationTable,
                    {
                        raw: true
                    }
                );

            XLSX.utils.book_append_sheet(
                workbook,
                durationSheet,
                'Durasi'
            );
        }

        const tanggal =
            $('#tanggalStatus').val()
                .replaceAll(' ', '_')
                .replaceAll('/', '-');

        XLSX.writeFile(
            workbook,
            `Overspeed_${tanggal}.xlsx`
        );
    });

</script>
