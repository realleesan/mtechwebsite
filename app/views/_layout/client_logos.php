<?php
/**
 * _layout/client_logos.php — Partial: Client Logo Area
 * Dùng chung cho home.php và about.php
 * Biến nhận: $clientLogos (array)
 */
$clientLogos = $clientLogos ?? [];
?>

<section class="clients_logo_area">
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap align-items-center">
            <?php if (!empty($clientLogos)): ?>
                <?php foreach ($clientLogos as $client): ?>
                    <div class="clients_logo_item">
                        <a href="<?php echo htmlspecialchars($client['url'] ?? '#'); ?>"
                           <?php echo ($client['url'] !== '#') ? 'target="_blank" rel="noopener"' : ''; ?>>
                            <img src="<?php echo htmlspecialchars($client['logo']); ?>"
                                 alt="<?php echo htmlspecialchars($client['name']); ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback static -->
                <div class="clients_logo_item"><a href="#"><img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/clients_logo1.png" alt="Partner"></a></div>
                <div class="clients_logo_item"><a href="#"><img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/clients_logo2.png" alt="Partner"></a></div>
                <div class="clients_logo_item"><a href="#"><img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/clients_logo2.png" alt="Partner"></a></div>
                <div class="clients_logo_item"><a href="#"><img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/clients_logo1.png" alt="Partner"></a></div>
                <div class="clients_logo_item"><a href="#"><img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/clients_logo1.png" alt="Partner"></a></div>
            <?php endif; ?>
        </div>
    </div>
</section>
