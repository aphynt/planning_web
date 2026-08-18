@include('layout.head', ['title' => 'Mine Monitoring'])
@include('layout.header')
@include('layout.theme_settings')
@include('layout.sidebar')

<link href="{{ asset('app') }}/assets/css/leaflet.css" rel="stylesheet" type="text/css" />

<style>
    .mine-page {
        padding-bottom: 24px;
    }

    .monitor-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .monitor-title h4 {
        margin-bottom: 4px;
        font-weight: 700;
    }

    .monitor-title p {
        margin: 0;
        color: #7b8190;
        font-size: 12px;
    }

    .monitor-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 20px;
        background: #ecfdf3;
        color: #16a34a;
        font-size: 12px;
        font-weight: 600;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, .12);
    }

    .update-box {
        text-align: right;
        min-width: 95px;
    }

    .update-label {
        color: #8a919d;
        font-size: 10px;
    }

    .update-time {
        font-size: 12px;
        font-weight: 600;
    }

    .monitor-kpi {
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 15px;
        padding: 17px;
        display: flex;
        align-items: center;
        gap: 13px;
        box-shadow: 0 4px 16px rgba(20, 30, 50, .045);
        height: 100%;
        transition: .2s ease;
    }

    .monitor-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(20, 30, 50, .08);
    }

    .kpi-icon {
        width: 43px;
        height: 43px;
        min-width: 43px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .kpi-label {
        color: #7b8190;
        font-size: 11px;
        margin-bottom: 4px;
    }

    .kpi-value {
        font-size: 23px;
        font-weight: 700;
        line-height: 1;
    }

    .monitor-card {
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(20, 30, 50, .05);
        overflow: hidden;
    }

    .monitor-card-header {
        padding: 17px 19px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-bottom: 1px solid #edf0f5;
    }

    .monitor-card-header h5 {
        margin: 0 0 3px;
        font-size: 15px;
        font-weight: 700;
    }

    .monitor-card-header span {
        color: #8a919d;
        font-size: 11px;
    }

    .map-card {
        position: relative;
    }

    #mineMap {
        height: 780px;
        width: 100%;
        background: #e5e7eb;
    }

    .mine-map-line {
        pointer-events: none;
    }

    .map-legend {
        position: absolute;
        left: 15px;
        bottom: 15px;
        z-index: 500;
        background: rgba(255,255,255,.96);
        border-radius: 10px;
        padding: 9px 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 10px;
        box-shadow: 0 5px 18px rgba(0,0,0,.12);
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .production {
        background: #3b82f6;
    }

    .waiting {
        background: #f59e0b;
    }

    .maintenance {
        background: #ef4444;
    }

    .offline {
        background: #64748b;
    }

    .other {
        background: #22c55e;
    }

    .unit-search {
        padding: 14px 17px 0;
        position: relative;
    }

    .unit-search i {
        position: absolute;
        left: 29px;
        top: 25px;
        color: #9aa1ad;
        z-index: 2;
    }

    .unit-search input {
        padding-left: 34px;
        border-radius: 9px;
        background: #f8fafc;
        border-color: #edf0f5;
        font-size: 12px;
    }

    .unit-list {
        height: 520px;
        overflow-y: auto;
        padding: 7px 10px 10px;
    }

    .unit-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 9px;
        border-bottom: 1px solid #f0f2f5;
        cursor: pointer;
        transition: .15s ease;
    }

    .unit-item:hover {
        background: #f8fafc;
        border-radius: 9px;
    }

    .unit-status {
        width: 8px;
        height: 8px;
        min-width: 8px;
        border-radius: 50%;
    }

    .unit-info {
        min-width: 0;
        flex: 1;
    }

    .unit-name {
        font-size: 12px;
        font-weight: 700;
    }

    .unit-type {
        color: #7b8190;
        font-size: 10px;
        margin-top: 1px;
    }

    .unit-state {
        font-size: 10px;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .unit-speed {
        text-align: right;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .unit-speed small {
        color: #8a919d;
        font-size: 9px;
        font-weight: 400;
    }

    .unit-action {
        color: #adb4c0;
        font-size: 17px;
    }

    .activity-list {
        padding: 5px 19px;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid #f0f2f5;
    }

    .activity-time {
        width: 50px;
        color: #8a919d;
        font-size: 11px;
    }

    .activity-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 15px;
    }

    .activity-location {
        max-width: 230px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .leaflet-popup-content {
        margin: 11px 13px;
    }

    .map-popup-title {
        font-weight: 700;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .map-popup-status {
        font-size: 11px;
        margin-bottom: 7px;
    }

    .map-popup-row {
        font-size: 10px;
        color: #6b7280;
        margin-top: 3px;
    }

    .map-marker {
        width: 28px;
        height: 28px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,.25);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .map-marker span {
        transform: rotate(45deg);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
    }

    .map-marker.production {
        background: #22c55e;
    }

    .map-marker.waiting {
        background: #f59e0b;
    }

    .map-marker.maintenance {
        background: #ef4444;
    }

    .map-marker.offline {
        background: #64748b;
    }

    .map-marker.other {
        background: #3b82f6;
    }

    .empty-state {
        padding: 45px 15px;
        text-align: center;
        color: #9aa1ad;
        font-size: 12px;
    }

    .api-error {
        display: none;
        margin-bottom: 15px;
    }

    @media (max-width: 991.98px) {
        #mineMap {
            height: 450px;
        }

        .unit-list {
            height: 400px;
        }

        .monitor-header {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (max-width: 575.98px) {
        .monitor-actions {
            width: 100%;
            justify-content: space-between;
        }

        .map-legend {
            left: 10px;
            right: 10px;
            bottom: 10px;
        }
    }

    .map-navigation {
        display: flex;
        flex-direction: column;
        gap: 3px;
        background: transparent;
    }

    .map-navigation-row {
        display: flex;
        justify-content: center;
        gap: 3px;
    }

    .map-navigation button {
        width: 32px;
        height: 32px;
        border: 1px solid #bbb;
        background: #fff;
        color: #333;
        font-size: 17px;
        font-weight: 600;
        cursor: pointer;
        border-radius: 5px;
        box-shadow: 0 1px 4px rgba(0,0,0,.25);
        transition: all .15s ease;
    }

    .map-navigation button:hover {
        background: #f1f5f9;
        transform: scale(1.05);
    }

    .map-navigation button:active {
        transform: scale(.95);
    }

    .map-marker-wrapper {
        position: relative;
        width: 80px;
        height: 48px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .map-marker-wrapper .map-marker {
        flex-shrink: 0;
    }

    .map-marker-label {
        margin-top: 2px;
        padding: 1px 5px;
        background: rgba(255, 255, 255, 0.92);
        color: #1e293b;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 700;
        line-height: 13px;
        white-space: nowrap;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        text-align: center;
    }

    .mine-area-label {
        background: transparent !important;
        border: 0 !important;
        pointer-events: none;
    }

    .mine-area-name {
        color: #111827;
        font-size: 11px;
        font-weight: 800;
        line-height: 13px;
        text-align: center;
        white-space: nowrap;
        text-shadow:
            0 1px 2px #fff,
            1px 0 2px #fff,
            -1px 0 2px #fff,
            0 -1px 2px #fff;
        transform: translateY(-1px);
    }
</style>

<div class="page-content">
    <div class="container-fluid mine-page">

        <div id="apiError" class="alert alert-danger api-error">
            Gagal mengambil data Mine Monitoring.
        </div>

        {{-- <div class="row g-3 mb-3">
            <div class="col-xl col-md-6">
                <div class="monitor-kpi">
                    <div class="kpi-icon bg-primary-subtle text-primary">
                        <i class="ri-truck-line"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Total Unit</div>
                        <div class="kpi-value" id="totalUnit">0</div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6">
                <div class="monitor-kpi">
                    <div class="kpi-icon bg-success-subtle text-success">
                        <i class="ri-play-circle-line"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Production</div>
                        <div class="kpi-value" id="productionUnit">0</div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6">
                <div class="monitor-kpi">
                    <div class="kpi-icon bg-warning-subtle text-warning">
                        <i class="ri-time-line"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Waiting</div>
                        <div class="kpi-value" id="waitingUnit">0</div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6">
                <div class="monitor-kpi">
                    <div class="kpi-icon bg-danger-subtle text-danger">
                        <i class="ri-tools-line"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Maintenance</div>
                        <div class="kpi-value" id="maintenanceUnit">0</div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6">
                <div class="monitor-kpi">
                    <div class="kpi-icon bg-secondary-subtle text-secondary">
                        <i class="ri-wifi-off-line"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Offline</div>
                        <div class="kpi-value" id="offlineUnit">0</div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row g-3">

            <div class="col-xl-12">
                <div class="monitor-card map-card">
                    <div class="monitor-card-header">
                        <div>
                            <h5>Mine Monitoring (beta)</h5>
                            <span>Real-time unit position</span>
                        </div>

                        <div class="monitor-actions">
                            <div class="update-box">
                                <div class="update-label">Last Update</div>
                                <div class="update-time" id="lastUpdate">-</div>
                            </div>

                            <span class="live-badge">
                                <span class="live-dot"></span>
                                Live
                            </span>

                            <button type="button" class="btn btn-light border btn-sm" id="btnRefresh">
                                <i class="ri-refresh-line"></i>
                            </button>
                        </div>
                    </div>

                    <div id="mineMap"></div>

                    <div class="map-legend">
                        <span class="legend-item">
                            <span class="legend-dot production"></span>
                            Production
                        </span>
                        <span class="legend-item">
                            <span class="legend-dot waiting"></span>
                            Waiting
                        </span>
                        <span class="legend-item">
                            <span class="legend-dot maintenance"></span>
                            Maintenance
                        </span>
                        <span class="legend-item">
                            <span class="legend-dot offline"></span>
                            Offline
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="monitor-card h-100">
                    <div class="monitor-card-header">
                        <div>
                            <h5>Unit Monitoring</h5>
                            <span id="unitCountLabel">0 unit</span>
                        </div>

                        <button type="button" class="btn btn-sm btn-light border" id="btnClearSearch">
                            <i class="ri-filter-off-line"></i>
                        </button>
                    </div>

                    <div class="unit-search">
                        <i class="ri-search-line"></i>
                        <input type="text" class="form-control form-control-sm" id="searchUnit" placeholder="Search unit...">
                    </div>

                    <div class="unit-list" id="unitList">
                        <div class="empty-state">Memuat data...</div>
                    </div>
                </div>
            </div>

            <div class="col-8">
                <div class="monitor-card">
                    <div class="monitor-card-header">
                        <div>
                            <h5>Activity Monitoring</h5>
                            <span>Aktivitas unit terbaru</span>
                        </div>
                    </div>

                    <div class="activity-list" id="activityList">
                        <div class="empty-state">Memuat data...</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@include('layout.footer')

<script src="{{ asset('app') }}/assets/js/leaflet.js"></script>

<script>
    let mineMonitoringTimer = null;
    let mineMonitoringUnits = [];
    let mineMap = null;
    let mineMarkers = {};
    let mapObjectLayers = {};
    let mapHasFitted = false;

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    function formatNumber(value, decimal = 1) {
        const number = Number(value);
        return Number.isFinite(number) ? number.toFixed(decimal) : '0.0';
    }

    function getStatusIcon(category) {
        if (category === 'waiting') {
            return 'ri-time-line';
        }

        if (category === 'maintenance') {
            return 'ri-tools-line';
        }

        if (category === 'offline') {
            return 'ri-wifi-off-line';
        }

        return 'ri-truck-line';
    }

    function initMineMap() {
        mineMap = L.map('mineMap', {
            zoomControl: true,
            attributionControl: true
        });

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 16,
            attribution: '&copy; IT - PT.SIMS JAYA KALTIM'
        }).addTo(mineMap);

        mineMap.setView([-0.35, 115.52], 17);
    }



    function createUnitIcon(unit) {
        const unitId = String(unit.id || '');
        const shortName = unitId.substring(0, 2);

        return L.divIcon({
            className: '',
            html: `
                <div class="map-marker-wrapper">
                    <div class="map-marker ${escapeHtml(unit.category || 'other')}">
                        <span>${escapeHtml(shortName)}</span>
                    </div>
                    <div class="map-marker-label">
                        ${escapeHtml(unitId)}
                    </div>
                </div>
            `,
            iconSize: [80, 48],
            iconAnchor: [40, 28],
            popupAnchor: [0, -35]
        });
    }

    function updateMap(units, mapObjects) {
        if (!mineMap) {
            return;
        }

        updateMapObjects(mapObjects || []);

        const activeMarkerIds = {};
        const validPoints = [];

        units.forEach(function(unit) {
            const lat = Number(unit.lat);
            const lon = Number(unit.lon);

            if (!Number.isFinite(lat) || !Number.isFinite(lon)) {
                return;
            }

            if (lat === 0 && lon === 0) {
                return;
            }

            activeMarkerIds[unit.id] = true;
            validPoints.push([lat, lon]);

            const popup = `
                <div class="map-popup-title">${escapeHtml(unit.id)}</div>
                <div class="map-popup-status">
                    <strong>${escapeHtml(unit.status || 'Ready')}</strong>
                </div>
                <div class="map-popup-row">Speed: ${formatNumber(unit.speed)} km/h</div>
                <div class="map-popup-row">Location: ${escapeHtml(unit.location || '-')}</div>
                <div class="map-popup-row">Operator: ${escapeHtml(unit.operator || '-')}</div>
                <div class="map-popup-row">Assignment: ${escapeHtml(unit.assignment || '-')}</div>
            `;

            if (!mineMarkers[unit.id]) {
                mineMarkers[unit.id] = L.marker([lat, lon], {
                    icon: createUnitIcon(unit)
                }).addTo(mineMap);

                mineMarkers[unit.id].bindPopup(popup);
            } else {
                mineMarkers[unit.id].setLatLng([lat, lon]);
                mineMarkers[unit.id].setIcon(createUnitIcon(unit));
                mineMarkers[unit.id].setPopupContent(popup);
            }
        });

        Object.keys(mineMarkers).forEach(function(id) {
            if (!activeMarkerIds[id]) {
                mineMap.removeLayer(mineMarkers[id]);
                delete mineMarkers[id];
            }
        });

        if (!mapHasFitted && validPoints.length > 0) {
            mineMap.fitBounds(validPoints, {
                padding: [10, 10],
                maxZoom: 17
            });

            mapHasFitted = true;
        }
    }


    function getMapObjectId(object) {
        return String(
            object.id ??
            object.MAPOBJECTID ??
            object.mapobjectid ??
            ''
        );
    }

    function getMapObjectType(object) {
        return Number(
            object.type ??
            object.MAPOBJECTTYPE ??
            object.mapobjecttype ??
            0
        );
    }

    function getMapObjectName(object) {
        return String(
            object.name ??
            object.MAPOBJECTNAME ??
            object.mapobjectname ??
            object.title ??
            object.TITLE ??
            object.layer ??
            object.LAYERNAME ??
            object.layername ??
            ''
        ).trim();
    }

    function getMapObjectGeoPoints(object) {
        return object.geopoints ??
            object.GEOPOINTS ??
            object.geopoint ??
            object.GEOPOINT ??
            '';
    }

    function createAreaLabel(object, points) {
        const polygon = L.polygon(points);
        const center = polygon.getBounds().getCenter();
        const name = getMapObjectName(object);

        if (!name) {
            return null;
        }

        return L.marker(center, {
            icon: L.divIcon({
                className: 'mine-area-label',
                html: `
                    <div class="mine-area-name">
                        ${escapeHtml(name)}
                    </div>
                `,
                iconSize: [220, 26],
                iconAnchor: [110, 13]
            }),
            interactive: false
        });
    }

    function createGeoPolygon(object) {
        const points = parseGeoPoints(object.geopoints);

        if (points.length < 3) {
            return null;
        }

        const style = getPolygonStyle(object);

        const polygon = L.polygon(points, {
            ...style,
            lineJoin: 'round',
            interactive: false
        });

        const label = createAreaLabel(object, points);

        const group = L.layerGroup([polygon]);

        if (label) {
            mineMap.removeLayer(label);
            group.addLayer(label);
        }

        return group.addTo(mineMap);
    }

    function parseGeoPoints(value) {
        if (value === null || value === undefined) {
            return [];
        }

        const text = String(value).trim();

        if (!text) {
            return [];
        }

        const points = [];

        // Format utama: [[lon,lat],[lon,lat],...]
        const bracketRegex =
            /\[\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\]/g;

        let match;

        while ((match = bracketRegex.exec(text)) !== null) {
            const longitude = Number(match[1]);
            const latitude = Number(match[2]);

            if (
                Number.isFinite(longitude) &&
                Number.isFinite(latitude) &&
                Math.abs(latitude) <= 90 &&
                Math.abs(longitude) <= 180
            ) {
                points.push([latitude, longitude]);
            }
        }

        if (points.length >= 2) {
            return points;
        }

        // Format alternatif: "lon,lat;lon,lat;..."
        const pairs = text
            .replace(/^\s*\[\s*/, '')
            .replace(/\s*\]\s*$/, '')
            .split(';');

        pairs.forEach(function(pair) {
            const values = pair
                .replace(/[\[\]]/g, '')
                .split(',')
                .map(function(item) {
                    return Number(String(item).trim());
                });

            if (values.length >= 2) {
                const longitude = values[0];
                const latitude = values[1];

                if (
                    Number.isFinite(longitude) &&
                    Number.isFinite(latitude) &&
                    Math.abs(latitude) <= 90 &&
                    Math.abs(longitude) <= 180
                ) {
                    points.push([latitude, longitude]);
                }
            }
        });

        return points;
    }

    function createGeoLine(object) {
        const points = parseGeoPoints(object.geopoints);

        if (points.length < 2) {
            return null;
        }

        return L.polyline(points, {
            color: '#60a5fa',
            weight: 2,
            opacity: 0.85,
            lineCap: 'round',
            lineJoin: 'round',
            className: 'mine-map-line',
            interactive: false
        }).addTo(mineMap);
    }

    function getPolygonStyle(object) {
        const name = getMapObjectName(object).toUpperCase();

        if (name.includes('DISPOSAL')) {
            return {
                color: '#64748b',
                fillColor: '#cbd5e1',
                fillOpacity: 0.38,
                weight: 1
            };
        }

        if (
            name.includes('PIT') ||
            name.includes('MINING')
        ) {
            return {
                color: '#78716c',
                fillColor: '#d6d3d1',
                fillOpacity: 0.32,
                weight: 1
            };
        }

        if (
            name.includes('WORKSHOP') ||
            name.includes('OFFICE') ||
            name.includes('FUEL')
        ) {
            return {
                color: '#64748b',
                fillColor: '#e2e8f0',
                fillOpacity: 0.45,
                weight: 1
            };
        }

        return {
            color: '#94a3b8',
            fillColor: '#cbd5e1',
            fillOpacity: 0.25,
            weight: 1
        };
    }

    function updateMapObjects(objects) {
        const activeObjectIds = {};

        (objects || []).forEach(function(object) {
            const id = getMapObjectId(object);
            const type = getMapObjectType(object);
            const geoPoints = getMapObjectGeoPoints(object);
            const name = getMapObjectName(object);

            if (!id) {
                return;
            }

            const points = parseGeoPoints(geoPoints);

            if (points.length < 2) {
                return;
            }

            activeObjectIds[id] = true;

            /*
             * MAPOBJECTTYPE = 6 = road/line.
             * Jangan melihat jumlah titik untuk menentukan road.
             * Kalau type = 6, selalu gambar sebagai polyline.
             */
            const isRoad = type === 6;

            if (!mapObjectLayers[id]) {

                if (isRoad) {
                    mapObjectLayers[id] = createGeoLine({
                        ...object,
                        geopoints: geoPoints
                    });
                } else {
                    mapObjectLayers[id] = createGeoPolygon({
                        ...object,
                        geopoints: geoPoints,
                        name: name
                    });
                }

                return;
            }

            const layer = mapObjectLayers[id];

            if (isRoad) {

                if (layer && typeof layer.setLatLngs === 'function') {
                    layer.setLatLngs(points);
                }

            } else {

                /*
                 * Area disimpan sebagai LayerGroup:
                 * [polygon, label]
                 */
                if (!layer || typeof layer.getLayers !== 'function') {
                    if (layer) {
                        mineMap.removeLayer(layer);
                    }

                    mapObjectLayers[id] = createGeoPolygon({
                        ...object,
                        geopoints: geoPoints,
                        name: name
                    });

                    return;
                }

                const layers = layer.getLayers();

                const polygon = layers.find(function(item) {
                    return item instanceof L.Polygon;
                });

                const label = layers.find(function(item) {
                    return item instanceof L.Marker;
                });

                if (polygon) {
                    polygon.setLatLngs(points);
                    polygon.setStyle(getPolygonStyle({
                        ...object,
                        name: name
                    }));
                }

                if (label) {
                    const tempPolygon = L.polygon(points);
                    const center = tempPolygon.getBounds().getCenter();

                    label.setLatLng(center);

                    label.setIcon(L.divIcon({
                        className: 'mine-area-label',
                        html: `
                            <div class="mine-area-name">
                                ${escapeHtml(name)}
                            </div>
                        `,
                        iconSize: [260, 28],
                        iconAnchor: [130, 14]
                    }));
                } else if (name) {
                    const tempPolygon = L.polygon(points);
                    const center = tempPolygon.getBounds().getCenter();

                    const newLabel = L.marker(center, {
                        icon: L.divIcon({
                            className: 'mine-area-label',
                            html: `
                                <div class="mine-area-name">
                                    ${escapeHtml(name)}
                                </div>
                            `,
                            iconSize: [260, 28],
                            iconAnchor: [130, 14]
                        }),
                        interactive: false
                    });

                    layer.addLayer(newLabel);
                }
            }
        });

        Object.keys(mapObjectLayers).forEach(function(id) {
            if (!activeObjectIds[id]) {
                mineMap.removeLayer(mapObjectLayers[id]);
                delete mapObjectLayers[id];
            }
        });
    }

    function fitMapToUnits() {
        if (!mineMap) {
            return;
        }

        const points = [];

        Object.keys(mineMarkers).forEach(function(id) {
            const marker = mineMarkers[id];

            if (marker) {
                points.push(marker.getLatLng());
            }
        });

        if (points.length === 0) {
            return;
        }

        if (points.length === 1) {
            mineMap.setView(points[0], 17);
            return;
        }

        const bounds = L.latLngBounds(points);

        mineMap.fitBounds(bounds, {
            padding: [10, 10],
            maxZoom: 17
        });
    }

    function updateSummary(summary) {
        $('#totalUnit').text(summary.total ?? 0);
        $('#productionUnit').text(summary.production ?? 0);
        $('#waitingUnit').text(summary.waiting ?? 0);
        $('#maintenanceUnit').text(summary.maintenance ?? 0);
        $('#offlineUnit').text(summary.offline ?? 0);
    }

    function updateUnitList(units) {
        mineMonitoringUnits = units || [];

        const keyword = ($('#searchUnit').val() || '').toLowerCase().trim();

        const filtered = mineMonitoringUnits.filter(function(unit) {
            return String(unit.id || '').toLowerCase().includes(keyword) ||
                String(unit.status || '').toLowerCase().includes(keyword) ||
                String(unit.type || '').toLowerCase().includes(keyword);
        });

        $('#unitCountLabel').text(filtered.length + ' unit');

        if (!filtered.length) {
            $('#unitList').html('<div class="empty-state">Unit tidak ditemukan.</div>');
            return;
        }

        let html = '';

        filtered.forEach(function(unit) {
            const category = unit.category || 'other';
            const statusText = category === 'offline' ? 'Offline' : (unit.status || 'Ready');

            html += `
                <div class="unit-item" data-unit-id="${escapeHtml(unit.id)}">
                    <div class="unit-status ${escapeHtml(category)}"></div>

                    <div class="unit-info">
                        <div class="unit-name">${escapeHtml(unit.id)}</div>
                        <div class="unit-type">Type ${escapeHtml(unit.type)}</div>
                        <div class="unit-type">IP Address ${escapeHtml(unit.ipaddress)}</div>
                        <div class="unit-state">${escapeHtml(statusText)}</div>
                    </div>

                    <div class="unit-speed">
                        ${formatNumber(unit.speed)}
                        <small>km/h</small>
                    </div>

                    <div class="unit-action">
                        <i class="ri-arrow-right-s-line"></i>
                    </div>
                </div>
            `;
        });

        $('#unitList').html(html);
    }

    function updateActivities(activities) {
        if (!activities || !activities.length) {
            $('#activityList').html('<div class="empty-state">Belum ada aktivitas.</div>');
            return;
        }

        let html = '';

        activities.forEach(function(unit) {
            const category = unit.category || 'other';
            const icon = getStatusIcon(category);

            let time = '-';

            if (unit.report_time) {
                const date = new Date(String(unit.report_time).replace(' ', 'T'));

                if (!Number.isNaN(date.getTime())) {
                    time = date.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                } else {
                    time = String(unit.report_time).substring(11, 19);
                }
            }

            html += `
                <div class="activity-item">
                    <div class="activity-time">${escapeHtml(time)}</div>

                    <div class="activity-icon ${escapeHtml(category)}">
                        <i class="${icon}"></i>
                    </div>

                    <div class="flex-grow-1">
                        <div class="fw-semibold">${escapeHtml(unit.id)}</div>
                        <div class="text-muted small">${escapeHtml(unit.statusdesc || '-')}</div>
                    </div>

                    <div class="activity-location text-muted small">
                        ${escapeHtml(unit.location || '-')}
                    </div>
                </div>
            `;
        });

        $('#activityList').html(html);
    }

    function loadMineMonitoring() {
        $.ajax({
            url: "{{ route('mineMonitoring.api') }}",
            type: "GET",
            dataType: "json",
            cache: false,
            success: function(res) {
                if (!res || !res.success) {
                    $('#apiError').show();
                    return;
                }

                $('#apiError').hide();

                updateSummary(res.summary || {});
                updateUnitList(res.units || []);
                updateActivities(res.activities || []);
                updateMap(
                    res.units || [],
                    res.map_objects || res.mapObjects || []
                );

                $('#lastUpdate').text(res.updated_at || '-');
            },
            error: function(xhr) {
                $('#apiError').show();
                console.error('Mine Monitoring API Error:', xhr.responseText || xhr.statusText);
            }
        });
    }


    function startMineMonitoring() {
        loadMineMonitoring();

        if (mineMonitoringTimer) {
            clearInterval(mineMonitoringTimer);
        }

        mineMonitoringTimer = setInterval(function() {
            loadMineMonitoring();
        }, 5000);
    }

    $(document).ready(function() {
        initMineMap();

        $('#btnRefresh').on('click', function() {
            loadMineMonitoring();
        });

        $('#btnMapFit').on('click', function() {
            fitMapToUnits();
        });

        $('#searchUnit').on('input', function() {
            updateUnitList(mineMonitoringUnits);
        });

        $('#btnClearSearch').on('click', function() {
            $('#searchUnit').val('');
            updateUnitList(mineMonitoringUnits);
        });

        $(document).on('click', '.unit-item', function() {
            const unitId = $(this).data('unit-id');
            const marker = mineMarkers[unitId];

            if (marker && mineMap) {
                mineMap.setView(marker.getLatLng(), 16);
                marker.openPopup();
            }
        });

        startMineMonitoring();
    });

    const NavigationControl = L.Control.extend({
        options: {
            position: 'topleft'
        },

        onAdd: function(map) {
            const container = L.DomUtil.create('div', 'map-navigation');

            const rowUp = L.DomUtil.create('div', 'map-navigation-row', container);
            const rowMiddle = L.DomUtil.create('div', 'map-navigation-row', container);
            const rowDown = L.DomUtil.create('div', 'map-navigation-row', container);

            const btnUp = L.DomUtil.create('button', '', rowUp);
            const btnLeft = L.DomUtil.create('button', '', rowMiddle);
            const btnRight = L.DomUtil.create('button', '', rowMiddle);
            const btnDown = L.DomUtil.create('button', '', rowDown);

            btnUp.innerHTML = '↑';
            btnLeft.innerHTML = '←';
            btnRight.innerHTML = '→';
            btnDown.innerHTML = '↓';

            btnUp.title = 'Geser ke atas';
            btnLeft.title = 'Geser ke kiri';
            btnRight.title = 'Geser ke kanan';
            btnDown.title = 'Geser ke bawah';

            L.DomEvent.disableClickPropagation(container);
            L.DomEvent.disableScrollPropagation(container);

            const moveMap = function(direction) {
                const distance = 0.35;

                const center = map.getCenter();

                let lat = center.lat;
                let lng = center.lng;

                if (direction === 'up') {
                    lat += distance;
                }

                if (direction === 'down') {
                    lat -= distance;
                }

                if (direction === 'left') {
                    lng -= distance;
                }

                if (direction === 'right') {
                    lng += distance;
                }

                map.panTo([lat, lng], {
                    animate: true,
                    duration: 0.3
                });
            };

            L.DomEvent.on(btnUp, 'click', function() {
                moveMap('up');
            });

            L.DomEvent.on(btnDown, 'click', function() {
                moveMap('down');
            });

            L.DomEvent.on(btnLeft, 'click', function() {
                moveMap('left');
            });

            L.DomEvent.on(btnRight, 'click', function() {
                moveMap('right');
            });

            return container;
        }
    });

    mineMap.addControl(new NavigationControl());
</script>
