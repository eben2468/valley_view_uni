/**
 * Admin image cropper.
 *
 * Any file input marked with data-crop opens a crop dialog as soon as an
 * image is picked. The cropped result replaces the picked file in the input,
 * so the form uploads exactly what the editor framed — no server changes.
 *
 *   <input type="file" accept="image/*" data-crop="16/10">
 *
 * data-crop is the starting aspect ratio ("16/10", "1/1", "free"). The editor
 * can switch ratio, zoom, rotate and flip in the dialog, or keep the original
 * image untouched.
 *
 * Ordering with upload-guard.js: this listens in the capture phase and stops
 * the original change event, then re-dispatches "change" once the editor has
 * decided. upload-guard.js (and any page preview script) therefore only ever
 * sees the final file, and still shrinks it if it is heavy.
 *
 * Cropper.js is loaded from cdnjs (allowed by the site CSP) on first use only.
 */
(function () {
    'use strict';

    var CROPPER_JS  = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js';
    var CROPPER_CSS = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css';
    var MAX_EDGE = 2600;   // matches upload-guard.js — the site never shows more
    var QUALITY = 0.92;

    var supported = (typeof DataTransfer !== 'undefined') &&
                    !!HTMLCanvasElement.prototype.toBlob;
    if (!supported) return;

    var PRESETS = [
        { label: 'Free',  value: NaN },
        { label: '1:1',   value: 1 },
        { label: '4:3',   value: 4 / 3 },
        { label: '16:10', value: 16 / 10 },
        { label: '16:9',  value: 16 / 9 },
        { label: '8:9',   value: 8 / 9 }
    ];

    function parseRatio(text) {
        if (!text || text === 'free') return NaN;
        var parts = String(text).split(/[\/:]/);
        var r = parts.length === 2 ? parseFloat(parts[0]) / parseFloat(parts[1]) : parseFloat(text);
        return isFinite(r) && r > 0 ? r : NaN;
    }

    /* ── Lazy-load Cropper.js ── */
    var loading = null;
    function loadCropper() {
        if (window.Cropper) return Promise.resolve();
        if (loading) return loading;
        loading = new Promise(function (resolve, reject) {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = CROPPER_CSS;
            document.head.appendChild(link);

            var s = document.createElement('script');
            s.src = CROPPER_JS;
            s.onload = function () { resolve(); };
            s.onerror = function () { loading = null; reject(new Error('Cropper failed to load')); };
            document.head.appendChild(s);
        });
        return loading;
    }

    /* ── Dialog ── */
    var styleAdded = false;
    function addStyles() {
        if (styleAdded) return;
        styleAdded = true;
        var css = [
            '.vcrop{position:fixed;inset:0;z-index:20000;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.72)}',
            '.vcrop-box{display:flex;flex-direction:column;width:100%;max-width:960px;max-height:100%;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 30px 60px -20px rgba(0,0,0,.5)}',
            '.vcrop-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 18px;border-bottom:1px solid #e5e7eb}',
            '.vcrop-title{margin:0;font-size:17px;font-weight:700;color:#1e3a8a}',
            '.vcrop-x{border:0;background:none;font-size:24px;line-height:1;color:#6b7280;cursor:pointer}',
            '.vcrop-stage{flex:1 1 auto;min-height:260px;height:60vh;background:#0f172a}',
            '.vcrop-stage img{display:block;max-width:100%}',
            '.vcrop-tools{display:flex;flex-wrap:wrap;align-items:center;gap:8px;padding:12px 18px;border-top:1px solid #e5e7eb;background:#f8fafc}',
            '.vcrop-group{display:flex;flex-wrap:wrap;gap:6px;align-items:center}',
            '.vcrop-sep{width:1px;height:24px;background:#e5e7eb;margin:0 4px}',
            '.vcrop-tools button{border:1px solid #d1d5db;background:#fff;color:#1f2937;border-radius:999px;padding:5px 12px;font-size:13px;font-weight:600;cursor:pointer}',
            '.vcrop-tools button:hover{border-color:#1e3a8a;color:#1e3a8a}',
            '.vcrop-tools button.is-active{background:#1e3a8a;border-color:#1e3a8a;color:#fff}',
            '.vcrop-hint{font-size:12px;color:#6b7280;margin-left:auto}',
            '.vcrop-foot{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:10px;padding:14px 18px;border-top:1px solid #e5e7eb}',
            '.vcrop-foot button{border-radius:999px;padding:9px 20px;font-size:14px;font-weight:700;cursor:pointer;border:1.5px solid #1e3a8a}',
            '.vcrop-keep{background:#fff;color:#1e3a8a}',
            '.vcrop-ok{background:#1e3a8a;color:#fff}',
            '.vcrop-ok:hover{background:#172554}',
            '.vcrop-ok[disabled]{opacity:.6;cursor:wait}',
            '.vcrop-preview{display:flex;align-items:center;gap:10px;margin-top:8px;font-size:12px;color:#15803d}',
            '.vcrop-preview img{width:84px;height:56px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb}',
            '.vcrop-preview button{border:0;background:none;color:#1e3a8a;font-weight:700;text-decoration:underline;cursor:pointer;padding:0}',
            '@media (max-width:600px){.vcrop-stage{height:50vh}.vcrop-hint{display:none}}'
        ].join('');
        var st = document.createElement('style');
        st.textContent = css;
        document.head.appendChild(st);
    }

    function el(tag, cls, text) {
        var e = document.createElement(tag);
        if (cls) e.className = cls;
        if (text != null) e.textContent = text;
        return e;
    }

    function button(label, cls, onClick, title) {
        var b = el('button', cls, label);
        b.type = 'button';
        if (title) b.title = title;
        b.addEventListener('click', onClick);
        return b;
    }

    /**
     * Opens the dialog for one file. Resolves with the cropped File, the
     * original File (Keep original), or null (dialog closed / cancelled).
     */
    function openDialog(file, startRatio) {
        return new Promise(function (resolve) {
            addStyles();
            var url = URL.createObjectURL(file);
            var overlay = el('div', 'vcrop');
            overlay.setAttribute('role', 'dialog');
            overlay.setAttribute('aria-modal', 'true');
            var box = el('div', 'vcrop-box');

            var head = el('div', 'vcrop-head');
            head.appendChild(el('h3', 'vcrop-title', 'Crop image'));
            var stage = el('div', 'vcrop-stage');
            var img = el('img');
            img.alt = '';
            img.src = url;
            stage.appendChild(img);

            var cropper = null;
            var done = false;

            function finish(result) {
                if (done) return;
                done = true;
                document.removeEventListener('keydown', onKey, true);
                if (cropper) cropper.destroy();
                URL.revokeObjectURL(url);
                overlay.remove();
                resolve(result);
            }
            function onKey(e) {
                if (e.key === 'Escape') { e.preventDefault(); finish(null); }
            }

            head.appendChild(button('×', 'vcrop-x', function () { finish(null); }, 'Cancel'));

            // Ratio buttons
            var tools = el('div', 'vcrop-tools');
            var ratios = el('div', 'vcrop-group');
            var ratioButtons = [];
            var presets = PRESETS.slice();
            if (!isNaN(startRatio) && !presets.some(function (p) { return Math.abs(p.value - startRatio) < 0.001; })) {
                presets.splice(1, 0, { label: 'Card', value: startRatio });
            }
            presets.forEach(function (p) {
                var b = button(p.label, '', function () {
                    if (cropper) cropper.setAspectRatio(p.value);
                    ratioButtons.forEach(function (x) { x.classList.toggle('is-active', x === b); });
                });
                if ((isNaN(p.value) && isNaN(startRatio)) || Math.abs(p.value - startRatio) < 0.001) {
                    b.classList.add('is-active');
                }
                ratioButtons.push(b);
                ratios.appendChild(b);
            });
            tools.appendChild(ratios);
            tools.appendChild(el('span', 'vcrop-sep'));

            var actions = el('div', 'vcrop-group');
            actions.appendChild(button('Zoom +', '', function () { cropper && cropper.zoom(0.1); }));
            actions.appendChild(button('Zoom −', '', function () { cropper && cropper.zoom(-0.1); }));
            actions.appendChild(button('Rotate', '', function () { cropper && cropper.rotate(90); }, 'Rotate 90°'));
            var flipX = 1;
            actions.appendChild(button('Flip', '', function () {
                if (!cropper) return;
                flipX = -flipX;
                cropper.scaleX(flipX);
            }, 'Flip horizontally'));
            actions.appendChild(button('Reset', '', function () { cropper && cropper.reset(); }));
            tools.appendChild(actions);
            tools.appendChild(el('span', 'vcrop-hint', 'Drag to move · scroll to zoom'));

            var foot = el('div', 'vcrop-foot');
            foot.appendChild(button('Keep original', 'vcrop-keep', function () { finish(file); }));
            var ok = button('Crop & use', 'vcrop-ok', function () {
                if (!cropper) return;
                ok.disabled = true;
                ok.textContent = 'Cropping…';
                var canvas = cropper.getCroppedCanvas({
                    maxWidth: MAX_EDGE,
                    maxHeight: MAX_EDGE,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                    fillColor: file.type === 'image/png' ? 'transparent' : '#fff'
                });
                if (!canvas) { finish(file); return; }
                var type = file.type === 'image/png' ? 'image/png'
                         : (file.type === 'image/webp' ? 'image/webp' : 'image/jpeg');
                canvas.toBlob(function (blob) {
                    if (!blob) { finish(file); return; }
                    var ext = type === 'image/png' ? '.png' : (type === 'image/webp' ? '.webp' : '.jpg');
                    var base = (file.name || 'image').replace(/\.[^.]+$/, '') || 'image';
                    try {
                        finish(new File([blob], base + '-cropped' + ext, { type: type, lastModified: Date.now() }));
                    } catch (e) {
                        finish(file);
                    }
                }, type, QUALITY);
            });
            foot.appendChild(ok);

            box.appendChild(head);
            box.appendChild(stage);
            box.appendChild(tools);
            box.appendChild(foot);
            overlay.appendChild(box);
            overlay.addEventListener('mousedown', function (e) { if (e.target === overlay) finish(null); });
            document.body.appendChild(overlay);
            document.addEventListener('keydown', onKey, true);

            img.addEventListener('load', function () {
                cropper = new window.Cropper(img, {
                    aspectRatio: startRatio,
                    viewMode: 1,
                    autoCropArea: 1,
                    dragMode: 'move',
                    background: false,
                    responsive: true,
                    checkOrientation: true
                });
            });
        });
    }

    /* ── Preview + "Crop again" under the input ── */
    var originals = new WeakMap();

    function showPreview(input, file, cropped) {
        var id = input.dataset.cropPreviewId || (input.dataset.cropPreviewId = 'vcrop-' + Math.random().toString(36).slice(2));
        var box = document.getElementById(id);
        if (!box) {
            addStyles();
            box = el('div', 'vcrop-preview');
            box.id = id;
            input.parentNode.insertBefore(box, input.nextSibling);
        }
        box.innerHTML = '';
        var thumb = el('img');
        thumb.alt = '';
        var url = URL.createObjectURL(file);
        thumb.onload = function () { URL.revokeObjectURL(url); };
        thumb.src = url;
        box.appendChild(thumb);
        box.appendChild(el('span', '', cropped ? 'Cropped image ready to upload.' : 'Original image will be uploaded.'));
        box.appendChild(button('Crop again', '', function () {
            var orig = originals.get(input);
            if (orig) run(input, orig);
        }));
    }

    function setFile(input, file) {
        var dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
    }

    function redispatch(input) {
        input.dataset.cropPass = '1';
        input.dispatchEvent(new Event('change', { bubbles: true }));
        delete input.dataset.cropPass;
    }

    function run(input, file) {
        var ratio = parseRatio(input.getAttribute('data-crop'));
        loadCropper().then(function () {
            return openDialog(file, ratio);
        }).then(function (result) {
            if (result === null) {
                // Cancelled: clear the picker so nothing unintended uploads
                // (a previous crop, if any, stays in place).
                if (!input.dataset.cropHasResult) {
                    input.value = '';
                    var prev = document.getElementById(input.dataset.cropPreviewId || '');
                    if (prev) prev.remove();
                }
                return;
            }
            try { setFile(input, result); } catch (e) { return; }
            input.dataset.cropHasResult = '1';
            showPreview(input, result, result !== file);
            redispatch(input);
        }).catch(function () {
            // Cropper could not load (offline?) — carry on with the original.
            redispatch(input);
        });
    }

    document.addEventListener('change', function (event) {
        var input = event.target;
        if (!input || input.tagName !== 'INPUT' || input.type !== 'file') return;
        if (!input.hasAttribute('data-crop') || input.multiple) return;
        if (input.dataset.cropPass) return;          // our own re-dispatch
        var file = input.files && input.files[0];
        if (!file || !file.type || file.type.indexOf('image/') !== 0) return;
        if (file.type === 'image/gif' || file.type === 'image/svg+xml') return;

        // Hold everyone else (upload-guard, page previews) until cropping is done
        event.stopImmediatePropagation();
        originals.set(input, file);
        delete input.dataset.cropHasResult;
        run(input, file);
    }, true);
})();
