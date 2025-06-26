<?php 
session_start();
require_once __DIR__ . '/includes/config.php';
$currentPage = basename($_SERVER['PHP_SELF']);
// Fetch activities from database
$activities = [];
$query = "SELECT * FROM activities ORDER BY id DESC";
$result = $conn->query($query);
if ($result) {
    $activities = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities & Zero Emissions Travel | Greenourist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            padding-top: 70px; /* For fixed header */
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: linear-gradient(45deg, #2d506b, #5a8f3d);
            color: white;
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: background 0.3s ease;
        }

        .logo {
            width: 70px;
            height: auto;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #90ee90;
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: white;
            margin: 3px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        .mobile-menu {
            position: fixed;
            top: 70px;
            left: -100%;
            width: 100%;
            height: calc(100vh - 70px);
            background: linear-gradient(45deg, #2d506b, #5a8f3d);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 2rem;
            transition: left 0.3s ease;
            z-index: 999;
            overflow-y: auto;
        }

        .mobile-menu.active {
            left: 0;
        }

        .mobile-menu a {
            color: white;
            text-decoration: none;
            padding: 1rem;
            font-size: 1.2rem;
            width: 100%;
            text-align: center;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #f0f8f0, #e8f5e8);
            padding: 6rem 0 4rem;
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: #2d5016;
            margin-bottom: 1rem;
        }

        .page-header p {
            font-size: 1.2rem;
            color: #4a7c25;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Section Styles */
        section {
            padding: 3rem 0;
        }

        h2 {
            text-align: center;
            font-size: 2.2rem;
            margin-bottom: 2rem;
            color: #2d5016;
        }

        h3 {
            color: #4a7c25;
            margin-bottom: 1rem;
        }

        /* Filters Section */
        .filters-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .filter-group {
            margin-bottom: 1rem;
        }

        .filter-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #4a5568;
        }

        .filter-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #f8fafc;
            font-size: 1rem;
        }

        .filter-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .filter-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .filter-btn.primary {
            background-color: #4CAF50;
            color: white;
        }

        .filter-btn.secondary {
            background-color: #f1f1f1;
            color: #333;
        }

        .starred-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
        }

        .starred-toggle.active {
            background-color: #fffaf0;
            border-color: #feebc8;
        }

        .star-icon {
            color: #e2e8f0;
        }

        .starred-toggle.active .star-icon {
            color: #ecc94b;
        }

        /* Activities Grid */
        .featured-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 215, 0, 0.9);
    color: #8b4513;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
    z-index: 1;
}

