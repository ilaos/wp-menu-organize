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

  function app(root){
    const formId = parseInt(root.dataset.formId || SFB_APP?.form_id || 1, 10);
    const tabsEl   = qs('#sfb-tabs', root);
    const panelEl  = qs('#sfb-panel', root);
    const cartEl   = qs('#sfb-cart-list', root);
    const genBtn   = qs('#sfb-generate', root);
    const statusEl = qs('#sfb-generate-status', root);

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
      panelEl.innerHTML='';
      cat.children.forEach(prod=>{
        panelEl.appendChild(el('h3', {class:'sfb-h3'}, prod.title));
        prod.children.forEach(type=>{
          const group = el('div', {class:'sfb-type-group'},
            el('div', {class:'sfb-type-title'}, type.title)
          );
          const grid = el('div', {class:'sfb-model-grid'});
          (type.children||[]).forEach(model=>{
            const meta = (model.settings && model.settings.fields) || {};
            const key = String(model.id);
            const card = el('label', {class:'sfb-card', title: tooltip(meta)});
            const chk  = el('input', {type:'checkbox', 'data-id': key});
            // set property (not attribute) so false means unchecked
            chk.checked = selected.has(key);
            chk.addEventListener('change', (e)=>{
              if (e.target.checked){
                selected.set(key, { id:model.id, title:model.title, meta, path:[cat.title, prod.title, type.title] });
              } else {
                selected.delete(key);
              }
              renderCart();
            });
            const title = el('div', {class:'sfb-card-title'}, model.title);
            card.append(chk, title);
            grid.appendChild(card);
          });
          group.appendChild(grid);
          panelEl.appendChild(group);
        });
      });
    }

    function renderCart(){
      cartEl.innerHTML='';
      const items = [...selected.values()];
      items.forEach(it=>{
        const idKey = String(it.id);
        const row = el('div', {class:'sfb-cart-row'},
          el('div', {class:'sfb-cart-title'}, it.title),
          el('div', {class:'sfb-cart-meta'}, (it.meta?.size||'')),
          el('button', {class:'sfb-btn sfb-btn-link', onClick: ()=>{
            selected.delete(idKey);
            // uncheck the matching grid checkbox
            const input = root.querySelector(`.sfb-card input[type="checkbox"][data-id="${idKey}"]`);
            if (input) input.checked = false;
            renderCart();
          }}, 'Remove')
        );
        cartEl.appendChild(row);
      });
      genBtn.disabled = items.length === 0;
    }

    if (!genBtn.dataset.bound) {
      genBtn.dataset.bound = '1';
      genBtn.addEventListener('click', ()=>{
        const items = [...selected.values()];
        const meta = {
          project:    qs('#sfb-meta-project').value.trim(),
          contractor: qs('#sfb-meta-contractor').value.trim(),
          submittal:  qs('#sfb-meta-submittal').value.trim(),
          include_cover: qs('#sfb-meta-cover').checked,
          include_leed:  qs('#sfb-meta-leed').checked,
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
            statusEl.innerHTML = `Ready${note}. <a href="${res.url}" target="_blank" rel="noopener">Open packet</a>.`;
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
      })
      .catch(err=>{
        console.error('SFB front fetch error', err);
        tabsEl.innerHTML = '<em>Failed to load form.</em>';
      });
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
