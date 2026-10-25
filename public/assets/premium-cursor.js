/*
 * BD SOURCING HUT — Dependency-free cinematic cursor.
 * The cursor is created only at runtime and cannot block clicks or scrolling.
 * Works with both Laravel Blade layouts, including dynamically added links.
 */
(() => {
  'use strict';

  function init() {
    const body = document.body;
    if (!body || !window.matchMedia || window.__bdLuxuryCursorLoaded) return;
    window.__bdLuxuryCursorLoaded = true;

    const isAdmin = body.classList.contains('admin-body');
    const motionValue = getComputedStyle(document.documentElement).getPropertyValue('--motion-strength').trim();
    const n = Number(motionValue);
    const intensity = isAdmin ? .68 : (motionValue && Number.isFinite(n) ? Math.max(0, Math.min(1, n)) : .75);
    if (intensity <= 0) return; // Respect the public theme's animation-intensity setting.

    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    // Public scroll progress works even when the user scrolls with the keyboard.
    // Do not add another progress bar to the admin workspace.
    if (!isAdmin) {
      const progress = document.createElement('div');
      progress.className = 'bd-scroll-progress';
      progress.setAttribute('aria-hidden', 'true');
      const line = document.createElement('span');
      line.className = 'bd-scroll-progress__line';
      progress.appendChild(line);
      body.appendChild(progress);

      let queued = false;
      const setScrollProgress = () => {
        queued = false;
        const maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
        const fraction = maxScroll > 0 ? Math.min(1, Math.max(0, window.scrollY / maxScroll)) : 0;
        line.style.transform = `scaleX(${fraction.toFixed(5)})`;
      };
      const queueScrollProgress = () => {
        if (queued) return;
        queued = true;
        window.requestAnimationFrame(setScrollProgress);
      };
      queueScrollProgress();
      window.addEventListener('scroll', queueScrollProgress, { passive: true });
      window.addEventListener('resize', queueScrollProgress, { passive: true });
      window.addEventListener('load', queueScrollProgress, { once: true });
    }

    const layer = document.createElement('div');
    layer.className = 'bd-cursor-layer';
    layer.setAttribute('aria-hidden', 'true');
    const ring = document.createElement('span');
    ring.className = 'bd-cursor-ring';
    const label = document.createElement('span');
    label.className = 'bd-cursor-label';
    ring.appendChild(label);
    const dot = document.createElement('span');
    dot.className = 'bd-cursor-dot';
    layer.appendChild(ring);
    layer.appendChild(dot);
    body.appendChild(layer);

    let px = -100;
    let py = -100;
    let ringX = -100;
    let ringY = -100;
    let frame = 0;
    let positioned = false;
    let pulseTimeout = 0;
    let currentLabel = '';
    let currentInteractive = false;
    let currentField = false;

    const canAnimate = () => finePointer.matches && !reducedMotion.matches;
    const locate = (node, x, y) => {
      node.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0) translate(-50%, -50%)`;
    };

    const follow = () => {
      frame = 0;
      // A light spring effect: sharp central dot + softly trailing outer ring.
      const ease = .13 + intensity * .13;
      ringX += (px - ringX) * ease;
      ringY += (py - ringY) * ease;
      locate(ring, ringX, ringY);
      if (Math.abs(px - ringX) > .22 || Math.abs(py - ringY) > .22) {
        frame = window.requestAnimationFrame(follow);
      }
    };

    const updateTarget = element => {
      const field = element?.closest('input:not([type="submit"]):not([type="button"]):not([type="reset"]), textarea, select, [contenteditable], [role="textbox"]');
      const interactive = field ? null : element?.closest('a, button, summary, [role="button"], input[type="submit"], input[type="button"], [data-cursor-label]');
      const hinted = interactive?.getAttribute('data-cursor-label');
      const viewItem = interactive?.matches('.collection-tile, .service-row, .related-tile, .index-service') ||
        Boolean(interactive?.closest('.collection-tile, .service-row, .related-tile, .index-service'));
      const nextLabel = interactive ? (hinted || (viewItem ? 'VIEW' : (interactive.matches('button, input[type="submit"], input[type="button"]') ? 'GO' : '↗'))) : '';
      const isInteractive = Boolean(interactive);
      const isField = Boolean(field);
      if (currentLabel !== nextLabel) {
        label.textContent = nextLabel.substring(0, 10);
        currentLabel = nextLabel;
      }
      if (currentInteractive !== isInteractive) {
        layer.classList.toggle('is-interactive', isInteractive);
        currentInteractive = isInteractive;
      }
      if (currentField !== isField) {
        body.classList.toggle('bd-cursor-in-field', isField);
        currentField = isField;
      }
    };

    const disable = () => {
      body.classList.remove('bd-cursor-active', 'bd-cursor-in-field');
      layer.classList.remove('is-interactive', 'is-scrolling', 'is-pressed');
      currentInteractive = false;
      currentField = false;
      currentLabel = '';
      label.textContent = '';
      positioned = false;
      if (frame) window.cancelAnimationFrame(frame);
      frame = 0;
      window.clearTimeout(pulseTimeout);
      pulseTimeout = 0;
    };

    document.addEventListener('pointermove', event => {
      if (event.pointerType && event.pointerType !== 'mouse') return;
      if (!canAnimate()) {
        disable();
        return;
      }
      px = event.clientX;
      py = event.clientY;
      if (!positioned) {
        positioned = true;
        ringX = px;
        ringY = py;
        locate(ring, px, py);
      }
      locate(dot, px, py);
      updateTarget(event.target instanceof Element ? event.target : null);
      body.classList.add('bd-cursor-active');
      if (!frame) frame = window.requestAnimationFrame(follow);
    }, { passive: true });

    const pulseScroll = () => {
      if (!body.classList.contains('bd-cursor-active') || !canAnimate()) return;
      layer.classList.add('is-scrolling');
      window.clearTimeout(pulseTimeout);
      pulseTimeout = window.setTimeout(() => layer.classList.remove('is-scrolling'), 240);
    };
    window.addEventListener('scroll', pulseScroll, { passive: true });
    window.addEventListener('wheel', pulseScroll, { passive: true });

    document.addEventListener('pointerdown', event => {
      if (event.pointerType && event.pointerType !== 'mouse') return;
      if (body.classList.contains('bd-cursor-active')) layer.classList.add('is-pressed');
    }, { passive: true });
    const release = () => layer.classList.remove('is-pressed');
    document.addEventListener('pointerup', release, { passive: true });
    document.addEventListener('pointercancel', release, { passive: true });

    document.documentElement.addEventListener('pointerleave', disable, { passive: true });
    window.addEventListener('blur', disable);
    document.addEventListener('keydown', event => {
      // Keyboard users should get an unmodified focus and pointer experience.
      if (['Tab', 'Escape', 'Enter', ' '].includes(event.key)) disable();
    });

    const onPreferencesChanged = () => {
      if (!canAnimate()) disable();
    };
    finePointer.addEventListener?.('change', onPreferencesChanged);
    reducedMotion.addEventListener?.('change', onPreferencesChanged);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
