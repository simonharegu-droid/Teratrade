# TerraTrade

TerraTrade is a concept crypto trading platform landing page delivered as a PHP site. It showcases a modern trading terminal, dynamic market data, pricing plans, education hub, and contact flows. The public site draws hero and contact copy from editable content managed through an admin dashboard.

## Getting started (XAMPP)

1. Copy this repository into your XAMPP `htdocs` directory (e.g. `C:/xampp/htdocs/TerraTrade`).
2. Start Apache from the XAMPP control panel.
3. Visit `http://localhost/TerraTrade/index.php` in your browser.
4. Ensure you have an internet connection to load the Chart.js CDN used for the portfolio chart.

### Alternative: PHP built-in server

If you have PHP installed locally you can run the project without XAMPP:

```bash
php -S localhost:8000
```

Then open `http://localhost:8000/index.php` in your browser.

## Admin controls

An admin dashboard is available at `http://localhost/TerraTrade/admin/` for updating the hero messaging, CTA links, and contact details without editing code.

- **Username:** `admin`
- **Password:** `TerraTrade!2024`

Changes are written to `data/site-content.json` and reflected immediately on the public site.

## Features

- Hero section with live-updating portfolio snapshot and interactive chart
- Real-time style market table with positive/negative change indicators
- Trading ticket with leverage controls, cost/PnL estimates, and synthetic order confirmations
- Synthetic order book depth module and curated market insight feed
- Feature, pricing, academy, testimonial, blog, CTA, and contact sections optimized for responsive layouts
- Editable hero and contact content managed through a PHP admin panel
- Toast notifications, animated navigation, and automatic footer year updates handled via vanilla JavaScript

## Tech stack

- Semantic HTML5
- Modern CSS3 with custom properties and responsive grid/flexbox layouts
- PHP 8 for templating and admin content management
- Vanilla JavaScript for interaction logic and simulated data updates
- [Chart.js](https://www.chartjs.org/) for portfolio value visualization
