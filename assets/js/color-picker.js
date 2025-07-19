(function($) {
    'use strict';

    // Debounce function for performance optimization
    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    // Initialize inline color picker functionality
    function initInlineColorPicker() {
        console.log('WMO: Initializing inline color picker functionality');
        
        // Handle color swatch clicks
        $(document).on('click', '.color-swatch', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $swatch = $(this);
            var itemId = $swatch.data('item-id');
            var currentColor = $swatch.css('background-color');
            
            console.log('WMO: Color swatch clicked for item:', itemId, 'Current color:', currentColor);
            
            // Disable sortable during color picker
            var $sortableMenu = $('#wmo-sortable-menu');
            if ($sortableMenu.hasClass('ui-sortable')) {
                $sortableMenu.sortable('disable');
            }
            
            // Create color picker input
            var $colorInput = $('<input type="text" class="inline-color-picker" value="" style="position: absolute; opacity: 0; pointer-events: none;">');
            
            // Position the input near the swatch
            $swatch.css('position', 'relative').append($colorInput);
            
            // Initialize WordPress Color Picker
            if ($.fn.wpColorPicker) {
                $colorInput.wpColorPicker({
                    defaultColor: currentColor !== 'transparent' ? currentColor : '#000000',
                    change: debounce(function(event, ui) {
                        var newColor = ui.color.toString();
                        console.log('WMO: Color changed to:', newColor);
                        
                        // Update swatch background
                        $swatch.css('background-color', newColor);
                        
                        // Save color via AJAX
                        saveInlineColor(itemId, newColor, $swatch);
                    }, 300), // Debounce for performance
                    clear: function() {
                        console.log('WMO: Color cleared');
                        // Handle color clear (set to transparent)
                        $swatch.css('background-color', 'transparent');
                        saveInlineColor(itemId, '', $swatch);
                    }
                });
                
                // Show the color picker
                $colorInput.wpColorPicker('open');
                
                // Handle color picker close
                $(document).on('click.wmoColorPicker', function(e) {
                    if (!$(e.target).closest('.wp-color-result, .wp-picker-container').length) {
                        closeColorPicker($colorInput, $sortableMenu);
                    }
                });
                
                // Handle escape key
                $(document).on('keydown.wmoColorPicker', function(e) {
                    if (e.keyCode === 27) { // Escape key
                        closeColorPicker($colorInput, $sortableMenu);
                    }
                });
                
            } else {
                console.error('WMO: WordPress Color Picker not available');
                alert('Color picker not available. Please ensure WordPress Color Picker is loaded.');
                reEnableSortable($sortableMenu);
            }
        });
    }
    
    // Function to save inline color
    function saveInlineColor(itemId, color, $swatch) {
        console.log('WMO: Saving inline color for item:', itemId, 'Color:', color);
        
        // Add loading state
        $swatch.addClass('edit-loading');
        
        // Show spinner
        var $spinner = $('<span class="edit-spinner">⏳</span>');
        $swatch.append($spinner);
        
        $.ajax({
            url: wmo_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'wmo_save_color',
                id: itemId,
                color: color,
                nonce: wmo_ajax.nonce
            },
            success: function(response) {
                console.log('WMO: Color save response:', response);
                
                if (response.success) {
                    // Show success feedback
                    showInlineFeedback($swatch, 'Color saved!', 'success');
                    console.log('WMO: Color saved successfully');
                } else {
                    // Show error feedback
                    var errorMsg = 'Error saving color';
                    if (response.data && response.data.message) {
                        errorMsg = response.data.message;
                    }
                    showInlineFeedback($swatch, errorMsg, 'error');
                    console.error('WMO: Color save failed:', response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('WMO: Color save AJAX error:', textStatus, errorThrown);
                
                // Show error feedback
                var errorMsg = 'Network error';
                if (jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.message) {
                    errorMsg = jqXHR.responseJSON.data.message;
                }
                showInlineFeedback($swatch, errorMsg, 'error');
            },
            complete: function() {
                $swatch.removeClass('edit-loading');
                $spinner.remove();
            }
        });
    }
    
    // Function to close color picker
    function closeColorPicker($colorInput, $sortableMenu) {
        console.log('WMO: Closing color picker');
        
        // Remove event listeners
        $(document).off('click.wmoColorPicker keydown.wmoColorPicker');
        
        // Remove color input
        $colorInput.remove();
        
        // Re-enable sortable
        reEnableSortable($sortableMenu);
    }
    
    // Function to re-enable sortable
    function reEnableSortable($sortableMenu) {
        if ($sortableMenu.hasClass('ui-sortable')) {
            $sortableMenu.sortable('enable');
        }
    }
    
    // Function to show inline feedback
    function showInlineFeedback($element, message, type) {
        var $feedback = $('<div class="edit-' + type + '">' + message + '</div>');
        $element.css('position', 'relative').append($feedback);
        
        // Show feedback
        setTimeout(function() {
            $feedback.addClass('show');
        }, 10);
        
        // Hide feedback after 3 seconds
        setTimeout(function() {
            $feedback.removeClass('show');
            setTimeout(function() {
                $feedback.remove();
            }, 300);
        }, 3000);
    }
    
    // Initialize when document is ready
    $(function() {
        // Initialize existing color picker functionality
        if ($.fn.wpColorPicker) {
            $('.wmo-color-field').wpColorPicker({
                change: function(event, ui) {
                    var $input = $(this);
                    var slug = $input.data('menu-slug');
                    var color = ui.color.toString();
                    var isSubmenu = $input.data('is-submenu') === true;

                    console.log('WMO: Color changed for', slug, 'to', color, 'Is submenu:', isSubmenu);

                    // Trigger a custom event that can be listened to in admin.js
                    $(document).trigger('wmoColorChanged', [slug, color, isSubmenu]);
                }
            });
        } else {
            console.error('WMO: WordPress Color Picker not available');
        }
        
        // Initialize inline color picker
        initInlineColorPicker();
    });
    
    // Initialize inline color picker when sortable is ready
    $(document).on('wmo_inline_ready', function(event, item) {
        console.log('WMO: Inline color picker ready for item:', item);
        initInlineColorPicker();
    });

})(jQuery);