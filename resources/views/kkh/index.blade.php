@include('layout.head', ['title' => 'List KKH'])
@include('layout.header')
@include('layout.theme_settings')
@include('layout.sidebar')
<style>
    @media (max-width: 767.98px) {
        .dt-buttons {
            display: none !important;
        }
    }

</style>
<div class="page-content">

    <!-- Start Container Fluid -->
    <div class="container-fluid">

        <!-- ========== Page Title Start ========== -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="mb-3 fw-semibold">Kesiapan Kerja Harian</h4>

                    <div class="col-12">
                        <div class="mb-3 row">
                            <div class="col-6 col-md-2 mb-2">
                                <label for="tanggalKKH">Tanggal</label>
                                <input type="text" id="tanggalKKH" class="form-control" name="tanggalKKH">
                            </div>
                            <div class="col-6 col-md-1 mb-2">
                                <label for="shift">Shift</label>
                                <select class="form-select" name="shift" id="shift">
                                    <option value="Semua">Semua</option>
                                    <option value="Pagi">Pagi</option>
                                    <option value="Malam">Malam</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <label for="shift">Nama</label>
                                <select class="form-control" data-choices name="name" id="choices-single-default">
                                    <option value="Semua">Semua</option>
                                    @foreach ($user as $us)
                                        <option value="{{ $us->nik }}">{{ $us->name }} ({{ $us->nik }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3 mb-2 d-flex align-items-end">
                                <button id="cariKKH" class="btn btn-primary w-100 me-2" style="padding-top:10px;padding-bottom:10px;">Tampilkan</button>
                                <button id="downloadpdfKKH" class="btn btn-secondary w-100" style="padding-top:10px;padding-bottom:10px;">Download PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
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

                    <div class="table-responsive">
                        <table id="dataKKH" class="table table-striped table-hover table-bordered nowrap">
                            <thead>
                                <tr>
                                            <th rowspan="2">Hari/Tanggal</th>
                                            <th rowspan="2">Jam Pulang</th>
                                            <th colspan="2">Pengisi</th>
                                            <th rowspan="2">Shift</th>
                                            <th colspan="3">Jam Tidur</th>
                                            <th rowspan="2">Jam Berangkat</th>
                                            <th rowspan="2">Fit Bekerja</th>
                                            <th rowspan="2">Keluhan</th>
                                            <th rowspan="2">Masalah Pribadi</th>
                                            <th colspan="2">Verifikasi Pengawas</th>
                                            <th rowspan="2">Aksi</th>
                                        </tr>
                                        <tr>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Mulai</th>
                                            <th>Bangun</th>
                                            <th>Total</th>
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

    </div>
</div>
@include('layout.footer')
<script>
    document.getElementById('tanggalKKH').flatpickr({
        mode: "range"
    });
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const rangeDate = urlParams.get('rangeDate');

        const rangeInput = document.getElementById('tanggalKKH');

        if (rangeDate) {
            rangeInput.value = rangeDate;
        } else {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const formatted = `${yyyy}-${mm}-${dd} to ${yyyy}-${mm}-${dd}`;
            rangeInput.placeholder = formatted;
        }
    });

    $('#basic-btn').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'print']
    });


    $('#cbtn-selectors').DataTable({
        dom: 'Bfrtip',
        buttons: [{
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [0, ':visible']
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'portrait',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                },
                customize: function (doc) {
                    doc.content[1].margin = [10, 10, 10, 10];
                }
            },
            'colvis'
        ]
    });


    $('#excel-bg').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            customize: function (xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                $('row c[r^="F"]', sheet).each(function () {
                    if ($('is t', this).text().replace(/[^\d]/g, '') * 1 >= 500000) {
                        $(this).attr('s', '20');
                    }
                });
            }
        }]
    });


    $('#pdf-json').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            text: 'JSON',
            action: function (e, dt, button, config) {
                var data = dt.buttons.exportData();
                $.fn.dataTable.fileSave(new Blob([JSON.stringify(data)]), 'Export.json');
            }
        }]
    });

