(function($) {
    'use strict';

    // Debug code to verify script loading
    console.log('=== WMO: Script loaded ===');
    console.log('jQuery available:', typeof jQuery);
    console.log('jQuery UI available:', typeof jQuery.ui);
    console.log('Sortable available:', typeof jQuery.fn.sortable);
    console.log('jQuery version:', jQuery.fn.jquery);
    console.log('jQuery UI version:', jQuery.ui ? jQuery.ui.version : 'Not available');

    // Global saveMenuOrder function - define at the top before wmoInitializeSortable
    window.saveMenuOrder = function() {
        try {
            console.log('WMO: saveMenuOrder function called');
            
            var $status = $('#wmo-save-status');
            var $saveButton = $('#wmo-save-order');
            
            if ($status.length) {
                $status.removeClass('success error').addClass('loading').text('Saving...');
            }
            if ($saveButton.length) {
                $saveButton.prop('disabled', true);
            }
            
            var order = [];
            $('#wmo-sortable-menu li').each(function() {
                order.push($(this).data('slug'));
            });
            
            console.log('WMO: Menu order to save:', order);
            
            // Check if wmo_ajax is defined, fallback to admin-ajax.php
            var ajaxUrl = (typeof wmo_ajax !== 'undefined' && wmo_ajax.ajax_url) ? wmo_ajax.ajax_url : ajaxurl;
            var nonce = (typeof wmo_ajax !== 'undefined' && wmo_ajax.nonce) ? wmo_ajax.nonce : '';
            
            console.log('WMO: Using AJAX URL:', ajaxUrl);
            console.log('WMO: Nonce available:', nonce ? 'YES' : 'NO');
            
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wmo_save_menu_order',
                    order: order,
                    nonce: nonce
                },
                success: function(response) {
                    console.log('WMO: Save response:', response);
                    
                    if (response.success) {
                        if ($status.length) {
                            $status.removeClass('loading error').addClass('success').text('Menu order saved successfully!');
                        }
                        
                        // Show success notice
                        var notice = $('<div class="wmo-notice success">Menu order saved!</div>');
                        $('body').append(notice);
                        setTimeout(function() {
                            notice.remove();
                        }, 3000);
                        
                        setTimeout(function() {
                            if ($status.length) {
                                $status.removeClass('success').text('');
                            }
                        }, 3000);
                    } else {
                        if ($status.length) {
                            $status.removeClass('loading success').addClass('error').text('Error saving menu order: ' + (response.data || 'Unknown error'));
                        }
                        console.error('WMO: Save failed:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('WMO: AJAX error:', status, error);
                    if ($status.length) {
                        $status.removeClass('loading success').addClass('error').text('Network error while saving menu order');
                    }
                },
                complete: function() {
                    if ($saveButton.length) {
                        $saveButton.prop('disabled', false);
                    }
                }
            });
        } catch (error) {
            console.error('WMO: Error in saveMenuOrder:', error);
            alert('Error saving menu order: ' + error.message);
        }
    };

    // Global function to initialize sortable (for manual injection fallback)
    window.wmoInitializeSortable = function() {
        console.log('WMO: Initializing sortable...');
        
        var $sortableContainer = $('.menu-items-list');
        var $sortableMenu = $('#wmo-sortable-menu');
        
        console.log('WMO: Container found:', $sortableContainer.length);
        console.log('WMO: Menu found:', $sortableMenu.length);
        console.log('WMO: Menu items found:', $sortableMenu.find('li').length);
        
        if ($sortableContainer.length && $sortableMenu.length) {
            try {
                $sortableMenu.sortable({
                    items: '> li',
                    handle: '.menu-item-handle',
                    placeholder: 'sortable-placeholder',
                    tolerance: 'pointer',
                    cursor: 'move',
                    axis: 'y',
                    opacity: 0.8,
                    zIndex: 1000,
                    start: function(event, ui) {
                        console.log('WMO: Started dragging:', ui.item);
                        ui.item.addClass('dragging');
                    },
                    sort: function(event, ui) {
                        console.log('WMO: Sorting in progress');
                    },
                    change: function(event, ui) {
                        console.log('WMO: Item position changed');
                    },
                    update: function(event, ui) {
                        console.log('WMO: Menu order updated');
                        // Check if saveMenuOrder is defined before calling
                        if (typeof window.saveMenuOrder === 'function') {
                            window.saveMenuOrder();
                        } else {
                            console.error('WMO: saveMenuOrder function not found');
                        }
                    },
                    stop: function(event, ui) {
                        console.log('WMO: Stopped dragging:', ui.item);
                        ui.item.removeClass('dragging');
                    }
                }).disableSelection();
                
                console.log('WMO: Sortable initialized successfully');
            } catch (error) {
                console.error('WMO: Error initializing sortable:', error);
            }
        } else {
            console.error('WMO: Sortable container or menu not found');
            console.error('WMO: Container found:', $sortableContainer.length);
            console.error('WMO: Menu found:', $sortableMenu.length);
        }
    };

    jQuery(document).ready(function($) {
        console.log('WMO: Admin script loaded successfully');
        console.log('WMO: Checking for menu items list...');
        
        // Check if the menu items list exists
        var menuList = $('.menu-items-list');
        if (menuList.length > 0) {
            console.log('WMO: Menu items list found with', menuList.find('li').length, 'items');
            console.log('WMO: CSS classes applied:', menuList.attr('class'));
        } else {
            console.log('WMO: Menu items list not found');
        }
        
        // Check jQuery UI availability
        if (typeof $.fn.sortable !== 'undefined') {
            console.log('WMO: jQuery UI sortable is available');
        } else {
            console.log('WMO: jQuery UI sortable is NOT available');
        }

        // Enhanced debugging for jQuery UI availability
        console.log('=== WMO: jQuery UI Debugging ===');
        console.log('typeof jQuery:', typeof jQuery);
        console.log('typeof jQuery.ui:', typeof jQuery.ui);
        console.log('typeof jQuery.fn.sortable:', typeof jQuery.fn.sortable);
        console.log('jQuery.ui.version:', jQuery.ui ? jQuery.ui.version : 'Not available');
        
        // Check for other jQuery UI widgets
        var uiWidgets = Object.keys($.fn).filter(key => key.includes('ui') || key.includes('sortable') || key.includes('draggable'));
        console.log('Available jQuery UI widgets:', uiWidgets);
        
        // Check if jQuery UI CSS is loaded
        var jqueryUICSS = $('link[href*="jquery-ui"]').length;
        console.log('jQuery UI CSS files loaded:', jqueryUICSS);
        
        // Check for WordPress jQuery UI script tags
        var wpJQueryUIScripts = $('script[src*="jquery-ui"]').length;
        console.log('WordPress jQuery UI script tags found:', wpJQueryUIScripts);
        
        // Check for jquery-ui-sortable specifically
        var sortableScripts = $('script[src*="sortable"]').length;
        console.log('jQuery UI sortable script tags found:', sortableScripts);

        // Check if jQuery UI is available
        if (typeof $.fn.sortable === 'undefined') {
            console.error('WMO: jQuery UI Sortable is not available');
            console.error('WMO: Available jQuery UI widgets:', Object.keys($.fn).filter(key => key.includes('ui')));
            
            // Try to detect if WordPress jQuery UI failed to load
            var wpJQueryUIScripts = $('script[src*="jquery-ui"]').length;
            console.error('WMO: WordPress jQuery UI script tags found:', wpJQueryUIScripts);
            
            // Check if CDN fallback is available
            var cdnJQueryUI = $('script[src*="jquery-ui.min.js"]').length;
            console.error('WMO: CDN jQuery UI script tags found:', cdnJQueryUI);
            
            console.error('WMO: jQuery UI sortable not available. Please check if scripts are loading properly.');
            return;
        }

        // Initialize sortable on the correct container
        var $sortableContainer = $('.menu-items-list');
        var $sortableMenu = $('#wmo-sortable-menu');
        
        console.log('WMO: Looking for sortable container:', $sortableContainer.length);
        console.log('WMO: Looking for sortable menu:', $sortableMenu.length);
        console.log('WMO: Menu items found:', $sortableMenu.find('li').length);

        if ($sortableContainer.length && $sortableMenu.length) {
            try {
                console.log('WMO: Initializing sortable on container');
                
                // Use the global function
                wmoInitializeSortable();
                
                // Test the sortable functionality
                setTimeout(function() {
                    var isSortable = $sortableMenu.hasClass('ui-sortable');
                    console.log('WMO: Sortable initialized:', isSortable);
                    if (!isSortable) {
                        console.error('WMO: Sortable not properly initialized');
                    }
                    
                    // Additional tests
                    console.log('WMO: Sortable instance:', $sortableMenu.sortable('instance'));
                    console.log('WMO: Sortable options:', $sortableMenu.sortable('option'));
                }, 100);
                
            } catch (error) {
                console.error('WMO: Error initializing sortable:', error);
                console.error('WMO: Error stack:', error.stack);
                alert('Error initializing drag and drop: ' + error.message);
            }
        } else {
            console.error('WMO: Sortable container or menu not found');
            console.error('WMO: Container found:', $sortableContainer.length);
            console.error('WMO: Menu found:', $sortableMenu.length);
        }

        // Add global debugging function
        window.wmoDebug = function() {
            console.log('=== WMO Debug Function ===');
            console.log('jQuery version:', $.fn.jquery);
            console.log('jQuery UI version:', $.ui ? $.ui.version : 'Not loaded');
            console.log('Sortable available:', typeof $.fn.sortable === 'function');
            console.log('Container found:', $('.menu-items-list').length);
            console.log('Menu found:', $('#wmo-sortable-menu').length);
            console.log('Menu items:', $('#wmo-sortable-menu li').length);
            console.log('Is sortable:', $('#wmo-sortable-menu').hasClass('ui-sortable'));
            console.log('saveMenuOrder function:', typeof window.saveMenuOrder === 'function');
            return {
                jquery: $.fn.jquery,
                jqueryUI: $.ui ? $.ui.version : null,
                sortable: typeof $.fn.sortable === 'function',
                container: $('.menu-items-list').length,
                menu: $('#wmo-sortable-menu').length,
                items: $('#wmo-sortable-menu li').length,
                isSortable: $('#wmo-sortable-menu').hasClass('ui-sortable'),
                saveMenuOrder: typeof window.saveMenuOrder === 'function'
            };
        };

        // Add CSS debugging function
        window.wmoDebugCSS = function() {
            console.log('=== WMO CSS Debug Function ===');
            
            var $container = $('.menu-items-list');
            var $menu = $('#wmo-sortable-menu');
            var $items = $('#wmo-sortable-menu li');
            var $handles = $('.menu-item-handle');
            
            // Check pointer-events
            console.log('Container pointer-events:', $container.css('pointer-events'));
            console.log('Menu pointer-events:', $menu.css('pointer-events'));
            console.log('First item pointer-events:', $items.first().css('pointer-events'));
            console.log('First handle pointer-events:', $handles.first().css('pointer-events'));
            
            // Check positioning
            console.log('Container position:', $container.css('position'));
            console.log('Menu position:', $menu.css('position'));
            console.log('First item position:', $items.first().css('position'));
            console.log('First handle position:', $handles.first().css('position'));
            
            // Check z-index
            console.log('Container z-index:', $container.css('z-index'));
            console.log('Menu z-index:', $menu.css('z-index'));
            console.log('First item z-index:', $items.first().css('z-index'));
            console.log('First handle z-index:', $handles.first().css('z-index'));
            
            // Check cursor
            console.log('Container cursor:', $container.css('cursor'));
            console.log('Menu cursor:', $menu.css('cursor'));
            console.log('First item cursor:', $items.first().css('cursor'));
            console.log('First handle cursor:', $handles.first().css('cursor'));
            
            // Check for overlapping elements
            var containerRect = $container[0] ? $container[0].getBoundingClientRect() : null;
            var menuRect = $menu[0] ? $menu[0].getBoundingClientRect() : null;
            var firstItemRect = $items.first()[0] ? $items.first()[0].getBoundingClientRect() : null;
            
            console.log('Container bounds:', containerRect);
            console.log('Menu bounds:', menuRect);
            console.log('First item bounds:', firstItemRect);
            
            // Check for any elements with pointer-events: none
            var noPointerElements = $('*').filter(function() {
                return $(this).css('pointer-events') === 'none';
            });
            console.log('Elements with pointer-events: none:', noPointerElements.length);
            if (noPointerElements.length > 0) {
                console.log('Elements with no pointer events:', noPointerElements);
            }
            
            return {
                containerPointerEvents: $container.css('pointer-events'),
                menuPointerEvents: $menu.css('pointer-events'),
                itemPointerEvents: $items.first().css('pointer-events'),
                handlePointerEvents: $handles.first().css('pointer-events'),
                containerPosition: $container.css('position'),
                menuPosition: $menu.css('position'),
                itemPosition: $items.first().css('position'),
                handlePosition: $handles.first().css('position'),
                containerZIndex: $container.css('z-index'),
                menuZIndex: $menu.css('z-index'),
                itemZIndex: $items.first().css('z-index'),
                handleZIndex: $handles.first().css('z-index'),
                containerCursor: $container.css('cursor'),
                menuCursor: $menu.css('cursor'),
                itemCursor: $items.first().css('cursor'),
                handleCursor: $handles.first().css('cursor'),
                noPointerElements: noPointerElements.length
            };
        };

        // Add CSS testing function
        window.wmoTestCSS = function() {
            console.log('=== WMO CSS Testing ===');
            
            // Add debug classes
            $('.menu-items-list').addClass('wmo-debug-drag wmo-debug-outline');
            console.log('Added debug classes to menu container');
            
            // Test click events
            $('.menu-item-handle').on('click', function() {
                console.log('Click detected on handle:', this);
            });
            
            // Test mouse events
            $('.menu-item-handle').on('mouseenter', function() {
                console.log('Mouse enter on handle:', this);
            });
            
            $('.menu-item-handle').on('mouseleave', function() {
                console.log('Mouse leave on handle:', this);
            });
            
            // Test mousedown (drag start)
            $('.menu-item-handle').on('mousedown', function(e) {
                console.log('Mouse down on handle:', this);
                console.log('Event:', e);
                console.log('Button:', e.button);
                console.log('Buttons:', e.buttons);
            });
            
            console.log('CSS testing enabled - try clicking and dragging menu items');
        };

        // Add function to remove debug styles
        window.wmoRemoveDebugCSS = function() {
            $('.menu-items-list').removeClass('wmo-debug-drag wmo-debug-outline');
            $('.menu-item-handle').off('click mouseenter mouseleave mousedown');
            console.log('Removed debug CSS and event listeners');
        };

        // Add function to check script loading status
        window.wmoCheckScripts = function() {
            console.log('=== WMO: Script Loading Check ===');
            
            // Check for jQuery
            console.log('jQuery loaded:', typeof jQuery !== 'undefined');
            if (typeof jQuery !== 'undefined') {
                console.log('jQuery version:', jQuery.fn.jquery);
            }
            
            // Check for jQuery UI
            console.log('jQuery UI loaded:', typeof jQuery.ui !== 'undefined');
            if (typeof jQuery.ui !== 'undefined') {
                console.log('jQuery UI version:', jQuery.ui.version);
            }
            
            // Check for sortable
            console.log('Sortable available:', typeof jQuery.fn.sortable === 'function');
            
            // Check script tags in DOM
            var jqueryScripts = $('script[src*="jquery"]').length;
            var jqueryUIScripts = $('script[src*="jquery-ui"]').length;
            var sortableScripts = $('script[src*="sortable"]').length;
            
            console.log('jQuery script tags:', jqueryScripts);
            console.log('jQuery UI script tags:', jqueryUIScripts);
            console.log('Sortable script tags:', sortableScripts);
            
            // Check CSS files
            var jqueryUICSS = $('link[href*="jquery-ui"]').length;
            console.log('jQuery UI CSS files:', jqueryUICSS);
            
            return {
                jquery: typeof jQuery !== 'undefined',
                jqueryUI: typeof jQuery.ui !== 'undefined',
                sortable: typeof jQuery.fn.sortable === 'function',
                jqueryScripts: jqueryScripts,
                jqueryUIScripts: jqueryUIScripts,
                sortableScripts: sortableScripts,
                jqueryUICSS: jqueryUICSS
            };
        };

        // Add function to check WordPress admin CSS conflicts
        window.wmoCheckWordPressCSS = function() {
            console.log('=== WMO WordPress CSS Conflict Check ===');
            
            // Check for WordPress admin styles that might interfere
            var wpAdminSelectors = [
                '.wp-admin *',
                '#wpcontent *',
                '#wpbody *',
                '.wrap *',
                '.menu-items-list *',
                '#wmo-sortable-menu *'
            ];
            
            wpAdminSelectors.forEach(function(selector) {
                try {
                    var elements = $(selector);
                    var noPointerCount = elements.filter(function() {
                        return $(this).css('pointer-events') === 'none';
                    }).length;
                    
                    if (noPointerCount > 0) {
                        console.log('Found', noPointerCount, 'elements with pointer-events: none in', selector);
                    }
                } catch (e) {
                    console.log('Error checking selector:', selector, e);
                }
            });
            
            // Check for specific WordPress admin classes that might interfere
            var wpClasses = [
                '.wp-menu-item',
                '.wp-submenu',
                '.wp-menu-name',
                '.wp-menu-image',
                '.wp-menu-arrow'
            ];
            
            wpClasses.forEach(function(className) {
                var elements = $(className);
                if (elements.length > 0) {
                    console.log('Found WordPress elements with class:', className, 'Count:', elements.length);
                    elements.each(function(index) {
                        if (index < 3) { // Only log first 3
                            console.log('  Element', index, 'pointer-events:', $(this).css('pointer-events'));
                            console.log('  Element', index, 'position:', $(this).css('position'));
                            console.log('  Element', index, 'z-index:', $(this).css('z-index'));
                        }
                    });
                }
            });
            
            // Check for any overlapping elements
            var container = $('.menu-items-list')[0];
            if (container) {
                var containerRect = container.getBoundingClientRect();
                var overlappingElements = [];
                
                $('*').each(function() {
                    if (this !== container && !$.contains(container, this)) {
                        var rect = this.getBoundingClientRect();
                        if (rect.left < containerRect.right && 
                            rect.right > containerRect.left && 
                            rect.top < containerRect.bottom && 
                            rect.bottom > containerRect.top) {
                            overlappingElements.push({
                                element: this,
                                tagName: this.tagName,
                                className: this.className,
                                zIndex: $(this).css('z-index')
                            });
                        }
                    }
                });
                
                console.log('Overlapping elements found:', overlappingElements.length);
                if (overlappingElements.length > 0) {
                    console.log('Overlapping elements:', overlappingElements.slice(0, 5)); // Show first 5
                }
            }
        };

        // Enhanced retry mechanism for sortable initialization
        function initializeSortableWithRetry(maxRetries = 10, delay = 300) {
            var retryCount = 0;
            
            function tryInitialize() {
                console.log('WMO: Attempting to initialize sortable (attempt ' + (retryCount + 1) + ' of ' + maxRetries + ')');
                
                // Check if elements exist
                var $container = $('.menu-items-list');
                var $menu = $('#wmo-sortable-menu');
                var $items = $menu.find('li');
                
                console.log('WMO: Container found:', $container.length);
                console.log('WMO: Menu found:', $menu.length);
                console.log('WMO: Menu items found:', $items.length);
                
                if ($container.length && $menu.length && $items.length > 0) {
                    console.log('WMO: Elements found, initializing sortable');
                    
                    // Try to use the existing wmoInitializeSortable function first
                    if (typeof window.wmoInitializeSortable === 'function') {
                        console.log('WMO: Using existing wmoInitializeSortable function');
                        try {
                            window.wmoInitializeSortable();
                        } catch (error) {
                            console.error('WMO: Error in wmoInitializeSortable:', error);
                            // Fall back to our own initialization
                            initializeSortableFallback($menu);
                        }
                    } else {
                        // Fallback to our own initialization
                        console.log('WMO: wmoInitializeSortable not found, using fallback initialization');
                        initializeSortableFallback($menu);
                    }
                    return true; // Success
                } else {
                    console.log('WMO: Elements not found yet, retrying...');
                    
                    retryCount++;
                    if (retryCount < maxRetries) {
                        setTimeout(tryInitialize, delay);
                    } else {
                        console.error('WMO: Failed to initialize sortable after', maxRetries, 'attempts');
                        alert('Sortable menu not found. Please refresh or check console.');
                        $('.wmo-instructions').append('<div class="notice notice-error"><p>Failed to initialize drag and drop functionality. Please refresh the page and try again.</p></div>');
                    }
                    return false;
                }
            }
            
            // Fallback sortable initialization
            function initializeSortableFallback($menu) {
                // Initialize sortable if jQuery UI is available
                if (typeof $.fn.sortable !== 'undefined') {
                    try {
                        $menu.sortable({
                            items: '> li',
                            handle: '.menu-item-handle',
                            placeholder: 'sortable-placeholder',
                            tolerance: 'pointer',
                            cursor: 'move',
                            axis: 'y',
                            opacity: 0.8,
                            zIndex: 1000,
                            start: function(event, ui) {
                                ui.item.addClass('dragging');
                            },
                            stop: function(event, ui) {
                                ui.item.removeClass('dragging');
                            },
                            update: function(event, ui) {
                                console.log('WMO: Menu order updated');
                                if (typeof window.saveMenuOrder === 'function') {
                                    window.saveMenuOrder();
                                } else {
                                    console.error('WMO: saveMenuOrder function not found');
                                }
                            }
                        }).disableSelection();
                        
                        console.log('WMO: Sortable initialized successfully (fallback)');
                    } catch (error) {
                        console.error('WMO: Error initializing sortable:', error);
                    }
                } else {
                    console.error('WMO: jQuery UI sortable not available');
                    $('.wmo-instructions').append('<div class="notice notice-error"><p>jQuery UI sortable is not available. Please check if jQuery UI is properly loaded.</p></div>');
                }
            }
            
            // Start the retry process
            tryInitialize();
        }

        // Initialize with enhanced retry mechanism if on reorder page
        if ($('.menu-items-list').length > 0) {
            console.log('WMO: Found menu items list, initializing with retry mechanism');
            initializeSortableWithRetry();
        } else {
            console.log('WMO: No menu items list found, skipping sortable initialization');
        }

        // Apply menu colors functionality (existing code)
        var menuColors = {};
        
        // Check if wmo_ajax is defined and has menuColors
        if (typeof wmo_ajax !== 'undefined' && wmo_ajax.menuColors) {
            menuColors = wmo_ajax.menuColors;
        }
        
        // Function to apply color to menu items
        function wmoApplyColor(slug, color, isSubmenu = false, isPreview = false) {
            console.log('WMO: Applying color to', slug, 'with color', color, 'isSubmenu:', isSubmenu, 'isPreview:', isPreview);
            
            if (!color) {
                console.log('WMO: No color provided, skipping');
                return;
            }
            
            // Try different selectors for menu items
            var selectors = [
                '#menu-' + slug,
                '#toplevel_page_' + slug,
                '#adminmenu li[id*="' + slug + '"]',
                '#adminmenu a[href*="' + slug + '"]'
            ];
            
            var menuItems = [];
            selectors.forEach(function(selector) {
                var items = document.querySelectorAll(selector);
                if (items.length > 0) {
                    menuItems = menuItems.concat(Array.from(items));
                }
            });
            
            // Apply color to found items
            menuItems.forEach(function(item) {
                if (isSubmenu) {
                    // For submenu items, apply to the link
                    var link = item.querySelector('a');
                    if (link) {
                        link.style.color = color;
                    }
                } else {
                    // For main menu items, apply to the menu name
                    var menuName = item.querySelector('.wp-menu-name');
                    if (menuName) {
                        menuName.style.color = color;
                    }
                }
            });

            if (menuItems.length === 0) {
                console.log('WMO: Menu item not found for slug:', slug);
                // Debug: Log available menu items
                var allMenuItems = document.querySelectorAll('#adminmenu > li');
                console.log('WMO: Available menu items for debugging:');
                allMenuItems.forEach(function(item, index) {
                    if (index < 10) { // Only log first 10 to avoid spam
                        console.log('  ' + index + ':', item.id, item.className, item.textContent.trim().substring(0, 30));
                    }
                });
            } else {
                console.log('WMO: Successfully applied color to', menuItems.length, 'menu item(s)');
            }

            if (!isSubmenu && !isPreview) {
                var parentItem = document.querySelector('#toplevel_page_ai1wm_export, #menu-' + slug);
                if (parentItem) {
                    var submenuItems = parentItem.querySelectorAll('.wp-submenu li a');
                    submenuItems.forEach(function(submenuItem) {
                        var submenuSlug = submenuItem.textContent.trim().toLowerCase().replace(/\s+/g, '-');
                        if (menuColors[submenuSlug]) {
                            submenuItem.style.color = menuColors[submenuSlug];
                        } else {
                            submenuItem.style.color = 'inherit';
                        }
                    });
                }
            }
        }
        
        // Make wmoApplyColor globally accessible for live preview
        window.wmoApplyColor = wmoApplyColor;
        
        // Function to inject CSS for menu icons (targets pseudo-elements that JS can't reach)
        function wmoInjectIconCSS(slug, color) {
            console.log('WMO: Injecting icon CSS for', slug, 'with color', color);
            
            // Remove any existing preview CSS for this slug
            var existingStyle = document.getElementById('wmo-preview-' + slug);
            if (existingStyle) {
                existingStyle.remove();
            }
            
            if (color) {
                // Create new style element
                var style = document.createElement('style');
                style.id = 'wmo-preview-' + slug;
                style.type = 'text/css';
                
                // Generate CSS for various menu icon selectors
                var css = '';
                var selectors = [
                    '#menu-' + slug + ' .wp-menu-image:before',
                    '#menu-' + slug + ' .dashicons:before',
                    '#toplevel_page_' + slug + ' .wp-menu-image:before',
                    '#toplevel_page_' + slug + ' .dashicons:before',
                    '#adminmenu li[id*="' + slug + '"] .wp-menu-image:before',
                    '#adminmenu li[id*="' + slug + '"] .dashicons:before',
                    '#menu-' + slug + ' .wp-menu-image',
                    '#menu-' + slug + ' .wp-menu-image',
                    '#toplevel_page_' + slug + ' .wp-menu-image',
                    '#toplevel_page_' + slug + ' .dashicons',
                    '#adminmenu li[id*="' + slug + '"] .wp-menu-image',
                    '#adminmenu li[id*="' + slug + '"] .dashicons'
                ];
                
                selectors.forEach(function(selector) {
                    css += selector + ' { color: ' + color + ' !important; }\n';
                });
                
                style.innerHTML = css;
                document.head.appendChild(style);
                
                console.log('WMO: Injected icon CSS:', css);
            }
        }
        
        // Function to clean up preview CSS when needed
        function wmoCleanupPreviewCSS(slug) {
            var existingStyle = document.getElementById('wmo-preview-' + slug);
            if (existingStyle) {
                existingStyle.remove();
                console.log('WMO: Cleaned up preview CSS for', slug);
            }
        }

        // Initial application of stored colors
        for (var slug in menuColors) {
            if (menuColors[slug]) {
                var isSubmenu = slug.includes('-');
                wmoApplyColor(slug, menuColors[slug], isSubmenu);
            }
        }
    });
})(jQuery);