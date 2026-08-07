/**
 * Admin upload guard.
 *
 * Camera and phone photos land in the CMS at 4000-6000px and 3-12MB each. Any
 * one of those can exceed the web server's request-size limit, which produces
 * a bare "413 Request Entity Too Large" page before PHP ever runs — so no
 * amount of PHP configuration catches it.
 *
 * This shrinks oversized images in the browser before the form is submitted.
 * The site never displays anything wider than ~1920px and generates its own
 * thumbnails anyway, so nothing of value is lost, and a 12MB upload becomes a
 * few hundred KB — comfortably under any host's limit.
 *
 * Deliberately left alone:
 *   - non-images (PDF, Word, Excel) — nothing to resize
 *   - GIFs — re-encoding would drop the animation
 *   - images already small enough — no pointless re-compression
 *   - PNGs with transparency when WebP is unavailable — JPEG would black out
 *     the transparent areas
 */
(function () {
    'use strict';

    var MAX_DIMENSION = 2000;      // px on the longest edge
    var SIZE_THRESHOLD = 1200000;  // ~1.2MB — below this, leave the file alone
    var QUALITY = 0.85;

    // Bail out on browsers without the APIs needed to swap the file back in
    var supported = (typeof DataTransfer !== 'undefined') &&
                    (typeof HTMLCanvasElement !== 'undefined') &&
                    !!HTMLCanvasElement.prototype.toBlob;

    if (!supported) {
        return;
    }

    var canWebp = (function () {
        var c = document.createElement('canvas');
        c.width = c.height = 1;
        return c.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    })();

    function humanSize(bytes) {
        return bytes >= 1048576
            ? (bytes / 1048576).toFixed(1) + 'MB'
            : Math.round(bytes / 1024) + 'KB';
    }

    function shouldProcess(file) {
        if (!file || !file.type || file.type.indexOf('image/') !== 0) return false;
        if (file.type === 'image/gif') return false;   // keep animation intact
        if (file.type === 'image/svg+xml') return false;
        return file.size > SIZE_THRESHOLD;
    }

    /** Resize one File; resolves with the original if anything goes wrong. */
    function shrink(file) {
        return new Promise(function (resolve) {
            var url = URL.createObjectURL(file);
            var img = new Image();

            img.onload = function () {
                URL.revokeObjectURL(url);

                var scale = Math.min(1, MAX_DIMENSION / Math.max(img.width, img.height));

                // Already within bounds and merely a heavy file — still worth
                // re-encoding, but never upscale.
                var w = Math.round(img.width * scale);
                var h = Math.round(img.height * scale);

                var canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;

                var ctx = canvas.getContext('2d');
                if (!ctx) { resolve(file); return; }

                var isPng = file.type === 'image/png';
                var type = canWebp ? 'image/webp' : (isPng ? 'image/png' : 'image/jpeg');

                // JPEG has no alpha channel — lay down white first so any
                // transparency renders as white instead of black.
                if (type === 'image/jpeg') {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, w, h);
                }

                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, 0, 0, w, h);

                canvas.toBlob(function (blob) {
                    // Keep the original if re-encoding somehow made it bigger
                    if (!blob || blob.size >= file.size) { resolve(file); return; }

                    var ext = type === 'image/webp' ? '.webp'
                            : type === 'image/png'  ? '.png'
                            : '.jpg';
                    var base = file.name.replace(/\.[^.]+$/, '') || 'image';

                    try {
                        resolve(new File([blob], base + ext, {
                            type: type,
                            lastModified: Date.now()
                        }));
                    } catch (e) {
                        resolve(file);   // File constructor unavailable
                    }
                }, type, QUALITY);
            };

            img.onerror = function () {
                URL.revokeObjectURL(url);
                resolve(file);
            };

            img.src = url;
        });
    }

    function note(input, text, isError) {
        var id = 'upload-note-' + (input.dataset.guardId || (input.dataset.guardId = Math.random().toString(36).slice(2)));
        var el = document.getElementById(id);
        if (!el) {
            el = document.createElement('div');
            el.id = id;
            el.style.cssText = 'font-size:12px;margin-top:4px;';
            input.parentNode.insertBefore(el, input.nextSibling);
        }
        el.style.color = isError ? '#c0392b' : '#15803d';
        el.textContent = text;
    }

    // Delegated so it also covers inputs inside modals added after page load
    document.addEventListener('change', function (event) {
        var input = event.target;
        if (!input || input.tagName !== 'INPUT' || input.type !== 'file') return;
        if (!input.files || !input.files.length) return;

        var files = Array.prototype.slice.call(input.files);
        if (!files.some(shouldProcess)) return;

        var before = files.reduce(function (sum, f) { return sum + f.size; }, 0);
        note(input, 'Optimising image…');

        Promise.all(files.map(function (f) {
            return shouldProcess(f) ? shrink(f) : Promise.resolve(f);
        })).then(function (processed) {
            var after = processed.reduce(function (sum, f) { return sum + f.size; }, 0);

            if (after >= before) {
                note(input, 'Image already optimised (' + humanSize(before) + ').');
                return;
            }

            try {
                var dt = new DataTransfer();
                processed.forEach(function (f) { dt.items.add(f); });
                input.files = dt.files;
                note(input, 'Image optimised: ' + humanSize(before) + ' → ' + humanSize(after) + ' before upload.');
            } catch (e) {
                note(input, 'Could not optimise automatically — if the upload fails, resize the image first.', true);
            }
        }).catch(function () {
            note(input, 'Could not optimise automatically — if the upload fails, resize the image first.', true);
        });
    }, false);
})();