</script>
<script>

    $('#downloadpdfKKH').click(function () {
        var tanggalKKH = $('#tanggalKKH').val();
        var name = $('#choices-single-default').val();
        var shift = $('#shift').val();

        if (name == 'Semua') {
            Swal.fire({
                title: 'Upps!',
                text: 'Silakan isi nama pengisi KKH terlebih dahulu!',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        const queryParams = $.param({
            tanggalKKH: tanggalKKH,
            name: name,
            shift: shift
        });

        const downloadUrl = "{{ route('kkh.downloadPDF') }}?" + queryParams;

        const a = document.createElement('a');
        a.href = downloadUrl;
        a.target = '_self';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });


    var table;
    $(document).ready(function() {
        var userRole = "{{ Auth::user()->role }}";
        table = $('#dataKKH').DataTable({


            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('kkh.all_api') }}',
                method: 'GET',
                data: function(d) {
                    var tanggalKKH = $('#tanggalKKH').val();
                    d.tanggalKKH = tanggalKKH;
                    var name = $('#choices-single-default').val();
                    d.name = name;
                    var shift = $('#shift').val();
                    d.shift = shift;
                    delete d.columns;
                    // delete d.search;
                    delete d.order;
                },
            },
            columns: [
                { data: 'TANGGAL_DIBUAT' },
                { data: 'JAM_PULANG' },
                { data: 'NIK_PENGISI' },
                { data: 'NAMA_PENGISI' },
                { data: 'SHIFT' },
                { data: 'JAM_TIDUR' },
                { data: 'JAM_BANGUN' },
                {
                    data: 'TOTAL_TIDUR',
                    render: function(data, type, row) {
                        if (data === null || data === '') return '-';

                        var nilai = parseFloat(data);
                        var teks = data + ' Jam';

                        if (!isNaN(nilai) && nilai < 6) {
                            return '<span style="color:red;">' + teks + '</span>';
                        }
                        return '<span style="color:green;">' + teks + '</span>';
                    }
                },
                { data: 'JAM_BERANGKAT' },
                {
                    data: 'FIT_BEKERJA',
                    render: function(data, type, row) {

                        if (data == 0 || data === null || data === '') {
                            return '<span style="color:red;">Perlu Verifikasi</span>';
                        }
                        return '<span style="color:green;">Ya</span>';
                    }
                },
                { data: 'KELUHAN' },
                { data: 'MASALAH_PRIBADI' },
                { data: 'NIK_PENGAWAS' },
                { data: 'NAMA_PENGAWAS' },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (!row) return '';
                        if ([
                            'JUNIOR FOREMAN', 'FOREMAN', 'JUNIOR STAFF', 'STAFF',
                            'SUPERVISOR', 'PJS. SUPERINTENDENT', 'SUPERINTENDENT', 'MANAGER'
                        ].includes(userRole) && row.ferivikasi_pengawas == false) {

                            let currentUserRole = userRole?.toUpperCase();
                            let jabatanPengawas = row.JABATAN?.toUpperCase();
                            let isFuelMan = ['FUELMAN', 'OPERATOR'].includes(jabatanPengawas?.toUpperCase());
                            let allowedToVerify = false;

                            // Cegah verifikasi diri sendiri
                            if (jabatanPengawas !== currentUserRole) {
                                // Kasus jika pengawas adalah Fuelman
                                if (isFuelMan) {
                                    allowedToVerify = [
                                        'JUNIOR FOREMAN', 'FOREMAN', 'JUNIOR STAFF', 'STAFF',
                                        'SUPERVISOR', 'PJS. SUPERINTENDENT', 'SUPERINTENDENT'
                                    ].includes(currentUserRole);
                                } else {
                                    // Aturan berjenjang berdasarkan peran pengawas
                                    switch (jabatanPengawas) {
                                        case 'JUNIOR FOREMAN':
                                        case 'JUNIOR STAFF':
                                            allowedToVerify = [
                                                'FOREMAN', 'STAFF', 'SUPERVISOR',
                                                'PJS. SUPERINTENDENT', 'SUPERINTENDENT'
                                            ].includes(currentUserRole);
                                            break;
                                        case 'FOREMAN':
                                        case 'STAFF':
                                            allowedToVerify = [
                                                'SUPERVISOR', 'PJS. SUPERINTENDENT', 'SUPERINTENDENT'
                                            ].includes(currentUserRole);
                                            break;
                                        case 'SUPERVISOR':
                                            allowedToVerify = ['PJS. SUPERINTENDENT', 'SUPERINTENDENT'].includes(currentUserRole);
                                            break;
                                        case 'PJS. SUPERINTENDENT':
                                            allowedToVerify = ['SUPERINTENDENT'].includes(currentUserRole);
                                            break;
                                        case 'SUPERINTENDENT':
                                            allowedToVerify = ['MANAGER'].includes(currentUserRole);
                                            break;
                                        default:
                                            allowedToVerify = [
                                                'JUNIOR FOREMAN', 'FOREMAN', 'JUNIOR STAFF', 'STAFF',
                                                'SUPERVISOR', 'PJS. SUPERINTENDENT', 'SUPERINTENDENT'
                                            ].includes(currentUserRole);
                                    }
                                }
                            }

                            if (allowedToVerify) {
                                let editUrl = "{{ route('kkh.verifikasi') }}" + "?rowID=" + encodeURIComponent(row.id);
                                return `
                                    <button class="btn btn-success btn-verifikasi" data-id="${row.id}">
                                        Verifikasi
                                    </button>
                                `;
                            }
                        }

                        return '';
                    }
                }

            ],
            "order": [[0, "asc"]],
            "pageLength": 25,
            "lengthMenu": [10, 15, 25, 50],
        });

        table.on('processing.dt', function (e, settings, processing) {

            if (processing) {
                $('#loadingOverlay').css('display', 'flex');
            } else {
                $('#loadingOverlay').hide();
            }

        });

        $('#cariKKH').click(function() {
            table.ajax.reload();
        });

        table.ajax.reload();
    });



    $(document).on('click', '.btn-verifikasi', function (e) {
        e.preventDefault();

        const rowID = $(this).data('id');

        // $.ajax({
        //     url: "{{ route('kkh.verifikasi') }}",
        //     method: 'POST',
        //     data: {
        //         _token: "{{ csrf_token() }}",
        //         rowID: rowID
        //     },
        //     success: function(response) {
        //         // Swal.fire('Terverifikasi!', 'Data berhasil diverifikasi.', 'success');

        //         // ✅ Refresh DataTables tanpa reload halaman
        //         table.ajax.reload(null, false);
        //     },
        //     error: function(xhr) {
        //         Swal.fire('Gagal', 'Terjadi kesalahan saat memverifikasi.', 'error');
        //     }
        // });

        Swal.fire({
            title: 'Verifikasi Data?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Verifikasi'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('kkh.verifikasi') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        rowID: rowID
                    },
                    success: function(response) {
                        Swal.fire('Terverifikasi!', 'Data berhasil diverifikasi.', 'success');

                        // ✅ Refresh DataTables tanpa reload halaman
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', 'Terjadi kesalahan saat memverifikasi.', 'error');
                    }
                });
            }
        });
    });
</script>
