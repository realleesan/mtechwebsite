/**
 * Dashboard JavaScript - Admin MTech Website
 * Interactive features for dashboard page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard functionality
    initDashboard();
});

/**
 * Initialize dashboard features
 */
function initDashboard() {
    // Auto-refresh functionality
    initAutoRefresh();
    
    // Initialize tooltips if Bootstrap is available
    initTooltips();
    
    // Handle stat card interactions
    initStatCards();
    
    // Initialize Chart.js for access statistics
    initAccessChart();
    
    // Initialize real-time updates (if needed)
    initRealTimeUpdates();
}

/**
 * Auto-refresh dashboard data
 */
function initAutoRefresh() {
    const refreshInterval = 60000; // 1 minute
    let refreshTimer;
    
    function refreshDashboard() {
        // Show loading state
        document.body.classList.add('loading');
        
        // Refresh the page to get latest data
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }
    
    // Set up auto-refresh
    refreshTimer = setInterval(refreshDashboard, refreshInterval);
    
    // Clear timer when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(refreshTimer);
        } else {
            refreshTimer = setInterval(refreshDashboard, refreshInterval);
        }
    });
}

/**
 * Initialize Bootstrap tooltips
 */
function initTooltips() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

/**
 * Handle stat card interactions
 */
function initStatCards() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach(card => {
        card.addEventListener('click', function() {
            // Add click animation
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
        
        // Add hover sound effect (optional)
        card.addEventListener('mouseenter', function() {
            this.style.cursor = 'pointer';
        });
    });
}

/**
 * Initialize real-time updates
 */
function initRealTimeUpdates() {
    // WebSocket or polling for real-time updates
    // This is a placeholder for future implementation
    
    // Example: Check for new notifications
    checkForNewNotifications();
}

/**
 * Check for new notifications
 */
function checkForNewNotifications() {
    // This would typically make an AJAX request to check for updates
    // For now, it's just a placeholder
    
    // Example implementation:
    /*
    fetch('/api/notifications/check')
        .then(response => response.json())
        .then(data => {
            if (data.hasNew) {
                showNotificationBadge(data.count);
            }
        })
        .catch(error => {
            console.error('Error checking notifications:', error);
        });
    */
}

/**
 * Show notification badge
 */
function showNotificationBadge(count) {
    // Create or update notification badge
    let badge = document.querySelector('.notification-badge');
    
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'notification-badge badge bg-danger';
        // Append to appropriate element
    }
    
    badge.textContent = count > 99 ? '99+' : count;
    badge.style.display = count > 0 ? 'inline-block' : 'none';
}

/**
 * Format numbers for display
 */
function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

/**
 * Format time ago
 */
function timeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    
    let interval = Math.floor(seconds / 31536000);
    if (interval > 1) return interval + ' năm trước';
    
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) return interval + ' tháng trước';
    
    interval = Math.floor(seconds / 86400);
    if (interval > 1) return interval + ' ngày trước';
    
    interval = Math.floor(seconds / 3600);
    if (interval > 1) return interval + ' giờ trước';
    
    interval = Math.floor(seconds / 60);
    if (interval > 1) return interval + ' phút trước';
    
    return 'Vừa xong';
}

/**
 * Export functions for global use
 */
window.dashboardUtils = {
    formatNumber,
    timeAgo,
    checkForNewNotifications,
    showNotificationBadge
};

/**
 * Initialize Chart.js for access statistics
 */
function initAccessChart() {
    const ctx = document.getElementById('accessChart');
    if (!ctx) return;
    
    // Initialize chart variable
    window.accessChart = null;
    
    // Load initial data (7 days)
    loadChartData('7days');
    
    // Setup period button handlers
    const periodButtons = document.querySelectorAll('[data-period]');
    periodButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            periodButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // Load data for selected period
            const period = this.getAttribute('data-period');
            loadChartData(period);
        });
    });
}

/**
 * Load chart data for specific period
 */
