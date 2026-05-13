/**
 * Image Editor Overlay - Enhanced UX
 * Tính năng: lưới 9x9, crop kiểu iPhone, edit/remove buttons
 * Dùng cho module blogs (create & edit)
 */

const ImageEditor = (function () {

// ── State ──────────────────────────────────────────────────────────
     let cfg             = {};
     let backdrop        = null;
     let canvas          = null;
     let ctx             = null;

     let img             = new Image();
     let imgLoaded       = false;
     let originalImageSrc    = null; // Ảnh gốc của lần mở hiện tại
     let savedOriginalImageSrc = null; // Ảnh gốc đầu tiên (không bị ghi đè)

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
    let cropRect   = null;   // { x, y, w, h } in canvas px
    let aspectRatio = null;  // null = free, hoặc số như 1, 16/9, 4/3
    let isDraggingHandle = false;
    let activeHandle = null; // 'tl', 'tr', 'bl', 'br', 't', 'r', 'b', 'l', 'move'
    let dragStart = { x: 0, y: 0 };
    let cropStartRect = null;

    // Grid
    let showGrid   = false;

    // ── Build DOM ──────────────────────────────────────────────────────
    function buildDOM() {
        if (document.getElementById('imgEditorBackdrop')) return;

        const html = `
        <div class="img-editor-backdrop" id="imgEditorBackdrop" role="dialog" aria-modal="true">
          <div class="img-editor-dialog">

            <!-- Header -->
            <div class="img-editor-header">
              <h5><i class="bi bi-crop me-2"></i>Chỉnh sửa ảnh tải lên</h5>
              <button class="img-editor-close" id="imgEditorClose" title="Đóng">
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
                <!-- Aspect Ratio Presets - nằm trong toolbar, chỉ hiện khi crop mode -->
                <div class="img-editor-aspect-presets" id="imgEditorAspectPresets" style="display: none;">
                  <span class="aspect-label">Tỷ lệ:</span>
                  <button class="aspect-preset-btn active" data-aspect="free">Tự do</button>
                  <button class="aspect-preset-btn" data-aspect="1">1:1</button>
                  <button class="aspect-preset-btn" data-aspect="1.777">16:9</button>
                  <button class="aspect-preset-btn" data-aspect="1.333">4:3</button>
                  <button class="aspect-preset-btn" data-aspect="0.75">3:4</button>
                  <button class="aspect-preset-btn" data-aspect="0.5625">9:16</button>
                </div>
              </div>

              <!-- Canvas -->
              <div class="img-editor-canvas-wrap" id="imgEditorCanvasWrap">
                <canvas id="imgEditorCanvas"></canvas>
              </div>

              <!-- Hint -->
              <div class="img-editor-hint" id="imgEditorHint">
                Kéo để di chuyển ảnh · Cuộn để phóng to/thu nhỏ
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
            
            const aspectPresets = document.getElementById('imgEditorAspectPresets');
            const canvasWrap = document.getElementById('imgEditorCanvasWrap');
            
            if (cropMode) {
                aspectPresets.style.display = 'flex';
                canvasWrap.classList.add('crop-mode');
                // Tạo crop rect mặc định (center, 80% kích thước)
                initDefaultCropRect();
            } else {
                aspectPresets.style.display = 'none';
                canvasWrap.classList.remove('crop-mode');
                cropRect = null;
                aspectRatio = null;
                draw();
            }
            updateHint();
        });

        // Aspect ratio presets
        document.getElementById('imgEditorAspectPresets').addEventListener('click', function (e) {
            if (!e.target.classList.contains('aspect-preset-btn')) return;
            
            // Update active state
            this.querySelectorAll('.aspect-preset-btn').forEach(btn => btn.classList.remove('active'));
            e.target.classList.add('active');
            
            // Set aspect ratio
            const aspect = e.target.dataset.aspect;
            aspectRatio = aspect === 'free' ? null : parseFloat(aspect);
            
            // Adjust current crop rect to new aspect ratio
            if (cropRect && aspectRatio) {
                adjustCropRectToAspectRatio();
            }
            draw();
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

        // Canvas mouse events
        const wrap = document.getElementById('imgEditorCanvasWrap');
        wrap.addEventListener('mousedown',  onPointerDown);
        wrap.addEventListener('mousemove',  onPointerMove);
        wrap.addEventListener('mouseup',    onPointerUp);
        wrap.addEventListener('mouseleave', onPointerUp);
        wrap.addEventListener('wheel',      onWheel, { passive: false });
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
         aspectRatio = null;
         savedOriginalImageSrc = null; // Reset khi mở file mới

         document.getElementById('imgEditorCropBtn').classList.remove('active');
         document.getElementById('imgEditorGridBtn').classList.remove('active');
         document.getElementById('imgEditorAspectPresets').style.display = 'none';
         document.getElementById('imgEditorCanvasWrap').classList.remove('crop-mode');
         updateHint();

         const reader = new FileReader();
         reader.onload = function (e) {
             img = new Image();
             img.onload = function () {
                 imgLoaded = true;
                 originalImageSrc = e.target.result;
                 savedOriginalImageSrc = e.target.result; // Luôn lưu ảnh gốc file mới
                 resizeCanvas();
                 resetTransform();
                 backdrop.classList.add('active');
             };
             img.src = e.target.result;
         };
         reader.readAsDataURL(file);
     }

     // Mở lại editor với Image object (dùng khi edit lại từ ảnh gốc)
     function openForEdit(imageObj) {
         buildDOM();
         backdrop = document.getElementById('imgEditorBackdrop');
         canvas   = document.getElementById('imgEditorCanvas');
         ctx      = canvas.getContext('2d');

         imgLoaded = false;
         cropRect  = null;
         cropMode  = false;
         showGrid  = false;
         aspectRatio = null;

         document.getElementById('imgEditorCropBtn').classList.remove('active');
         document.getElementById('imgEditorGridBtn').classList.remove('active');
         document.getElementById('imgEditorAspectPresets').style.display = 'none';
         document.getElementById('imgEditorCanvasWrap').classList.remove('crop-mode');
         updateHint();

         img = imageObj;
         img.onload = function () {
             imgLoaded = true;
             // Ghi đè originalImageSrc cho phiên edit hiện tại
             originalImageSrc = img.src;

             // Nếu chưa có savedOriginalImageSrc, lưu lại (trường hợp mở trực tiếp)
             if (!savedOriginalImageSrc) {
                 savedOriginalImageSrc = img.src;
             }

             resizeCanvas();
             resetTransform();
             backdrop.classList.add('active');
         };
         // Nếu ảnh đã được load trước đó (đã có .src và .naturalWidth)
         if (img.naturalWidth > 0) {
             imgLoaded = true;
             originalImageSrc = img.src;
             if (!savedOriginalImageSrc) {
                 savedOriginalImageSrc = img.src;
             }
             resizeCanvas();
             resetTransform();
             backdrop.classList.add('active');
         }
     }

function close() {
         if (backdrop) backdrop.classList.remove('active');
         // Reset file input để không trigger lại
         const input = document.querySelector(cfg.triggerInput);
         if (input) input.value = '';
         // Reset saved original image khi đóng editor
         savedOriginalImageSrc = originalImageSrc = null;
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

        // Fit ảnh vào canvas, giữ tỉ lệ (có padding 10%)
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

    // ── Initialize default crop rect ───────────────────────────────────
    function initDefaultCropRect() {
        if (!imgLoaded) return;

        const wrap = document.getElementById('imgEditorCanvasWrap');
        const cw = wrap.clientWidth;
        const ch = wrap.clientHeight;

        // Crop rect mặc định: center, 70% canvas
        const cropW = cw * 0.7;
        const cropH = ch * 0.7;
        cropRect = {
            x: (cw - cropW) / 2,
            y: (ch - cropH) / 2,
            w: cropW,
            h: cropH
        };
        draw();
    }

    // ── Adjust crop rect to aspect ratio ───────────────────────────────
    function adjustCropRectToAspectRatio() {
        if (!cropRect || !aspectRatio) return;
        
        const centerX = cropRect.x + cropRect.w / 2;
        const centerY = cropRect.y + cropRect.h / 2;
        
        // Giữ chiều rộng, điều chỉnh chiều cao
        const newHeight = cropRect.w / aspectRatio;
        
        cropRect.h = newHeight;
        cropRect.x = centerX - cropRect.w / 2;
        cropRect.y = centerY - cropRect.h / 2;
        
        // Clamp trong canvas
        clampCropRect();
    }

    // ── Clamp crop rect within canvas ──────────────────────────────────
    function clampCropRect() {
        if (!cropRect) return;
        
        const wrap = document.getElementById('imgEditorCanvasWrap');
        const cw = wrap.clientWidth;
        const ch = wrap.clientHeight;
        
        // Đảm bảo crop rect không vượt ra ngoài canvas
        cropRect.x = Math.max(0, Math.min(cropRect.x, cw - cropRect.w));
        cropRect.y = Math.max(0, Math.min(cropRect.y, ch - cropRect.h));
        cropRect.w = Math.max(20, Math.min(cropRect.w, cw - cropRect.x));
        cropRect.h = Math.max(20, Math.min(cropRect.h, ch - cropRect.y));
    }

    // ── Draw ───────────────────────────────────────────────────────────
    function draw() {
        if (!imgLoaded || !ctx) return;
        const cw = canvas.width;
        const ch = canvas.height;

        ctx.clearRect(0, 0, cw, ch);

        // Nền đen
        ctx.fillStyle = '#111';
        ctx.fillRect(0, 0, cw, ch);

        // Vẽ ảnh
        ctx.save();
        ctx.translate(offsetX, offsetY);
        ctx.scale(scale, scale);
        ctx.drawImage(img, 0, 0);
        ctx.restore();

        // Crop rect (lưới 3x3 trong crop được vẽ bên trong drawCropRect)
        if (cropRect) drawCropRect();
    }

function drawCropRect() {
        const { x, y, w, h } = cropRect;
        if (w < 4 || h < 4) return;

        ctx.save();

        // Làm tối vùng ngoài crop
        ctx.fillStyle = 'rgba(0,0,0,0.55)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Vẽ lại ảnh trong vùng crop (sắc nét)
        ctx.save();
        ctx.beginPath();
        ctx.rect(x, y, w, h);
        ctx.clip();
        ctx.translate(offsetX, offsetY);
        ctx.scale(scale, scale);
        ctx.drawImage(img, 0, 0);
        ctx.restore();

        // Viền crop
        ctx.strokeStyle = '#fff';
        ctx.lineWidth   = 2;
        ctx.setLineDash([]);
        ctx.strokeRect(x, y, w, h);

        // Handle góc/cạnh
        drawCropHandles(x, y, w, h);

        // Lưới 3x3 trong vùng crop (luôn hiển thị khi đang crop)
        drawCropGrid(x, y, w, h);

        ctx.restore();
    }

    function drawCropHandles(x, y, w, h) {
        const handleSize = 20;
        const handleThickness = 3;
        const cornerLength = 6;
        
        ctx.strokeStyle = '#fff';
        ctx.lineWidth = handleThickness;
        ctx.setLineDash([]);
        
        // Corner handles (L-shaped)
        // Top-left
        ctx.beginPath();
        ctx.moveTo(x, y + cornerLength);
        ctx.lineTo(x, y);
        ctx.lineTo(x + cornerLength, y);
        ctx.stroke();
        
        // Top-right
        ctx.beginPath();
        ctx.moveTo(x + w - cornerLength, y);
        ctx.lineTo(x + w, y);
        ctx.lineTo(x + w, y + cornerLength);
        ctx.stroke();
        
        // Bottom-left
        ctx.beginPath();
        ctx.moveTo(x, y + h - cornerLength);
        ctx.lineTo(x, y + h);
        ctx.lineTo(x + cornerLength, y + h);
        ctx.stroke();
        
        // Bottom-right
        ctx.beginPath();
        ctx.moveTo(x + w - cornerLength, y + h);
        ctx.lineTo(x + w, y + h);
        ctx.lineTo(x + w, y + h - cornerLength);
        ctx.stroke();
        
        // Edge handles (small lines)
        const edgeLength = 4;
        
        // Top edge
        ctx.beginPath();
        ctx.moveTo(x + w/2 - edgeLength, y);
        ctx.lineTo(x + w/2 + edgeLength, y);
        ctx.stroke();
        
        // Right edge
        ctx.beginPath();
        ctx.moveTo(x + w, y + h/2 - edgeLength);
        ctx.lineTo(x + w, y + h/2 + edgeLength);
        ctx.stroke();
        
        // Bottom edge
        ctx.beginPath();
        ctx.moveTo(x + w/2 - edgeLength, y + h);
        ctx.lineTo(x + w/2 + edgeLength, y + h);
        ctx.stroke();
        
        // Left edge
        ctx.beginPath();
        ctx.moveTo(x, y + h/2 - edgeLength);
        ctx.lineTo(x, y + h/2 + edgeLength);
        ctx.stroke();
    }

function drawCropGrid(x, y, w, h, cols = 3, rows = 3) {
         ctx.save();
         ctx.strokeStyle = 'rgba(255,255,255,0.5)';
         ctx.lineWidth = 1;
         ctx.setLineDash([3, 3]);

         // Vertical lines
         for (let i = 1; i < cols; i++) {
             const lineX = x + (w / cols) * i;
             ctx.beginPath();
             ctx.moveTo(lineX, y);
             ctx.lineTo(lineX, y + h);
             ctx.stroke();
         }

         // Horizontal lines
         for (let j = 1; j < rows; j++) {
             const lineY = y + (h / rows) * j;
             ctx.beginPath();
             ctx.moveTo(x, lineY);
             ctx.lineTo(x + w, lineY);
             ctx.stroke();
         }

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

        if (cropMode && cropRect) {
            // Check if clicking on crop handles
            const handle = getHandleAtPosition(pos);
            if (handle) {
                isDraggingHandle = true;
                activeHandle = handle;
                dragStart = pos;
                cropStartRect = { ...cropRect };
                canvas.style.cursor = getHandleCursor(handle);
                return;
            }
            
            // Check if clicking inside crop rect to move image
            if (isInsideCropRect(pos)) {
                isPanning = true;
                panStartX = e.clientX;
                panStartY = e.clientY;
                panOriginX = offsetX;
                panOriginY = offsetY;
                canvas.style.cursor = 'move';
                return;
            }
        }

        if (!cropMode) {
            // Normal pan mode
            isPanning = true;
            panStartX = e.clientX;
            panStartY = e.clientY;
            panOriginX = offsetX;
            panOriginY = offsetY;
            canvas.style.cursor = 'grabbing';
        }
    }

    function onPointerMove(e) {
        e.preventDefault();
        const pos = getCanvasPos(e);

        if (isDraggingHandle && activeHandle && cropStartRect) {
            // Handle crop rect resizing
            resizeCropRect(pos);
            draw();
        } else if (isPanning) {
            // Pan image
            offsetX = panOriginX + (e.clientX - panStartX);
            offsetY = panOriginY + (e.clientY - panStartY);
            clampOffset();
            draw();
        } else if (cropMode && cropRect) {
            // Update cursor based on hover position
            const handle = getHandleAtPosition(pos);
            if (handle) {
                canvas.style.cursor = getHandleCursor(handle);
            } else if (isInsideCropRect(pos)) {
                canvas.style.cursor = 'move';
            } else {
                canvas.style.cursor = 'crosshair';
            }
        }
    }

    function onPointerUp(e) {
        isDraggingHandle = false;
        activeHandle = null;
        dragStart = { x: 0, y: 0 };
        cropStartRect = null;
        isPanning = false;
        
        canvas.style.cursor = cropMode ? 'crosshair' : 'grab';
    }

    function isInsideCropRect(pos) {
        if (!cropRect) return false;
        return pos.x >= cropRect.x && pos.x <= cropRect.x + cropRect.w &&
               pos.y >= cropRect.y && pos.y <= cropRect.y + cropRect.h;
    }

    // ── iOS-style crop handles ─────────────────────────────────────────
    function getHandleAtPosition(pos) {
        if (!cropRect) return null;
        
        const handleSize = 20; // Touch-friendly size
        const { x, y, w, h } = cropRect;
        
        // Corner handles
        if (isNearPoint(pos, { x, y }, handleSize)) return 'tl'; // top-left
        if (isNearPoint(pos, { x: x + w, y }, handleSize)) return 'tr'; // top-right
        if (isNearPoint(pos, { x, y: y + h }, handleSize)) return 'bl'; // bottom-left
        if (isNearPoint(pos, { x: x + w, y: y + h }, handleSize)) return 'br'; // bottom-right
        
        // Edge handles
        if (isNearPoint(pos, { x: x + w/2, y }, handleSize)) return 't'; // top
        if (isNearPoint(pos, { x: x + w, y: y + h/2 }, handleSize)) return 'r'; // right
        if (isNearPoint(pos, { x: x + w/2, y: y + h }, handleSize)) return 'b'; // bottom
        if (isNearPoint(pos, { x, y: y + h/2 }, handleSize)) return 'l'; // left
        
        return null;
    }

    function isNearPoint(pos, point, threshold) {
        return Math.abs(pos.x - point.x) <= threshold && Math.abs(pos.y - point.y) <= threshold;
    }

    function getHandleCursor(handle) {
        const cursors = {
            'tl': 'nw-resize', 'tr': 'ne-resize', 'bl': 'sw-resize', 'br': 'se-resize',
            't': 'n-resize', 'r': 'e-resize', 'b': 's-resize', 'l': 'w-resize'
        };
        return cursors[handle] || 'crosshair';
    }

    function resizeCropRect(currentPos) {
        if (!cropStartRect || !activeHandle) return;
        
        const dx = currentPos.x - dragStart.x;
        const dy = currentPos.y - dragStart.y;
        let newRect = { ...cropStartRect };
        
        // Apply handle-specific resizing
        switch (activeHandle) {
            case 'tl': // top-left corner
                newRect.x += dx;
                newRect.y += dy;
                newRect.w -= dx;
                newRect.h -= dy;
                break;
            case 'tr': // top-right corner
                newRect.y += dy;
                newRect.w += dx;
                newRect.h -= dy;
                break;
            case 'bl': // bottom-left corner
                newRect.x += dx;
                newRect.w -= dx;
                newRect.h += dy;
                break;
            case 'br': // bottom-right corner
                newRect.w += dx;
                newRect.h += dy;
                break;
            case 't': // top edge
                newRect.y += dy;
                newRect.h -= dy;
                break;
            case 'r': // right edge
                newRect.w += dx;
                break;
            case 'b': // bottom edge
                newRect.h += dy;
                break;
            case 'l': // left edge
                newRect.x += dx;
                newRect.w -= dx;
                break;
        }
        
        // Apply aspect ratio constraint
        if (aspectRatio && aspectRatio > 0) {
            // For corner handles, maintain aspect ratio
            if (['tl', 'tr', 'bl', 'br'].includes(activeHandle)) {
                const targetHeight = Math.abs(newRect.w) / aspectRatio;
                const heightDiff = targetHeight - Math.abs(newRect.h);
                
                if (activeHandle === 'tl' || activeHandle === 'tr') {
                    newRect.y -= heightDiff;
                }
                newRect.h = newRect.h >= 0 ? targetHeight : -targetHeight;
            }
        }
        
        // Ensure minimum size
        const minSize = 50;
        if (Math.abs(newRect.w) < minSize) {
            if (newRect.w < 0) {
                newRect.x = cropStartRect.x + cropStartRect.w - minSize;
                newRect.w = -minSize;
            } else {
                newRect.w = minSize;
            }
        }
        if (Math.abs(newRect.h) < minSize) {
            if (newRect.h < 0) {
                newRect.y = cropStartRect.y + cropStartRect.h - minSize;
                newRect.h = -minSize;
            } else {
                newRect.h = minSize;
            }
        }
        
        // Normalize negative dimensions
        if (newRect.w < 0) {
            newRect.x += newRect.w;
            newRect.w = -newRect.w;
        }
        if (newRect.h < 0) {
            newRect.y += newRect.h;
            newRect.h = -newRect.h;
        }
        
        // Clamp to canvas bounds
        const wrap = document.getElementById('imgEditorCanvasWrap');
        const cw = wrap.clientWidth;
        const ch = wrap.clientHeight;
        
        newRect.x = Math.max(0, Math.min(newRect.x, cw - newRect.w));
        newRect.y = Math.max(0, Math.min(newRect.y, ch - newRect.h));
        newRect.w = Math.min(newRect.w, cw - newRect.x);
        newRect.h = Math.min(newRect.h, ch - newRect.y);
        
        cropRect = newRect;
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

    // ── Helpers ────────────────────────────────────────────────────────
    function clampOffset() {
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
            ? 'Kéo các góc/cạnh để thay đổi vùng cắt · Kéo bên trong để di chuyển ảnh · Chọn tỷ lệ bên trên'
            : 'Kéo để di chuyển ảnh · Cuộn để phóng to/thu nhỏ';
    }

    // ── Save ───────────────────────────────────────────────────────────
    function save() {
        if (!imgLoaded) return;

        if (savedOriginalImageSrc && savedOriginalImageSrc.startsWith('data:')) {
            // Ảnh gốc là dataURL → tạo Image mới để vẽ full resolution
            const tmpImg = new Image();
            tmpImg.onload = function () { doSave(tmpImg); };
            tmpImg.src = savedOriginalImageSrc;
        } else {
            // Ảnh gốc là URL hoặc không có → dùng img hiện tại
            doSave(img);
        }
    }

    function doSave(sourceImg) {
        let outputCanvas;

        if (cropRect && Math.abs(cropRect.w) >= 8 && Math.abs(cropRect.h) >= 8) {
            // --- Crop mode: xuất vùng đã chọn ---
            const x1 = cropRect.x;
            const y1 = cropRect.y;
            const rw = cropRect.w;
            const rh = cropRect.h;

            // Chuyển từ canvas px → image px
            const imgX = (x1 - offsetX) / scale;
            const imgY = (y1 - offsetY) / scale;
            const imgW = rw / scale;
            const imgH = rh / scale;

            outputCanvas        = document.createElement('canvas');
            outputCanvas.width  = Math.round(Math.abs(imgW));
            outputCanvas.height = Math.round(Math.abs(imgH));
            const octx          = outputCanvas.getContext('2d');
            octx.drawImage(sourceImg, imgX, imgY, imgW, imgH, 0, 0, Math.abs(imgW), Math.abs(imgH));
        } else {
            // --- Không crop: xuất toàn bộ ảnh gốc ---
            outputCanvas        = document.createElement('canvas');
            outputCanvas.width  = sourceImg.naturalWidth;
            outputCanvas.height = sourceImg.naturalHeight;
            const octx          = outputCanvas.getContext('2d');
            octx.drawImage(sourceImg, 0, 0);
        }

        const dataURL = outputCanvas.toDataURL('image/jpeg', 0.92);

        // Ghi vào hidden input
        const outputInput = document.querySelector(cfg.outputInput);
        if (outputInput) outputInput.value = dataURL;

        // Hiện preview
        const previewImg = document.querySelector(cfg.previewImg);
        if (previewImg) {
            // Lưu lại src gốc để edit lại sau (chỉ lưu lần đầu)
            if (!previewImg.dataset.originalSrc) {
                previewImg.dataset.originalSrc = previewImg.src;
            }
            previewImg.src = dataURL;
            previewImg.style.display = 'block';
            // Thêm controls để edit lại hoặc remove
            addPreviewControls(previewImg);
        }

        // Đánh dấu đã edit để controller biết dùng base64 thay vì file upload
        const editedFlag = document.querySelector(cfg.editedFlag || '#imageEditedFlag');
        if (editedFlag) editedFlag.value = '1';

        backdrop.classList.remove('active');
    }

    // ── Add preview controls ───────────────────────────────────────────
    function addPreviewControls(previewImg) {
        // Remove existing controls
        const existingControls = previewImg.parentNode.querySelector('.img-preview-controls');
        if (existingControls) existingControls.remove();

        // Create new controls
        const controls = document.createElement('div');
        controls.className = 'img-preview-controls';
        controls.innerHTML = `
            <button type="button" class="btn-edit-image" onclick="ImageEditor.editCurrentPreview()">
                <i class="bi bi-pencil"></i> Chỉnh sửa lại
            </button>
            <button type="button" class="btn-remove-image" onclick="ImageEditor.removeCurrentPreview()">
                <i class="bi bi-x"></i> Xóa ảnh mới
            </button>
        `;

        previewImg.parentNode.insertBefore(controls, previewImg.nextSibling);

        // Make preview clickable to edit
        previewImg.style.cursor = 'pointer';
        previewImg.onclick = function() { ImageEditor.editCurrentPreview(); };

        // Ẩn nút Chỉnh sửa/Xóa ảnh hiện tại (tránh lặp UI)
        const currentImageBtns = document.getElementById('currentImageBtns');
        if (currentImageBtns) currentImageBtns.style.display = 'none';
    }

// ── Public methods for preview controls ────────────────────────────
     function editCurrentPreview() {
         // Lấy src ảnh gốc đã lưu
         const previewImg = document.querySelector(cfg.previewImg);
         if (!previewImg) return;

         // Ưu tiên: savedOriginalImageSrc (dataURL từ file upload gốc)
         // Sau đó: data-original-src trên preview img
         // Sau đó: data-original-src trên #currentImage (trang edit)
         const currentImg = document.getElementById('currentImage');
         const srcToUse = savedOriginalImageSrc
             || previewImg.dataset.originalSrc
             || (currentImg && currentImg.dataset.originalSrc)
             || null;

         if (!srcToUse) return;

         // Nếu là dataURL (file upload) → dùng trực tiếp
         if (srcToUse.startsWith('data:')) {
             const tmpImg = new Image();
             tmpImg.onload = function () {
                 savedOriginalImageSrc = srcToUse;
                 openForEdit(tmpImg);
             };
             tmpImg.src = srcToUse;
             return;
         }

         // Nếu là URL → load với crossOrigin
         const tmpImg = new Image();
         tmpImg.crossOrigin = 'anonymous';
         tmpImg.onload = function () {
             const offscreen = document.createElement('canvas');
             offscreen.width  = tmpImg.naturalWidth;
             offscreen.height = tmpImg.naturalHeight;
             offscreen.getContext('2d').drawImage(tmpImg, 0, 0);
             offscreen.toBlob(function (blob) {
                 if (blob) {
                     const file = new File([blob], 'original.jpg', { type: 'image/jpeg' });
                     savedOriginalImageSrc = null; // reset để open() lưu lại
                     open(file);
                 } else {
                     savedOriginalImageSrc = srcToUse;
                     openForEdit(tmpImg);
                 }
             }, 'image/jpeg', 0.95);
         };
         tmpImg.onerror = function () {
             // Fallback: mở bằng URL trực tiếp
             openWithUrl(srcToUse);
         };
         tmpImg.src = srcToUse + (srcToUse.includes('?') ? '&' : '?') + '_t=' + Date.now();
     }

    function removeCurrentPreview() {
        const previewImg = document.querySelector(cfg.previewImg);
        const outputInput = document.querySelector(cfg.outputInput);
        const editedFlag = document.querySelector(cfg.editedFlag);
        const controls = document.querySelector('.img-preview-controls');

        if (previewImg) {
            previewImg.src = '';
            previewImg.style.display = 'none';
            previewImg.onclick = null;
            previewImg.style.cursor = 'auto';
            previewImg.removeAttribute('data-original-src');
        }
        if (outputInput) outputInput.value = '';
        if (editedFlag) editedFlag.value = '0';

        if (controls) {
            controls.innerHTML = `
                <button type="button" class="btn-remove-image" disabled style="opacity:0.4;cursor:not-allowed;">
                    <i class="bi bi-x"></i> Đã xóa
                </button>
            `;
        }

        // Hiện lại nút Chỉnh sửa/Xóa ảnh hiện tại (vì ảnh mới đã bị xóa)
        const currentImageBtns = document.getElementById('currentImageBtns');
        if (currentImageBtns) currentImageBtns.style.display = '';

        // Reset file input
        const input = document.querySelector(cfg.triggerInput);
        if (input) input.value = '';
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

    // Mở editor với Image object đã load sẵn (dùng khi CORS cho phép crossOrigin)
    function openWithImage(imageObj, originalSrc) {
        buildDOM();
        backdrop = document.getElementById('imgEditorBackdrop');
        canvas   = document.getElementById('imgEditorCanvas');
        ctx      = canvas.getContext('2d');

        imgLoaded = false;
        cropRect  = null;
        cropMode  = false;
        showGrid  = false;
        aspectRatio = null;
        savedOriginalImageSrc = null;

        document.getElementById('imgEditorCropBtn').classList.remove('active');
        document.getElementById('imgEditorGridBtn').classList.remove('active');
        document.getElementById('imgEditorAspectPresets').style.display = 'none';
        document.getElementById('imgEditorCanvasWrap').classList.remove('crop-mode');
        updateHint();

        img = imageObj;
        imgLoaded = true;
        // Lưu src gốc để dùng khi save
        savedOriginalImageSrc = originalSrc || img.src;
        originalImageSrc = savedOriginalImageSrc;

        resizeCanvas();
        resetTransform();
        backdrop.classList.add('active');
    }

    // Mở editor với URL trực tiếp (fallback khi CORS bị chặn hoàn toàn)
    function openWithUrl(url) {
        buildDOM();
        backdrop = document.getElementById('imgEditorBackdrop');
        canvas   = document.getElementById('imgEditorCanvas');
        ctx      = canvas.getContext('2d');

        imgLoaded = false;
        cropRect  = null;
        cropMode  = false;
        showGrid  = false;
        aspectRatio = null;
        savedOriginalImageSrc = null;

        document.getElementById('imgEditorCropBtn').classList.remove('active');
        document.getElementById('imgEditorGridBtn').classList.remove('active');
        document.getElementById('imgEditorAspectPresets').style.display = 'none';
        document.getElementById('imgEditorCanvasWrap').classList.remove('crop-mode');
        updateHint();

        img = new Image();
        img.onload = function () {
            imgLoaded = true;
            savedOriginalImageSrc = url;
            originalImageSrc = url;
            resizeCanvas();
            resetTransform();
            backdrop.classList.add('active');
        };
        img.onerror = function () {
            alert('Không thể tải ảnh để chỉnh sửa. Vui lòng tải ảnh mới.');
        };
        img.src = url;
    }

    return {
        init,
        open,
        openWithImage,
        openWithUrl,
        close,
        editCurrentPreview,
        removeCurrentPreview
    };

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

 // Expose ra window để admin.blogs.js và các inline handler có thể gọi
 window.ImageEditor = ImageEditor;