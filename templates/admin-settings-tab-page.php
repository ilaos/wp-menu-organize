<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1>Menu Organize Settings</h1>
    
    <!-- Theme Toggle Section -->
    <div class="wmo-section wmo-theme-section">
        <h3><span class="dashicons dashicons-admin-appearance"></span> Theme Settings</h3>
        <div class="wmo-theme-toggle-wrapper">
            <div class="wmo-theme-toggle">
                <label class="wmo-theme-label">
                    <input type="checkbox" id="wmo-dark-mode-toggle" class="wmo-theme-checkbox" />
                    <span class="wmo-theme-slider">
                        <span class="wmo-theme-slider-inner">
                            <span class="wmo-theme-icon wmo-light-icon">☀️</span>
                            <span class="wmo-theme-icon wmo-dark-icon">🌙</span>
                        </span>
                    </span>
                    <span class="wmo-theme-text">Dark Mode</span>
                </label>
            </div>
            <p class="description">
                Toggle between light and dark theme for the admin interface. Settings are saved automatically.
            </p>
        </div>
    </div>
    
    <!-- Import/Export Configuration Section -->
    <div class="wmo-section wmo-import-export-section">
        <h3><span class="dashicons dashicons-download"></span> Import/Export Configuration</h3>
        
        <div class="wmo-import-export-intro">
            <p class="description">
                <strong>⚡ Why use Import/Export?</strong><br>
                • <strong>Multi-site management:</strong> Apply the same menu styling to multiple WordPress sites<br>
                • <strong>Backup & recovery:</strong> Save your menu customizations and restore them anytime<br>
                • <strong>Development workflow:</strong> Transfer settings from staging to production sites<br>
                • <strong>Template sharing:</strong> Share your menu designs with team members or clients
            </p>
        </div>
        
        <div class="wmo-import-export-wrapper">
            <!-- Export Section -->
            <div class="wmo-export-section">
                <h4><span class="dashicons dashicons-upload"></span> Export Your Settings</h4>
                <p class="description">Download a file containing all your current menu customizations.</p>
                
                <div class="wmo-export-options">
                    <label><input type="checkbox" id="wmo-export-colors" checked> Colors</label>
                    <label><input type="checkbox" id="wmo-export-typography" checked> Typography</label>
                    <label><input type="checkbox" id="wmo-export-badges" checked> Badges</label>
                    <label><input type="checkbox" id="wmo-export-theme" checked> Theme Settings</label>
                </div>
                
                <button type="button" id="wmo-export-btn" class="button button-secondary">
                    <span class="dashicons dashicons-download"></span> Export Configuration
                </button>
                
                <div class="wmo-export-info">
                    <small>💾 This will create a .json file that you can save as a backup or use on other sites.</small>
                </div>
            </div>
            
            <!-- Import Section -->
            <div class="wmo-import-section">
                <h4><span class="dashicons dashicons-download"></span> Import Settings</h4>
                <p class="description">Upload a configuration file to apply menu settings from another site or backup.</p>
                
                <div class="wmo-import-methods">
                    <div class="wmo-import-file">
                        <label for="wmo-import-file">Choose Configuration File:</label>
                        <input type="file" id="wmo-import-file" accept=".json" />
                    </div>
                    
                    <div class="wmo-import-text">
                        <label for="wmo-import-textarea">Or paste JSON configuration:</label>
                        <textarea id="wmo-import-textarea" rows="4" placeholder='Paste your exported JSON configuration here...'></textarea>
                    </div>
                </div>
                
                <div class="wmo-import-options">
                    <label><input type="radio" name="wmo-import-mode" value="replace" checked> Replace all settings</label>
                    <label><input type="radio" name="wmo-import-mode" value="merge"> Merge with existing settings</label>
                </div>
                
                <button type="button" id="wmo-preview-import-btn" class="button button-secondary" disabled>
                    <span class="dashicons dashicons-visibility"></span> Preview Import
                </button>
                
                <button type="button" id="wmo-import-btn" class="button button-primary" disabled>
                    <span class="dashicons dashicons-download"></span> Import Configuration
                </button>
                
                <div class="wmo-import-info">
                    <small>⚠️ <strong>Tip:</strong> Always preview before importing to see what will change!</small>
                </div>
            </div>
        </div>
        
        <!-- Import Preview Modal -->
        <div id="wmo-import-preview-modal" class="wmo-modal" style="display: none;">
            <div class="wmo-modal-content">
                <div class="wmo-modal-header">
                    <h3>Import Preview</h3>
                    <button type="button" class="wmo-modal-close">&times;</button>
                </div>
                <div class="wmo-modal-body">
                    <p>The following changes will be made:</p>
                    <div id="wmo-preview-content"></div>
                </div>
                <div class="wmo-modal-footer">
                    <button type="button" class="button button-secondary wmo-modal-close">Cancel</button>
                    <button type="button" id="wmo-confirm-import-btn" class="button button-primary">Proceed with Import</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="wmo-section wmo-settings-section">
        <h3><span class="dashicons dashicons-admin-settings"></span> General Settings</h3>
        
        <div class="wmo-settings-content">
            <div class="wmo-settings-group">
                <h4>Plugin Configuration</h4>
                <p class="description">
                    Configure general settings for the Menu Organize plugin. More options will be added here in future updates.
                </p>
                
                <div class="wmo-settings-placeholder">
                    <div class="wmo-placeholder-item">
                        <span class="dashicons dashicons-clock"></span>
                        <h5>Coming Soon</h5>
                        <p>Advanced configuration options will be available here.</p>
                    </div>
                    
                    <div class="wmo-placeholder-item">
                        <span class="dashicons dashicons-admin-tools"></span>
                        <h5>Plugin Management</h5>
                        <p>Tools for managing plugin behavior and performance.</p>
                    </div>
                    
                    <div class="wmo-placeholder-item">
                        <span class="dashicons dashicons-admin-users"></span>
                        <h5>User Permissions</h5>
                        <p>Configure who can access and modify menu settings.</p>
                    </div>
                    
                    <div class="wmo-placeholder-item">
                        <span class="dashicons dashicons-backup"></span>
                        <h5>Backup & Restore</h5>
                        <p>Advanced backup and restore functionality for menu configurations.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="wmo-section wmo-advanced-settings-section">
        <h3><span class="dashicons dashicons-admin-tools"></span> Advanced Settings</h3>
        
        <div class="wmo-settings-content">
            <div class="wmo-settings-group">
                <h4>Performance & Optimization</h4>
                <p class="description">
                    Fine-tune the plugin's performance and behavior for your specific needs.
                </p>
                
                <div class="wmo-settings-placeholder">
                    <div class="wmo-placeholder-item">
                        <span class="dashicons dashicons-performance"></span>
                        <h5>Performance Options</h5>
                        <p>Optimize loading times and resource usage.</p>
                    </div>
                    
                    <div class="wmo-placeholder-item">
                        <span class="dashicons dashicons-admin-plugins"></span>
                        <h5>Integration Settings</h5>
                        <p>Configure how the plugin integrates with other WordPress features.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.wmo-settings-section,
