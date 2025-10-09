// WP Menu Organize - Templates System
(function($) {
    'use strict';

    // Debug: Check if wmo_ajax is available
    console.log('WMO: Script loaded, checking wmo_ajax availability');
    if (typeof wmo_ajax !== 'undefined') {
        console.log('WMO: wmo_ajax is available!');
        console.log('WMO: AJAX URL:', wmo_ajax.ajax_url);
        console.log('WMO: AJAX Nonce:', wmo_ajax.nonce);
    } else {
        console.error('WMO: wmo_ajax is NOT defined! Script localization failed.');
        return; // Exit if wmo_ajax is not available
    }

    // Initialize when document is ready
    $(function() {
        if ($('#wmo-templates-gallery').length > 0) {
            initTemplatesFunctionality();
        }
    });

    // Initialize templates functionality
    function initTemplatesFunctionality() {
        console.log('WMO: Initializing templates functionality');
        wmoLoadTemplates('all');
    }

    // Load templates from server
    function wmoLoadTemplates(category) {
        console.log('WMO: Loading templates for category:', category);
        
        // Safety check: Ensure wmo_ajax is available
        if (typeof wmo_ajax === 'undefined') {
            console.error('WMO: Cannot load templates - wmo_ajax is not defined!');
            $('#wmo-templates-gallery').html('<div class="wmo-templates-error">AJAX configuration missing. Please refresh the page.</div>');
            return;
        }
        
        console.log('WMO: Using AJAX URL:', wmo_ajax.ajax_url);
        console.log('WMO: Using nonce:', wmo_ajax.nonce);
        
        var $gallery = $('#wmo-templates-gallery');
        $gallery.html('<div class="wmo-templates-loading"><span class="dashicons dashicons-update-alt wmo-spin"></span> Loading templates...</div>');

        // Use raw XMLHttpRequest to avoid jQuery parsing issues
        var xhr = new XMLHttpRequest();
        xhr.open('POST', wmo_ajax.ajax_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        
        xhr.onload = function() {
            console.log('WMO: XHR response received');
            console.log('WMO: XHR status:', xhr.status);
            console.log('WMO: XHR response text:', xhr.responseText);
            
            if (xhr.status === 200) {
                try {
                    // Clean the response text
                    var cleanResponse = xhr.responseText.trim();
                    console.log('WMO: Cleaned response:', cleanResponse);
                    
                    // Parse JSON manually
                    var parsedResponse = JSON.parse(cleanResponse);
                    console.log('WMO: Successfully parsed JSON:', parsedResponse);
                    
                    if (parsedResponse && parsedResponse.success) {
                        console.log('WMO: Templates:', parsedResponse.data.templates);
                        wmoRenderTemplates(parsedResponse.data.templates);
                    } else {
                        console.error('WMO: Server returned error:', parsedResponse ? parsedResponse.data : 'No response');
                        $gallery.html('<div class="wmo-templates-error">Server error: ' + (parsedResponse && parsedResponse.data ? parsedResponse.data : 'Unknown error') + '</div>');
                    }
                } catch (e) {
                    console.error('WMO: JSON parse error:', e);
                    console.error('WMO: Raw response that failed to parse:', xhr.responseText);
                    $gallery.html('<div class="wmo-templates-error">Failed to parse server response. Check console for details.</div>');
                }
            } else {
                console.error('WMO: XHR error - status:', xhr.status);
                $gallery.html('<div class="wmo-templates-error">Server error: HTTP ' + xhr.status + '</div>');
            }
        };
        
        xhr.onerror = function() {
            console.error('WMO: XHR network error');
            $gallery.html('<div class="wmo-templates-error">Network error occurred</div>');
        };
        
        // Prepare form data
        var formData = 'action=wmo_load_templates&category=' + encodeURIComponent(category) + '&nonce=' + encodeURIComponent(wmo_ajax.nonce);
        console.log('WMO: Sending XHR request with data:', formData);
        
        xhr.send(formData);
    }

    // Render templates on the page
    function wmoRenderTemplates(templates) {
        console.log('WMO: Rendering', templates.length, 'templates');
        
        var $gallery = $('#wmo-templates-gallery');
        
        if (templates.length === 0) {
            $gallery.html('<div class="wmo-templates-empty">No templates found.</div>');
            return;
        }

        var html = '<div class="wmo-templates-grid">';
        
        templates.forEach(function(template) {
            html += '<div class="wmo-template-card" data-template-id="' + template.id + '">';
            html += '  <h4>' + template.name + '</h4>';
            html += '  <div class="wmo-template-category">' + template.category + '</div>';
            html += '  <div class="wmo-template-description">' + template.description + '</div>';
            html += '  <div class="wmo-template-preview">' + template.preview + '</div>';
            html += '  <div class="wmo-template-actions">';
            html += '    <button type="button" class="button button-primary wmo-apply-template" data-template-id="' + template.id + '">Apply Template</button>';
            html += '  </div>';
            html += '</div>';
        });
        
        html += '</div>';
        $gallery.html(html);
        
        console.log('WMO: Templates rendered successfully!');
    }

})(jQuery); 