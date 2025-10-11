/**
 * Submittal & Spec Sheet Builder - Review Page (Step 2) - MVP
 * Quantity/Note management + Project Fields (no branding UI)
 *
 * @package SubmittalBuilder
 * @version 1.0.2
 */

(() => {
  'use strict';

  // ---- State ----
  const selectedKeys = new Set(JSON.parse(localStorage.getItem('sfb-selected') || '[]'));
  const selectedMap = new Map(JSON.parse(sessionStorage.getItem('sfb-selected-map') || '[]'));

  // Default any missing entries
  selectedKeys.forEach(k => {
    if (!selectedMap.has(k)) selectedMap.set(k, { quantity: 1, note: '' });
  });

  // ---- DOM Refs ----
  const rootSel = document.getElementById('sfb-selected-root');
  const projName = document.getElementById('sfb-project-name');
  const projNotes = document.getElementById('sfb-project-notes');
  const generateBtn = document.getElementById('sfb-generate-pdf-sticky');
  const backBtn = document.getElementById('sfb-back-to-products-sticky');
  const returnBtn = document.getElementById('sfb-return-to-products');
  const toastEl = document.getElementById('sfb-toast');
  const ariaLive = document.getElementById('sfb-review-status');

  // ---- Helpers ----
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
  }

  /**
   * Enrich item with specs from global products map if missing
   * @param {Object} item - Product item to enrich
   * @param {string} compositeKey - The composite key to look up full product data
   */
  function enrichWithSpecs(item, compositeKey) {
    if (item?.specs && Object.keys(item.specs).length) {
      return item;
    }

    // Try to get full product data from the global products map using composite_key
    const src = window.SFB?.productsById?.get(compositeKey);

    if (src?.specs) {
      item.specs = src.specs;
    }
    return item;
  }

  function showToast(message, duration = 2500) {
    if (!toastEl) return;
    toastEl.textContent = message;
    toastEl.classList.add('sfb-toast-visible');
    setTimeout(() => {
      toastEl.classList.remove('sfb-toast-visible');
    }, duration);
  }

  function updateAriaLive(message) {
    if (!ariaLive) return;
    ariaLive.textContent = message;
  }

  function fmtSpecs(specs) {
    if (!specs || typeof specs !== 'object') {
      return '';
    }
    const parts = [];
    if (specs.Size || specs.size) parts.push(`Size ${specs.Size || specs.size}`);
    if (specs.Thickness || specs.thickness) parts.push(`Thick ${specs.Thickness || specs.thickness}`);
    if (specs.Flange || specs.flange) parts.push(`Flange ${specs.Flange || specs.flange}`);
    if (specs.KSI || specs.ksi) parts.push(`KSI: ${specs.KSI || specs.ksi}`);
    return parts.join(' • ');
  }

  function groupByType(keys) {
    const groups = new Map();
    keys.forEach(k => {
      const p = window.sfbProductsMap?.get(k);
      const label = p?.type_label || 'Other';
      if (!groups.has(label)) groups.set(label, []);
      groups.get(label).push(k);
    });
    return groups;
  }

  function persistSelection() {
    localStorage.setItem('sfb-selected', JSON.stringify([...selectedKeys]));
    sessionStorage.setItem('sfb-selected-map', JSON.stringify([...selectedMap.entries()]));

    // CTA guard - disable if no selections
    const disabled = selectedKeys.size === 0;
    if (generateBtn) {
      generateBtn.disabled = disabled;
      generateBtn.title = disabled ? 'Please select at least one product to generate a PDF' : '';
    }

    // Update aria-live region
    const count = selectedKeys.size;
    updateAriaLive(`${count} product${count !== 1 ? 's' : ''} selected`);
  }

  // ---- Selected Renderer ----
  function renderSelected() {
    if (!rootSel) {
      console.error('[SFB Review] rootSel element not found!');
      return;
    }

    rootSel.innerHTML = '';
    const groups = groupByType([...selectedMap.keys()]);

    const reviewEmpty = document.getElementById('sfb-review-empty');

    if (groups.size === 0) {
      rootSel.style.display = 'none';
      if (reviewEmpty) reviewEmpty.style.display = 'block';
      return;
    }

    rootSel.style.display = 'block';
    if (reviewEmpty) reviewEmpty.style.display = 'none';

    groups.forEach((keys, typeLabel) => {
      const header = document.createElement('div');
      header.className = 'sfb-selected__group';
      header.innerHTML = `
        <h3 class="sfb-selected__group-title" role="heading" aria-level="3">${escapeHtml(typeLabel)}</h3>
        <button class="sfb-selected__group-btn" aria-label="Remove all items in ${escapeHtml(typeLabel)}">Remove all</button>
      `;
      header.querySelector('button').addEventListener('click', () => {
        const count = keys.length;
        keys.forEach(k => {
          selectedMap.delete(k);
          selectedKeys.delete(k);
        });
        persistSelection();
        renderSelected();
        showToast(`Removed ${count} item${count !== 1 ? 's' : ''} from ${typeLabel}`);
      });
      rootSel.appendChild(header);

      keys.forEach(k => {
        let p = window.sfbProductsMap?.get(k);
        if (p) {
          // Enrich with specs if missing
          p = enrichWithSpecs(p, k);
        }
        const { quantity = 1, note = '' } = selectedMap.get(k) || {};
        const row = document.createElement('div');
        row.className = 'sfb-row';
        row.setAttribute('role', 'button');
        row.setAttribute('tabindex', '0');
        row.dataset.key = k;

        const specsHtml = fmtSpecs(p?.specs || {});

        row.innerHTML = `
          <div class="sfb-row__left">
            <div class="sfb-row__badges">
              <span class="badge--type">${escapeHtml(p?.type_label || '')}</span>
              <span class="crumb--category">${escapeHtml(p?.category || '')}</span>
            </div>
            <div class="sfb-row__title">${escapeHtml(p?.model || k)}</div>
            <div class="sfb-row__meta">${specsHtml}</div>
          </div>
          <div class="sfb-row__right">
            <label class="sr-only" for="qty-${escapeHtml(k)}">Quantity for ${escapeHtml(p?.model || '')}</label>
            <input id="qty-${escapeHtml(k)}" class="sfb-qty" type="number" min="1" step="1" value="${quantity}">
            <button class="sfb-remove" aria-label="Remove ${escapeHtml(p?.model || 'item')}">✕</button>
          </div>
          <div class="sfb-note">
            <label style="display:block;font-size:12px;color:#374151;margin-bottom:4px;">Row note</label>
            <textarea placeholder="Add an optional note…" maxlength="500">${escapeHtml(note || '')}</textarea>
          </div>
        `;

        // Make note field always visible
        row.classList.add('is-open');

        // Interactions
        row.addEventListener('click', e => {
          // Don't do anything on row click - notes are always visible
          if (e.target.closest('input, textarea, button, label')) return;
        });

        row.addEventListener('keydown', e => {
          if (e.key === 'Delete' || e.key === 'Backspace') {
            e.preventDefault();
            removeKey(k);
          }
        });

        row.querySelector('.sfb-remove').addEventListener('click', e => {
          e.preventDefault();
          e.stopPropagation();
          removeKey(k);
        });

        row.querySelector('.sfb-qty').addEventListener('change', e => {
          e.stopPropagation();
          const val = Math.max(1, parseInt(e.target.value || '1', 10));
          selectedMap.set(k, { ...selectedMap.get(k), quantity: val });
          persistSelection();
        });

        row.querySelector('.sfb-qty').addEventListener('click', e => {
          e.stopPropagation();
        });

        row.querySelector('textarea').addEventListener('input', e => {
          selectedMap.set(k, { ...selectedMap.get(k), note: e.target.value.slice(0, 500) });
          persistSelection();
        });

        row.querySelector('textarea').addEventListener('click', e => {
          e.stopPropagation();
        });

        rootSel.appendChild(row);
      });
    });
  }

  function removeKey(k) {
    if (!selectedMap.has(k)) return;
    selectedMap.delete(k);
    selectedKeys.delete(k);
    persistSelection();
    renderSelected();
  }

  // ---- Project fields → payload stash ----
  function persistProjectFields() {
    const payload = {
      name: (projName?.value || '').trim(),
      notes: (projNotes?.value || '').slice(0, 1000)
    };
    sessionStorage.setItem('sfb-project-fields', JSON.stringify(payload));
  }

  if (projName) projName.addEventListener('input', persistProjectFields);
  if (projNotes) projNotes.addEventListener('input', persistProjectFields);

  // Restore project fields on load
  const savedProject = JSON.parse(sessionStorage.getItem('sfb-project-fields') || '{"name":"","notes":""}');
  if (projName) projName.value = savedProject.name || '';
  if (projNotes) projNotes.value = savedProject.notes || '';

  // ---- Public payload collector for PDF generation ----
  window.SFB_collectReviewPayload = function collectReviewPayload(projectNameOverride) {
    const proj = JSON.parse(sessionStorage.getItem('sfb-project-fields') || '{"name":"","notes":""}');

    // Rebuild selectedMap from persisted data if needed
    const persisted = JSON.parse(sessionStorage.getItem('sfb-selected-map') || '[]');
    if (persisted.length && selectedMap.size === 0) {
      persisted.forEach(([k, v]) => selectedMap.set(k, v));
    }

    const products = [];
    selectedMap.forEach((v, k) => {
      const product = window.sfbProductsMap?.get(k);
      if (!product) return;

      products.push({
        key: k,
        id: product.id,
        node_id: product.node_id || product.id,
        quantity: Math.max(1, v.quantity | 0),
        note: (v.note || '').slice(0, 500)
      });
    });

    return {
      project: {
        name: projectNameOverride || proj.name,
        notes: proj.notes
      },
      products
    };
  };

  // ---- Initialize ----
  renderSelected();
  persistSelection(); // Sets CTA disabled state if needed

  // Listen for products loaded event and re-render
  window.addEventListener('sfb-products-loaded', () => {
    renderSelected();
  });

  // ---- Brand Preview Initialization ----
  function initBrandPreview() {
    const container = document.getElementById('sfb-brand-preview-container');
    if (!container) return;

    const themes = [
      { key: 'engineering', name: 'Engineering', color: '#7861FF', desc: 'Classic industrial blue accent' },
      { key: 'architectural', name: 'Architectural', color: '#0ea5e9', desc: 'Modern sky blue for design work' },
      { key: 'corporate', name: 'Corporate', color: '#10b981', desc: 'Professional green accent' }
    ];

    let activeTheme = 'engineering'; // Default

    const html = themes.map(theme => `
      <div class="sfb-brand-preview-theme ${theme.key === activeTheme ? 'active' : ''}"
           data-theme="${escapeHtml(theme.key)}"
           role="button"
           tabindex="0"
           aria-pressed="${theme.key === activeTheme ? 'true' : 'false'}">
        <div class="sfb-brand-preview-theme-header">
          <div class="sfb-brand-preview-theme-color" style="background-color: ${theme.color};"></div>
          <div class="sfb-brand-preview-theme-info">
            <div class="sfb-brand-preview-theme-name">${escapeHtml(theme.name)}</div>
            <div class="sfb-brand-preview-theme-desc">${escapeHtml(theme.desc)}</div>
          </div>
        </div>
        <div class="sfb-brand-preview-theme-sample" style="color: ${theme.color};">
          <p class="sfb-brand-preview-theme-sample-text">Headers and accents will use this color</p>
        </div>
      </div>
    `).join('');

    container.innerHTML = html;

    // Attach click handlers for theme preview
    container.querySelectorAll('.sfb-brand-preview-theme').forEach(themeEl => {
      themeEl.addEventListener('click', () => {
        const themeKey = themeEl.dataset.theme;
        selectTheme(themeKey);
      });

      themeEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const themeKey = themeEl.dataset.theme;
          selectTheme(themeKey);
        }
      });
    });

    function selectTheme(themeKey) {
      activeTheme = themeKey;

      // Update active state
      container.querySelectorAll('.sfb-brand-preview-theme').forEach(el => {
        const isActive = el.dataset.theme === themeKey;
        el.classList.toggle('active', isActive);
        el.setAttribute('aria-pressed', isActive ? 'true' : 'false');
      });

      // Apply preview styles to Review step
      applyThemePreview(themeKey);
    }

    function applyThemePreview(themeKey) {
      const themeColors = {
        engineering: '#7861FF',
        architectural: '#0ea5e9',
        corporate: '#10b981'
      };

      const color = themeColors[themeKey] || themeColors.engineering;

      // Apply to review group titles
      document.querySelectorAll('.sfb-selected__group-title').forEach(el => {
        el.style.color = color;
        el.style.borderBottomColor = color;
      });

      // Apply to badges if present
      document.querySelectorAll('.badge--type').forEach(el => {
        el.style.background = `${color}20`;
        el.style.color = color;
      });
    }
  }

  // Initialize brand preview after a short delay to ensure DOM is ready
  setTimeout(initBrandPreview, 100);

  // Expose for debugging
  window.sfbReviewState = { selectedMap };
})();
