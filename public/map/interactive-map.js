document.addEventListener('DOMContentLoaded', function () {
    const clickedMarkers = [];
    const allMarkers = []; 
    const favouritesArray = [];
    const leaflet = L;

    const corner1 = L.latLng(50.7504, 3.3316),
        corner2 = L.latLng(53.5587, 7.2271);
    const bounds = L.latLngBounds(corner1, corner2);

    const map = leaflet.map('map', {
        center: [52.1326, 5.2913],
        zoom: 7,
        maxBounds: bounds,
        maxBoundsViscosity: 1.0
    });

    leaflet.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors, © CARTO'
    }).addTo(map);

    function fetchGeoJson(url) {
        return fetch(url).then(response => response.json());
    }

    const geoJsonLayers = []; 
    let currentDatasetUrl = 'provinciepolygondata.geojson'; 

    // DYNAMIC STYLING ENGINE: Adjusts visibility based on area size AND zoom level
    function getBaseStyle() {
        const neutralColor = "#5e7690"; 
        const currentZoom = map.getZoom();
        
        if (currentDatasetUrl.includes('buurt')) {
            // Buurt scales dynamically: thicker lines when zoomed in
            let dynamicWeight = 0.5;
            if (currentZoom > 10) dynamicWeight = 1.0;
            if (currentZoom > 13) dynamicWeight = 2.0;
            return { color: neutralColor, weight: dynamicWeight, opacity: 0.6, fillOpacity: 0.02, fillColor: neutralColor };
        } else if (currentDatasetUrl.includes('gemeente')) {
            return { color: neutralColor, weight: 1.2, opacity: 0.8, fillOpacity: 0.02, fillColor: neutralColor };
        } else {
            return { color: neutralColor, weight: 2.0, opacity: 1.0, fillOpacity: 0.02, fillColor: neutralColor };
        }
    }

    // Recalculate styles when the user zooms in or out
    map.on('zoomend', function() {
        const baseStyle = getBaseStyle();
        allMarkers.forEach(marker => {
            if (!clickedMarkers.includes(marker)) {
                marker.setStyle(baseStyle);
            }
        });
    });

    fetchAndAddGeoJson(currentDatasetUrl);

    function fetchAndAddGeoJson(url) {
        fetchGeoJson(`/geopackages/${url}`)
            .then(geoJsonData => {
                const layer = addGeoJsonPoints(geoJsonData);
                geoJsonLayers.push({ url, layer });
            })
            .catch(error => console.error(`Error fetching ${url}:`, error));
    }

    function addGeoJsonPoints(geoJsonData) {
        const layer = L.geoJSON(geoJsonData, {
            style: function () {
                return getBaseStyle(); 
            },
            onEachFeature: function (feature, layer) {
                allMarkers.push(layer); 
                
                layer.bindTooltip(feature.properties.statnaam, {
                    sticky: true,
                    className: 'tactical-tooltip',
                    direction: 'auto'
                });
                
                layer.on('click', function () {
                    handleFeatureClick(layer, feature);
                });
            }
        }).addTo(map);
        return layer;
    }

    function removeAllGeoJsonLayers() {
        geoJsonLayers.forEach(item => map.removeLayer(item.layer));
        geoJsonLayers.length = 0; 
        allMarkers.length = 0; 
        clickedMarkers.length = 0;
        document.getElementById('item-list').innerHTML = ''; // Clear selection log on layer switch
    }

    function switchGeoJsonLayer(url, buttonId) {
        currentDatasetUrl = url; 
        removeAllGeoJsonLayers(); 
        fetchAndAddGeoJson(url); 
        
        // Update top-bar button active state
        document.querySelectorAll('.layer-btn').forEach(btn => btn.classList.remove('active-button'));
        document.getElementById(buttonId).classList.add('active-button');
    }

    // Top-bar Layer Buttons
    document.getElementById('buurt-button').addEventListener('click', () => switchGeoJsonLayer('buurtpolygondata.geojson', 'buurt-button'));
    document.getElementById('gemeente-button').addEventListener('click', () => switchGeoJsonLayer('gemeentepolygondata.geojson', 'gemeente-button'));
    document.getElementById('provincie-button').addEventListener('click', () => switchGeoJsonLayer('provinciepolygondata.geojson', 'provincie-button'));

    function handleFeatureClick(layer, feature) {
        const properties = feature.properties;
        const statcodeStr = String(properties.statcode); 

        if (clickedMarkers.includes(layer)) {
            const index = clickedMarkers.indexOf(layer);
            if (index !== -1) {
                clickedMarkers.splice(index, 1);
                layer.setStyle(getBaseStyle()); 
                
                const items = document.querySelectorAll('#item-list .list-item');
                items.forEach(item => {
                    if (String(item.getAttribute('data-id')) === statcodeStr) item.remove();
                });
            }
        } else {
            clickedMarkers.push(layer);
            layer.setStyle({ fillColor: "#ff9900", fillOpacity: 0.25, color: "#ff9900", weight: 2, opacity: 1 });

            const safeGeometry = encodeURIComponent(JSON.stringify(feature.geometry));

            // COMPACT UI: Uses flex layout and FontAwesome icons
            const sidebarContent = `
                <li class="list-item" data-id="${statcodeStr}" data-name="${properties.statnaam}" data-geometry="${safeGeometry}" style="padding: 6px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="line-height: 1.2;">
                            <span style="color:#45a29e; font-size: 0.8em;">ID:</span> <span class="data-mono" style="font-size: 0.9em;">${statcodeStr}</span><br/>
                            <span style="color:#45a29e; font-size: 0.8em;">AREA:</span> <strong style="color: #66fcf1; font-size: 0.9em;">${properties.statnaam}</strong>
                        </div>
                        <div style="display: flex; gap: 4px;">
                            <button class="cmd-btn favourite-button" style="padding: 4px 8px;" title="Star"><i class="fas fa-star"></i></button>
                            <button class="cmd-btn download-individual-button" style="padding: 4px 8px;" title="Save"><i class="fas fa-download"></i></button>
                            <button class="cmd-btn remove-button" style="color: #ff0033; border-color: rgba(255, 0, 51, 0.4); padding: 4px 8px;" title="Remove"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </li>`;
            document.getElementById('item-list').insertAdjacentHTML('beforeend', sidebarContent);
            attachButtonEventListeners();
        }
    }

    function attachButtonEventListeners() {
        document.querySelectorAll('.remove-button').forEach(button => {
            button.addEventListener('click', function () {
                const listItem = this.closest('.list-item');
                const itemId = String(listItem.getAttribute('data-id'));
                removeMarker(itemId);
                listItem.remove();
            });
        });

        document.querySelectorAll('.download-individual-button').forEach(button => {
            button.addEventListener('click', function () {
                const listItem = this.closest('.list-item');
                const id = listItem.getAttribute('data-id');
                const name = listItem.getAttribute('data-name');
                const rawGeometry = listItem.getAttribute('data-geometry');
                const geometry = JSON.parse(decodeURIComponent(rawGeometry));

                downloadGeoJson([{ id, name, geometry }], `${id}_data.geojson`);
                
                this.style.backgroundColor = 'rgba(102, 252, 241, 0.1)';
                this.style.borderColor = '#66fcf1';
                this.style.color = '#66fcf1';
            });
        });

        document.querySelectorAll('.favourite-button').forEach(button => {
            button.addEventListener('click', function () {
                const listItem = this.closest('.list-item');
                const itemId = String(listItem.getAttribute('data-id'));
                const itemName = listItem.getAttribute('data-name');
                const rawGeometry = listItem.getAttribute('data-geometry');
                const geometry = JSON.parse(decodeURIComponent(rawGeometry));

                toggleFavourite(itemId, itemName, geometry);
                this.classList.toggle('favourited');
            });
        });
    }

    function removeMarker(itemId) {
        const markerIndex = clickedMarkers.findIndex(m => String(m.feature.properties.statcode) === String(itemId));
        if (markerIndex !== -1) {
            clickedMarkers[markerIndex].setStyle(getBaseStyle());
            clickedMarkers.splice(markerIndex, 1);
        }
    }

    function downloadGeoJson(data, filename) {
        const geoJsonData = {
            type: "FeatureCollection",
            features: data.map(item => ({
                type: "Feature", geometry: item.geometry, properties: { id: item.id, name: item.name }
            }))
        };

        const blob = new Blob([JSON.stringify(geoJsonData, null, 2)], { type: 'application/geo+json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = filename;
        document.body.appendChild(a); a.click(); document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function toggleFavourite(id, name, geometry) {
        const existingIndex = favouritesArray.findIndex(item => String(item.id) === String(id));
        if (existingIndex === -1) {
            favouritesArray.push({ id: String(id), name, geometry });
        } else {
            favouritesArray.splice(existingIndex, 1);
        }
    }

    document.getElementById('reset-button').addEventListener('click', function () {
        document.getElementById('item-list').innerHTML = '';
        clickedMarkers.forEach(marker => marker.setStyle(getBaseStyle()));
        clickedMarkers.length = 0;
        
        const downloadBtn = document.getElementById('download-button');
        downloadBtn.textContent = 'Export';
        downloadBtn.style.backgroundColor = '';
        document.getElementById('export-filename').value = '';
    });

const downloadButton = document.getElementById('download-button');
    downloadButton.addEventListener('click', function () {
        const items = document.querySelectorAll('#item-list .list-item');
        if (items.length === 0) {
            alert('No polygons selected for export.');
            return;
        }

        const dataToDownload = Array.from(items).map(item => {
            const rawGeometry = item.getAttribute('data-geometry');
            return {
                id: item.getAttribute('data-id'),
                name: item.getAttribute('data-name'),
                geometry: JSON.parse(decodeURIComponent(rawGeometry))
            };
        });

        // NEW: Filename capture and validation logic
        const filenameInput = document.getElementById('export-filename').value.trim();
        let exportName = filenameInput !== '' ? filenameInput : 'jrcz_extraction'; // Fallback to default
        
        // Ensure it always has the correct extension
        if (!exportName.endsWith('.geojson')) {
            exportName += '.geojson';
        }

        downloadGeoJson(dataToDownload, exportName);
        
        downloadButton.textContent = 'Exported';
        downloadButton.style.backgroundColor = 'rgba(102, 252, 241, 0.1)';
        
        setTimeout(() => { 
            downloadButton.textContent = 'Export'; 
            downloadButton.style.backgroundColor = ''; 
        }, 3000);
    });

    const searchBar = document.getElementById('search-bar');
    searchBar.addEventListener('input', function () {
        const query = searchBar.value.toLowerCase();
        const base = getBaseStyle();
        
        allMarkers.forEach(marker => {
            const { statcode, statnaam } = marker.feature.properties;
            const match = String(statcode).toLowerCase().includes(query) || statnaam.toLowerCase().includes(query);
            
            marker.setStyle({ 
                fillColor: match ? "#ff9900" : base.fillColor, 
                color: match ? "#ff9900" : base.color,
                fillOpacity: match ? 0.3 : base.fillOpacity,
                weight: match ? 2 : base.weight,
                opacity: match ? 1 : base.opacity
            });
        });
    });
});