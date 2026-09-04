/* ==========================================================================
   Valley View Radio 97.7 MHz — live player + on-air clock
   Expects window.VVR_DATA (injected by vvu_radio.php) with:
     { rows, dayNames, categories, stream: {url, type}, now: {day, minutes, epoch} }
   All schedule maths runs on Africa/Accra time (GMT, no DST) so a listener in
   another timezone still sees what the station is actually broadcasting.
   ========================================================================== */
(function () {
    'use strict';

    var data = window.VVR_DATA;
    if (!data) { return; }

    var $  = function (sel, root) { return (root || document).querySelector(sel); };
    var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

    /* ----------------------------------------------------------------------
       Station clock
       ---------------------------------------------------------------------- */

    var DAY_INDEX = { Sun: 0, Mon: 1, Tue: 2, Wed: 3, Thu: 4, Fri: 5, Sat: 6 };
    var loadedAt = Date.now();
    var accraFormatter = null;

    try {
        accraFormatter = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Africa/Accra',
            hour12: false,
            weekday: 'short',
            hour: '2-digit',
            minute: '2-digit'
        });
        // Probe once — some engines accept the option and then throw on use.
        accraFormatter.formatToParts(new Date());
    } catch (e) {
        accraFormatter = null;
    }

    function stationNow() {
        if (accraFormatter) {
            try {
                var parts = {};
                accraFormatter.formatToParts(new Date()).forEach(function (p) { parts[p.type] = p.value; });
                var day = DAY_INDEX[parts.weekday];
                if (day !== undefined) {
                    return { day: day, minutes: (parseInt(parts.hour, 10) % 24) * 60 + parseInt(parts.minute, 10) };
                }
            } catch (e) { /* fall through to the server clock */ }
        }
        // Fallback: the server's Accra time at page load, advanced locally.
        var elapsed = Math.floor((Date.now() - loadedAt) / 60000);
        var total   = data.now.day * 1440 + data.now.minutes + elapsed;
        return { day: Math.floor(total / 1440) % 7, minutes: total % 1440 };
    }

    function toMinutes(hhmm) {
        var bits = hhmm.split(':');
        return parseInt(bits[0], 10) * 60 + parseInt(bits[1], 10);
    }

    function formatTime(hhmm) {
        var bits = hhmm.split(':');
        var h = parseInt(bits[0], 10);
        var suffix = h >= 12 ? 'PM' : 'AM';
        var hour = h % 12 || 12;
        return hour + ':' + bits[1] + ' ' + suffix;
    }

    function slotMatches(row, minutes) {
        var start = toMinutes(row.start);
        var end   = toMinutes(row.end);
        return end > start ? (minutes >= start && minutes < end) : (minutes >= start || minutes < end);
    }

    function categoryOf(title) {
        return data.categories[title] || 'music';
    }

    /** Mirrors vvr_on_air() in includes/radio_schedule_data.php. */
    function onAir(day, minutes) {
        var current = null, i, row;

        for (i = 0; i < data.rows.length; i++) {
            row = data.rows[i];
            if (slotMatches(row, minutes) && row.shows[day]) {
                current = { title: row.shows[day], start: row.start, end: row.end, category: categoryOf(row.shows[day]) };
                break;
            }
        }
        if (!current) {
            current = { title: 'Non-Stop Music', start: null, end: null, category: 'music' };
        }

        var next = null;
        for (var step = 0; step <= 7 && !next; step++) {
            var probe = (day + step) % 7;
            for (i = 0; i < data.rows.length; i++) {
                row = data.rows[i];
                if (!row.shows[probe]) { continue; }
                if (step === 0 && toMinutes(row.start) <= minutes) { continue; }
                if (step === 0 && row.shows[probe] === current.title) { continue; }
                next = { title: row.shows[probe], start: row.start, end: row.end, day: probe, category: categoryOf(row.shows[probe]) };
                break;
            }
        }

        return { current: current, next: next };
    }

    /* ----------------------------------------------------------------------
       On-air display
       ---------------------------------------------------------------------- */

    var els = {
        showTitle:  $('[data-vvr="show-title"]'),
        showTime:   $('[data-vvr="show-time"]'),
        showChip:   $('[data-vvr="show-chip"]'),
        nextTitle:  $('[data-vvr="next-title"]'),
        nextTime:   $('[data-vvr="next-time"]'),
        progress:   $('[data-vvr="progress"]'),
        progressBar:$('[data-vvr="progress-bar"]'),
        clock:      $('[data-vvr="clock"]'),
        miniTitle:  $('[data-vvr="mini-title"]')
    };

    function renderOnAir() {
        var now  = stationNow();
        var info = onAir(now.day, now.minutes);
        var cur  = info.current;

        if (els.showTitle) { els.showTitle.textContent = cur.title; }
        if (els.miniTitle) { els.miniTitle.textContent = cur.title; }

        if (els.showChip) {
            els.showChip.textContent = data.categoryLabels[cur.category] || cur.category;
            els.showChip.className = 'vvr-chip vvr-cat-' + cur.category;
        }

        if (els.showTime) {
            els.showTime.textContent = cur.start
                ? formatTime(cur.start) + ' – ' + formatTime(cur.end)
                : 'Between scheduled programmes';
        }

        if (els.clock) {
            els.clock.textContent = data.dayNames[now.day] + ', ' +
                formatTime(String(Math.floor(now.minutes / 60)).padStart(2, '0') + ':' +
                           String(now.minutes % 60).padStart(2, '0')) + ' GMT';
        }

        // How far through the current block we are.
        if (els.progress && els.progressBar) {
            if (cur.start) {
                var start = toMinutes(cur.start);
                var end   = toMinutes(cur.end);
                var span  = end > start ? end - start : (1440 - start) + end;
                var done  = now.minutes >= start ? now.minutes - start : (1440 - start) + now.minutes;
                els.progress.hidden = false;
                els.progressBar.style.width = Math.max(0, Math.min(100, (done / span) * 100)).toFixed(1) + '%';
            } else {
                els.progress.hidden = true;
            }
        }

        if (info.next) {
            if (els.nextTitle) { els.nextTitle.textContent = info.next.title; }
            if (els.nextTime) {
                els.nextTime.textContent = (info.next.day === now.day ? 'Today' : data.dayNames[info.next.day]) +
                    ' · ' + formatTime(info.next.start);
            }
        }

        highlightGrid(now);
    }

    /** Moves the "on air now" outline around the weekly table and day list. */
    function highlightGrid(now) {
        $$('.vvr-grid td.is-now, .vvr-dayrow.is-now').forEach(function (el) { el.classList.remove('is-now'); });
        $$('.vvr-grid tr.is-now-row').forEach(function (el) { el.classList.remove('is-now-row'); });

        $$('.vvr-grid td[data-day]').forEach(function (cell) {
            if (parseInt(cell.dataset.day, 10) !== now.day) { return; }
            if (!slotMatches({ start: cell.dataset.start, end: cell.dataset.end }, now.minutes)) { return; }
            if (!cell.classList.contains('has-show')) { return; }
            cell.classList.add('is-now');
            if (cell.parentElement) { cell.parentElement.classList.add('is-now-row'); }
        });

        $$('.vvr-dayrow[data-day]').forEach(function (rowEl) {
            if (parseInt(rowEl.dataset.day, 10) !== now.day) { return; }
            if (slotMatches({ start: rowEl.dataset.start, end: rowEl.dataset.end }, now.minutes)) {
                rowEl.classList.add('is-now');
            }
        });
    }

    /* ----------------------------------------------------------------------
       Audio player
       ---------------------------------------------------------------------- */

    var audio      = $('#vvr-audio');
    var playBtn    = $('[data-vvr="play"]');
    var miniBtn    = $('[data-vvr="mini-play"]');
    var statusEl   = $('[data-vvr="status"]');
    var liveBadge  = $('[data-vvr="live-badge"]');
    var eq         = $('[data-vvr="eq"]');
    var volume     = $('[data-vvr="volume"]');
    var muteBtn    = $('[data-vvr="mute"]');
    var VOL_KEY    = 'vvr:volume';
    var retries    = 0;
    // Pausing a live stream means dropping its source entirely, which some
    // browsers report as a media error. Without this flag that self-inflicted
    // error starts the reconnect loop on a stream the listener just stopped.
    var wantsPlay  = false;

    function setIcon(btn, name) {
        if (!btn) { return; }
        var icon = btn.querySelector('.material-symbols-outlined');
        if (icon) { icon.textContent = name; }
    }

    function setState(state, message) {
        [playBtn, miniBtn].forEach(function (btn) {
            if (!btn) { return; }
            btn.classList.toggle('is-busy', state === 'connecting');
            btn.setAttribute('aria-label', state === 'playing' ? 'Pause the live stream' : 'Play the live stream');
            btn.setAttribute('aria-pressed', state === 'playing' ? 'true' : 'false');
            setIcon(btn, state === 'playing' ? 'pause' : 'play_arrow');
        });

        if (eq) { eq.classList.toggle('is-playing', state === 'playing'); }
        if (statusEl && message) { statusEl.textContent = message; }
        if (liveBadge) {
            liveBadge.dataset.state = (state === 'error' || state === 'offline') ? 'offline' : 'live';
        }
    }

    // Reading or writing localStorage throws outright in some privacy modes,
    // which would take the whole player down with it.
    function rememberVolume(value) {
        try {
            if (value === undefined) { return parseFloat(localStorage.getItem(VOL_KEY)); }
            localStorage.setItem(VOL_KEY, String(value));
        } catch (e) { /* volume just will not persist */ }
        return NaN;
    }

    if (audio && data.stream.url) {
        var saved = rememberVolume();
        audio.volume = isNaN(saved) ? 0.85 : Math.min(1, Math.max(0, saved));
        if (volume) { volume.value = String(Math.round(audio.volume * 100)); }

        function togglePlay() {
            if (audio.paused) {
                wantsPlay = true;
                retries = 0;
                setState('connecting', 'Connecting to the studio…');
                // Re-point at the stream each time: a live source that was cut
                // cannot be resumed from its old buffer, it has to be refetched.
                audio.src = data.stream.url;
                audio.load();
                var attempt = audio.play();
                if (attempt && attempt.catch) {
                    attempt.catch(function () {
                        wantsPlay = false;
                        setState('error', 'Could not start playback. Tap play to try again.');
                    });
                }
            } else {
                wantsPlay = false;
                audio.pause();
                audio.removeAttribute('src');
                audio.load();
                setState('paused', 'Paused — tap play to reconnect.');
            }
        }

        [playBtn, miniBtn].forEach(function (btn) {
            if (btn) { btn.addEventListener('click', togglePlay); }
        });

        audio.addEventListener('playing', function () {
            retries = 0;
            setState('playing', 'Live from the VVR studio, Oyibi.');
        });
        audio.addEventListener('waiting', function () { setState('connecting', 'Buffering…'); });
        audio.addEventListener('pause', function () {
            if (!audio.ended) { setState('paused', 'Paused — tap play to reconnect.'); }
        });
        audio.addEventListener('error', function () {
            if (!wantsPlay) { return; }
            // Streams drop; give it a few quiet retries before saying so.
            if (retries < 3) {
                retries++;
                setState('connecting', 'Stream interrupted — reconnecting (' + retries + '/3)…');
                window.setTimeout(function () {
                    audio.src = data.stream.url + (data.stream.url.indexOf('?') === -1 ? '?' : '&') + 'r=' + Date.now();
                    audio.load();
                    audio.play().catch(function () { /* handled by the next error event */ });
                }, 2000 * retries);
            } else {
                wantsPlay = false;
                setState('error', 'The stream is not responding right now. Please try again shortly.');
            }
        });

        if (volume) {
            volume.addEventListener('input', function () {
                audio.volume = volume.value / 100;
                audio.muted = audio.volume === 0;
                rememberVolume(audio.volume);
                setIcon(muteBtn, audio.muted ? 'volume_off' : (audio.volume < 0.5 ? 'volume_down' : 'volume_up'));
            });
        }

        if (muteBtn) {
            muteBtn.addEventListener('click', function () {
                audio.muted = !audio.muted;
                setIcon(muteBtn, audio.muted ? 'volume_off' : (audio.volume < 0.5 ? 'volume_down' : 'volume_up'));
                muteBtn.setAttribute('aria-pressed', audio.muted ? 'true' : 'false');
            });
        }
    } else if (data.stream.type === 'none') {
        // No stream configured: leave the copy the server rendered in place and
        // just take the badge out of its "we are broadcasting" state.
        setState('offline', '');
    }

    /* ----------------------------------------------------------------------
       Sticky mini player — appears once the main player scrolls away
       ---------------------------------------------------------------------- */

    var mini    = $('[data-vvr="mini"]');
    var anchor  = $('#listen-live');
    var miniOff = false;

    if (mini && anchor && 'IntersectionObserver' in window) {
        var closeBtn = $('[data-vvr="mini-close"]');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                miniOff = true;
                mini.classList.remove('is-visible');
            });
        }
        new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                mini.classList.toggle('is-visible', !miniOff && !entry.isIntersecting && entry.boundingClientRect.top < 0);
            });
        }, { threshold: 0 }).observe(anchor);
    }

    /* ----------------------------------------------------------------------
       Schedule: day tabs and view switch
       ---------------------------------------------------------------------- */

    $$('[data-vvr-daytab]').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var day = tab.dataset.vvrDaytab;
            $$('[data-vvr-daytab]').forEach(function (t) {
                t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
            });
            $$('[data-vvr-daypanel]').forEach(function (panel) {
                panel.hidden = panel.dataset.vvrDaypanel !== day;
            });
        });
    });

    $$('[data-vvr-view]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var view = btn.dataset.vvrView;
            $$('[data-vvr-view]').forEach(function (b) {
                b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
            });
            $$('[data-vvr-viewpanel]').forEach(function (panel) {
                panel.hidden = panel.dataset.vvrViewpanel !== view;
            });
        });
    });

    /* ---------------------------------------------------------------------- */

    renderOnAir();
    window.setInterval(renderOnAir, 20000);
}());