.activity-card {
    position: relative;
}
        .activities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .activity-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
             position: relative;
        }

        .activity-card:hover {
            transform: translateY(-5px);
        }

        .activity-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .activity-info {
            padding: 1.5rem;
        }

        .activity-info h3 {
            margin-bottom: 0.5rem;
            font-size: 1.3rem;
            
        }

        .activity-info p {
            color: #666;
            margin-bottom: 1rem;
                font-size: 0.95rem;
    line-height: 1.5;
        }

        .activity-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .difficulty {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .easy { background: #90ee90; color: #2d5016; }
        .moderate { background: #ffd700; color: #8b4513; }
        .challenging { background: #ff6b6b; color: white; }

        .duration {
            color: #4a7c25;
            font-weight: bold;
        }
        

        /* Map Section */
        .map-container {
            height: 400px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        .map-info {
            background: linear-gradient(135deg, #f0f8f0, #e8f5e8);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
        }

        /* Zero Emissions Section */
        .zero-emissions {
            background: linear-gradient(135deg, #f0f8f0, #e8f5e8);
        }

        .transport-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .transport-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .transport-card:hover {
            transform: translateY(-10px);
        }

        .transport-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4a7c25, #6ba832);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
        }

        .transport-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        /* Transport Tips */
        .transport-tips {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-top: 2rem;
            text-align: center;
        }

        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .tip-item {
            padding: 1rem;
        }

        .tip-item h4 {
            color: #4a7c25;
            margin-bottom: 0.5rem;
        }

        /* No Results Message */
        .no-results-message {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
            color: #6c757d;
            display: none;
            font-style: italic;
            border: 1px dashed #dee2e6;
        }

        /* Footer */
        footer {
            background: linear-gradient(45deg, #2d506b, #5a8f3d);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hamburger {
                display: flex;
            }

            .page-header {
                padding: 5rem 0 3rem;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.8rem;
            }

            .activities-grid,
            .transport-grid {
                grid-template-columns: 1fr;
            }

            .map-container {
                height: 300px;
            }

            .filter-actions {
                flex-direction: column;
            }

            .filter-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="container">
            <div class="logo-container">
                <a href="index.php" class="text-logo">
                    <img src="assets/images/Greenourist-logo.png" alt="Greenourist Logo" class="logo">
                </a> 
            </div>
            <div class="header-title">
                <h1>Greenourist</h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="#activities">Activities</a></li>
                <li><a href="#map">Map</a></li>
                <li><a href="#transport">Zero Emissions</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
        <div class="mobile-menu" id="mobileMenu">
            <a href="index.php">Home</a>
            <a href="#activities">Activities</a>
            <a href="#map">Map</a>
            <a href="#transport">Zero Emissions</a>
            <a href="contact.php">Contact</a>
        </div>
    </header>

    <main>
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1>🌱 Eco-Activities & Sustainable Travel</h1>
                <p>Discover amazing eco-friendly activities and learn about zero-emissions transportation options for your green adventures</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="container">
            <div class="filters-section">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="difficulty-filter" class="filter-label">Difficulty</label>
                        <select id="difficulty-filter" class="filter-select">
                            <option value="all">All Difficulties</option>
                            <option value="easy">Easy/Beginner</option>
                            <option value="moderate">Moderate/Intermediate</option>
                            <option value="hard">Hard/Advanced</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="time-filter" class="filter-label">Time</label>
                        <select id="time-filter" class="filter-select">
                            <option value="all">All Activities</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="past">Past</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Options</label>
                        <div class="starred-toggle" id="starred-filter">
                            <span class="star-icon">★</span> Starred Only
                        </div>
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button class="filter-btn secondary" id="clear-filters">Reset</button>
                    <button class="filter-btn primary" id="apply-filters">Apply Filters</button>
                </div>
            </div>
            
            <div class="no-results-message">
                No activities match your current filters.
            </div>
        </div>

        <!-- Activities Section -->
        <section id="activities" class="activities">
            <?php if ($activity['featured'] ?? 0): ?>
    <div class="featured-badge">
        <i class="fas fa-star"></i> Featured
    </div>
<?php endif; ?>
          <div class="container">
    <h2>Eco-Friendly Activities</h2>
    <div class="activities-grid">
          <div class="activity-card fade-in-up" data-difficulty="moderate" data-date="2023-12-15T09:00:00" data-starred="false">
                        <img src="https://cdn.pixabay.com/photo/2023/10/23/17/25/hike-8336525_640.jpg" alt="Forest hiking trail">
                        <div class="activity-info">
                            <h3>🥾 Forest Hiking Trails</h3>
                            <p>Explore pristine forest paths while learning about local ecosystem conservation and wildlife protection efforts.</p>
                            <div class="activity-details">
                                <span class="difficulty moderate">Moderate</span>
                                <span class="duration">2-6 hours</span>
                                <span class="activity-date" style="display:none">2023-12-15T09:00:00</span>
                            </div>
                        </div>
                    </div>

                    <div class="activity-card fade-in-up" data-difficulty="easy" data-date="2023-11-20T10:00:00" data-starred="false">
                        <img src="https://cdn.pixabay.com/photo/2017/09/20/15/49/bike-2769021_640.jpg" alt="Cycling through nature">
                        <div class="activity-info">
                            <h3>🚴‍♀️ Eco-Cycling Tours</h3>
                            <p>Join sustainable cycling adventures through scenic routes using electric bikes and eco-friendly equipment.</p>
                            <div class="activity-details">
                                <span class="difficulty easy">Easy</span>
                                <span class="duration">3-4 hours</span>
                                <span class="activity-date" style="display:none">2023-11-20T10:00:00</span>
                            </div>
                        </div>
                    </div>

                    <div class="activity-card fade-in-up" data-difficulty="challenging" data-date="2024-01-10T08:00:00" data-starred="false">
                        <img src="https://cdn.pixabay.com/photo/2020/10/12/19/10/mountaineers-5649828_640.jpg" alt="Mountain trekking">
                        <div class="activity-info">
                            <h3>⛰️ Sustainable Mountain Trekking</h3>
                            <p>Challenge yourself with high-altitude adventures while following Leave No Trace principles and supporting local guides.</p>
                            <div class="activity-details">
                                <span class="difficulty challenging">Challenging</span>
                                <span class="duration">Full Day</span>
                                <span class="activity-date" style="display:none">2024-01-10T08:00:00</span>
                            </div>
                        </div>
                    </div>

                    <div class="activity-card fade-in-up" data-difficulty="easy" data-date="2023-12-05T13:00:00" data-starred="false">
                        <img src="https://cdn.pixabay.com/photo/2022/04/18/19/19/forest-7141417_640.jpg" alt="Wildlife observation">
                        <div class="activity-info">
                            <h3>🦋 Wildlife Observation</h3>
                            <p>Participate in citizen science projects while observing and documenting local wildlife in their natural habitats.</p>
                            <div class="activity-details">
                                <span class="difficulty easy">Easy</span>
                                <span class="duration">2-3 hours</span>
                                <span class="activity-date" style="display:none">2023-12-05T13:00:00</span>
                            </div>
                        </div>
                    </div>

                    <div class="activity-card fade-in-up" data-difficulty="moderate" data-date="2024-02-15T15:00:00" data-starred="false">
                        <img src="https://cdn.pixabay.com/photo/2021/08/23/11/06/nature-6567542_640.jpg" alt="Eco camping">
                        <div class="activity-info">
                            <h3>🏕️ Eco-Camping Adventures</h3>
                            <p>Experience minimal-impact camping with solar-powered facilities and composting toilets in protected areas.</p>
                            <div class="activity-details">
                                <span class="difficulty moderate">Moderate</span>
                                <span class="duration">2-3 days</span>
                                <span class="activity-date" style="display:none">2024-02-15T15:00:00</span>
                            </div>
                        </div>
                    </div>

                    <div class="activity-card fade-in-up" data-difficulty="moderate" data-date="2023-11-30T11:00:00" data-starred="false">
                        <img src="https://cdn.pixabay.com/photo/2020/08/13/14/15/mountains-5485366_640.jpg" alt="River restoration">
                        <div class="activity-info">
                            <h3>🌊 River Restoration Projects</h3>
                            <p>Volunteer in hands-on conservation work including stream cleaning, native plant restoration, and water quality monitoring.</p>
                            <div class="activity-details">
                                <span class="difficulty moderate">Moderate</span>
                                <span class="duration">4-5 hours</span>
                                <span class="activity-date" style="display:none">2023-11-30T11:00:00</span>
                            </div>
                        </div>
                    </div>
        
        <?php if (count($activities) > 0): ?>
            <?php foreach ($activities as $activity): ?>
                <div class="activity-card fade-in-up" 
                     data-difficulty="<?= htmlspecialchars($activity['difficulty']) ?>" 
                     data-date="<?= date('Y-m-d\TH:i:s', strtotime($activity['created_at'] ?? 'now')) ?>"
                     data-starred="false">
                    <img src="<?= htmlspecialchars($activity['picture']) ?>" alt="<?= htmlspecialchars($activity['title']) ?>">
                    <div class="activity-info">
                        <h3><?= htmlspecialchars($activity['title']) ?></h3>
                        <p><?= htmlspecialchars($activity['description']) ?></p>
                        <div class="activity-details">
                            <span class="difficulty <?= htmlspecialchars($activity['difficulty']) ?>">
                                <?= ucfirst(htmlspecialchars($activity['difficulty'])) ?>
                                <?php if ($activity['difficulty'] === 'easy') echo '/Beginner'; ?>
                                <?php if ($activity['difficulty'] === 'moderate') echo '/Intermediate'; ?>
                                <?php if ($activity['difficulty'] === 'hard') echo '/Advanced'; ?>
                            </span>
                            <span class="duration"><?= htmlspecialchars($activity['duration'] ?? '2-4 hours') ?></span>
                            <span class="activity-date" style="display:none"><?= date('Y-m-d\TH:i:s', strtotime($activity['created_at'] ?? 'now')) ?></span>
                        </div>
                    </div>
                    <?php if ($activity['featured'] ?? 0): ?>
                        <div class="featured-badge">
                            <i class="fas fa-star"></i> Featured
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results-message" style="display: block;">
                No activities found. Please check back later.
            </div>
        <?php endif; ?>
    </div> 
</div> 
        </section>

        <!-- Map Section -->
        <section id="map" class="map-section">
            <div class="container">
                <h2>Activity Locations Map</h2>
                <div class="map-container">
                    <div id="map"></div>
                </div>
                <div class="map-info">
                    <h3>🗺️ Interactive Activity Map</h3>
                    <p>Explore the locations of our eco-friendly activities. Click on the markers to learn more about each destination and find the best zero-emissions transportation routes to reach them.</p>
                </div>
            </div>
        </section>

        <!-- Zero Emissions Transport Section -->
        <section id="transport" class="zero-emissions">
            <div class="container">
                <h2>🚌 Zero Emissions Transportation</h2>
                <div class="transport-grid">
                    <div class="transport-card fade-in-up">
                        <div class="transport-icon">🚲</div>
                        <img src="https://cdn.pixabay.com/photo/2022/01/19/19/41/san-francisco-6950837_640.jpg" alt="Electric bicycle">
                        <h3>Electric Bicycles</h3>
                        <p>Rent our fleet of electric bikes for easy, emission-free travel to nearby activity locations. Perfect for distances up to 30km with charging stations along routes.</p>
                    </div>

                    <div class="transport-card fade-in-up">
                        <div class="transport-icon">🚌</div>
                        <img src="https://cdn.pixabay.com/photo/2020/04/13/00/00/public-transport-5036202_640.jpg" alt="Electric bus">
                        <h3>Electric Public Transport</h3>
                        <p>Use our comprehensive electric bus network connecting all major eco-activity hubs. Buses run on renewable energy and operate every 30 minutes.</p>
                    </div>

                    <div class="transport-card fade-in-up">
                        <div class="transport-icon">🚂</div>
                        <img src="https://cdn.pixabay.com/photo/2020/05/30/01/03/shinkansen-5237269_640.jpg" alt="Electric train">
                        <h3>High-Speed Electric Trains</h3>
                        <p>Travel longer distances using our electric rail network powered by 100% renewable energy. Connects major regions with minimal environmental impact.</p>
                    </div>

                    <div class="transport-card fade-in-up">
                        <div class="transport-icon">🚶‍♀️</div>
                        <img src="https://cdn.pixabay.com/photo/2021/08/23/08/28/path-6567149_640.jpg" alt="Walking paths">
                        <h3>Walking & Hiking Paths</h3>
                        <p>Access many activities through our extensive network of walking and hiking trails. Well-marked paths with rest stops and educational signage.</p>
                    </div>

                    <div class="transport-card fade-in-up">
                        <div class="transport-icon">🛶</div>
                        <img src="https://cdn.pixabay.com/photo/2020/08/13/14/15/mountains-5485366_640.jpg" alt="Water transport">
                        <h3>Solar-Powered Water Transport</h3>
                        <p>For waterfront activities, use our solar-powered boats and kayaks. Silent operation preserves wildlife while providing access to aquatic eco-adventures.</p>
                    </div>

                    <div class="transport-card fade-in-up">
                        <div class="transport-icon">🚐</div>
                        <img src="https://cdn.pixabay.com/photo/2024/03/02/07/09/car-8607713_640.jpg" alt="Electric vehicle">
                        <h3>Electric Vehicle Shuttles</h3>
                        <p>For remote locations, join our electric shuttle service. Vehicles charged using on-site solar panels and equipped with nature guides.</p>
                    </div>
                </div>

                <!-- Transport Tips -->
                <div class="transport-tips">
                    <h3>🌱 Transportation Tips</h3>
                    <div class="tips-grid">
                        <div class="tip-item">
                            <h4>📱 Plan Your Route</h4>
                            <p>Use our mobile app to plan the most efficient zero-emissions route to your activities.</p>
                        </div>
                        <div class="tip-item">
                            <h4>🎫 Multi-Modal Tickets</h4>
                            <p>Purchase combination tickets covering all transport modes for seamless, eco-friendly travel.</p>
                        </div>
                        <div class="tip-item">
                            <h4>⚡ Charging Stations</h4>
                            <p>Find electric vehicle charging stations powered by renewable energy at all major activity sites.</p>
                        </div>
                        <div class="tip-item">
                            <h4>🌿 Carbon Offset</h4>
                            <p>Any unavoidable emissions are automatically offset through verified reforestation projects.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>© 2024 Greenourist - Sustainable Activities & Zero Emissions Transportation 🌍</p>
            <p>Exploring nature responsibly, one adventure at a time</p>
        </div>
    </footer>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Initialize the map
        const map = L.map('map').setView([31.7917, -7.0926], 6); // Centered on Morocco

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Activity locations with coordinates
        const activities = [
            {
                name: "🥾 Forest Trails",
                coords: [31.246205, -7.978673],
                description: "Ancient forest hiking trails with guided eco-tours and wildlife observation points.",
                transport: "Nomads horse caravan + 6km hiking trail"
            },
            {
                name: "🚴‍♀️ Mountain Bike Loop Trail",
                coords: [33.484850, -5.132117],
                description: "Scenic cycling path for mountain bikes through varied terrain.",
                transport: "Bike rental available on-site"
            },
            {
                name: "⛰️ Jbel Mousa (839m)",
                coords: [35.898945, -5.412950],
                description: "This scenic trail ascends a lush, forested mountain with vibrant greenery and panoramic views.",
                transport: "Bikes or local horse rides available"
            }
        ];

        // Custom green icon for map markers
        const greenIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Add markers for each activity
        activities.forEach(activity => {
            if (activity.coords && activity.coords.length === 2) {
                const marker = L.marker(activity.coords, {icon: greenIcon}).addTo(map);
                
                const popupContent = `
                    <div style="font-family: Arial, sans-serif; max-width: 250px;">
                        <h3 style="color: #2d5016; margin: 0 0 10px 0; font-size: 14px;">${activity.name}</h3>
                        <p style="margin: 0 0 8px 0; font-size: 12px; line-height: 1.4;">${activity.description}</p>
                        <div style="background: #f0f8f0; padding: 8px; border-radius: 5px; font-size: 11px;">
                            <strong style="color: #4a7c25;">🚌 Transport:</strong> ${activity.transport}
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent);
            }
        });

        // Mobile menu toggle
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');

        hamburger.addEventListener('click', () => {
            const isActive = hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            document.body.style.overflow = isActive ? 'hidden' : '';
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const difficultyFilter = document.getElementById('difficulty-filter');
            const timeFilter = document.getElementById('time-filter');
            const starredFilter = document.getElementById('starred-filter');
            const applyBtn = document.getElementById('apply-filters');
            const clearBtn = document.getElementById('clear-filters');
            const noResultsMsg = document.querySelector('.no-results-message');
            
            let currentFilters = {
                difficulty: 'all',
                time: 'all',
                starredOnly: false
            };
            
            const difficultyMap = {
                'easy': ['easy', 'beginner'],
                'moderate': ['moderate', 'intermediate'],
                'hard': ['hard', 'advanced', 'challenging']
            };
            
            // Apply filters when button is clicked
            applyBtn.addEventListener('click', applyFilters);
            
            // Clear all filters
            clearBtn.addEventListener('click', function() {
                difficultyFilter.value = 'all';
                timeFilter.value = 'all';
                starredFilter.classList.remove('active');
                currentFilters = {
                    difficulty: 'all',
                    time: 'all',
                    starredOnly: false
                };
                applyFilters();
            });
            
            // Starred filter toggle
            starredFilter.addEventListener('click', function() {
                currentFilters.starredOnly = !currentFilters.starredOnly;
                this.classList.toggle('active');
                applyFilters();
            });
            
            // Dropdown change handlers
            difficultyFilter.addEventListener('change', function() {
                currentFilters.difficulty = this.value;
            });
            
            timeFilter.addEventListener('change', function() {
                currentFilters.time = this.value;
            });
            
            // Main filter function
            function applyFilters() {
                const activities = document.querySelectorAll('.activity-card');
                const now = new Date();
                let hasVisibleActivities = false;
                
                activities.forEach(activity => {
                    const difficulty = activity.dataset.difficulty.toLowerCase();
                    const date = new Date(activity.dataset.date || activity.querySelector('.activity-date').textContent);
                    const isStarred = activity.dataset.starred === 'true';
                    
                    // Check difficulty match
                    const difficultyMatch = currentFilters.difficulty === 'all' || 
                        (difficultyMap[currentFilters.difficulty] && 
                         difficultyMap[currentFilters.difficulty].includes(difficulty));
                    
                    // Check time filter
                    let timeMatch = true;
                    if (currentFilters.time === 'upcoming') {
                        timeMatch = date > now;
                    } else if (currentFilters.time === 'past') {
                        timeMatch = date < now;
                    }
                    
                    // Check starred filter
                    const starredMatch = !currentFilters.starredOnly || isStarred;
                    
                    // Determine visibility
                    const shouldShow = difficultyMatch && timeMatch && starredMatch;
                    activity.style.display = shouldShow ? 'block' : 'none';
                    
                    if (shouldShow) hasVisibleActivities = true;
                });
                
                // Show/hide no activities message
                noResultsMsg.style.display = hasVisibleActivities ? 'none' : 'block';
            }
            
            // Initialize
            applyFilters();
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all elements with fade-in-up class
        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });

        // Header background change on scroll
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'linear-gradient(45deg, #2d506b, #5a8f3d)';
            } else {
                header.style.background = 'linear-gradient(45deg, #2d506b, #5a8f3d)';
            }
        });
    </script>
</body>
</html>