<?php
// ---------------- DATABASE CONNECTION ----------------
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hmpi_system";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("DB ERROR: " . $conn->connect_error); }

// Fetch samples
$samples = $conn->query("SELECT * FROM samples ORDER BY id DESC");

// Fetch metal details
$metalData = [];
$m = $conn->query("SELECT * FROM metals");
while ($row = $m->fetch_assoc()) {
    $metalData[$row['sample_id']][] = $row;
}

// SHORT LOCATION NAME FUNCTION (avoid long display_name)
function getShortLocation($lat, $lon) {
    $url = "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lon&format=json";
    $opts = ["http" => ["header" => "User-Agent: HMPI-System"]];
    $json = @file_get_contents($url, false, stream_context_create($opts));

    if (!$json) return "Unknown";

    $d = json_decode($json, true);

    // SHORT FORM: Village, Taluk, District, State
    $addr = $d['address'] ?? [];
    $parts = [];

    if(isset($addr['village'])) $parts[] = $addr['village'];
    elseif(isset($addr['town'])) $parts[] = $addr['town'];
    elseif(isset($addr['suburb'])) $parts[] = $addr['suburb'];

    if(isset($addr['county'])) $parts[] = $addr['county'];
    if(isset($addr['state_district'])) $parts[] = $addr['state_district'];
    if(isset($addr['state'])) $parts[] = $addr['state'];

    return implode(", ", $parts);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>HMPI Report</title>

<style>
    body {
        font-family: Poppins, sans-serif;
        background: #eef2ff;
        margin: 0;
        padding: 0;
    }

    /* NAVIGATION BAR */
    .navbar {
        background: #3446c4;
        padding: 14px 24px;
        color: white;
        display: flex;
        align-items: center;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        margin-right: 22px;
        font-size: 18px;
        font-weight: 600;
    }

    .navbar a:hover { text-decoration: underline; }

    h2 { text-align: center; margin-top: 20px; }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        margin-top: 20px;
        white-space: nowrap; /* 🚀 PREVENT WRAPPING */
    }

    th {
        background: #3446c4;
        color: white;
        padding: 12px;
        font-size: 15px;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #dcdcdc;
        font-size: 14px;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 6px;
        color: white;
        font-weight: 600;
    }

    .green { background: #4caf50; }
    .orange { background: #fb8c00; }
    .red { background: #e53935; }

    .metal-box {
        background: #f5f7ff;
        padding: 6px 10px;
        margin-bottom: 6px;
        border-radius: 6px;
        font-size: 13px;
    }
</style>
</head>

<body>

<!-- NAVIGATION BAR -->
<div class="navbar">
    <a href="login.php">➤ login</a>
    <a href="home.php">➤ Home</a>
    <a href="input.php">➤ Input</a>
    <a href="map.php">➤ Map</a>
    <a href="report.php">➤ Report</a>
</div>

<h2>HMPI Pollution Report</h2>

<table>
<tr>
    <th>ID</th>
    <th>Date</th>
    <th>Time</th>
    <th>Latitude</th>
    <th>Longitude</th>
    <th>📍 Location</th>
    <th>HMPI</th>
    <th>Pollution Level</th>
    <th>Metals</th>
</tr>

<?php while($row = $samples->fetch_assoc()): ?>
<?php
    $lat = $row['latitude'];
    $lon = $row['longitude'];
    $locationShort = getShortLocation($lat, $lon); // short location
?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= date("d-m-Y", strtotime($row['created_at'])) ?></td>
    <td><?= date("h:i A", strtotime($row['created_at'])) ?></td>
    <td><?= $lat ?></td>
    <td><?= $lon ?></td>
    <td><b><?= $locationShort ?></b></td>
    <td><b><?= round($row['hmpi'], 2) ?></b></td>

    <!-- Pollution Level Badge -->
    <td>
        <span class="badge <?= strtolower($row['color']) ?>">
            <?= $row['pollution_level'] ?>
        </span>
    </td>

    <td>
        <?php if(isset($metalData[$row['id']])): ?>
            <?php foreach($metalData[$row['id']] as $m): ?>
                <div class="metal-box">
                    <b><?= $m['metal_name'] ?></b>: 
                    <?= $m['concentration'] ?> mg/L 
                    (Limit <?= $m['permissible_limit'] ?>)
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            No Data
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
