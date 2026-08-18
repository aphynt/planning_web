<style>
.modern-header {
    position: relative;
    z-index: 1000;
}

.modern-topbar {
    background: rgba(255, 255, 255, 0.92);
    border-bottom: 1px solid rgba(15, 23, 42, 0.06);
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}

.modern-topbar .navbar-header {
    min-height: 72px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}


.modern-topbar-button {
    width: 42px;
    height: 42px;
    border: 0;
    border-radius: 12px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    color: #64748b;
    background: transparent;

    transition:
        background .2s ease,
        color .2s ease,
        transform .2s ease;
}

.modern-topbar-button:hover {
    color: #2563eb;
    background: #f1f5f9;
    transform: translateY(-1px);
}

.modern-topbar-button:active {
    transform: scale(.95);
}

.modern-search {
    margin-left: 8px;
}

.modern-search-wrapper {
    width: 340px;
    height: 42px;

    position: relative;

    display: flex;
    align-items: center;

    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 13px;

    transition: all .2s ease;
}

.modern-search-wrapper:focus-within {
    background: #ffffff;
    border-color: rgba(37, 99, 235, .35);
    box-shadow: 0 0 0 4px rgba(37, 99, 235, .08);
}

.modern-search-icon {
    position: absolute;
    left: 15px;

    font-size: 19px;
    color: #94a3b8;

    pointer-events: none;
}

.modern-search-input {
    width: 100%;
    height: 100%;

    padding: 0 85px 0 44px;

    border: 0 !important;
    outline: 0 !important;

    background: transparent !important;

    color: #334155;
    font-size: 13px;
}

.modern-search-input::placeholder {
    color: #94a3b8;
}

.modern-search-shortcut {
    position: absolute;
    right: 10px;

    display: flex;
    gap: 4px;
}

.modern-search-shortcut span {
    min-width: 22px;
    height: 21px;

    padding: 0 5px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #e2e8f0;
    border-radius: 5px;

    background: #ffffff;

    color: #94a3b8;
    font-size: 10px;
    font-weight: 600;

    box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
}

.modern-header-divider {
    width: 1px;
    height: 28px;
    background: #e2e8f0;
    margin: 0 5px;
}

.modern-user {
    height: 52px;

    padding: 4px 7px 4px 12px;

    display: flex;
    align-items: center;
    gap: 10px;

    border-radius: 14px;

    text-decoration: none !important;

    transition: background .2s ease;
}

.modern-user:hover {
    background: #f8fafc;
}

.modern-user-info {
    flex-direction: column;
    align-items: flex-end;
    line-height: 1.25;
}

.modern-user-name {
    max-width: 160px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;

    color: #1e293b;
    font-size: 13px;
    font-weight: 600;
}

.modern-user-role {
    margin-top: 2px;

    color: #94a3b8;
    font-size: 10px;
}

.modern-avatar-wrapper {
    position: relative;
    width: 42px;
    height: 42px;
}

.modern-avatar {
    width: 42px;
    height: 42px;

    object-fit: cover;

    border-radius: 13px;

    border: 2px solid #ffffff;

    box-shadow:
        0 3px 10px rgba(15, 23, 42, .12);
}

.modern-online-indicator {
    position: absolute;

    right: -1px;
    bottom: -1px;

    width: 11px;
    height: 11px;

    border-radius: 50%;

    background: #22c55e;
    border: 2px solid #ffffff;
}

.modern-user-arrow {
    color: #94a3b8;
    font-size: 17px;
}

.modern-user-dropdown {
    width: 310px;

    margin-top: 8px !important;
    padding: 8px;

    border: 1px solid rgba(226, 232, 240, .8);
    border-radius: 18px;

    background: rgba(255, 255, 255, .97);

    box-shadow:
        0 20px 50px rgba(15, 23, 42, .12),
        0 5px 15px rgba(15, 23, 42, .06);

    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
}
.modern-dropdown-header {
    display: flex;
    align-items: center;

    padding: 13px 12px;
}

.modern-dropdown-avatar {
    position: relative;

    width: 46px;
    height: 46px;

    margin-right: 11px;
}

.modern-dropdown-avatar img {
    width: 46px;
    height: 46px;

    object-fit: cover;

    border-radius: 14px;
}

.modern-dropdown-user {
    min-width: 0;
}

.modern-dropdown-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;

    color: #1e293b;
    font-size: 14px;
    font-weight: 700;
}

.modern-dropdown-nik {
    margin-top: 3px;

    color: #94a3b8;
    font-size: 11px;
}

.modern-dropdown-greeting {
    margin: 0 8px;
    padding: 9px 11px;

    display: flex;
    align-items: center;
    gap: 7px;

    border-radius: 10px;

    background: #f8fafc;

    color: #64748b;
    font-size: 11px;
    font-weight: 500;
}

.modern-dropdown-greeting i {
    color: #f59e0b;
    font-size: 15px;
}

