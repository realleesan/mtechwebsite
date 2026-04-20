<?php
/**
 * Page Header - Banner với tiêu đề trang và breadcrumb
 * Theo cấu trúc Wokrate template
 */

// Tự động lấy tiêu đề nếu không được truyền vào
if (!isset($pageTitle)) {
    $pageTitles = [
        'about'           => 'About Us',
        'services'        => 'Services',
        'projects'        => 'Projects',
        'blogs'           => 'Blog',
        'contact'         => 'Contact Us',
        'company.history' => 'Company History',
        'teams'           => 'Our Team',
    ];
    $currentPage = $_GET['page'] ?? 'home';
    $pageTitle = $pageTitles[$currentPage] ?? ucfirst($currentPage);
}
?>

<section class="banner_area">
    <div class="container">
        <div class="banner_content text-center">

            <h2 class="page_title"><?php echo htmlspecialchars($pageTitle); ?></h2>

            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="./">Home</a></li>

                <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                    <?php foreach ($breadcrumbs as $crumb): ?>
                        <?php if (!empty($crumb['url'])): ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo htmlspecialchars($crumb['url']); ?>">
                                    <?php echo htmlspecialchars($crumb['title']); ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo htmlspecialchars($crumb['title']); ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php echo htmlspecialchars($pageTitle); ?>
                    </li>
                <?php endif; ?>
            </ol>

        </div>
    </div>
</section>
