<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1>Menu Templates</h1>
    <div class="wmo-section wmo-templates-section">
        <h3><span class="dashicons dashicons-art"></span> Menu Templates</h3>
        <div class="wmo-templates-gallery" id="wmo-templates-gallery">
            <div class="wmo-templates-loading">
                <span class="dashicons dashicons-update-alt wmo-spin"></span>
                Loading templates...
            </div>
        </div>
    </div>
</div>

<!-- Template Preview Modal -->
<div id="template-preview-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 100000;">
    <div style="background: white; margin: 50px auto; max-width: 800px; padding: 30px; position: relative; border-radius: 8px;">
        <h2 id="preview-title" style="margin-top: 0;">Template Preview</h2>
        <div style="background: #23282d; padding: 0; min-height: 400px; display: flex;">
            <div id="preview-menu" style="width: 200px; background: #23282d;">
                <!-- Menu items here -->
            </div>
            <div style="flex: 1; background: #f0f0f1; padding: 20px;">
                <div style="background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <h3 style="margin-top: 0; color: #333;">Dashboard</h3>
                    <p style="color: #666;">This preview shows how your admin menu will look with the selected template colors.</p>
                </div>
            </div>
        </div>
        <button type="button" class="button button-large" onclick="jQuery('#template-preview-modal').hide();" style="margin-top: 20px;">Close Preview</button>
    </div>
</div>
