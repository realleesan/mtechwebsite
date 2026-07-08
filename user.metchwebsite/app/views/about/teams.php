<?php
/**
 * Teams Page View
 * Hiển thị sơ đồ tổ chức công ty MTECH
 */
?>

<section class="teams_area sec_gap">
    <div class="container">

        <!-- Section Title -->
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Sơ đồ tổ chức công ty</h2>
            <span class="title_br"></span>
        </div>

    </div>

    <!-- Sơ đồ tổ chức công ty -->
    <div class="container">
        <img src="/app/views/about/so do phan cap.png"
             alt="Sơ đồ tổ chức công ty MTECH"
             class="org_img"
             style="width:100%; height:auto; display:block; cursor:pointer;">

        <hr style="border:none; border-top:1px solid #e0e0e0; margin:40px 0;">

        <img src="/app/views/about/so do phan cap tanh.jpg"
             alt="Sơ đồ phân cấp tổ chức MTECH"
             class="org_img"
             style="width:100%; height:auto; display:block; cursor:pointer;">
    </div>

</section>

<!-- Lightbox Overlay cho Sơ đồ tổ chức -->
<div class="org_lightbox" id="orgLightbox" role="dialog" aria-modal="true" aria-label="Xem ảnh">
    <button class="org_lightbox_close" id="orgLightboxClose" aria-label="Đóng">&times;</button>
    <img src="" alt="" id="orgLightboxImg" class="org_lightbox_img">
</div>
