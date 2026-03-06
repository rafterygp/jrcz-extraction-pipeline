# JRCZ // Spatial Extraction Pipeline

A high-performance web-based ETL (Extract, Transform, Load) and visualization module designed to process, query, and extract large-scale Dutch administrative boundary polygons (Provincie, Gemeente, Buurt). 

Built as a standalone data-processing dashboard, this tool allows researchers to mount raw geospatial payloads, isolate specific geographic sectors via dynamic search or interactive rendering, and compile custom GeoJSON datasets for downstream quantitative analysis.

## Core Architecture

* **Backend Framework:** PHP 8.5 / Laravel
* **Frontend Engine:** Vanilla JavaScript, Leaflet.js (CartoDB Dark Matter tiles)
* **Data Structures:** GeoPackage (.gpkg), GeoJSON (Polygon / MultiPolygon geometries)
* **State Management:** Zero-latency UI updates reflecting backend directory states without full page reloads via HMR (Vite).

## Key Features

* **True Polygon Extraction:** Extracts full multi-point geometric boundaries to ensure zero data degradation and high-fidelity spatial exports for downstream quantitative models.
* **Dynamic Geometry Rendering:** Implements an adaptive scaling engine that adjusts vector line weights and opacity based on active zoom levels and layer types, preventing visual clutter when rendering thousands of high-density polygons.
* **Tactical UX/UI:** Dark-mode, data-dense interface utilizing Flexbox containment. Features non-obstructive hover tooltips, rapid search indexing, and a persistent selection log for continuous workflow.
* **Custom Compilation:** Users can queue multiple asynchronous spatial selections and compile them into a single, custom-named `.geojson` output file.

## Installation & Deployment

This application requires PHP 8.5+ and Node.js.

```bash
# 1. Clone the repository
git clone [https://github.com/rafterygp/jrcz-extraction-pipeline.git](https://github.com/rafterygp/jrcz-extraction-pipeline.git)
cd jrcz-extraction-pipeline

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Initialize local processing engines
php artisan serve
npm run dev