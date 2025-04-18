<?php
// index.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Travel Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Web Layout</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link href="https://api.fontshare.com/v2/css?f[]=switzer@100,101,200,201,300,301,400,401,500,501,600,601,700,701,800,801,900,901,1,2&display=swap" rel="stylesheet">
    <style>
        .subheader2 {
            font-size: 14px;
            color: rgb(241, 241, 241);
        }   

        .blog-header2 {
            font-size: 28px; /* Increase the font size */
            font-weight: bold;
            margin: 0;
            color: white; /* Ensure the header text is white */
        }

        .arrow {
            filter: invert(100%);
        }
    </style>
</head>
<body>

    <!-- Include shared header -->
    <?php include 'header.php'; ?>

    <!-- Hero Image -->
    <div class="hero-image">
        <img src="images/header.JPG" alt="Hero Image">
        <div class="hero-content">
            <h1>Discover Iceland</h1>
            <p>Land of Fire, Ice, and Unforgettable Adventures</p>
        </div>
    </div>
    
    <!-- Information Section -->
    <div class="information rounded-section">
        <h2 class="section-title">How We Help You</h2>
        <p class="section-subtitle">Essential travel resources, guides, and tips for your next adventure</p>
        <div class="offers-container">
            <div class="offer-item">
                <div class="offer-icon"><img src="icons/location-pin-solid.svg" alt="Icon"></div>
                <h3>Ready-Made Plans</h3>
                <p>Access detailed itineraries to make exploring your dream destinations effortless.</p>
            </div>
            <div class="offer-item">
                <div class="offer-icon"><img src="icons/plane-solid.svg" alt="Icon"></div>
                <h3>Practical Travel Advice</h3>
                <p>Find helpful tips on budgeting, packing, and stress-free travel preparation.</p>
            </div>
            <div class="offer-item">
                <div class="offer-icon"><img src="icons/bed-solid.svg" alt="Icon"></div>
                <h3>Honest Place Reviews</h3>
                <p>Read trustworthy insights on hotels, landmarks, and local attractions.</p>
            </div>
            <div class="offer-item">
                <div class="offer-icon"><img src="icons/language-solid.svg" alt="Icon"></div>
                <h3>Language Cheat Sheets</h3>
                <p>Learn key phrases to communicate confidently and connect with locals.</p>
            </div>
        </div>
    </div>
    
    <!-- Blogs Container -->
    <div class="blogs-container">
        <!-- Left Big Box -->
        <div class="blog-left">
            <div class="image">
                <!-- Make sure the file name "singapoure.JPG" exactly matches the file on your server -->
                <img src="images/singapoure.JPG" alt="Blog Image">
            </div>
            <div class="content">
                <div class="blog-header2">Singapore</div>
                <div class="subheader2">Where modernity meets culture</div>
            </div>
            <div class="arrow">
                <img src="icons/arrow-right-solid.svg" alt="Arrow">
            </div>
        </div>
    
        <!-- Right Column -->
        <div class="blog-right">
            <!-- Top Wide Box -->
            <div class="top-box">
                <div class="content">
                    <div class="blog-header2">Madagascar</div>
                    <div class="subheader2">Explore unique landscapes</div>
                </div>
                <div class="image">
                    <!-- Ensure "savana.JPG" is the correct filename -->
                    <img src="images/savana.JPG" alt="Blog Image">
                </div>
                <div class="arrow">
                    <img src="icons/arrow-right-solid.svg" alt="Arrow">
                </div>
            </div>
    
            <!-- Bottom Two Boxes -->
            <div class="bottom-boxes">
                <div class="bottom-box">
                    <div class="content">
                        <div class="blog-header2">China</div>
                        <div class="subheader2">Experience ancient wonders</div>
                    </div>
                    <div class="image">
                        <!-- Ensure "china.JPG" exactly matches -->
                        <img src="images/china.JPG" alt="Blog Image">
                    </div>
                    <div class="arrow">
                        <img src="icons/arrow-right-solid.svg" alt="Arrow">
                    </div>
                </div>
                <div class="bottom-box see-more">
                    <div class="content">
                        <span>See more</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Image with Text Section -->
    <div class="image-with-text">
        <img src="images/secondary-image.jpg" alt="Background Image">
        <div class="content">
            <h2>Majestic Mount Fuji</h2>
            <button>See More</button>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <h2>Travel Blog<span>*</span></h2>
                <p>A place where nature and adventure unite</p>
            </div>
            <div class="footer-links">
                <div class="footer-column">
                    <h3>About us</h3>
                    <ul>
                        <li><a href="#">Our guides</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Contact us</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>FAQ</h3>
                    <ul>
                        <li><a href="#">Personal trip</a></li>
                        <li><a href="#">Group trip</a></li>
                        <li><a href="#">Tour payment</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <hr>
        <div class="footer-bottom">
            <p>Copyright 2025 Travel Blog - Plzeň, Czech Republic</p>
            <div class="footer-icons">
                <a href="#"><img src="icons/facebook-f-brands-solid.svg" alt=""></a>
                <a href="#"><img src="icons/instagram-brands-solid.svg" alt=""></a>
                <a href="#"><img src="icons/youtube-brands-solid.svg" alt=""></a>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
