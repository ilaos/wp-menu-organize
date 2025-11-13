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
        $('.wamm-notification').remove();
        
        // Create notification element
        var $notification = $('<div class="wamm-notification wmo-notification-' + type + '">' + message + '</div>');
        
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
        console.log('WAMM: Initializing WordPress color pickers');
        
        if (!$.fn.wpColorPicker) {
            console.error('WAMM: WordPress Color Picker not available');
            return;
        }

        // Initialize main color fields
        console.log('WAMM: Initializing main color fields, found:', $('.wamm-color-field').length);
        $('.wamm-color-field').each(function(index) {
            console.log('WAMM: Color field', index, 'ID:', this.id, 'Is submenu:', $(this).data('is-submenu'));
        });
        
        $('.wamm-color-field').wpColorPicker({
            defaultColor: '#23282d',
            change: debounce(function(event, ui) {
                var $input = $(this);
                var slug = $input.data('menu-slug');
                var color = ui.color.toString();
                var isSubmenu = $input.data('is-submenu') === true;

                console.log('WAMM: Color changed for', slug, 'to', color, 'Is submenu:', isSubmenu);

                // Update input value
                $input.val(color);
                
                // Apply color to menu immediately (live preview)
                if (isSubmenu) {
                    // Use complex function for submenus
                    wammApplyColorToMenu(slug, color);
                } else {
                    // Use simple, direct approach for parent menus (restore working functionality)
                    wammInjectCSS(slug, color);
                }
                
                // Trigger custom event
                $(document).trigger('wammColorChanged', [slug, color, isSubmenu]);
                
                // Auto-save color
                if (slug) {
                    wammAutoSaveColor(slug, color, $input);
                    // Show saved notification
                    showNotification('Color saved successfully!', 'success');
                }
                
                // Auto-close picker after delay
                console.log('WAMM: Attempting to auto-close color picker for', slug);
                setTimeout(function() {
                    // Force close using multiple methods to ensure it works
                    try {
                        // Method 1: WordPress API
                        if ($input.wpColorPicker && typeof $input.wpColorPicker === 'function') {
                            $input.wpColorPicker('close');
                            console.log('WAMM: Successfully closed color picker for', slug, 'using wpColorPicker method');
                        }
                    } catch (error) {
                        console.log('WAMM: wpColorPicker method failed for', slug, ':', error);
                    }
                    
                    // Method 2: Always use fallback to ensure closure
                    setTimeout(function() {
                        $('.wp-picker-holder').hide();
                        $('.wp-color-result').removeClass('wp-picker-open');
                        console.log('WAMM: Applied fallback close method for', slug);
                    }, 100);
                    
                }, 300);
            }, 300),
            clear: function(event, ui) {
                var $input = $(this);
                var slug = $input.data('menu-slug');
                
                console.log('WAMM: Color cleared for', slug);
                $input.val('');
                
                if (slug) {
                    wammAutoSaveColor(slug, '', $input);
                }
                
                setTimeout(function() {
                    $input.wpColorPicker('close');
                }, 300);
            }
        });

        // Initialize background color fields (WordPress color picker)
        console.log('WAMM: Initializing WordPress color pickers for background colors...');
        console.log('WAMM: Total background color fields found:', $('.wamm-background-color-field').length);
        $('.wamm-background-color-field').each(function(index) {
            console.log('WAMM: Background color field', index, 'ID:', this.id, 'Is submenu:', $(this).data('is-submenu'), 'Value:', $(this).val());
        });
        
            // Initialize background color fields with WordPress color picker
    $('.wamm-background-color-field').wpColorPicker({
        defaultColor: '#000000',
        change: debounce(function(event, ui) {
            var $input = $(this);
            var slug = $input.data('menu-slug');
            var color = ui.color.toString();
            var isSubmenu = $input.data('is-submenu') === true;

            console.log('WAMM: Background color changed for', slug, 'to', color);

            // Update color swatch and preview
            wammUpdateBackgroundColorPreview($input, color);
            
            // Apply background color to menu immediately (live preview)
            wammApplyBackgroundColorToMenu(slug, color);
            
            // Trigger custom event
            $(document).trigger('wammBackgroundColorChanged', [slug, color, isSubmenu]);
            
            // Auto-save background color
            if (slug) {
                console.log('WAMM: About to auto-save background color for', slug, 'Color:', color);
                console.log('WAMM: wamm_ajax available:', typeof wamm_ajax !== 'undefined');
                if (typeof wamm_ajax !== 'undefined') {
                    console.log('WAMM: AJAX URL:', wamm_ajax.ajax_url);
                    console.log('WAMM: Nonce available:', wamm_ajax.nonce ? 'YES' : 'NO');
                }
                wammAutoSaveBackgroundColor(slug, color, $input);
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
                        console.log('WAMM: Successfully closed background color picker for', slug, 'using wpColorPicker method');
                    }
                } catch (error) {
                    console.log('WAMM: wpColorPicker method failed for', slug, ':', error);
                }
                
                // Method 2: Always use fallback to ensure closure
                setTimeout(function() {
                    $('.wp-picker-holder').hide();
                    $('.wp-color-result').removeClass('wp-picker-open');
                    console.log('WAMM: Applied fallback close method for background color picker', slug);
                }, 100);
                
            }, 300);
        }, 300),
        clear: function(event, ui) {
            var $input = $(this);
            var slug = $input.data('menu-slug');
            
            console.log('WAMM: Background color cleared for', slug);
            $input.val('');
            
            // Update color swatch and preview
            wammUpdateBackgroundColorPreview($input, '');
            
            if (slug) {
                wammAutoSaveBackgroundColor(slug, '', $input);
            }
            
            setTimeout(function() {
                // Force close using multiple methods to ensure it works
                try {
                    // Method 1: WordPress API
                    if ($input.wpColorPicker && typeof $input.wpColorPicker === 'function') {
                        $input.wpColorPicker('close');
                        console.log('WAMM: Successfully closed background color picker (clear) for', slug, 'using wpColorPicker method');
                    }
                } catch (error) {
                    console.log('WAMM: wpColorPicker method failed for', slug, ':', error);
                }
                
                // Method 2: Always use fallback to ensure closure
                setTimeout(function() {
                    $('.wp-picker-holder').hide();
                    $('.wp-color-result').removeClass('wp-picker-open');
                    console.log('WAMM: Applied fallback close method for background color picker (clear)', slug);
                }, 100);
                
            }, 300);
        }
    });
    
    // Initialize quick color buttons
    $('.wamm-quick-color').on('click', function() {
        var $button = $(this);
        var color = $button.data('color');
        var $section = $button.closest('.wamm-background-color-section');
        var $input = $section.find('.wamm-background-color-field');
        var slug = $input.data('menu-slug');
        
        console.log('WAMM: Quick color selected:', color, 'for slug:', slug);
        
        // Update the color picker
        $input.wpColorPicker('color', color);
        
        // Update color swatch and preview
        wammUpdateBackgroundColorPreview($input, color);
        
        // Apply background color to menu immediately
        wammApplyBackgroundColorToMenu(slug, color);
        
        // Auto-save background color
        if (slug) {
            wammAutoSaveBackgroundColor(slug, color, $input);
        }
        
        // Visual feedback
        $button.addClass('wamm-quick-color-active');
        setTimeout(function() {
            $button.removeClass('wamm-quick-color-active');
        }, 200);
    });
        

        
        // Debug: Add click handler for background color fields
        $(document).on('click', '.wamm-background-color-field', function() {
            console.log('WAMM: Background color field clicked:', this.id);
            console.log('WAMM: Field value:', $(this).val());
            console.log('WAMM: Field has wpColorPicker:', $(this).hasClass('wp-color-picker'));
            
            // Set up a watcher to see if the value changes
            var $field = $(this);
            var originalValue = $field.val();
            
            setTimeout(function() {
                var newValue = $field.val();
                if (newValue !== originalValue) {
                    console.log('WAMM: Field value changed from', originalValue, 'to', newValue);
                } else {
                    console.log('WAMM: Field value did not change, still:', originalValue);
                }
            }, 1000);
        });
        
        // Debug: Check for any element with the class
        $(document).on('click', '[class*="background-color"]', function() {
            console.log('WAMM: Any background-color element clicked:', this.className);
        });
        
        // Debug: Check document ready state
        console.log('WAMM: Document ready state:', document.readyState);
        console.log('WAMM: jQuery version:', $.fn.jquery);
        console.log('WAMM: wpColorPicker available:', typeof $.fn.wpColorPicker);
        
        // Debug: Check all input fields on the page
        console.log('WAMM: Total input fields on page:', $('input').length);
        console.log('WAMM: Input fields with "color" in class:', $('input[class*="color"]').length);
        $('input[class*="color"]').each(function() {
            console.log('WAMM: Color-related input found:', this.id, 'Class:', this.className);
        });

        // Fix color picker positioning when opened
        $(document).on('click', '.wp-color-result', function() {
            var $this = $(this);
            var $container = $this.closest('.wp-picker-container');
            var $holder = $container.find('.wp-picker-holder');
            var $picker = $holder.find('.iris-picker');
            
            // Check if we're inside the problematic containers
            var $parentGroup = $this.closest('.wamm-color-group.wamm-parent-menu-group');
            var $parentWrapper = $this.closest('.wamm-menu-item-wrapper.wamm-submenu-wrapper.expanded');
            
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
        $('.wamm-badge-color-picker, .wamm-badge-bg-picker').wpColorPicker({
            change: debounce(function(event, ui) {
                var $input = $(this);
                var $wrapper = $input.closest('.wamm-badge-wrapper');
                var slug = $wrapper.data('menu-slug');
                
                // Fallback: try to get slug from the input itself if wrapper doesn't have it
                if (!slug) {
                    slug = $input.data('menu-slug');
                }
                
                var color = ui.color.toString();
                
                console.log('WAMM: Badge color changed for', slug, 'to', color);
                console.log('WAMM: Badge color - Wrapper found:', $wrapper.length > 0, 'Slug:', slug);
                
                // Update badge preview
                wammUpdateBadgePreview(slug);
                
                // Auto-save badge
                if (slug) {
                    wammAutoSaveBadge(slug);
                }
                
                setTimeout(function() {
                    $input.wpColorPicker('close');
                }, 300);
            }, 300)
        });


    }

    // Auto-save color function
    function wammAutoSaveColor(slug, color, $input) {
        // Clear existing timeout
        if (autoSaveTimeouts[slug]) {
            clearTimeout(autoSaveTimeouts[slug]);
        }

        // Show saving indicator
        wammShowSavingIndicator($input, 'Saving...', 'info');

        // Set new timeout
        autoSaveTimeouts[slug] = setTimeout(function() {
            $.ajax({
                url: wamm_ajax.ajax_url,
                method: 'POST',
                data: {
                    action: 'wamm_save_color',
                    id: slug,
                    color: color,
                    nonce: wamm_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        wammShowSavingIndicator($input, 'Saved!', 'success');
                        console.log('WAMM: Color saved successfully for', slug);
                    } else {
                        wammShowSavingIndicator($input, 'Error saving', 'error');
                        console.error('WAMM: Color save failed:', response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    wammShowSavingIndicator($input, 'Network error', 'error');
                    console.error('WAMM: Color save AJAX error:', textStatus, errorThrown);
                }
            });
        }, 500);
    }

    // Auto-save background color function
    function wammAutoSaveBackgroundColor(slug, color, $input) {
        console.log('WAMM: wammAutoSaveBackgroundColor called for', slug, 'Color:', color);
        
        // Clear existing timeout
        if (autoSaveTimeouts[slug + '_bg']) {
            clearTimeout(autoSaveTimeouts[slug + '_bg']);
        }

        // Show saving indicator
        wammShowSavingIndicator($input, 'Saving...', 'info');

        // Set new timeout
        autoSaveTimeouts[slug + '_bg'] = setTimeout(function() {
            console.log('WAMM: Executing AJAX save for background color', slug);
            $.ajax({
                url: wamm_ajax.ajax_url,
                method: 'POST',
                data: {
                    action: 'wamm_save_background_color',
                    id: slug,
                    color: color,
                    nonce: wamm_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        wammShowSavingIndicator($input, 'Saved!', 'success');
                        console.log('WAMM: Background color saved successfully for', slug);
                    } else {
                        wammShowSavingIndicator($input, 'Error saving', 'error');
                        console.error('WAMM: Background color save failed:', response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    wammShowSavingIndicator($input, 'Network error', 'error');
                    console.error('WAMM: Background color save AJAX error:', textStatus, errorThrown);
                }
            });
        }, 500);
    }

         // Show saving indicator
     function wammShowSavingIndicator($input, message, type = 'info') {
         var $indicator = $input.siblings('.wamm-saving-indicator');
         
         if ($indicator.length === 0) {
             $indicator = $('<div class="wamm-saving-indicator"></div>');
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
        $(document).on('change', '.wamm-badge-enable', function() {
            var $wrapper = $(this).closest('.wamm-badge-wrapper');
            var slug = $wrapper.data('menu-slug');
            
            // Fallback: try to get slug from the input itself if wrapper doesn't have it
            if (!slug) {
                slug = $(this).data('menu-slug');
            }
            
            var enabled = $(this).is(':checked');
            
            console.log('WAMM: Badge enable/disable - Wrapper found:', $wrapper.length > 0, 'Slug:', slug);
            
            $wrapper.find('.wamm-badge-controls').toggle(enabled);
            
            if (enabled) {
                wammUpdateBadgePreview(slug);
                wammAutoSaveBadge(slug);
            } else {
                wammRemoveBadgeFromMenu(slug);
                wammAutoSaveBadge(slug);
            }
        });

        // Badge text input
        $(document).on('input', '.wamm-badge-text', function() {
            var $wrapper = $(this).closest('.wamm-badge-wrapper');
            var slug = $wrapper.data('menu-slug');
            
            // Fallback: try to get slug from the input itself if wrapper doesn't have it
            if (!slug) {
                slug = $(this).data('menu-slug');
            }
            
            console.log('WAMM: Badge text input - Slug:', slug);
            
            clearTimeout(window.wammBadgeTextTimeout);
            window.wammBadgeTextTimeout = setTimeout(function() {
                wammUpdateBadgePreview(slug);
                wammAutoSaveBadge(slug);
            }, 300);
        });
    }

    // Update badge preview
    function wammUpdateBadgePreview(slug) {
        console.log('WAMM: Updating badge preview for slug:', slug);
        
        var $wrapper = $('.wamm-badge-wrapper').filter(function() {
            return $(this).data('menu-slug') === slug;
        });
        
        console.log('WAMM: Badge preview - Wrapper found:', $wrapper.length > 0);
        
        if ($wrapper.length === 0) {
            console.log('WAMM: Badge preview - No wrapper found for slug:', slug);
            return;
        }
        
        var text = $wrapper.find('.wamm-badge-text').val();
        var color = $wrapper.find('.wamm-badge-color-picker').val();
        var background = $wrapper.find('.wamm-badge-bg-picker').val();
        var enabled = $wrapper.find('.wamm-badge-enable').is(':checked');
        
        console.log('WAMM: Badge preview - Text:', text, 'Color:', color, 'Background:', background, 'Enabled:', enabled);
        
        var $preview = $wrapper.find('.wamm-badge-preview');
        if ($preview.length === 0) {
            $preview = $('<span class="wamm-badge-preview"></span>');
            $wrapper.find('.wamm-badge-controls').append($preview);
            console.log('WAMM: Badge preview - Created new preview element');
        }
        
        if (enabled && text) {
            $preview.text(text).css({
                'color': color || '#ffffff',
                'background-color': background || '#0073aa'
            }).show();
            console.log('WAMM: Badge preview - Showing badge with text:', text);
        } else {
            $preview.hide();
            console.log('WAMM: Badge preview - Hiding badge (disabled or no text)');
        }
    }

    // Auto-save badge
    function wammAutoSaveBadge(slug) {
        var $wrapper = $('.wamm-badge-wrapper').filter(function() {
            return $(this).data('menu-slug') === slug;
        });
        
        if ($wrapper.length === 0) return;
        
        var enabled = $wrapper.find('.wamm-badge-enable').is(':checked');
        var text = $wrapper.find('.wamm-badge-text').val();
        var color = $wrapper.find('.wamm-badge-color-picker').val();
        var background = $wrapper.find('.wamm-badge-bg-picker').val();
        
        $.ajax({
            url: wamm_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'wamm_save_badge',
                slug: slug,
                enabled: enabled,
                text: text,
                color: color,
                background: background,
                nonce: wamm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    console.log('WAMM: Badge saved successfully for', slug);
                } else {
                    console.error('WAMM: Badge save failed:', response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('WAMM: Badge save AJAX error:', textStatus, errorThrown);
            }
        });
    }

    // Remove badge from menu
    function wammRemoveBadgeFromMenu(slug) {
        var selectors = [
            "#menu-" + slug + " > a .wp-menu-name .wamm-menu-badge",
            "#toplevel_page_" + slug + " > a .wp-menu-name .wamm-menu-badge"
        ];
        
        selectors.forEach(function(selector) {
            $(selector).remove();
        });
    }

    // NEW - Real-time CSS injection function
    function wammInjectCSS(slug, color) {
        const styleId = 'wamm-color-' + slug;
        let styleElement = document.getElementById(styleId);
        
        if (!styleElement) {
            styleElement = document.createElement('style');
            styleElement.id = styleId;
            document.head.appendChild(styleElement);
        }
        
        const cssRules = `
            body #adminmenu li[id*='${slug}'] > a,
            body #adminmenu li[id*='${slug}'] .wp-menu-name,
            body #adminmenu li[id*='${slug}'] .wp-menu-image:before { 
                color: ${color} !important; 
            }
        `;
        
        styleElement.textContent = cssRules;
        console.log('WAMM: Injected CSS for', slug, 'with color', color);
        console.log('WAMM: CSS Rules:', cssRules);
        
        // Debug: Check if elements exist
        const selectors = [
            `#menu-${slug}`,
            `#toplevel_page_${slug}`,
            `li[id='menu-${slug}']`,
            `li[id='toplevel_page_${slug}']`
        ];
        
        selectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            console.log(`WAMM: Selector "${selector}" found ${elements.length} elements:`, elements);
        });
    }

    // NEW - Apply color to WordPress menu (real-time)
    function wammApplyColorToMenu(slug, color) {
        console.log('WAMM: Applying color to menu for', slug, 'Color:', color);
        console.log('WAMM: Slug type:', typeof slug, 'Slug value:', slug);
        
        try {
            if (color) {
                // First, try to apply to parent menu items (existing functionality)
                wammInjectCSS(slug, color);
                
                // Then, handle submenu items by text content
                wammApplyColorToSubmenuByText(slug, color);
            } else {
                // Remove color from parent menu items
                const styleId = 'wamm-color-' + slug;
                const styleElement = document.getElementById(styleId);
                if (styleElement) {
                    styleElement.remove();
                    console.log('WAMM: Removed CSS for', slug);
                }
                
                // Remove color from submenu items
                wammRemoveColorFromSubmenuByText(slug);
            }
        } catch (error) {
            console.error('WAMM: Error applying color:', error);
            // Fallback to old method if needed
            wammApplyColorToMenuFallback(slug, color);
        }
    }

    // NEW - Apply color to submenu items by text content
    function wammApplyColorToSubmenuByText(slug, color) {
        console.log('WAMM: Applying color to submenu by text for', slug, 'Color:', color);
        
        // Find submenu items by text content
        const submenuLinks = document.querySelectorAll('#adminmenu .wp-submenu li a');
        let found = false;
        
        console.log('WAMM: Total submenu links found:', submenuLinks.length);
        
        submenuLinks.forEach((link, index) => {
            const linkText = link.textContent.trim();
            console.log(`WAMM: Submenu link ${index}: "${linkText}"`);
            
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
                console.log('WAMM: ? MATCH FOUND! Applied submenu color', color, 'to', linkText);
                console.log('WAMM: Matching variations:', slugVariations);
            }
        });
        
        if (!found) {
            console.log('WAMM: ? No submenu item found with text for slug:', slug);
            console.log('WAMM: Tried variations:', slugVariations);
            
            // Debug: Show all available submenu texts
            console.log('WAMM: Available submenu texts:');
            submenuLinks.forEach((link, index) => {
                if (index < 10) { // Only show first 10 to avoid spam
                    console.log(`  ${index}: "${link.textContent.trim()}"`);
                }
            });
        }
    }

    // NEW - Remove color from submenu items by text content
    function wammRemoveColorFromSubmenuByText(slug) {
        console.log('WAMM: Removing color from submenu by text for', slug);
        
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
                console.log('WAMM: Removed submenu color from', linkText);
            }
        });
    }

    // NEW - Apply background color to submenu items by text content
    function wammApplyBackgroundColorToSubmenuByText(slug, color) {
        console.log('WAMM: Applying background color to submenu by text for', slug, 'Color:', color);
        
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
                console.log('WAMM: ? MATCH FOUND! Applied submenu background color', color, 'to', linkText);
                console.log('WAMM: Matching variations:', slugVariations);
            }
        });
        
        if (!found) {
            console.log('WAMM: ? No submenu item found for background color application. Slug:', slug);
        }
    }

    // NEW - Remove background color from submenu items by text content
    function wammRemoveBackgroundColorFromSubmenuByText(slug) {
        console.log('WAMM: Removing background color from submenu by text for', slug);
        
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
                console.log('WAMM: Removed submenu background color from', linkText);
            }
        });
    }

    // NEW - Inject background color CSS
    function wammInjectBackgroundCSS(slug, color) {
        const styleId = 'wamm-bg-color-' + slug;
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
        console.log('WAMM: Injected background CSS for', slug, 'with color', color);
    }

    // NEW - Apply background color to WordPress menu (real-time)
    function wammApplyBackgroundColorToMenu(slug, color) {
        console.log('WAMM: Applying background color to menu for', slug, 'Color:', color);
        
        try {
            if (color) {
                wammInjectBackgroundCSS(slug, color);
                // Also apply background color to submenu items
                wammApplyBackgroundColorToSubmenuByText(slug, color);
            } else {
                // Remove background color
                const styleId = 'wamm-bg-color-' + slug;
                const styleElement = document.getElementById(styleId);
                if (styleElement) {
                    styleElement.remove();
                    console.log('WAMM: Removed background CSS for', slug);
                }
                // Remove background color from submenu items
                wammRemoveBackgroundColorFromSubmenuByText(slug);
            }
        } catch (error) {
            console.error('WAMM: Error applying background color:', error);
            // Fallback to old method if needed
            wammApplyBackgroundColorToMenuFallback(slug, color);
        }
    }

    // Fallback function for background color (keeping old method as backup)
    function wammApplyBackgroundColorToMenuFallback(slug, color) {
        console.log('WAMM: Using fallback method for background color', slug);
        
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
                    console.log('WAMM: Applied background color via fallback to:', element.textContent.trim());
                });
            }
        });
        
        if (!found) {
            console.log('WAMM: Warning - No menu elements found for background color application. Slug:', slug);
        }
    }

    // Update background color preview elements
    function wammUpdateBackgroundColorPreview($input, color) {
        var $section = $input.closest('.wamm-background-color-section');
        var $swatch = $section.find('.wamm-color-swatch');
        var $value = $section.find('.wamm-color-value');
        var $preview = $section.find('.wamm-menu-preview');
        
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
    function wammApplyColorToMenuFallback(slug, color) {
        console.log('WAMM: Using fallback method for', slug);
        
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
                    console.log('WAMM: Applied color via fallback to:', element.textContent.trim());
                });
            }
        });
        
        if (!found) {
            console.log('WAMM: Warning - No menu elements found for color application. Slug:', slug);
        }
    }

    // Theme toggle functionality
    function initThemeToggle() {
        var $themeToggle = $('#wamm-dark-mode-toggle');
        if ($themeToggle.length === 0) return;
        
        $themeToggle.on('change', function() {
            var isDarkMode = $(this).is(':checked');
            
            // Apply theme immediately
            if (isDarkMode) {
                $('body').addClass('wamm-dark-theme');
            } else {
                $('body').removeClass('wamm-dark-theme');
            }
            
            // Auto-save theme
            wammAutoSaveTheme(isDarkMode);
        });
    }

    // Auto-save theme
    function wammAutoSaveTheme(isDarkMode) {
        $.ajax({
            url: wamm_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'wamm_save_theme',
                dark_mode: isDarkMode,
                nonce: wamm_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    console.log('WAMM: Theme saved successfully');
                } else {
                    console.error('WAMM: Theme save failed:', response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('WAMM: Theme save AJAX error:', textStatus, errorThrown);
            }
        });
    }

    // Typography functionality initialization
    function initTypographyFunctionality() {
        console.log('WAMM: Initializing typography functionality');
        
        // Handle typography enable/disable toggle
        $(document).on('change', '.wamm-typography-enable', function() {
            var $checkbox = $(this);
            var slug = $checkbox.data('menu-slug');
            var enabled = $checkbox.is(':checked');
            var $controls = $checkbox.closest('.wamm-typography-wrapper').find('.wamm-typography-controls');
            
            console.log('WAMM: Typography toggle for', slug, 'enabled:', enabled);
            
            if (enabled) {
                $controls.show();
            } else {
                $controls.hide();
                // Remove typography from menu when disabled
                wammRemoveTypographyFromMenu(slug);
            }
            
            // Auto-save the change
            wammAutoSaveTypography(slug);
        });
        
        // Handle typography dropdown changes
        $(document).on('change', '.wamm-typography-family, .wamm-typography-size, .wamm-typography-weight', function() {
            var $select = $(this);
            var slug = $select.data('menu-slug');
            
            console.log('WAMM: Typography changed for', slug, 'Value:', $select.val());
            

            
            // Apply to actual menu immediately (live preview)
            wammApplyTypographyToMenu(slug);
            
            // Debounced auto-save
            clearTimeout(window.wammTypographyTimeout);
            window.wammTypographyTimeout = setTimeout(function() {
                wammAutoSaveTypography(slug);
            }, 500);
        });
        
        // Apply existing typography on page load
        setTimeout(function() {
            console.log('WAMM: Applying existing typography on page load');
            $('.wamm-typography-enable:checked').each(function() {
                var slug = $(this).data('menu-slug');
                console.log('WAMM: Found enabled typography for:', slug);
                wammApplyTypographyToMenu(slug);
            });
        }, 1000);
    }
    

    
    // Apply typography to WordPress menu
    function wammApplyTypographyToMenu(slug) {
        var $wrapper = $('.wamm-typography-wrapper').filter(function() {
            return $(this).find('[data-menu-slug="' + slug + '"]').length > 0;
        });
        
        var enabled = $wrapper.find('.wamm-typography-enable[data-menu-slug="' + slug + '"]').is(':checked');
        if (!enabled) {
            wammRemoveTypographyFromMenu(slug);
            return;
        }
        
        var fontFamily = $wrapper.find('.wamm-typography-family[data-menu-slug="' + slug + '"]').val();
        var fontSize = $wrapper.find('.wamm-typography-size[data-menu-slug="' + slug + '"]').val();
        var fontWeight = $wrapper.find('.wamm-typography-weight[data-menu-slug="' + slug + '"]').val();
        
        console.log('WAMM: Applying typography to menu for', slug, '- Family:', fontFamily, 'Size:', fontSize, 'Weight:', fontWeight);
        
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
            console.log('WAMM: Trying selector:', selector, 'Found elements:', elements.length);
            elements.forEach(function(element) {
                if (element) {
                    // Apply typography styles with !important for higher priority
                    if (fontFamily) element.style.setProperty('font-family', fontFamily, 'important');
                    if (fontSize) element.style.setProperty('font-size', fontSize, 'important');
                    if (fontWeight) element.style.setProperty('font-weight', fontWeight, 'important');
                    
                    found = true;
                    console.log('WAMM: Applied typography to element:', element, 'Text:', element.textContent.trim());
                }
            });
        });
        
        // Fallback: try exact text matching (more precise)
        if (!found) {
            console.log('WAMM: No elements found with selectors, trying exact text matching');
            
            $('#adminmenu > li > a').each(function() {
                var $link = $(this);
                var linkText = $link.text().trim().toLowerCase();
                var slugText = slug.replace(/-/g, ' ').toLowerCase();
                var slugTextAlt = slug.replace(/-/g, '').toLowerCase();
                
                // More precise matching - check if the text exactly matches or is very close
                var isExactMatch = linkText === slugText || 
                                  linkText === slugTextAlt ||
                                  linkText.includes(slugText) && linkText.length <= slugText.length + 5;
                
                console.log('WAMM: Checking link text for typography:', linkText, 'against slug:', slugText, 'Exact match:', isExactMatch);
                
                if (isExactMatch) {
                    var element = this;
                    if (fontFamily) element.style.setProperty('font-family', fontFamily, 'important');
                    if (fontSize) element.style.setProperty('font-size', fontSize, 'important');
                    if (fontWeight) element.style.setProperty('font-weight', fontWeight, 'important');
                    
                    console.log('WAMM: Applied typography via exact text matching to:', element, 'Text:', element.textContent.trim());
                    found = true;
                    return false; // Break the loop after finding the first match
                }
            });
        }
        
        if (!found) {
            console.log('WAMM: Warning - No menu elements found for typography application');
        }
    }
    
    // Remove typography from WordPress menu
    function wammRemoveTypographyFromMenu(slug) {
        console.log('WAMM: Removing typography from menu for', slug);
        
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
    function wammAutoSaveTypography(slug) {
        // Clear any existing timeout for this slug
        if (typographyAutoSaveTimeouts[slug]) {
            clearTimeout(typographyAutoSaveTimeouts[slug]);
        }
        
        // Get the wrapper and input for saving indicator
        var $wrapper = $('.wamm-typography-wrapper').filter(function() {
            return $(this).find('[data-menu-slug="' + slug + '"]').length > 0;
        });
        var $input = $wrapper.find('.wamm-typography-family[data-menu-slug="' + slug + '"]');
        
        // Show saving indicator - try multiple approaches
        console.log('WAMM: Showing saving indicator for input:', $input.length ? 'found' : 'not found');
        if ($input.length > 0) {
            wammShowSavingIndicator($input, 'Saving...', 'info');
        } else {
            // Try to find any input in the wrapper for the saving indicator
            var $anyInput = $wrapper.find('select, input').first();
            if ($anyInput.length > 0) {
                console.log('WAMM: Using fallback input for saving indicator');
                wammShowSavingIndicator($anyInput, 'Saving...', 'info');
            } else {
                console.log('WAMM: Could not find any input for saving indicator');
            }
        }
        
        // Set a new timeout to save after 500ms delay
        typographyAutoSaveTimeouts[slug] = setTimeout(function() {
            console.log('WAMM: Auto-saving typography for', slug);
            
            var enabled = $wrapper.find('.wamm-typography-enable[data-menu-slug="' + slug + '"]').is(':checked');
            var fontFamily = $wrapper.find('.wamm-typography-family[data-menu-slug="' + slug + '"]').val();
            var fontSize = $wrapper.find('.wamm-typography-size[data-menu-slug="' + slug + '"]').val();
            var fontWeight = $wrapper.find('.wamm-typography-weight[data-menu-slug="' + slug + '"]').val();
            
            $.ajax({
                url: wamm_ajax.ajax_url,
                method: 'POST',
                data: {
                    action: 'wamm_save_typography',
                    slug: slug,
                    enabled: enabled ? 1 : 0,
                    font_family: fontFamily,
                    font_size: fontSize,
                    font_weight: fontWeight,
                    nonce: wamm_ajax.nonce
                },
                success: function(response) {
                    console.log('WAMM: Typography auto-save response:', response);
                    if (response.success) {
                        wammShowSavingIndicator($input, 'Saved!', 'success');
                        console.log('WAMM: Typography auto-saved successfully');
                    } else {
                        wammShowSavingIndicator($input, 'Error saving', 'error');
                        console.error('WAMM: Typography auto-save failed:', response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    wammShowSavingIndicator($input, 'Network error', 'error');
                    console.error('WAMM: Typography auto-save AJAX error:', textStatus, errorThrown);
                }
            });
        }, 500); // 500ms delay for debouncing
    }

         // Initialize everything when document is ready
     $(document).ready(function() {
         console.log('WAMM: Color picker script loaded');
         console.log('WAMM: Document ready - initializing color picker functionality');
         console.log('WAMM: Script loaded successfully');
         
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
             console.log('WAMM: Delayed initialization of color pickers');
             initColorPickers();
         }, 1000);
         
         // Re-initialize color pickers when content is expanded (for submenus)
         $(document).on('wammContentExpanded', function() {
             console.log('WAMM: Content expanded, re-initializing color pickers');
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
             console.log('WAMM: Applying existing colors on page load');
             $('.wamm-color-field').each(function() {
                 var $input = $(this);
                 var slug = $input.data('menu-slug');
                 var color = $input.val();
                 if (slug && color) {
                     console.log('WAMM: Found existing color for:', slug, 'Color:', color);
                     wammApplyColorToMenu(slug, color);
                 }
             });
         }, 2000); // Increased delay to ensure menu is loaded
         
         // Handle color swatch clicks (if any)
         $(document).on('click', '.color-swatch', function(e) {
             e.preventDefault();
             var $swatch = $(this);
             var $input = $swatch.siblings('.wamm-color-field');
             if ($input.length) {
                 $input.wpColorPicker('open');
             }
         });

         // Debug function to inspect WordPress admin menu structure
         function wammDebugMenuStructure() {
             console.log('=== WAMM: WordPress Admin Menu Structure Debug ===');
             
             // Check all menu items
             const allMenuItems = document.querySelectorAll('#adminmenu li');
             console.log('WAMM: Total menu items found:', allMenuItems.length);
             
             allMenuItems.forEach((item, index) => {
                 if (index < 10) { // Only log first 10 to avoid spam
                     const id = item.id;
                     const text = item.textContent.trim();
                     const classes = item.className;
                     console.log(`WAMM: Menu item ${index}: ID="${id}", Text="${text}", Classes="${classes}"`);
                 }
             });
             
             // Check submenu items specifically
             const submenuItems = document.querySelectorAll('#adminmenu .wp-submenu li');
             console.log('WAMM: Total submenu items found:', submenuItems.length);
             
             submenuItems.forEach((item, index) => {
                 if (index < 10) { // Only log first 10 to avoid spam
                     const id = item.id;
                     const text = item.textContent.trim();
                     const classes = item.className;
                     console.log(`WAMM: Submenu item ${index}: ID="${id}", Text="${text}", Classes="${classes}"`);
                 }
             });
         }
         
         // Call debug function after a delay
         setTimeout(wammDebugMenuStructure, 3000);
         

         
     });

    // Remove color from WordPress menu
    function wammRemoveColorFromMenu(slug) {
        console.log('WAMM: Removing color from menu for', slug);
        
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
                    console.log('WAMM: Removed color from element:', element, 'Text:', element.textContent.trim());
                }
            });
        });
        
        // Also remove color from submenu items
        wammRemoveColorFromSubmenuByText(slug);
    }

})(jQuery);
