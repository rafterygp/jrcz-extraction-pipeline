# GeoJSON Spatial Delivery API & Interactive Map

An interactive, high-performance web engine for visualizing, managing, and extracting authoritative Dutch geographical boundaries. 

Originally conceived as a GeoPackage (GPKG) extraction pipeline, this project was successfully refactored and optimized into a **GeoJSON Spatial Delivery API**. It serves, renders, and exports precise `Polygon` and `MultiPolygon` boundary data for Dutch municipalities (gemeenten), districts (wijken), and neighborhoods (buurten) using official Centraal Bureau voor de Statistiek (CBS) data.

## 🚀 Key Features

* **Precision Geometry Extraction:** Completely rebuilt the export engine to prevent data-loss. Instead of degrading boundary selections into single-axis `Point` coordinates, the system accurately parses, stores, and downloads complex `MultiPolygon` data.
* **Spatial File Routing:** A lightweight, highly functional backend file delivery system that parses and serves pre-formatted GeoJSON payloads on the fly.
* **Robust State Management:** Frontend JavaScript accurately serializes and preserves raw spatial geometry across recent clicks, favorites, and selections without dropping coordinate fidelity.
* **Test-Driven Reliability:** Backend endpoints are covered by comprehensive PHPUnit tests ensuring 200 OK delivery, 404 graceful degradation, and strict structural validation of the JSON payloads.

## 🛠️ Tech Stack

* **Backend:** PHP / Laravel (Spatial Delivery API & File Routing)
* **Frontend:** JavaScript (Vite), HTML5, CSS3
* **Testing:** PHPUnit (Feature testing for endpoints and payload structure)
* **Data Source:** PDOK / CBS Wijk- en Buurtkaart 

## 🧠 Architecture & Evolution

During development, the architecture underwent a critical pivot. Initial iterations struggled with a bug that collapsed complex geographical boundaries into single map-click coordinates. By hooking directly into the Leaflet/Map layers, the serialization logic was rewritten to encode and decode raw geometry securely through the DOM.

Simultaneously, a deep dive into the backend payload structure revealed that the system was not querying binary SQLite `.gpkg` databases as originally assumed, but was actually executing as a highly efficient **GeoJSON file router**. We embraced this architecture, optimizing the endpoints to serve these static JSON files natively, resulting in lightning-fast frontend render times and zero database overhead.

## ⚙️ Local Installation

1. Clone the repository: `git clone https://github.com/yourusername/geojson-spatial-api.git`
2. Install PHP dependencies: `composer install`
3. Install frontend dependencies: `npm install`
4. Ensure your `public/geopackages` directory contains the required CBS `.geojson` files.
5. Boot the server: `php artisan serve`
6. Run the Vite build: `npm run dev`
7. Run tests: `php artisan test`