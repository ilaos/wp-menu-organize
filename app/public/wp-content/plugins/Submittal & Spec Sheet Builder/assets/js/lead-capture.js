/**
 * Lead Capture Modal - Pro Feature
 *
 * Handles lead capture modal interactions, form submission, and validation.
 *
 * @package SubmittalBuilder
 * @version 1.0.2
 */

(function() {
  'use strict';

  // State
  let modalOpenTime = 0;
  let pendingPdfData = null;

  /**
   * Initialize lead capture functionality
   */
  function init() {
    const modal = document.getElementById('sfb-lead-modal');
    if (!modal) return; // Modal not present (lead capture disabled)

    // Close button
    const closeBtn = modal.querySelector('.sfb-modal-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', closeModal);
    }

    // Close on overlay click
    const overlay = modal.querySelector('.sfb-modal-overlay');
    if (overlay) {
      overlay.addEventListener('click', closeModal);
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && modal.style.display !== 'none') {
        closeModal();
      }
    });

    // Form submission
    const form = document.getElementById('sfb-lead-form');
    if (form) {
      form.addEventListener('submit', handleSubmit);

      // Real-time form validation
      setupFormValidation();

      // Enable submit button after minimum delay (anti-bot)
      enableSubmitAfterDelay();
    }
  }

  /**
   * Setup real-time form validation
   */
  function setupFormValidation() {
    const emailInput = document.getElementById('sfb-lead-email');
    const consentCheckbox = document.getElementById('sfb-lead-consent');
    const submitBtn = document.getElementById('sfb-lead-submit');

    if (!emailInput || !submitBtn) return;

    // Validate form on input changes
    const validateForm = () => {
      const emailValid = emailInput.value && isValidEmail(emailInput.value);
      const consentGiven = consentCheckbox ? consentCheckbox.checked : true;

      // Only enable if email is valid AND consent is checked (if checkbox exists)
      if (emailValid && consentGiven) {
        submitBtn.disabled = false;
        submitBtn.setAttribute('aria-disabled', 'false');
      } else {
        submitBtn.disabled = true;
        submitBtn.setAttribute('aria-disabled', 'true');
      }
    };

    // Listen for changes
    emailInput.addEventListener('input', validateForm);
    emailInput.addEventListener('blur', validateForm);

    if (consentCheckbox) {
      consentCheckbox.addEventListener('change', validateForm);
    }

    // Initial validation
    validateForm();
  }

  /**
   * Open the modal
   * @param {Object} pdfData - Data to pass when generating PDF after lead submission
   */
  function openModal(pdfData = {}) {
    const modal = document.getElementById('sfb-lead-modal');
    if (!modal) return;

    // Store PDF data for later
    pendingPdfData = pdfData;

    // Auto-populate project name if provided
    const projectNameDisplay = document.getElementById('sfb-lead-project-name');
    if (projectNameDisplay && pdfData.projectName) {
      projectNameDisplay.textContent = pdfData.projectName;
      projectNameDisplay.style.display = 'block';
    }

    // Record open time
    modalOpenTime = Date.now();

    // Show modal
    modal.style.display = 'flex';

    // Focus first input
    const firstInput = modal.querySelector('input[type="email"]');
    if (firstInput) {
      setTimeout(() => firstInput.focus(), 100);
    }

    // Prevent body scroll
    document.body.style.overflow = 'hidden';
  }

  /**
   * Close the modal
   */
  function closeModal() {
    const modal = document.getElementById('sfb-lead-modal');
    if (!modal) return;

    modal.style.display = 'none';
    document.body.style.overflow = '';

    // Clear pending data
    pendingPdfData = null;
  }

  /**
   * Enable submit button after minimum delay (anti-bot measure)
   */
  function enableSubmitAfterDelay() {
    const submitBtn = document.getElementById('sfb-lead-submit');
    if (!submitBtn) return;

    // Random delay between 800-1200ms
    const delay = Math.floor(Math.random() * 400) + 800;

    submitBtn.disabled = true;

    setTimeout(() => {
      submitBtn.disabled = false;
    }, delay);
  }

  /**
   * Handle form submission
   */
  async function handleSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = document.getElementById('sfb-lead-submit');
    const errorEl = document.getElementById('sfb-lead-error');

    // Clear previous errors
    if (errorEl) {
      errorEl.style.display = 'none';
      errorEl.textContent = '';
    }

    // Get form data
    const formData = new FormData(form);
    const email = formData.get('email');
    const phone = formData.get('phone');
    const consent = formData.get('consent') ? 1 : 0;

    // Basic client validation
    if (!email || !isValidEmail(email)) {
      showError('Please enter a valid email address.');
      return;
    }

    // Set loading state
    submitBtn.disabled = true;
    submitBtn.classList.add('is-loading');

    // Get builder app container for nonce and AJAX URL
    const builderApp = document.getElementById('sfb-builder-app');
    const nonce = builderApp?.dataset.nonce;
    const ajaxUrl = builderApp?.dataset.ajaxUrl;

    if (!ajaxUrl || !nonce) {
      showError('Configuration error. Please refresh the page.');
      submitBtn.disabled = false;
      submitBtn.classList.remove('is-loading');
      return;
    }

    // Capture UTM parameters from URL
    const urlParams = new URLSearchParams(window.location.search);
    const utmData = {
      utm_source: urlParams.get('utm_source') || '',
      utm_medium: urlParams.get('utm_medium') || '',
      utm_campaign: urlParams.get('utm_campaign') || '',
      utm_term: urlParams.get('utm_term') || '',
      utm_content: urlParams.get('utm_content') || '',
    };

    // Get project context from pending PDF data
    const projectName = pendingPdfData?.projectName || '';
    const numItems = pendingPdfData?.products?.length || 0;
    const topCategory = getTopCategory(pendingPdfData?.products);

    // Prepare AJAX data
    const data = {
      action: 'sfb_submit_lead',
      nonce: nonce,
      email: email,
      phone: phone,
      consent: consent,
      project_name: projectName,
      num_items: numItems,
      top_category: topCategory,
      sfb_website: formData.get('sfb_website'), // Honeypot
      ...utmData
    };

    try {
      const response = await fetch(ajaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data)
      });

      const result = await response.json();

      if (result.success) {
        // Show success message in modal
        showSuccessMessage();

        // Close modal after brief delay
        setTimeout(() => {
          closeModal();

          // Continue with PDF generation
          if (window.SFB && typeof window.SFB.continueWithPdfGeneration === 'function') {
            window.SFB.continueWithPdfGeneration();
          }
        }, 1500);
      } else {
        // Show error
        const errorMsg = result.data?.message || 'An error occurred. Please try again.';
        showError(errorMsg);

        submitBtn.disabled = false;
        submitBtn.classList.remove('is-loading');
      }
    } catch (error) {
      console.error('Lead submission error:', error);
      showError('Network error. Please check your connection and try again.');

      submitBtn.disabled = false;
      submitBtn.classList.remove('is-loading');
    }
  }

  /**
   * Show success message in modal
   */
  function showSuccessMessage() {
    const modalBody = document.querySelector('.sfb-modal-body');
    if (!modalBody) return;

    // Hide form
    const form = document.getElementById('sfb-lead-form');
    if (form) {
      form.style.display = 'none';
    }

    // Show success message
    const successMsg = document.createElement('div');
    successMsg.className = 'sfb-lead-success';
    successMsg.innerHTML = `
      <div class="sfb-success-icon">✓</div>
      <h3>Thank you!</h3>
      <p>Generating your submittal packet...</p>
    `;
    successMsg.style.cssText = 'text-align: center; padding: 40px 20px;';

    modalBody.appendChild(successMsg);
  }

  /**
   * Show error message
   */
  function showError(message) {
    const errorEl = document.getElementById('sfb-lead-error');
    if (!errorEl) return;

    errorEl.textContent = message;
    errorEl.style.display = 'block';

    // Scroll to error
    errorEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  /**
   * Simple email validation
   */
  function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  /**
   * Get top category from products array
   */
  function getTopCategory(products) {
    if (!products || !Array.isArray(products) || products.length === 0) {
      return '';
    }

    // Count categories
    const categoryCounts = {};
    products.forEach(product => {
      const category = product.lineage?.category || product.category || 'Uncategorized';
      categoryCounts[category] = (categoryCounts[category] || 0) + 1;
    });

    // Find most common
    let topCategory = '';
    let maxCount = 0;
    for (const [category, count] of Object.entries(categoryCounts)) {
      if (count > maxCount) {
        maxCount = count;
        topCategory = category;
      }
    }

    return topCategory;
  }

  /**
   * Show toast notification
   */
  function showToast(message) {
    // Check if toast already exists
    let toast = document.querySelector('.sfb-toast');

    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'sfb-toast';
      document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.classList.add('sfb-toast-visible');

    setTimeout(() => {
      toast.classList.remove('sfb-toast-visible');
    }, 3000);
  }

  // Expose public API
  window.SFB_LeadCapture = {
    openModal: openModal,
    closeModal: closeModal
  };

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
