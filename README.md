# HMPI – Heavy Metal Pollution Index Monitoring System

A web-based application for monitoring and visualizing groundwater pollution using the Heavy Metal Pollution Index (HMPI). The system allows users to enter groundwater sample data, automatically calculates the HMPI, classifies pollution levels, and displays the results on an interactive map and detailed report.

---

## Features

- User Login Page
- Home Page
- Input groundwater sample details
- Automatic HMPI calculation
- Pollution level classification
  - Low Pollution
  - Moderate Pollution
  - High Pollution
- Interactive Leaflet Map
- Search locations on map
- Fullscreen map support
- Pollution markers with color coding
- Detailed pollution report
- Reverse geocoding to display location names
- Store sample and metal data in MySQL database
- Add multiple groundwater samples at once
- Responsive and user-friendly interface

---

## Technologies Used

### Frontend
- HTML5
- CSS3
- JavaScript

### Backend
- PHP

### Database
- MySQL

### Libraries
- Leaflet.js
- OpenStreetMap
- Leaflet Control Geocoder

---

## Project Structure

```
HMPI/
│
├── login.php
├── home.php
├── input.php
├── map.php
├── report.php
├── README.md
```

---

## HMPI Calculation

The Heavy Metal Pollution Index is calculated using the concentration of heavy metals in groundwater samples.

### Heavy Metals Used

- Lead (Pb)
- Arsenic (As)
- Mercury (Hg)
- Iron (Fe)

### Formula

```
Qi = (Measured Concentration / Permissible Limit) × 100

HMPI = (Σ Qi) / Number of Metals
```

---

## Pollution Classification

| HMPI Range | Pollution Level |
|------------|-----------------|
| Less than 50 | Low Pollution |
| 50 to 100 | Moderate Pollution |
| Greater than 100 | High Pollution |

---

## Database

**Database Name**

```
hmpi_system
```

### Tables

#### samples

Stores:

- Latitude
- Longitude
- HMPI Value
- Pollution Level
- Marker Color
- Created Date & Time

#### metals

Stores:

- Sample ID
- Metal Name
- Concentration
- Permissible Limit

---

## Installation

### Step 1

Install XAMPP.

### Step 2

Copy the project folder into:

```
C:\xampp\htdocs\
```

Example:

```
C:\xampp\htdocs\HMPI\
```

### Step 3

Start **Apache** and **MySQL** from the XAMPP Control Panel.

### Step 4

Open phpMyAdmin:

```
http://localhost/phpmyadmin
```

Create a database named:

```
hmpi_system
```

Import the SQL database file.

### Step 5

Run the project:

```
http://localhost/HMPI/home.php
```

or

```
http://localhost/HMPI/login.php
```

---

## Project Workflow

```
Login
   ↓
Home
   ↓
Input Groundwater Sample
   ↓
HMPI Calculation
   ↓
Store Data in MySQL
   ↓
Interactive Pollution Map
   ↓
Generate Report
```

---

## Interactive Map Features

- OpenStreetMap integration
- Automatic marker placement
- Color-coded pollution markers
- Location search
- Fullscreen mode
- Zoom controls
- Marker popup displaying:
  - Latitude
  - Longitude
  - HMPI Value
  - Pollution Level

---

## Report Features

- Sample ID
- Date and Time
- Latitude and Longitude
- Location Name
- HMPI Value
- Pollution Level
- Heavy Metal Details

---

## Future Enhancements

- Admin Dashboard
- User Authentication
- Export Reports to PDF
- Charts and Data Analytics
- Historical Data Comparison
- Water Quality Trend Analysis
- AI-based Pollution Prediction
- Mobile Responsive Design
- Email Notifications
- Real-time Sensor Integration

---

## Author

**Sharada Sunadolli**

Bachelor of Engineering (Computer Science and Engineering)

Jain College of Engineering and Research

---

## License

This project is developed for educational and research purposes.
