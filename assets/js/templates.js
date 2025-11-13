// WP Menu Organize - Templates System
(function($) {
    'use strict';

    // Initialize when document is ready
    $(function() {
        if ($('#wamm-templates-gallery').length > 0) {
            initTemplatesFunctionality();
        }
    });

    // Initialize templates functionality
    function initTemplatesFunctionality() {
        console.log('WAMM: Initializing templates functionality');
        wammLoadTemplates('all');
    }

    // Load templates from server
    function wammLoadTemplates(category) {
        console.log('WAMM: Loading templates for category:', category);
        
        var $gallery = $('#wamm-templates-gallery');
        $gallery.html('<div class="wamm-templates-loading"><span class="dashicons dashicons-update-alt wmo-spin"></span> Loading templates...</div>');

        $.ajax({
            url: wamm_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'wamm_load_templates',
                category: category,
                nonce: wamm_ajax.nonce
            },
            success: function(response) {
                console.log('WAMM: AJAX response:', response);
                if (response.success) {
                    console.log('WAMM: Templates:', response.data.templates);
                    wammRenderTemplates(response.data.templates);
                } else {
                    console.error('WAMM: Failed to load templates:', response.data);
                    $gallery.html('<div class="wamm-templates-error">Failed to load templates: ' + response.data + '</div>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('WAMM: AJAX error:', textStatus, errorThrown);
                $gallery.html('<div class="wamm-templates-error">Failed to load templates. Please refresh the page.</div>');
            }
        });
    }

    // Render templates on the page
    function wammRenderTemplates(templates) {
        console.log('WAMM: Rendering', templates.length, 'templates');
        console.log('WAMM: Template data:', templates);
        
        var $gallery = $('#wamm-templates-gallery');
        
        if (templates.length === 0) {
            $gallery.html('<div class="wamm-templates-empty">No templates found.</div>');
            return;
        }

        var html = '<div class="wamm-templates-grid">';
        
        templates.forEach(function(template, index) {
            console.log('WAMM: Template', index, ':', template);
            console.log('WAMM: Template', index, 'category:', template.category);
            console.log('WAMM: Template', index, 'preview:', template.preview);
            
            html += '<div class="wamm-template-card" data-template-id="' + (template.id || '') + '">';
            html += '  <div class="template-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">';
            html += '    <h4>' + (template.name || 'Untitled Template') + '</h4>';
            html += '    <span class="favorite-star" data-template-id="' + (template.id || '') + '" style="cursor: pointer; font-size: 24px; color: #ddd;">?</span>';
            html += '  </div>';
            html += '  <div class="wamm-template-category">' + (template.category || 'General') + '</div>';
            html += '  <div class="wamm-template-description">' + (template.description || 'No description available') + '</div>';
            
            // Add color preview bar based on template name
            var templateName = (template.name || '').toLowerCase();
            var colors = [];
            
            if (templateName.includes('default')) {
                colors = ['#23282d', '#0073aa', '#ffffff', '#f1f1f1'];
            } else if (templateName.includes('minimal')) {
                colors = ['#2271b1', '#72aee6', '#ffffff', '#f0f0f1'];
            } else if (templateName.includes('content') || templateName.includes('focused')) {
                colors = ['#d63638', '#ff8c00', '#0073aa', '#00a32a'];
            } else if (templateName.includes('dark')) {
                colors = ['#1e1e1e', '#2d2d2d', '#646464', '#00d4ff'];
            } else if (templateName.includes('classic')) {
                colors = ['#23282d', '#0073aa', '#00a0d2', '#0085ba'];
            } else if (templateName.includes('developer')) {
                colors = ['#000000', '#00ff00', '#008000', '#333333'];
            } else if (templateName.includes('agency')) {
                colors = ['#6c5ce7', '#a29bfe', '#fd79a8', '#fdcb6e'];
            } else if (templateName.includes('ecommerce') || templateName.includes('commerce')) {
                colors = ['#96588a', '#c9356c', '#f36e5d', '#fbb034'];
            } else if (templateName.includes('blogger')) {
                colors = ['#ffeaa7', '#dfe6e9', '#fd79a8', '#74b9ff'];
            } else {
                // Default color preview for other templates
                colors = ['#0073aa', '#72aee6', '#ffffff', '#f0f0f1'];
            }
            
            // Create color preview HTML
            html += '<div class="template-color-preview" style="display: flex; gap: 2px; margin: 10px 0; height: 20px;">';
            colors.forEach(function(color) {
                html += '<span style="background: ' + color + '; flex: 1;"></span>';
            });
            html += '</div>';
            
            html += '  <div class="wamm-template-preview">' + (template.preview || 'Preview not available') + '</div>';
            html += '  <div class="wamm-template-actions">';
            html += '    <button type="button" class="button preview-template" data-template-id="' + (template.id || '') + '">Preview</button>';
            html += '    <button type="button" class="button button-primary wmo-apply-template" data-template-id="' + (template.id || '') + '">Apply Template</button>';
            html += '  </div>';
            html += '</div>';
        });
        
        html += '</div>';
        $gallery.html(html);
        
        // Attach event handlers to the Apply Template buttons
        $gallery.find('.wamm-apply-template').on('click', function() {
            var templateId = $(this).data('template-id');
            var templateName = $(this).closest('.wamm-template-card').find('h4').text();
            
            // Confirm before applying
            if (!confirm('Apply the "' + templateName + '" template? This will change your menu colors.')) {
                return;
            }
            
            // Get template data
            var templateColors = getTemplateColorScheme(templateId);
            
            // Send AJAX request to save template
            $.ajax({
                url: wamm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'wamm_apply_template',
                    template_id: templateId,
                    colors: templateColors,
                    nonce: wamm_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        alert('Template applied successfully! The page will now reload.');
                        // Reload to show new colors
                        window.location.reload();
                    } else {
                        alert('Error applying template: ' + response.data);
                    }
                },
                error: function() {
                    alert('Error applying template. Please try again.');
                }
            });
        });
        
        console.log('WAMM: Templates rendered successfully!');
        
        // Initialize favorites system after templates are rendered
        initFavoritesSystem();
    }

    // Apply template functionality
    function wammApplyTemplate(templateId) {
        console.log('WAMM: Applying template with ID:', templateId);
        
        // Show loading state
        var $button = $('.wamm-apply-template[data-template-id="' + templateId + '"]');
        var originalText = $button.text();
        $button.text('Applying...').prop('disabled', true);
        
        $.ajax({
            url: wamm_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'wamm_apply_template',
                template_id: templateId,
                nonce: wamm_ajax.nonce
            },
            success: function(response) {
                console.log('WAMM: Apply template response:', response);
                if (response.success) {
                    // Show success message
                    $button.text('Applied!').removeClass('button-primary').addClass('button-secondary');
                    setTimeout(function() {
                        $button.text(originalText).removeClass('button-secondary').addClass('button-primary').prop('disabled', false);
                    }, 2000);
                    
                    // Optionally reload the page or show a notification
                    alert('Template applied successfully!');
                } else {
                    console.error('WAMM: Failed to apply template:', response.data);
                    $button.text('Error').removeClass('button-primary').addClass('button-secondary');
                    setTimeout(function() {
                        $button.text(originalText).removeClass('button-secondary').addClass('button-primary').prop('disabled', false);
                    }, 2000);
                    alert('Failed to apply template: ' + response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('WAMM: Apply template error:', textStatus, errorThrown);
                $button.text('Error').removeClass('button-primary').addClass('button-secondary');
                setTimeout(function() {
                    $button.text(originalText).removeClass('button-secondary').addClass('button-primary').prop('disabled', false);
                }, 2000);
                alert('Error applying template. Please try again.');
            }
        });
    }

    // Preview functionality
    jQuery(document).on('click', '.preview-template', function() {
        var templateId = jQuery(this).data('template-id');
        var templateName = jQuery(this).closest('.wamm-template-card').find('h4').text();
        
        // Get colors based on template
        var menuHtml = generatePreviewMenu(templateId);
        
        jQuery('#preview-title').text('Preview: ' + templateName);
        jQuery('#preview-menu').html(menuHtml);
        jQuery('#template-preview-modal').show();
    });

    // Add click-outside-to-close functionality
    jQuery('#template-preview-modal').on('click', function(e) {
        // Only close if clicking the overlay, not the modal content
        if (e.target === this) {
            jQuery(this).hide();
        }
    });

    // Also add ESC key to close
    jQuery(document).on('keydown', function(e) {
        if (e.key === 'Escape' && jQuery('#template-preview-modal').is(':visible')) {
            jQuery('#template-preview-modal').hide();
        }
    });

    function generatePreviewMenu(templateId) {
        var colors = getTemplateColors(templateId);
        return '<div style="width: 160px; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif;">' +
            '<div style="padding: 8px 12px; background: ' + colors[0] + '; color: rgba(255,255,255,0.9); font-size: 14px; border-left: 4px solid transparent; cursor: pointer;">?? Dashboard</div>' +
            '<div style="padding: 8px 12px; background: ' + colors[1] + '; color: rgba(255,255,255,0.9); font-size: 14px; border-left: 4px solid transparent; cursor: pointer;">?? Posts</div>' +
            '<div style="padding: 8px 12px; background: ' + colors[2] + '; color: rgba(255,255,255,0.9); font-size: 14px; border-left: 4px solid transparent; cursor: pointer;">??? Media</div>' +
            '<div style="padding: 8px 12px; background: ' + colors[3] + '; color: rgba(255,255,255,0.9); font-size: 14px; border-left: 4px solid #007cba; cursor: pointer;">?? Pages</div>' +
            '<div style="padding: 8px 12px; background: ' + colors[0] + '; color: rgba(255,255,255,0.9); font-size: 14px; border-left: 4px solid transparent; cursor: pointer;">?? Comments</div>' +
            '<div style="padding: 8px 12px; background: ' + colors[1] + '; color: rgba(255,255,255,0.9); font-size: 14px; border-left: 4px solid transparent; cursor: pointer;">?? Appearance</div>' +
            '<div style="padding: 8px 12px; background: ' + colors[2] + '; color: rgba(255,255,255,0.9); font-size: 14px; border-left: 4px solid transparent; cursor: pointer;">?? Plugins</div>' +
            '<div style="padding: 8px 12px; background: ' + colors[3] + '; color: rgba(255,255,255,0.9); font-size: 14px; border-left: 4px solid transparent; cursor: pointer;">?? Settings</div>' +
            '</div>';
    }

    function getTemplateColors(templateId) {
        // Return color array based on template ID
        if (templateId.includes('dark')) return ['#1e1e1e', '#2d2d2d', '#646464', '#00d4ff'];
        if (templateId.includes('minimal')) return ['#2271b1', '#72aee6', '#ffffff', '#f0f0f1'];
        if (templateId.includes('content') || templateId.includes('focused')) return ['#d63638', '#ff8c00', '#0073aa', '#00a32a'];
        if (templateId.includes('classic')) return ['#23282d', '#0073aa', '#00a0d2', '#0085ba'];
        if (templateId.includes('developer')) return ['#000000', '#00ff00', '#008000', '#333333'];
        if (templateId.includes('agency')) return ['#6c5ce7', '#a29bfe', '#fd79a8', '#fdcb6e'];
        if (templateId.includes('ecommerce') || templateId.includes('commerce')) return ['#96588a', '#c9356c', '#f36e5d', '#fbb034'];
        if (templateId.includes('blogger')) return ['#ffeaa7', '#dfe6e9', '#fd79a8', '#74b9ff'];
        return ['#23282d', '#0073aa', '#ffffff', '#f1f1f1']; // default
    }

    function getTemplateColorScheme(templateId) {
        // Each template just needs 4 colors that will rotate
        var schemes = {
            'default': ['#23282d', '#0073aa', '#32373c', '#0085ba'],
            'minimal': ['#2271b1', '#72aee6', '#135e96', '#dcdcde'],
            'content-focused': ['#d63638', '#ff8c00', '#0073aa', '#00a32a'],
            'dark-mode': ['#1e1e1e', '#2d2d2d', '#646464', '#00d4ff'],
            'wordpress-classic': ['#23282d', '#0073aa', '#00a0d2', '#0085ba'],
            'developer': ['#000000', '#00ff00', '#008000', '#333333'],
            'agency': ['#6c5ce7', '#a29bfe', '#fd79a8', '#fdcb6e'],
            'ecommerce': ['#96588a', '#c9356c', '#f36e5d', '#fbb034'],
            'blogger': ['#ffeaa7', '#dfe6e9', '#fd79a8', '#74b9ff']
        };
        
        return schemes[templateId] || schemes['default'];
    }

    // Favorites system
    function initFavoritesSystem() {
        // Load favorites from localStorage
        var favorites = JSON.parse(localStorage.getItem('wamm_favorite_templates') || '[]');
        
        // Update star display
        function updateStars() {
            $('.favorite-star').each(function() {
                var templateId = $(this).data('template-id');
                if (favorites.includes(templateId)) {
                    $(this).text('?').css('color', '#ffb900');
                } else {
                    $(this).text('?').css('color', '#ddd');
                }
            });
        }
        
        // Handle star clicks
        $(document).off('click', '.favorite-star').on('click', '.favorite-star', function(e) {
            e.stopPropagation();
            var templateId = $(this).data('template-id');
            
            if (favorites.includes(templateId)) {
                // Remove from favorites
                favorites = favorites.filter(id => id !== templateId);
                $(this).text('?').css('color', '#ddd');
            } else {
                // Add to favorites
                favorites.push(templateId);
                $(this).text('?').css('color', '#ffb900');
            }
            
            localStorage.setItem('wamm_favorite_templates', JSON.stringify(favorites));
            reorganizeTemplates();
        });
        
        // Reorganize templates to show favorites first
        function reorganizeTemplates() {
            var container = $('.wamm-templates-grid').first();
            var favoriteTemplates = [];
            var regularTemplates = [];
            
            container.find('.wamm-template-card').each(function() {
                var templateId = $(this).find('.favorite-star').data('template-id');
                if (favorites.includes(templateId)) {
                    favoriteTemplates.push($(this).detach());
                } else {
                    regularTemplates.push($(this).detach());
                }
            });
            
            // Add favorites first, then regular templates
            favoriteTemplates.forEach(t => container.append(t));
            
            // Add separator if there are both favorites and regular templates
            if (favoriteTemplates.length > 0 && regularTemplates.length > 0) {
                container.append('<div style="width: 100%; border-bottom: 2px solid #ddd; margin: 20px 0; padding-bottom: 10px; font-weight: bold; color: #666;">Other Templates</div>');
            }
            
            regularTemplates.forEach(t => container.append(t));
        }
        
        // Initialize on page load
        updateStars();
        reorganizeTemplates();
    }

})(jQuery);