.wmo-advanced-settings-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.wmo-settings-section h3,
.wmo-advanced-settings-section h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #23282d;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.wmo-settings-section h3 .dashicons,
.wmo-advanced-settings-section h3 .dashicons {
    color: #0073aa;
}

.wmo-settings-group h4 {
    margin-top: 0;
    margin-bottom: 12px;
    color: #23282d;
    font-size: 16px;
}

.wmo-settings-placeholder {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.wmo-placeholder-item {
    background: #f9f9f9;
    border: 1px solid #e5e5e5;
    border-radius: 6px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
}

.wmo-placeholder-item:hover {
    background: #f0f0f0;
    border-color: #0073aa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.wmo-placeholder-item .dashicons {
    font-size: 32px;
    width: 32px;
    height: 32px;
    color: #0073aa;
    margin-bottom: 12px;
}

.wmo-placeholder-item h5 {
    margin: 0 0 8px 0;
    color: #23282d;
    font-size: 14px;
    font-weight: 600;
}

.wmo-placeholder-item p {
    margin: 0;
    color: #666;
    font-size: 13px;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .wmo-settings-placeholder {
        grid-template-columns: 1fr;
    }
    
    .wmo-settings-section,
    .wmo-advanced-settings-section {
        padding: 16px;
    }
}
</style>
