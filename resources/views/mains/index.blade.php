<x-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="{{ asset('map/interactive-map.js') }}"></script>

    <style>
        body, html {
            background-color: #080b12; color: #8b9cb0;
            font-family: system-ui, -apple-system, sans-serif; 
            margin: 0; padding: 0; height: 100vh; overflow: hidden;
        }

        .leaflet-container { background: #080b12; border: 1px solid #1f3a47; border-radius: 6px; }

        /* HUD HEADER WITH CENTERED TOGGLES */
        .sys-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 24px; border-bottom: 1px solid #1f3a47; background: #0b101a;
        }
        .sys-header h1 { margin: 0; color: #66fcf1; font-size: 1.1em; font-weight: 600; flex: 1; }
        
        .layer-toggles {
            display: flex; gap: 10px; flex: 1; justify-content: center;
        }
        
        .sys-header .user-status { color: #8b9cb0; font-size: 0.85em; font-family: monospace; flex: 1; text-align: right; }

        /* DASHBOARD LAYOUT */
.dashboard-container { 
            display: flex; 
            /* Subtract more pixels to lift the entire container up */
            height: calc(100vh - 85px); 
            /* Top/Right/Bottom/Left - added 25px specifically to the bottom */
            padding: 15px 15px 25px 15px; 
            gap: 15px; 
        }
        .map-section { flex: 2; position: relative; z-index: 1; min-height: 500px; }
        
        /* SIDEBAR (Flexbox enforced) */
        .control-panel {
            flex: 1; min-width: 350px; max-width: 450px; 
            display: flex; flex-direction: column; gap: 15px; z-index: 10;
        }

        .terminal-box {
            border: 1px solid #1f3a47; background-color: rgba(31, 58, 71, 0.1); 
            padding: 15px; border-radius: 6px;
        }
        .terminal-box h2 {
            margin: 0 0 10px 0; font-size: 0.95em; color: #45a29e; font-weight: 600;
            border-bottom: 1px solid #1f3a47; padding-bottom: 8px; text-transform: uppercase;
        }

        .cmd-input {
            width: 100%; background: #0a0e17; border: 1px solid #1f3a47; color: #66fcf1;
            padding: 10px; font-family: inherit; margin-bottom: 5px; outline: none; border-radius: 4px; box-sizing: border-box;
        }
        .cmd-input:focus { border-color: #66fcf1; box-shadow: 0 0 0 2px rgba(102, 252, 241, 0.1); }
        
        .cmd-btn {
            background: rgba(31, 58, 71, 0.3); border: 1px solid #1f3a47; color: #8b9cb0;
            padding: 8px 12px; cursor: pointer; font-family: inherit; font-size: 0.9em; 
            font-weight: 500; border-radius: 4px; transition: all 0.15s ease;
        }
        .cmd-btn:hover { border-color: #ff9900; color: #ff9900; background: rgba(255, 153, 0, 0.05); }
        .cmd-btn.active-button { border-color: #66fcf1; color: #66fcf1; background: rgba(102, 252, 241, 0.05); }
        .cmd-btn.favourited { background: rgba(226, 167, 58, 0.1); color: #e2a73a; border-color: #e2a73a; }

        /* FLEX CONTAINMENT FOR SELECTION LOG */
        .results-box { 
            display: flex; flex-direction: column; flex-grow: 1; min-height: 0; 
        }
        .scroll-container { 
            flex-grow: 1; overflow-y: auto; margin-bottom: 15px; 
            border: 1px solid #1f3a47; background: #0a0e17; padding: 10px; border-radius: 4px;
        }
        .list { list-style: none; padding: 0; margin: 0; }
        .list li { border-bottom: 1px solid #1f3a47; padding: 8px 0; font-size: 0.85em; }
        .list li:last-child { border-bottom: none; }
        
        .data-mono { font-family: 'Consolas', monospace; color: #e2a73a; }

        /* TACTICAL TOOLTIPS */
        .tactical-tooltip {
            background-color: rgba(8, 11, 18, 0.95) !important; color: #66fcf1 !important;
            border: 1px solid #45a29e !important; border-radius: 4px !important;
            padding: 4px 8px !important; font-family: 'Consolas', monospace !important; font-size: 0.9em !important;
        }
        .leaflet-tooltip-top:before, .leaflet-tooltip-bottom:before, .leaflet-tooltip-left:before, .leaflet-tooltip-right:before { border: none !important; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #080b12; }
        ::-webkit-scrollbar-thumb { background: #1f3a47; border-radius: 4px; }
    </style>

    <div class="sys-header">
        <h1>JRCZ // SPATIAL_ENGINE</h1>
        <div class="layer-toggles">
            <button class="cmd-btn layer-btn active-button" id="provincie-button">Province</button>
            <button class="cmd-btn layer-btn" id="gemeente-button">Gemeente</button>
            <button class="cmd-btn layer-btn" id="buurt-button">Buurt</button>
        </div>
        <div class="user-status">STATUS: SYSTEM_ONLINE</div>
    </div>

    <div class="dashboard-container">
        <div class="map-section map" id="map"></div>

        <div class="control-panel">
            
            <div class="terminal-box">
                <h2>Data Source</h2>
                @if($activeFile ?? false)
                    <div style="border-left: 2px solid #66fcf1; padding-left: 10px; margin-bottom: 10px;">
                        <span style="font-size: 0.85em; color: #45a29e;">STATUS:</span> <span style="color: #66fcf1; font-weight: bold;">Connected</span><br>
                        <span style="font-size: 0.85em; color: #45a29e;">FILE:</span> <span style="color: #e2a73a;">{{ $activeFile }}</span>
                    </div>
                    <form id="uploadForm" method="POST" action="{{ route('upload.post') }}" enctype="multipart/form-data">
                        @csrf
                        <input name="file" type="file" id="formFile" style="display: none;" accept=".geojson" onchange="document.getElementById('uploadForm').submit();">
                        <label for="formFile" style="cursor: pointer; color: #ff9900; font-size: 0.85em; text-decoration: underline;">Replace Database</label>
                    </form>
                @else
                    <form id="uploadForm" method="POST" action="{{ route('upload.post') }}" enctype="multipart/form-data">
                        @csrf
                        <input name="file" type="file" id="formFile" style="display: none;" accept=".geojson" onchange="document.getElementById('uploadForm').submit();">
                        <label for="formFile" style="display: block; width: 100%; text-align: center; border: 1px dashed #45a29e; padding: 10px; cursor: pointer; color: #66fcf1;">Mount Payload</label>
                    </form>
                @endif
            </div>

            <div class="terminal-box">
                <h2>Query Engine</h2>
                <input type="text" class="cmd-input search-bar" id="search-bar" placeholder="Search by ID or Name...">
            </div>
<div class="terminal-box results-box">
                <h2>Selection Log</h2>
                <div class="scroll-container">
                    <ul class="list" id="item-list"></ul>
                </div>
                
                <input type="text" class="cmd-input" id="export-filename" placeholder="> filename..." style="font-size: 0.85em; margin-bottom: 10px; padding: 8px;">
                
                <div style="display: flex; gap: 10px;">
                    <button id="reset-button" class="cmd-btn" style="flex: 1; color:#ff0033; border-color: rgba(255, 0, 51, 0.4);">Reset</button>
                    <button id="download-button" class="cmd-btn" style="flex: 2; border-color:#66fcf1; color:#66fcf1;">Export</button>
                </div>
            </div>

        </div>
    </div>
</x-layout>