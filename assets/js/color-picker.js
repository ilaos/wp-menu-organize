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

    // Notification system for showing "Saved" messages
    function showNotification(message, type = 'success') {
        // Remove any existing notifications
        $('.wmo-notification').remove();
        
        // Create notification element
        var $notification = $('<div class="wmo-notification wmo-notification-' + type + '">' + message + '</div>');
        
        // Add to body
        $('body').append($notification);
        
        // Show notification
        setTimeout(function() {
            $notification.addClass('show');
        }, 100);
        
        // Auto-hide after 3 seconds
        setTimeout(function() {
            $notification.removeClass('show');
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
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
        console.log('WMO: Initializing main color fields, found:', $('.wmo-color-field').length);
        $('.wmo-color-field').each(function(index) {
            console.log('WMO: Color field', index, 'ID:', this.id, 'Is submenu:', $(this).data('is-submenu'));
        });
        
        $('.wmo-color-field').wpColorPicker({
            defaultColor: '#23282d',
            change: debounce(function(event, ui) {
                var $input = $(this);
                var slug = $input.data('menu-slug');
                var color = ui.color.toString();
                var isSubmenu = $input.data('is-submenu') === true;

                console.log('WMO: Color changed for', slug, 'to', color, 'Is submenu:', isSubmenu);

                // Update input value
                $input.val(color);
                
                // Apply color to menu immediately (live preview)
                wmoApplyColorToMenu(slug, color);
                
                // Trigger custom event
                $(document).trigger('wmoColorChanged', [slug, color, isSubmenu]);
                
                // Auto-save color
                if (slug) {
                    wmoAutoSaveColor(slug, color, $input);
                    // Show saved notification
                    showNotification('Color saved successfully!', 'success');
                }
                
                // Auto-close picker after delay
                console.log('WMO: Attempting to auto-close color picker for', slug);
                setTimeout(function() {
                    // Force close using multiple methods to ensure it works
                    try {
                        // Method 1: WordPress API
                        if ($input.wpColorPicker && typeof $input.wpColorPicker === 'function') {
                            $input.wpColorPicker('close');
                            console.log('WMO: Successfully closed color picker for', slug, 'using wpColorPicker method');
                        }
                    } catch (error) {
                        console.log('WMO: wpColorPicker method failed for', slug, ':', error);
                    }
                    
                    // Method 2: Always use fallback to ensure closure
                    setTimeout(function() {
                        $('.wp-picker-holder').hide();
                        $('.wp-color-result').removeClass('wp-picker-open');
                        console.log('WMO: Applied fallback close method for', slug);
                    }, 100);
                    
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

        // Initialize background color fields (WordPress color picker)
        console.log('WMO: Initializing WordPress color pickers for background colors...');
        console.log('WMO: Total background color fields found:', $('.wmo-background-color-field').length);
        $('.wmo-background-color-field').each(function(index) {
            console.log('WMO: Background color field', index, 'ID:', this.id, 'Is submenu:', $(this).data('is-submenu'), 'Value:', $(this).val());
        });
        
            // Initialize background color fields with WordPress color picker
    $('.wmo-background-color-field').wpColorPicker({
        defaultColor: '#000000',
        change: debounce(function(event, ui) {
            var $input = $(this);
            var slug = $input.data('menu-slug');
            var color = ui.color.toString();
            var isSubmenu = $input.data('is-submenu') === true;

            console.log('WMO: Background color changed for', slug, 'to', color);

            // Update color swatch and preview
            wmoUpdateBackgroundColorPreview($input, color);
            
            // Apply background color to menu immediately (live preview)
            wmoApplyBackgroundColorToMenu(slug, color);
            
            // Trigger custom event
            $(document).trigger('wmoBackgroundColorChanged', [slug, color, isSubmenu]);
            
            // Auto-save background color
            if (slug) {
                wmoAutoSaveBackgroundColor(slug, color, $input);
                // Show saved notification
                showNotification('Background color saved successfully!', 'success');
            }
            
            // Auto-close picker after delay
            setTimeout(function() {
                // Force close using multiple methods to ensure it works
                try {
                    // Method 1: WordPress API
                    if ($input.wpColorPicker && typeof $input.wpColorPicker === 'function') {
                        $input.wpColorPicker('close');
                        console.log('WMO: Successfully closed background color picker for', slug, 'using wpColorPicker method');
                    }
                } catch (error) {
                    console.log('WMO: wpColorPicker method failed for', slug, ':', error);
                }
                
                // Method 2: Always use fallback to ensure closure
                setTimeout(function() {
                    $('.wp-picker-holder').hide();
                    $('.wp-color-result').removeClass('wp-picker-open');
                    console.log('WMO: Applied fallback close method for background color picker', slug);
                }, 100);
                
            }, 300);
        }, 300),
        clear: function(event, ui) {
            var $input = $(this);
            var slug = $input.data('menu-slug');
            
            console.log('WMO: Background color cleared for', slug);
            $input.val('');
            
            // Update color swatch and preview
            wmoUpdateBackgroundColorPreview($input, '');
            
            if (slug) {
                wmoAutoSaveBackgroundColor(slug, '', $input);
            }
            
            setTimeout(function() {
                // Force close using multiple methods to ensure it works
                try {
                    // Method 1: WordPress API
                    if ($input.wpColorPicker && typeof $input.wpColorPicker === 'function') {
                        $input.wpColorPicker('close');
                        console.log('WMO: Successfully closed background color picker (clear) for', slug, 'using wpColorPicker method');
                    }
                } catch (error) {
                    console.log('WMO: wpColorPicker method failed for', slug, ':', error);
                }
                
                // Method 2: Always use fallback to ensure closure
                setTimeout(function() {
                    $('.wp-picker-holder').hide();
                    $('.wp-color-result').removeClass('wp-picker-open');
                    console.log('WMO: Applied fallback close method for background color picker (clear)', slug);
                }, 100);
                
            }, 300);
        }
    });
    
    // Initialize quick color buttons
    $('.wmo-quick-color').on('click', function() {
        var $button = $(this);
        var color = $button.data('color');
        var $section = $button.closest('.wmo-background-color-section');
        var $input = $section.find('.wmo-background-color-field');
        var slug = $input.data('menu-slug');
        
        console.log('WMO: Quick color selected:', color, 'for slug:', slug);
        
        // Update the color picker
        $input.wpColorPicker('color', color);
        
        // Update color swatch and preview
        wmoUpdateBackgroundColorPreview($input, color);
        
        // Apply background color to menu immediately
        wmoApplyBackgroundColorToMenu(slug, color);
        
        // Auto-save background color
        if (slug) {
            wmoAutoSaveBackgroundColor(slug, color, $input);
        }
        
        // Visual feedback
        $button.addClass('wmo-quick-color-active');
        setTimeout(function() {
            $button.removeClass('wmo-quick-color-active');
        }, 200);
    });
        

        
        // Debug: Add click handler for background color fields
        $(document).on('click', '.wmo-background-color-field', function() {
            console.log('WMO: Background color field clicked:', this.id);
            console.log('WMO: Field value:', $(this).val());
            console.log('WMO: Field has wpColorPicker:', $(this).hasClass('wp-color-picker'));
            
            // Set up a watcher to see if the value changes
            var $field = $(this);
            var originalValue = $field.val();
            
            setTimeout(function() {
                var newValue = $field.val();
                if (newValue !== originalValue) {
                    console.log('WMO: Field value changed from', originalValue, 'to', newValue);
                } else {
                    console.log('WMO: Field value did not change, still:', originalValue);
                }
            }, 1000);
        });
        
        // Debug: Check for any element with the class
        $(document).on('click', '[class*="background-color"]', function() {
            console.log('WMO: Any background-color element clicked:', this.className);
        });
        
        // Debug: Check document ready state
        console.log('WMO: Document ready state:', document.readyState);
        console.log('WMO: jQuery version:', $.fn.jquery);
        console.log('WMO: wpColorPicker available:', typeof $.fn.wpColorPicker);
        
        // Debug: Check all input fields on the page
        console.log('WMO: Total input fields on page:', $('input').length);
        console.log('WMO: Input fields with "color" in class:', $('input[class*="color"]').length);
        $('input[class*="color"]').each(function() {
            console.log('WMO: Color-related input found:', this.id, 'Class:', this.className);
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

    // Auto-save background color function
    function wmoAutoSaveBackgroundColor(slug, color, $input) {
        // Clear existing timeout
        if (autoSaveTimeouts[slug + '_bg']) {
            clearTimeout(autoSaveTimeouts[slug + '_bg']);
        }

        // Show saving indicator
        wmoShowSavingIndicator($input, 'Saving...', 'info');

        // Set new timeout
        autoSaveTimeouts[slug + '_bg'] = setTimeout(function() {
            $.ajax({
                url: wmo_ajax.ajax_url,
                method: 'POST',
                data: {
                    action: 'wmo_save_background_color',
                    id: slug,
                    color: color,
                    nonce: wmo_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        wmoShowSavingIndicator($input, 'Saved!', 'success');
                        console.log('WMO: Background color saved successfully for', slug);
                    } else {
                        wmoShowSavingIndicator($input, 'Error saving', 'error');
                        console.error('WMO: Background color save failed:', response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    wmoShowSavingIndicator($input, 'Network error', 'error');
                    console.error('WMO: Background color save AJAX error:', textStatus, errorThrown);
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
            body #adminmenu li[id='menu-${slug}'] > a,
            body #adminmenu li[id='toplevel_page_${slug}'] > a,
            body #adminmenu li#menu-${slug} .wp-menu-name,
            body #adminmenu li#toplevel_page_${slug} .wp-menu-name,
            body #adminmenu li#menu-${slug} .wp-menu-image:before,
            body #adminmenu li#toplevel_page_${slug} .wp-menu-image:before { 
                color: ${color} !important; 
            }
        `;
        
        styleElement.textContent = cssRules;
        console.log('WMO: Injected CSS for', slug, 'with color', color);
        console.log('WMO: CSS Rules:', cssRules);
        
        // Debug: Check if elements exist
        const selectors = [
            `#menu-${slug}`,
            `#toplevel_page_${slug}`,
            `li[id='menu-${slug}']`,
            `li[id='toplevel_page_${slug}']`
        ];
        
        selectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            console.log(`WMO: Selector "${selector}" found ${elements.length} elements:`, elements);
        });
    }

    // NEW - Apply color to WordPress menu (real-time)
    function wmoApplyColorToMenu(slug, color) {
        console.log('WMO: Applying color to menu for', slug, 'Color:', color);
        console.log('WMO: Slug type:', typeof slug, 'Slug value:', slug);
        
        try {
            if (color) {
                // First, try to apply to parent menu items (existing functionality)
                wmoInjectCSS(slug, color);
                
                // Then, handle submenu items by text content
                wmoApplyColorToSubmenuByText(slug, color);
            } else {
                // Remove color from parent menu items
                const styleId = 'wmo-color-' + slug;
                const styleElement = document.getElementById(styleId);
                if (styleElement) {
                    styleElement.remove();
                    console.log('WMO: Removed CSS for', slug);
                }
                
                // Remove color from submenu items
                wmoRemoveColorFromSubmenuByText(slug);
            }
        } catch (error) {
            console.error('WMO: Error applying color:', error);
            // Fallback to old method if needed
            wmoApplyColorToMenuFallback(slug, color);
        }
    }

    // NEW - Apply color to submenu items by text content
    function wmoApplyColorToSubmenuByText(slug, color) {
        console.log('WMO: Applying color to submenu by text for', slug, 'Color:', color);
        
        // Find submenu items by text content
        const submenuLinks = document.querySelectorAll('#adminmenu .wp-submenu li a');
        let found = false;
        
        console.log('WMO: Total submenu links found:', submenuLinks.length);
        
        submenuLinks.forEach((link, index) => {
            const linkText = link.textContent.trim();
            console.log(`WMO: Submenu link ${index}: "${linkText}"`);
            
            // Enhanced matching logic with common submenu patterns
            const slugVariations = [
                slug.toLowerCase(),
                slug.replace(/-/g, ' ').toLowerCase(),
                slug.replace(/-/g, '').toLowerCase(),
                slug.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()).toLowerCase()
            ];
            
            // Add common submenu patterns
            if (slug === 'home') {
                slugVariations.push('dashboard', 'main', 'overview');
            }
            if (slug === 'dashboard') {
                slugVariations.push('home', 'main', 'overview');
            }
            
            const linkTextLower = linkText.toLowerCase();
            const isMatch = slugVariations.some(variation => 
                linkTextLower === variation || 
                linkTextLower.includes(variation) ||
                variation.includes(linkTextLower)
            );
            
            if (isMatch) {
                link.style.setProperty('color', color, 'important');
                found = true;
                console.log('WMO: ✅ MATCH FOUND! Applied submenu color', color, 'to', linkText);
                console.log('WMO: Matching variations:', slugVariations);
            }
        });
        
        if (!found) {
            console.log('WMO: ❌ No submenu item found with text for slug:', slug);
            console.log('WMO: Tried variations:', slugVariations);
            
            // Debug: Show all available submenu texts
            console.log('WMO: Available submenu texts:');
            submenuLinks.forEach((link, index) => {
                if (index < 10) { // Only show first 10 to avoid spam
                    console.log(`  ${index}: "${link.textContent.trim()}"`);
                }
            });
        }
    }

    // NEW - Remove color from submenu items by text content
    function wmoRemoveColorFromSubmenuByText(slug) {
        console.log('WMO: Removing color from submenu by text for', slug);
        
        // Find submenu items by text content and remove color
        const submenuLinks = document.querySelectorAll('#adminmenu .wp-submenu li a');
        
        submenuLinks.forEach(link => {
            const linkText = link.textContent.trim();
            // Use the same enhanced matching logic
            const slugVariations = [
                slug.toLowerCase(),
                slug.replace(/-/g, ' ').toLowerCase(),
                slug.replace(/-/g, '').toLowerCase(),
                slug.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()).toLowerCase()
            ];
            
            // Add common submenu patterns
            if (slug === 'home') {
                slugVariations.push('dashboard', 'main', 'overview');
            }
            if (slug === 'dashboard') {
                slugVariations.push('home', 'main', 'overview');
            }
            
            const linkTextLower = linkText.toLowerCase();
            const isMatch = slugVariations.some(variation => 
                linkTextLower === variation || 
                linkTextLower.includes(variation) ||
                variation.includes(linkTextLower)
            );
            
            if (isMatch) {
                link.style.removeProperty('color');
                console.log('WMO: Removed submenu color from', linkText);
            }
        });
    }

    // NEW - Apply background color to submenu items by text content
    function wmoApplyBackgroundColorToSubmenuByText(slug, color) {
        console.log('WMO: Applying background color to submenu by text for', slug, 'Color:', color);
        
        // Find submenu items by text content
        const submenuLinks = document.querySelectorAll('#adminmenu .wp-submenu li a');
        let found = false;
        
        submenuLinks.forEach((link, index) => {
            const linkText = link.textContent.trim();
            
            // Enhanced matching logic with common submenu patterns
            const slugVariations = [
                slug.toLowerCase(),
                slug.replace(/-/g, ' ').toLowerCase(),
                slug.replace(/-/g, '').toLowerCase(),
                slug.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()).toLowerCase()
            ];
            
            // Add common submenu patterns
            if (slug === 'home') {
                slugVariations.push('dashboard', 'main', 'overview');
            }
            if (slug === 'dashboard') {
                slugVariations.push('home', 'main', 'overview');
            }
            
            const linkTextLower = linkText.toLowerCase();
            const isMatch = slugVariations.some(variation => 
                linkTextLower === variation || 
                linkTextLower.includes(variation) ||
                variation.includes(linkTextLower)
            );
            
            if (isMatch) {
                link.style.setProperty('background-color', color, 'important');
                found = true;
                console.log('WMO: ✅ MATCH FOUND! Applied submenu background color', color, 'to', linkText);
                console.log('WMO: Matching variations:', slugVariations);
            }
        });
        
        if (!found) {
            console.log('WMO: ❌ No submenu item found for background color application. Slug:', slug);
        }
    }

    // NEW - Remove background color from submenu items by text content
    function wmoRemoveBackgroundColorFromSubmenuByText(slug) {
        console.log('WMO: Removing background color from submenu by text for', slug);
        
        // Find submenu items by text content and remove background color
        const submenuLinks = document.querySelectorAll('#adminmenu .wp-submenu li a');
        
        submenuLinks.forEach(link => {
            const linkText = link.textContent.trim();
            // Use the same enhanced matching logic
            const slugVariations = [
                slug.toLowerCase(),
                slug.replace(/-/g, ' ').toLowerCase(),
                slug.replace(/-/g, '').toLowerCase(),
                slug.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()).toLowerCase()
            ];
            
            // Add common submenu patterns
            if (slug === 'home') {
                slugVariations.push('dashboard', 'main', 'overview');
            }
            if (slug === 'dashboard') {
                slugVariations.push('home', 'main', 'overview');
            }
            
            const linkTextLower = linkText.toLowerCase();
            const isMatch = slugVariations.some(variation => 
                linkTextLower === variation || 
                linkTextLower.includes(variation) ||
                variation.includes(linkTextLower)
            );
            
            if (isMatch) {
                link.style.removeProperty('background-color');
                console.log('WMO: Removed submenu background color from', linkText);
            }
        });
    }

    // NEW - Inject background color CSS
    function wmoInjectBackgroundCSS(slug, color) {
        const styleId = 'wmo-bg-color-' + slug;
        let styleElement = document.getElementById(styleId);
        
        if (!styleElement) {
            styleElement = document.createElement('style');
            styleElement.id = styleId;
            document.head.appendChild(styleElement);
        }
        
        const cssRules = `
            body #adminmenu li#menu-${slug} > a,
            body #adminmenu li#toplevel_page_${slug} > a,
            body #adminmenu li[id='menu-${slug}'] > a,
            body #adminmenu li[id='toplevel_page_${slug}'] > a { 
                background-color: ${color} !important; 
            }
        `;
        
        styleElement.textContent = cssRules;
        console.log('WMO: Injected background CSS for', slug, 'with color', color);
    }

    // NEW - Apply background color to WordPress menu (real-time)
    function wmoApplyBackgroundColorToMenu(slug, color) {
        console.log('WMO: Applying background color to menu for', slug, 'Color:', color);
        
        try {
            if (color) {
                wmoInjectBackgroundCSS(slug, color);
                // Also apply background color to submenu items
                wmoApplyBackgroundColorToSubmenuByText(slug, color);
            } else {
                // Remove background color
                const styleId = 'wmo-bg-color-' + slug;
                const styleElement = document.getElementById(styleId);
                if (styleElement) {
                    styleElement.remove();
                    console.log('WMO: Removed background CSS for', slug);
                }
                // Remove background color from submenu items
                wmoRemoveBackgroundColorFromSubmenuByText(slug);
            }
        } catch (error) {
            console.error('WMO: Error applying background color:', error);
            // Fallback to old method if needed
            wmoApplyBackgroundColorToMenuFallback(slug, color);
        }
    }

    // Fallback function for background color (keeping old method as backup)
    function wmoApplyBackgroundColorToMenuFallback(slug, color) {
        console.log('WMO: Using fallback method for background color', slug);
        
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
                        element.style.setProperty('background-color', color, 'important');
                    } else {
                        element.style.removeProperty('background-color');
                    }
                    found = true;
                    console.log('WMO: Applied background color via fallback to:', element.textContent.trim());
                });
            }
        });
        
        if (!found) {
            console.log('WMO: Warning - No menu elements found for background color application. Slug:', slug);
        }
    }

    // Update background color preview elements
    function wmoUpdateBackgroundColorPreview($input, color) {
        var $section = $input.closest('.wmo-background-color-section');
        var $swatch = $section.find('.wmo-color-swatch');
        var $value = $section.find('.wmo-color-value');
        var $preview = $section.find('.wmo-menu-preview');
        
        if (color) {
            $swatch.css('background-color', color);
            $value.text(color);
            $preview.css('background-color', color);
        } else {
            $swatch.css('background-color', '#000000');
            $value.text('#000000');
            $preview.css('background-color', '#000000');
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
         console.log('WMO: Document ready - initializing color picker functionality');
         console.log('WMO: Script loaded successfully');
         
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
         
         // Initialize color pickers with delay to ensure DOM is ready
         setTimeout(function() {
             console.log('WMO: Delayed initialization of color pickers');
             initColorPickers();
         }, 1000);
         
         // Re-initialize color pickers when content is expanded (for submenus)
         $(document).on('wmoContentExpanded', function() {
             console.log('WMO: Content expanded, re-initializing color pickers');
             setTimeout(function() {
                 initColorPickers();
             }, 100);
         });
         
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

         // Debug function to inspect WordPress admin menu structure
         function wmoDebugMenuStructure() {
             console.log('=== WMO: WordPress Admin Menu Structure Debug ===');
             
             // Check all menu items
             const allMenuItems = document.querySelectorAll('#adminmenu li');
             console.log('WMO: Total menu items found:', allMenuItems.length);
             
             allMenuItems.forEach((item, index) => {
                 if (index < 10) { // Only log first 10 to avoid spam
                     const id = item.id;
                     const text = item.textContent.trim();
                     const classes = item.className;
                     console.log(`WMO: Menu item ${index}: ID="${id}", Text="${text}", Classes="${classes}"`);
                 }
             });
             
             // Check submenu items specifically
             const submenuItems = document.querySelectorAll('#adminmenu .wp-submenu li');
             console.log('WMO: Total submenu items found:', submenuItems.length);
             
             submenuItems.forEach((item, index) => {
                 if (index < 10) { // Only log first 10 to avoid spam
                     const id = item.id;
                     const text = item.textContent.trim();
                     const classes = item.className;
                     console.log(`WMO: Submenu item ${index}: ID="${id}", Text="${text}", Classes="${classes}"`);
                 }
             });
         }
         
         // Call debug function after a delay
         setTimeout(wmoDebugMenuStructure, 3000);
         

         
     });

    // Remove color from WordPress menu
    function wmoRemoveColorFromMenu(slug) {
        console.log('WMO: Removing color from menu for', slug);
        
        // More specific selectors to target only the exact menu item
        var selectors = [
            '#menu-' + slug + ' > a',                    // Standard WordPress menu format
            '#toplevel_page_' + slug + ' > a',           // Plugin pages
            'li[id="menu-' + slug + '"] > a',           // Alternative format
            'li[id="toplevel_page_' + slug + '"] > a'   // Plugin page format
        ];
        
        selectors.forEach(function(selector) {
            var elements = document.querySelectorAll(selector);
            elements.forEach(function(element) {
                if (element) {
                    // Remove color styles
                    element.style.removeProperty('color');
                    console.log('WMO: Removed color from element:', element, 'Text:', element.textContent.trim());
                }
            });
        });
        
        // Also remove color from submenu items
        wmoRemoveColorFromSubmenuByText(slug);
    }

})(jQuery);
