(function(){
  // nonce for REST
  if (window.SFB_APP && SFB_APP.nonce && wp.apiFetch && wp.apiFetch.createNonceMiddleware) {
    wp.apiFetch.use( wp.apiFetch.createNonceMiddleware(SFB_APP.nonce) );
  }

  function qs(sel, el=document){ return el.querySelector(sel); }
  function qsa(sel, el=document){ return [...el.querySelectorAll(sel)]; }
  function el(tag, attrs={}, ...kids){
    const n = document.createElement(tag);
    Object.entries(attrs).forEach(([k,v])=>{
      if (k==='class') n.className=v;
      else if (k==='dataset') Object.entries(v).forEach(([dk,dv])=> n.dataset[dk]=dv);
      else if (k.startsWith('on') && typeof v === 'function') n.addEventListener(k.slice(2).toLowerCase(), v);
      else n.setAttribute(k,v);
    });
    kids.forEach(k=> typeof k === 'string' ? n.appendChild(document.createTextNode(k)) : (k && n.appendChild(k)));
    return n;
  }

  function buildTree(flat){
    const byId={}; flat.forEach(n=>byId[n.id]={...n,children:[]});
    const roots=[]; flat.forEach(n=> (n.parent_id && byId[n.parent_id]) ? byId[n.parent_id].children.push(byId[n.id]) : roots.push(byId[n.id]));
    return roots;
  }

  function tooltip(meta){
    const t = [];
    if (meta.size) t.push(`Size: ${meta.size}`);
    if (meta.flange) t.push(`Flange: ${meta.flange}`);
    if (meta.thickness) t.push(`Thickness: ${meta.thickness}`);
    if (meta.ksi) t.push(`KSI: ${meta.ksi}`);
    return t.join('\n');
  }

  // Shared utility: create a collapsible header with arrow
  function createCollapsibleHeader(titleText, onToggle, initialOpen = false) {
    const header = el('div', {
      class: 'sfb-collapse-header',
      role: 'button',
      tabindex: '0',
      'aria-expanded': initialOpen ? 'true' : 'false'
    });
    const arrow = el('span', { class: 'sfb-arrow' + (initialOpen ? ' rotated' : '') }, '▸');
    const label = el('span', { class: 'sfb-collapse-title' }, titleText);

    header.append(arrow, label);

    const toggle = () => {
      const isOpen = header.classList.toggle('open');
      arrow.classList.toggle('rotated', isOpen);
      header.setAttribute('aria-expanded', isOpen);
      if (typeof onToggle === 'function') onToggle(isOpen);
    };

    header.addEventListener('click', toggle);
    header.addEventListener('keypress', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        toggle();
      }
    });

    return { header, arrow, toggle };
  }

  // === Draft storage (Phase 1: localStorage) =========================
  const DRAFT_KEY = 'sfb_draft_v1';

  function saveDraftLocal() {
    try {
      const items = [...selected.values()];
      const meta = getFormMeta();
      const draft = {
        version: 1,
        ts: Date.now(),
        items,
        meta
      };
      localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
    } catch (e) { /* ignore */ }
  }

  function loadDraftLocal() {
    try {
      const raw = localStorage.getItem(DRAFT_KEY);
      if (!raw) return null;
      const draft = JSON.parse(raw);
      if (!draft || draft.version !== 1) return null;
      return draft;
    } catch (e) { return null; }
  }

  function clearDraftLocal() {
    try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
  }

  // Throttle saves so we don't hammer storage
  let saveTimer = null;
  function scheduleDraftSave() {
    if (saveTimer) return;
    saveTimer = setTimeout(() => {
      saveTimer = null;
      saveDraftLocal();
    }, 400);
  }

  function getFormMeta() {
    return {
      project: qs('#sfb-meta-project')?.value || '',
      contractor: qs('#sfb-meta-contractor')?.value || '',
      submittal: qs('#sfb-meta-submittal')?.value || '',
      preset: qs('#sfb-meta-preset')?.value || 'packet',
      format: qs('#sfb-meta-format')?.value || 'pdf',
      include_cover: qs('#sfb-meta-cover')?.checked || false,
      include_leed: qs('#sfb-meta-leed')?.checked || false
    };
  }

  function applyFormMeta(meta) {
    if (!meta) return;
    const proj = qs('#sfb-meta-project');
    const contr = qs('#sfb-meta-contractor');
    const subm = qs('#sfb-meta-submittal');
    const preset = qs('#sfb-meta-preset');
    const format = qs('#sfb-meta-format');
    const cover = qs('#sfb-meta-cover');
    const leed = qs('#sfb-meta-leed');

    if (proj && meta.project) proj.value = meta.project;
    if (contr && meta.contractor) contr.value = meta.contractor;
    if (subm && meta.submittal) subm.value = meta.submittal;
    if (preset && meta.preset) preset.value = meta.preset;
    if (format && meta.format) format.value = meta.format;
    if (cover && meta.include_cover !== undefined) cover.checked = meta.include_cover;
    if (leed && meta.include_leed !== undefined) leed.checked = meta.include_leed;
  }

  function showRestoreBanner(draft) {
    const bar = document.createElement('div');
    bar.className = 'sfb-restore';
    bar.innerHTML = `
      <div class="sfb-restore-inner">
        <span>We found your last selections from ${new Date(draft.ts).toLocaleString()}.</span>
        <button type="button" id="sfb-restore-btn">Restore</button>
        <button type="button" id="sfb-dismiss-btn">Dismiss</button>
      </div>
    `;
    document.body.appendChild(bar);
    document.getElementById('sfb-restore-btn').onclick = () => {
      restoreDraft(draft);
      bar.remove();
    };
    document.getElementById('sfb-dismiss-btn').onclick = () => {
      bar.remove();
    };
  }

  function restoreDraft(draft) {
    try {
      selected.clear();
      for (const it of draft.items) {
        const key = String(it.id);
        selected.set(key, it);
      }
      applyFormMeta(draft.meta);
      renderCart();
      // Re-check checkboxes in UI
      draft.items.forEach(it => {
        const idKey = String(it.id);
        const input = root.querySelector(`.sfb-card input[type="checkbox"][data-id="${idKey}"]`);
        if (input) input.checked = true;
      });
    } catch (e) {}
  }

  function app(root){
    const formId = parseInt(root.dataset.formId || SFB_APP?.form_id || 1, 10);
    const tabsEl   = qs('#sfb-tabs', root);
    const panelEl  = qs('#sfb-panel', root);
    const cartEl   = qs('#sfb-cart-list', root);
    const genBtn   = qs('#sfb-generate', root);
    const statusEl = qs('#sfb-generate-status', root);

    // Fetch status and settings
    const fallbackExpiryDays = 45; // matches server default
    let appStatus = { drafts: { autosave_enabled: true, server_enabled: false, expiry_days: fallbackExpiryDays, rate_limit_sec: 20 } };
    wp.apiFetch({ path: '/sfb/v1/status' })
      .then(res => {
        if (res?.ok) appStatus = res;
        // Hide save draft button if server drafts disabled
        const saveDraftBtn = qs('#sfb-save-draft', root);
        if (saveDraftBtn && !appStatus.drafts.server_enabled) {
          saveDraftBtn.style.display = 'none';
        }
      })
      .catch(err => console.error('Failed to load status:', err));

    // Apply branding accent color if available
    if (SFB_APP?.brand?.primary_color){
      root.style.setProperty('--sfb-accent', SFB_APP.brand.primary_color);
    }

    // --- NEW: project info fields --- (guarded so it won't duplicate)
    const cartBox = cartEl.parentElement;
    let infoWrap = root.querySelector('.sfb-info');
    if (!infoWrap) {
      // Get cover default from branding settings
      const coverDefault = SFB_APP?.brand?.cover_default !== undefined ? !!SFB_APP.brand.cover_default : true;

      infoWrap = el('div', {class:'sfb-info'});
      infoWrap.innerHTML = `
        <div class="sfb-info-row"><label>Project</label><input id="sfb-meta-project" type="text"></div>
        <div class="sfb-info-row"><label>Contractor</label><input id="sfb-meta-contractor" type="text"></div>
        <div class="sfb-info-row"><label>Submittal #</label><input id="sfb-meta-submittal" type="text"></div>
        <div class="sfb-info-row">
          <label>Layout Preset</label>
          <select id="sfb-meta-preset" class="sfb-preset">
            <option value="technical">Technical</option>
            <option value="branded">Branded</option>
            <option value="packet" selected>Packet (Cover + Summary + TOC)</option>
          </select>
        </div>
        <div class="sfb-info-row">
          <label>Format</label>
          <select id="sfb-meta-format">
            <option value="pdf" selected>PDF</option>
            <option value="html">HTML</option>
          </select>
        </div>
        <div class="sfb-info-checks">
          <label><input id="sfb-meta-cover" type="checkbox" ${coverDefault ? 'checked' : ''}> Cover sheet</label>
          <label><input id="sfb-meta-leed" type="checkbox"> Include LEED note</label>
        </div>
        <hr class="sfb-sep">
      `;
      cartBox.insertBefore(infoWrap, cartEl);

      // Listen for changes to meta fields to save draft (if autosave enabled)
      const metaFields = infoWrap.querySelectorAll('input, select');
      metaFields.forEach(field => {
        field.addEventListener('change', () => {
          if (appStatus.drafts.autosave_enabled) scheduleDraftSave();
        });
        field.addEventListener('input', () => {
          if (appStatus.drafts.autosave_enabled) scheduleDraftSave();
        });
      });
    }

    let nodes = [];
    let tree  = [];
    const selected = new Map(); // key: model id -> payload

    function renderTabs(){
      tabsEl.innerHTML='';
      tree.forEach((cat, idx)=>{
        const b = el('button', {class:'sfb-tab'+(idx===0?' active':''), dataset:{idx:String(idx)}}, cat.title);
        b.addEventListener('click', ()=> {
          qsa('.sfb-tab', tabsEl).forEach(x=>x.classList.remove('active'));
          b.classList.add('active');
          renderCategory(cat);
        });
        tabsEl.appendChild(b);
      });
      if (tree[0]) renderCategory(tree[0]);
    }

    function renderCategory(cat){
      panelEl.innerHTML = '';

      // Optional global controls for large lists
      const controls = el('div', { class: 'sfb-collapse-controls' },
        el('button', {
          class: 'sfb-btn sfb-collapse-all',
          onClick: () => qsa('.sfb-type-group', panelEl).forEach(g => {
            g.classList.remove('open');
            const content = g.querySelector('.sfb-type-content');
            if (content) content.style.maxHeight = '0';
            const arrow = g.querySelector('.sfb-arrow');
            if (arrow) arrow.classList.remove('rotated');
            const header = g.querySelector('.sfb-collapse-header');
            if (header) {
              header.classList.remove('open');
              header.setAttribute('aria-expanded', 'false');
            }
          })
        }, 'Collapse All'),
        el('button', {
          class: 'sfb-btn sfb-expand-all',
          onClick: () => qsa('.sfb-type-group', panelEl).forEach(g => {
            g.classList.add('open');
            const content = g.querySelector('.sfb-type-content');
            if (content) content.style.maxHeight = content.scrollHeight + 'px';
            const arrow = g.querySelector('.sfb-arrow');
            if (arrow) arrow.classList.add('rotated');
            const header = g.querySelector('.sfb-collapse-header');
            if (header) {
              header.classList.add('open');
              header.setAttribute('aria-expanded', 'true');
            }
          })
        }, 'Expand All')
      );
      panelEl.appendChild(controls);

      cat.children.forEach(prod => {
        panelEl.appendChild(el('h3', { class: 'sfb-h3' }, prod.title));

        prod.children.forEach(type => {
          const key = `type-${type.id}`;
          const persisted = sessionStorage.getItem(key) === 'open';

          const group = el('div', { class: 'sfb-type-group' + (persisted ? ' open' : '') });

          const grid = el('div', { class: 'sfb-model-grid sfb-type-content' });
          (type.children || []).forEach(model => {
            const meta = (model.settings && model.settings.fields) || {};
            const idKey = String(model.id);
            const card = el('label', { class: 'sfb-card', title: tooltip(meta) });
            const chk = el('input', { type: 'checkbox', 'data-id': idKey });
            chk.checked = selected.has(idKey);
            chk.addEventListener('change', e => {
              if (e.target.checked) {
                selected.set(idKey, {
                  id: model.id,
                  title: model.title,
                  meta,
                  path: [cat.title, prod.title, type.title]
                });
              } else {
                selected.delete(idKey);
              }
              renderCart();
              if (appStatus.drafts.autosave_enabled) {
                scheduleDraftSave();
              }
            });
            card.append(chk, el('div', { class: 'sfb-card-title' }, model.title));
            grid.appendChild(card);
          });

          // Use shared collapsible header
          const { header } = createCollapsibleHeader(type.title, (isOpen) => {
            const content = grid;
            const height = content.scrollHeight + 'px';
            if (isOpen) {
              group.classList.add('open');
              content.style.maxHeight = height;
            } else {
              group.classList.remove('open');
              content.style.maxHeight = '0';
            }
            sessionStorage.setItem(key, isOpen ? 'open' : 'closed');
          }, persisted);

          header.classList.add('sfb-type-title');
          group.appendChild(header);
          group.appendChild(grid);
          panelEl.appendChild(group);

          // Set initial height for persisted state
          if (persisted) grid.style.maxHeight = grid.scrollHeight + 'px';
          else grid.style.maxHeight = '0';
        });
      });
    }

    function renderCart(){
      cartEl.innerHTML = '';

      const items = [...selected.values()];
      if (items.length === 0) {
        cartEl.innerHTML = '<em>No products selected.</em>';
        genBtn.disabled = true;
        return;
      }

      // Group items by category (first element in path)
      const grouped = {};
      items.forEach(it => {
        const cat = it.path?.[0] || 'Miscellaneous';
        if (!grouped[cat]) grouped[cat] = [];
        grouped[cat].push(it);
      });

      // Render grouped categories
      Object.entries(grouped).forEach(([cat, groupItems]) => {
        const catHeader = el('div', { class: 'sfb-cart-cat' }, cat);
        cartEl.appendChild(catHeader);

        groupItems.forEach(it => {
          const idKey = String(it.id);
          const itemWrap = el('div', { class: 'sfb-cart-item' });

          // Build meta detail list
          const metaLines = [];
          if (it.meta?.size) metaLines.push(`Size: ${it.meta.size}`);
          if (it.meta?.flange) metaLines.push(`Flange: ${it.meta.flange}`);
          if (it.meta?.thickness) metaLines.push(`Thickness: ${it.meta.thickness}`);
          if (it.meta?.ksi) metaLines.push(`KSI: ${it.meta.ksi}`);

          const metaDetails = el('div', { class: 'sfb-cart-item-meta' }, metaLines.join('\n'));

          // Use shared collapsible header
          const { header } = createCollapsibleHeader(it.title, (isOpen) => {
            const content = metaDetails;
            if (isOpen) {
              itemWrap.classList.add('open');
              content.style.display = 'block';
              const height = content.scrollHeight + 'px';
              content.style.maxHeight = height;
              setTimeout(() => {
                content.style.maxHeight = 'none';
                itemWrap.classList.add('highlight');
                setTimeout(() => itemWrap.classList.remove('highlight'), 400);
              }, 300);
            } else {
              itemWrap.classList.remove('open');
              content.style.maxHeight = content.scrollHeight + 'px';
              requestAnimationFrame(() => {
                content.style.maxHeight = '0';
              });
              setTimeout(() => {
                content.style.display = 'none';
              }, 300);
            }
          }, false);

          // Add trash button to header
          const trashBtn = el('button', {
            class: 'sfb-btn sfb-btn-trash',
            title: 'Remove item',
            onClick: (e) => {
              e.stopPropagation(); // prevent toggle
              selected.delete(idKey);
              const input = root.querySelector(`.sfb-card input[type="checkbox"][data-id="${idKey}"]`);
              if (input) input.checked = false;
              renderCart();
              if (appStatus.drafts.autosave_enabled) {
                scheduleDraftSave();
              }
            }
          }, '🗑️');

          header.appendChild(trashBtn);
          header.classList.add('sfb-cart-item-header');

          itemWrap.append(header, metaDetails);
          cartEl.appendChild(itemWrap);
        });
      });

      genBtn.disabled = false;
    }

    if (!genBtn.dataset.bound) {
      genBtn.dataset.bound = '1';
      genBtn.addEventListener('click', ()=>{
        const items = [...selected.values()];
        const preset = qs('#sfb-meta-preset').value || 'packet';
        const meta = {
          project:    qs('#sfb-meta-project').value.trim(),
          contractor: qs('#sfb-meta-contractor').value.trim(),
          submittal:  qs('#sfb-meta-submittal').value.trim(),
          include_cover: qs('#sfb-meta-cover').checked,
          include_leed:  qs('#sfb-meta-leed').checked,
          layout: preset,
          include_summary: preset === 'packet',
          // Automation flags (can be enhanced with UI toggles later)
          email_to: '',
          send_email: false,
          archive: true,
          track: true,
          white_label: false,
          // Signature block (optional, can be added via UI later)
          approve_block: false,
          approved_by: '',
          approved_title: '',
          approved_date: '',
        };
        const format = qs('#sfb-meta-format').value || 'pdf';
        statusEl.textContent = 'Generating...';
        genBtn.disabled = true;

        wp.apiFetch({
          path: '/sfb/v1/generate',
          method: 'POST',
          data: { form_id: formId, items, meta, format }
        })
        .then(res=>{
          if (res?.ok && res.url){
            const note = res.format === 'pdf' ? '' : ' (HTML fallback)';
            window.open(res.url, '_blank', 'noopener');
            let msg = `Ready${note}. <a href="${res.url}" target="_blank" rel="noopener">Open packet</a>`;
            if (res.tracking_url) {
              msg += ` • <a href="${res.tracking_url}" target="_blank" rel="noopener">Share link</a>`;
            }
            if (res.emailed) {
              msg += ' • Email sent';
            }
            if (res.archived) {
              msg += ' • Archived';
            }
            statusEl.innerHTML = msg + '.';

            // Show clear-after-generate dialog
            setTimeout(() => showClearAfterGenerateDialog(), 500);
          } else {
            statusEl.textContent = 'Failed to generate.';
          }
        })
        .catch(err=>{
          console.error(err);
          statusEl.textContent = err?.message || 'Error generating.';
        })
        .finally(()=> {
          genBtn.disabled = items.length === 0;
        });
      });
    }

    // fetch data
    wp.apiFetch({ path: `/sfb/v1/form/${formId}` })
      .then(res=>{
        if (!res?.ok) throw new Error('Form fetch failed');
        nodes = res.nodes || [];
        tree  = buildTree(nodes).filter(n=>n.node_type==='category');
        renderTabs();
        renderCart();

        // Check for saved draft after rendering
        const draft = loadDraftLocal();
        if (draft && draft.items && draft.items.length) {
          showRestoreBanner(draft);
        }
      })
      .catch(err=>{
        console.error('SFB front fetch error', err);
        tabsEl.innerHTML = '<em>Failed to load form.</em>';
      });

    // Add clear draft button handler
    const clearDraftBtn = qs('#sfb-clear-draft', root);
    if (clearDraftBtn && !clearDraftBtn.dataset.bound) {
      clearDraftBtn.dataset.bound = '1';
      clearDraftBtn.addEventListener('click', () => {
        clearDraftLocal();
        alert('Saved progress cleared on this device.');
      });
    }

    // Add save draft button handler (Pro feature)
    const saveDraftBtn = qs('#sfb-save-draft', root);
    if (saveDraftBtn && !saveDraftBtn.dataset.bound) {
      saveDraftBtn.dataset.bound = '1';
      saveDraftBtn.addEventListener('click', async () => {
        const items = [...selected.values()];
        if (items.length === 0) {
          showToast('Please select at least one product before saving.', 'error');
          return;
        }

        const payload = {
          items,
          meta: getFormMeta(),
          version: 1
        };

        saveDraftBtn.disabled = true;
        saveDraftBtn.textContent = '💾 Saving...';

        try {
          const res = await wp.apiFetch({
            path: '/sfb/v1/drafts',
            method: 'POST',
            data: payload
          });

          if (res.ok) {
            showShareLinkToast(res.url, res.expires_at, appStatus.drafts.expiry_days);
            // Store last server draft ID
            localStorage.setItem('sfb_last_server_draft', JSON.stringify({
              id: res.id,
              ts: Date.now()
            }));
          } else if (res.code === 'pro_required') {
            showUpsellModal();
          } else if (res.code === 'disabled') {
            showToast('Server drafts are currently disabled.', 'error');
          } else {
            showToast('Failed to save: ' + (res.message || 'Unknown error'), 'error');
          }
        } catch (err) {
          console.error('Draft save error:', err);
          showToast('Failed to save draft. Please try again.', 'error');
        } finally {
          saveDraftBtn.disabled = false;
          saveDraftBtn.textContent = '💾 Save progress';
        }
      });
    }

    // Check for draft in URL on page load
    const urlParams = new URLSearchParams(window.location.search);
    const draftId = urlParams.get('sfb_draft');
    if (draftId) {
      loadServerDraft(draftId);
    }
  }

  // ========== SERVER DRAFT HELPERS ==========

  // Modal A11y helpers
  let lastFocusedElement = null;
  let modalFocusTrap = null;

  function openModal(modal) {
    lastFocusedElement = document.activeElement;
    document.body.classList.add('sfb-modal-open');
    document.body.appendChild(modal);

    // Focus first focusable element
    const focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable.length) focusable[0].focus();

    // Setup focus trap
    modalFocusTrap = (e) => {
      if (e.key === 'Tab') {
        const focusableElements = Array.from(focusable);
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (e.shiftKey && document.activeElement === firstElement) {
          e.preventDefault();
          lastElement.focus();
        } else if (!e.shiftKey && document.activeElement === lastElement) {
          e.preventDefault();
          firstElement.focus();
        }
      }

      if (e.key === 'Escape') {
        closeModal(modal);
      }
    };

    modal.addEventListener('keydown', modalFocusTrap);
  }

  function closeModal(modal) {
    modal.remove();
    document.body.classList.remove('sfb-modal-open');
    if (lastFocusedElement) lastFocusedElement.focus();
  }

  async function loadServerDraft(draftId) {
    try {
      const res = await wp.apiFetch({
        path: `/sfb/v1/drafts/${draftId}`,
        method: 'GET'
      });

      if (res.ok) {
        showServerDraftRestoreModal(res, draftId);
      } else if (res.code === 'expired') {
        showExpiredDraftModal('expired');
      } else if (res.code === 'disabled') {
        showToast('Server drafts are currently disabled.', 'error');
      }
    } catch (err) {
      console.error('Failed to load server draft:', err);
      // Check if it's a 404 or 410
      if (err.code === 'rest_no_route' || err.data?.status === 404) {
        showExpiredDraftModal('not_found');
      } else if (err.data?.status === 410) {
        showExpiredDraftModal('expired');
      } else {
        showToast('Failed to load draft. Please try again.', 'error');
      }

      // Clean up URL param
      const url = new URL(window.location);
      url.searchParams.delete('sfb_draft');
      window.history.replaceState({}, '', url);
    }
  }

  function showServerDraftRestoreModal(draft, draftId) {
    const modal = el('div', { class: 'sfb-modal', role: 'dialog', 'aria-modal': 'true', 'aria-labelledby': 'sfb-modal-title' });
    const itemCount = draft.items?.length || 0;
    const created = new Date(draft.created_at);
    const expires = new Date(draft.expires_at);

    modal.innerHTML = `
      <div class="sfb-modal-overlay"></div>
      <div class="sfb-modal-content">
        <h3 id="sfb-modal-title">Restore Draft?</h3>
        <p>Found a saved draft from <strong>${created.toLocaleString()}</strong></p>
        <p><strong>${itemCount}</strong> item${itemCount !== 1 ? 's' : ''} selected</p>
        <p style="font-size:12px;color:#666">Expires: ${expires.toLocaleDateString()}</p>
        <div class="sfb-modal-actions">
          <button id="sfb-restore-server" class="sfb-btn sfb-btn-primary">Restore</button>
          <button id="sfb-cancel-restore" class="sfb-btn">Cancel</button>
        </div>
      </div>
    `;

    openModal(modal);

    qs('#sfb-restore-server', modal).onclick = () => {
      selected.clear();
      draft.items.forEach(it => {
        const key = String(it.id);
        selected.set(key, it);
      });
      applyFormMeta(draft.meta);
      renderCart();

      // Update checkboxes
      draft.items.forEach(it => {
        const idKey = String(it.id);
        const input = document.querySelector(`.sfb-card input[type="checkbox"][data-id="${idKey}"]`);
        if (input) input.checked = true;
      });

      // Mark local draft as superseded
      localStorage.setItem('sfb_draft_superseded', '1');
      clearDraftLocal();

      // Remove URL parameter
      const url = new URL(window.location);
      url.searchParams.delete('sfb_draft');
      window.history.replaceState({}, '', url);

      closeModal(modal);
      showToast('Draft restored successfully!', 'success');
    };

    qs('#sfb-cancel-restore', modal).onclick = () => {
      // Remove URL parameter
      const url = new URL(window.location);
      url.searchParams.delete('sfb_draft');
      window.history.replaceState({}, '', url);
      closeModal(modal);
    };

    // Close on overlay click
    qs('.sfb-modal-overlay', modal).onclick = () => {
      qs('#sfb-cancel-restore', modal).click();
    };

    // Enter key on primary button
    qs('#sfb-restore-server', modal).addEventListener('keypress', (e) => {
      if (e.key === 'Enter') e.target.click();
    });
  }

  function showUpsellModal() {
    const modal = el('div', { class: 'sfb-modal', role: 'dialog', 'aria-modal': 'true', 'aria-labelledby': 'sfb-upsell-title' });
    const upgradeUrl = SFB_APP?.upgrade_url || admin_url('admin.php?page=sfb-upgrade');

    modal.innerHTML = `
      <div class="sfb-modal-overlay"></div>
      <div class="sfb-modal-content">
        <h3 id="sfb-upsell-title">✨ Pro Feature</h3>
        <p><strong>Save & Share Drafts</strong> is available in Pro.</p>
        <ul style="text-align:left;margin:10px 20px">
          <li>Save your selections to the server</li>
          <li>Share with team via short URL</li>
          <li>Drafts auto-expire after 45 days</li>
        </ul>
        <div class="sfb-modal-actions">
          <a href="${upgradeUrl}" class="sfb-btn sfb-btn-primary" target="_blank">Upgrade to Pro</a>
          <button id="sfb-close-upsell" class="sfb-btn">Maybe Later</button>
        </div>
      </div>
    `;

    openModal(modal);

    qs('#sfb-close-upsell', modal).onclick = () => closeModal(modal);
    qs('.sfb-modal-overlay', modal).onclick = () => closeModal(modal);
  }

  function showExpiredDraftModal(reason) {
    const modal = el('div', { class: 'sfb-modal', role: 'dialog', 'aria-modal': 'true', 'aria-labelledby': 'sfb-expired-title' });
    const title = reason === 'expired' ? 'Draft Expired' : 'Draft Not Found';
    const message = reason === 'expired'
      ? 'This draft has expired and is no longer available.'
      : 'This draft could not be found. It may have been deleted or expired.';

    const localDraft = loadDraftLocal();
    const hasLocalDraft = localDraft && localDraft.items && localDraft.items.length > 0;

    modal.innerHTML = `
      <div class="sfb-modal-overlay"></div>
      <div class="sfb-modal-content">
        <h3 id="sfb-expired-title">${title}</h3>
        <p>${message}</p>
        ${hasLocalDraft ? `<p style="margin-top:12px">You have a local draft from ${new Date(localDraft.ts).toLocaleString()} with ${localDraft.items.length} item(s).</p>` : ''}
        <div class="sfb-modal-actions">
          ${hasLocalDraft ? '<button id="sfb-restore-local" class="sfb-btn sfb-btn-primary">Restore Local Draft</button>' : ''}
          <button id="sfb-dismiss-expired" class="sfb-btn">Dismiss</button>
        </div>
      </div>
    `;

    openModal(modal);

    if (hasLocalDraft) {
      qs('#sfb-restore-local', modal).onclick = () => {
        restoreDraft(localDraft);
        closeModal(modal);
        showToast('Local draft restored successfully!', 'success');
      };
    }

    qs('#sfb-dismiss-expired', modal).onclick = () => closeModal(modal);
    qs('.sfb-modal-overlay', modal).onclick = () => closeModal(modal);
  }

  function showShareLinkToast(url, expiresAt, expiryDays = 45) {
    const expires = new Date(expiresAt);
    const toast = el('div', { class: 'sfb-toast sfb-toast--ok' });

    toast.innerHTML = `
      <div class="sfb-toast-content">
        <p><strong>Draft saved!</strong> Share this link:</p>
        <div class="sfb-share-link-wrap">
          <input id="sfb-share-url" type="text" value="${url}" readonly class="sfb-share-input">
          <button id="sfb-copy-link" class="sfb-btn">Copy</button>
        </div>
        <p style="font-size:11px;color:#666;margin-top:4px">Expires in ${expiryDays} days (${expires.toLocaleDateString()})</p>
      </div>
      <button class="sfb-toast-close" aria-label="Close notification">×</button>
    `;

    // Ensure toast container exists
    let container = qs('#sfb-toast-container');
    if (!container) {
      container = el('div', { id: 'sfb-toast-container', 'aria-live': 'polite', 'aria-atomic': 'true' });
      document.body.appendChild(container);
    }
    container.appendChild(toast);

    // Focus and select input
    const input = qs('#sfb-share-url', toast);
    input.focus();
    input.select();

    // Copy button
    qs('#sfb-copy-link', toast).onclick = () => {
      input.select();
      document.execCommand('copy');
      const btn = qs('#sfb-copy-link', toast);
      const orig = btn.textContent;
      btn.textContent = '✓ Copied!';
      setTimeout(() => btn.textContent = orig, 2000);
    };

    // Close button
    qs('.sfb-toast-close', toast).onclick = () => toast.remove();

    // Auto-close after 20 seconds
    setTimeout(() => toast.remove(), 20000);
  }

  function showToast(message, type = 'info') {
    // Ensure toast container exists
    let container = qs('#sfb-toast-container');
    if (!container) {
      container = el('div', { id: 'sfb-toast-container', 'aria-live': 'polite', 'aria-atomic': 'true' });
      document.body.appendChild(container);
    }

    const toast = el('div', { class: `sfb-toast sfb-toast--${type}` });
    toast.innerHTML = `
      <div class="sfb-toast-content">${message}</div>
      <button class="sfb-toast-close" aria-label="Close notification">×</button>
    `;
    container.appendChild(toast);
    qs('.sfb-toast-close', toast).onclick = () => toast.remove();
    setTimeout(() => toast.remove(), 5000);
  }

  function showClearAfterGenerateDialog() {
    const modal = el('div', { class: 'sfb-modal', role: 'dialog', 'aria-modal': 'true', 'aria-labelledby': 'sfb-clear-title' });

    modal.innerHTML = `
      <div class="sfb-modal-overlay"></div>
      <div class="sfb-modal-content">
        <h3 id="sfb-clear-title">Keep your selections saved?</h3>
        <p>Your packet was generated successfully. Would you like to keep your current selections saved for reuse?</p>
        <div class="sfb-modal-actions">
          <button id="sfb-keep-selections" class="sfb-btn sfb-btn-primary">Keep</button>
          <button id="sfb-clear-selections" class="sfb-btn">Clear</button>
        </div>
      </div>
    `;

    openModal(modal);

    qs('#sfb-keep-selections', modal).onclick = () => closeModal(modal);
    qs('#sfb-clear-selections', modal).onclick = () => {
      clearDraftLocal();
      selected.clear();
      renderCart();
      closeModal(modal);
      showToast('Selections cleared successfully.', 'success');
    };
    qs('.sfb-modal-overlay', modal).onclick = () => closeModal(modal);
  }

  document.addEventListener('DOMContentLoaded', ()=>{
    const nodes = document.querySelectorAll('.sfb-frontend');
    nodes.forEach(root=>{
      if (root.dataset.sfbInited === '1') return;   // <- guard
      root.dataset.sfbInited = '1';
      app(root);
    });
  });
})();
