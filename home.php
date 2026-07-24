<!DOCTYPE html>
<html>
<head>
    <title>HMPI System – Home</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Poppins, sans-serif;

            /* Beautiful groundwater / environment background */
            background-image: url('https://images.unsplash.com/photo-1502786129293-79981df4e689');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* Navigation Bar */
        .topnav {
            background: rgba(47, 78, 184, 0.9);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: white;
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

        .title-box {
            margin-top: 140px;
            text-align: center;
            background: rgba(255, 255, 255, 0.85);
            display: inline-block;
            padding: 25px 40px;
            border-radius: 15px;
            box-shadow: 0 0 12px rgba(0,0,0,0.25);
        }

        h1 {
            margin: 0;
            color: #1e2a52;
            font-size: 36px;
            font-weight: 700;
        }

        h3 {
            margin-top: 10px;
            color: #333;
            font-weight: 500;
        }

        .home-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 22px;
            background: #2f4eb8;
            color: white;
            font-size: 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        .home-btn:hover {
            background: #1b2f80;
        }

        .center-container {
            width: 100%;
            text-align: center;
        }
    </style>
</head>

<body>

<!-- NAVIGATION BAR -->
<div class="topnav">
    <a href="login.php">➤ login</a>
    <a href="home.php">➤ Home</a>
    <a href="input.php">➤ Input</a>
    <a href="map.php">➤ Map</a>
    <a href="report.php">➤ Report</a>
</div>


<div class="center-container">
    <div class="title-box">
        <h1>HMPI – Heavy Metal Pollution Index</h1>
        <h3>Smart Groundwater Quality Assessment & Geo-Mapping System</h3>

        <a class="home-btn" href="input.php">Start Entering Data ➤</a>
    </div>
</div>

</body>
</html>