.modern-dropdown-divider {
    height: 1px;

    margin: 8px 5px;

    background: #f1f5f9;
}

.modern-dropdown-item {
    min-height: 58px;

    margin: 2px 0;
    padding: 8px 10px;

    display: flex;
    align-items: center;
    gap: 10px;

    border-radius: 12px;

    text-decoration: none !important;

    transition: background .2s ease;
}

.modern-dropdown-item:hover {
    background: #f8fafc;
}

.modern-dropdown-item strong {
    display: block;

    color: #334155;
    font-size: 12px;
    font-weight: 600;
}

.modern-dropdown-item small {
    display: block;

    margin-top: 2px;

    color: #94a3b8;
    font-size: 10px;
}

.modern-dropdown-item > i {
    color: #cbd5e1;
    font-size: 17px;

    transition: transform .2s ease;
}

.modern-dropdown-item:hover > i {
    transform: translateX(2px);
}


/* Icon */

.modern-dropdown-icon {
    width: 36px;
    height: 36px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #f1f5f9;

    color: #64748b;
    font-size: 19px;
}


/* Logout */

.modern-dropdown-item.logout-item:hover {
    background: #fff1f2;
}

.modern-dropdown-item.logout-item .modern-dropdown-icon {
    background: #fff1f2;
    color: #ef4444;
}

.modern-dropdown-item.logout-item strong {
    color: #ef4444;
}

[data-bs-theme="dark"] .modern-topbar,
.dark-mode .modern-topbar {
    background: rgba(15, 23, 42, .92);
    border-bottom-color: rgba(255, 255, 255, .06);
}

[data-bs-theme="dark"] .modern-topbar-button:hover,
.dark-mode .modern-topbar-button:hover {
    background: rgba(255, 255, 255, .06);
}

[data-bs-theme="dark"] .modern-topbar-button,
.dark-mode .modern-topbar-button {
    color: #94a3b8;
}

[data-bs-theme="dark"] .modern-search-wrapper,
.dark-mode .modern-search-wrapper {
    background: rgba(255, 255, 255, .05);
    border-color: rgba(255, 255, 255, .08);
}

[data-bs-theme="dark"] .modern-search-input,
.dark-mode .modern-search-input {
    color: #e2e8f0;
}

[data-bs-theme="dark"] .modern-search-wrapper:focus-within,
.dark-mode .modern-search-wrapper:focus-within {
    background: rgba(255, 255, 255, .07);
}

[data-bs-theme="dark"] .modern-search-shortcut span,
.dark-mode .modern-search-shortcut span {
    background: rgba(255, 255, 255, .05);
    border-color: rgba(255, 255, 255, .08);
}

[data-bs-theme="dark"] .modern-user:hover,
.dark-mode .modern-user:hover {
    background: rgba(255, 255, 255, .05);
}

[data-bs-theme="dark"] .modern-user-name,
.dark-mode .modern-user-name {
    color: #e2e8f0;
}

[data-bs-theme="dark"] .modern-user-dropdown,
.dark-mode .modern-user-dropdown {
    background: rgba(15, 23, 42, .98);
    border-color: rgba(255, 255, 255, .08);
}

[data-bs-theme="dark"] .modern-dropdown-name,
.dark-mode .modern-dropdown-name {
    color: #e2e8f0;
}

[data-bs-theme="dark"] .modern-dropdown-greeting,
.dark-mode .modern-dropdown-greeting {
    background: rgba(255, 255, 255, .05);
}

[data-bs-theme="dark"] .modern-dropdown-item:hover,
.dark-mode .modern-dropdown-item:hover {
    background: rgba(255, 255, 255, .05);
}

[data-bs-theme="dark"] .modern-dropdown-icon,
.dark-mode .modern-dropdown-icon {
    background: rgba(255, 255, 255, .06);
}

[data-bs-theme="dark"] .modern-dropdown-divider,
.dark-mode .modern-dropdown-divider {
    background: rgba(255, 255, 255, .06);
}

