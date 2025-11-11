/**
 * Submittal & Spec Sheet Builder - Frontend JavaScript
 * State machine for 3-step flow, product handling, PDF generation
 *
 * @package SubmittalBuilder
 * @version 1.0.2
 */

(function() {
  'use strict';

  // ========== State Management ==========
  const state = {
    currentStep: 1,
    products: [], // All available products (raw from server)
    productsMap: new Map(), // Map<composite_key, product> - deduplicated
    byCategory: new Map(), // Map<category, composite_key[]> - category index
    categoryPositionMap: new Map(), // Map<category_id, position> - actual category node positions from database
    categoryIdMap: new Map(), // Map<category_name, category_id> - for looking up category positions
    byTypeWithinCategory: new Map(), // Map<'category:type', composite_key[]> - type index
    productOrderMap: new Map(), // Map<category::product_label, product_id> - tracks global product order from API
    productPositionMap: new Map(), // Map<product_id, position> - actual product node positions from database
    selected: new Set(), // Set of composite_keys for selected products (localStorage persistence)
    selectedProducts: new Map(), // Map<composite_key, product> - selected using composite keys (for compatibility)
    selectedFieldValues: new Map(), // Map<composite_key, {fieldName: selectedValue}> - track user-selected dropdown values
    activeCategory: null,
    activeType: null, // Track active type filter
    searchQuery: '',
    projectName: '',
    projectNotes: '',
    pdfUrl: null,
    collapsedGroups: new Set() // Set of product group keys that are collapsed (localStorage persistence)
  };

  // ========== DOM Elements ==========
  let elements = {};

  // ========== Initialize ==========
  function init() {
    // Cache DOM elements
    cacheElements();

    // Restore state from sessionStorage
    restoreState();

    // Load products from server
    loadProducts();

    // Attach event listeners
    attachEventListeners();

    // Initialize UI
    updateUI();
  }

  function cacheElements() {
    const app = document.getElementById('sfb-builder-app');
    if (!app) return;

    elements = {
      app,
      nonce: app.dataset.nonce,
      ajaxUrl: app.dataset.ajaxUrl,
      restUrl: app.dataset.restUrl,
      leadCapture: app.dataset.leadCapture === '1',
      branding: JSON.parse(app.dataset.branding || '{}'),

      // Steps
      step1: document.getElementById('sfb-step-1'),
      step2: document.getElementById('sfb-step-2'),
      step3: document.getElementById('sfb-step-3'),

      // Header
      headerProject: document.getElementById('sfb-header-project'),
      pills: document.querySelectorAll('.sfb-pill'),

      // Step 1: Products
      searchInput: document.getElementById('sfb-product-search'),
      categoryList: document.getElementById('sfb-category-list'),
      productsGrid: document.getElementById('sfb-products-grid'),
      productsEmpty: document.getElementById('sfb-products-empty'),
      resultsCount: document.getElementById('sfb-results-count'),
      clearFilters: document.getElementById('sfb-clear-filters'),
      toggleAllGroupsBtn: document.getElementById('sfb-toggle-all-groups'),
      toggleAllText: document.getElementById('sfb-toggle-all-text'),
      selectionCounter: document.getElementById('sfb-selection-counter'),
      selectionCountNumber: document.getElementById('sfb-selection-count-number'),
      selectionViewBtn: document.getElementById('sfb-selection-view-btn'),

      // Selected Products Tray (new live tray)
      tray: document.getElementById('sfb-selected-tray'),
      trayHeader: document.getElementById('sfb-tray-header'),
      trayCountHeader: document.getElementById('sfb-tray-count-header'),
      trayProductsList: document.getElementById('sfb-tray-products-list'),
      trayContinueBtn: document.getElementById('sfb-tray-continue'),
      trayClearAllBtn: document.getElementById('sfb-tray-clear-all'),
      trayToggleBtn: document.getElementById('sfb-tray-toggle'),

      // Step 2: Review
      reviewProductsList: document.getElementById('sfb-review-products-list'),
      reviewCount: document.getElementById('sfb-review-count'),
      reviewClearAllBtn: document.getElementById('sfb-review-clear-all'),
      returnToProductsBtn: document.getElementById('sfb-return-to-products'),
      projectNameInput: document.getElementById('sfb-project-name'),
      projectNotesInput: document.getElementById('sfb-project-notes'),
      backToProductsBtn: document.getElementById('sfb-back-to-products'),
      generatePdfBtn: document.getElementById('sfb-generate-pdf'),
      stickyActions: document.getElementById('sfb-sticky-actions'),
      backToProductsStickyBtn: document.getElementById('sfb-back-to-products-sticky'),
      generatePdfStickyBtn: document.getElementById('sfb-generate-pdf-sticky'),

      // Step 3: Generate
      openPdfBtn: document.getElementById('sfb-open-pdf'),
      startOverBtn: document.getElementById('sfb-start-over'),

      // Overlay
      loadingOverlay: document.getElementById('sfb-loading-overlay')
    };
  }

  // ========== Event Listeners ==========
  function attachEventListeners() {
    // Search
    if (elements.searchInput) {
      elements.searchInput.addEventListener('input', debounce(handleSearch, 300));
    }

    // Clear filters
    if (elements.clearFilters) {
      elements.clearFilters.addEventListener('click', clearFilters);
    }

    // Toggle all groups expand/collapse
    if (elements.toggleAllGroupsBtn) {
      elements.toggleAllGroupsBtn.addEventListener('click', toggleAllGroups);
    }

    // Selection counter "View" button
    if (elements.selectionViewBtn) {
      elements.selectionViewBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        // Go directly to review step
        goToStep(2);
      });
    }

    // Make progress pills clickable (only completed steps)
    elements.pills.forEach((pill, index) => {
      pill.addEventListener('click', (e) => {
        const stepNumber = index + 1;

        // Only allow navigation to completed steps or current step
        if (pill.classList.contains('sfb-pill-complete') || pill.classList.contains('sfb-pill-active')) {
          goToStep(stepNumber);
        }
      });
    });

    // Tray continue button
    if (elements.trayContinueBtn) {
      elements.trayContinueBtn.addEventListener('click', () => {
        if (state.currentStep === 1) {
          // Step 1: Go to review step and collapse tray
          goToStep(2);
          if (elements.tray && !elements.tray.classList.contains('sfb-tray--collapsed')) {
            toggleTray();
          }
        } else if (state.currentStep === 2) {
          // Step 2: Generate PDF and collapse tray
          handleGeneratePDF();
          if (elements.tray && !elements.tray.classList.contains('sfb-tray--collapsed')) {
            toggleTray();
          }
        }
      });
    }

    // Tray clear all button
    if (elements.trayClearAllBtn) {
      elements.trayClearAllBtn.addEventListener('click', clearAllSelections);
    }

    // Tray toggle button
    if (elements.trayToggleBtn) {
      elements.trayToggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleTray();
      });
    }

    // Tray header (also clickable for better UX)
    if (elements.trayHeader) {
      elements.trayHeader.addEventListener('click', toggleTray);
    }

    // Note: Removed auto-close on outside click - users should manually toggle tray
    // This prevents annoying auto-collapse when selecting multiple products

    // Step 2 buttons
    if (elements.backToProductsBtn) {
      elements.backToProductsBtn.addEventListener('click', () => goToStep(1));
    }

    if (elements.generatePdfBtn) {
      elements.generatePdfBtn.addEventListener('click', () => handleGeneratePDF());
    }

    // Review clear all button
    if (elements.reviewClearAllBtn) {
      elements.reviewClearAllBtn.addEventListener('click', () => {
        if (confirm('Clear all selected products?')) {
          state.selected.clear();
          state.selectedProducts.clear();
          localStorage.removeItem('sfb-selected');
          sessionStorage.removeItem('sfb_selected_products'); // Legacy cleanup
          renderReview();
        }
      });
    }

    // Return to products button (empty state)
    if (elements.returnToProductsBtn) {
      elements.returnToProductsBtn.addEventListener('click', () => goToStep(1));
    }

    // Sticky action buttons
    if (elements.backToProductsStickyBtn) {
      elements.backToProductsStickyBtn.addEventListener('click', () => goToStep(1));
    }

    if (elements.generatePdfStickyBtn) {
      elements.generatePdfStickyBtn.addEventListener('click', () => handleGeneratePDF());
    }

    // Sticky actions scroll detection
    if (elements.stickyActions && elements.step2) {
      window.addEventListener('scroll', debounce(() => {
        if (state.currentStep === 2) {
          const step2Rect = elements.step2.getBoundingClientRect();
          const isScrolled = step2Rect.bottom > window.innerHeight + 100;
          elements.stickyActions.classList.toggle('sfb-sticky-actions--visible', isScrolled);
        }
      }, 50));
    }

    // Step 3 buttons
    if (elements.openPdfBtn) {
      elements.openPdfBtn.addEventListener('click', openPDF);
    }

    if (elements.startOverBtn) {
      elements.startOverBtn.addEventListener('click', startOver);
    }

    // Sync header project name with review step
    if (elements.headerProject) {
      elements.headerProject.addEventListener('input', (e) => {
        state.projectName = e.target.value;
        if (elements.projectNameInput) {
          elements.projectNameInput.value = e.target.value;
        }
      });
    }

    if (elements.projectNameInput) {
      elements.projectNameInput.addEventListener('input', (e) => {
        state.projectName = e.target.value;
        if (elements.headerProject) {
          elements.headerProject.value = e.target.value;
        }
      });
    }

    if (elements.projectNotesInput) {
      elements.projectNotesInput.addEventListener('input', (e) => {
        state.projectNotes = e.target.value;
      });
    }
  }

  // ========== Load Products from Server ==========
  function loadProducts() {
    // AJAX call to load products
    fetch(elements.ajaxUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'sfb_list_products',
        nonce: elements.nonce
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const rawProducts = data.data.products || [];

        // Step 2: Normalize and deduplicate using composite_key
        normalizeProducts(rawProducts);

        // Expose catalog to Review step
        window.SFB = window.SFB || {};
        window.SFB.productsById = state.productsMap;
        window.sfbProductsMap = state.productsMap; // Alias for review.js compatibility

        renderCategories();
        renderProducts();
        // Restore selected products from sessionStorage
        restoreSelectedProducts();

        // Trigger event for review.js to re-render now that products are loaded
        window.dispatchEvent(new CustomEvent('sfb-products-loaded'));
      } else {
        showError('Failed to load products: ' + (data.data?.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error loading products:', error);
      showError('Failed to load products. Please refresh the page.');
    });
  }

  /**
   * Normalize products: deduplicate by composite_key and build indexes
   * @param {Array} rawProducts - Products from server with composite_key
   */
  function normalizeProducts(rawProducts) {
    state.products = rawProducts;
    state.productsMap.clear();
    state.byCategory.clear();
    state.categoryPositionMap.clear();
    state.categoryIdMap.clear();
    state.byTypeWithinCategory.clear();
    state.productOrderMap.clear();
    state.productPositionMap.clear();

    rawProducts.forEach(product => {
      const key = product.composite_key;

      if (!key) {
        console.warn('[SFB] Product missing composite_key:', product);
        return;
      }

      // Deduplicate: keep first occurrence (or use 'last' - be consistent)
      if (!state.productsMap.has(key)) {
        state.productsMap.set(key, product);

        // Build category index and track category positions
        const category = product.category || 'Uncategorized';
        if (!state.byCategory.has(category)) {
          state.byCategory.set(category, []);

          // Track category position by category_id
          if (product.category_id && !state.categoryPositionMap.has(product.category_id)) {
            state.categoryPositionMap.set(product.category_id, product.category_position || 99999);
            state.categoryIdMap.set(category, product.category_id);
          }
        }
        state.byCategory.get(category).push(key);

        // Build type-within-category index
        if (product.type_label) {
          const typeKey = `${category}:${product.type_label}`;
          if (!state.byTypeWithinCategory.has(typeKey)) {
            state.byTypeWithinCategory.set(typeKey, []);
          }
          state.byTypeWithinCategory.get(typeKey).push(key);
        }

        // Track product position by product_id (the actual product node's database position)
        const productLabel = product.product_label || 'Uncategorized';
        const productKey = `${category}::${productLabel}`;

        if (product.product_id && !state.productPositionMap.has(product.product_id)) {
          state.productPositionMap.set(product.product_id, product.product_position || 99999);
        }

        // Map composite key to product_id for later lookup
        if (!state.productOrderMap.has(productKey) && product.product_id) {
          state.productOrderMap.set(productKey, product.product_id);
        }
      }
    });
  }

  // ========== Render Categories ==========
  function renderCategories() {
    if (!elements.categoryList) return;

    // Sort categories by their actual database position (via category_id lookup)
    const categories = Array.from(state.byCategory.keys()).sort((a, b) => {
      const categoryIdA = state.categoryIdMap.get(a) || 0;
      const categoryIdB = state.categoryIdMap.get(b) || 0;
      const positionA = state.categoryPositionMap.get(categoryIdA) ?? 99999;
      const positionB = state.categoryPositionMap.get(categoryIdB) ?? 99999;
      return positionA - positionB;
    });

    if (categories.length === 0) {
      elements.categoryList.innerHTML = '<p class="sfb-hint">No categories available</p>';
      return;
    }

    const html = categories.map(cat => {
      // Get count for this category
      const count = state.byCategory.get(cat)?.length || 0;

      return `
        <div class="sfb-category-item ${state.activeCategory === cat ? 'sfb-category-item-active' : ''}"
             data-category="${escapeHtml(cat)}"
             role="button"
             tabindex="0">
          ${escapeHtml(cat)} <span class="sfb-category-count">(${count})</span>
        </div>
      `;
    }).join('');

    elements.categoryList.innerHTML = html;

    // Attach category click handlers
    elements.categoryList.querySelectorAll('.sfb-category-item').forEach(item => {
      item.addEventListener('click', () => {
        const category = item.dataset.category;
        state.activeCategory = state.activeCategory === category ? null : category;
        state.activeType = null; // Clear type filter when changing category
        renderCategories();
        renderProducts();
      });
    });
  }

  // ========== Build Configurable Fields (Dropdowns) ==========
  function buildConfigurableFields(product) {
    // Check if product has field_configs with select-type fields
    if (!product.field_configs || !Array.isArray(product.field_configs)) {
      return ''; // No configurable fields
    }

    // Filter to only select-type fields
    const selectFields = product.field_configs.filter(fc => fc.type === 'select' && fc.options && fc.options.length > 0);

    if (selectFields.length === 0) {
      return ''; // No dropdown fields
    }

    // Get currently selected values for this product
    const selectedValues = state.selectedFieldValues.get(product.composite_key) || {};

    // Build dropdown HTML for each select field
    const dropdownsHtml = selectFields.map(fieldConfig => {
      const currentValue = selectedValues[fieldConfig.name] || product.specs[fieldConfig.name] || '';

      return `
        <div class="sfb-config-field">
          <label class="sfb-config-label">${escapeHtml(fieldConfig.name)}:</label>
          <select
            class="sfb-config-select"
            data-composite-key="${escapeHtml(product.composite_key)}"
            data-field-name="${escapeHtml(fieldConfig.name)}"
            onclick="event.stopPropagation();">
            <option value="">Choose ${escapeHtml(fieldConfig.name)}...</option>
            ${fieldConfig.options.map(opt =>
              `<option value="${escapeHtml(opt)}" ${currentValue === opt ? 'selected' : ''}>${escapeHtml(opt)}</option>`
            ).join('')}
          </select>
        </div>
      `;
    }).join('');

    return `<div class="sfb-config-fields">${dropdownsHtml}</div>`;
  }

  // ========== Render Products ==========
  function renderProducts() {
    if (!elements.productsGrid) return;

    // Get filtered composite keys
    let filteredKeys = [];

    // Start with all products or category filter
    if (state.activeCategory) {
      filteredKeys = state.byCategory.get(state.activeCategory) || [];
    } else {
      filteredKeys = Array.from(state.productsMap.keys());
    }

    // Filter by type (if active)
    if (state.activeType && state.activeCategory) {
      const typeKey = `${state.activeCategory}:${state.activeType}`;
      filteredKeys = state.byTypeWithinCategory.get(typeKey) || [];
    }

    // Filter by search using search_tokens
    if (state.searchQuery) {
      const query = state.searchQuery.toLowerCase().trim();
      filteredKeys = filteredKeys.filter(key => {
        const product = state.productsMap.get(key);
        if (!product || !product.search_tokens) return false;

        const tokens = product.search_tokens.toLowerCase();

        // Split search tokens into words (including hyphenated parts)
        // e.g., "cf-18 1.5hp 208v #3" becomes ["cf", "18", "1", "5hp", "208v", "3"]
        const tokenWords = tokens.split(/[\s\-#.]+/).filter(w => w.length > 0);

        // Split query by same delimiters to handle "CF-18" input
        // e.g., "cf-18" becomes ["cf", "18"]
        const queryWords = query.split(/[\s\-#.]+/).filter(w => w.length > 0);

        // Match if ALL query words are found as substrings in any token word
        return queryWords.every(queryWord => {
          return tokenWords.some(tokenWord => tokenWord.includes(queryWord));
        });
      });
    }

    // Map keys to products
    const filtered = filteredKeys.map(key => state.productsMap.get(key)).filter(Boolean);

    // Update results count
    if (elements.resultsCount) {
      elements.resultsCount.textContent = `${filtered.length} product${filtered.length !== 1 ? 's' : ''}`;
    }

    // Show/hide empty state
    if (filtered.length === 0) {
      elements.productsGrid.style.display = 'none';
      if (elements.productsEmpty) {
        elements.productsEmpty.style.display = 'block';
      }
      return;
    }

    elements.productsGrid.style.display = 'block';
    if (elements.productsEmpty) {
      elements.productsEmpty.style.display = 'none';
    }

    // Group models by category::product_label composite key (preserving global order from API)
    const groupedByProduct = {};
    const productDisplayNames = {}; // Map composite key -> display name
    filtered.forEach(model => {
      const category = model.category || 'Uncategorized';
      const productLabel = model.product_label || 'Uncategorized';
      const compositeKey = `${category}::${productLabel}`;

      if (!groupedByProduct[compositeKey]) {
        groupedByProduct[compositeKey] = [];
        productDisplayNames[compositeKey] = productLabel; // Store display name
      }
      groupedByProduct[compositeKey].push(model);
    });

    // Sort products by their actual database position (via product_id lookup)
    const productOrder = Object.keys(groupedByProduct).sort((a, b) => {
      const productIdA = state.productOrderMap.get(a) || 0;
      const productIdB = state.productOrderMap.get(b) || 0;
      const positionA = state.productPositionMap.get(productIdA) ?? 99999;
      const positionB = state.productPositionMap.get(productIdB) ?? 99999;
      return positionA - positionB;
    });

    // Render product groups with headers (sorted by product position)
    const html = productOrder.map(compositeKey => {
      const models = groupedByProduct[compositeKey];
      const productLabel = productDisplayNames[compositeKey];
      // Skip empty groups (shouldn't happen, but safety check)
      if (models.length === 0) return '';

      // Extract category from the first model (all models in group share same category)
      const category = models[0]?.category || 'Uncategorized';

      // Check if this is a "leaf" product (single model with no meaningful hierarchy)
      // A leaf product has only 1 model and the product_label matches (or is very similar to) the model name
      const isLeafProduct = models.length === 1 &&
        (models[0].model === productLabel ||
         models[0].product_label === models[0].model ||
         !models[0].type_label ||
         models[0].type_label === productLabel);

      // If this is a leaf product, render it as a simple card without the product-group wrapper
      if (isLeafProduct) {
        const product = models[0];
        const isSelected = state.selected.has(product.composite_key);

        // Format specs inline
        let specsHtml = '';
        let specs = product.specs;
        let hasSpecs = false;

        if (specs) {
          if (Array.isArray(specs)) {
            hasSpecs = specs.length > 0;
          } else if (typeof specs === 'object') {
            hasSpecs = Object.keys(specs).length > 0;
          }
        }

        if (hasSpecs) {
          const lines = [];

          if (!Array.isArray(specs)) {
            // Line 1: Size and Thickness/Gauge
            const line1Parts = [];
            if (specs.Size || specs.size) {
              const size = specs.Size || specs.size;
              line1Parts.push(`Size: ${escapeHtml(size)}`);
            }
            if (specs.Thickness || specs.thickness || specs['Gauge/Thickness'] || specs.Gauge || specs.gauge) {
              const thickness = specs.Thickness || specs.thickness || specs['Gauge/Thickness'] || specs.Gauge || specs.gauge;
              line1Parts.push(`Thick: ${escapeHtml(thickness)}`);
            }
            if (line1Parts.length > 0) {
              lines.push(line1Parts.join(' · '));
            }

            // Line 2: KSI and/or Flange
            const line2Parts = [];
            if (specs.KSI || specs.ksi) {
              const ksi = specs.KSI || specs.ksi;
              line2Parts.push(`KSI: ${escapeHtml(ksi)}`);
            }
            if (specs.Flange || specs.flange) {
              const flange = specs.Flange || specs.flange;
              line2Parts.push(`Flange: ${escapeHtml(flange)}`);
            }
            if (line2Parts.length > 0) {
              lines.push(line2Parts.join(' · '));
            }

            // Fallback: if no specific fields found, show first 2-3 specs
            if (lines.length === 0) {
              const specEntries = Object.entries(specs).slice(0, 3);
              const line1 = specEntries.slice(0, 2).map(([k, v]) => `${escapeHtml(k)}: ${escapeHtml(v)}`).join(' · ');
              const line2 = specEntries.length > 2 ? `${escapeHtml(specEntries[2][0])}: ${escapeHtml(specEntries[2][1])}` : '';
              if (line1) lines.push(line1);
              if (line2) lines.push(line2);
            }
          }

          if (lines.length > 0) {
            specsHtml = `
              <div class="sfb-card-specs">
                ${lines.map(line => `<div>${line}</div>`).join('')}
              </div>
            `;
          }
        }

        // Build card head with category crumb
        let cardHead = '';
        if (product.category) {
          cardHead = `<div class="sfb-card__head"><span class="crumb--category">${escapeHtml(product.category)}</span></div>`;
        }

        // Return leaf product as a simple card (no product-group wrapper)
        return `
          <div class="sfb-product-card sfb-leaf-product ${isSelected ? 'sfb-product-card-selected' : ''}"
               data-composite-key="${escapeHtml(product.composite_key)}"
               role="button"
               tabindex="0"
               aria-pressed="${isSelected ? 'true' : 'false'}"
               aria-label="${escapeHtml(product.model)} - ${isSelected ? 'Selected' : 'Not selected'}. ${product.category ? 'Category: ' + escapeHtml(product.category) + '.' : ''} Press Enter or Space to ${isSelected ? 'remove' : 'add'}.">
            <button class="sfb-sr-only sfb-card__toggle" aria-pressed="${isSelected ? 'true' : 'false'}">
              Toggle selection for ${escapeHtml(product.model)}
            </button>
            <div class="sfb-card__selected-indicator" aria-hidden="true">✓ ADDED</div>
            ${cardHead}
            <h4 class="sfb-product-name">${escapeHtml(product.model)}</h4>
            ${specsHtml}
            ${buildConfigurableFields(product)}
          </div>
        `;
      }

      // NEW: Group models by type_label within this product
      const groupedByType = {};
      models.forEach(model => {
        const typeLabel = model.type_label || 'Other';
        if (!groupedByType[typeLabel]) {
          groupedByType[typeLabel] = [];
        }
        groupedByType[typeLabel].push(model);
      });

      // Render type groups within this product
      const typeGroupsHtml = Object.entries(groupedByType).map(([typeLabel, typeModels]) => {
        const typeModelsHtml = typeModels.map(product => {
          const isSelected = state.selected.has(product.composite_key);

          // Format specs inline - 2 lines max with labels
          let specsHtml = '';

          // Handle both array and object specs formats
          let specs = product.specs;
          let hasSpecs = false;

          if (specs) {
            if (Array.isArray(specs)) {
              hasSpecs = specs.length > 0;
            } else if (typeof specs === 'object') {
              hasSpecs = Object.keys(specs).length > 0;
            }
          }

          if (hasSpecs) {
            const lines = [];

            // If specs is an object (expected format)
            if (!Array.isArray(specs)) {
              // Line 1: Size and Thickness/Gauge (if available)
              const line1Parts = [];
              if (specs.Size || specs.size) {
                const size = specs.Size || specs.size;
                line1Parts.push(`Size: ${escapeHtml(size)}`);
              }
              if (specs.Thickness || specs.thickness || specs['Gauge/Thickness'] || specs.Gauge || specs.gauge) {
                const thickness = specs.Thickness || specs.thickness || specs['Gauge/Thickness'] || specs.Gauge || specs.gauge;
                line1Parts.push(`Thick: ${escapeHtml(thickness)}`);
              }
              if (line1Parts.length > 0) {
                lines.push(line1Parts.join(' · '));
              }

              // Line 2: KSI and/or Flange (if available)
              const line2Parts = [];
              if (specs.KSI || specs.ksi) {
                const ksi = specs.KSI || specs.ksi;
                line2Parts.push(`KSI: ${escapeHtml(ksi)}`);
              }
              if (specs.Flange || specs.flange) {
                const flange = specs.Flange || specs.flange;
                line2Parts.push(`Flange: ${escapeHtml(flange)}`);
              }
              if (line2Parts.length > 0) {
                lines.push(line2Parts.join(' · '));
              }

              // Fallback: if no specific fields found, show first 2-3 specs
              if (lines.length === 0) {
                const specEntries = Object.entries(specs).slice(0, 3);
                const line1 = specEntries.slice(0, 2).map(([k, v]) => `${escapeHtml(k)}: ${escapeHtml(v)}`).join(' · ');
                const line2 = specEntries.length > 2 ? `${escapeHtml(specEntries[2][0])}: ${escapeHtml(specEntries[2][1])}` : '';
                if (line1) lines.push(line1);
                if (line2) lines.push(line2);
              }
            }

            if (lines.length > 0) {
              specsHtml = `
                <div class="sfb-card-specs">
                  ${lines.map(line => `<div>${line}</div>`).join('')}
                </div>
              `;
            }
          }

          // Build card head - REMOVED TYPE BADGE, keep only category crumb
          let cardHead = '';
          if (product.category) {
            cardHead = `<div class="sfb-card__head"><span class="crumb--category">${escapeHtml(product.category)}</span></div>`;
          }

          return `
            <div class="sfb-product-card ${isSelected ? 'sfb-product-card-selected' : ''}"
                 data-composite-key="${escapeHtml(product.composite_key)}"
                 role="button"
                 tabindex="0"
                 aria-pressed="${isSelected ? 'true' : 'false'}"
                 aria-label="${escapeHtml(product.model)} - ${isSelected ? 'Selected' : 'Not selected'}. ${product.category ? 'Category: ' + escapeHtml(product.category) + '.' : ''} Press Enter or Space to ${isSelected ? 'remove' : 'add'}.">
              <button class="sfb-sr-only sfb-card__toggle" aria-pressed="${isSelected ? 'true' : 'false'}">
                Toggle selection for ${escapeHtml(product.model)}
              </button>
              <div class="sfb-card__selected-indicator" aria-hidden="true">✓ ADDED</div>
              ${cardHead}
              <h4 class="sfb-product-name">${escapeHtml(product.model)}</h4>
              ${specsHtml}
              ${buildConfigurableFields(product)}
            </div>
          `;
        }).join('');

        // Return type group with subheading
        return `
          <div class="sfb-type-group">
            <div class="sfb-type-subheading">
              <span class="sfb-type-subheading__label">${escapeHtml(typeLabel)}</span>
              <span class="sfb-type-subheading__count">${typeModels.length} model${typeModels.length !== 1 ? 's' : ''}</span>
            </div>
            <div class="sfb-type-models-grid">
              ${typeModelsHtml}
            </div>
          </div>
        `;
      }).join('');

      const groupKey = `${category}::${productLabel}`;

      // If first load with no saved state, collapse all groups by default
      if (state.defaultCollapsed && !state.collapsedGroups.has(groupKey)) {
        state.collapsedGroups.add(groupKey);
      }

      const isCollapsed = state.collapsedGroups.has(groupKey);

      return `
        <div class="sfb-product-group ${isCollapsed ? 'sfb-product-group--collapsed' : ''}" data-group-key="${escapeHtml(groupKey)}">
          <div class="sfb-product-header" role="button" tabindex="0" aria-expanded="${!isCollapsed}">
            <div class="sfb-product-header__left">
              <span class="sfb-product-header__toggle" aria-label="${isCollapsed ? 'Expand' : 'Collapse'} section">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <h3 class="sfb-product-header__title">${escapeHtml(productLabel)}</h3>
            </div>
            <span class="sfb-product-header__count">${models.length} model${models.length !== 1 ? 's' : ''}</span>
          </div>
          <div class="sfb-product-group__content">
            ${typeGroupsHtml}
          </div>
        </div>
      `;
    }).join('');

    elements.productsGrid.innerHTML = html;

    // If we just set default collapsed state, save it and clear the flag
    if (state.defaultCollapsed) {
      saveCollapseState();
      state.defaultCollapsed = false;
    }

    // Attach product card handlers (click-anywhere)
    attachCardHandlers();

    // Attach dropdown change handlers
    attachDropdownHandlers();

    // Attach collapse/expand handlers
    attachCollapseHandlers();
  }

  /**
   * Attach click and keyboard handlers to product cards
   */
  function attachCardHandlers() {
    const cards = elements.productsGrid.querySelectorAll('.sfb-product-card');

    cards.forEach(card => {
      // Click handler
      card.addEventListener('click', (e) => {
        e.preventDefault();
        const compositeKey = card.dataset.compositeKey;
        toggleByCard(compositeKey);
      });

      // Keyboard handler (Enter or Space)
      card.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const compositeKey = card.dataset.compositeKey;
          toggleByCard(compositeKey);
        }
      });
    });
  }

  /**
   * Attach change handlers to configuration dropdowns
   */
  function attachDropdownHandlers() {
    const dropdowns = elements.productsGrid.querySelectorAll('.sfb-config-select');

    dropdowns.forEach(dropdown => {
      dropdown.addEventListener('change', (e) => {
        const compositeKey = e.target.dataset.compositeKey;
        const fieldName = e.target.dataset.fieldName;
        const selectedValue = e.target.value;

        // Store the selected value
        const currentValues = state.selectedFieldValues.get(compositeKey) || {};
        currentValues[fieldName] = selectedValue;
        state.selectedFieldValues.set(compositeKey, currentValues);

        // Auto-select the product if a dropdown value is chosen
        if (selectedValue && !state.selected.has(compositeKey)) {
          toggleByCard(compositeKey);
        }
      });
    });
  }

  /**
   * Attach click and keyboard handlers to product group headers for collapse/expand
   */
  function attachCollapseHandlers() {
    const headers = elements.productsGrid.querySelectorAll('.sfb-product-header');

    headers.forEach(header => {
      // Click handler
      header.addEventListener('click', (e) => {
        // Don't collapse if clicking inside the content area (cards, dropdowns, etc.)
        e.stopPropagation();
        const group = header.closest('.sfb-product-group');
        toggleProductGroup(group);
      });

      // Keyboard handler (Enter or Space)
      header.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const group = header.closest('.sfb-product-group');
          toggleProductGroup(group);
        }
      });
    });
  }

  /**
   * Toggle collapse/expand state of a product group
   * @param {HTMLElement} group - The product group element
   */
  function toggleProductGroup(group) {
    if (!group) return;

    const groupKey = group.dataset.groupKey;
    const isCurrentlyCollapsed = state.collapsedGroups.has(groupKey);

    if (isCurrentlyCollapsed) {
      // Expand
      state.collapsedGroups.delete(groupKey);
      group.classList.remove('sfb-product-group--collapsed');
      group.querySelector('.sfb-product-header').setAttribute('aria-expanded', 'true');
    } else {
      // Collapse
      state.collapsedGroups.add(groupKey);
      group.classList.add('sfb-product-group--collapsed');
      group.querySelector('.sfb-product-header').setAttribute('aria-expanded', 'false');
    }

    // Persist to localStorage
    saveCollapseState();
  }

  /**
   * Toggle all product groups expand/collapse
   */
  function toggleAllGroups() {
    const allGroups = elements.productsGrid.querySelectorAll('.sfb-product-group');
    if (allGroups.length === 0) return;

    // Determine if we should expand or collapse all
    // If any group is collapsed, expand all. If all are expanded, collapse all.
    const anyCollapsed = Array.from(allGroups).some(group =>
      group.classList.contains('sfb-product-group--collapsed')
    );

    const shouldExpand = anyCollapsed;

    allGroups.forEach(group => {
      const groupKey = group.dataset.groupKey;
      const header = group.querySelector('.sfb-product-header');

      if (shouldExpand) {
        // Expand this group
        state.collapsedGroups.delete(groupKey);
        group.classList.remove('sfb-product-group--collapsed');
        if (header) header.setAttribute('aria-expanded', 'true');
      } else {
        // Collapse this group
        state.collapsedGroups.add(groupKey);
        group.classList.add('sfb-product-group--collapsed');
        if (header) header.setAttribute('aria-expanded', 'false');
      }
    });

    // Update button text and title
    if (elements.toggleAllText) {
      elements.toggleAllText.textContent = shouldExpand ? 'Collapse All' : 'Expand All';
    }
    if (elements.toggleAllGroupsBtn) {
      elements.toggleAllGroupsBtn.title = shouldExpand ? 'Collapse all product groups' : 'Expand all product groups';
      elements.toggleAllGroupsBtn.classList.toggle('sfb-toggle-all-btn--expanded', shouldExpand);
    }

    // Persist to localStorage
    saveCollapseState();
  }

  /**
   * Toggle product selection by card
   * @param {string} compositeKey - The composite key of the product
   */
  function toggleByCard(compositeKey) {
    if (state.selected.has(compositeKey)) {
      state.selected.delete(compositeKey);
      state.selectedProducts.delete(compositeKey);
    } else {
      const product = state.productsMap.get(compositeKey);
      if (product) {
        state.selected.add(compositeKey);
        state.selectedProducts.set(compositeKey, product);
      }
    }

    // Update card state immediately
    setCardSelected(compositeKey, state.selected.has(compositeKey));

    // Persist to localStorage
    saveStateToLocalStorage();

    // Update UI
    flushSelectedCounter();
    updateTray();
  }

  /**
   * Update a single card's selected state
   * @param {string} compositeKey - The composite key of the product
   * @param {boolean} isSelected - Whether the product is selected
   */
  function setCardSelected(compositeKey, isSelected) {
    const card = elements.productsGrid.querySelector(`[data-composite-key="${compositeKey}"]`);
    if (card) {
      card.classList.toggle('sfb-product-card-selected', isSelected);
      card.setAttribute('aria-pressed', isSelected ? 'true' : 'false');

      // Update SR button aria-pressed
      const srBtn = card.querySelector('.sfb-card__toggle');
      if (srBtn) {
        srBtn.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
      }

      // Update aria-label
      const product = state.productsMap.get(compositeKey);
      if (product) {
        const categoryText = product.category ? `Category: ${product.category}. ` : '';
        card.setAttribute('aria-label', `${product.model} - ${isSelected ? 'Selected' : 'Not selected'}. ${categoryText}Press Enter or Space to ${isSelected ? 'remove' : 'add'}.`);
      }
    }
  }

  /**
   * Update the sticky selection counter
   */
  function flushSelectedCounter() {
    const count = state.selected.size;

    if (elements.selectionCounter && elements.selectionCountNumber) {
      if (count > 0) {
        elements.selectionCounter.style.display = 'flex';
        elements.selectionCountNumber.textContent = count;
      } else {
        elements.selectionCounter.style.display = 'none';
      }
    }
  }

  // ========== Product Selection (Legacy compatibility) ==========
  function addProduct(compositeKey) {
    const product = state.productsMap.get(compositeKey);
    if (product) {
      state.selected.add(compositeKey);
      state.selectedProducts.set(compositeKey, product);
      saveStateToLocalStorage();
      updateUI();
      updateTray();
    }
  }

  function removeProduct(compositeKey) {
    state.selected.delete(compositeKey);
    state.selectedProducts.delete(compositeKey);
    saveStateToLocalStorage();
    updateUI();
    updateTray();
  }

  function removeProductFromTray(compositeKey) {
    removeProduct(compositeKey);
  }

  // ========== Search & Filters ==========
  function handleSearch(e) {
    state.searchQuery = e.target.value;
    renderProducts();
  }

  function clearFilters() {
    state.searchQuery = '';
    state.activeCategory = null;
    if (elements.searchInput) {
      elements.searchInput.value = '';
    }
    renderCategories();
    renderProducts();
  }

  function clearAllSelections() {
    if (state.selected.size > 0 && confirm('Clear all selected products?')) {
      state.selected.clear();
      state.selectedProducts.clear();
      localStorage.removeItem('sfb-selected');
      sessionStorage.removeItem('sfb_selected_products'); // Legacy cleanup
      updateUI();
    }
  }


  // ========== Step Navigation ==========
  function goToStep(step) {
    state.currentStep = step;

    // Hide all steps
    [elements.step1, elements.step2, elements.step3].forEach(el => {
      if (el) el.classList.remove('sfb-step-active');
    });

    // Show active step
    const activeStep = document.getElementById(`sfb-step-${step}`);
    if (activeStep) {
      activeStep.classList.add('sfb-step-active');
    }

    // Update progress pills
    elements.pills.forEach((pill, index) => {
      const pillStep = index + 1;
      pill.classList.toggle('sfb-pill-active', pillStep === step);
      pill.classList.toggle('sfb-pill-complete', pillStep < step);
    });

    // Step-specific actions
    if (step === 2) {
      // Let review.js handle rendering (it auto-initializes)
      // Don't call renderReview() - review.js will handle it
    }

    // Update tray button text and visibility based on step
    updateTrayForStep(step);

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // ========== Step 2: Render Review ==========
  function renderReview() {
    if (!elements.reviewProductsList) return;

    const products = Array.from(state.selectedProducts.values());
    const reviewEmpty = document.getElementById('sfb-review-empty');

    // Update count
    if (elements.reviewCount) {
      elements.reviewCount.textContent = `(${products.length})`;
    }

    // Show empty state if no products
    if (products.length === 0) {
      elements.reviewProductsList.style.display = 'none';
      if (reviewEmpty) {
        reviewEmpty.style.display = 'block';
      }
      return;
    }

    elements.reviewProductsList.style.display = 'block';
    if (reviewEmpty) {
      reviewEmpty.style.display = 'none';
    }

    // Group products by category
    const groupedProducts = {};
    products.forEach(product => {
      const category = product.category || 'Uncategorized';
      if (!groupedProducts[category]) {
        groupedProducts[category] = [];
      }
      groupedProducts[category].push(product);
    });

    // Render grouped review items with drag handles
    const html = Object.entries(groupedProducts).map(([category, categoryProducts]) => {
      const itemsHtml = categoryProducts.map((product, index) => {
        // Format specs inline - same as product cards
        let specsLine = '';
        if (product.specs && typeof product.specs === 'object') {
          const parts = [];
          if (product.specs.Size || product.specs.size) {
            parts.push(`Size: ${escapeHtml(product.specs.Size || product.specs.size)}`);
          }
          if (product.specs.Thickness || product.specs.thickness) {
            parts.push(`Thick: ${escapeHtml(product.specs.Thickness || product.specs.thickness)}`);
          }
          if (product.specs.Flange || product.specs.flange) {
            parts.push(`Flange: ${escapeHtml(product.specs.Flange || product.specs.flange)}`);
          }
          if (parts.length === 0 && Object.keys(product.specs).length > 0) {
            parts.push(Object.entries(product.specs).slice(0, 3).map(([k, v]) => `${k}: ${v}`).join(' · '));
          }
          specsLine = parts.join(' · ');
        }

        // Build lineage for review item
        let lineageText = '';
        if (product.type_label) {
          lineageText = `<span class="sfb-review-lineage">${escapeHtml(product.type_label)}</span>`;
        }

        return `
          <div class="sfb-review-item" data-composite-key="${escapeHtml(product.composite_key)}" draggable="true">
            <div class="sfb-review-drag-handle" title="Drag to reorder">
              <span>⋮</span>
            </div>
            <div class="sfb-review-item-content">
              <div class="sfb-review-item-name">${escapeHtml(product.model)} ${lineageText}</div>
              ${specsLine ? `<div class="sfb-review-item-specs">${specsLine}</div>` : ''}
            </div>
            <button class="sfb-review-remove-btn" data-composite-key="${escapeHtml(product.composite_key)}" aria-label="Remove ${escapeHtml(product.model)}" title="Remove">
              ×
            </button>
          </div>
        `;
      }).join('');

      return `
        <div class="sfb-review-group">
          <div class="sfb-review-group__title">${escapeHtml(category)}</div>
          <div class="sfb-review-list">${itemsHtml}</div>
        </div>
      `;
    }).join('');

    elements.reviewProductsList.innerHTML = html;

    // Attach remove handlers
    elements.reviewProductsList.querySelectorAll('.sfb-review-remove-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const compositeKey = btn.dataset.compositeKey;
        removeProduct(compositeKey);
        renderReview();
      });
    });

    // Attach drag-and-drop handlers
    initDragAndDrop();

    // Attach keyboard shortcuts
    initKeyboardReorder();
  }

  // ========== Drag and Drop Reordering ==========
  let draggedItem = null;
  let draggedCompositeKey = null;

  function initDragAndDrop() {
    const items = elements.reviewProductsList.querySelectorAll('.sfb-review-item');

    items.forEach(item => {
      // Drag start
      item.addEventListener('dragstart', (e) => {
        draggedItem = item;
        draggedCompositeKey = item.dataset.compositeKey;
        item.classList.add('sfb-dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', item.innerHTML);
      });

      // Drag end
      item.addEventListener('dragend', (e) => {
        item.classList.remove('sfb-dragging');
        draggedItem = null;
        draggedCompositeKey = null;
      });

      // Drag over
      item.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';

        if (draggedItem && draggedItem !== item) {
          const rect = item.getBoundingClientRect();
          const midpoint = rect.top + rect.height / 2;

          if (e.clientY < midpoint) {
            item.parentNode.insertBefore(draggedItem, item);
          } else {
            item.parentNode.insertBefore(draggedItem, item.nextSibling);
          }
        }
      });

      // Drop
      item.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();

        // Update state order based on new DOM order
        updateProductOrder();
      });
    });
  }

  // ========== Keyboard Reordering (Alt+↑/↓) ==========
  function initKeyboardReorder() {
    const items = elements.reviewProductsList.querySelectorAll('.sfb-review-item');

    items.forEach((item, index) => {
      item.setAttribute('tabindex', '0');

      item.addEventListener('keydown', (e) => {
        // Alt+ArrowUp: Move up
        if (e.altKey && e.key === 'ArrowUp') {
          e.preventDefault();
          const prev = item.previousElementSibling;
          if (prev && prev.classList.contains('sfb-review-item')) {
            item.parentNode.insertBefore(item, prev);
            item.focus();
            updateProductOrder();
          }
        }

        // Alt+ArrowDown: Move down
        if (e.altKey && e.key === 'ArrowDown') {
          e.preventDefault();
          const next = item.nextElementSibling;
          if (next && next.classList.contains('sfb-review-item')) {
            item.parentNode.insertBefore(next, item);
            item.focus();
            updateProductOrder();
          }
        }
      });
    });
  }

  // Update product order in state based on DOM order
  function updateProductOrder() {
    const items = elements.reviewProductsList.querySelectorAll('.sfb-review-item');
    const newOrder = new Map();

    items.forEach(item => {
      const compositeKey = item.dataset.compositeKey;
      const product = state.selectedProducts.get(compositeKey);
      if (product) {
        newOrder.set(compositeKey, product);
      }
    });

    state.selectedProducts = newOrder;
    saveState();
  }

  // ========== Generate PDF ==========
  async function handleGeneratePDF(skipLeadCapture = false) {
    if (state.selected.size === 0) {
      alert('Please select at least one product.');
      return;
    }

    // Check if lead capture is enabled (Pro feature)
    const leadCaptureEnabled = elements.app?.dataset.leadCapture === '1';

    if (leadCaptureEnabled && !skipLeadCapture && typeof window.SFB_LeadCapture !== 'undefined') {
      // Prepare PDF data to pass to modal
      const pdfData = {
        projectName: state.projectName || '',
        products: Array.from(state.selectedProducts.values())
      };

      // Open lead capture modal
      window.SFB_LeadCapture.openModal(pdfData);
      return; // Stop here - will continue after lead submission
    }

    // Show loading overlay
    if (elements.loadingOverlay) {
      elements.loadingOverlay.style.display = 'flex';
    }

    try {
      // Collect review payload if available (quantities, notes, overrides)
      let review = null;
      if (typeof window.SFB_collectReviewPayload === 'function') {
        review = window.SFB_collectReviewPayload(state.projectName || '');
      }

      // Convert selectedFieldValues Map to plain object for JSON
      const fieldValuesObj = {};
      state.selectedFieldValues.forEach((values, compositeKey) => {
        fieldValuesObj[compositeKey] = values;
      });

      // Build REST API payload
      const payload = {
        review: review || {
          project: {
            name: state.projectName || '',
            notes: state.projectNotes || ''
          },
          products: Array.from(state.selectedProducts.values()).map(p => ({
            id: p.id || p.node_id,
            node_id: p.node_id || p.id,
            quantity: 1,
            note: ''
          }))
        },
        selected_field_values: fieldValuesObj,
        nonce: elements.nonce // Add nonce to payload for backend verification
      };

      // Generate PDF via REST API
      // restUrl already includes '/wp-json/sfb/v1/' so just append 'generate'
      const restUrl = elements.restUrl + 'generate';

      const response = await fetch(restUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
          // Note: X-WP-Nonce intentionally omitted for public endpoint
          // The endpoint uses permission_callback: __return_true and handles
          // its own nonce verification inside the handler
        },
        credentials: 'same-origin', // CRITICAL: Send cookies so WordPress can verify nonce
        body: JSON.stringify(payload)
      });

      // Parse JSON response
      let data;
      try {
        data = await response.json();
      } catch (parseError) {
        console.error('[SFB] Failed to parse JSON response:', parseError);
        throw new Error('Server returned an invalid response. Check the browser console for details.');
      }

      // Hide loading
      if (elements.loadingOverlay) {
        elements.loadingOverlay.style.display = 'none';
      }

      // Check for success (REST format: {ok: true, url})
      if (!response.ok) {
        // Handle REST API error format
        const errorMessage = data?.message || data?.code || `Server error: ${response.status}`;
        throw new Error(errorMessage);
      }

      if (data.ok && data.url) {
        state.pdfUrl = data.url;

        goToStep(3);
      } else {
        throw new Error(data?.message || 'PDF generation failed - no URL returned');
      }

    } catch (error) {
      console.error('[SFB] Error generating PDF:', error);

      // Hide loading
      if (elements.loadingOverlay) {
        elements.loadingOverlay.style.display = 'none';
      }

      // Show user-friendly error
      showError('Failed to generate PDF: ' + error.message);
    }
  }

  // ========== Open PDF ==========
  function openPDF(e) {
    if (e) e.preventDefault();
    if (state.pdfUrl) {
      window.open(state.pdfUrl, '_blank', 'noopener,noreferrer');
    }
  }

  // ========== Start Over ==========
  function startOver() {
    if (confirm('Are you sure you want to start over? This will clear your selections.')) {
      state.selected.clear();
      state.selectedProducts.clear();
      state.projectName = '';
      state.projectNotes = '';
      state.pdfUrl = null;
      localStorage.removeItem('sfb-selected');
      sessionStorage.removeItem('sfb_selected_products'); // Legacy cleanup
      if (elements.headerProject) elements.headerProject.value = '';
      if (elements.projectNameInput) elements.projectNameInput.value = '';
      if (elements.projectNotesInput) elements.projectNotesInput.value = '';
      goToStep(1);
      updateUI();
    }
  }

  // ========== Update Tray for Current Step ==========
  function updateTrayForStep(step) {
    if (!elements.tray || !elements.trayContinueBtn) return;

    const count = state.selected.size;

    // Update button text based on step
    if (step === 1) {
      // Step 1: "Continue to Review"
      elements.trayContinueBtn.innerHTML = 'Continue to Review <span class="sfb-icon-arrow">→</span>';
      // Show tray if items selected
      elements.tray.classList.toggle('sfb-tray-visible', count > 0);
    } else if (step === 2) {
      // Step 2: "Generate PDF"
      elements.trayContinueBtn.innerHTML = 'Generate PDF <span class="sfb-icon-arrow">→</span>';
      // Keep tray visible on step 2
      elements.tray.classList.toggle('sfb-tray-visible', count > 0);
    } else {
      // Step 3: Hide tray
      elements.tray.classList.remove('sfb-tray-visible');
    }
  }

  // ========== Update Tray ==========
  function updateTray() {
    const count = state.selectedProducts.size;
    const products = Array.from(state.selectedProducts.values());

    // Update count header
    if (elements.trayCountHeader) {
      elements.trayCountHeader.textContent = `(${count})`;
    }

    // Enable/disable continue button
    if (elements.trayContinueBtn) {
      elements.trayContinueBtn.disabled = count === 0;
    }

    // Update button text and visibility for current step
    updateTrayForStep(state.currentStep);

    // Update products list
    if (elements.trayProductsList) {
      if (count === 0) {
        elements.trayProductsList.innerHTML = `
          <div class="sfb-tray-empty">
            <p>No products selected yet</p>
          </div>
        `;
      } else {
        // Group products by category, then optionally by subtype if present
        const groupedProducts = {};
        products.forEach(product => {
          const category = product.category || 'Uncategorized';
          const subtype = product.subtype_label || '';

          // Create category group if it doesn't exist
          if (!groupedProducts[category]) {
            groupedProducts[category] = {};
          }

          // Group by subtype within category (or use empty string for no subtype)
          if (!groupedProducts[category][subtype]) {
            groupedProducts[category][subtype] = [];
          }

          groupedProducts[category][subtype].push(product);
        });

        // Render groups (category → subtypes → products)
        const html = Object.entries(groupedProducts).map(([category, subtypeGroups]) => {
          // Render subtype groups within category
          const subtypeHtml = Object.entries(subtypeGroups).map(([subtype, subtypeProducts]) => {
            const productsHtml = subtypeProducts.map(product => {
              // Get 1-line spec summary
              const specs = product.specs ?
                Object.entries(product.specs).slice(0, 2)
                  .map(([k, v]) => v)
                  .join(' · ') : '';

              return `
                <div class="sfb-tray-product-item" data-composite-key="${escapeHtml(product.composite_key)}" role="listitem">
                  <div class="sfb-tray-product-info">
                    <div class="sfb-tray-product-name">${escapeHtml(product.model)}</div>
                    ${specs ? `<div class="sfb-tray-product-specs">${escapeHtml(specs)}</div>` : ''}
                  </div>
                  <button class="sfb-tray-remove-btn" data-composite-key="${escapeHtml(product.composite_key)}" aria-label="Remove ${escapeHtml(product.model)}">
                    ×
                  </button>
                </div>
              `;
            }).join('');

            // If there's a subtype, show it as a subheading
            return subtype ? `
              <div class="sfb-tray-subgroup">
                <h4 class="sfb-tray-subgroup__title">${escapeHtml(subtype)}</h4>
                <div class="sfb-tray-subgroup__list" role="list">${productsHtml}</div>
              </div>
            ` : `
              <div class="sfb-tray-group__list" role="list">${productsHtml}</div>
            `;
          }).join('');

          return `
            <div class="sfb-tray-group">
              <h3 class="sfb-tray-group__title" role="heading" aria-level="3">${escapeHtml(category)}</h3>
              ${subtypeHtml}
            </div>
          `;
        }).join('');

        elements.trayProductsList.innerHTML = html;

        // Attach remove handlers
        elements.trayProductsList.querySelectorAll('.sfb-tray-remove-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const compositeKey = btn.dataset.compositeKey;
            removeProductFromTray(compositeKey);
          });
        });
      }
    }
  }

  function toggleTray() {
    if (elements.tray) {
      const isCollapsed = elements.tray.classList.toggle('sfb-tray--collapsed');

      // Update ARIA attributes for accessibility
      if (elements.trayToggleBtn) {
        elements.trayToggleBtn.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
        elements.trayToggleBtn.setAttribute('aria-label', isCollapsed ?
          'Open selection tray' :
          'Close selection tray'
        );
      }

      // Persist collapsed state to localStorage (per site, not per project)
      try {
        localStorage.setItem('sfb_tray_collapsed', isCollapsed ? '1' : '0');
      } catch (e) {
        console.warn('Failed to save tray state:', e);
      }
    }
  }

  // ========== Update UI ==========
  function updateUI() {
    // Re-render products to show selected state
    renderProducts();

    // Update tray
    updateTray();

    // Update selection counter
    updateSelectionCounter();
  }

  // ========== Update Selection Counter (Legacy wrapper) ==========
  function updateSelectionCounter() {
    flushSelectedCounter();
  }

  // ========== LocalStorage Persistence ==========
  function saveStateToLocalStorage() {
    try {
      const selectedKeys = Array.from(state.selected);
      localStorage.setItem('sfb-selected', JSON.stringify(selectedKeys));
      // Also save to sessionStorage for backwards compatibility
      sessionStorage.setItem('sfb_selected_products', JSON.stringify(selectedKeys));
    } catch (e) {
      console.warn('Failed to save state to localStorage:', e);
    }
  }

  // Legacy function for backwards compatibility
  function saveState() {
    saveStateToLocalStorage();
  }

  function restoreState() {
    try {
      // Restore selected products from localStorage (primary) or sessionStorage (fallback)
      let savedKeys = localStorage.getItem('sfb-selected');
      if (!savedKeys) {
        savedKeys = sessionStorage.getItem('sfb_selected_products'); // Legacy fallback
      }

      if (savedKeys) {
        const keys = JSON.parse(savedKeys);
        // Note: we'll restore the actual products after they're loaded
        state.pendingRestoreKeys = keys;
      }

      // Restore tray collapsed state from localStorage
      const trayCollapsed = localStorage.getItem('sfb_tray_collapsed');
      if (trayCollapsed === '1' && elements.tray) {
        elements.tray.classList.add('sfb-tray--collapsed');
      }

      // Restore collapsed product groups from localStorage
      const collapsedGroups = localStorage.getItem('sfb_collapsed_groups');
      if (collapsedGroups) {
        const groups = JSON.parse(collapsedGroups);
        state.collapsedGroups = new Set(groups);
      } else {
        // Default: all groups start collapsed (will be populated after products load)
        state.defaultCollapsed = true;
      }
    } catch (e) {
      console.warn('Failed to restore state from localStorage/sessionStorage:', e);
    }
  }

  /**
   * Save collapsed groups state to localStorage
   */
  function saveCollapseState() {
    try {
      const groups = Array.from(state.collapsedGroups);
      localStorage.setItem('sfb_collapsed_groups', JSON.stringify(groups));
    } catch (e) {
      console.warn('Failed to save collapse state to localStorage:', e);
    }
  }

  function restoreSelectedProducts() {
    if (state.pendingRestoreKeys && state.pendingRestoreKeys.length > 0) {
      state.pendingRestoreKeys.forEach(key => {
        const product = state.productsMap.get(key);
        if (product) {
          state.selected.add(key);
          state.selectedProducts.set(key, product);
        }
      });
      state.pendingRestoreKeys = null;
      updateUI();
    }
  }

  // ========== Utilities ==========
  function debounce(func, wait) {
    let timeout;
    return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), wait);
    };
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function showError(message) {
    alert(message); // Simple for MVP; can be replaced with toast notification
  }

  // ========== Initialize on DOM Ready ==========
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Expose state and productsMap for review.js
  window.sfbState = state;
  window.sfbProductsMap = state.productsMap;

  // Expose API for lead capture integration (Pro feature)
  window.SFB = window.SFB || {};
  window.SFB.continueWithPdfGeneration = async function() {
    // This is called after lead capture is submitted
    // Continue with the PDF generation that was interrupted
    try {
      // Note: lead submission already happened in lead-capture.js
      // Just proceed with the actual PDF generation, skipping lead capture this time
      await handleGeneratePDF(true); // skipLeadCapture = true
    } catch (error) {
      console.error('[SFB] Error continuing with PDF generation:', error);
    }
  };

})();
