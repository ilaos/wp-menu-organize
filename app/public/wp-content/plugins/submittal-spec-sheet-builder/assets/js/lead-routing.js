/**
 * Advanced Lead Routing UI
 *
 * React-like component for managing lead routing rules, fallback, and delivery log
 */

(function() {
  'use strict';

  // State management
  let state = {
    enabled: false,
    rules: [],
    fallback: { email: '', webhook: '' },
    log: [],
    categories: [],
    editing: null,
    saving: false,
    testing: null,
  };

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    const container = document.getElementById('sfb-lead-routing-app');
    if (!container || !window.SFB_Routing) return;

    // Load initial state
    state.enabled = window.SFB_Routing.enabled || false;
    state.rules = window.SFB_Routing.rules || [];
    state.fallback = window.SFB_Routing.fallback || { email: '', webhook: '' };
    state.log = window.SFB_Routing.log || [];
    state.categories = window.SFB_Routing.categories || [];

    render();
  }

  function render() {
    const container = document.getElementById('sfb-lead-routing-app');
    if (!container) return;

    container.innerHTML = `
      <div class="sfb-routing-controls">
        ${renderToggle()}
      </div>

      ${state.enabled ? `
        <div class="sfb-routing-main" style="margin-top: 24px;">
          ${renderRulesSection()}
          ${renderFallbackSection()}
          ${renderLogSection()}
        </div>
      ` : `
        <div style="padding: 40px; text-align: center; background: #f9fafb; border-radius: 8px; margin-top: 24px;">
          <p style="color: #6b7280; margin: 0;">
            Enable Advanced Lead Routing to configure rules and webhooks.
          </p>
        </div>
      `}
    `;

    attachEventListeners();
  }

  function renderToggle() {
    return `
      <label style="display: flex; align-items: center; font-weight: 600; cursor: pointer;">
        <input
          type="checkbox"
          id="sfb-routing-enabled"
          ${state.enabled ? 'checked' : ''}
          style="margin-right: 12px;">
        Enable Advanced Lead Routing
      </label>
    `;
  }

  function renderRulesSection() {
    return `
      <div class="sfb-routing-rules">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
          <h3 style="margin: 0;">Routing Rules</h3>
          <button type="button" class="button" id="sfb-add-rule">
            <span class="dashicons dashicons-plus" style="margin-top: 4px;"></span> Add Rule
          </button>
        </div>

        ${state.rules.length === 0 ? `
          <div style="padding: 32px; text-align: center; background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 8px;">
            <p style="color: #9ca3af; margin: 0;">
              No rules configured. Click "Add Rule" to get started.
            </p>
          </div>
        ` : `
          <div class="sfb-rules-list">
            ${state.rules.map((rule, index) => renderRule(rule, index)).join('')}
          </div>
        `}

        ${state.rules.length > 0 ? `
          <div style="margin-top: 16px;">
            <button type="button" class="button button-primary" id="sfb-save-routing">
              Save Rules
            </button>
            ${state.saving ? '<span style="margin-left: 12px; color: #6b7280;">Saving...</span>' : ''}
          </div>
        ` : ''}
      </div>
    `;
  }

  function renderRule(rule, index) {
    const isEditing = state.editing === rule.id;

    return `
      <div class="sfb-rule-card" data-rule-id="${rule.id}" data-index="${index}">
        <div class="sfb-rule-header">
          <span class="dashicons dashicons-menu sfb-drag-handle" title="Drag to reorder"></span>
          <strong>${escapeHtml(rule.name || 'Unnamed Rule')}</strong>
          <label style="margin-left: auto; display: flex; align-items: center; gap: 8px;">
            <input
              type="checkbox"
              class="sfb-rule-enabled"
              data-rule-id="${rule.id}"
              ${rule.enabled ? 'checked' : ''}>
            <span style="font-weight: normal;">Enabled</span>
          </label>
        </div>

        ${isEditing ? renderRuleForm(rule, index) : renderRulePreview(rule, index)}
      </div>
    `;
  }

  function renderRuleForm(rule, index) {
    return `
      <div class="sfb-rule-form">
        <div class="sfb-form-row">
          <label>
            <strong>Rule Name</strong>
            <input
              type="text"
              class="regular-text"
              value="${escapeHtml(rule.name || '')}"
              data-field="name"
              data-rule-id="${rule.id}"
              placeholder="E.g., Acme Domains">
          </label>
        </div>

        <div class="sfb-form-section">
          <h4 style="margin: 0 0 12px 0; color: #374151;">Conditions (any match triggers rule)</h4>

          <div class="sfb-form-row">
            <label>
              <strong>If Email Domain contains</strong>
              <input
                type="text"
                class="regular-text"
                value="${escapeHtml(rule.email_domains || '')}"
                data-field="email_domains"
                data-rule-id="${rule.id}"
                placeholder="acme.com, bigco.net (comma-separated)">
              <small style="color: #6b7280;">Match against the domain part of the email address</small>
            </label>
          </div>

          <div class="sfb-form-row">
            <label>
              <strong>If UTM Source contains</strong>
              <input
                type="text"
                class="regular-text"
                value="${escapeHtml(rule.utm_source || '')}"
                data-field="utm_source"
                data-rule-id="${rule.id}"
                placeholder="google, facebook (comma-separated)">
            </label>
          </div>

          <div class="sfb-form-row">
            <label>
              <strong>If UTM Medium contains</strong>
              <input
                type="text"
                class="regular-text"
                value="${escapeHtml(rule.utm_medium || '')}"
                data-field="utm_medium"
                data-rule-id="${rule.id}"
                placeholder="cpc, email (comma-separated)">
            </label>
          </div>

          <div class="sfb-form-row">
            <label>
              <strong>If UTM Campaign contains</strong>
              <input
                type="text"
                class="regular-text"
                value="${escapeHtml(rule.utm_campaign || '')}"
                data-field="utm_campaign"
                data-rule-id="${rule.id}"
                placeholder="fall_promo, winter_sale (comma-separated)">
            </label>
          </div>

          <div class="sfb-form-row">
            <label>
              <strong>If Top Category equals</strong>
              <select
                class="regular-text"
                data-field="top_category"
                data-rule-id="${rule.id}">
                <option value="">-- Any Category --</option>
                ${state.categories.map(cat => `
                  <option value="${escapeHtml(cat)}" ${rule.top_category === cat ? 'selected' : ''}>
                    ${escapeHtml(cat)}
                  </option>
                `).join('')}
              </select>
            </label>
          </div>
        </div>

        <div class="sfb-form-section">
          <h4 style="margin: 0 0 12px 0; color: #374151;">Actions (when rule matches)</h4>

          <div class="sfb-form-row">
            <label>
              <strong>Then Email to</strong>
              <input
                type="text"
                class="regular-text"
                value="${escapeHtml(rule.then_email || '')}"
                data-field="then_email"
                data-rule-id="${rule.id}"
                placeholder="sales@example.com, manager@example.com (comma-separated)">
              <small style="color: #6b7280;">Comma-separated email addresses</small>
            </label>
          </div>

          <div class="sfb-form-row">
            <label>
              <strong>Then Webhook URL</strong>
              <input
                type="url"
                class="regular-text"
                value="${escapeHtml(rule.then_webhook || '')}"
                data-field="then_webhook"
                data-rule-id="${rule.id}"
                placeholder="https://hooks.zapier.com/...">
              <small style="color: #6b7280;">Must start with https://</small>
            </label>
          </div>
        </div>

        <div class="sfb-rule-actions">
          <button type="button" class="button button-primary" data-action="save-rule" data-rule-id="${rule.id}">
            Save Changes
          </button>
          <button type="button" class="button" data-action="test-rule" data-rule-id="${rule.id}">
            Test with Last Lead
          </button>
          <button type="button" class="button button-link-delete" data-action="delete-rule" data-rule-id="${rule.id}">
            Delete Rule
          </button>
        </div>
      </div>
    `;
  }

  function renderRulePreview(rule, index) {
    const conditions = [];
    if (rule.email_domains) conditions.push(`Email domain: ${rule.email_domains}`);
    if (rule.utm_source) conditions.push(`UTM Source: ${rule.utm_source}`);
    if (rule.utm_medium) conditions.push(`UTM Medium: ${rule.utm_medium}`);
    if (rule.utm_campaign) conditions.push(`UTM Campaign: ${rule.utm_campaign}`);
    if (rule.top_category) conditions.push(`Category: ${rule.top_category}`);

    const actions = [];
    if (rule.then_email) actions.push(`📧 Email to: ${rule.then_email}`);
    if (rule.then_webhook) actions.push(`🔗 Webhook: ${truncate(rule.then_webhook, 40)}`);

    return `
      <div class="sfb-rule-preview">
        ${conditions.length > 0 ? `
          <div style="margin-bottom: 8px;">
            <strong style="color: #6b7280; font-size: 12px; text-transform: uppercase;">Conditions:</strong>
            <ul style="margin: 4px 0 0 0; padding-left: 20px; color: #374151;">
              ${conditions.map(c => `<li style="font-size: 13px;">${escapeHtml(c)}</li>`).join('')}
            </ul>
          </div>
        ` : `
          <p style="color: #9ca3af; margin: 0 0 8px 0; font-style: italic;">No conditions configured</p>
        `}

        ${actions.length > 0 ? `
          <div>
            <strong style="color: #6b7280; font-size: 12px; text-transform: uppercase;">Actions:</strong>
            <ul style="margin: 4px 0 0 0; padding-left: 20px; color: #374151;">
              ${actions.map(a => `<li style="font-size: 13px;">${escapeHtml(a)}</li>`).join('')}
            </ul>
          </div>
        ` : `
          <p style="color: #9ca3af; margin: 0; font-style: italic;">No actions configured</p>
        `}

        <div class="sfb-rule-actions" style="margin-top: 16px;">
          <button type="button" class="button" data-action="edit-rule" data-rule-id="${rule.id}">
            Edit
          </button>
          <button type="button" class="button" data-action="test-rule" data-rule-id="${rule.id}">
            Test
          </button>
        </div>
      </div>
    `;
  }

  function renderFallbackSection() {
    return `
      <div class="sfb-routing-fallback" style="margin-top: 32px; padding: 20px; background: #fef3c7; border: 2px solid #fbbf24; border-radius: 8px;">
        <h3 style="margin: 0 0 12px 0;">Fallback (when no rules match)</h3>
        <p style="margin: 0 0 16px 0; color: #78350f;">
          If none of the rules above match, use these fallback destinations (optional).
        </p>

        <div class="sfb-form-row">
          <label>
            <strong>Fallback Email to</strong>
            <input
              type="text"
              class="regular-text"
              id="sfb-fallback-email"
              value="${escapeHtml(state.fallback.email || '')}"
              placeholder="fallback@example.com">
          </label>
        </div>

        <div class="sfb-form-row">
          <label>
            <strong>Fallback Webhook URL</strong>
            <input
              type="url"
              class="regular-text"
              id="sfb-fallback-webhook"
              value="${escapeHtml(state.fallback.webhook || '')}"
              placeholder="https://...">
          </label>
        </div>

        <button type="button" class="button button-primary" id="sfb-save-fallback">
          Save Fallback
        </button>
      </div>
    `;
  }

  function renderLogSection() {
    return `
      <div class="sfb-routing-log" style="margin-top: 32px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
          <h3 style="margin: 0;">Delivery Log (Last 20)</h3>
          <button type="button" class="button" id="sfb-clear-log">
            Clear Log
          </button>
        </div>

        ${state.log.length === 0 ? `
          <div style="padding: 32px; text-align: center; background: #f9fafb; border-radius: 8px;">
            <p style="color: #9ca3af; margin: 0;">No deliveries logged yet.</p>
          </div>
        ` : `
          <table class="wp-list-table widefat fixed striped">
            <thead>
              <tr>
                <th style="width: 15%;">Time</th>
                <th style="width: 15%;">Lead ID</th>
                <th style="width: 20%;">Rule</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 20%;">Webhook</th>
                <th style="width: 15%;">Retries</th>
              </tr>
            </thead>
            <tbody>
              ${state.log.map(entry => renderLogEntry(entry)).join('')}
            </tbody>
          </table>
        `}
      </div>
    `;
  }

  function renderLogEntry(entry) {
    const emailStatus = entry.email_status ?
      (entry.email_status === 'success' ?
        '<span style="color: #10b981;">✓ Sent</span>' :
        '<span style="color: #ef4444;">✗ Failed</span>') :
      '<span style="color: #9ca3af;">—</span>';

    const webhookStatus = entry.webhook_status ?
      (entry.webhook_status === 'success' ?
        `<span style="color: #10b981;">✓ ${entry.webhook_code || 200}</span>` :
        `<span style="color: #ef4444;">✗ ${entry.webhook_code || 'Error'}</span>`) :
      '<span style="color: #9ca3af;">—</span>';

    const retries = entry.webhook_attempts > 1 ?
      `<span style="color: #f59e0b;">${entry.webhook_attempts} attempts</span>` :
      '<span style="color: #9ca3af;">—</span>';

    return `
      <tr>
        <td>${escapeHtml(formatTime(entry.timestamp))}</td>
        <td>#${entry.lead_id}</td>
        <td>
          ${entry.is_rule ? escapeHtml(entry.rule_name) : '<em style="color: #9ca3af;">fallback</em>'}
        </td>
        <td>${emailStatus}</td>
        <td>${webhookStatus}</td>
        <td>${retries}</td>
      </tr>
    `;
  }

  function attachEventListeners() {
    // Toggle enabled status
    const toggleEl = document.getElementById('sfb-routing-enabled');
    if (toggleEl) {
      toggleEl.addEventListener('change', handleToggle);
    }

    // Add rule button
    const addRuleBtn = document.getElementById('sfb-add-rule');
    if (addRuleBtn) {
      addRuleBtn.addEventListener('click', handleAddRule);
    }

    // Save rules button
    const saveBtn = document.getElementById('sfb-save-routing');
    if (saveBtn) {
      saveBtn.addEventListener('click', handleSaveAll);
    }

    // Save fallback button
    const saveFallbackBtn = document.getElementById('sfb-save-fallback');
    if (saveFallbackBtn) {
      saveFallbackBtn.addEventListener('click', handleSaveFallback);
    }

    // Clear log button
    const clearLogBtn = document.getElementById('sfb-clear-log');
    if (clearLogBtn) {
      clearLogBtn.addEventListener('click', handleClearLog);
    }

    // Rule-specific buttons
    document.querySelectorAll('[data-action]').forEach(btn => {
      btn.addEventListener('click', handleRuleAction);
    });

    // Rule enabled toggles
    document.querySelectorAll('.sfb-rule-enabled').forEach(checkbox => {
      checkbox.addEventListener('change', handleRuleEnabledToggle);
    });

    // Rule form inputs
    document.querySelectorAll('[data-field]').forEach(input => {
      input.addEventListener('input', handleFieldChange);
    });

    // Drag and drop for reordering
    initDragDrop();
  }

  function handleToggle(e) {
    state.enabled = e.target.checked;
    handleSaveAll();
  }

  function handleAddRule() {
    const newRule = {
      id: 'rule_' + Date.now(),
      name: '',
      email_domains: '',
      utm_source: '',
      utm_medium: '',
      utm_campaign: '',
      top_category: '',
      then_email: '',
      then_webhook: '',
      enabled: true,
    };

    state.rules.push(newRule);
    state.editing = newRule.id;
    render();
  }

  function handleRuleAction(e) {
    const action = e.currentTarget.dataset.action;
    const ruleId = e.currentTarget.dataset.ruleId;

    switch (action) {
      case 'edit-rule':
        state.editing = ruleId;
        render();
        break;

      case 'save-rule':
        state.editing = null;
        render();
        break;

      case 'test-rule':
        handleTestRule(ruleId);
        break;

      case 'delete-rule':
        if (confirm('Are you sure you want to delete this rule?')) {
          state.rules = state.rules.filter(r => r.id !== ruleId);
          render();
          handleSaveAll();
        }
        break;
    }
  }

  function handleRuleEnabledToggle(e) {
    const ruleId = e.target.dataset.ruleId;
    const rule = state.rules.find(r => r.id === ruleId);
    if (rule) {
      rule.enabled = e.target.checked;
    }
  }

  function handleFieldChange(e) {
    const ruleId = e.target.dataset.ruleId;
    const field = e.target.dataset.field;
    const value = e.target.value;

    const rule = state.rules.find(r => r.id === ruleId);
    if (rule) {
      rule[field] = value;
    }
  }

  function handleSaveAll() {
    state.saving = true;
    render();

    const formData = new FormData();
    formData.append('action', 'sfb_routing_save');
    formData.append('nonce', window.SFB_Routing.nonce);
    formData.append('enabled', state.enabled ? '1' : '0');
    formData.append('rules', JSON.stringify(state.rules));
    formData.append('fallback', JSON.stringify(state.fallback));

    fetch(window.SFB_Routing.ajaxUrl, {
      method: 'POST',
      body: formData,
    })
    .then(res => res.json())
    .then(data => {
      state.saving = false;
      if (data.success) {
        showNotice('Settings saved successfully', 'success');
        state.rules = data.data.rules || state.rules;
        state.fallback = data.data.fallback || state.fallback;
        render();
      } else {
        showNotice(data.data?.message || 'Failed to save settings', 'error');
        render();
      }
    })
    .catch(err => {
      state.saving = false;
      showNotice('Network error: ' + err.message, 'error');
      render();
    });
  }

  function handleSaveFallback() {
    state.fallback.email = document.getElementById('sfb-fallback-email').value;
    state.fallback.webhook = document.getElementById('sfb-fallback-webhook').value;
    handleSaveAll();
  }

  function handleTestRule(ruleId) {
    const rule = state.rules.find(r => r.id === ruleId);
    if (!rule) return;

    state.testing = ruleId;
    render();

    const formData = new FormData();
    formData.append('action', 'sfb_routing_test');
    formData.append('nonce', window.SFB_Routing.nonce);
    formData.append('rule', JSON.stringify(rule));

    fetch(window.SFB_Routing.ajaxUrl, {
      method: 'POST',
      body: formData,
    })
    .then(res => res.json())
    .then(data => {
      state.testing = null;
      if (data.success) {
        const result = data.data;
        if (!result.success) {
          alert(result.message);
        } else if (result.matches) {
          alert('✓ Rule matches!\n\nLast lead:\n' +
            'Email: ' + result.lead.email + '\n' +
            'Project: ' + (result.lead.project_name || 'N/A') + '\n' +
            'Category: ' + (result.lead.top_category || 'N/A'));
        } else {
          alert('✗ Rule does not match the last captured lead.\n\n' +
            'Last lead:\n' +
            'Email: ' + result.lead.email + '\n' +
            'Project: ' + (result.lead.project_name || 'N/A') + '\n' +
            'Category: ' + (result.lead.top_category || 'N/A'));
        }
      } else {
        alert('Test failed: ' + (data.data?.message || 'Unknown error'));
      }
      render();
    })
    .catch(err => {
      state.testing = null;
      alert('Network error: ' + err.message);
      render();
    });
  }

  function handleClearLog() {
    if (!confirm('Are you sure you want to clear the delivery log?')) return;

    const formData = new FormData();
    formData.append('action', 'sfb_routing_clear_log');
    formData.append('nonce', window.SFB_Routing.nonce);

    fetch(window.SFB_Routing.ajaxUrl, {
      method: 'POST',
      body: formData,
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        state.log = [];
        render();
        showNotice('Delivery log cleared', 'success');
      } else {
        showNotice(data.data?.message || 'Failed to clear log', 'error');
      }
    })
    .catch(err => {
      showNotice('Network error: ' + err.message, 'error');
    });
  }

  function initDragDrop() {
    const ruleCards = document.querySelectorAll('.sfb-rule-card');
    let draggedElement = null;

    ruleCards.forEach(card => {
      const handle = card.querySelector('.sfb-drag-handle');
      if (!handle) return;

      handle.addEventListener('mousedown', () => {
        card.draggable = true;
      });

      card.addEventListener('dragstart', (e) => {
        draggedElement = card;
        card.style.opacity = '0.5';
      });

      card.addEventListener('dragend', () => {
        card.style.opacity = '';
        card.draggable = false;
      });

      card.addEventListener('dragover', (e) => {
        e.preventDefault();
        const afterElement = getDragAfterElement(card.parentElement, e.clientY);
        if (afterElement == null) {
          card.parentElement.appendChild(draggedElement);
        } else {
          card.parentElement.insertBefore(draggedElement, afterElement);
        }
      });

      card.addEventListener('drop', () => {
        // Reorder state.rules based on new DOM order
        const newOrder = [];
        document.querySelectorAll('.sfb-rule-card').forEach(c => {
          const ruleId = c.dataset.ruleId;
          const rule = state.rules.find(r => r.id === ruleId);
          if (rule) newOrder.push(rule);
        });
        state.rules = newOrder;
      });
    });
  }

  function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.sfb-rule-card:not(.dragging)')];

    return draggableElements.reduce((closest, child) => {
      const box = child.getBoundingClientRect();
      const offset = y - box.top - box.height / 2;

      if (offset < 0 && offset > closest.offset) {
        return { offset: offset, element: child };
      } else {
        return closest;
      }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
  }

  function showNotice(message, type = 'info') {
    const notice = document.createElement('div');
    notice.className = `notice notice-${type} is-dismissible`;
    notice.innerHTML = `<p>${escapeHtml(message)}</p>`;

    const container = document.querySelector('.wrap');
    if (container) {
      container.insertBefore(notice, container.firstChild);
      setTimeout(() => notice.remove(), 5000);
    }
  }

  // Helper functions
  function escapeHtml(text) {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
  }

  function truncate(str, length) {
    return str.length > length ? str.substring(0, length) + '...' : str;
  }

  function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
  }

})();
