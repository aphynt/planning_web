@include('layout.head', ['title' => 'Status & Activity'])
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
                    <h4 class="fw-semibold">Status & Activity</h4>

                    <div class="col-12">
                        <div class="row">
                            <div class="col-6 col-md-2 mb-2">
                                <label for="tanggalStatus">Tanggal</label>
                                <input type="text" id="tanggalStatus" class="form-control" name="tanggalStatus">
                            </div>
                            <div class="col-6 col-md-1 mb-2">
                                <label for="shift">Shift</label>
                                <select class="form-select" name="shift" id="shift">
                                    <option value="Semua">Semua</option>
                                    <option value="6" selected>Siang</option>
                                    <option value="7">Malam</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2 mb-2">
                                <label for="shift">Unit ID</label>
                                <select class="form-control" data-choices name="vhc_id" id="choices-single-default">
                                    <option value="Semua">Semua</option>
                                    @foreach ($vehicle as $vhc)
                                        <option value="{{ $vhc->VHC_ID }}">{{ $vhc->VHC_ID }}</option>
                                    @endforeach
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
                                    <th>Time Start</th>
                                    <th>Time End</th>
                                    <th>Unit ID</th>
                                    <th>Shift</th>
                                    <th>Status & Activity</th>
                                    <th>Moving (minutes)</th>
                                    <th>Stopped (minutes)</th>
                                    <th>Engine Off (minutes)</th>
                                    <th>Duration (minutes)</th>
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
    flatpickr("#tanggalStatus", {
    dateFormat: "Y-m-d",
    defaultDate: "today"
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
                url: '{{ route('statusActivity.api') }}',
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
                {
                    data: 'OPR_REPORTTIME',
                    render: function (data) {
                        if (!data) return '-';
                        return data.substring(0, 19);
                    }
                },
                {
                    data: 'OPR_ENDTIME',
                    render: function (data) {
                        if (!data) return '-';
                        return data.substring(0, 19);
                    }
                },
                { data: 'VHC_ID' },
                { data: 'SHIFTDESC' },
                { data: 'STATUSACTIVITYDESC' },
                { data: 'ENG_TRAVEL',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'ENG_STOPPED',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'ENG_OFF',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                },
                { data: 'DURATION',
                    render: function (data) {
                        return parseFloat(data || 0).toFixed(1);
                    }
                }

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
