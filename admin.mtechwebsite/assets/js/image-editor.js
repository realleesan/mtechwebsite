/**
 * Image Editor Overlay
 * Tính năng: zoom, pan, lưới grid, cắt ảnh tự do, xuất canvas → base64
 * Dùng cho module blogs (create & edit)
 *
 * Cách dùng:
 *   ImageEditor.init({
 *     triggerInput : '#image',          // input[type=file] kích hoạt editor
 *     outputInput  : '#imageEdited',    // hidden input nhận base64 kết quả
 *     previewImg   : '#image-preview',  // <img> preview sau khi lưu
 *   });
 */

const ImageEditor = (function () {

    // ── State ──────────────────────────────────────────────────────────
    let cfg        = {};
    let backdrop   = null;
    let canvas     = null;
    let ctx        = null;

    let img        = new Image();
    let imgLoaded  = false;

    // Transform
    let scale      = 1;
    let minScale   = 0.1;
    let maxScale   = 5;
    let offsetX    = 0;
    let offsetY    = 0;

    // Pan
    let isPanning  = false;
    let panStartX  = 0;
    let panStartY  = 0;
    let panOriginX = 0;
    let panOriginY = 0;

    // Crop
    let cropMode   = false;
    let isCropping = false;
    let cropStart  = { x: 0, y: 0 };
    let cropRect   = null;   // { x, y, w, h } in canvas px

    // Grid
    let showGrid   = false;

    // ── Build DOM ──────────────────────────────────────────────────────
    function buildDOM() {
        if (document.getElementById('imgEditorBackdrop')) return;

        const html = `
        <div class="img-editor-backdrop" id="imgEditorBackdrop" role="dialog" aria-modal="true" aria-label="Chỉnh sửa ảnh tải lên">
          <div class="img-editor-dialog">

            <!-- Header -->
            <div class="img-editor-header">
              <h5><i class="bi bi-crop me-2"></i>Chỉnh sửa ảnh tải lên</h5>
              <button class="img-editor-close" id="imgEditorClose" title="Đóng" aria-label="Đóng">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>

            <!-- Body -->
            <div class="img-editor-body">

              <!-- Toolbar -->
              <div class="img-editor-toolbar">
                <button class="img-editor-tool-btn" id="imgEditorGridBtn" title="Hiện/ẩn lưới">
                  <i class="bi bi-grid-3x3"></i> Lưới
                </button>
                <button class="img-editor-tool-btn" id="imgEditorCropBtn" title="Chế độ cắt ảnh">
                  <i class="bi bi-scissors"></i> Cắt ảnh
                </button>
                <button class="img-editor-tool-btn" id="imgEditorResetBtn" title="Đặt lại">
                  <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                </button>
              </div>

              <!-- Canvas -->
              <div class="img-editor-canvas-wrap" id="imgEditorCanvasWrap">
                <canvas id="imgEditorCanvas"></canvas>
              </div>

              <!-- Hint -->
              <div class="img-editor-hint" id="imgEditorHint">
                Kéo để di chuyển ảnh &nbsp;·&nbsp; Cuộn để phóng to/thu nhỏ
              </div>

              <!-- Zoom slider -->
              <div class="img-editor-zoom-row">
                <i class="bi bi-zoom-out zoom-icon"></i>
                <input type="range" id="imgEditorZoom" min="10" max="500" value="100" step="1">
                <i class="bi bi-zoom-in zoom-icon"></i>
                <span class="img-editor-zoom-label" id="imgEditorZoomLabel">100%</span>
              </div>

            </div>

            <!-- Footer -->
            <div class="img-editor-footer">
              <button class="btn btn-outline-secondary btn-sm" id="imgEditorCancel">Hủy</button>
              <button class="btn btn-primary btn-sm" id="imgEditorSave">
                <i class="bi bi-check-lg me-1"></i>Lưu
              </button>
            </div>

          </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', html);

        backdrop = document.getElementById('imgEditorBackdrop');
        canvas   = document.getElementById('imgEditorCanvas');
        ctx      = canvas.getContext('2d');

        bindEvents();
    }

    // ── Bind events ────────────────────────────────────────────────────
    function bindEvents() {
        // Close
        document.getElementById('imgEditorClose').addEventListener('click', close);
        document.getElementById('imgEditorCancel').addEventListener('click', close);
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) close();
        });

        // Keyboard
        document.addEventListener('keydown', function (e) {
            if (!backdrop.classList.contains('active')) return;
            if (e.key === 'Escape') close();
        });

        // Grid toggle
        document.getElementById('imgEditorGridBtn').addEventListener('click', function () {
            showGrid = !showGrid;
            this.classList.toggle('active', showGrid);
            draw();
        });

        // Crop toggle
        document.getElementById('imgEditorCropBtn').addEventListener('click', function () {
            cropMode = !cropMode;
            this.classList.toggle('active', cropMode);
            if (!cropMode) {
                cropRect = null;
                draw();
            }
            updateHint();
        });

        // Reset
        document.getElementById('imgEditorResetBtn').addEventListener('click', resetTransform);

        // Zoom slider
        document.getElementById('imgEditorZoom').addEventListener('input', function () {
            const newScale = parseInt(this.value) / 100;
            const wrap     = document.getElementById('imgEditorCanvasWrap');
            const cx       = wrap.clientWidth  / 2;
            const cy       = wrap.clientHeight / 2;
            // Zoom về tâm canvas
            offsetX = cx - (cx - offsetX) * (newScale / scale);
            offsetY = cy - (cy - offsetY) * (newScale / scale);
            scale   = newScale;
            clampOffset();
            draw();
            document.getElementById('imgEditorZoomLabel').textContent = this.value + '%';
        });

        // Save
        document.getElementById('imgEditorSave').addEventListener('click', save);

        // Canvas mouse/touch events
        const wrap = document.getElementById('imgEditorCanvasWrap');
        wrap.addEventListener('mousedown',  onPointerDown);
        wrap.addEventListener('mousemove',  onPointerMove);
        wrap.addEventListener('mouseup',    onPointerUp);
        wrap.addEventListener('mouseleave', onPointerUp);
        wrap.addEventListener('wheel',      onWheel, { passive: false });

        // Touch
        wrap.addEventListener('touchstart',  onTouchStart, { passive: false });
        wrap.addEventListener('touchmove',   onTouchMove,  { passive: false });
        wrap.addEventListener('touchend',    onTouchEnd);
    }

    // ── Open / Close ───────────────────────────────────────────────────
    function open(file) {
        buildDOM();
        backdrop = document.getElementById('imgEditorBackdrop');
        canvas   = document.getElementById('imgEditorCanvas');
        ctx      = canvas.getContext('2d');

        imgLoaded = false;
        cropRect  = null;
        cropMode  = false;
        showGrid  = false;

        document.getElementById('imgEditorCropBtn').classList.remove('active');
        document.getElementById('imgEditorGridBtn').classList.remove('active');
        updateHint();

        const reader = new FileReader();
        reader.onload = function (e) {
            img = new Image();
            img.onload = function () {
                imgLoaded = true;
                resizeCanvas();
                resetTransform();
                backdrop.classList.add('active');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function close() {
        if (backdrop) backdrop.classList.remove('active');
        // Reset file input để không trigger lại
        const input = document.querySelector(cfg.triggerInput);
        if (input) input.value = '';
    }

    // ── Canvas resize ──────────────────────────────────────────────────
    function resizeCanvas() {
        const wrap = document.getElementById('imgEditorCanvasWrap');
        canvas.width  = wrap.clientWidth;
        canvas.height = wrap.clientHeight;
    }

    // ── Reset transform ────────────────────────────────────────────────
    function resetTransform() {
        if (!imgLoaded) return;
        const wrap = document.getElementById('imgEditorCanvasWrap');
        const cw   = wrap.clientWidth;
        const ch   = wrap.clientHeight;

        // Fit ảnh vào canvas, giữ tỉ lệ
        const fitScale = Math.min(cw / img.naturalWidth, ch / img.naturalHeight) * 0.9;
        scale   = fitScale;
        offsetX = (cw - img.naturalWidth  * scale) / 2;
        offsetY = (ch - img.naturalHeight * scale) / 2;

        minScale = fitScale * 0.3;
        maxScale = fitScale * 8;

        cropRect = null;
        syncZoomSlider();
        draw();
    }

    // ── Draw ───────────────────────────────────────────────────────────
    function draw() {
        if (!imgLoaded || !ctx) return;
        const cw = canvas.width;
        const ch = canvas.height;

        ctx.clearRect(0, 0, cw, ch);

        // Background
        ctx.fillStyle = '#111';
        ctx.fillRect(0, 0, cw, ch);

        // Image
        ctx.save();
        ctx.translate(offsetX, offsetY);
        ctx.scale(scale, scale);
        ctx.drawImage(img, 0, 0);
        ctx.restore();

        // Grid
        if (showGrid) drawGrid(cw, ch);

        // Crop rect
        if (cropRect) drawCropRect();
    }

    function drawGrid(cw, ch) {
        const cols = 3;
        const rows = 3;
        ctx.save();
        ctx.strokeStyle = 'rgba(255,255,255,0.25)';
        ctx.lineWidth   = 1;
        ctx.setLineDash([4, 4]);

        for (let i = 1; i < cols; i++) {
            const x = (cw / cols) * i;
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, ch);
            ctx.stroke();
        }
        for (let j = 1; j < rows; j++) {
            const y = (ch / rows) * j;
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(cw, y);
            ctx.stroke();
        }
        ctx.restore();
    }

    function drawCropRect() {
        const { x, y, w, h } = cropRect;
        if (Math.abs(w) < 4 || Math.abs(h) < 4) return;

        const x1 = w >= 0 ? x : x + w;
        const y1 = h >= 0 ? y : y + h;
        const rw = Math.abs(w);
        const rh = Math.abs(h);

        // Dim outside
        ctx.save();
        ctx.fillStyle = 'rgba(0,0,0,0.45)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Clear inside crop
        ctx.clearRect(x1, y1, rw, rh);

        // Redraw image inside crop (sharp)
        ctx.save();
        ctx.beginPath();
        ctx.rect(x1, y1, rw, rh);
        ctx.clip();
        ctx.translate(offsetX, offsetY);
        ctx.scale(scale, scale);
        ctx.drawImage(img, 0, 0);
        ctx.restore();

        // Border
        ctx.strokeStyle = '#fff';
        ctx.lineWidth   = 1.5;
        ctx.setLineDash([]);
        ctx.strokeRect(x1, y1, rw, rh);

        // Rule-of-thirds inside crop
        ctx.strokeStyle = 'rgba(255,255,255,0.3)';
        ctx.lineWidth   = 0.5;
        for (let i = 1; i < 3; i++) {
            ctx.beginPath();
            ctx.moveTo(x1 + rw * i / 3, y1);
            ctx.lineTo(x1 + rw * i / 3, y1 + rh);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(x1, y1 + rh * i / 3);
            ctx.lineTo(x1 + rw, y1 + rh * i / 3);
            ctx.stroke();
        }

        // Corner handles
        const hs = 8;
        ctx.fillStyle = '#fff';
        [[x1, y1], [x1 + rw, y1], [x1, y1 + rh], [x1 + rw, y1 + rh]].forEach(([hx, hy]) => {
            ctx.fillRect(hx - hs / 2, hy - hs / 2, hs, hs);
        });

        ctx.restore();
    }

    // ── Pointer events (pan & crop) ────────────────────────────────────
    function getCanvasPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width  / rect.width;
        const scaleY = canvas.height / rect.height;
        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top)  * scaleY,
        };
    }

    function onPointerDown(e) {
        e.preventDefault();
        const pos = getCanvasPos(e);

        if (cropMode) {
            isCropping = true;
            cropStart  = pos;
            cropRect   = { x: pos.x, y: pos.y, w: 0, h: 0 };
        } else {
            isPanning  = true;
            panStartX  = e.clientX;
            panStartY  = e.clientY;
            panOriginX = offsetX;
            panOriginY = offsetY;
            canvas.style.cursor = 'grabbing';
        }
    }

    function onPointerMove(e) {
        e.preventDefault();
        if (cropMode && isCropping) {
            const pos  = getCanvasPos(e);
            cropRect.w = pos.x - cropStart.x;
            cropRect.h = pos.y - cropStart.y;
            draw();
        } else if (isPanning) {
            offsetX = panOriginX + (e.clientX - panStartX);
            offsetY = panOriginY + (e.clientY - panStartY);
            clampOffset();
            draw();
        }
    }

    function onPointerUp(e) {
        if (isCropping) {
            isCropping = false;
            // Nếu crop quá nhỏ → bỏ
            if (cropRect && Math.abs(cropRect.w) < 8 && Math.abs(cropRect.h) < 8) {
                cropRect = null;
                draw();
            }
        }
        isPanning = false;
        canvas.style.cursor = cropMode ? 'crosshair' : 'grab';
    }

    // ── Wheel zoom ─────────────────────────────────────────────────────
    function onWheel(e) {
        e.preventDefault();
        const pos    = getCanvasPos(e);
        const delta  = e.deltaY > 0 ? 0.9 : 1.1;
        const newScale = Math.min(maxScale, Math.max(minScale, scale * delta));

        offsetX = pos.x - (pos.x - offsetX) * (newScale / scale);
        offsetY = pos.y - (pos.y - offsetY) * (newScale / scale);
        scale   = newScale;

        clampOffset();
        syncZoomSlider();
        draw();
    }

    // ── Touch (pinch zoom + pan) ───────────────────────────────────────
    let lastTouchDist = 0;
    let lastTouchMid  = { x: 0, y: 0 };

    function getTouchDist(t) {
        const dx = t[0].clientX - t[1].clientX;
        const dy = t[0].clientY - t[1].clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }

    function getTouchMid(t) {
        return {
            x: (t[0].clientX + t[1].clientX) / 2,
            y: (t[0].clientY + t[1].clientY) / 2,
        };
    }

    function onTouchStart(e) {
        e.preventDefault();
        if (e.touches.length === 2) {
            lastTouchDist = getTouchDist(e.touches);
            lastTouchMid  = getTouchMid(e.touches);
        } else if (e.touches.length === 1) {
            isPanning  = true;
            panStartX  = e.touches[0].clientX;
            panStartY  = e.touches[0].clientY;
            panOriginX = offsetX;
            panOriginY = offsetY;
        }
    }

    function onTouchMove(e) {
        e.preventDefault();
        if (e.touches.length === 2) {
            const dist     = getTouchDist(e.touches);
            const mid      = getTouchMid(e.touches);
            const delta    = dist / lastTouchDist;
            const newScale = Math.min(maxScale, Math.max(minScale, scale * delta));
            const rect     = canvas.getBoundingClientRect();
            const cx       = (mid.x - rect.left) * (canvas.width  / rect.width);
            const cy       = (mid.y - rect.top)  * (canvas.height / rect.height);

            offsetX = cx - (cx - offsetX) * (newScale / scale);
            offsetY = cy - (cy - offsetY) * (newScale / scale);
            scale   = newScale;

            // Pan with mid point
            offsetX += mid.x - lastTouchMid.x;
            offsetY += mid.y - lastTouchMid.y;

            lastTouchDist = dist;
            lastTouchMid  = mid;

            clampOffset();
            syncZoomSlider();
            draw();
        } else if (e.touches.length === 1 && isPanning) {
            offsetX = panOriginX + (e.touches[0].clientX - panStartX);
            offsetY = panOriginY + (e.touches[0].clientY - panStartY);
            clampOffset();
            draw();
        }
    }

    function onTouchEnd(e) {
        if (e.touches.length < 2) lastTouchDist = 0;
        if (e.touches.length === 0) isPanning = false;
    }

    // ── Helpers ────────────────────────────────────────────────────────
    function clampOffset() {
        // Cho phép kéo ảnh ra ngoài tối đa 80% kích thước ảnh đã scale
        const imgW = img.naturalWidth  * scale;
        const imgH = img.naturalHeight * scale;
        const cw   = canvas.width;
        const ch   = canvas.height;
        const pad  = 40;

        offsetX = Math.min(cw - pad, Math.max(pad - imgW, offsetX));
        offsetY = Math.min(ch - pad, Math.max(pad - imgH, offsetY));
    }

    function syncZoomSlider() {
        const slider = document.getElementById('imgEditorZoom');
        const label  = document.getElementById('imgEditorZoomLabel');
        if (!slider) return;
        const pct = Math.round(scale * 100);
        slider.value = Math.min(500, Math.max(10, pct));
        label.textContent = pct + '%';
    }

    function updateHint() {
        const hint = document.getElementById('imgEditorHint');
        if (!hint) return;
        hint.textContent = cropMode
            ? 'Kéo để vẽ vùng cắt &nbsp;·&nbsp; Bấm Lưu để áp dụng cắt'
            : 'Kéo để di chuyển ảnh &nbsp;·&nbsp; Cuộn để phóng to/thu nhỏ';
    }

    // ── Save ───────────────────────────────────────────────────────────
    function save() {
        if (!imgLoaded) return;

        let outputCanvas;

        if (cropRect && Math.abs(cropRect.w) >= 8 && Math.abs(cropRect.h) >= 8) {
            // --- Crop mode: xuất vùng đã chọn ---
            const x1 = cropRect.w >= 0 ? cropRect.x : cropRect.x + cropRect.w;
            const y1 = cropRect.h >= 0 ? cropRect.y : cropRect.y + cropRect.h;
            const rw = Math.abs(cropRect.w);
            const rh = Math.abs(cropRect.h);

            // Chuyển từ canvas px → image px
            const imgX = (x1 - offsetX) / scale;
            const imgY = (y1 - offsetY) / scale;
            const imgW = rw / scale;
            const imgH = rh / scale;

            outputCanvas        = document.createElement('canvas');
            outputCanvas.width  = Math.round(imgW);
            outputCanvas.height = Math.round(imgH);
            const octx          = outputCanvas.getContext('2d');
            octx.drawImage(img, imgX, imgY, imgW, imgH, 0, 0, imgW, imgH);
        } else {
            // --- Không crop: xuất toàn bộ ảnh gốc (không mất chất lượng) ---
            outputCanvas        = document.createElement('canvas');
            outputCanvas.width  = img.naturalWidth;
            outputCanvas.height = img.naturalHeight;
            const octx          = outputCanvas.getContext('2d');
            octx.drawImage(img, 0, 0);
        }

        const dataURL = outputCanvas.toDataURL('image/jpeg', 0.92);

        // Ghi vào hidden input
        const outputInput = document.querySelector(cfg.outputInput);
        if (outputInput) outputInput.value = dataURL;

        // Hiện preview
        const previewImg = document.querySelector(cfg.previewImg);
        if (previewImg) {
            previewImg.src = dataURL;
            previewImg.style.display = 'block';
        }

        // Đánh dấu đã edit để controller biết dùng base64 thay vì file upload
        const editedFlag = document.querySelector(cfg.editedFlag || '#imageEditedFlag');
        if (editedFlag) editedFlag.value = '1';

        backdrop.classList.remove('active');
    }

    // ── Public API ─────────────────────────────────────────────────────
    function init(options) {
        cfg = Object.assign({
            triggerInput : '#image',
            outputInput  : '#imageEdited',
            previewImg   : '#image-preview',
            editedFlag   : '#imageEditedFlag',
        }, options);

        buildDOM();

        // Trigger khi chọn file
        const input = document.querySelector(cfg.triggerInput);
        if (!input) return;

        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            open(file);
        });
    }

    return { init, open, close };

})();

// Auto-init khi DOM sẵn sàng (nếu có element #image và #imageEdited)
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('image') && document.getElementById('imageEdited')) {
        ImageEditor.init({
            triggerInput : '#image',
            outputInput  : '#imageEdited',
            previewImg   : '#image-preview',
            editedFlag   : '#imageEditedFlag',
        });
    }
});
