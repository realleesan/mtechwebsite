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
            <img src="docs/template/_layout/screen/logo_header.svg" alt="MTech Logo">
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
            <ul class="navbar-nav menu">
                
                <!-- Home -->
                <li class="nav-item <?php echo ($currentPage === 'home') ? 'active' : ''; ?>">
                    <a class="nav-link" href="./" title="Home">Home</a>
                </li>
                
                <!-- About -->
                <li class="nav-item <?php echo ($currentPage === 'about') ? 'active' : ''; ?>">
                    <a class="nav-link" href="?page=about" title="About">About</a>
                </li>
                
                <!-- Services -->
                <li class="nav-item dropdown submenu <?php echo ($currentPage === 'services') ? 'active' : ''; ?>">
                    <a class="nav-link dropdown-toggle" href="?page=services" title="Services" data-toggle="dropdown">
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
                <li class="nav-item dropdown submenu <?php echo ($currentPage === 'projects') ? 'active' : ''; ?>">
                    <a class="nav-link dropdown-toggle" href="?page=projects" title="Projects" data-toggle="dropdown">
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
                <li class="nav-item dropdown submenu <?php echo ($currentPage === 'blogs') ? 'active' : ''; ?>">
                    <a class="nav-link dropdown-toggle" href="?page=blogs" title="Blog" data-toggle="dropdown">
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
        </div>
    </nav>
</header>
