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

    // Auto-save timeouts storage
    var autoSaveTimeouts = {};

    // Initialize color picker functionality
    function initColorPickers() {
        console.log('WMO: Initializing WordPress color pickers');
        
        if (!$.fn.wpColorPicker) {
            console.error('WMO: WordPress Color Picker not available');
            return;
        }

        // Initialize main color fields
        $('.wmo-color-field').wpColorPicker({
            defaultColor: '#23282d',
            palettes: [
                '#23282d', '#0073aa', '#00a0d2', '#0085ba',
                '#006799', '#00b9eb', '#91c5f2', '#ddd'
            ],
                         change: debounce(function(event, ui) {
                 var $input = $(this);
                 var slug = $input.data('menu-slug');
                 var color = ui.color.toString();
                 var isSubmenu = $input.data('is-submenu') === true;

                 console.log('WMO: Color changed for', slug, 'to', color);

                 // Update input value
                 $input.val(color);
                 
                 // Apply color to menu immediately (live preview)
                 wmoApplyColorToMenu(slug, color);
                 
                 // Trigger custom event
                 $(document).trigger('wmoColorChanged', [slug, color, isSubmenu]);
                 
                 // Auto-save color
                 if (slug) {
                     wmoAutoSaveColor(slug, color, $input);
                 }
                 
                 // Auto-close picker after delay
                 setTimeout(function() {
                     $input.wpColorPicker('close');
                 }, 300);
             }, 300),
            clear: function(event, ui) {
                var $input = $(this);
                var slug = $input.data('menu-slug');
                
                console.log('WMO: Color cleared for', slug);
                $input.val('');
                
                if (slug) {
                    wmoAutoSaveColor(slug, '', $input);
                }
                
                setTimeout(function() {
                    $input.wpColorPicker('close');
                }, 300);
            }
        });

        // Initialize badge color pickers
        $('.wmo-badge-color-picker, .wmo-badge-bg-picker').wpColorPicker({
            change: debounce(function(event, ui) {
                var $input = $(this);
                var $wrapper = $input.closest('.wmo-badge-wrapper');
                var slug = $wrapper.data('menu-slug');
                
                // Fallback: try to get slug from the input itself if wrapper doesn't have it
                if (!slug) {
                    slug = $input.data('menu-slug');
                }
                
                var color = ui.color.toString();
                
                console.log('WMO: Badge color changed for', slug, 'to', color);
                console.log('WMO: Badge color - Wrapper found:', $wrapper.length > 0, 'Slug:', slug);
                
                // Update badge preview
                wmoUpdateBadgePreview(slug);
                
                // Auto-save badge - only show notification if badge is properly configured
                if (slug) {
                    var text = $wrapper.find('.wmo-badge-text').val();
                    var enabled = $wrapper.find('.wmo-badge-enable').is(':checked');
                    var showNotification = enabled && text && text.trim().length > 0;
                    wmoAutoSaveBadge(slug, showNotification);
                }
                
                setTimeout(function() {
                    $input.wpColorPicker('close');
                }, 300);
            }, 300)
        });
    }

    // Auto-save color function
    function wmoAutoSaveColor(slug, color, $input) {
        // Clear existing timeout
        if (autoSaveTimeouts[slug]) {
            clearTimeout(autoSaveTimeouts[slug]);
        }

        // Show saving indicator
        wmoShowSavingIndicator($input, 'Saving...', 'info');

        // Set new timeout
        autoSaveTimeouts[slug] = setTimeout(function() {
            $.ajax({
                url: wmo_ajax.ajax_url,
                method: 'POST',
                data: {
                    action: 'wmo_save_color',
                    id: slug,
                    color: color,
                    nonce: wmo_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        wmoShowSavingIndicator($input, 'Saved!', 'success');
                        console.log('WMO: Color saved successfully for', slug);
                        wmoShowNotification('Color settings saved! Please refresh your browser to see the changes.', 'success', $input);
                    } else {
                        wmoShowSavingIndicator($input, 'Error saving', 'error');
                        console.error('WMO: Color save failed:', response.data);
                        wmoShowNotification('Error saving color settings. Please try again.', 'error', $input);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    wmoShowSavingIndicator($input, 'Network error', 'error');
                    console.error('WMO: Color save AJAX error:', textStatus, errorThrown);
                    wmoShowNotification('Network error. Please check your connection and try again.', 'error', $input);
                }
            });
        }, 500);
    }

         // Show saving indicator
     function wmoShowSavingIndicator($input, message, type = 'info') {
         var $indicator = $input.siblings('.wmo-saving-indicator');
         
         if ($indicator.length === 0) {
             $indicator = $('<div class="wmo-saving-indicator"></div>');
             $input.parent().append($indicator);
         }
         
         $indicator.text(message).removeClass('success error info').addClass(type);
         
         if (type === 'success') {
             setTimeout(function() {
                 $indicator.fadeOut();
             }, 2000);
         }
     }

    // Badge functionality
    function initBadgeFunctionality() {
        // Badge enable/disable
        $(document).on('change', '.wmo-badge-enable', function() {
            var $wrapper = $(this).closest('.wmo-badge-wrapper');
            var slug = $wrapper.data('menu-slug');
            
            // Fallback: try to get slug from the input itself if wrapper doesn't have it
            if (!slug) {
                slug = $(this).data('menu-slug');
            }
            
            var enabled = $(this).is(':checked');
            
            console.log('WMO: Badge enable/disable - Wrapper found:', $wrapper.length > 0, 'Slug:', slug);
            
            $wrapper.find('.wmo-badge-controls').toggle(enabled);
            
            if (enabled) {
                // Apply preview immediately when controls are shown
                setTimeout(function() {
                    wmoUpdateBadgePreview(slug);
                }, 100);
                // Don't show notification for just enabling - wait for actual configuration
                wmoAutoSaveBadge(slug, false);
            } else {
                wmoRemoveBadgeFromMenu(slug);
                // Show notification when disabling (this is a final action)
                wmoAutoSaveBadge(slug, true);
            }
        });

        // Badge text input
        $(document).on('input', '.wmo-badge-text', function() {
            var $wrapper = $(this).closest('.wmo-badge-wrapper');
            var slug = $wrapper.data('menu-slug');
            
            // Fallback: try to get slug from the input itself if wrapper doesn't have it
            if (!slug) {
                slug = $(this).data('menu-slug');
            }
            
            console.log('WMO: Badge text input - Slug:', slug);
            
            clearTimeout(window.wmoBadgeTextTimeout);
            window.wmoBadgeTextTimeout = setTimeout(function() {
                wmoUpdateBadgePreview(slug);
                // Only show notification if badge is actually configured (has text)
                var text = $wrapper.find('.wmo-badge-text').val();
                var showNotification = text && text.trim().length > 0;
                wmoAutoSaveBadge(slug, showNotification);
            }, 300);
        });
    }

    // Update badge preview
    function wmoUpdateBadgePreview(slug) {
        console.log('WMO: Updating badge preview for slug:', slug);
        
        var $wrapper = $('.wmo-badge-wrapper').filter(function() {
            return $(this).data('menu-slug') === slug;
        });
        
        console.log('WMO: Badge preview - Wrapper found:', $wrapper.length > 0);
        
        if ($wrapper.length === 0) {
            console.log('WMO: Badge preview - No wrapper found for slug:', slug);
            return;
        }
        
        var text = $wrapper.find('.wmo-badge-text').val();
        var color = $wrapper.find('.wmo-badge-color-picker').val();
        var background = $wrapper.find('.wmo-badge-bg-picker').val();
        var enabled = $wrapper.find('.wmo-badge-enable').is(':checked');
        
        console.log('WMO: Badge preview - Text:', text, 'Color:', color, 'Background:', background, 'Enabled:', enabled);
        
        var $preview = $wrapper.find('.wmo-badge-preview');
        if ($preview.length === 0) {
            $preview = $('<span class="wmo-badge-preview"></span>');
            $wrapper.find('.wmo-badge-controls').append($preview);
            console.log('WMO: Badge preview - Created new preview element');
        }
        
        if (enabled && text) {
            $preview.text(text).css({
                'color': color || '#ffffff',
                'background-color': background || '#0073aa',
                'display': 'inline-block',
                'padding': '2px 6px',
                'border-radius': '3px',
                'font-size': '10px',
                'font-weight': '600',
                'text-transform': 'uppercase',
                'letter-spacing': '0.5px',
                'margin-left': '8px'
            }).show();
            console.log('WMO: Badge preview - Showing badge with text:', text);
        } else {
            $preview.hide();
            console.log('WMO: Badge preview - Hiding badge (disabled or no text)');
        }
    }

    // Auto-save badge
    function wmoAutoSaveBadge(slug, showNotification = true) {
        var $wrapper = $('.wmo-badge-wrapper').filter(function() {
            return $(this).data('menu-slug') === slug;
        });
        
        if ($wrapper.length === 0) return;
        
        var enabled = $wrapper.find('.wmo-badge-enable').is(':checked');
        var text = $wrapper.find('.wmo-badge-text').val();
        var color = $wrapper.find('.wmo-badge-color-picker').val();
        var background = $wrapper.find('.wmo-badge-bg-picker').val();
        
        $.ajax({
            url: wmo_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'wmo_save_badge',
                slug: slug,
                enabled: enabled,
                text: text,
                color: color,
                background: background,
                nonce: wmo_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    console.log('WMO: Badge saved successfully for', slug);
                    // Only show notification if requested
                    if (showNotification) {
                        var $targetElement = $('.wmo-badge-wrapper').filter(function() {
                            return $(this).data('menu-slug') === slug;
                        });
                        wmoShowNotification('Badge settings saved! Please refresh your browser to see the changes.', 'success', $targetElement);
                    }
                } else {
                    console.error('WMO: Badge save failed:', response.data);
                    // Always show error notifications
                    var $targetElement = $('.wmo-badge-wrapper').filter(function() {
                        return $(this).data('menu-slug') === slug;
                    });
                    wmoShowNotification('Error saving badge settings. Please try again.', 'error', $targetElement);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('WMO: Badge save AJAX error:', textStatus, errorThrown);
                // Always show error notifications
                var $targetElement = $('.wmo-badge-wrapper').filter(function() {
                    return $(this).data('menu-slug') === slug;
                });
                wmoShowNotification('Network error. Please check your connection and try again.', 'error', $targetElement);
            }
        });
    }

    // Remove badge from menu
    function wmoRemoveBadgeFromMenu(slug) {
        var selectors = [
            "#menu-" + slug + " > a .wp-menu-name .wmo-menu-badge",
            "#toplevel_page_" + slug + " > a .wp-menu-name .wmo-menu-badge"
        ];
        
        selectors.forEach(function(selector) {
            $(selector).remove();
        });
    }

    // Show notification popup
    function wmoShowNotification(message, type = 'info', $targetElement = null) {
        // Remove any existing notifications
        $('.wmo-notification').remove();
        
        // Create notification element
        var $notification = $('<div class="wmo-notification wmo-notification-' + type + '">' +
            '<div class="wmo-notification-content">' +
            '<span class="wmo-notification-message">' + message + '</span>' +
            '<button class="wmo-notification-close">&times;</button>' +
            '</div>' +
            '</div>');
        
        // Add to page
        $('body').append($notification);
        
        // Position notification relative to target element if provided
        if ($targetElement && $targetElement.length > 0) {
            var elementOffset = $targetElement.offset();
            var elementHeight = $targetElement.outerHeight();
            
            // Position above the element
            $notification.css({
                'position': 'absolute',
                'top': (elementOffset.top - $notification.outerHeight() - 10) + 'px',
                'left': elementOffset.left + 'px',
                'right': 'auto',
                'z-index': 999999
            });
            
            // Scroll to notification if it's not visible
            var notificationTop = elementOffset.top - $notification.outerHeight() - 10;
            if (notificationTop < $(window).scrollTop()) {
                $('html, body').animate({
                    scrollTop: notificationTop - 20
                }, 300);
            }
        } else {
            // Default positioning (top-right corner)
            $notification.css({
                'position': 'fixed',
                'top': '32px',
                'right': '20px',
                'left': 'auto',
                'z-index': 999999
            });
        }
        
        // Show notification with animation
        $notification.fadeIn(300);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
        
        // Close button functionality
        $notification.find('.wmo-notification-close').on('click', function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        console.log('WMO: Notification shown:', message, 'Type:', type, 'Target element:', $targetElement ? 'found' : 'none');
    }

    // NEW - Real-time CSS injection function
    function wmoInjectCSS(slug, color) {
        const styleId = 'wmo-color-' + slug;
        let styleElement = document.getElementById(styleId);
        
        if (!styleElement) {
            styleElement = document.createElement('style');
            styleElement.id = styleId;
            document.head.appendChild(styleElement);
        }
        
        const cssRules = `
            body #adminmenu li#menu-${slug} > a,
            body #adminmenu li#toplevel_page_${slug} > a,
            body #adminmenu li[id*='${slug}'] > a,
            body #adminmenu li[id*='${slug}'] .wp-menu-name,
            body #adminmenu li[id*='${slug}'] .wp-menu-image:before,
            body #adminmenu li[id*='${slug}'] .wp-menu-image:before { 
                color: ${color} !important; 
            }
        `;
        
        styleElement.textContent = cssRules;
        console.log('WMO: Injected CSS for', slug, 'with color', color);
    }

    // NEW - Apply color to WordPress menu (real-time)
     function wmoApplyColorToMenu(slug, color) {
         console.log('WMO: Applying color to menu for', slug, 'Color:', color);
         
        try {
            if (color) {
                wmoInjectCSS(slug, color);
            } else {
                // Remove color
                const styleId = 'wmo-color-' + slug;
                const styleElement = document.getElementById(styleId);
                if (styleElement) {
                    styleElement.remove();
                    console.log('WMO: Removed CSS for', slug);
                }
            }
        } catch (error) {
            console.error('WMO: Error applying color:', error);
            // Fallback to old method if needed
            wmoApplyColorToMenuFallback(slug, color);
        }
    }

    // Fallback function (keeping old method as backup)
    function wmoApplyColorToMenuFallback(slug, color) {
        console.log('WMO: Using fallback method for', slug);
        
        // Try direct element targeting as fallback
        const selectors = [
             '#menu-' + slug + ' > a',
             '#toplevel_page_' + slug + ' > a',
            'li[id*="' + slug + '"] > a'
         ];
         
        let found = false;
        selectors.forEach(function(selector) {
            const elements = document.querySelectorAll(selector);
             if (elements.length > 0) {
                 elements.forEach(function(element) {
                         if (color) {
                             element.style.setProperty('color', color, 'important');
                         } else {
                             element.style.removeProperty('color');
                         }
                         found = true;
                    console.log('WMO: Applied color via fallback to:', element.textContent.trim());
                 });
             }
         });
         
         if (!found) {
             console.log('WMO: Warning - No menu elements found for color application. Slug:', slug);
         }
     }

    // Theme toggle functionality
    function initThemeToggle() {
        var $themeToggle = $('#wmo-dark-mode-toggle');
        if ($themeToggle.length === 0) return;
        
        $themeToggle.on('change', function() {
            var isDarkMode = $(this).is(':checked');
            
            // Apply theme immediately
            if (isDarkMode) {
                $('body').addClass('wmo-dark-theme');
            } else {
                $('body').removeClass('wmo-dark-theme');
            }
            
            // Auto-save theme
            wmoAutoSaveTheme(isDarkMode);
        });
    }

    // Auto-save theme
    function wmoAutoSaveTheme(isDarkMode) {
        $.ajax({
            url: wmo_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'wmo_save_theme',
                dark_mode: isDarkMode,
                nonce: wmo_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    console.log('WMO: Theme saved successfully');
                } else {
                    console.error('WMO: Theme save failed:', response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('WMO: Theme save AJAX error:', textStatus, errorThrown);
            }
        });
    }

    // Typography functionality initialization
    function initTypographyFunctionality() {
        console.log('WMO: Initializing typography functionality');
        
        // Handle typography enable/disable toggle
        $(document).on('change', '.wmo-typography-enable', function() {
            var $checkbox = $(this);
            var slug = $checkbox.data('menu-slug');
            var enabled = $checkbox.is(':checked');
            var $controls = $checkbox.closest('.wmo-typography-wrapper').find('.wmo-typography-controls');
            
            console.log('WMO: Typography toggle for', slug, 'enabled:', enabled);
            
            if (enabled) {
                $controls.show();
            } else {
                $controls.hide();
                // Remove typography from menu when disabled
                wmoRemoveTypographyFromMenu(slug);
            }
            
            // Auto-save the change
            wmoAutoSaveTypography(slug);
        });
        
        // Handle typography dropdown changes
        $(document).on('change', '.wmo-typography-family, .wmo-typography-size, .wmo-typography-weight', function() {
            var $select = $(this);
            var slug = $select.data('menu-slug');
            
            console.log('WMO: Typography changed for', slug, 'Value:', $select.val());
            
            // Update preview
            wmoUpdateTypographyPreview(slug);
            
            // Apply to actual menu immediately (live preview)
            wmoApplyTypographyToMenu(slug);
            
            // Debounced auto-save
            clearTimeout(window.wmoTypographyTimeout);
            window.wmoTypographyTimeout = setTimeout(function() {
                wmoAutoSaveTypography(slug);
            }, 500);
        });
        
        // Apply existing typography on page load
        setTimeout(function() {
            console.log('WMO: Applying existing typography on page load');
            $('.wmo-typography-enable:checked').each(function() {
                var slug = $(this).data('menu-slug');
                console.log('WMO: Found enabled typography for:', slug);
                wmoApplyTypographyToMenu(slug);
            });
        }, 1000);
    }
    
    // Update typography preview
    function wmoUpdateTypographyPreview(slug) {
        var $wrapper = $('.wmo-typography-wrapper').filter(function() {
            return $(this).find('[data-menu-slug="' + slug + '"]').length > 0;
        });
        
        var $preview = $wrapper.find('.wmo-typography-sample[data-menu-slug="' + slug + '"]');
        var fontFamily = $wrapper.find('.wmo-typography-family[data-menu-slug="' + slug + '"]').val();
        var fontSize = $wrapper.find('.wmo-typography-size[data-menu-slug="' + slug + '"]').val();
        var fontWeight = $wrapper.find('.wmo-typography-weight[data-menu-slug="' + slug + '"]').val();
        
        // Apply typography styles to preview
        var styles = {};
        if (fontFamily) styles['font-family'] = fontFamily;
        if (fontSize) styles['font-size'] = fontSize;
        if (fontWeight) styles['font-weight'] = fontWeight;
        
        $preview.css(styles);
        
        console.log('WMO: Updated typography preview for', slug, 'with styles:', styles);
    }
    
    // Apply typography to WordPress menu
    function wmoApplyTypographyToMenu(slug) {
        var $wrapper = $('.wmo-typography-wrapper').filter(function() {
            return $(this).find('[data-menu-slug="' + slug + '"]').length > 0;
        });
        
        var enabled = $wrapper.find('.wmo-typography-enable[data-menu-slug="' + slug + '"]').is(':checked');
        if (!enabled) {
            wmoRemoveTypographyFromMenu(slug);
            return;
        }
        
        var fontFamily = $wrapper.find('.wmo-typography-family[data-menu-slug="' + slug + '"]').val();
        var fontSize = $wrapper.find('.wmo-typography-size[data-menu-slug="' + slug + '"]').val();
        var fontWeight = $wrapper.find('.wmo-typography-weight[data-menu-slug="' + slug + '"]').val();
        
        console.log('WMO: Applying typography to menu for', slug, '- Family:', fontFamily, 'Size:', fontSize, 'Weight:', fontWeight);
        
        // More specific selectors to target only the exact menu item
        var selectors = [
            '#menu-' + slug + ' > a',                    // Standard WordPress menu format
            '#toplevel_page_' + slug + ' > a',           // Plugin pages
            'li[id="menu-' + slug + '"] > a',           // Alternative format
            'li[id="toplevel_page_' + slug + '"] > a'   // Plugin page format
        ];
        
        var found = false;
        selectors.forEach(function(selector) {
            var elements = document.querySelectorAll(selector);
            console.log('WMO: Trying selector:', selector, 'Found elements:', elements.length);
            elements.forEach(function(element) {
                if (element) {
                    // Apply typography styles with !important for higher priority
                    if (fontFamily) element.style.setProperty('font-family', fontFamily, 'important');
                    if (fontSize) element.style.setProperty('font-size', fontSize, 'important');
                    if (fontWeight) element.style.setProperty('font-weight', fontWeight, 'important');
                    
                    found = true;
                    console.log('WMO: Applied typography to element:', element, 'Text:', element.textContent.trim());
                }
            });
        });
        
        // Fallback: try exact text matching (more precise)
        if (!found) {
            console.log('WMO: No elements found with selectors, trying exact text matching');
            
            $('#adminmenu > li > a').each(function() {
                var $link = $(this);
                var linkText = $link.text().trim().toLowerCase();
                var slugText = slug.replace(/-/g, ' ').toLowerCase();
                var slugTextAlt = slug.replace(/-/g, '').toLowerCase();
                
                // More precise matching - check if the text exactly matches or is very close
                var isExactMatch = linkText === slugText || 
                                  linkText === slugTextAlt ||
                                  linkText.includes(slugText) && linkText.length <= slugText.length + 5;
                
                console.log('WMO: Checking link text for typography:', linkText, 'against slug:', slugText, 'Exact match:', isExactMatch);
                
                if (isExactMatch) {
                    var element = this;
                    if (fontFamily) element.style.setProperty('font-family', fontFamily, 'important');
                    if (fontSize) element.style.setProperty('font-size', fontSize, 'important');
                    if (fontWeight) element.style.setProperty('font-weight', fontWeight, 'important');
                    
                    console.log('WMO: Applied typography via exact text matching to:', element, 'Text:', element.textContent.trim());
                    found = true;
                    return false; // Break the loop after finding the first match
                }
            });
        }
        
        if (!found) {
            console.log('WMO: Warning - No menu elements found for typography application');
        }
    }
    
    // Remove typography from WordPress menu
    function wmoRemoveTypographyFromMenu(slug) {
        console.log('WMO: Removing typography from menu for', slug);
        
        var selectors = [
            '#menu-' + slug + ' > a',
            '#toplevel_page_' + slug + ' > a',
            'li[id="menu-' + slug + '"] > a',
            '#adminmenu li[id*="' + slug + '"] > a'
        ];
        
        selectors.forEach(function(selector) {
            var elements = document.querySelectorAll(selector);
            elements.forEach(function(element) {
                if (element) {
                    // Remove typography styles
                    element.style.removeProperty('font-family');
                    element.style.removeProperty('font-size');
                    element.style.removeProperty('font-weight');
                }
            });
        });
    }
    
         // Initialize icon system functionality
     function initIconSystemFunctionality() {
         console.log('WMO: Initializing icon system functionality');
         
         // Toggle icon settings
         $(document).on('change', '.enable-custom-icon', function() {
             console.log('WMO: Icon enable/disable clicked');
             var $settings = $(this).closest('.icon-section').find('.icon-settings');
             $settings.toggle($(this).is(':checked'));
         });
         
         // Switch between emoji and dashicon
         $(document).on('change', '.icon-type-selector', function() {
             console.log('WMO: Icon type selector changed');
             var type = $(this).val();
             var $section = $(this).closest('.icon-settings');
             
             if (type === 'emoji') {
                 $section.find('.emoji-picker').show();
                 $section.find('.dashicon-picker').hide();
             } else {
                 $section.find('.emoji-picker').hide();
                 $section.find('.dashicon-picker').show();
             }
         });
         
         // Search functionality
         $(document).on('keyup', '.icon-search-input', function() {
             var search = $(this).val().toLowerCase();
             var $picker = $(this).closest('.icon-settings').find('.emoji-picker:visible, .dashicon-picker:visible');
             
             if ($picker.hasClass('dashicon-picker')) {
                 // Search dashicons
                 $picker.find('.dashicon-option').each(function() {
                     var iconName = $(this).data('dashicon').toLowerCase();
                     var category = $(this).data('category').toLowerCase();
                     var matches = iconName.includes(search) || category.includes(search) || search.length === 0;
                     $(this).toggle(matches);
                 });
             } else {
                 // Search emojis
                 $picker.find('.emoji-option').each(function() {
                     var category = $(this).data('category').toLowerCase();
                     var matches = category.includes(search) || search.length === 0;
                     $(this).toggle(matches);
                 });
             }
         });
         
         // Select emoji - using direct event binding for better reliability
         $(document).off('click', '.emoji-option').on('click', '.emoji-option', function(e) {
             e.preventDefault();
             e.stopPropagation();
             console.log('WMO: Emoji option clicked:', $(this).data('emoji'));
             
             var emoji = $(this).data('emoji');
             var $section = $(this).closest('.icon-settings');
             var $iconSection = $(this).closest('.icon-section');
             var $menuWrapper = $iconSection.closest('.wmo-menu-item-wrapper');
             var menuSlug = $menuWrapper.data('original-slug'); // Use the actual menu slug
             
             // Fallback: if original-slug is not available, try the checkbox data attribute
             if (!menuSlug) {
                 menuSlug = $iconSection.find('.enable-custom-icon').data('menu-slug');
             }
             
             console.log('WMO: Menu slug found:', menuSlug);
             
             // Highlight selected
             $section.find('.emoji-option').removeClass('selected').css('background', 'transparent');
             $(this).addClass('selected').css('background', '#0073aa');
             
             // Update preview
             $section.find('.preview-icon').html(emoji + ' ');
             $section.find('.selected-emoji').val(emoji);
             
             // Save via AJAX
             saveIconChoice(menuSlug, 'emoji', emoji);
             
             // Apply to actual menu immediately
             applyIconToMenu(menuSlug, 'emoji', emoji);
         });
         
         // Select dashicon - using direct event binding for better reliability
         $(document).off('click', '.dashicon-option').on('click', '.dashicon-option', function(e) {
             e.preventDefault();
             e.stopPropagation();
             console.log('WMO: Dashicon option clicked:', $(this).data('dashicon'));
             
             var dashicon = $(this).data('dashicon');
             var $section = $(this).closest('.icon-settings');
             var $iconSection = $(this).closest('.icon-section');
             var $menuWrapper = $iconSection.closest('.wmo-menu-item-wrapper');
             var menuSlug = $menuWrapper.data('original-slug'); // Use the actual menu slug
             
             // Fallback: if original-slug is not available, try the checkbox data attribute
             if (!menuSlug) {
                 menuSlug = $iconSection.find('.enable-custom-icon').data('menu-slug');
             }
             
             console.log('WMO: Menu slug found:', menuSlug);
             
             // Highlight selected
             $section.find('.dashicon-option').removeClass('selected').css('background', 'transparent');
             $(this).addClass('selected').css('background', '#0073aa');
             
             // Update preview
             $section.find('.preview-icon').html('<span class="dashicons ' + dashicon + '"></span> ');
             $section.find('.selected-dashicon').val(dashicon);
             
             // Save via AJAX
             saveIconChoice(menuSlug, 'dashicon', dashicon);
             
             // Apply to actual menu immediately
             applyIconToMenu(menuSlug, 'dashicon', dashicon);
         });
         
         function saveIconChoice(menuSlug, type, value) {
             console.log('WMO: Saving icon choice:', menuSlug, type, value);
             
             if (!menuSlug) {
                 console.error('WMO: No menu slug found for icon save');
                 return;
             }
             
             if (typeof wmo_ajax === 'undefined') {
                 console.error('WMO: wmo_ajax object not defined');
                 return;
             }
             
             $.ajax({
                 url: wmo_ajax.ajax_url,
                 type: 'POST',
                 data: {
                     action: 'wmo_save_icon',
                     menu_id: menuSlug,
                     icon_type: type,
                     icon_value: value,
                     nonce: wmo_ajax.nonce
                 },
                 success: function(response) {
                     console.log('WMO: Icon save response:', response);
                     if (response.success) {
                         console.log('WMO: Icon saved successfully');
                     } else {
                         console.error('WMO: Error saving icon:', response.data);
                     }
                 },
                 error: function(xhr, status, error) {
                     console.error('WMO: AJAX error saving icon:', status, error);
                     console.error('WMO: Response:', xhr.responseText);
                 }
             });
         }
         
         function applyIconToMenu(menuSlug, type, value) {
             console.log('WMO: Applying icon to menu:', menuSlug, type, value);
             
             if (!menuSlug) {
                 console.error('WMO: No menu slug found for icon application');
                 return;
             }
             
             // Find the menu item in the WordPress admin menu
             var $menuItem = $('#adminmenu').find('[id*="' + menuSlug + '"]').first();
             
             if ($menuItem.length === 0) {
                 // Try alternative selectors
                 $menuItem = $('#adminmenu').find('a[href*="' + menuSlug + '"]').closest('li');
             }
             
             if ($menuItem.length === 0) {
                 // Try more generic selectors
                 $menuItem = $('#adminmenu li').filter(function() {
                     return $(this).text().toLowerCase().includes(menuSlug.replace(/-/g, ' ').toLowerCase());
                 }).first();
             }
             
             if ($menuItem.length > 0) {
                 var $iconWrapper = $menuItem.find('.wp-menu-image');
                 
                 if ($iconWrapper.length > 0) {
                     // Create the new icon HTML
                     var iconHtml = '';
                     if (type === 'emoji') {
                         iconHtml = '<span class="custom-menu-icon" style="font-size: 20px;">' + value + '</span>';
                     } else {
                         iconHtml = '<span class="dashicons ' + value + '" style="font-size: 20px;"></span>';
                     }
                     
                     // IMPORTANT: Clear ALL existing content first
                     $iconWrapper.empty();
                     
                     // Remove any existing dashicon classes from the wrapper
                     $iconWrapper.removeClass(function(index, className) {
                         return (className.match(/(^|\s)dashicons-\S+/g) || []).join(' ');
                     });
                     $iconWrapper.removeClass('dashicons');
                     
                     // Add custom icon class to help with CSS targeting
                     $iconWrapper.addClass('has-custom-icon');
                     
                     // Add the new icon
                     $iconWrapper.html(iconHtml);
                     
                     console.log('WMO: Icon applied to menu item:', $menuItem);
                 } else {
                     console.log('WMO: Could not find icon wrapper in menu item');
                 }
             } else {
                 console.log('WMO: Could not find menu item for slug:', menuSlug);
                 console.log('WMO: Available menu items:', $('#adminmenu li').map(function() {
                     return $(this).attr('id') || $(this).text().trim();
                 }).get());
             }
         }
         
         // Apply saved icons on page load
         function applySavedIcons() {
             console.log('WMO: Applying saved icons on page load');
             
             // Get saved icons from localized script
             if (typeof wmo_saved_icons !== 'undefined' && wmo_saved_icons) {
                 console.log('WMO: Found saved icons:', wmo_saved_icons);
                 
                 $.each(wmo_saved_icons, function(menuId, iconData) {
                     console.log('WMO: Applying saved icon for', menuId, ':', iconData);
                     applyIconToMenu(menuId, iconData.type, iconData.value);
                 });
             } else {
                 console.log('WMO: No saved icons found');
             }
         }
         
         // Apply saved icons after page loads
         setTimeout(function() {
             applySavedIcons();
         }, 1000);
         
         // Debug: Log available icon options on page load
         setTimeout(function() {
             console.log('WMO: Icon system initialized. Available emoji options:', $('.emoji-option').length);
             console.log('WMO: Available dashicon options:', $('.dashicon-option').length);
             console.log('WMO: Icon sections found:', $('.icon-section').length);
         }, 1000);
     }

     // Auto-save typography data
     var typographyAutoSaveTimeouts = {};
     function wmoAutoSaveTypography(slug) {
        // Clear any existing timeout for this slug
        if (typographyAutoSaveTimeouts[slug]) {
            clearTimeout(typographyAutoSaveTimeouts[slug]);
        }
        
        // Get the wrapper and input for saving indicator
        var $wrapper = $('.wmo-typography-wrapper').filter(function() {
            return $(this).find('[data-menu-slug="' + slug + '"]').length > 0;
        });
        var $input = $wrapper.find('.wmo-typography-family[data-menu-slug="' + slug + '"]');
        
        // Show saving indicator - try multiple approaches
        console.log('WMO: Showing saving indicator for input:', $input.length ? 'found' : 'not found');
        if ($input.length > 0) {
            wmoShowSavingIndicator($input, 'Saving...', 'info');
        } else {
            // Try to find any input in the wrapper for the saving indicator
            var $anyInput = $wrapper.find('select, input').first();
            if ($anyInput.length > 0) {
                console.log('WMO: Using fallback input for saving indicator');
                wmoShowSavingIndicator($anyInput, 'Saving...', 'info');
            } else {
                console.log('WMO: Could not find any input for saving indicator');
            }
        }
        
        // Set a new timeout to save after 500ms delay
        typographyAutoSaveTimeouts[slug] = setTimeout(function() {
            console.log('WMO: Auto-saving typography for', slug);
            
            var enabled = $wrapper.find('.wmo-typography-enable[data-menu-slug="' + slug + '"]').is(':checked');
            var fontFamily = $wrapper.find('.wmo-typography-family[data-menu-slug="' + slug + '"]').val();
            var fontSize = $wrapper.find('.wmo-typography-size[data-menu-slug="' + slug + '"]').val();
            var fontWeight = $wrapper.find('.wmo-typography-weight[data-menu-slug="' + slug + '"]').val();
            
            $.ajax({
                url: wmo_ajax.ajax_url,
                method: 'POST',
                data: {
                    action: 'wmo_save_typography',
                    slug: slug,
                    enabled: enabled ? 1 : 0,
                    font_family: fontFamily,
                    font_size: fontSize,
                    font_weight: fontWeight,
                    nonce: wmo_ajax.nonce
                },
                success: function(response) {
                    console.log('WMO: Typography auto-save response:', response);
                    if (response.success) {
                        wmoShowSavingIndicator($input, 'Saved!', 'success');
                        console.log('WMO: Typography auto-saved successfully');
                    } else {
                        wmoShowSavingIndicator($input, 'Error saving', 'error');
                        console.error('WMO: Typography auto-save failed:', response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    wmoShowSavingIndicator($input, 'Network error', 'error');
                    console.error('WMO: Typography auto-save AJAX error:', textStatus, errorThrown);
                }
            });
        }, 500); // 500ms delay for debouncing
    }

         // Initialize everything when document is ready
     $(document).ready(function() {
         console.log('WMO: Color picker script loaded');
         
                   // Add CSS to ensure color changes are visible and notification styling
         var style = document.createElement('style');
         style.textContent = `
             #adminmenu li a[style*="color"] {
                 color: inherit !important;
             }
             #adminmenu li a[style*="color:"] {
                 color: inherit !important;
             }
              
                             /* Notification Styles */
               .wmo-notification {
                   position: fixed;
                   top: 32px;
                   right: 20px;
                   z-index: 999999;
                   max-width: 400px;
                   background: #fff;
                   border-left: 4px solid #0073aa;
                   box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                   border-radius: 4px;
                   display: none;
                   font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                   /* Ensure notification is visible when positioned absolutely */
                   min-width: 300px;
               }
              
              .wmo-notification-success {
                  border-left-color: #46b450;
              }
              
              .wmo-notification-error {
                  border-left-color: #dc3232;
              }
              
              .wmo-notification-info {
                  border-left-color: #0073aa;
              }
              
              .wmo-notification-content {
                  padding: 12px 16px;
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
              }
              
              .wmo-notification-message {
                  color: #23282d;
                  font-size: 14px;
                  line-height: 1.4;
                  margin-right: 10px;
              }
              
              .wmo-notification-close {
                  background: none;
                  border: none;
                  color: #666;
                  font-size: 18px;
                  cursor: pointer;
                  padding: 0;
                  width: 20px;
                  height: 20px;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  border-radius: 50%;
                  transition: background-color 0.2s;
              }
              
              .wmo-notification-close:hover {
                  background-color: #f0f0f0;
                  color: #333;
              }
              
              /* WordPress admin bar adjustment */
              .admin-bar .wmo-notification {
                  top: 46px;
              }
         `;
         document.head.appendChild(style);
         
         // Initialize color pickers
         initColorPickers();
         
         // Initialize badge functionality
         initBadgeFunctionality();
          
          // Initialize existing badge previews on page load
          setTimeout(function() {
              console.log('WMO: Initializing existing badge previews on page load');
              $('.wmo-badge-enable:checked').each(function() {
                  var $wrapper = $(this).closest('.wmo-badge-wrapper');
                  var slug = $wrapper.data('menu-slug');
                  if (slug) {
                      console.log('WMO: Found enabled badge for:', slug);
                      wmoUpdateBadgePreview(slug);
                  }
              });
          }, 1000);
         
         // Initialize theme toggle
         initThemeToggle();

         // Initialize typography functionality
         initTypographyFunctionality();
         
         // Initialize icon system functionality
         initIconSystemFunctionality();
         
         // Apply existing colors on page load with longer delay
         setTimeout(function() {
             console.log('WMO: Applying existing colors on page load');
             $('.wmo-color-field').each(function() {
                 var $input = $(this);
                 var slug = $input.data('menu-slug');
                 var color = $input.val();
                 if (slug && color) {
                     console.log('WMO: Found existing color for:', slug, 'Color:', color);
                     wmoApplyColorToMenu(slug, color);
                 }
             });
         }, 2000); // Increased delay to ensure menu is loaded
         
         // Handle color swatch clicks (if any)
         $(document).on('click', '.color-swatch', function(e) {
             e.preventDefault();
             var $swatch = $(this);
             var $input = $swatch.siblings('.wmo-color-field');
             if ($input.length) {
                 $input.wpColorPicker('open');
             }
         });

         
     });

    

})(jQuery);
