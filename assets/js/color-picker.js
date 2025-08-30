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

        // Fix color picker positioning when opened
        $(document).on('click', '.wp-color-result', function() {
            var $this = $(this);
            var $container = $this.closest('.wp-picker-container');
            var $holder = $container.find('.wp-picker-holder');
            var $picker = $holder.find('.iris-picker');
            
            // Check if we're inside the problematic containers
            var $parentGroup = $this.closest('.wmo-color-group.wmo-parent-menu-group');
            var $parentWrapper = $this.closest('.wmo-menu-item-wrapper.wmo-submenu-wrapper.expanded');
            
            // Set highest z-index with !important using inline styles
            $holder.attr('style', $holder.attr('style') + '; z-index: 1000023 !important; position: absolute !important;');
            $picker.attr('style', $picker.attr('style') + '; z-index: 1000024 !important; position: relative !important;');
            
            // If inside problematic containers, force the color picker to break out
            if ($parentGroup.length || $parentWrapper.length) {
                // Move to body temporarily
                if ($holder.parent().is('body') === false) {
                    $('body').append($holder);
                }
                
                // Position relative to the button
                setTimeout(function() {
                    var buttonOffset = $this.offset();
                    var buttonHeight = $this.outerHeight();
                    
                    $holder.css({
                        'position': 'absolute',
                        'top': (buttonOffset.top + buttonHeight + 5) + 'px',
                        'left': buttonOffset.left + 'px',
                        'z-index': '1000023'
                    });
                }, 10);
            }
            
            // Check if picker would be cut off at bottom
            setTimeout(function() {
                var pickerTop = $holder.offset().top;
                var pickerHeight = $holder.outerHeight();
                var windowHeight = $(window).height();
                var scrollTop = $(window).scrollTop();
                
                if (pickerTop + pickerHeight > scrollTop + windowHeight) {
                    var newTop = scrollTop + windowHeight - pickerHeight - 20; // 20px margin
                    $holder.attr('style', $holder.attr('style') + '; top: ' + newTop + 'px !important;');
                }
            }, 10);
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
                
                // Auto-save badge
                if (slug) {
                    wmoAutoSaveBadge(slug);
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
                    } else {
                        wmoShowSavingIndicator($input, 'Error saving', 'error');
                        console.error('WMO: Color save failed:', response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    wmoShowSavingIndicator($input, 'Network error', 'error');
                    console.error('WMO: Color save AJAX error:', textStatus, errorThrown);
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
                wmoUpdateBadgePreview(slug);
                wmoAutoSaveBadge(slug);
            } else {
                wmoRemoveBadgeFromMenu(slug);
                wmoAutoSaveBadge(slug);
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
                wmoAutoSaveBadge(slug);
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
                'background-color': background || '#0073aa'
            }).show();
            console.log('WMO: Badge preview - Showing badge with text:', text);
        } else {
            $preview.hide();
            console.log('WMO: Badge preview - Hiding badge (disabled or no text)');
        }
    }

    // Auto-save badge
    function wmoAutoSaveBadge(slug) {
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
                } else {
                    console.error('WMO: Badge save failed:', response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('WMO: Badge save AJAX error:', textStatus, errorThrown);
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
         
         // Add CSS to ensure color changes are visible
         var style = document.createElement('style');
         style.textContent = `
             #adminmenu li a[style*="color"] {
                 color: inherit !important;
             }
             #adminmenu li a[style*="color:"] {
                 color: inherit !important;
             }
         `;
         document.head.appendChild(style);
         
         // Initialize color pickers
         initColorPickers();
         
         // Initialize badge functionality
         initBadgeFunctionality();
         
         // Initialize theme toggle
         initThemeToggle();

         // Initialize typography functionality
         initTypographyFunctionality();
         
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
