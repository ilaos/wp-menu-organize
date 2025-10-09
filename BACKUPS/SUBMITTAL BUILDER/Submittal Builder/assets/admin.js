(function () {
  const { createElement: h, useState, useEffect, useLayoutEffect, Fragment, useRef, useCallback, useMemo } = wp.element;

  // Nonce
  if (window.SFB_ADMIN && SFB_ADMIN.nonce && wp.apiFetch && wp.apiFetch.createNonceMiddleware) {
    wp.apiFetch.use( wp.apiFetch.createNonceMiddleware(SFB_ADMIN.nonce) );
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

  function Row({ node, onSelect, selectedId, collapsed, onToggle, onAddChild, onRename, onDuplicate, onDelete, searchQuery, searchFilter }){
    const ref = useRef(null);
    const [isEditing, setIsEditing] = useState(false);
    const [editValue, setEditValue] = useState(node.title);
    const [showMenu, setShowMenu] = useState(false);
    const inputRef = useRef(null);

    const isSel = selectedId === node.id;
    const hasChildren = (node.children||[]).length > 0;
    const isCollapsed = collapsed.has(node.id);
    const allowedChild = ALLOWED_CHILDREN[node.node_type];

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

    const saveEdit = () => {
      if (editValue.trim() && editValue !== node.title) {
        onRename(node, editValue.trim());
      }
      setIsEditing(false);
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

    return h(Fragment, null,
      h('div', {
        ref,
        className:'sfb-tree-row'+(isSel?' selected':'')+(isDimmed?' sfb-dim':''),
        'data-id': node.id,
        'data-collapsed': isCollapsed ? '1' : '0',
        onClick:(e)=>{ e.stopPropagation(); if(!isEditing) onSelect(node); }
      },
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
                  value: editValue,
                  onChange: (e)=> setEditValue(e.target.value),
                  onKeyDown: handleKeyDown,
                  onBlur: saveEdit,
                  onClick: (e)=> e.stopPropagation()
                })
              )
            : h('span', {
                className:'sfb-node-title',
                style:{fontWeight: node.node_type!=='model'?'600':'400'},
                onClick: (e)=> { e.stopPropagation(); startEdit(); }
              }, node.title),
          h('span', {className:'sfb-badge '+node.node_type}, TYPE_LABEL[node.node_type])
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
              onClick:(e)=>{ e.stopPropagation(); setShowMenu(false); onAddChild(node, allowedChild[0]); }
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
      hasChildren && h('div', {
        className:'sfb-tree-children',
        'data-parent': node.id,
        style: { display: isCollapsed ? 'none' : 'block' }
      },
        (node.children||[]).map(ch=> h(Row,{
          key:ch.id, node:ch, onSelect, selectedId, collapsed, onToggle, onAddChild, onRename, onDuplicate, onDelete,
          searchQuery, searchFilter
        }))
      )
    );
  }

  function RightPane({ selected, onSave, onCreate, onDelete, onReorder, breadcrumbs, onSelectNode }){
    return h('div', {className:'sfb-right'},
      h('div', {className:'sfb-right-inner sfb-card sfb-inspector-sticky'},
        h(Inspector, { node: selected, onSave, onCreate, onDelete, onReorder, breadcrumbs, onSelectNode })
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

  function Inspector({ node, onSave, onCreate, onDelete, onReorder, breadcrumbs, onSelectNode }){
    if (!node) return h('p', null, 'Select a node to edit');

    const isModel = node.node_type === 'model';
    const fields = (node.settings && node.settings.fields) || {};
    const [title, setTitle] = useState(node.title);
    const [size, setSize] = useState(fields.size || '');
    const [flange, setFlange] = useState(fields.flange || '');
    const [thickness, setThickness] = useState(fields.thickness || '');
    const [ksi, setKsi] = useState(fields.ksi || '');
    const titleInputRef = useRef(null);

    // Auto-focus and select title input when node changes (for new nodes)
    useEffect(()=> {
      if (titleInputRef.current && node.title.startsWith('New ') || node.title === 'Category' || node.title === 'Product' || node.title === 'Type' || node.title === 'Model' || node.title === 'Untitled') {
        titleInputRef.current.focus();
        titleInputRef.current.select();
      }
    }, [node.id]);

    function save(){
      const payload = {
        id: node.id,
        form_id: node.form_id || 1,
        parent_id: node.parent_id || 0,
        node_type: node.node_type,
        title,
        position: node.position || 0,
        settings: isModel ? { fields:{ size, flange, thickness, ksi } } : (node.settings || {})
      };
      onSave(payload);

      // Show success toast
      if (wp.data && wp.data.dispatch) {
        wp.data.dispatch('core/notices').createNotice('success', 'Saved', {
          isDismissible: true,
          type: 'snackbar'
        });
      }
    }

    function createChild(type){
      onCreate({
        form_id: node.form_id || 1,
        parent_id: node.id,
        node_type: type,
        title: TYPE_LABEL[type]
      });
    }

    const allowedChildren = {
      category: ['product'],
      product:  ['type'],
      type:     ['model'],
      model:    []
    }[node.node_type];

    return h(Fragment, null,
      // Breadcrumbs
      breadcrumbs && breadcrumbs.length > 0 && h('div', {className:'sfb-breadcrumbs'},
        breadcrumbs.map((seg,i)=>
          h('span',{key:i},
            i < breadcrumbs.length - 1
              ? h('a', { onClick: () => onSelectNode && onSelectNode(seg) }, seg.title)
              : seg.title
          )
        )
      ),
      h('div', { style:{display:'flex', gap:'8px', marginBottom:'10px'} },
        h('button', { className:'button', onClick: ()=>onReorder(node,'up') }, 'Move Up'),
        h('button', { className:'button', onClick: ()=>onReorder(node,'down') }, 'Move Down'),
        h('button', { className:'button button-link-delete', onClick: ()=> onDelete(node.id) }, 'Delete')
      ),
      h('div', { style:{marginBottom:'12px'} },
        h('label',{style:{display:'block',fontWeight:600}},'Node Type'),
        h('div', null, TYPE_LABEL[node.node_type])
      ),
      h('div', { style:{marginBottom:'12px'} },
        h('label',{style:{display:'block',fontWeight:600}},'Title'),
        h('input',{
          ref: titleInputRef,
          type:'text',
          value:title,
          onChange:e=>setTitle(e.target.value),
          style:{width:'100%'}
        })
      ),
      isModel && h('fieldset',{style:{border:'1px solid #e5e7eb',padding:'10px',borderRadius:'6px'}},
        h('legend',{style:{padding:'0 6px'}},'Model Fields'),
        h('div',{style:{display:'grid',gridTemplateColumns:'1fr 1fr',gap:'10px'}},
          h('div',null,h('label',null,'Size'),h('input',{type:'text',value:size,onChange:e=>setSize(e.target.value),style:{width:'100%'}})),
          h('div',null,h('label',null,'Flange'),h('input',{type:'text',value:flange,onChange:e=>setFlange(e.target.value),style:{width:'100%'}})),
          h('div',null,h('label',null,'Thickness'),h('input',{type:'text',value:thickness,onChange:e=>setThickness(e.target.value),style:{width:'100%'}})),
          h('div',null,h('label',null,'KSI'),h('input',{type:'text',value:ksi,onChange:e=>setKsi(e.target.value),style:{width:'100%'}}))
        )
      ),
      h('button',{className:'button button-primary', onClick:save, style:{marginTop:'10px'}},'Save'),
      allowedChildren && allowedChildren.length>0 && h('div',{style:{marginTop:'14px'}},
        h('label',{style:{display:'block',fontWeight:600,marginBottom:'6px'}},'Add child:'),
        allowedChildren.map(t=> h('button',{key:t,className:'button',style:{marginRight:'6px'},onClick:()=>createChild(t)}, TYPE_LABEL[t] ))
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

  function App(){
    const [loading, setLoading] = useState(true);
    const [flat, setFlat] = useState([]);
    const [tree, setTree] = useState([]);
    const [selected, setSelected] = useState(null);
    const nextSelectIdRef = useRef(null);
    const [collapsed, setCollapsed] = useState(loadCollapsedSet);
    const { query, setQuery, filter, setFilter, debouncedQuery } = useSearchFilter();

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

    function seed(){
      wp.apiFetch({ path:'/sfb/v1/form/seed', method:'POST' })
        .then(()=>load())
        .catch(err=>{ console.error(err); alert('Seed failed: '+(err?.message||err)); });
    }

    function save(payload){
      wp.apiFetch({ path:'/sfb/v1/node/save', method:'POST', data: payload })
        .then(res=> res?.ok ? load() : alert('Save failed'))
        .catch(err=>{ console.error(err); alert('Save error: '+(err?.message||err)); });
    }

    function createNode(payload){
      wp.apiFetch({ path:'/sfb/v1/node/create', method:'POST', data: payload })
        .then(res=> {
          if (res?.ok && res.node?.id){
            nextSelectIdRef.current = res.node.id;
            load();
          } else {
            alert('Create failed');
          }
        })
        .catch(err=>{ console.error(err); alert('Create error: '+(err?.message||err)); });
    }

    function addCategory(){
      createNode({ form_id:1, parent_id:0, node_type:'category', title:'New Category' });
    }

    function addChildInline(parentNode, childType){
      createNode({
        form_id: parentNode.form_id || 1,
        parent_id: parentNode.id,
        node_type: childType,
        title: TYPE_LABEL[childType]
      });
    }

    function deleteNode(id){
      if (!confirm('Delete this node and all its children?')) return;
      wp.apiFetch({ path:'/sfb/v1/node/delete', method:'POST', data:{ id } })
        .then(res=> res?.ok ? (setSelected(null), load()) : alert('Delete failed'))
        .catch(err=>{ console.error(err); alert('Delete error: '+(err?.message||err)); });
    }

    function reorder(node, direction){
      wp.apiFetch({ path:'/sfb/v1/node/reorder', method:'POST', data:{ id: node.id, direction } })
        .then(res=> res?.ok ? load() : alert('Move failed'))
        .catch(err=>{ console.error(err); alert('Move error: '+(err?.message||err)); });
    }

    function renameNode(node, newTitle){
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
        .then(res=> res?.ok ? load() : alert('Rename failed'))
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

    return h('div',{id:'sfb-admin-shell'},
      h('div',{className:'sfb-col sfb-col-left'},
        h('h2',null,'Form Tree'),
        h('div',{className:'sfb-tree-controls'},
          h(SplitButton, { selected, onAddCategory:addCategory, onAddChild:addChildInline }),
          h('button',{className:'button',onClick:seed},'Seed Demo'),
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
          h('button',{className:'button',onClick:importJSON},'Import JSON')
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
                searchQuery:debouncedQuery,
                searchFilter:filter
              }))
            )
            : h('div',{className:'sfb-empty'},
                h('h3',null,'Start your submittal form'),
                h('p',null,'Add your first category to begin. Categories organize your product lines.'),
                h('div',{className:'sfb-empty-actions'},
                  h('button',{className:'button button-primary',onClick:addCategory},'+ Add Category'),
                  h('button',{className:'button',onClick:seed},'Seed Demo')
                )
              )
          )
      ),
      h('div',{className:'sfb-col sfb-col-right'},
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
          }
        })
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
})();
