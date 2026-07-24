<?php
$conn = new mysqli("localhost", "root", "", "hmpi_system");
$data = [];

$result = $conn->query("SELECT * FROM samples");

if (!$result) {
    die("Query Failed: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>HMPI Smart Geo Map</title>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Leaflet Fullscreen -->
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css"/>
    <script src="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js"></script>

    <!-- Search Bar -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <style>
        /* NAVIGATION BAR */
        .topnav {
            background: #2f4eb8;
            padding: 14px 22px;
            display: flex;
            align-items: center;
            font-family: Poppins;
        }
        .topnav a {
            color: white;
            text-decoration: none;
            margin-right: 25px;
            font-size: 18px;
            font-weight: 600;
        }
        .topnav a:hover {
            text-decoration: underline;
        }

        body {
            font-family: Poppins;
            margin: 0;
            background: #eef3ff;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #2b3a67;
            font-weight: 600;
        }

        #map {
            height: 560px;
            width: 90%;
            margin: 20px auto;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0,0,0,0.15);
        }

        .legend {
            background: white;
            padding: 10px;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.6;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }

        .legend span {
            height: 12px;
            width: 12px;
            display: inline-block;
            margin-right: 6px;
            border-radius: 50%;
        }
    </style>
</head>

<body>

<!-- NAVIGATION BAR -->
<div class="topnav">
    <a href="login.php">➤ Login</a>
    <a href="home.php">➤ Home</a>
    <a href="input.php">➤ Input</a>
    <a href="map.php">➤ Map</a>
    <a href="report.php">➤ Report</a>
</div>

<h2>HMPI Interactive Pollution Map</h2>

<div id="map"></div>

<script>
var map = L.map('map', {
    fullscreenControl: true,
    zoomControl: true,
    scrollWheelZoom: true,     // Smooth zoom
    smoothWheelZoom: true,     // Much smoother zooming
    smoothSensitivity: 1
});

// Base layer - smooth loading & high quality
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

// Search bar
L.Control.geocoder().addTo(map);

// Load PHP data
var samples = <?php echo json_encode($data); ?>;

var bounds = [];

// Add markers
samples.forEach(s => {
    var lat = parseFloat(s.latitude);
    var lng = parseFloat(s.longitude);

    if (!isNaN(lat) && !isNaN(lng)) {
        bounds.push([lat, lng]);

        L.circleMarker([lat, lng], {
            color: s.color,
            radius: 12,
            weight: 2,
            fillOpacity: 0.85
        }).addTo(map).bindPopup(`
            <b>📍 Location</b><br>
            Latitude: ${lat}<br>
            Longitude: ${lng}<br><br>
            <b>HMPI:</b> <span style="color:${s.color}; font-weight:600;">${s.hmpi}</span><br>
            <b>Pollution Level:</b> ${s.pollution_level}
        `);
    }
});

// Auto-fit map to all markers
if (bounds.length > 0) {
    map.fitBounds(bounds, { padding: [50, 50] });
} else {
    map.setView([20.5, 78.9], 5); // fallback
}

// Legend
var legend = L.control({position: "bottomright"});
legend.onAdd = function () {
    var div = L.DomUtil.create("div", "legend");
    div.innerHTML = `
        <b>Pollution Level</b><br>
        <span style="background:green;"></span> Low<br>
        <span style="background:orange;"></span> Moderate<br>
        <span style="background:red;"></span> High
    `;
    return div;
};
legend.addTo(map);
</script>

</body>
</html>
