@include('layout.head', ['title' => 'List KLKH Fuel Station'])
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
                    <h4 class="mb-3 fw-semibold">KLKH Fuel Station</h4>

                    <!-- Kontainer baris horizontal -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">

                        <!-- Grup kiri: Datepicker + Tampilkan -->
                        <form action="" method="get">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                            <input type="text" id="range-datepicker" class="form-control"
                                style="width: 230px;" placeholder="" name="rangeDate">
                            <button type="submit" id="btn-tampilkan" class="btn btn-info">
                                <i class="bx bx-search"></i> Tampilkan
                            </button>
                        </div>
                        </form>

                        <!-- Grup tengah: DataTable buttons (ditempel lewat JS) -->
                        <div id="datatable-buttons-container" class="d-flex gap-2 flex-wrap"></div>

                        @if (in_array(Auth::user()->role, ['JUNIOR FOREMAN', 'FOREMAN', 'JUNIOR STAFF', 'STAFF', 'SUPERVISOR']))
                            <a href="{{ route('klkh.fuelStation.insert') }}" class="btn btn-primary">
                                <i class="bx bx-plus"></i> Isi KLKH Fuel Station
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table id="cbtn-selectors" class="table table-striped table-hover table-bordered nowrap">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Tgl Pembuatan</th>
                                <th colspan="3">PIC</th>
                                <th rowspan="2">PIT</th>
                                <th rowspan="2">Shift</th>
                                <th rowspan="2">Waktu</th>
                                <th colspan="3">Diketahui</th>
                                <th rowspan="2">Aksi</th>
                            </tr>
                            <tr>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Tanggal Verifikasi</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Tanggal Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fuelStation as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ date('d-m-Y H:i', strtotime($item->TANGGAL_PEMBUATAN)) }}</td>
                                <td>{{ $item->NIK_PIC }}</td>
                                <td>{{ $item->NAMA_PIC }}</td>
                                <td>{{ $item->VERIFIED_DATETIME_PENGAWAS != null ? date('d-m-Y H:i', strtotime($item->VERIFIED_DATETIME_PENGAWAS)) : '' }}</td>
                                <td>{{ $item->PIT }}</td>
                                <td>{{ $item->SHIFT }}</td>
                                <td>{{ date('d-m-Y', strtotime($item->DATE)) }}
                                    {{ date('H:i', strtotime($item->TIME)) }}</td>
                                <td>{{ $item->NIK_DIKETAHUI }}</td>
                                <td>{{ $item->NAMA_DIKETAHUI }}
                                    @if ($item->VERIFIED_DIKETAHUI != null)
                                        <span class="badge bg-success">T</span>
                                    @else
                                        <span class="badge bg-danger">B</span>
                                    @endif
                                </td>
                                <td>{{ $item->VERIFIED_DATETIME_DIKETAHUI != null ? date('d-m-Y H:i', strtotime($item->VERIFIED_DATETIME_DIKETAHUI)) : '' }}</td>
                                <td>
                                    <a href="{{ route('klkh.fuelStation.preview', $item->UUID) }}"
                                        class="btn btn-sm btn-info"><i class="bx bx-show"></i>
                                    </a>
                                    @if (Auth::user()->nik == $item->NIK_PIC)
                                        <a href="{{ route('klkh.fuelStation.edit', $item->UUID) }}" class="btn btn-sm btn-warning"><i class="bx bx-edit"></i>
                                        </a>
                                    @endif
                                    @if(
                                        in_array(Auth::user()->role, ['STAFF', 'PJS. SUPERINTENDENT', 'SUPERINTENDENT']) ||
                                        ($item->NIK_PIC == Auth::user()->nik && $item->VERIFIED_DIKETAHUI == null)
                                    )
                                        <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteKLKH{{ $item->ID }}">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                    @endif


                                </td>
                            </tr>
                            @include('klkh.fuelStation.modal.delete')
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <span class="badge bg-success">T</span> : Telah diverifikasi
                <br>
                <span class="badge bg-danger">B</span> : Belum diverifikasi
            </div>
        </div>

    </div>
</div>
@include('layout.footer')
<script>
    document.getElementById('range-datepicker').flatpickr({
        mode: "range"
    });
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const rangeDate = urlParams.get('rangeDate');

        const rangeInput = document.getElementById('range-datepicker');

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
    // [ HTML5 Export Buttons ]
    $('#basic-btn').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'print']
    });

    // [ Column Selectors ]
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

    // [ Excel - Cell Background ]
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

    // [ Custom File (JSON) ]
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
