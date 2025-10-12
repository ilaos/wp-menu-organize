(function () {
  const { createElement: h, useState, useEffect, useLayoutEffect, Fragment, useRef, useCallback, useMemo } = wp.element;

  // Set X-WP-Nonce header for all write requests
  if (window.wp && wp.apiFetch) {
    wp.apiFetch.use((options, next) => {
      const method = (options.method || 'GET').toUpperCase();
      if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
        options.headers = Object.assign({}, options.headers, {
          'X-WP-Nonce': (window.SFB && SFB.restNonce) || ''
        });
      }
      return next(options);
    });

    // Optional: friendly 401/403 message
    wp.apiFetch.use(async (options, next) => {
      try {
        return await next(options);
      } catch (err) {
        if (err && err.data && (err.data.status === 401 || err.data.status === 403)) {
          console.error('[SFB] Auth/nonce error:', err);
          alert('Your session expired. Please refresh the page and try again.');
        }
        throw err;
      }
    });
  }

  // Fancy tooltip helpers (global scope)
  let sfbTip;
  function showFancyTip(e, text) {
    if (!text) return hideFancyTip();
    if (!sfbTip) {
      sfbTip = document.createElement('div');
      sfbTip.className = 'sfb-tooltip';
      document.body.appendChild(sfbTip);
    }
    sfbTip.textContent = text;
    sfbTip.style.display = 'block';
    const pad = 10;
    const x = e.pageX + pad;
    const y = e.pageY + pad;
    sfbTip.style.left = x + 'px';
    sfbTip.style.top  = y + 'px';
  }
  function hideFancyTip() {
    if (sfbTip) sfbTip.style.display = 'none';
  }

  function nest(flat){
    const byId={}; flat.forEach(n=>byId[n.id]={...n,children:[]});
    const roots=[]; flat.forEach(n=> (n.parent_id && byId[n.parent_id]) ? byId[n.parent_id].children.push(byId[n.id]) : roots.push(byId[n.id]) );
    return roots;
  }

  function pathFromNode(flat, node){
    const byId={}; flat.forEach(n=>byId[n.id]=n);
    const out=[]; let cur=node;
    while(cur){ out.unshift(cur); cur = cur.parent_id && byId[cur.parent_id]; }
    return out;
  }

  const TYPE_LABEL = { category:'Category', product:'Product', type:'Type', model:'Model' };
  const TYPE_ICON = { category:'📁', product:'🧱', type:'⚙️', model:'🔩' };
  const ALLOWED_CHILDREN = { category:['product'], product:['type'], type:['model'], model:[] };

  // --- Drag + hierarchy config ---
  const TYPE = { category: 'category', product: 'product', type: 'type', model: 'model' };

  // Valid child mapping (Set-based for faster lookups)
  const CHILDREN_OF = {
    category: new Set(['product']),
    product:  new Set(['type']),
    type:     new Set(['model']),
    model:    new Set([]),
  };

  // Legacy map for compatibility
  const ALLOWED_CHILDREN_MAP = {
    [TYPE.category]: TYPE.product,
    [TYPE.product]:  TYPE.type,
    [TYPE.type]:     TYPE.model,
    [TYPE.model]:    null, // no children
  };

  function isAncestor(nodesById, ancestorId, nodeId) {
    // Climb nodeId -> parent chain; return true if ancestorId is found
    let cur = nodesById[nodeId];
    while (cur && cur.parent_id) {
      if (cur.parent_id === ancestorId) return true;
      cur = nodesById[cur.parent_id];
    }
    return false;
  }

  function canDropInside(dragType, targetType) {
    return CHILDREN_OF[targetType]?.has(dragType) ?? false;
  }

  function canDropBeforeAfter(dragType, targetType, sameParent) {
    // Reordering among siblings is always ok; cross-parent before/after is ok
    // (we'll keep parent same if dropping between siblings of the same parent)
    // We still need to ensure that the target's parent accepts dragType if the parent changes.
    return true;
  }

  function canNest(childType, parentType) {
    if (!parentType) return false;
    const allowed = ALLOWED_CHILDREN_MAP[parentType] || null;
    return !!allowed && allowed === childType;
  }

  function isLeaf(type) {
    return ALLOWED_CHILDREN_MAP[type] === null;
  }

  // Small helper to flash invalid drop feedback
  function flashInvalid(el) {
    if (!el) return;
    el.classList.add('sfb-row--shake','sfb-drop-bad');
    setTimeout(() => {
      el.classList.remove('sfb-row--shake','sfb-drop-bad');
    }, 350);
  }

  // ---- Validation helpers ----
  function validateNode(node, allNodes) {
    const errors = [];

    // Check for duplicate title under same parent
    const siblings = allNodes.filter(n =>
      n.id !== node.id &&
      n.parent_id === node.parent_id &&
      n.title?.trim().toLowerCase() === node.title?.trim().toLowerCase()
    );
    if (siblings.length > 0) {
      errors.push('Duplicate title under same parent');
    }

    // Model field validation
    if (node.node_type === 'model' && node.settings?.fields) {
      const fields = node.settings.fields;

      // Thickness: must be "20 mil", "33 mil", or numeric
      if (fields.thickness) {
        const thick = typeof fields.thickness === 'string' ? fields.thickness.trim() : String(fields.thickness);
        const isMil = /^\d+\s*mil/i.test(thick);
        const isNumeric = /^\d+(\.\d+)?$/.test(thick);
        if (!isMil && !isNumeric) {
          errors.push('Thickness must be numeric or "XX mil" format');
        }
      }

      // KSI: must be numeric
      if (fields.ksi !== undefined && fields.ksi !== null && fields.ksi !== '') {
        const ksi = typeof fields.ksi === 'string' ? fields.ksi.trim() : String(fields.ksi);
        if (!/^\d+(\.\d+)?$/.test(ksi)) {
          errors.push('KSI must be numeric');
        }
      }
    }

    return errors;
  }

  function computeCounts(node, allNodes) {
    // Recursively count children by type
    const counts = { type: 0, model: 0 };

    function countDescendants(nodeId) {
      const children = allNodes.filter(n => n.parent_id === nodeId);
      children.forEach(child => {
        if (child.node_type === 'type') counts.type++;
        if (child.node_type === 'model') counts.model++;
        countDescendants(child.id);
      });
    }

    countDescendants(node.id);
    return counts;
  }

  function fieldsTooltip(node) {
    try {
      if (!node || node.node_type !== 'model') return '';
      // Fields are stored in node.settings.fields
      if (!node.settings || typeof node.settings !== 'object') return '';
      const f = node.settings.fields || {};
      if (!f || typeof f !== 'object' || Object.keys(f).length === 0) return '';
      const parts = [];
      if (f.size)      parts.push(`Size: ${f.size}`);
      if (f.flange)    parts.push(`Flange: ${f.flange}`);
      if (f.thickness) parts.push(`Thickness: ${f.thickness}`);
      if (typeof f.ksi !== 'undefined' && f.ksi !== null) parts.push(`KSI: ${f.ksi}`);
      return parts.join('\n');
    } catch (e) {
      console.error('fieldsTooltip error:', e, node);
      return '';
    }
  }

  function mediaPicker(onSelect){
    if (!wp || !wp.media) { alert('Media library not available'); return; }
    const frame = wp.media({ title:'Select Logo', button:{ text:'Use this logo' }, multiple:false });
    frame.on('select', ()=>{
      const atts = frame.state().get('selection').first().toJSON();
      onSelect && onSelect(atts.url);
    });
    frame.open();
  }

  // ---- ID coercion (use everywhere collapse touches ids) ----
  const toID = (v) => Number(v);

  // ---- Collapse persistence helpers (coerced to numbers) ----
  const LS_COLLAPSE_KEY = 'sfb_tree_collapse';
  function loadCollapsedSet() {
    try {
      const raw = localStorage.getItem(LS_COLLAPSE_KEY);
      const arr = raw ? JSON.parse(raw) : [];
      const normalized = Array.isArray(arr) ? arr.map(toID) : [];
      return new Set(normalized);
    } catch (e) {
      console.warn('[SFB] loadCollapsedSet error', e);
      return new Set();
    }
  }
  function saveCollapsedSet(set) {
    try {
      const out = Array.from(set).map(toID);
      localStorage.setItem(LS_COLLAPSE_KEY, JSON.stringify(out));
    } catch (e) {
      console.warn('[SFB] saveCollapsedSet error', e);
    }
  }

  // Search & filter utilities
  function useSearchFilter(){
    const [query, setQuery] = useState(()=> localStorage.getItem('sfb_tree_query') || '');
    const [filter, setFilter] = useState(()=> localStorage.getItem('sfb_tree_filter') || 'all');
    const [debouncedQuery, setDebouncedQuery] = useState(query);

    useEffect(()=> {
      const timer = setTimeout(()=> setDebouncedQuery(query), 150);
      return ()=> clearTimeout(timer);
    }, [query]);

    useEffect(()=> {
      localStorage.setItem('sfb_tree_query', query);
    }, [query]);

    useEffect(()=> {
      localStorage.setItem('sfb_tree_filter', filter);
    }, [filter]);

    return { query, setQuery, filter, setFilter, debouncedQuery };
  }

  function matchesSearch(node, query, filter){
    if (!query && filter === 'all') return true;
    const queryMatch = !query || node.title.toLowerCase().includes(query.toLowerCase());
    const filterMatch = filter === 'all' || node.node_type === filter;
    return queryMatch && filterMatch;
  }

  function hasMatchingDescendant(node, query, filter){
    if (matchesSearch(node, query, filter)) return true;
    return (node.children || []).some(ch => hasMatchingDescendant(ch, query, filter));
  }

  // --- Drop UI helpers ---
  let dropLineEl = null;
  function ensureDropLine() {
    if (!dropLineEl) {
      dropLineEl = document.createElement('div');
      dropLineEl.className = 'sfb-drop-line';
      dropLineEl.style.position = 'fixed';
      dropLineEl.style.height = '2px';
      dropLineEl.style.background = '#4f46e5';
      dropLineEl.style.zIndex = '1000';
      dropLineEl.style.pointerEvents = 'none';
      document.body.appendChild(dropLineEl);
    }
  }

  function showDropUIGlobal(targetEl, intent, valid) {
    targetEl.classList.toggle('sfb-drop-into', intent === 'inside' && valid);
    targetEl.style.cursor = valid ? '' : 'not-allowed';

    if (intent === 'before' || intent === 'after') {
      ensureDropLine();
      const r = targetEl.getBoundingClientRect();
      const y = (intent === 'before') ? r.top : r.bottom;
      dropLineEl.style.display = valid ? 'block' : 'none';
      dropLineEl.style.left = (r.left + 10) + 'px';
      dropLineEl.style.right = (window.innerWidth - r.right) + 'px';
      dropLineEl.style.top = (y - 1) + 'px';
    } else if (dropLineEl) {
      dropLineEl.style.display = 'none';
    }
  }

  function clearDropUIGlobal() {
    document.querySelectorAll('.sfb-tree-row.sfb-drop-into').forEach(el => {
      el.classList.remove('sfb-drop-into');
      el.style.cursor = '';
    });
    if (dropLineEl) {
      dropLineEl.style.display = 'none';
    }
  }

  // --- Drag & Drop handlers ---
  let dragging = null; // { id, type, originParentId }

  function onDragStart(e, node) {
    // limit drag to handle: already enforced by attaching only to handle
    dragging = {
      id: String(node.id),
      type: node.node_type,
      originParentId: String(node.parent_id ?? ''),
    };
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', dragging.id);
    document.body.classList.add('sfb-dragging');
  }

  function onDragEnd() {
    dragging = null;
    document.body.classList.remove('sfb-dragging');
    cleanupDropClasses();
  }

  function cleanupDropClasses() {
    document.querySelectorAll('.sfb-drop-ok,.sfb-drop-bad').forEach(el => {
      el.classList.remove('sfb-drop-ok','sfb-drop-bad');
    });
  }

  /**
   * Compute if dropping "into" target node is allowed (becoming its child).
   * Also allow reordering among siblings (same parent) regardless of type.
   */
  function evaluateDrop(targetNode) {
    if (!dragging) return { ok: false, reason: 'no-drag' };

    const dragId = dragging.id;
    const dragType = dragging.type;
    const dragOriginParent = dragging.originParentId;

    const targetId = String(targetNode.id);
    const targetType = targetNode.node_type;
    const targetParentId = String(targetNode.parent_id ?? '');

    // Prevent dropping on self or descendant (basic guard; full ancestor check not shown)
    if (dragId === targetId) return { ok: false, reason: 'self' };

    // If we drop "between/around" a target (your existing logic may compute position),
    // reordering under the SAME parent is always ok
    if (dragOriginParent === targetParentId) {
      return { ok: true, mode: 'reorder', parentId: targetParentId };
    }

    // If we intend to drop INTO target as a child, target must allow the child type
    if (canNest(dragType, targetType)) {
      return { ok: true, mode: 'nest', parentId: targetId };
    }

    return { ok: false, reason: 'hierarchy' };
  }

  function onDragEnter(e, targetNode) {
    if (!dragging) return;
    e.preventDefault();
    const el = e.currentTarget;
    const check = evaluateDrop(targetNode);
    el.classList.toggle('sfb-drop-ok', check.ok);
    el.classList.toggle('sfb-drop-bad', !check.ok);
  }

  function onDragOver(e, targetNode) {
    if (!dragging) return;
    const check = evaluateDrop(targetNode);
    // Only allow drop if valid
    if (check.ok) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
    } else {
      e.dataTransfer.dropEffect = 'none';
    }
  }

  function onDragLeave(e) {
    const el = e.currentTarget;
    el.classList.remove('sfb-drop-ok','sfb-drop-bad');
  }

  async function onDrop(e, targetNode, loadFormCallback) {
    if (!dragging) return;
    e.preventDefault();
    e.stopPropagation();

    const el = e.currentTarget;
    const check = evaluateDrop(targetNode);

    cleanupDropClasses();

    if (!check.ok) {
      flashInvalid(el);
      return;
    }

    // Compute final parent & position using your existing logic
    // If your current reorder uses a "position" (float), keep it; otherwise just pass sibling index.
    // Below we keep it simple: send move to computed parent with "position" = 0.5 to place relative.
    try {
      await wp.apiFetch({
        path: '/sfb/v1/node/move',
        method: 'POST',
        data: {
          id: dragging.id,
          parent_id: check.parentId,   // either target parent or target id (nest case)
          position: 0.5,               // your server handler calculates final order; adjust if needed
        },
      });
      // reload/refresh tree
      if (loadFormCallback) await loadFormCallback();
    } catch (err) {
      console.error('Move error:', err);
      flashInvalid(el);
    } finally {
      onDragEnd();
    }
  }

  function Row({ node, onSelect, selectedId, collapsed, onToggle, onAddChild, onRename, onDuplicate, onDelete, onDragDrop, searchQuery, searchFilter, bulkSelected, onBulkToggle, allNodes, loadForm, drag, setDrag, dragDisabled, flat, nodesById, lastClickedId, setLastClickedId }){
    const ref = useRef(null);
    const [isEditing, setIsEditing] = useState(false);
    const [editValue, setEditValue] = useState(node.title);
    const [showMenu, setShowMenu] = useState(false);
    const [dropZone, setDropZone] = useState(null); // 'before' | 'after' | 'inside' | null
    const [creatingChild, setCreatingChild] = useState(null); // nodeType to create
    const [saveState, setSaveState] = useState(null); // 'saving' | 'success' | null
    const inputRef = useRef(null);

    const isSel = selectedId === node.id;
    const hasChildren = (node.children||[]).length > 0;
    const isCollapsed = collapsed.has(node.id);
    const allowedChild = ALLOWED_CHILDREN[node.node_type];
    const isBulkSelected = bulkSelected && bulkSelected.has(node.id);

    // Checkbox handler for multi-select
    const onRowCheckbox = (e) => {
      e.stopPropagation();
      if (e.shiftKey && lastClickedId != null) {
        // Compute range using 'flat' array order
        const ids = flat.map(n => n.id);
        const a = ids.indexOf(lastClickedId);
        const b = ids.indexOf(node.id);
        if (a !== -1 && b !== -1) {
          const [start, end] = a < b ? [a, b] : [b, a];
          const range = ids.slice(start, end + 1);
          const next = new Set(bulkSelected);
          range.forEach(id => next.add(id));
          onBulkToggle && onBulkToggle(null, null, next);
        }
      } else if (e.metaKey || e.ctrlKey) {
        const next = new Set(bulkSelected);
        if (next.has(node.id)) next.delete(node.id);
        else next.add(node.id);
        onBulkToggle && onBulkToggle(null, null, next);
        setLastClickedId && setLastClickedId(node.id);
      } else {
        // Plain click -> toggle only this row
        const next = new Set(bulkSelected);
        if (next.has(node.id)) next.delete(node.id);
        else next.add(node.id);
        onBulkToggle && onBulkToggle(null, null, next);
        setLastClickedId && setLastClickedId(node.id);
      }
    };

    // Validation and counts
    const validationErrors = allNodes ? validateNode(node, allNodes) : [];
    const hasErrors = validationErrors.length > 0;
    const counts = (node.node_type === 'category' || node.node_type === 'product') && allNodes
      ? computeCounts(node, allNodes)
      : null;

    // Drag handle visibility - show for all nodes to allow reordering
    // Set to !isLeaf(node.node_type) if you only want non-leaf nodes to be draggable
    const showHandle = !dragDisabled;

    // Search/filter logic
    const isMatch = matchesSearch(node, searchQuery, searchFilter);
    const hasMatchChild = hasChildren && (node.children||[]).some(ch => hasMatchingDescendant(ch, searchQuery, searchFilter));
    const shouldShow = isMatch || hasMatchChild;
    const isDimmed = !isMatch && hasMatchChild;


    useEffect(()=> {
      if (window.__SFB_SCROLL_TO === node.id && ref.current){
        ref.current.scrollIntoView({block:'nearest'});
        delete window.__SFB_SCROLL_TO;
      }
    }, [node.id]);

    useEffect(()=> {
      if (isEditing && inputRef.current) {
        inputRef.current.focus();
        inputRef.current.select();
      }
    }, [isEditing]);

    useEffect(()=> {
      if (showMenu) {
        const handleClick = () => setShowMenu(false);
        document.addEventListener('click', handleClick);
        return () => document.removeEventListener('click', handleClick);
      }
    }, [showMenu]);

    useEffect(()=> {
      const handleRename = (e) => {
        if (e.detail === node.id) startEdit();
      };
      window.addEventListener('sfb-rename', handleRename);
      return () => window.removeEventListener('sfb-rename', handleRename);
    }, [node.id]);

    const startEdit = (e) => {
      if (e) e.stopPropagation();
      setEditValue(node.title);
      setIsEditing(true);
    };

    const saveEdit = async () => {
      const trimmed = editValue.trim();
      if (!trimmed || trimmed === node.title) {
        setIsEditing(false);
        return;
      }

      try {
        setSaveState('saving');
        if (ref.current) ref.current.classList.add('sfb-saving');

        // Use the onRename callback if provided, or call API directly
        if (onRename) {
          await new Promise((resolve, reject) => {
            onRename(node, trimmed, (err) => {
              if (err) reject(err);
              else resolve();
            });
          });
        }

        setSaveState('success');
        setTimeout(() => {
          setSaveState(null);
          setIsEditing(false);
        }, 800);
      } catch (err) {
        console.error('Inline rename error', err);
        setSaveState(null);
        setEditValue(node.title); // Revert to original
        setIsEditing(false);
        if (ref.current) {
          ref.current.classList.add('sfb-drop-error');
          setTimeout(() => ref.current.classList.remove('sfb-drop-error'), 450);
        }
      } finally {
        if (ref.current) ref.current.classList.remove('sfb-saving');
      }
    };

    const cancelEdit = () => {
      setEditValue(node.title);
      setIsEditing(false);
    };

    const handleKeyDown = (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        saveEdit();
      } else if (e.key === 'Escape') {
        e.preventDefault();
        cancelEdit();
      }
    };

    if (!shouldShow) return null;

    const tooltip = fieldsTooltip(node);

    return h(Fragment, null,
      h('div', {
        ref,
        className:'sfb-tree-row'+(isSel?' selected':'')+(isDimmed?' sfb-dim':'')+(dropZone?' sfb-drop-'+dropZone:'')+(isEditing?' editing':'')+(isBulkSelected?' bulk-selected':''),
        'data-id': String(node.id),
        'data-parent-id': String(node.parent_id ?? ''),
        'data-node-type': node.node_type,
        'data-collapsed': isCollapsed ? '1' : '0',
        title: tooltip || undefined,
        draggable: !dragDisabled,
        onMouseMove: (e) => { if (node.node_type === 'model') showFancyTip(e, tooltip); },
        onMouseLeave: () => hideFancyTip(),
        onDragStart: (e) => {
          if (dragDisabled) return e.preventDefault();
          setDrag({ id: node.id, type: node.node_type, sourceParentId: node.parent_id });
          e.dataTransfer.effectAllowed = 'move';
          e.dataTransfer.setData('text/plain', String(node.id));
          e.currentTarget.classList.add('dragging');
        },
        onDragEnd: (e) => {
          e.currentTarget.classList.remove('dragging');
          clearDropUIGlobal();
          setDrag(null);
        },
        onDragOver: (e) => {
          if (!drag || dragDisabled) return;
          e.preventDefault();
          const rect = e.currentTarget.getBoundingClientRect();
          const y = e.clientY - rect.top;
          const third = rect.height / 3;

          let intent = 'inside';
          if (y < third) intent = 'before';
          else if (y > rect.height - third) intent = 'after';

          const validInside = (intent === 'inside' && canDropInside(drag.type, node.node_type) && !isAncestor(nodesById, drag.id, node.id) && node.node_type !== 'model');
          const validAround = ((intent === 'before' || intent === 'after') && canDropBeforeAfter(drag.type, node.node_type, drag.sourceParentId === node.parent_id) && !isAncestor(nodesById, drag.id, node.id) && drag.id !== node.id);
          const valid = validInside || validAround;

          showDropUIGlobal(e.currentTarget, intent, valid);
          e.dataTransfer.dropEffect = valid ? 'move' : 'none';
        },
        onDragLeave: () => {
          clearDropUIGlobal();
        },
        onDrop: async (e) => {
          if (!drag || dragDisabled) return;
          e.preventDefault();
          const container = e.currentTarget;
          const rect = container.getBoundingClientRect();
          const y = e.clientY - rect.top;
          const third = rect.height / 3;

          let intent = 'inside';
          if (y < third) intent = 'before';
          else if (y > rect.height - third) intent = 'after';

          const validInside = (intent === 'inside' && canDropInside(drag.type, node.node_type) && !isAncestor(nodesById, drag.id, node.id) && node.node_type !== 'model');
          const validAround = ((intent === 'before' || intent === 'after') && canDropBeforeAfter(drag.type, node.node_type, drag.sourceParentId === node.parent_id) && !isAncestor(nodesById, drag.id, node.id) && drag.id !== node.id);
          const valid = validInside || validAround;

          if (!valid) {
            container.classList.add('sfb-drop-error');
            setTimeout(() => container.classList.remove('sfb-drop-error'), 450);
            clearDropUIGlobal();
            return;
          }

          let parent_id = node.parent_id;
          let position;

          if (intent === 'inside') {
            parent_id = node.id;
            const kids = flat.filter(n => n.parent_id === parent_id).sort((a,b)=>a.position - b.position);
            position = kids.length ? kids[kids.length-1].position + 1 : 0;
          } else {
            parent_id = node.parent_id;
            const siblings = flat.filter(n => n.parent_id === parent_id).sort((a,b)=>a.position - b.position);
            const idx = siblings.findIndex(n => n.id === node.id);
            let left = idx > 0 ? siblings[idx-1].position : null;
            let right = siblings[idx] ? siblings[idx].position : 0;
            if (intent === 'after') {
              left = siblings[idx]?.position ?? right;
              right = siblings[idx+1]?.position ?? (left + 2);
            }
            if (left === null) {
              position = right - 0.5;
            } else if (right == null) {
              position = left + 1;
            } else {
              position = (left + right) / 2;
            }
          }

          try {
            await wp.apiFetch({
              path: '/sfb/v1/node/move',
              method: 'POST',
              data: { id: drag.id, parent_id, position, form_id: 1 }
            });
            await loadForm();
          } catch (err) {
            console.error('Drag move error', err);
            container.classList.add('sfb-drop-error');
            setTimeout(() => container.classList.remove('sfb-drop-error'), 450);
          } finally {
            clearDropUIGlobal();
            setDrag(null);
          }
        },
        onClick:(e)=>{ e.stopPropagation(); if(!isEditing) onSelect(node); }
      },
        // Bulk selection checkbox
        onBulkToggle && h('input', {
          type: 'checkbox',
          className: 'sfb-row-check',
          checked: isBulkSelected,
          onClick: onRowCheckbox
        }),
        // Drag handle (handle-only dragging)
        showHandle && h('span', {
          className: 'sfb-drag-handle',
          title: 'Drag to reorder',
          draggable: false,
        }, '⠿'),
        // Expand/collapse arrow
        h('div', {
          className: 'sfb-tree-arrow' + (hasChildren ? (isCollapsed?' collapsed':' expanded') : ' leaf'),
          onClick: (e)=>{ e.stopPropagation(); if(hasChildren) onToggle(node.id); }
        }),
        // Debug badge - shows "C" when collapsed
        h('span', {className:'sfb-debug-badge'}, isCollapsed ? 'C' : ''),
        // Type icon
        h('div', {className:'sfb-node-icon'}, TYPE_ICON[node.node_type]),
        // Label with title (editable) and badge
        h('div', {className:'sfb-node-label'},
          isEditing
            ? h('div', {className:'sfb-title-inline'},
                h('input', {
                  ref: inputRef,
                  type:'text',
                  className: 'sfb-inline-input',
                  value: editValue,
                  onChange: (e)=> setEditValue(e.target.value),
                  onKeyDown: handleKeyDown,
                  onBlur: saveEdit,
                  onClick: (e)=> e.stopPropagation(),
                  disabled: saveState === 'saving',
                  title: 'Press Enter to save, Escape to cancel'
                }),
                saveState === 'saving' && h('span', {style:{marginLeft:'6px',color:'#6b7280',fontSize:'12px'}}, '⏳'),
                saveState === 'success' && h('span', {style:{marginLeft:'6px',color:'#10b981',fontSize:'12px'}}, '✓')
              )
            : h('span', {
                className:'sfb-node-title',
                style:{fontWeight: node.node_type!=='model'?'600':'400'},
                onDoubleClick: (e)=> { e.stopPropagation(); startEdit(); }
              }, node.title),
          h('span', {className:'sfb-badge '+node.node_type}, TYPE_LABEL[node.node_type]),
          // Count badge for categories and products
          counts && (counts.type > 0 || counts.model > 0) && h('span', {className:'sfb-count-badge'},
            node.node_type === 'category'
              ? `${counts.type} Types / ${counts.model} Models`
              : `${counts.model} Models`
          ),
          // Error indicator
          hasErrors && h('span', {className:'sfb-error-dot', title: validationErrors.join(', ')}, '●')
        ),
        // Kebab menu
        !isEditing && h('div', {className:'sfb-row-actions', style:{position:'relative'}},
          h('div', {
            className:'sfb-kebab',
            onClick:(e)=>{ e.stopPropagation(); setShowMenu(!showMenu); }
          }, '⋯'),
          showMenu && h('div', {className:'sfb-kebab-menu'},
            allowedChild?.length > 0 && h('div', {
              className:'sfb-kebab-menu-item',
              onClick:(e)=>{
                e.stopPropagation();
                setShowMenu(false);
                setCreatingChild(allowedChild[0]);
                // Ensure parent is expanded
                if (isCollapsed) onToggle(node.id);
              }
            }, '➕ Add '+TYPE_LABEL[allowedChild[0]]),
            h('div', {
              className:'sfb-kebab-menu-item',
              onClick:(e)=>{ e.stopPropagation(); setShowMenu(false); onDuplicate(node); }
            }, '📋 Duplicate'),
            h('div', {
              className:'sfb-kebab-menu-item',
              onClick:(e)=>{ e.stopPropagation(); setShowMenu(false); startEdit(); }
            }, '✏️ Rename'),
            h('div', {
              className:'sfb-kebab-menu-item',
              onClick:(e)=>{ e.stopPropagation(); setShowMenu(false); onDelete(node.id); }
            }, '🗑️ Delete')
          )
        )
      ),
      // CHILDREN WRAPPER (always present; effect will hide it)
      (hasChildren || creatingChild) && h('div', {
        className:'sfb-tree-children',
        'data-parent': node.id,
        style: { display: isCollapsed ? 'none' : 'block' }
      },
        (node.children||[]).map(ch=> h(Row,{
          key:ch.id, node:ch, onSelect, selectedId, collapsed, onToggle, onAddChild, onRename, onDuplicate, onDelete, onDragDrop,
          searchQuery, searchFilter, bulkSelected, onBulkToggle, allNodes, loadForm, drag, setDrag, dragDisabled, flat, nodesById,
          lastClickedId, setLastClickedId
        })),
        creatingChild && h(InlineCreateRow, {
          key: 'creating',
          parentNode: node,
          nodeType: creatingChild,
          onSave: (title) => {
            onAddChild(node, creatingChild, title);
            setCreatingChild(null);
          },
          onCancel: () => setCreatingChild(null),
          allNodes
        })
      )
    );
  }

  // Temporary row for inline creation
  function InlineCreateRow({ parentNode, nodeType, onSave, onCancel, allNodes }){
    const [title, setTitle] = useState('');
    const [saving, setSaving] = useState(false);
    const inputRef = useRef(null);

    useEffect(()=> {
      if (inputRef.current) {
        inputRef.current.focus();
      }
    }, []);

    // Check for duplicate title
    const hasDuplicate = allNodes && title.trim() && allNodes.some(n =>
      n.parent_id === parentNode.id &&
      n.title?.trim().toLowerCase() === title.trim().toLowerCase()
    );

    const handleSave = () => {
      const trimmed = title.trim();
      if (!trimmed) {
        onCancel();
        return;
      }
      // Block save if duplicate
      if (hasDuplicate) {
        return;
      }
      setSaving(true);
      onSave(trimmed);
    };

    const handleKeyDown = (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        handleSave();
      } else if (e.key === 'Escape') {
        e.preventDefault();
        onCancel();
      }
    };

    return h('div', {
      className:'sfb-tree-row sfb-creating',
      style:{ marginLeft: '20px', opacity: saving ? 0.6 : 1 }
    },
      h('div', {className:'sfb-tree-arrow leaf'}),
      h('span', {className:'sfb-debug-badge'}),
      h('div', {className:'sfb-node-icon'}, TYPE_ICON[nodeType]),
      h('div', {className:'sfb-node-label'},
        h('div', {className:'sfb-title-inline'},
          h('input', {
            ref: inputRef,
            type:'text',
            value: title,
            onChange: (e)=> setTitle(e.target.value),
            onKeyDown: handleKeyDown,
            onBlur: handleSave,
            onClick: (e)=> e.stopPropagation(),
            disabled: saving,
            placeholder: 'Enter ' + TYPE_LABEL[nodeType] + ' name...',
            style: hasDuplicate ? { borderColor: '#ef4444' } : {}
          }),
          saving && h('span', {style:{marginLeft:'6px'}}, '⏳'),
          hasDuplicate && h('span', {className:'sfb-inline-error'}, 'Duplicate name')
        ),
        h('span', {className:'sfb-badge '+nodeType}, TYPE_LABEL[nodeType])
      )
    );
  }

  function RightPane({ selected, onSave, onCreate, onDelete, onReorder, breadcrumbs, onSelectNode, allNodes }){
    return h('div', {className:'sfb-right'},
      h('div', {className:'sfb-right-inner sfb-card sfb-inspector-sticky'},
        h(Inspector, { node: selected, onSave, onCreate, onDelete, onReorder, breadcrumbs, onSelectNode, allNodes })
      )
    );
  }

  function BrandingBox(){
    const [loading, setLoading] = useState(true);
    const [s, setS] = useState({
      logo_url:'', company_name:'', company_address:'', company_phone:'', company_website:'',
      primary_color:'#111827', cover_default:true, footer_text:'Generated by Submittal Builder'
    });

    function load(){
      setLoading(true);
      wp.apiFetch({ path:'/sfb/v1/settings' })
        .then(res=> { if(res?.ok){ setS(res.settings); } })
        .finally(()=> setLoading(false));
    }
    function save(){
      wp.apiFetch({ path:'/sfb/v1/settings', method:'POST', data:{ settings:s } })
        .then(res=>{ if(res?.ok){ alert('Branding saved'); } else { alert('Save failed'); } })
        .catch(err=> alert(err?.message||'Save error'));
    }

    useEffect(()=>{ load(); }, []);

    if (loading) return h('div', null, 'Loading branding...');

    return h('div', {className:'sfb-settings'},
      h('h3', null, 'Branding'),
      h('div', {className:'sfb-grid'},
        h('label', null, 'Logo'),
        h('div', {className:'sfb-logo-wrap'},
          h('div', null,
            h('input', {type:'text', value:s.logo_url, onChange:e=>setS({...s, logo_url:e.target.value}), style:{width:'100%'}}),
            h('button', {className:'button', onClick:()=>mediaPicker(url=>setS({...s, logo_url:url}))}, 'Choose...')
          ),
          s.logo_url ? h('img',{src:s.logo_url}) : null
        ),

        h('label', null, 'Company name'),
        h('input', {type:'text', value:s.company_name, onChange:e=>setS({...s, company_name:e.target.value}), style:{width:'100%'}}),

        h('label', null, 'Address'),
        h('textarea', {value:s.company_address, onChange:e=>setS({...s, company_address:e.target.value}), style:{width:'100%', minHeight:'70px'}}),

        h('label', null, 'Phone'),
        h('input', {type:'text', value:s.company_phone, onChange:e=>setS({...s, company_phone:e.target.value}), style:{width:'100%'}}),

        h('label', null, 'Website'),
        h('input', {type:'text', value:s.company_website, onChange:e=>setS({...s, company_website:e.target.value}), style:{width:'100%'}}),

        h('label', null, 'Primary color'),
        h('input', {type:'color', value:s.primary_color, onChange:e=>setS({...s, primary_color:e.target.value})}),

        h('label', null, 'Cover sheet default'),
        h('label', null,
          h('input', {type:'checkbox', checked:s.cover_default, onChange:e=>setS({...s, cover_default:e.target.checked})}),
          ' Enable cover sheet by default'
        ),

        h('label', null, 'Footer text'),
        h('input', {type:'text', value:s.footer_text, onChange:e=>setS({...s, footer_text:e.target.value}), style:{width:'100%'}})
      ),
      h('div', {className:'sfb-actions'},
        h('button', {className:'button button-primary', onClick:save}, 'Save Branding')
      )
    );
  }

  function Inspector({ node, onSave, onCreate, onDelete, onReorder, breadcrumbs, onSelectNode, allNodes }){
    if (!node) return h('p', {style:{padding:'20px',textAlign:'center',color:'#6b7280'}}, 'Select a node to view details');

    const [activeTab, setActiveTab] = useState('details');
    const [saveStatus, setSaveStatus] = useState(null); // 'saving' | 'saved' | null
    const [history, setHistory] = useState([]);
    const [loadingHistory, setLoadingHistory] = useState(false);

    const isModel = node.node_type === 'model';
    const fields = (node.settings && node.settings.fields) || {};

    const [title, setTitle] = useState(node.title);
    const [slug, setSlug] = useState(node.slug || '');
    const [note, setNote] = useState((node.settings && node.settings.note) || '');
    const [size, setSize] = useState(fields.size || '');
    const [flange, setFlange] = useState(fields.flange || '');
    const [thickness, setThickness] = useState(fields.thickness || '');
    const [ksi, setKsi] = useState(fields.ksi || '');

    // Compute validation errors for the current node state
    const currentNodeState = {
      ...node,
      title,
      settings: {
        ...(node.settings || {}),
        note,
        ...(isModel ? { fields: { size, flange, thickness, ksi } } : {})
      }
    };
    const validationErrors = allNodes ? validateNode(currentNodeState, allNodes) : [];

    const titleInputRef = useRef(null);

    // Reset state when node changes
    useEffect(()=> {
      setTitle(node.title);
      setSlug(node.slug || '');
      setNote((node.settings && node.settings.note) || '');
      const f = (node.settings && node.settings.fields) || {};
      setSize(f.size || '');
      setFlange(f.flange || '');
      setThickness(f.thickness || '');
      setKsi(f.ksi || '');
      setSaveStatus(null);
    }, [node.id]);

    // Auto-focus title for new nodes
    useEffect(()=> {
      if (titleInputRef.current && (node.title.startsWith('New ') || node.title === 'Category' || node.title === 'Product' || node.title === 'Type' || node.title === 'Model' || node.title === 'Untitled')) {
        titleInputRef.current.focus();
        titleInputRef.current.select();
      }
    }, [node.id]);

    // Load history when History tab is active
    useEffect(()=> {
      if (activeTab === 'history') {
        setLoadingHistory(true);
        wp.apiFetch({ path: `/sfb/v1/node/history?id=${node.id}` })
          .then(res => {
            if (res?.ok) {
              setHistory(res.history || []);
            }
          })
          .catch(err => console.error('History load error:', err))
          .finally(() => setLoadingHistory(false));
      }
    }, [activeTab, node.id]);

    function autoSave(){
      // Don't save if there are validation errors
      if (validationErrors.length > 0) {
        setSaveStatus(null);
        return;
      }

      setSaveStatus('saving');
      const payload = {
        id: node.id,
        form_id: node.form_id || 1,
        parent_id: node.parent_id || 0,
        node_type: node.node_type,
        title,
        slug,
        position: node.position || 0,
        settings: {
          ...(node.settings || {}),
          note,
          ...(isModel ? { fields: { size, flange, thickness, ksi } } : {})
        }
      };

      onSave(payload, () => {
        setSaveStatus('saved');
        setTimeout(() => setSaveStatus(null), 2000);
      });
    }

    const allowedChildren = ALLOWED_CHILDREN[node.node_type] || [];

    return h(Fragment, null,
      // Breadcrumbs
      breadcrumbs && breadcrumbs.length > 0 && h('div', {className:'sfb-breadcrumbs'},
        breadcrumbs.map((seg,i)=>
          h('span',{key:i, title: fieldsTooltip(seg) || undefined},
            i < breadcrumbs.length - 1
              ? h('a', { onClick: () => onSelectNode && onSelectNode(seg) }, seg.title)
              : seg.title
          )
        )
      ),

      // Save status indicator
      saveStatus && h('div', {className:'sfb-save-status'},
        saveStatus === 'saving' && h('span', {style:{color:'#6b7280'}}, '⏳ Saving...'),
        saveStatus === 'saved' && h('span', {style:{color:'#10b981'}}, '✓ Saved')
      ),

      // Tabs
      h('div', {className:'sfb-inspector-tabs'},
        h('button', {
          className: 'sfb-inspector-tab' + (activeTab === 'details' ? ' active' : ''),
          onClick: () => setActiveTab('details')
        }, 'Details'),
        isModel && h('button', {
          className: 'sfb-inspector-tab' + (activeTab === 'fields' ? ' active' : ''),
          onClick: () => setActiveTab('fields')
        }, 'Fields'),
        h('button', {
          className: 'sfb-inspector-tab' + (activeTab === 'advanced' ? ' active' : ''),
          onClick: () => setActiveTab('advanced')
        }, 'Advanced'),
        h('button', {
          className: 'sfb-inspector-tab' + (activeTab === 'history' ? ' active' : ''),
          onClick: () => setActiveTab('history')
        }, 'History')
      ),

      // Tab content
      h('div', {className:'sfb-inspector-content'},
        // Validation errors (show in all tabs)
        validationErrors.length > 0 && h('div', {className:'sfb-validation-errors'},
          h('div', {className:'sfb-validation-header'}, '⚠️ Validation Errors'),
          validationErrors.map((err, i) =>
            h('div', {key: i, className:'sfb-validation-error'}, err)
          )
        ),

        // Details Tab
        activeTab === 'details' && h(Fragment, null,
          h('div', {className:'sfb-field'},
            h('label', null, 'Title'),
            h('input', {
              ref: titleInputRef,
              type: 'text',
              value: title,
              onChange: e => setTitle(e.target.value),
              onBlur: autoSave
            })
          ),
          h('div', {className:'sfb-field'},
            h('label', null, 'Node Type'),
            h('div', {className:'sfb-field-readonly'}, TYPE_LABEL[node.node_type])
          ),
          h('div', {className:'sfb-field'},
            h('label', null, 'Actions'),
            h('div', {style:{display:'flex',gap:'6px',flexWrap:'wrap'}},
              h('button', {className:'button', onClick: () => onReorder(node,'up')}, '↑ Up'),
              h('button', {className:'button', onClick: () => onReorder(node,'down')}, '↓ Down'),
              h('button', {className:'button button-link-delete', onClick: () => onDelete(node.id)}, '🗑️ Delete')
            )
          ),
          allowedChildren.length > 0 && h('div', {className:'sfb-field'},
            h('label', null, 'Add Child'),
            h('div', {style:{display:'flex',gap:'6px',flexWrap:'wrap'}},
              allowedChildren.map(t =>
                h('button', {
                  key: t,
                  className: 'button',
                  onClick: () => onCreate({
                    form_id: node.form_id || 1,
                    parent_id: node.id,
                    node_type: t,
                    title: TYPE_LABEL[t]
                  })
                }, '+ ' + TYPE_LABEL[t])
              )
            )
          )
        ),

        // Fields Tab (Model only)
        activeTab === 'fields' && isModel && h(Fragment, null,
          h('div', {className:'sfb-field'},
            h('label', null, 'Size'),
            h('input', {type:'text', value:size, onChange:e=>setSize(e.target.value), onBlur:autoSave})
          ),
          h('div', {className:'sfb-field'},
            h('label', null, 'Flange'),
            h('input', {type:'text', value:flange, onChange:e=>setFlange(e.target.value), onBlur:autoSave})
          ),
          h('div', {className:'sfb-field'},
            h('label', null, 'Thickness'),
            h('input', {type:'text', value:thickness, onChange:e=>setThickness(e.target.value), onBlur:autoSave})
          ),
          h('div', {className:'sfb-field'},
            h('label', null, 'KSI'),
            h('input', {type:'text', value:ksi, onChange:e=>setKsi(e.target.value), onBlur:autoSave})
          )
        ),

        // Advanced Tab
        activeTab === 'advanced' && h(Fragment, null,
          h('div', {className:'sfb-field'},
            h('label', null, 'Slug'),
            h('input', {
              type: 'text',
              value: slug,
              onChange: e => setSlug(e.target.value),
              onBlur: autoSave,
              placeholder: 'Auto-generated from title'
            })
          ),
          h('div', {className:'sfb-field'},
            h('label', null, 'Position'),
            h('div', {className:'sfb-field-readonly'}, node.position || 0)
          ),
          h('div', {className:'sfb-field'},
            h('label', null, 'Internal Note'),
            h('textarea', {
              value: note,
              onChange: e => setNote(e.target.value),
              onBlur: autoSave,
              placeholder: 'Add internal notes...',
              rows: 4
            })
          )
        ),

        // History Tab
        activeTab === 'history' && h(Fragment, null,
          loadingHistory
            ? h('div', {style:{textAlign:'center',padding:'20px',color:'#6b7280'}}, 'Loading history...')
            : history.length === 0
              ? h('div', {style:{textAlign:'center',padding:'20px',color:'#6b7280'}}, 'No history available')
              : h('div', {className:'sfb-history-list'},
                  history.map((entry, idx) =>
                    h('div', {key:idx, className:'sfb-history-item'},
                      h('div', {className:'sfb-history-action'}, entry.action),
                      h('div', {className:'sfb-history-meta'},
                        entry.user && h('span', null, entry.user),
                        entry.user && entry.timestamp && h('span', null, ' • '),
                        entry.timestamp && h('span', null, new Date(entry.timestamp).toLocaleString())
                      )
                    )
                  )
                )
        )
      )
    );
  }

  function SplitButton({ selected, onAddCategory, onAddChild }){
    const [open, setOpen] = useState(false);

    useEffect(()=> {
      if (open) {
        const handleClick = () => setOpen(false);
        document.addEventListener('click', handleClick);
        return () => document.removeEventListener('click', handleClick);
      }
    }, [open]);

    const allowedChild = selected ? ALLOWED_CHILDREN[selected.node_type] : [];
    const canAddChild = selected && allowedChild && allowedChild.length > 0;

    return h('div',{className:'sfb-split'+(open?' open':'')},
      h('button',{
        className:'button sfb-split-main',
        onClick:onAddCategory
      },'+ Add Category'),
      h('button',{
        className:'button sfb-split-caret',
        onClick:(e)=>{ e.stopPropagation(); setOpen(!open); }
      },'▼'),
      h('div',{className:'dropdown'},
        canAddChild && allowedChild.map(type =>
          h('div',{
            key:type,
            className:'item',
            onClick:()=> { onAddChild(selected, type); setOpen(false); }
          }, `Add ${TYPE_LABEL[type]}`)
        ),
        !canAddChild && h('div',{className:'item disabled'},'Select a node first')
      )
    );
  }

  // Command Palette
  function CommandPalette({ isOpen, onClose, nodes, onSelectNode, onAction, selected }){
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedIndex, setSelectedIndex] = useState(0);
    const inputRef = useRef(null);
    const resultsRef = useRef(null);

    // Reset when opening
    useEffect(() => {
      if (isOpen) {
        setSearchTerm('');
        setSelectedIndex(0);
        setTimeout(() => inputRef.current?.focus(), 50);
      }
    }, [isOpen]);

    // Fuzzy search function
    const fuzzyMatch = (text, search) => {
      if (!search) return true;
      const searchLower = search.toLowerCase();
      const textLower = text.toLowerCase();

      // Simple contains check first
      if (textLower.includes(searchLower)) return true;

      // Fuzzy match: all chars must appear in order
      let searchIdx = 0;
      for (let i = 0; i < textLower.length && searchIdx < searchLower.length; i++) {
        if (textLower[i] === searchLower[searchIdx]) searchIdx++;
      }
      return searchIdx === searchLower.length;
    };

    // Build quick actions
    const quickActions = [];
    if (!searchTerm || fuzzyMatch('Add Category', searchTerm)) {
      quickActions.push({
        type: 'action',
        id: 'add-category',
        label: '➕ Add Category',
        action: () => onAction('add-category')
      });
    }
    if (!searchTerm || fuzzyMatch('Load Sample Catalog', searchTerm) || fuzzyMatch('seed', searchTerm)) {
      quickActions.push({
        type: 'action',
        id: 'load-catalog',
        label: '📦 Load Sample Catalog',
        description: 'Populate a realistic sample tree and branding for quick previews',
        action: () => onAction('load-catalog')
      });
    }
    if (selected && (!searchTerm || fuzzyMatch('Duplicate', searchTerm))) {
      quickActions.push({
        type: 'action',
        id: 'duplicate',
        label: '📋 Duplicate Selected Node',
        action: () => onAction('duplicate', selected)
      });
    }
    if (selected && (!searchTerm || fuzzyMatch('Export', searchTerm))) {
      quickActions.push({
        type: 'action',
        id: 'export',
        label: '💾 Export JSON for Selected',
        action: () => onAction('export', selected)
      });
    }

    // Search nodes (fuzzy match title + model fields)
    const searchResults = searchTerm ? nodes.filter(node => {
      // Match title
      if (fuzzyMatch(node.title, searchTerm)) return true;

      // Match model fields
      if (node.node_type === 'model' && node.settings?.fields) {
        const fields = node.settings.fields;
        if (fields.size && fuzzyMatch(fields.size, searchTerm)) return true;
        if (fields.flange && fuzzyMatch(fields.flange, searchTerm)) return true;
        if (fields.thickness && fuzzyMatch(fields.thickness, searchTerm)) return true;
        if (fields.ksi && fuzzyMatch(fields.ksi, searchTerm)) return true;
      }

      return false;
    }).slice(0, 10) : [];

    const allResults = [...quickActions, ...searchResults.map(node => ({ type: 'node', ...node }))];

    // Keyboard navigation
    const handleKeyDown = (e) => {
      if (e.key === 'Escape') {
        onClose();
      } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        setSelectedIndex(prev => Math.min(prev + 1, allResults.length - 1));
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        setSelectedIndex(prev => Math.max(prev - 1, 0));
      } else if (e.key === 'Enter') {
        e.preventDefault();
        if (allResults[selectedIndex]) {
          executeSelection(allResults[selectedIndex]);
        }
      }
    };

    const executeSelection = (item) => {
      if (item.type === 'action') {
        item.action();
      } else {
        onSelectNode(item);
      }
      onClose();
    };

    // Scroll selected item into view
    useEffect(() => {
      if (resultsRef.current) {
        const selectedEl = resultsRef.current.children[selectedIndex];
        if (selectedEl) {
          selectedEl.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
      }
    }, [selectedIndex]);

    if (!isOpen) return null;

    return h('div', {
      className: 'sfb-palette-overlay',
      onClick: onClose
    },
      h('div', {
        className: 'sfb-palette',
        onClick: (e) => e.stopPropagation()
      },
        h('input', {
          ref: inputRef,
          className: 'sfb-palette-input',
          type: 'text',
          placeholder: 'Search nodes, fields, or type a command...',
          value: searchTerm,
          onChange: (e) => {
            setSearchTerm(e.target.value);
            setSelectedIndex(0);
          },
          onKeyDown: handleKeyDown
        }),
        h('div', {
          ref: resultsRef,
          className: 'sfb-palette-results'
        },
          allResults.length === 0 && h('div', { className: 'sfb-palette-empty' },
            searchTerm ? 'No results found' : 'Type to search...'
          ),
          allResults.map((item, idx) =>
            h('div', {
              key: item.id || item.label,
              className: 'sfb-palette-item' + (idx === selectedIndex ? ' selected' : ''),
              onClick: () => executeSelection(item),
              onMouseEnter: () => setSelectedIndex(idx)
            },
              item.type === 'action'
                ? h('div', { className: 'sfb-palette-action' },
                    h('div', null, item.label),
                    item.description && h('div', { className: 'sfb-palette-action-desc' }, item.description)
                  )
                : h(Fragment, null,
                    h('div', { className: 'sfb-palette-node-title' },
                      TYPE_ICON[item.node_type] + ' ' + item.title
                    ),
                    item.node_type === 'model' && item.settings?.fields && h('div', { className: 'sfb-palette-node-meta' },
                      [
                        item.settings.fields.size && `Size: ${item.settings.fields.size}`,
                        item.settings.fields.flange && `Flange: ${item.settings.fields.flange}`,
                        item.settings.fields.thickness && `Thickness: ${item.settings.fields.thickness}`,
                        item.settings.fields.ksi && `KSI: ${item.settings.fields.ksi}`
                      ].filter(Boolean).join(' • ')
                    )
                  )
            )
          )
        ),
        h('div', { className: 'sfb-palette-footer' },
          '↑↓ Navigate • Enter Select • Esc Close'
        )
      )
    );
  }

  // Bulk Actions Bar
  function BulkActionsBar({ selectedIds, nodes, onDelete, onDuplicate, onMove, onExport, onClear }){
    const [showMovePopover, setShowMovePopover] = useState(false);
    const count = selectedIds.size;

    if (count === 0) return null;

    // Get valid parent targets based on selected node types
    const selectedNodes = Array.from(selectedIds).map(id => nodes.find(n => n.id === id)).filter(Boolean);
    const selectedTypes = [...new Set(selectedNodes.map(n => n.node_type))];

    // Find valid parents that can accept all selected types
    const validParents = nodes.filter(node => {
      const allowedChildren = ALLOWED_CHILDREN[node.node_type];
      if (!allowedChildren) return false;
      return selectedTypes.every(type => allowedChildren.includes(type));
    });

    return h('div', {className:'sfb-bulk-bar'},
      h('div', {className:'sfb-bulk-info'},
        h('span', {className:'sfb-bulk-count'}, count + ' selected'),
        h('button', {
          className:'button-link',
          onClick: onClear,
          style:{marginLeft:'8px',fontSize:'12px'}
        }, 'Clear')
      ),
      h('div', {className:'sfb-bulk-actions'},
        h('button', {
          className:'button',
          onClick: () => {
            if (confirm(`Delete ${count} item(s) and their children?`)) {
              onDelete(Array.from(selectedIds));
            }
          }
        }, '🗑️ Delete'),
        h('button', {
          className:'button',
          onClick: () => onDuplicate(Array.from(selectedIds))
        }, '📋 Duplicate'),
        h('div', {
          className:'sfb-bulk-move-wrap',
          style:{position:'relative',display:'inline-block'}
        },
          h('button', {
            className:'button',
            onClick: (e) => {
              e.stopPropagation();
              setShowMovePopover(!showMovePopover);
            }
          }, '📁 Move to...'),
          showMovePopover && h('div', {
            className:'sfb-move-popover',
            onClick: (e) => e.stopPropagation()
          },
            validParents.length > 0
              ? validParents.map(parent =>
                  h('div', {
                    key: parent.id,
                    className:'sfb-move-popover-item',
                    onClick: () => {
                      onMove(Array.from(selectedIds), parent.id);
                      setShowMovePopover(false);
                    }
                  },
                    TYPE_ICON[parent.node_type] + ' ' + parent.title
                  )
                )
              : h('div', {className:'sfb-move-popover-item disabled'}, 'No valid parents')
          )
        ),
        h('button', {
          className:'button',
          onClick: () => onExport(Array.from(selectedIds))
        }, '💾 Export JSON')
      )
    );
  }

  function App(){
    const [loading, setLoading] = useState(true);
    const [flat, setFlat] = useState([]);
    const [tree, setTree] = useState([]);
    const [selected, setSelected] = useState(null);
    const nextSelectIdRef = useRef(null);
    const [collapsed, setCollapsed] = useState(loadCollapsedSet);
    const [bulkSelected, setBulkSelected] = useState(new Set()); // Set of node IDs
    const lastSelectedRef = useRef(null); // for Shift-click range selection
    const { query, setQuery, filter, setFilter, debouncedQuery } = useSearchFilter();

    // Drag & Drop state
    const [drag, setDrag] = useState(null);
    // shape: { id, type, sourceParentId } while dragging, else null
    const dragDisabled = !!debouncedQuery || filter !== 'all'; // disable drag when filtering

    // Build nodesById map for ancestry checks
    const nodesById = useMemo(() => {
      const map = {};
      flat.forEach(n => { map[n.id] = n; });
      return map;
    }, [flat]);

    // Bulk selection helpers
    const isSelected = useCallback((id) => bulkSelected.has(id), [bulkSelected]);
    const setSelectedFromArray = useCallback((arr) => {
      setBulkSelected(new Set(arr.map(Number)));
    }, []);
    const clearSelection = useCallback(() => {
      setBulkSelected(new Set());
      lastSelectedRef.current = null;
    }, []);

    // Command palette state
    const [paletteOpen, setPaletteOpen] = useState(false);

    // Bulk move modal state
    const [moveModal, setMoveModal] = useState({ open: false, targetId: null, options: [] });

    // Sample catalog modal state
    const [catalogModal, setCatalogModal] = useState({
      open: false,
      mode: 'replace',
      size: 'medium',
      withBranding: true,
      industryPack: (window.SFB && SFB.defaultIndustryPack) || 'electrical'
    });

    // Save as Pack modal state (Agency feature)
    const [packModal, setPackModal] = useState({
      open: false,
      name: '',
      includeBranding: true,
      includeNotes: false,
      saving: false
    });

    // Wipe/Delete All modal state
    const [showWipeModal, setShowWipeModal] = useState(false);
    const [wipeBranding, setWipeBranding] = useState(false);
    const [wipeBackup, setWipeBackup] = useState(true);
    const [wipeConfirmText, setWipeConfirmText] = useState('');
    const [wiping, setWiping] = useState(false);

    // Undo/Redo stacks
    const [undoStack, setUndoStack] = useState([]);
    const [redoStack, setRedoStack] = useState([]);
    const [toastMessage, setToastMessage] = useState(null);
    const MAX_STACK_SIZE = 50;

    // Ensure the DOM always reflects collapse state, even if another render path exists
    useLayoutEffect(() => {
      try {
        // Normalize the set once
        const ids = new Set(Array.from(collapsed, n => Number(n)));

        // 1) Mark rows with data-collapsed appropriately
        document.querySelectorAll('.sfb-tree-row').forEach(row => {
          const id = Number(row.getAttribute('data-id'));
          const isCollapsed = ids.has(id);
          row.setAttribute('data-collapsed', isCollapsed ? '1' : '0');
        });

        // 2) Hide/Show children containers
        document.querySelectorAll('.sfb-tree-children').forEach(group => {
          const parent = Number(group.getAttribute('data-parent'));
          const shouldHide = ids.has(parent);
          group.style.display = shouldHide ? 'none' : 'block';
        });

        // Optional: visible proof while we debug
        document.querySelectorAll('.sfb-tree-row').forEach(row => {
          const badge = row.querySelector('.sfb-debug-badge');
          if (!badge) return;
          badge.textContent = row.getAttribute('data-collapsed') === '1' ? 'C' : '';
        });
      } catch (e) {
        console.warn('[SFB] collapse DOM sync error', e);
      }
    }, [collapsed]);

    // Normalize node ids up front so rest of code sees numbers
    const normalizedFlat = useMemo(
      () => flat.map(n => ({ ...n, id: toID(n.id), parent_id: toID(n.parent_id ?? 0) })),
      [flat]
    );

    // Toggle a single node with logging
    const toggleCollapse = (nodeId) => {
      nodeId = toID(nodeId);
      setCollapsed(prev => {
        const next = new Set(prev);
        if (next.has(nodeId)) next.delete(nodeId); else next.add(nodeId);
        saveCollapsedSet(next);
        console.log('[SFB] toggleCollapse', { nodeId, collapsedBefore: Array.from(prev), collapsedAfter: Array.from(next) });
        return next;
      });
    };

    // Get expandable node IDs (nodes with children)
    const getExpandableIds = useCallback(() => {
      const byParent = new Map();
      for (const n of normalizedFlat) {
        if (!byParent.has(n.parent_id)) byParent.set(n.parent_id, []);
        byParent.get(n.parent_id).push(n);
      }
      const ids = [];
      for (const n of normalizedFlat) {
        const kids = byParent.get(n.id) || [];
        if (kids.length > 0) ids.push(n.id);
      }
      return ids;
    }, [normalizedFlat]);

    // Collapse/Expand all with logging
    const collapseAll = () => {
      const ids = getExpandableIds();
      const next = new Set(ids.map(toID));
      saveCollapsedSet(next);
      setCollapsed(next);
      console.log('[SFB] collapseAll =>', Array.from(next));
    };

    const expandAll = () => {
      saveCollapsedSet(new Set());
      setCollapsed(new Set());
      console.log('[SFB] expandAll => []');
    };

    // Collapse by type
    const collapseByType = (nodeType) => {
      const next = new Set(collapsed);
      const byParent = new Map();
      for (const n of normalizedFlat) {
        if (!byParent.has(n.parent_id)) byParent.set(n.parent_id, []);
        byParent.get(n.parent_id).push(n);
      }
      for (const n of normalizedFlat) {
        const kids = byParent.get(n.id) || [];
        if (n.node_type === nodeType && kids.length > 0) {
          next.add(toID(n.id));
        }
      }
      saveCollapsedSet(next);
      setCollapsed(next);
      console.log('[SFB] collapseByType', nodeType, '=>', Array.from(next));
    };

    // Drag & Drop reordering
    const handleDragDrop = (sourceId, targetId, zone, targetNode) => {
      sourceId = toID(sourceId);
      targetId = toID(targetId);

      console.log('[SFB] drag-drop', { sourceId, targetId, zone });

      let newParentId = targetNode.parent_id || 0;
      let newPosition = targetNode.position || 0;

      if (zone === 'inside') {
        // Move inside target (change parent)
        newParentId = targetId;
        // Find last position of children
        const targetChildren = normalizedFlat.filter(n => n.parent_id === targetId);
        newPosition = targetChildren.length > 0
          ? Math.max(...targetChildren.map(n => n.position || 0)) + 1
          : 1;
      } else if (zone === 'before') {
        // Insert before target (same parent, position slightly before)
        newPosition = (targetNode.position || 0) - 0.5;
      } else if (zone === 'after') {
        // Insert after target (same parent, position slightly after)
        newPosition = (targetNode.position || 0) + 0.5;
      }

      // Call REST endpoint
      wp.apiFetch({
        path: '/sfb/v1/node/move',
        method: 'POST',
        data: { id: sourceId, parent_id: newParentId, position: newPosition }
      })
      .then(res => {
        if (res?.ok) {
          load();
        } else {
          alert('Move failed');
        }
      })
      .catch(err => {
        console.error('[SFB] drag-drop error', err);
        alert('Move error: ' + (err?.message || err));
      });
    };

    // Expose for debugging
    useEffect(() => {
      window.__SFB = window.__SFB || {};
      window.__SFB.getCollapsed = () => Array.from(collapsed);
      window.__SFB.collapseAll = collapseAll;
      window.__SFB.expandAll = expandAll;
      window.__SFB.toggle = toggleCollapse;
    }, [collapsed]);

    function selectById(nodes, id){
      const found = nodes.find(n=>n.id===id);
      if (found) setSelected(found);
    }

    function load(){
      setLoading(true);
      wp.apiFetch({ path:'/sfb/v1/form/1' })
        .then(res=>{
          if (res?.ok) {
            setFlat(res.nodes);
            setTree(nest(res.nodes));
            if (nextSelectIdRef.current){
              selectById(res.nodes, nextSelectIdRef.current);
              window.__SFB_SCROLL_TO = nextSelectIdRef.current;
              nextSelectIdRef.current = null;
            } else if (selected) {
              selectById(res.nodes, selected.id);
            }
          } else {
            console.warn('SFB: form not ok', res);
          }
        })
        .catch(err=>console.error('SFB: fetch error', err))
        .finally(()=>setLoading(false));
    }

    function openCatalogModal(){
      setCatalogModal({
        open: true,
        mode: 'replace',
        size: 'medium',
        withBranding: true,
        industryPack: (window.SFB && SFB.defaultIndustryPack) || 'electrical'
      });
    }

    async function loadSampleCatalog(){
      try {
        setLoading(true);
        const res = await wp.apiFetch({
          path: '/sfb/v1/form/seed',
          method: 'POST',
          data: {
            form_id: 1,
            mode: catalogModal.mode,
            size: catalogModal.size,
            with_branding: catalogModal.withBranding,
            industry_pack: catalogModal.industryPack
          }
        });
        setCatalogModal({
          open: false,
          mode: 'replace',
          size: 'medium',
          withBranding: true,
          industryPack: catalogModal.industryPack // Preserve last selected pack
        });
        await load();
        if (res?.counts) {
          const total = res.counts.categories + res.counts.products + res.counts.types + res.counts.models;
          const msg = `Loaded ${total} items (${res.counts.categories} cats, ${res.counts.products} prods, ${res.counts.types} types, ${res.counts.models} models) in ${res.elapsed_ms}ms`;
          showToast(msg);
        }
      } catch (err) {
        console.error('Load sample catalog error:', err);
        alert('Failed to load sample catalog: ' + (err?.message || err));
      } finally {
        setLoading(false);
      }
    }

    // Agency feature: Save current catalog as Pack
    async function saveAsPack(){
      if (!packModal.name.trim()) {
        alert('Please enter a Pack name.');
        return;
      }

      try {
        setPackModal(prev => ({...prev, saving: true}));

        const res = await wp.apiFetch({
          path: '/sfb/v1/pack/save',
          method: 'POST',
          data: {
            name: packModal.name.trim(),
            include_branding: packModal.includeBranding,
            include_notes: packModal.includeNotes
          }
        });

        // Close modal and reset
        setPackModal({
          open: false,
          name: '',
          includeBranding: true,
          includeNotes: false,
          saving: false
        });

        if (res?.pack) {
          const productCount = res.pack.counts?.products || 0;
          showToast(`Pack "${res.pack.name}" created with ${productCount} products!`);
        }
      } catch (err) {
        console.error('Save Pack error:', err);
        alert('Failed to save Pack: ' + (err?.message || err));
        setPackModal(prev => ({...prev, saving: false}));
      }
    }

    function openWipeModal(){
      hideFancyTip();
      setShowWipeModal(true);
      setWipeBranding(false);
      setWipeBackup(true);
      setWipeConfirmText('');
    }

    async function runWipeAll(){
      if (wipeConfirmText !== 'DELETE') {
        alert('Type DELETE to confirm.');
        return;
      }
      try {
        setWiping(true);
        const res = await wp.apiFetch({
          path: '/sfb/v1/form/wipe',
          method: 'POST',
          data: { form_id: 1, with_branding: wipeBranding, make_backup: wipeBackup }
        });
        setShowWipeModal(false);
        await load();
        if (res.backup_url && wipeBackup) {
          const ok = confirm('All data cleared. A backup was created. Click OK to open it in a new tab.');
          if (ok) window.open(res.backup_url, '_blank');
        } else {
          alert('All data cleared.');
        }
      } catch (err) {
        console.error('Wipe error (apiFetch):', err);
        // Try to fetch raw response if available (some builds expose err.response)
        if (err && err.response && err.response.text) {
          try {
            const rawText = await err.response.text();
            console.error('Raw response body:', rawText);
          } catch (textErr) {
            console.error('Could not read response text:', textErr);
          }
        }
        alert('Failed to delete all: ' + (err?.message || err));
      } finally {
        setWiping(false);
      }
    }

    function save(payload, onSuccess){
      wp.apiFetch({ path:'/sfb/v1/node/save', method:'POST', data: payload })
        .then(res=> {
          if (res?.ok) {
            load();
            if (onSuccess) onSuccess();
          } else {
            alert('Save failed');
          }
        })
        .catch(err=>{ console.error(err); alert('Save error: '+(err?.message||err)); });
    }

    function createNode(payload, skipUndo = false){
      wp.apiFetch({ path:'/sfb/v1/node/create', method:'POST', data: payload })
        .then(res=> {
          if (res?.ok && res.node?.id){
            const newId = res.node.id;
            nextSelectIdRef.current = newId;
            load();

            // Push undo: create → delete
            if (!skipUndo) {
              pushUndo({
                execute: () => deleteNode(newId, true),
                redo: () => createNode(payload, true)
              });
              showToast('Node created');
            }
          } else {
            alert('Create failed');
          }
        })
        .catch(err=>{ console.error(err); alert('Create error: '+(err?.message||err)); });
    }

    function addCategory(){
      createNode({ form_id:1, parent_id:0, node_type:'category', title:'New Category' });
    }

    function addChildInline(parentNode, childType, title){
      createNode({
        form_id: parentNode.form_id || 1,
        parent_id: parentNode.id,
        node_type: childType,
        title: title || TYPE_LABEL[childType]
      });
    }

    function deleteNode(id, skipUndo = false){
      // Capture node snapshot before deletion
      const nodeToDelete = normalizedFlat.find(n => n.id === id);
      if (!nodeToDelete) return;

      if (!skipUndo && !confirm('Delete this node and all its children?')) return;

      // Capture entire subtree for undo
      const captureSubtree = (nodeId) => {
        const node = normalizedFlat.find(n => n.id === nodeId);
        if (!node) return null;
        const children = normalizedFlat.filter(n => n.parent_id === nodeId);
        return {
          ...node,
          children: children.map(ch => captureSubtree(ch.id)).filter(Boolean)
        };
      };

      const snapshot = captureSubtree(id);

      wp.apiFetch({ path:'/sfb/v1/node/delete', method:'POST', data:{ id } })
        .then(res=> {
          if (res?.ok) {
            setSelected(null);
            load();

            // Push undo: delete → restore subtree
            if (!skipUndo && snapshot) {
              pushUndo({
                execute: () => {
                  // Restore the node (simplified - would need full restore logic)
                  createNode({
                    form_id: snapshot.form_id,
                    parent_id: snapshot.parent_id,
                    node_type: snapshot.node_type,
                    title: snapshot.title,
                    position: snapshot.position,
                    settings: snapshot.settings
                  }, true);
                },
                redo: () => deleteNode(id, true)
              });
              showToast('Node deleted');
            }
          } else {
            alert('Delete failed');
          }
        })
        .catch(err=>{ console.error(err); alert('Delete error: '+(err?.message||err)); });
    }

    function reorder(node, direction, skipUndo = false){
      const oldPosition = node.position;
      const oldParentId = node.parent_id;

      wp.apiFetch({ path:'/sfb/v1/node/reorder', method:'POST', data:{ id: node.id, direction } })
        .then(res=> {
          if (res?.ok) {
            load();

            // Push undo: reorder → reverse direction
            if (!skipUndo) {
              const reverseDir = direction === 'up' ? 'down' : 'up';
              pushUndo({
                execute: () => {
                  const currentNode = normalizedFlat.find(n => n.id === node.id);
                  if (currentNode) reorder(currentNode, reverseDir, true);
                },
                redo: () => reorder(node, direction, true)
              });
              showToast(`Moved ${direction}`);
            }
          } else {
            alert('Move failed');
          }
        })
        .catch(err=>{ console.error(err); alert('Move error: '+(err?.message||err)); });
    }

    function renameNode(node, newTitle, onSuccess, skipUndo = false){
      const oldTitle = node.title;

      const payload = {
        id: node.id,
        form_id: node.form_id || 1,
        parent_id: node.parent_id || 0,
        node_type: node.node_type,
        title: newTitle,
        position: node.position || 0,
        settings: node.settings || {}
      };
      wp.apiFetch({ path:'/sfb/v1/node/save', method:'POST', data: payload })
        .then(res=> {
          if (res?.ok) {
            load();
            if (onSuccess) onSuccess();

            // Push undo: rename → old title
            if (!skipUndo) {
              pushUndo({
                execute: () => {
                  const currentNode = normalizedFlat.find(n => n.id === node.id);
                  if (currentNode) renameNode(currentNode, oldTitle, null, true);
                },
                redo: () => {
                  const currentNode = normalizedFlat.find(n => n.id === node.id);
                  if (currentNode) renameNode(currentNode, newTitle, null, true);
                }
              });
              showToast('Node renamed');
            }
          } else {
            alert('Rename failed');
          }
        })
        .catch(err=>{ console.error(err); alert('Rename error: '+(err?.message||err)); });
    }

    function duplicateNode(node){
      wp.apiFetch({ path:'/sfb/v1/node/duplicate', method:'POST', data:{ id: node.id } })
        .then(res=> {
          if (res?.ok && res.new_id){
            nextSelectIdRef.current = res.new_id;
            window.__SFB_SCROLL_TO = res.new_id;
            load();
          } else {
            alert('Duplicate failed');
          }
        })
        .catch(err=>{ console.error(err); alert('Duplicate error: '+(err?.message||err)); });
    }

    // Bulk selection handlers
    const handleBulkToggle = (nodeId, event, directSet) => {
      // If directSet is provided, use it directly (from Row component)
      if (directSet !== undefined) {
        setBulkSelected(directSet);
        return;
      }

      nodeId = toID(nodeId);
      const node = normalizedFlat.find(n => n.id === nodeId);
      if (!node) return;

      if (event.shiftKey && lastSelectedRef.current) {
        // Range selection: find all siblings between last and current
        const lastNode = normalizedFlat.find(n => n.id === lastSelectedRef.current);
        if (lastNode && lastNode.parent_id === node.parent_id) {
          const siblings = normalizedFlat
            .filter(n => n.parent_id === node.parent_id)
            .sort((a, b) => (a.position || 0) - (b.position || 0));

          const startIdx = siblings.findIndex(n => n.id === lastNode.id);
          const endIdx = siblings.findIndex(n => n.id === nodeId);
          const [min, max] = startIdx < endIdx ? [startIdx, endIdx] : [endIdx, startIdx];

          const rangeIds = siblings.slice(min, max + 1).map(n => n.id);
          setBulkSelected(prev => {
            const next = new Set(prev);
            rangeIds.forEach(id => next.add(id));
            return next;
          });
        }
      } else if (event.metaKey || event.ctrlKey) {
        // Toggle selection
        setBulkSelected(prev => {
          const next = new Set(prev);
          if (next.has(nodeId)) {
            next.delete(nodeId);
          } else {
            next.add(nodeId);
          }
          return next;
        });
        lastSelectedRef.current = nodeId;
      } else {
        // Single selection
        setBulkSelected(new Set([nodeId]));
        lastSelectedRef.current = nodeId;
      }
    };

    const clearBulkSelection = () => {
      setBulkSelected(new Set());
      lastSelectedRef.current = null;
    };

    function exportJSON(){
      const formId = 1; // TODO: make dynamic if multiple forms
      wp.apiFetch({ path:`/sfb/v1/form/${formId}/export` })
        .then(res=> {
          if (res?.ok){
            const blob = new Blob([JSON.stringify(res, null, 2)], {type:'application/json'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `submittal-form-${formId}.json`;
            a.click();
            URL.revokeObjectURL(url);
          } else {
            alert('Export failed');
          }
        })
        .catch(err=>{ console.error(err); alert('Export error: '+(err?.message||err)); });
    }

    function importJSON(){
      const input = document.createElement('input');
      input.type = 'file';
      input.accept = 'application/json';
      input.onchange = (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
          try {
            const data = JSON.parse(ev.target.result);
            const mode = confirm('Replace existing data? (Cancel = Append)') ? 'replace' : 'append';
            wp.apiFetch({
              path:'/sfb/v1/form/import',
              method:'POST',
              data:{ form_id:1, mode, nodes: data.nodes || [] }
            })
            .then(res=> {
              if (res?.ok){
                alert(`Imported ${res.imported} nodes`);
                setSelected(null);
                load();
              } else {
                alert('Import failed');
              }
            })
            .catch(err=>{ console.error(err); alert('Import error: '+(err?.message||err)); });
          } catch(err) {
            alert('Invalid JSON file');
          }
        };
        reader.readAsText(file);
      };
      input.click();
    }

    useEffect(()=>{ load(); }, []);

    // Undo/Redo functions
    const pushUndo = useCallback((operation) => {
      setUndoStack(prev => {
        const next = [...prev, operation];
        return next.slice(-MAX_STACK_SIZE);
      });
      setRedoStack([]); // Clear redo stack on new action
    }, [MAX_STACK_SIZE]);

    const executeUndo = useCallback(() => {
      if (undoStack.length === 0) return;

      const operation = undoStack[undoStack.length - 1];
      setUndoStack(prev => prev.slice(0, -1));
      setRedoStack(prev => [...prev, operation].slice(-MAX_STACK_SIZE));

      // Execute the inverse operation
      operation.execute();
      showToast('Action undone');
    }, [undoStack, MAX_STACK_SIZE]);

    const executeRedo = useCallback(() => {
      if (redoStack.length === 0) return;

      const operation = redoStack[redoStack.length - 1];
      setRedoStack(prev => prev.slice(0, -1));
      setUndoStack(prev => [...prev, operation].slice(-MAX_STACK_SIZE));

      // Execute the redo (which is the inverse of the undo)
      operation.redo();
      showToast('Action redone');
    }, [redoStack, MAX_STACK_SIZE]);

    const showToast = useCallback((message) => {
      setToastMessage(message);
      setTimeout(() => setToastMessage(null), 3000);
    }, []);

    // Command palette handlers
    const handlePaletteAction = useCallback((actionId) => {
      setPaletteOpen(false);
      switch(actionId) {
        case 'add-category':
          addCategory();
          break;
        case 'load-catalog':
          openCatalogModal();
          break;
        case 'duplicate':
          if (selected) duplicateNode(selected);
          break;
        case 'export':
          if (bulkSelected.size > 0) {
            handleBulkExport(Array.from(bulkSelected));
          } else {
            exportJSON();
          }
          break;
      }
    }, [selected, bulkSelected]);

    const handlePaletteSelectNode = useCallback((node) => {
      setPaletteOpen(false);

      // Expand all parent nodes
      const parentsToExpand = [];
      let current = node;
      while (current && current.parent_id) {
        const parent = normalizedFlat.find(n => n.id === current.parent_id);
        if (parent) {
          parentsToExpand.push(toID(parent.id));
          current = parent;
        } else {
          break;
        }
      }

      // Remove parents from collapsed set
      if (parentsToExpand.length > 0) {
        setCollapsed(prev => {
          const next = new Set(prev);
          parentsToExpand.forEach(id => next.delete(id));
          saveCollapsedSet(next);
          return next;
        });
      }

      // Select and scroll to node
      setSelected(node);
      window.__SFB_SCROLL_TO = node.id;
    }, [normalizedFlat]);

    // Keyboard shortcuts
    useEffect(() => {
      const handleKeyDown = (e) => {
        // Undo: Cmd/Ctrl+Z
        if ((e.metaKey || e.ctrlKey) && e.key === 'z' && !e.shiftKey) {
          e.preventDefault();
          executeUndo();
          return;
        }

        // Redo: Cmd/Ctrl+Shift+Z
        if ((e.metaKey || e.ctrlKey) && e.shiftKey && e.key === 'z') {
          e.preventDefault();
          executeRedo();
          return;
        }

        // Cmd/Ctrl+K = open command palette
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
          e.preventDefault();
          setPaletteOpen(true);
          return;
        }

        // Ignore if user is typing in an input/textarea
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

        // / = focus search
        if (e.key === '/') {
          e.preventDefault();
          const searchInput = document.querySelector('.sfb-search input');
          if (searchInput) searchInput.focus();
          return;
        }

        // n = add sibling
        if (e.key === 'n' && !e.shiftKey && selected) {
          e.preventDefault();
          const parent = normalizedFlat.find(n => n.id === selected.parent_id);
          if (parent) {
            const allowedTypes = ALLOWED_CHILDREN[parent.node_type];
            if (allowedTypes?.length > 0) {
              addChildInline(parent, selected.node_type);
            }
          }
          return;
        }

        // Shift+n = add child
        if (e.key === 'N' && e.shiftKey && selected) {
          e.preventDefault();
          const allowedTypes = ALLOWED_CHILDREN[selected.node_type];
          if (allowedTypes?.length > 0) {
            addChildInline(selected, allowedTypes[0]);
          }
          return;
        }

        // Delete = remove selected
        if ((e.key === 'Delete' || e.key === 'Backspace') && selected) {
          e.preventDefault();
          deleteNode(selected.id);
          return;
        }

        // Cmd/Ctrl + Arrow Up/Down = reorder
        if ((e.metaKey || e.ctrlKey) && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
          e.preventDefault();
          if (selected) {
            reorder(selected, e.key === 'ArrowUp' ? 'up' : 'down');
          }
          return;
        }
      };

      document.addEventListener('keydown', handleKeyDown);
      return () => document.removeEventListener('keydown', handleKeyDown);
    }, [selected, normalizedFlat, executeUndo, executeRedo]);

    // Helper: check if node is visible (all ancestors expanded + passes filter)
    function isNodeVisible(nodeId, collapsedSet, flatList) {
      const node = flatList.find(n => n.id === nodeId);
      if (!node) return false;

      // Check if matches search/filter
      const isMatch = matchesSearch(node, debouncedQuery, filter);
      const hasMatchChild = (node.children || []).some(ch => hasMatchingDescendant(ch, debouncedQuery, filter));
      const passesFilter = isMatch || hasMatchChild;
      if (!passesFilter) return false;

      // Check if all ancestors are expanded
      let current = node;
      while (current && current.parent_id) {
        const parent = flatList.find(n => n.id === current.parent_id);
        if (!parent) break;
        if (collapsedSet.has(parent.id)) return false;
        current = parent;
      }
      return true;
    }

    // Keyboard shortcuts for bulk selection
    useEffect(() => {
      function onKey(e) {
        // Esc = clear selection
        if (e.key === 'Escape') {
          clearSelection();
        }
        // Ctrl/Cmd+A = select all visible
        if ((e.key === 'a' || e.key === 'A') && (e.metaKey || e.ctrlKey)) {
          e.preventDefault();
          const visible = flat.filter(n => isNodeVisible(n.id, collapsed, flat));
          setSelectedFromArray(visible.map(n => n.id));
        }
      }
      window.addEventListener('keydown', onKey);
      return () => window.removeEventListener('keydown', onKey);
    }, [collapsed, flat, debouncedQuery, filter, clearSelection, setSelectedFromArray]);

    // Bulk action handlers
    const handleSelectAllInView = useCallback(() => {
      const visible = flat.filter(n => isNodeVisible(n.id, collapsed, flat));
      setSelectedFromArray(visible.map(n => n.id));
    }, [flat, collapsed, debouncedQuery, filter, setSelectedFromArray]);

    const handleBulkDelete = useCallback(async () => {
      if (!bulkSelected.size) return;
      if (!confirm(`Delete ${bulkSelected.size} node(s) and their children? This cannot be undone.`)) return;

      try {
        const ids = Array.from(bulkSelected);
        await Promise.all(ids.map(id =>
          wp.apiFetch({ path: '/sfb/v1/node/delete', method: 'POST', data: { id } })
        ));
        await load();
      } catch (err) {
        console.error('Bulk delete error', err);
        alert('Bulk delete failed. See console.');
      } finally {
        clearSelection();
      }
    }, [bulkSelected, clearSelection]);

    const handleBulkDuplicate = useCallback(async () => {
      if (!bulkSelected.size) return;

      try {
        const ids = Array.from(bulkSelected);
        await Promise.all(ids.map(id =>
          wp.apiFetch({ path: '/sfb/v1/node/duplicate', method: 'POST', data: { id } })
        ));
        await load();
      } catch (err) {
        console.error('Bulk duplicate error', err);
        alert('Bulk duplicate failed.');
      } finally {
        clearSelection();
      }
    }, [bulkSelected, clearSelection]);

    // Compute allowed targets for bulk move
    const computeAllowedTargets = useCallback(() => {
      if (!bulkSelected.size) return [];

      const selectedNodes = Array.from(bulkSelected).map(id => normalizedFlat.find(n => n.id === id)).filter(Boolean);
      if (!selectedNodes.length) return [];

      // Get all selected node types
      const selectedTypes = [...new Set(selectedNodes.map(n => n.node_type))];

      // Find parent types that can accept ALL selected node types
      const parentTypes = ['category', 'product', 'type']; // Exclude 'model' from being a parent
      const validParentTypes = parentTypes.filter(parentType => {
        const allowedChildren = CHILDREN_OF[parentType];
        return selectedTypes.every(nodeType => allowedChildren && allowedChildren.has(nodeType));
      });

      // Get all nodes of valid parent types
      let candidates = normalizedFlat.filter(n => validParentTypes.includes(n.node_type));

      // Exclude descendants of any selected node
      const selectedIds = new Set(bulkSelected);
      candidates = candidates.filter(candidate => {
        // Can't move to a descendant of any selected node
        return !selectedNodes.some(selectedNode => isAncestor(nodesById, selectedNode.id, candidate.id));
      });

      // Exclude the selected nodes themselves
      candidates = candidates.filter(c => !selectedIds.has(c.id));

      return candidates;
    }, [bulkSelected, normalizedFlat, nodesById]);

    // Compute end position of a parent's children
    const computeEndPositionOfParent = useCallback((parentId, flatList) => {
      const children = flatList.filter(n => n.parent_id === parentId);
      if (!children.length) return 0;
      return Math.max(...children.map(n => n.position || 0));
    }, []);

    const handleBulkMove = useCallback(() => {
      if (!bulkSelected.size) return;
      const options = computeAllowedTargets();
      setMoveModal({ open: true, targetId: null, options });
    }, [bulkSelected, computeAllowedTargets]);

    const confirmBulkMove = useCallback(async (targetId) => {
      if (!targetId) return;

      try {
        const ids = Array.from(bulkSelected);
        const lastPos = computeEndPositionOfParent(targetId, flat);
        await Promise.all(ids.map((id, idx) =>
          wp.apiFetch({
            path: '/sfb/v1/node/move',
            method: 'POST',
            data: { id, parent_id: targetId, position: lastPos + idx + 1 }
          })
        ));
        await load();
        setMoveModal({ open: false, targetId: null, options: [] });
      } catch (err) {
        console.error('Bulk move error', err);
        alert('Bulk move failed.');
      } finally {
        clearSelection();
      }
    }, [bulkSelected, flat, computeEndPositionOfParent, clearSelection]);

    // Bulk Export (safe, buildless)
    const handleBulkExport = useCallback(() => {
      try {
        // Helper: collect descendants (uses flat list which already has parent_id)
        function collectDescendants(rootIds, all) {
          const want = new Set(rootIds.map(Number));
          let added = true;
          while (added) {
            added = false;
            for (const n of all) {
              if (n.parent_id && want.has(Number(n.parent_id)) && !want.has(Number(n.id))) {
                want.add(Number(n.id));
                added = true;
              }
            }
          }
          return all.filter(n => want.has(Number(n.id)));
        }

        // Decide scope: selected subtree(s) or entire tree
        let nodesToExport;
        if (bulkSelected && bulkSelected.size > 0) {
          nodesToExport = collectDescendants(Array.from(bulkSelected), flat);
        } else {
          nodesToExport = flat;
        }

        const payload = {
          exported_at: new Date().toISOString(),
          form: { id: 1, title: 'Submittal Builder' },
          nodes: nodesToExport
        };

        // Download as JSON
        const blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        const suffix = (bulkSelected && bulkSelected.size > 0) ? '-selection' : '-all';
        a.download = `sfb-export${suffix}-${Date.now()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      } catch (err) {
        console.error('Bulk export error', err);
        alert('Export failed. See console for details.');
      }
    }, [flat, bulkSelected]);

    return h('div',{id:'sfb-admin-shell'},
      h('div',{className:'sfb-col sfb-col-left'},
        h('h2',null,'Form Tree'),
        h('div',{className:'sfb-tree-controls'},
          h(SplitButton, { selected, onAddCategory:addCategory, onAddChild:addChildInline }),
          h('button',{className:'button',onClick:openCatalogModal},'Load Sample Catalog'),
          h('button', {
            className: 'button button-link-delete',
            onClick: () => setShowWipeModal(true),
            title: 'Delete all nodes in this form'
          }, 'Delete All (Nuclear)'),
          h('div',{className:'sfb-tree-collapse-btns'},
            h('button',{className:'button',onClick:()=> window.__SFB && window.__SFB.collapseAll()},'Collapse All'),
            h('button',{className:'button',onClick:()=> window.__SFB && window.__SFB.expandAll()},'Expand All')
          )
        ),
        // Floating Action Toolbar
        h('div',{className:'sfb-toolbar'},
          h('button',{
            className:'button',
            disabled:!selected,
            onClick:()=> selected && duplicateNode(selected)
          },'Duplicate'),
          h('button',{
            className:'button',
            disabled:!selected,
            onClick:()=> {
              if(selected) window.dispatchEvent(new CustomEvent('sfb-rename', {detail: selected.id}));
            }
          },'Rename'),
          h('button',{
            className:'button',
            disabled:!selected,
            onClick:()=> selected && deleteNode(selected.id)
          },'Delete'),
          h('button',{className:'button',onClick:exportJSON},'Export JSON'),
          h('button',{className:'button',onClick:importJSON},'Import JSON'),
          // Agency feature: Save as Pack
          (window.SFB && SFB.isAgency) && h('button',{
            className:'button button-primary',
            onClick:()=> setPackModal({...packModal, open:true}),
            style: {marginLeft: '8px'}
          },'💼 Save as Pack')
        ),
        // Search & Filter Toolbar
        h('div',{className:'sfb-tree-toolbar'},
          h('div',{className:'sfb-search'},
            h('input',{
              type:'text',
              placeholder:'Search titles…',
              value:query,
              onChange:(e)=>setQuery(e.target.value)
            })
          ),
          h('div',{className:'sfb-pill'+(filter==='all'?' active':''), onClick:()=>setFilter('all')}, 'All'),
          h('div',{className:'sfb-pill'+(filter==='category'?' active':''), onClick:()=>setFilter('category')}, 'Category'),
          h('div',{className:'sfb-pill'+(filter==='product'?' active':''), onClick:()=>setFilter('product')}, 'Product'),
          h('div',{className:'sfb-pill'+(filter==='type'?' active':''), onClick:()=>setFilter('type')}, 'Type'),
          h('div',{className:'sfb-pill'+(filter==='model'?' active':''), onClick:()=>setFilter('model')}, 'Model'),
          h('div',{style:{borderLeft:'1px solid #e5e7eb',paddingLeft:'8px',marginLeft:'4px',display:'flex',gap:'4px'}},
            h('div',{className:'sfb-pill',onClick:()=>collapseByType('product'),title:'Collapse all Products'},'⊟ Products'),
            h('div',{className:'sfb-pill',onClick:()=>collapseByType('type'),title:'Collapse all Types'},'⊟ Types'),
            h('div',{className:'sfb-pill',onClick:()=>collapseByType('model'),title:'Collapse all Models'},'⊟ Models')
          )
        ),
        // Drag disabled banner
        dragDisabled && h('div', {className: 'sfb-drag-disabled-banner'},
          'Drag & drop is disabled while filtering.'
        ),
        // Bulk actions bar
        bulkSelected.size > 0 && (() => {
          const hasValidMoveTargets = computeAllowedTargets().length > 0;
          return h('div', {className: 'sfb-bulkbar'},
            h('div', {className: 'sfb-bulk-info'}, `${bulkSelected.size} selected`),
            h('div', {className: 'sfb-bulk-actions'}, [
              h('button', {key: 'select-all', className: 'button button-link', onClick: handleSelectAllInView}, 'Select all in view'),
              h('span', {key: 'spacer', className: 'sfb-bulk-spacer'}),
              h('button', {key: 'duplicate', className: 'button button-secondary', onClick: handleBulkDuplicate}, 'Duplicate'),
              h('button', {
                key: 'move',
                className: 'button button-secondary',
                disabled: !hasValidMoveTargets,
                title: !hasValidMoveTargets ? 'No common valid target for this selection' : '',
                onClick: handleBulkMove
              }, 'Move…'),
              h('button', {key: 'delete', className: 'button button-link-delete', onClick: handleBulkDelete}, 'Delete'),
              h('button', {key: 'clear', className: 'button', onClick: clearSelection}, 'Clear')
            ])
          );
        })(),
        loading ? h('p',null,'Loading...') :
          (tree.length>0
            ? h('div', {
                key: 'tree-' + Array.from(collapsed).sort((a,b)=>a-b).join(','),
                className: 'sfb-tree-wrapper'
              },
              tree.map(n=>h(Row,{
                key:n.id,
                node:n,
                onSelect:setSelected,
                selectedId:selected?.id,
                collapsed,
                onToggle:toggleCollapse,
                onAddChild:addChildInline,
                onRename:renameNode,
                onDuplicate:duplicateNode,
                onDelete:deleteNode,
                onDragDrop:handleDragDrop,
                searchQuery:debouncedQuery,
                searchFilter:filter,
                bulkSelected,
                onBulkToggle:handleBulkToggle,
                allNodes:normalizedFlat,
                loadForm:load,
                drag,
                setDrag,
                dragDisabled,
                flat,
                nodesById,
                lastClickedId:lastSelectedRef.current,
                setLastClickedId:(id)=>{lastSelectedRef.current=id;}
              }))
            )
            : h('div',{className:'sfb-empty'},
                h('h3',null,'Start your submittal form'),
                h('p',null,'Add your first category to begin. Categories organize your product lines.'),
                h('div',{className:'sfb-empty-actions'},
                  h('button',{className:'button button-primary',onClick:addCategory},'+ Add Category'),
                  h('button',{className:'button',onClick:openCatalogModal},'Load Sample Catalog'),
                  h('button', {
                    className: 'button button-link-delete',
                    onClick: () => setShowWipeModal(true),
                    title: 'Delete all nodes in this form'
                  }, 'Delete All (Nuclear)')
                )
              )
          )
      ),
      h('div',{className:'sfb-col sfb-col-right'},
        h(BulkActionsBar, {
          selectedIds: bulkSelected,
          nodes: normalizedFlat,
          onDelete: handleBulkDelete,
          onDuplicate: handleBulkDuplicate,
          onMove: handleBulkMove,
          onExport: handleBulkExport,
          onClear: clearBulkSelection
        }),
        selected && h(RightPane, {
          selected,
          onSave:save,
          onCreate:createNode,
          onDelete:deleteNode,
          onReorder:reorder,
          breadcrumbs: pathFromNode(flat, selected),
          onSelectNode: (node) => {
            setSelected(node);
            window.__SFB_SCROLL_TO = node.id;
          },
          allNodes: normalizedFlat
        })
      ),
      // Command Palette
      h(CommandPalette, {
        isOpen: paletteOpen,
        onClose: () => setPaletteOpen(false),
        nodes: normalizedFlat,
        onSelectNode: handlePaletteSelectNode,
        onAction: handlePaletteAction,
        selected
      }),
      // Bulk Move Modal
      moveModal.open && h('div', {
        className: 'sfb-modal-overlay',
        onClick: () => setMoveModal({ open: false, targetId: null, options: [] })
      },
        h('div', {
          className: 'sfb-modal',
          onClick: (e) => e.stopPropagation()
        },
          h('h3', null, 'Move to Parent'),
          h('div', {className: 'sfb-modal-body'},
            moveModal.options.length > 0
              ? h('div', {className: 'sfb-move-options'},
                  moveModal.options.map(option =>
                    h('label', {key: option.id},
                      h('input', {
                        type: 'radio',
                        name: 'bulk-move-target',
                        value: option.id,
                        checked: moveModal.targetId === option.id,
                        onChange: () => setMoveModal(prev => ({ ...prev, targetId: option.id }))
                      }),
                      h('span', null, `${TYPE_ICON[option.node_type]} ${option.title}`)
                    )
                  )
                )
              : h('p', {className: 'sfb-move-empty'}, 'No valid parent targets available.')
          ),
          h('div', {className: 'sfb-modal-footer'},
            h('button', {
              className: 'button',
              onClick: () => setMoveModal({ open: false, targetId: null, options: [] })
            }, 'Cancel'),
            h('button', {
              className: 'button button-primary',
              disabled: !moveModal.targetId,
              onClick: () => confirmBulkMove(moveModal.targetId)
            }, 'Move')
          )
        )
      ),
      // Sample Catalog Modal
      catalogModal.open && h('div', {
        className: 'sfb-modal-overlay',
        onClick: () => setCatalogModal({
          open: false,
          mode: 'replace',
          size: 'medium',
          withBranding: true,
          industryPack: catalogModal.industryPack
        })
      },
        h('div', {
          className: 'sfb-modal sfb-catalog-modal',
          onClick: (e) => e.stopPropagation()
        },
          h('h3', null, 'Load Sample Catalog'),
          h('div', {className: 'sfb-modal-body'},
            // Industry Pack selector
            h('div', {className: 'sfb-catalog-option-group'},
              h('label', {className: 'sfb-catalog-label'}, 'Industry Pack:'),
              h('div', {className: 'sfb-catalog-select-wrapper'},
                h('select', {
                  className: 'sfb-catalog-select',
                  value: catalogModal.industryPack,
                  onChange: (e) => setCatalogModal(prev => ({...prev, industryPack: e.target.value}))
                },
                  window.SFB && SFB.industryPacks
                    ? Object.entries(SFB.industryPacks).map(([key, title]) =>
                        h('option', {key, value: key}, title)
                      )
                    : h('option', {value: 'electrical'}, 'Electrical — Panels & Conduit')
                ),
                h('p', {className: 'sfb-catalog-hint'},
                  'Choose an industry-specific demo catalog. Each pack includes 3–5 categories with realistic products and metadata.'
                )
              )
            ),
            h('div', {className: 'sfb-catalog-option-group'},
              h('label', {className: 'sfb-catalog-label'}, 'Mode:'),
              h('div', {className: 'sfb-catalog-radio-group'},
                h('label', {className: 'sfb-catalog-radio'},
                  h('input', {
                    type: 'radio',
                    name: 'catalog-mode',
                    value: 'replace',
                    checked: catalogModal.mode === 'replace',
                    onChange: () => setCatalogModal(prev => ({...prev, mode: 'replace'}))
                  }),
                  h('span', null, 'Replace'),
                  h('small', null, ' (delete existing)')
                ),
                h('label', {className: 'sfb-catalog-radio'},
                  h('input', {
                    type: 'radio',
                    name: 'catalog-mode',
                    value: 'merge',
                    checked: catalogModal.mode === 'merge',
                    onChange: () => setCatalogModal(prev => ({...prev, mode: 'merge'}))
                  }),
                  h('span', null, 'Merge'),
                  h('small', null, ' (keep existing)')
                )
              )
            ),
            h('div', {className: 'sfb-catalog-option-group'},
              h('label', {className: 'sfb-catalog-label'}, 'Size:'),
              h('div', {className: 'sfb-catalog-radio-group'},
                h('label', {className: 'sfb-catalog-radio'},
                  h('input', {
                    type: 'radio',
                    name: 'catalog-size',
                    value: 'small',
                    checked: catalogModal.size === 'small',
                    onChange: () => setCatalogModal(prev => ({...prev, size: 'small'}))
                  }),
                  h('span', null, 'Small'),
                  h('small', null, ' (~30 items)')
                ),
                h('label', {className: 'sfb-catalog-radio'},
                  h('input', {
                    type: 'radio',
                    name: 'catalog-size',
                    value: 'medium',
                    checked: catalogModal.size === 'medium',
                    onChange: () => setCatalogModal(prev => ({...prev, size: 'medium'}))
                  }),
                  h('span', null, 'Medium'),
                  h('small', null, ' (~360 items)')
                ),
                h('label', {className: 'sfb-catalog-radio'},
                  h('input', {
                    type: 'radio',
                    name: 'catalog-size',
                    value: 'large',
                    checked: catalogModal.size === 'large',
                    onChange: () => setCatalogModal(prev => ({...prev, size: 'large'}))
                  }),
                  h('span', null, 'Large'),
                  h('small', null, ' (~2,880 items)')
                )
              )
            ),
            h('div', {className: 'sfb-catalog-option-group'},
              h('label', {className: 'sfb-catalog-checkbox'},
                h('input', {
                  type: 'checkbox',
                  checked: catalogModal.withBranding,
                  onChange: (e) => setCatalogModal(prev => ({...prev, withBranding: e.target.checked}))
                }),
                h('span', null, 'Include sample branding data')
              )
            )
          ),
          h('div', {className: 'sfb-modal-footer'},
            h('button', {
              className: 'button',
              onClick: () => setCatalogModal({
                open: false,
                mode: 'replace',
                size: 'medium',
                withBranding: true,
                industryPack: catalogModal.industryPack
              })
            }, 'Cancel'),
            h('button', {
              className: 'button button-primary',
              onClick: loadSampleCatalog
            }, 'Load Catalog')
          )
        )
      ),

      // Agency feature: Save as Pack modal
      packModal.open && h('div', {
        className: 'sfb-modal-overlay',
        onClick: () => setPackModal({open: false, name: '', includeBranding: true, includeNotes: false, saving: false})
      },
        h('div', {
          className: 'sfb-modal sfb-pack-modal',
          onClick: (e) => e.stopPropagation()
        },
          h('h3', null, '💼 Save Current Catalog as Agency Pack'),
          h('div', {className: 'sfb-modal-body'},
            h('div', {className: 'sfb-catalog-option-group'},
              h('label', {className: 'sfb-catalog-label'}, 'Pack Name:'),
              h('input', {
                type: 'text',
                className: 'sfb-pack-name-input',
                placeholder: 'e.g., Client A - Full Catalog',
                value: packModal.name,
                onChange: (e) => setPackModal(prev => ({...prev, name: e.target.value})),
                onKeyDown: (e) => {
                  if (e.key === 'Enter' && !packModal.saving) {
                    saveAsPack();
                  }
                },
                style: {width: '100%', padding: '10px', fontSize: '14px', borderRadius: '4px', border: '1px solid #ddd'}
              }),
              h('p', {className: 'sfb-catalog-hint'},
                'Choose a descriptive name for this Pack. You\'ll be able to identify it later when seeding other sites.'
              )
            ),
            h('div', {className: 'sfb-catalog-option-group'},
              h('label', {className: 'sfb-catalog-checkbox'},
                h('input', {
                  type: 'checkbox',
                  checked: packModal.includeBranding,
                  onChange: (e) => setPackModal(prev => ({...prev, includeBranding: e.target.checked}))
                }),
                h('span', null, 'Include branding settings'),
                h('small', {style: {display: 'block', marginLeft: '24px', color: '#6b7280'}},
                  'Save current logo, colors, and company info with this Pack'
                )
              )
            ),
            h('div', {className: 'sfb-catalog-option-group'},
              h('label', {className: 'sfb-catalog-checkbox'},
                h('input', {
                  type: 'checkbox',
                  checked: packModal.includeNotes,
                  onChange: (e) => setPackModal(prev => ({...prev, includeNotes: e.target.checked}))
                }),
                h('span', null, 'Include product notes/descriptions'),
                h('small', {style: {display: 'block', marginLeft: '24px', color: '#6b7280'}},
                  'Include all custom notes and descriptions (increases Pack size)'
                )
              )
            ),
            h('div', {style: {padding: '12px', background: '#f0f9ff', borderRadius: '6px', marginTop: '16px'}},
              h('p', {style: {margin: 0, fontSize: '13px', color: '#0c4a6e'}},
                h('strong', null, '💡 Tip: '),
                'After saving, export your Pack as JSON from ',
                h('strong', null, 'Agency Library'),
                ' to use on other sites during onboarding.'
              )
            )
          ),
          h('div', {className: 'sfb-modal-footer'},
            h('button', {
              className: 'button',
              onClick: () => setPackModal({open: false, name: '', includeBranding: true, includeNotes: false, saving: false}),
              disabled: packModal.saving
            }, 'Cancel'),
            h('button', {
              className: 'button button-primary',
              onClick: saveAsPack,
              disabled: packModal.saving || !packModal.name.trim()
            }, packModal.saving ? 'Saving...' : 'Save Pack')
          )
        )
      ),

      // Wipe/Delete All modal
      showWipeModal && h('div', { className:'sfb-modal-backdrop', onClick:(e)=>{ if(e.target===e.currentTarget) setShowWipeModal(false); } },
        h('div', { className:'sfb-modal-card' },
          h('h2', null, 'Delete All Data (Nuclear)'),
          h('p', null, 'This will permanently delete all categories, products, types, and models in this form.'),
          h('p', null, 'Optionally, you can also clear branding and create a JSON backup.'),
          h('div', { className:'sfb-fieldset' },
            h('label', null,
              h('input', { type:'checkbox', checked:wipeBackup, onChange:e=>setWipeBackup(e.target.checked) }),
              ' Create JSON backup before deleting'
            ),
            h('br'),
            h('label', null,
              h('input', { type:'checkbox', checked:wipeBranding, onChange:e=>setWipeBranding(e.target.checked) }),
              ' Also clear Branding settings'
            )
          ),
          h('div', { className:'sfb-fieldset' },
            h('div', { className:'sfb-legend' }, 'Type DELETE to confirm'),
            h('input', {
              type:'text',
              value:wipeConfirmText,
              onInput: e => setWipeConfirmText(e.target.value),
              placeholder:'DELETE',
              style:{ width:'160px' }
            })
          ),
          h('div', { className:'sfb-modal-actions' },
            h('button', { className:'button', onClick:()=> setShowWipeModal(false) }, 'Cancel'),
            h('button', {
              className:'button button-primary',
              disabled: wiping || wipeConfirmText !== 'DELETE',
              onClick: runWipeAll
            }, wiping ? 'Deleting…' : 'Delete All')
          )
        )
      ),

      // Toast notification
      toastMessage && h('div', {className:'sfb-toast'},
        h('span', null, toastMessage),
        undoStack.length > 0 && h('button', {
          className:'sfb-toast-action',
          onClick: () => {
            setToastMessage(null);
            executeUndo();
          }
        }, 'Undo')
      )
    );
  }

  function BrandingOnlyApp(){
    // Simple wrapper card so Branding is centered and clean
    return h('div', {className:'sfb-branding-only'},
      h('div', {className:'sfb-card'},
        h(BrandingBox, null)
      )
    );
  }

  document.addEventListener('DOMContentLoaded',function(){
    const rootEl = document.getElementById('sfb-admin-root');
    if (!rootEl) return;

    // Guarantee a single mount
    if (rootEl.__SFB_APP_MOUNTED) {
      try { wp.element.unmountComponentAtNode(rootEl); } catch(e){}
    }
    rootEl.__SFB_APP_MOUNTED = true;

    const view = (rootEl.dataset && rootEl.dataset.view) || (window.SFB_ADMIN && SFB_ADMIN.view) || 'builder';

    if (view === 'branding') {
      wp.element.render(h(BrandingOnlyApp), rootEl);
    } else {
      // default to Builder
      wp.element.render(h(App), rootEl);
    }
  });

  // ========================================
  // Onboarding Page Functionality
  // ========================================
  (function() {
    // Only run on onboarding page
    const params = new URLSearchParams(window.location.search);
    if (params.get('page') !== 'sfb-onboarding') return;

    // Pulse animation after successful setup
    if (params.get('setup') === 'done') {
      const btn = document.querySelector('#sfb-start-builder');
      if (btn) {
        btn.classList.add('sfb-pulse');
        setTimeout(() => btn.classList.remove('sfb-pulse'), 1600);
        btn.focus();
      }
    }

    // Toggle edit branding form with smooth animation
    const toggle = document.querySelector('#sfb-edit-branding-toggle');
    const panel = document.querySelector('#sfb-branding-form');

    if (toggle && panel) {
      // Add collapse class for animations
      panel.classList.add('sfb-collapse');

      // Helper functions for smooth open/close
      function openPanel() {
        panel.removeAttribute('hidden');
        // Measure height before opening
        const height = panel.scrollHeight;
        requestAnimationFrame(() => {
          panel.style.maxHeight = height + 'px';
          panel.classList.add('is-open');
          toggle.textContent = toggle.dataset.closeText || 'Hide Form';
        });
      }

      function closePanel() {
        panel.style.maxHeight = panel.scrollHeight + 'px';
        requestAnimationFrame(() => {
          panel.style.maxHeight = '0';
          panel.classList.remove('is-open');
          toggle.textContent = toggle.dataset.openText || 'Edit Branding';
        });
        // Add hidden attribute after animation completes
        setTimeout(() => {
          panel.setAttribute('hidden', '');
          panel.style.maxHeight = '';
        }, 260);
      }

      // Set initial state if panel is visible
      if (!panel.hasAttribute('hidden')) {
        panel.classList.add('is-open');
        panel.style.maxHeight = panel.scrollHeight + 'px';
      }

      // Toggle on click
      toggle.addEventListener('click', (e) => {
        e.preventDefault();
        const isHidden = panel.hasAttribute('hidden');
        if (isHidden) {
          openPanel();
        } else {
          closePanel();
        }
      });

      // Store text labels
      toggle.dataset.openText = 'Edit Branding';
      toggle.dataset.closeText = 'Hide Form';
    }
  })();

  // ========================================
  // Tools Page Functionality
  // ========================================
  (function() {
    // Only run on tools page
    const params = new URLSearchParams(window.location.search);
    if (params.get('page') !== 'sfb-tools') return;

    const el = {
      purgeBtn: document.querySelector('#sfb-purge-btn'),
      smokeBtn: document.querySelector('#sfb-smoke-btn'),
      status: document.querySelector('#sfb-drafts-status'),
      cron: document.querySelector('#sfb-cron-status'),
      stats: document.querySelector('#sfb-draft-stats')
    };

    function setStatus(cls, msg) {
      if (!el.status) return;
      el.status.className = 'sfb-status ' + cls;
      el.status.textContent = msg;
    }

    async function post(action, nonce) {
      const res = await fetch(ajaxurl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: new URLSearchParams({ action, _wpnonce: nonce })
      });
      const data = await res.json().catch(() => ({ success: false, data: { message: 'Invalid JSON' } }));
      if (!data.success) throw new Error(data.data?.message || 'Request failed');
      return data.data;
    }

    if (el.purgeBtn) {
      el.purgeBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        setStatus('warn', 'Working… purging expired drafts');
        el.purgeBtn.disabled = true;
        try {
          const data = await post('sfb_purge_expired_drafts', el.purgeBtn.dataset.nonce);
          setStatus('ok', data.message || 'Done.');
          if (el.stats) el.stats.textContent = data.stats_text || '';
        } catch (err) {
          setStatus('err', 'Error: ' + err.message);
        } finally {
          el.purgeBtn.disabled = false;
        }
      });
    }

    if (el.smokeBtn) {
      el.smokeBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        setStatus('warn', 'Running smoke test…');
        el.smokeBtn.disabled = true;
        try {
          const data = await post('sfb_run_smoke_test', el.smokeBtn.dataset.nonce);
          setStatus('ok', data.message || 'Smoke test passed.');
          if (el.stats) el.stats.textContent = data.stats_text || '';
        } catch (err) {
          setStatus('err', 'Error: ' + err.message);
        } finally {
          el.smokeBtn.disabled = false;
        }
      });
    }
  })();

  // ========================================================================
  // Copy License Key Functionality
  // ========================================================================
  (function() {
    if (!location.search.includes('page=sfb-license')) return;

    const btn = document.querySelector('[data-sfb-copy-key]');
    if (!btn) return;

    btn.addEventListener('click', async () => {
      const fullKey = btn.getAttribute('data-key');

      if (!fullKey) {
        alert('No license key available to copy.');
        return;
      }

      try {
        await navigator.clipboard.writeText(fullKey);

        // Show success feedback
        const originalText = btn.textContent;
        btn.textContent = 'Copied!';
        btn.disabled = true;

        // Show toast if available
        const toast = document.getElementById('sfb-copy-toast');
        if (toast) {
          toast.classList.add('sfb-show-toast');
          setTimeout(() => {
            toast.classList.remove('sfb-show-toast');
          }, 2500);
        }

        setTimeout(() => {
          btn.textContent = originalText;
          btn.disabled = false;
        }, 1200);
      } catch (err) {
        console.error('Copy failed:', err);
        alert('Copy failed. Please copy manually.');
      }
    });
  })();

  // ========================================================================
  // Branding Panel - Live Preview + Autosave
  // ========================================================================
  (function() {
    // Only run on branding page
    if (!location.search.includes('page=sfb-branding')) return;
    if (!window.SFB || !window.SFB.brand) return;

    console.log('[SFB] Initializing branding panel with autosave');

    // State management
    const Brand = {
      state: {
        company: { ...window.SFB.brand.company },
        visual: { ...window.SFB.brand.visual }
      },
      autosaveTimer: null,
      saving: false
    };

    // DOM Elements
    const elements = {
      // Company fields
      companyName: document.getElementById('sfb-company-name'),
      companyAddress: document.getElementById('sfb-company-address'),
      companyPhone: document.getElementById('sfb-company-phone'),
      companyWebsite: document.getElementById('sfb-company-website'),
      logoUrl: document.getElementById('sfb-logo-url'),
      logoPreview: document.getElementById('sfb-logo-preview'),
      uploadLogo: document.getElementById('sfb-upload-logo'),
      removeLogo: document.getElementById('sfb-remove-logo'),

      // Visual fields
      primaryColor: document.getElementById('sfb-primary-color'),
      primaryColorText: document.getElementById('sfb-primary-color-text'),
      colorPreviewBox: document.querySelector('.sfb-color-preview-box'),
      includeCover: document.querySelector('input[name*="[cover_default]"]'),
      footerText: document.getElementById('sfb-footer-text'),

      // Agency features (Phase B)
      useDefaultPreset: document.getElementById('sfb-use-default-preset'),

      // Live Preview elements
      previewHeader: document.getElementById('sfb-preview-header'),
      previewLogo: document.getElementById('sfb-preview-logo'),
      previewCompany: document.getElementById('sfb-preview-company'),
      previewTitle: document.getElementById('sfb-preview-title'),
      previewFooter: document.getElementById('sfb-preview-footer'),

      // Save button
      saveButton: document.getElementById('sfb-save-branding')
    };

    // Brand presets
    const presets = {
      modern_blue: '#0E45E9',
      architect_gray: '#9AA0A6',
      engineering_bold: '#0F5C2E',
      clean_violet: '#7861FF'
    };

    // Update preview
    function updatePreview() {
      const state = Brand.state;

      // Update CSS custom property for dynamic theming
      document.documentElement.style.setProperty('--sfb-primary', state.visual.primary_color);

      // Update color preview box if exists
      if (elements.colorPreviewBox) {
        elements.colorPreviewBox.style.backgroundColor = state.visual.primary_color;
      }

      // Update logo preview in form
      if (elements.logoPreview) {
        if (state.company.logo_url) {
          elements.logoPreview.innerHTML = `<img src="${state.company.logo_url}" alt="Logo preview">`;
        } else {
          elements.logoPreview.innerHTML = '';
        }
      }

      // Update live preview panel - Logo
      if (elements.previewLogo) {
        if (state.company.logo_url) {
          elements.previewLogo.innerHTML = `<img src="${state.company.logo_url}" alt="Logo">`;
        } else {
          elements.previewLogo.innerHTML = '<div class="sfb-logo-placeholder">Your Logo</div>';
        }
      }

      // Update live preview panel - Company name
      if (elements.previewCompany) {
        elements.previewCompany.textContent = state.company.name || 'Your Company Name';
      }

      // Update live preview panel - Header color
      if (elements.previewHeader) {
        elements.previewHeader.style.borderBottomColor = state.visual.primary_color;
      }

      // Update live preview panel - Title color
      if (elements.previewTitle) {
        elements.previewTitle.style.color = state.visual.primary_color;
      }

      // Update live preview panel - Footer text
      if (elements.previewFooter) {
        elements.previewFooter.textContent = state.visual.footer_text || 'Generated by Submittal & Spec Builder';
      }

      // Show/hide remove logo button
      if (elements.removeLogo) {
        elements.removeLogo.style.display = state.company.logo_url ? 'inline-block' : 'none';
      }

      console.log('[SFB] Preview updated with color:', state.visual.primary_color);
    }

    // Schedule autosave with debounce
    function scheduleAutosave() {
      if (Brand.autosaveTimer) {
        clearTimeout(Brand.autosaveTimer);
      }

      Brand.autosaveTimer = setTimeout(() => {
        saveSettings();
      }, 600); // 600ms debounce
    }

    // Save settings via AJAX
    async function saveSettings() {
      if (Brand.saving) return;

      Brand.saving = true;
      console.log('[SFB] Autosaving brand settings...', Brand.state);

      if (elements.saveButton) {
        elements.saveButton.textContent = 'Saving...';
        elements.saveButton.disabled = true;
      }

      try {
        const formData = new FormData();
        formData.append('action', 'sfb_save_brand');
        formData.append('_ajax_nonce', window.SFB.nonce);
        formData.append('data', JSON.stringify(Brand.state));

        const response = await fetch(window.SFB.ajax_url, {
          method: 'POST',
          body: formData
        });

        const text = await response.text();
        let data;

        try {
          data = JSON.parse(text);
        } catch (e) {
          console.error('[SFB] Failed to parse response:', text);
          throw new Error('Invalid server response');
        }

        if (!response.ok || !data.success) {
          throw new Error(data.data?.message || 'Save failed');
        }

        console.log('[SFB] Brand settings saved successfully');

        if (elements.saveButton) {
          elements.saveButton.textContent = '✓ Saved';
          setTimeout(() => {
            elements.saveButton.textContent = 'Save Branding';
          }, 2000);
        }

      } catch (error) {
        console.error('[SFB] Error saving brand settings:', error);
        if (elements.saveButton) {
          elements.saveButton.textContent = '✗ Error';
          setTimeout(() => {
            elements.saveButton.textContent = 'Save Branding';
          }, 2000);
        }
      } finally {
        Brand.saving = false;
        if (elements.saveButton) {
          elements.saveButton.disabled = false;
        }
      }
    }

    // Apply preset
    function applyPreset(presetKey) {
      // Convert hyphenated keys to underscores for lookup
      const normalizedKey = presetKey.replace(/-/g, '_');
      const color = presets[normalizedKey];

      if (!color) {
        console.warn('[SFB] Unknown preset:', presetKey);
        return;
      }

      console.log('[SFB] Applying preset:', presetKey, '→', color);

      Brand.state.visual.primary_color = color;
      Brand.state.visual.preset_key = normalizedKey;

      if (elements.primaryColor) elements.primaryColor.value = color;
      if (elements.primaryColorText) elements.primaryColorText.value = color;

      updatePreview();
      scheduleAutosave();
    }

    // Wire up company inputs
    if (elements.companyName) {
      elements.companyName.addEventListener('input', (e) => {
        Brand.state.company.name = e.target.value;
        updatePreview();
        scheduleAutosave();
      });
    }

    if (elements.companyAddress) {
      elements.companyAddress.addEventListener('input', (e) => {
        Brand.state.company.address = e.target.value;
        scheduleAutosave();
      });
    }

    if (elements.companyPhone) {
      elements.companyPhone.addEventListener('input', (e) => {
        Brand.state.company.phone = e.target.value;
        scheduleAutosave();
      });
    }

    if (elements.companyWebsite) {
      elements.companyWebsite.addEventListener('input', (e) => {
        Brand.state.company.website = e.target.value;
        scheduleAutosave();
      });
    }

    // Wire up visual inputs
    if (elements.primaryColor) {
      elements.primaryColor.addEventListener('input', (e) => {
        Brand.state.visual.primary_color = e.target.value;
        Brand.state.visual.preset_key = 'custom';
        if (elements.primaryColorText) elements.primaryColorText.value = e.target.value;
        updatePreview();
        scheduleAutosave();
      });
    }

    if (elements.primaryColorText) {
      elements.primaryColorText.addEventListener('input', (e) => {
        const value = e.target.value;
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
          Brand.state.visual.primary_color = value;
          Brand.state.visual.preset_key = 'custom';
          if (elements.primaryColor) elements.primaryColor.value = value;
          updatePreview();
          scheduleAutosave();
        }
      });
    }

    if (elements.includeCover) {
      elements.includeCover.addEventListener('change', (e) => {
        Brand.state.visual.include_cover = e.target.checked;
        scheduleAutosave();
      });
    }

    if (elements.footerText) {
      elements.footerText.addEventListener('input', (e) => {
        Brand.state.visual.footer_text = e.target.value;
        scheduleAutosave();
      });
    }

    // Use default preset toggle (Agency - Phase B)
    if (elements.useDefaultPreset) {
      elements.useDefaultPreset.addEventListener('change', (e) => {
        Brand.state.use_default_preset = e.target.checked;
        scheduleAutosave();
      });
    }

    // White-Label Mode Toggle (Agency - MVP)
    const whiteLabelToggle = document.getElementById('sfb-white-label-enabled');
    const whiteLabelSettings = document.getElementById('sfb-white-label-settings');
    if (whiteLabelToggle && whiteLabelSettings) {
      whiteLabelToggle.addEventListener('change', (e) => {
        // Show/hide white-label settings based on toggle
        if (e.target.checked) {
          whiteLabelSettings.style.display = 'block';
        } else {
          whiteLabelSettings.style.display = 'none';
        }
      });
    }

    // Logo upload using WordPress media library
    if (elements.uploadLogo) {
      elements.uploadLogo.addEventListener('click', (e) => {
        e.preventDefault();

        const frame = wp.media({
          title: 'Select Company Logo',
          button: { text: 'Use this logo' },
          multiple: false
        });

        frame.on('select', () => {
          const attachment = frame.state().get('selection').first().toJSON();
          Brand.state.company.logo_id = attachment.id;
          Brand.state.company.logo_url = attachment.url;

          if (elements.logoUrl) elements.logoUrl.value = attachment.url;

          updatePreview();
          scheduleAutosave();
        });

        frame.open();
      });
    }

    // Remove logo
    if (elements.removeLogo) {
      elements.removeLogo.addEventListener('click', (e) => {
        e.preventDefault();

        Brand.state.company.logo_id = 0;
        Brand.state.company.logo_url = '';

        if (elements.logoUrl) elements.logoUrl.value = '';

        updatePreview();
        scheduleAutosave();
      });
    }

    // Wire up preset buttons - make entire card clickable
    document.querySelectorAll('.sfb-preset-card[data-preset]').forEach(card => {
      // Make entire card clickable
      card.addEventListener('click', (e) => {
        e.preventDefault();
        const presetKey = card.dataset.preset;
        applyPreset(presetKey);
      });

      // Add pointer cursor to indicate clickability
      card.style.cursor = 'pointer';
    });

    // Also wire up individual preset buttons for backwards compatibility
    document.querySelectorAll('.sfb-preset-button[data-preset]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation(); // Prevent double-firing from card click
        const presetKey = btn.dataset.preset;
        applyPreset(presetKey);
      });
    });

    // Manual save button
    if (elements.saveButton) {
      elements.saveButton.addEventListener('click', (e) => {
        e.preventDefault();
        saveSettings();
      });
    }

    // Initial preview update
    updatePreview();

    console.log('[SFB] Branding panel initialized');
  })();
})();