function loadChartData(period) {
    const loadingElement = document.getElementById('chart-loading');
    const chartContainer = document.querySelector('.chart-container');
    
    // Show loading
    if (loadingElement) loadingElement.style.display = 'block';
    if (chartContainer) chartContainer.style.opacity = '0.5';
    
    // Call real API
    fetch(`/api/access-stats?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const chartData = {
                    labels: data.labels,
                    visits: data.data
                };
                renderChart(chartData, period);
                
                // Update stats cards
                updateStatsCards(data.stats);
            } else {
                console.error('API Error:', data.error);
                // Fallback to mock data on error
                const mockData = generateMockData(period);
                renderChart(mockData, period);
            }
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
            // Fallback to mock data on network error
            const mockData = generateMockData(period);
            renderChart(mockData, period);
        })
        .finally(() => {
            // Hide loading
            if (loadingElement) loadingElement.style.display = 'none';
            if (chartContainer) chartContainer.style.opacity = '1';
        });
}

/**
 * Generate mock data (replace with actual API data)
 */
function generateMockData(period) {
    const labels = [];
    const visits = [];
    let days = 7;
    
    switch(period) {
        case '7days':
            days = 7;
            for (let i = days - 1; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                labels.push(date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' }));
                visits.push(Math.floor(Math.random() * 100) + 20);
            }
            break;
        case 'month':
            days = 30;
            for (let i = days - 1; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                if (i % 3 === 0) { // Show every 3 days to avoid crowding
                    labels.push(date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' }));
                    visits.push(Math.floor(Math.random() * 150) + 30);
                }
            }
            break;
        case 'year':
            for (let i = 11; i >= 0; i--) {
                const date = new Date();
                date.setMonth(date.getMonth() - i);
                labels.push(date.toLocaleDateString('vi-VN', { month: 'short' }));
                visits.push(Math.floor(Math.random() * 500) + 100);
            }
            break;
        case 'all':
            for (let i = 4; i >= 0; i--) {
                const date = new Date();
                date.setFullYear(date.getFullYear() - i);
                labels.push(date.getFullYear().toString());
                visits.push(Math.floor(Math.random() * 5000) + 1000);
            }
            break;
    }
    
    return { labels, visits };
}

/**
 * Render the chart with data
 */
function renderChart(data, period) {
    const ctx = document.getElementById('accessChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.accessChart) {
        window.accessChart.destroy();
    }
    
    // Determine chart type based on period
    const chartType = period === 'all' ? 'bar' : 'line';
    
    // Create new chart
    window.accessChart = new Chart(ctx, {
        type: chartType,
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Lượt truy cập',
                data: data.visits,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: chartType === 'line' ? 'rgba(59, 130, 246, 0.1)' : 'rgba(59, 130, 246, 0.6)',
                borderWidth: 2,
                fill: chartType === 'line',
                tension: 0.4,
                pointRadius: chartType === 'line' ? 4 : 0,
                pointHoverRadius: chartType === 'line' ? 6 : 0,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Lượt truy cập: ' + context.parsed.y.toLocaleString('vi-VN');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            if (value >= 1000) {
                                return (value / 1000) + 'k';
                            }
                            return value;
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            animation: {
                duration: 750,
                easing: 'easeInOutQuart'
            }
        }
    });
}

/**
 * Update statistics cards with new data
 */
function updateStatsCards(stats) {
    // Update DOM elements with real stats from API
    const todayElement = document.getElementById('today-visits');
    const monthElement = document.getElementById('month-visits');
    const totalElement = document.getElementById('total-visits');
    
    if (todayElement && stats.today !== undefined) {
        const currentValue = parseInt(todayElement.textContent.replace(/,/g, '')) || 0;
        animateNumber(todayElement, currentValue, stats.today);
    }
    
    if (monthElement && stats.month !== undefined) {
        const currentValue = parseInt(monthElement.textContent.replace(/,/g, '')) || 0;
        animateNumber(monthElement, currentValue, stats.month);
    }
    
    if (totalElement && stats.total !== undefined) {
        const currentValue = parseInt(totalElement.textContent.replace(/,/g, '')) || 0;
        animateNumber(totalElement, currentValue, stats.total);
    }
}

/**
 * Animate number changes
 */
function animateNumber(element, start, end, duration = 1000) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.round(current).toLocaleString('vi-VN');
    }, 16);
}

// Console log for debugging
console.log('Dashboard initialized successfully');
