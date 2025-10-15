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
    byTypeWithinCategory: new Map(), // Map<'category:type', composite_key[]> - type index
    selected: new Set(), // Set of composite_keys for selected products (localStorage persistence)
    selectedProducts: new Map(), // Map<composite_key, product> - selected using composite keys (for compatibility)
    activeCategory: null,
    activeType: null, // Track active type filter
    searchQuery: '',
    projectName: '',
    projectNotes: '',
    pdfUrl: null
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
      viewBtns: document.querySelectorAll('.sfb-view-btn'),
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

    // View toggle
    elements.viewBtns.forEach(btn => {
      btn.addEventListener('click', () => toggleView(btn.dataset.view));
    });

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

    // Click outside tray to close (like a photo gallery)
    document.addEventListener('click', (e) => {
      if (elements.tray &&
          !elements.tray.classList.contains('sfb-tray--collapsed') &&
          elements.tray.classList.contains('sfb-tray-visible')) {
        // Check if click is outside the tray
        if (!elements.tray.contains(e.target)) {
          toggleTray();
        }
      }
    });

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
    state.byTypeWithinCategory.clear();

    rawProducts.forEach(product => {
      const key = product.composite_key;

      if (!key) {
        console.warn('[SFB] Product missing composite_key:', product);
        return;
      }

      // Deduplicate: keep first occurrence (or use 'last' - be consistent)
      if (!state.productsMap.has(key)) {
        state.productsMap.set(key, product);

        // Build category index
        const category = product.category || 'Uncategorized';
        if (!state.byCategory.has(category)) {
          state.byCategory.set(category, []);
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
      }
    });
  }

  // ========== Render Categories ==========
  function renderCategories() {
    if (!elements.categoryList) return;

    const categories = Array.from(state.byCategory.keys());

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

    elements.productsGrid.style.display = 'grid';
    if (elements.productsEmpty) {
      elements.productsEmpty.style.display = 'none';
    }

    // Render product cards
    const html = filtered.map(product => {
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

      // Build card head with new badge strategy: Type = chip, Category = crumb
      let cardHead = '';
      if (product.type_label || product.category) {
        const badges = [];
        if (product.type_label) {
          badges.push(`<span class="badge--type">${escapeHtml(product.type_label)}</span>`);
        }
        if (product.category) {
          badges.push(`<span class="crumb--category">${escapeHtml(product.category)}</span>`);
        }
        cardHead = `<div class="sfb-card__head">${badges.join('')}</div>`;
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
        </div>
      `;
    }).join('');

    elements.productsGrid.innerHTML = html;

    // Attach product card handlers (click-anywhere)
    attachCardHandlers();
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

  function toggleView(view) {
    elements.viewBtns.forEach(btn => {
      btn.classList.toggle('active', btn.dataset.view === view);
    });
    if (elements.productsGrid) {
      elements.productsGrid.dataset.view = view;
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

    // Debug logging
    console.log('[SFB] Lead capture check:', {
      leadCaptureEnabled,
      skipLeadCapture,
      hasLeadCaptureScript: typeof window.SFB_LeadCapture !== 'undefined',
      dataAttribute: elements.app?.dataset.leadCapture
    });

    if (leadCaptureEnabled && !skipLeadCapture && typeof window.SFB_LeadCapture !== 'undefined') {
      console.log('[SFB] Opening lead capture modal');
      // Prepare PDF data to pass to modal
      const pdfData = {
        projectName: state.projectName || '',
        products: Array.from(state.selectedProducts.values())
      };

      // Open lead capture modal
      window.SFB_LeadCapture.openModal(pdfData);
      return; // Stop here - will continue after lead submission
    } else {
      console.log('[SFB] Skipping lead capture - proceeding directly to PDF generation');
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

      const formData = new FormData();
      formData.append('action', 'sfb_generate_frontend_pdf');
      formData.append('nonce', elements.nonce);

      if (review) {
        // Use review payload (includes quantities, notes, overrides)
        formData.append('review', JSON.stringify(review));
      } else {
        // Fallback to legacy format
        const products = Array.from(state.selectedProducts.values());
        const productsData = products.map(p => ({ id: p.id, node_id: p.node_id || p.id }));
        formData.append('products', JSON.stringify(productsData));
        formData.append('project_name', state.projectName || '');
        formData.append('notes', state.projectNotes || '');
      }

      // Generate PDF via AJAX with robust error handling
      const response = await fetch(elements.ajaxUrl, {
        method: 'POST',
        body: formData
      });

      // Read response as text first
      const responseText = await response.text();

      // Try to parse as JSON
      let data;
      try {
        data = JSON.parse(responseText);
      } catch (parseError) {
        console.error('[SFB] Failed to parse JSON response:', parseError);
        console.error('[SFB] Full response body:', responseText);
        throw new Error('Server returned an error page. Check the browser console and wp-content/debug.log for details.');
      }

      // Hide loading
      if (elements.loadingOverlay) {
        elements.loadingOverlay.style.display = 'none';
      }

      // Check for success
      if (!response.ok) {
        throw new Error(data?.data?.message || `Server error: ${response.status}`);
      }

      if (data.success && data.data && data.data.url) {
        state.pdfUrl = data.data.url;
        goToStep(3);
      } else {
        throw new Error(data?.data?.message || 'PDF generation failed - no URL returned');
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
        // Group products by category
        const groupedProducts = {};
        products.forEach(product => {
          const category = product.category || 'Uncategorized';
          if (!groupedProducts[category]) {
            groupedProducts[category] = [];
          }
          groupedProducts[category].push(product);
        });

        // Render groups
        const html = Object.entries(groupedProducts).map(([category, categoryProducts]) => {
          const productsHtml = categoryProducts.map(product => {
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

          return `
            <div class="sfb-tray-group">
              <h3 class="sfb-tray-group__title" role="heading" aria-level="3">${escapeHtml(category)}</h3>
              <div class="sfb-tray-group__list" role="list">${productsHtml}</div>
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
    } catch (e) {
      console.warn('Failed to restore state from localStorage/sessionStorage:', e);
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
