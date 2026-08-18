@include('layout.head', ['title' => 'Refueling Monitoring'])
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
                    <h4 class="mb-3 fw-semibold">Refueling Monitoring</h4>

                    <div class="col-12">
                        <div class="mb-3 row">
                            <div class="col-6 col-md-1 mb-2 d-flex align-items-end">
                                <button id="cariStatus" class="btn btn-primary w-100 me-2" style="padding-top:10px;padding-bottom:10px;">Refresh</button>
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
                        <table id="dataStatus" class="table table-striped table-hover table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Unit</th>
                                    <th><></th>
                                    <th>Last Connected</th>
                                    <th>Type</th>
                                    <th>Last Refueling (liter)</th>
                                    <th>Last HM</th>
                                    <th>Last Fuel Burn (liter /h)</th>
                                    <th>Actual HM</th>
                                    <th>Est Moving Time Controller (h)</th>
                                    <th>Est Moving Time GPS (h)</th>
                                    <th>Est Fuel Usage (liter)</th>
                                    <th>Est Fuel Remain (liter)</th>
                                    <th>Est Fuel Level (%)</th>
                                    <th>Assignment</th>
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
                url: '{{ route('refuelingMonitoring.api') }}',
                beforeSend: function () {
                    $('#loadingOverlay').css('display', 'flex');
                },

                complete: function () {
                    $('#loadingOverlay').hide();
                },
                method: 'GET',
                data: function(d) {
                    var tanggalStatus = $('#tanggalStatus').val();
                    d.tanggalStatus = tanggalStatus;
                    var vhc_id = $('#choices-single-default').val();
                    d.vhc_id = vhc_id;
                    var shift = $('#shift').val();
                    d.shift = shift;
                    delete d.columns;
                    // delete d.search;
                    delete d.order;
                },
            },
            columns: [
                { data: 'VHC_ID' },
                {
                    data: 'STATUS',
                    className: 'text-center',
                    render: function(data) {

                        if (data == 0) {
                            return '<span class="badge bg-danger">0</span>';
                        }

                        return '<span class="badge bg-success">1</span>';
                    }
                },
                { data: 'LASTCONNECTED' },
                { data: 'EQU_TYPEID' },
                { data: 'LAST_FUELVOLUME' },
                { data: 'LAST_FUELHM',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'LAST_FUELRATE',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'ACT_HM',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'EST_MOVINGTIME',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'EST_MOVINGTIMEGPS',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'EST_FUELUSAGE',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'EST_FUELREMAINING',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'EST_FUELLEVEL',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'PSG_LOADERID' }

            ],
            "order": [[0, "asc"]],
            "pageLength": 25,
            "lengthMenu": [10, 15, 25, 50],
        });


        $('#cariStatus').click(function() {
            table.ajax.reload();
        });
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
