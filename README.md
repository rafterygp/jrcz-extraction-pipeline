# GeoJSON Spatial Delivery API & Interactive Map

An interactive, web engine for visualizing, managing, and extracting Dutch authoritative geographical boundaries. 



* **Personalized extraction:** Extract you selection of political boundaries and recieve coordinate based GeoJson polygons for your personalized selection  

## Tech Stack

* **Backend:** PHP / Laravel (Spatial Delivery API & File Routing)
* **Frontend:** JavaScript (Vite), HTML5, CSS3
* **Data Source:** PDOK / CBS Wijk- en Buurtkaart 


## Local Installation

1. Clone the repository: `git clone https://github.com/yourusername/geojson-spatial-api.git`
2. Install PHP dependencies: `composer install`
3. Install frontend dependencies: `npm install`
4. Ensure your `public/geopackages` directory contains the required CBS `.geojson` files.
5. Boot the server: `php artisan serve`
6. Run the Vite build: `npm run dev`
7. Run tests: `php artisan test`
