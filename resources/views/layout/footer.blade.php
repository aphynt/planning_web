<style>

.modern-footer {
    width: 100%;

    padding: 14px 0;

    background: rgba(255, 255, 255, 0.88);

    border-top: 1px solid rgba(15, 23, 42, 0.06);

    box-shadow:
        0 -4px 18px rgba(15, 23, 42, 0.025);

    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.modern-footer-content {
    min-height: 34px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    color: #94a3b8;

    font-size: 11px;
}

.modern-footer-left {
    display: flex;
    align-items: center;
    gap: 8px;

    white-space: nowrap;
}

.modern-footer-dot {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #2563eb;

    box-shadow:
        0 0 0 4px rgba(37, 99, 235, 0.08);
}

.modern-footer-right {
    display: flex;
    align-items: center;

    gap: 8px;

    white-space: nowrap;
}

.modern-footer-divider {
    width: 1px;
    height: 16px;

    margin-right: 5px;

    background: #e2e8f0;
}

.modern-footer-text {
    color: #94a3b8;
}

.modern-footer-brand {
    display: inline-flex;
    align-items: center;
    gap: 5px;

    color: #475569;

    font-weight: 700;

    text-decoration: none !important;

    transition:
        color .2s ease,
        transform .2s ease;
}

.modern-footer-brand:hover {
    color: #2563eb;

    transform: translateY(-1px);
}

.modern-footer-brand-icon {
    width: 22px;
    height: 22px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 6px;

    background: #eff6ff;

    color: #2563eb;

    font-size: 12px;
}

.modern-footer-version {
    padding: 3px 7px;

    border-radius: 6px;

    background: #f8fafc;
    border: 1px solid #e2e8f0;

    color: #94a3b8;

    font-size: 9px;
    font-weight: 600;
}

[data-bs-theme="dark"] .modern-footer,
.dark-mode .modern-footer {
    background: rgba(15, 23, 42, 0.92);

    border-top-color: rgba(255, 255, 255, 0.06);

    box-shadow:
        0 -4px 18px rgba(0, 0, 0, 0.08);
}

[data-bs-theme="dark"] .modern-footer-brand,
.dark-mode .modern-footer-brand {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .modern-footer-brand:hover,
.dark-mode .modern-footer-brand:hover {
    color: #60a5fa;
}

[data-bs-theme="dark"] .modern-footer-brand-icon,
.dark-mode .modern-footer-brand-icon {
    background: rgba(37, 99, 235, 0.12);
    color: #60a5fa;
}

[data-bs-theme="dark"] .modern-footer-divider,
.dark-mode .modern-footer-divider {
    background: rgba(255, 255, 255, 0.08);
}

[data-bs-theme="dark"] .modern-footer-version,
.dark-mode .modern-footer-version {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.08);
}

@media (max-width: 767.98px) {

    .modern-footer {
        padding: 12px 0;
    }

    .modern-footer-content {
        justify-content: center;

        flex-direction: column;

        gap: 6px;

        text-align: center;
    }

    .modern-footer-right {
        gap: 6px;
    }

    .modern-footer-divider {
        display: none;
    }

    .modern-footer-version {
        display: none;
    }
}
</style>
<footer class="modern-footer">
    <div class="container-fluid">
        <div class="modern-footer-content">

            <!-- Left -->
            <div class="modern-footer-left">
                <span class="modern-footer-dot"></span>

                <span>
                    &copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script>
                    {{ config('app.name') }}
                </span>
            </div>

            <!-- Right -->
            <div class="modern-footer-right">
                <span class="modern-footer-divider"></span>

                <span class="modern-footer-text">
                    Developed by
                </span>

                <a href="https://ahmadfadillah.my.id"
                    class="modern-footer-brand"
                    target="_blank"
                    rel="noopener noreferrer">

                    <span class="modern-footer-brand-icon">
                        <i class="ri-code-s-slash-line"></i>
                    </span>

                    IT-SIMS
                </a>

                <span class="modern-footer-version">
                    v1.4
                </span>
            </div>

        </div>
    </div>
</footer>

</div>

<!-- Vendor Javascript (Require in all Page) -->
<script src="{{ asset('app') }}/assets/js/vendor.js"></script>

<!-- App Javascript (Require in all Page) -->
<script src="{{ asset('app') }}/assets/js/app.js"></script>

<!-- Gridjs Plugin js -->
{{-- <script src="{{ asset('app') }}/assets/vendor/gridjs/gridjs.umd.js"></script> --}}

<script src="{{ asset('app') }}/assets/cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('js/xlsx.full.min.js') }}"></script>
<script src="{{ asset('app') }}/assets/js/plugins/dataTables.min.js"></script>
<script src="{{ asset('app') }}/assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('app') }}/assets/js/plugins/buttons.colVis.min.js"></script>
<script src="{{ asset('app') }}/assets/js/plugins/buttons.print.min.js"></script>
<script src="{{ asset('app') }}/assets/js/plugins/pdfmake.min.js"></script>
<script src="{{ asset('app') }}/assets/js/plugins/jszip.min.js"></script>
<script src="{{ asset('app') }}/assets/js/plugins/dataTables.buttons.min.js"></script>
<script src="{{ asset('app') }}/assets/js/plugins/vfs_fonts.js"></script>
<script src="{{ asset('app') }}/assets/js/plugins/buttons.html5.min.js"></script>
<script src="{{ asset('app') }}/assets/js/plugins/buttons.bootstrap5.min.js"></script>

    <!-- Gridjs Demo js -->
<script src="{{ asset('app') }}/assets/js/components/table-gridjs.js"></script>

<!-- Vector Map Js -->
<script src="{{ asset('app') }}/assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
<script src="{{ asset('app') }}/assets/vendor/jsvectormap/maps/world-merc.js"></script>
<script src="{{ asset('app') }}/assets/vendor/jsvectormap/maps/world.js"></script>

<!-- Dashboard Js -->
<script src="{{ asset('app') }}/assets/js/pages/dashboard-analytics.js"></script>

<!-- Flatepicker Demo Js -->
<script src="{{ asset('app') }}/assets/js/components/form-flatepicker.js"></script>

<!-- SweetAlert Demo js -->
{{-- <script src="{{ asset('app') }}/assets/js/components/extended-sweetalert.js"></script> --}}

</body>

</html>
