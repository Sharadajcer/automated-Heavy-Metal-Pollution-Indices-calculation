<?php
// ---------------- DATABASE CONNECTION ----------------
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hmpi_system";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// ---------------- METAL LIMITS ----------------
// These limits are used ONLY for HMPI calculation.
// User can enter ANY value.
$permissibleLimits = [
    "Lead" => 0.01,
    "Arsenic" => 0.01,
    "Mercury" => 0.001,
    "Iron" => 0.3
];

$saveError = "";
$saveSuccess = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ensure posted arrays exist
    if (!isset($_POST['latitude']) || !isset($_POST['longitude'])) {
        $saveError = "Latitude/Longitude data missing.";
    } else {
        $latitudes = $_POST['latitude'];
        $longitudes = $_POST['longitude'];

        // Ensure both are arrays and same length
        if (!is_array($latitudes) || !is_array($longitudes) || count($latitudes) !== count($longitudes)) {
            $saveError = "Latitude and Longitude inputs are invalid or mismatched.";
        } else {
            // Start DB transaction for safety (optional)
            $conn->begin_transaction();

            try {
                $samplesInserted = 0;

                $numSamples = count($latitudes);
                for ($index = 0; $index < $numSamples; $index++) {
                    $lat = isset($latitudes[$index]) ? floatval($latitudes[$index]) : 0.0;
                    $lng = isset($longitudes[$index]) ? floatval($longitudes[$index]) : 0.0;

                    $totalSubIndex = 0.0;
                    $metalCount = count($permissibleLimits);

                    // CALCULATE HMPI (even if values exceed limits)
                    foreach ($permissibleLimits as $metal => $limit) {
                        // If metal input missing for this index, treat as 0
                        $conc = 0.0;
                        if (isset($_POST[$metal]) && is_array($_POST[$metal]) && array_key_exists($index, $_POST[$metal])) {
                            $conc = floatval($_POST[$metal][$index]);
                        }
                        // Sub-index formula (guard for limit == 0)
                        if ($limit == 0) {
                            $Q = 0;
                        } else {
                            $Q = ($conc / $limit) * 100;
                        }
                        $totalSubIndex += $Q;
                    }

                    // avoid division by zero
                    if ($metalCount === 0) {
                        $hmpi = 0;
                    } else {
                        $hmpi = $totalSubIndex / $metalCount;
                    }

                    // Pollution Classification
                    if ($hmpi < 50) {
                        $level = "Low Pollution";
                        $color = "green";
                    } elseif ($hmpi < 100) {
                        $level = "Moderate Pollution";
                        $color = "orange";
                    } else {
                        $level = "High Pollution";
                        $color = "red";
                    }

                    // Save to samples table
                    $stmt = $conn->prepare("INSERT INTO samples (latitude, longitude, hmpi, pollution_level, color) 
                                            VALUES (?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        throw new Exception("Prepare failed (samples): " . $conn->error);
                    }
                    if (!$stmt->bind_param("dddss", $lat, $lng, $hmpi, $level, $color)) {
                        throw new Exception("Bind failed (samples): " . $stmt->error);
                    }
                    if (!$stmt->execute()) {
                        throw new Exception("Execute failed (samples): " . $stmt->error);
                    }

                    $sampleID = $stmt->insert_id;
                    $stmt->close();

                    // Save metal values
                    foreach ($permissibleLimits as $metal => $limit) {
                        $conc = 0.0;
                        if (isset($_POST[$metal]) && is_array($_POST[$metal]) && array_key_exists($index, $_POST[$metal])) {
                            $conc = floatval($_POST[$metal][$index]);
                        }
                        $stmt2 = $conn->prepare("INSERT INTO metals (sample_id, metal_name, concentration, permissible_limit) 
                                                 VALUES (?, ?, ?, ?)");
                        if (!$stmt2) {
                            throw new Exception("Prepare failed (metals): " . $conn->error);
                        }
                        if (!$stmt2->bind_param("isdd", $sampleID, $metal, $conc, $limit)) {
                            throw new Exception("Bind failed (metals): " . $stmt2->error);
                        }
                        if (!$stmt2->execute()) {
                            throw new Exception("Execute failed (metals): " . $stmt2->error);
                        }
                        $stmt2->close();
                    }

                    $samplesInserted++;
                } // end for each sample

                $conn->commit();
                $saveSuccess = "Saved $samplesInserted sample(s) successfully.";
                // redirect to map
                header("Location: map.php");
                exit;

            } catch (Exception $e) {
                $conn->rollback();
                $saveError = "Failed to save: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>HMPI Input</title>
<style>
   body {
    font-family: Poppins, sans-serif;
    margin: 0;
    padding: 0;
    background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}
body::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(255,255,255,0.55);
    backdrop-filter: blur(2px);
    z-index: -1;
}
.navbar {
    width: 100%;
    background: #0a3d62;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    color: white;
    font-size: 18px;
}
.navbar a {
    color: white;
    margin-right: 20px;
    text-decoration: none;
    font-weight: 600;
}
h2 { color: #0a3d62; margin-top: 20px; }

form {
    background: rgba(255, 255, 255, 0.90);
    padding: 25px;
    border-radius: 12px;
    width: 65%;
    margin-top: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
label { display: inline-block; margin: 8px; font-weight: 600; }
input {
    padding: 7px;
    border-radius: 6px;
    border: 1px solid #aaa;
    width: 140px;
}
.message { margin: 10px 0; padding: 8px 12px; border-radius:6px; width: 65%; }
.message.error { background: #fdecea; color: #b71c1c; border:1px solid #f5c6cb; }
.message.success { background: #e8f5e9; color: #256029; border:1px solid #c3e6cb; }
button, input[type=submit] {
    padding: 10px 18px;
    background: #0a3d62;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 15px;
    font-weight: 600;
}
.sample {
    padding: 12px;
    margin-bottom: 8px;
    border-bottom: 1px dashed #666;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <a href="login.php">➤ login</a>
    <a href="home.php">➤ Home</a>
    <a href="input.php">➤ Input</a>
    <a href="map.php">➤ Map</a>
    <a href="report.php">➤ Report</a>
</div>

<h2>Enter HMPI Sample Data</h2>

<?php if ($saveError): ?>
    <div class="message error"><?= htmlspecialchars($saveError) ?></div>
<?php elseif ($saveSuccess): ?>
    <div class="message success"><?= htmlspecialchars($saveSuccess) ?></div>
<?php endif; ?>

<form method="post">

    <div id="samples">
        <div class="sample">
            <label>Latitude:
                <input type="number" step="0.000001" name="latitude[]" required>
            </label>

            <label>Longitude:
                <input type="number" step="0.000001" name="longitude[]" required>
            </label>

            <br><br>

            <?php foreach ($permissibleLimits as $metal => $limit): ?>
                <label><?= $metal ?>:
                    <input type="number" step="0.001" name="<?= $metal ?>[]" required>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <button type="button" onclick="addSample()">Add Another Sample</button>
    <br>
    <input type="submit" value="Save & View Map">

</form>

<script>
// Add another sample dynamically
function addSample() {
    let div = document.createElement("div");
    div.classList.add("sample");

    div.innerHTML = `
        <label>Latitude:
            <input type="number" step="0.000001" name="latitude[]" required>
        </label>

        <label>Longitude:
            <input type="number" step="0.000001" name="longitude[]" required>
        </label>

        <br><br>

        <?php foreach ($permissibleLimits as $metal => $limit): ?>
            <label><?= $metal ?>:
                <input type="number" step="0.001" name="<?= $metal ?>[]" required>
            </label>
        <?php endforeach; ?>
        <hr>
    `;

    document.getElementById("samples").appendChild(div);
}
</script>

</body>
</html>