@media (max-width: 767.98px) {

    .modern-topbar .navbar-header {
        min-height: 64px;
    }

    .modern-topbar-button {
        width: 40px;
        height: 40px;
    }

    .modern-avatar-wrapper,
    .modern-avatar {
        width: 38px;
        height: 38px;
    }

    .modern-user {
        padding: 4px;
    }

    .modern-user-dropdown {
        width: 285px;
    }

}
</style>
<header class="modern-header">
    <div class="topbar modern-topbar">
        <div class="container-fluid">
            <div class="navbar-header">

                <!-- LEFT -->
                <div class="d-flex align-items-center gap-3">

                    <!-- Menu Toggle -->
                    <div class="topbar-item">
                        <button type="button"
                            class="modern-topbar-button button-toggle-menu"
                            aria-label="Toggle Menu">
                            <i class="ri-menu-2-line fs-22"></i>
                        </button>
                    </div>

                    <!-- Search -->
                    <form class="modern-search d-none d-md-block">
                        <div class="modern-search-wrapper">
                            <i class="ri-search-line modern-search-icon"></i>

                            <input type="search"
                                class="modern-search-input"
                                placeholder="Search anything..."
                                autocomplete="off">

                            <div class="modern-search-shortcut">
                                <span>Ctrl</span>
                                <span>K</span>
                            </div>
                        </div>
                    </form>

                </div>


                <!-- RIGHT -->
                <div class="d-flex align-items-center gap-2">

                    <!-- Theme -->
                    <div class="topbar-item">
                        <button type="button"
                            class="modern-topbar-button"
                            id="light-dark-mode"
                            title="Toggle Theme">

                            <i class="ri-moon-line fs-21 light-mode"></i>
                            <i class="ri-sun-line fs-21 dark-mode"></i>

                        </button>
                    </div>


                    <!-- Fullscreen -->
                    <div class="topbar-item d-none d-lg-flex">
                        <button type="button"
                            class="modern-topbar-button"
                            data-toggle="fullscreen"
                            title="Fullscreen">

                            <i class="ri-fullscreen-line fs-21 fullscreen"></i>
                            <i class="ri-fullscreen-exit-line fs-21 quit-fullscreen"></i>

                        </button>
                    </div>


                    <!-- Settings -->
                    <div class="topbar-item d-none d-md-flex">
                        <button type="button"
                            class="modern-topbar-button"
                            id="theme-settings-btn"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#theme-settings-offcanvas"
                            aria-controls="theme-settings-offcanvas"
                            title="Settings">

                            <i class="ri-settings-4-line fs-21"></i>

                        </button>
                    </div>


                    <!-- Divider -->
                    <div class="modern-header-divider d-none d-md-block"></div>


                    <!-- USER -->
                    <div class="dropdown topbar-item modern-user-wrapper">

                        @php
                            $avatar = Auth::user()->avatar;
                            $nik = Auth::user()->nik;

                            $avatarUrl = $avatar == 'gbr.jpg'
                                ? asset('app/assets/images/users/avatar.png')
                                : "http://10.72.4.202:2001/asset/foto/karyawan/{$avatar}";
                        @endphp

                        <a href="javascript:void(0)"
                            class="modern-user"
                            id="page-header-user-dropdown"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">

                            <!-- User Info -->
                            <div class="modern-user-info d-none d-lg-flex">

                                <div class="modern-user-name">
                                    {{ Auth::user()->name }}
                                </div>

                                <div class="modern-user-role">
                                    {{ $nik }}
                                </div>

                            </div>


                            <!-- Avatar -->
                            <div class="modern-avatar-wrapper">

                                <img src="{{ $avatarUrl }}"
                                    alt="avatar"
                                    class="modern-avatar">

                                <span class="modern-online-indicator"></span>

                            </div>

                            <i class="ri-arrow-down-s-line modern-user-arrow d-none d-lg-block"></i>

                        </a>


                        <!-- Dropdown -->
                        <div class="dropdown-menu dropdown-menu-end modern-user-dropdown">

                            <!-- Header -->
                            <div class="modern-dropdown-header">

                                <div class="modern-dropdown-avatar">

                                    <img src="{{ $avatarUrl }}"
                                        alt="avatar">

                                    <span class="modern-online-indicator"></span>

                                </div>

                                <div class="modern-dropdown-user">

                                    <div class="modern-dropdown-name">
                                        {{ Auth::user()->name }}
                                    </div>

                                    <div class="modern-dropdown-nik">
                                        NIK {{ $nik }}
                                    </div>

                                </div>

                            </div>


                            <div class="modern-dropdown-greeting">
                                <i class="ri-sparkling-2-line"></i>
                                Semangat hari ini!
                            </div>


                            <div class="modern-dropdown-divider"></div>


                            <!-- Profile -->
                            <a class="modern-dropdown-item"
                                href="javascript:void(0)"
                                onclick="Swal.fire({
                                    icon: 'info',
                                    title: 'Fitur belum tersedia',
                                    text: 'Fitur ini belum difungsikan.',
                                    confirmButtonText: 'OK'
                                })">

                                <span class="modern-dropdown-icon">
                                    <iconify-icon
                                        icon="solar:user-circle-broken">
                                    </iconify-icon>
                                </span>

                                <span>
                                    <strong>Profile</strong>
                                    <small>Kelola profil Anda</small>
                                </span>

                                <i class="ri-arrow-right-s-line ms-auto"></i>

                            </a>


                            <!-- Logout -->
                            <a class="modern-dropdown-item logout-item"
                                href="{{ route('logout') }}">

                                <span class="modern-dropdown-icon">
                                    <iconify-icon
                                        icon="solar:logout-3-broken">
                                    </iconify-icon>
                                </span>

                                <span>
                                    <strong>Logout</strong>
                                    <small>Keluar dari aplikasi</small>
                                </span>

                                <i class="ri-arrow-right-s-line ms-auto"></i>

                            </a>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</header>
