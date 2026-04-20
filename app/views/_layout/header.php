<?php
/**
 * Header Layout - Navigation Menu
 * Dự án: MTech Website
 */

// Lấy trang hiện tại để xác định active menu
$currentPage = $_GET['page'] ?? 'home';
?>

<header class="main_menu_area">
    <nav class="navbar navbar-expand-lg navbar-light menu_absolute">
        <!-- Logo -->
        <a class="navbar-brand" href="./">
            <img src="assets/images/logo.png" alt="Wokrate Industrial">
        </a>
        
        <!-- Hamburger Menu Button for Mobile -->
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="menu_toggle">
                <span class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <span class="hamburger-cross">
                    <span></span>
                    <span></span>
                </span>
            </span>
        </button>
        
        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            
            <!-- Close Button (Mobile only) -->
            <button class="nav-close-btn" aria-label="Close menu">
                <span class="nav-close-arrow">&#8592;</span>
                <span class="nav-close-text">Back</span>
            </button>
            
            <ul class="navbar-nav menu">
                
                <!-- Home -->
                <li class="nav-item <?php echo ($currentPage === 'home') ? 'active' : ''; ?>">
                    <a class="nav-link" href="./" title="Home">Home</a>
                </li>
                
                <!-- About -->
                <li class="nav-item submenu <?php echo ($currentPage === 'about') ? 'active' : ''; ?>">
                    <a class="nav-link" href="#" title="About" onclick="return false;">
                        About
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="?page=about" title="About Us">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=conpany.history" title="Company History">Company History</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=teams" title="Teams">Teams</a>
                        </li>
                    </ul>
                </li>
                
                <!-- Services -->
                <li class="nav-item submenu <?php echo ($currentPage === 'services') ? 'active' : ''; ?>">
                    <a class="nav-link" href="#" title="Services" onclick="return false;">
                        Services
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="?page=services" title="All Services">All Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=services&type=web-development" title="Web Development">Web Development</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=services&type=mobile-app" title="Mobile App">Mobile App</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=services&type=software-consulting" title="Software Consulting">Software Consulting</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=services&type=cloud-solutions" title="Cloud Solutions">Cloud Solutions</a>
                        </li>
                    </ul>
                </li>
                
                <!-- Projects -->
                <li class="nav-item submenu <?php echo ($currentPage === 'projects') ? 'active' : ''; ?>">
                    <a class="nav-link" href="#" title="Projects" onclick="return false;">
                        Projects
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="?page=projects" title="All Projects">All Projects</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=projects&category=web" title="Web Projects">Web Projects</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=projects&category=mobile" title="Mobile Projects">Mobile Projects</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=projects&id=1" title="Project Details">Project Details</a>
                        </li>
                    </ul>
                </li>
                
                <!-- Blog -->
                <li class="nav-item submenu <?php echo ($currentPage === 'blogs') ? 'active' : ''; ?>">
                    <a class="nav-link" href="#" title="Blog" onclick="return false;">
                        Blog
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="?page=blogs" title="All Blogs">All Blogs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=blogs&category=technology" title="Technology">Technology</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=blogs&category=business" title="Business">Business</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=blogs&id=1" title="Blog Details">Blog Details</a>
                        </li>
                    </ul>
                </li>
                
                <!-- Contact -->
                <li class="nav-item <?php echo ($currentPage === 'contact') ? 'active' : ''; ?>">
                    <a class="nav-link" href="?page=contact" title="Contact">Contact</a>
                </li>
                
            </ul>
            
            <!-- Phone Number -->
            <a href="tel:0123456789" class="header-phone">0123 456 789</a>
        </div>
    </nav>
</header>
