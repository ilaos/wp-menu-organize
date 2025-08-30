(function($) {
    'use strict';

    // Debug code to verify script loading
    console.log('=== WMO Icon Picker: Script loaded ===');
    console.log('jQuery available:', typeof jQuery);
    console.log('jQuery version:', jQuery.fn.jquery);

    // COORDINATION FLAGS - Prevent double initialization
    if (window.wmoIconPickerInitialized) {
        console.log('WMO Icon Picker: Already initialized, skipping duplicate initialization');
        return;
    }
    window.wmoIconPickerInitialized = true;

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

    // Auto-save timeouts storage for icons
    var iconAutoSaveTimeouts = {};

    // Initialize icon picker functionality
    function initIconPickers() {
        console.log('WMO Icon Picker: Initializing icon picker functionality');
        
        // Initialize icon type selector
        initIconTypeSelector();
        
        // Initialize icon selection
        initIconSelection();
        
        // Initialize search functionality
        initSearchFunctionality();
        
        // Initialize enable/disable toggle
        initIconToggle();
        
        // Load existing icon selections
        loadExistingIcons();
    }

    // Initialize icon type selector (Emoji vs Dashicon)
    function initIconTypeSelector() {
        console.log('WMO Icon Picker: Initializing icon type selector');
        
        $(document).on('change', '.icon-type-selector', function() {
            var $selector = $(this);
            var $iconSection = $selector.closest('.icon-section');
            var selectedType = $selector.val();
            var menuSlug = $iconSection.find('.enable-custom-icon').data('original-slug');
            
            console.log('WMO Icon Picker: Icon type changed to', selectedType, 'for menu', menuSlug);
            
            // Show/hide appropriate picker
            if (selectedType === 'emoji') {
                $iconSection.find('.emoji-picker').show();
                $iconSection.find('.dashicon-picker').hide();
            } else {
                $iconSection.find('.emoji-picker').hide();
                $iconSection.find('.dashicon-picker').show();
            }
            
            // Clear previous selection
            $iconSection.find('.selected-emoji, .selected-dashicon').val('');
            updateIconPreview($iconSection, '', selectedType);
        });
    }

    // Initialize icon selection functionality
    function initIconSelection() {
        console.log('WMO Icon Picker: Initializing icon selection');
        
        // Emoji selection
        $(document).on('click', '.emoji-option', function() {
            var $option = $(this);
            var $iconSection = $option.closest('.icon-section');
            var emoji = $option.data('emoji');
            var menuSlug = $iconSection.find('.enable-custom-icon').data('original-slug');
            
            console.log('WMO Icon Picker: Emoji selected:', emoji, 'for menu', menuSlug);
            
            // Update selection
            $iconSection.find('.selected-emoji').val(emoji);
            $iconSection.find('.emoji-option').removeClass('selected');
            $option.addClass('selected');
            
            // Update preview
            updateIconPreview($iconSection, emoji, 'emoji');
            
            // Preview only
            var $menuItem = $('#adminmenu li#menu-' + menuSlug + ' .wp-menu-image');
            if (!$menuItem.length) $menuItem = $('#adminmenu li#toplevel_page_' + menuSlug + ' .wp-menu-image');
            if ($menuItem.length && !$menuItem.data('preview-applied')) {
                $menuItem.data('original-icon', $menuItem.html()).data('preview-applied', true);
                $menuItem.empty().append('<span class="wmo-emoji-icon">' + emoji + '</span>');
            }
            
            // Auto-save
            if (menuSlug) {
                wmoAutoSaveIcon(menuSlug, 'emoji', emoji);
            }
        });
        
        // Dashicon selection
        $(document).on('click', '.dashicon-option', function() {
            var $option = $(this);
            var $iconSection = $option.closest('.icon-section');
            var dashicon = $option.data('dashicon');
            var menuSlug = $iconSection.find('.enable-custom-icon').data('original-slug');
            
            console.log('WMO Icon Picker: Dashicon selected:', dashicon, 'for menu', menuSlug);
            
            // Update selection
            $iconSection.find('.selected-dashicon').val(dashicon);
            $iconSection.find('.dashicon-option').removeClass('selected');
            $option.addClass('selected');
            
            // Update preview
            updateIconPreview($iconSection, dashicon, 'dashicon');
            
            // Preview only
            var $menuItem = $('#adminmenu li#menu-' + menuSlug + ' .wp-menu-image');
            if (!$menuItem.length) $menuItem = $('#adminmenu li#toplevel_page_' + menuSlug + ' .wp-menu-image');
            if ($menuItem.length && !$menuItem.data('preview-applied')) {
                $menuItem.data('original-icon', $menuItem.html()).data('preview-applied', true);
                $menuItem.empty().append('<span class="dashicons ' + dashicon + '"></span>');
            }
            
            // Auto-save
            if (menuSlug) {
                wmoAutoSaveIcon(menuSlug, 'dashicon', dashicon);
            }
        });
    }

    // Initialize search functionality
    function initSearchFunctionality() {
        console.log('WMO Icon Picker: Initializing search functionality');
        
        $(document).on('input', '.icon-search-input', debounce(function() {
            var $searchInput = $(this);
            var $iconSection = $searchInput.closest('.icon-section');
            var searchTerm = $searchInput.val().toLowerCase();
            var $activePicker = $iconSection.find('.emoji-picker:visible, .dashicon-picker:visible');
            
            console.log('WMO Icon Picker: Searching for:', searchTerm);
            
            if ($activePicker.hasClass('dashicon-picker')) {
                // Search dashicons
                $activePicker.find('.dashicon-option').each(function() {
                    var $option = $(this);
                    var iconName = $option.data('dashicon').toLowerCase();
                    var shouldShow = iconName.includes(searchTerm);
                    $option.toggle(shouldShow);
                });
            } else {
                // Search emojis (by category for now)
                $activePicker.find('.emoji-option').each(function() {
                    var $option = $(this);
                    var category = $option.data('category').toLowerCase();
                    var shouldShow = category.includes(searchTerm) || searchTerm === '';
                    $option.toggle(shouldShow);
                });
            }
        }, 300));
    }

    // Initialize icon enable/disable toggle
    function initIconToggle() {
        console.log('WMO Icon Picker: Initializing icon toggle');
        
        $(document).on('change', '.enable-custom-icon', function() {
            var $checkbox = $(this);
            var $iconSection = $checkbox.closest('.icon-section');
            var enabled = $checkbox.is(':checked');
            var menuSlug = $checkbox.data('original-slug');
            
            console.log('WMO Icon Picker: Icon toggle changed to', enabled, 'for menu', menuSlug);
            
            // Show/hide icon settings
            $iconSection.find('.icon-settings').toggle(enabled);
            
            if (enabled) {
                // Load existing icon if any
                loadExistingIconForMenu(menuSlug);
            } else {
                clearIconSelection($iconSection);
                if (menuSlug) {
                    wmoAutoSaveIcon(menuSlug, '', '');
                    var $menuItem = $('#adminmenu li#menu-' + menuSlug + ' .wp-menu-image');
                    if (!$menuItem.length) $menuItem = $('#adminmenu li#toplevel_page_' + menuSlug + ' .wp-menu-image');
                    if ($menuItem.length && $menuItem.data('original-icon')) {
                        $menuItem.html($menuItem.data('original-icon')).removeData('preview-applied');
                    }
                }
            }
        });
    }

    // Update icon preview
    function updateIconPreview($iconSection, iconValue, iconType) {
        var $preview = $iconSection.find('.preview-icon');
        var $menuName = $iconSection.find('.menu-name');
        var menuSlug = $iconSection.find('.enable-custom-icon').data('original-slug');
        
        if (iconValue && iconType) {
            if (iconType === 'emoji') {
                $preview.text(iconValue);
            } else if (iconType === 'dashicon') {
                $preview.html('<span class="dashicons ' + iconValue + '"></span>');
            }
            $preview.show();
            
            // Live preview on admin menu
            if (menuSlug) {
                var $menuItem = $('#adminmenu li#toplevel_page_' + menuSlug + ' .wp-menu-image');
                if ($menuItem.length) {
                    $menuItem.empty(); // Clear original content
                    if (iconType === 'dashicon') {
                        $menuItem.append('<span class="dashicons ' + iconValue + '"></span>');
                    } else if (iconType === 'emoji') {
                        $menuItem.append('<span class="wmo-emoji-icon">' + iconValue + '</span>');
                    }
                }
            }
        } else {
            $preview.hide();
            
            // Clear live preview on admin menu
            if (menuSlug) {
                var $menuItem = $('#adminmenu li#toplevel_page_' + menuSlug + ' .wp-menu-image');
                if ($menuItem.length) {
                    // Restore original icon by reloading the page or triggering a refresh
                    location.reload();
                }
            }
        }
        
        console.log('WMO Icon Picker: Updated preview for', iconType, ':', iconValue);
    }

    // Load existing icons on page load
    function loadExistingIcons() {
        console.log('WMO Icon Picker: Loading existing icons');
        
        // Check if saved icons are available from PHP
        if (typeof wmo_saved_icons !== 'undefined') {
            console.log('WMO Icon Picker: Found saved icons:', wmo_saved_icons);
            
            Object.keys(wmo_saved_icons).forEach(function(menuSlug) {
                loadExistingIconForMenu(menuSlug);
            });
        }
    }

    // Load existing icon for specific menu
    function loadExistingIconForMenu(menuSlug) {
        var $iconSection = $('.icon-section').filter(function() {
            return $(this).find('.enable-custom-icon').data('original-slug') === menuSlug;
        });
        
        if ($iconSection.length === 0) {
            console.log('WMO Icon Picker: No icon section found for menu', menuSlug);
            return;
        }
        
        // Check if we have saved icon data
        if (typeof wmo_saved_icons !== 'undefined' && wmo_saved_icons[menuSlug]) {
            var iconData = wmo_saved_icons[menuSlug];
            var iconType = iconData.type;
            var iconValue = iconData.value;
            
            console.log('WMO Icon Picker: Loading saved icon for', menuSlug, ':', iconType, iconValue);
            
            // Enable the icon section
            $iconSection.find('.enable-custom-icon').prop('checked', true);
            $iconSection.find('.icon-settings').show();
            
            // Set the icon type
            $iconSection.find('.icon-type-selector').val(iconType).trigger('change');
            
            // Set the icon value
            if (iconType === 'emoji') {
                $iconSection.find('.selected-emoji').val(iconValue);
                $iconSection.find('.emoji-option[data-emoji="' + iconValue + '"]').addClass('selected');
            } else if (iconType === 'dashicon') {
                $iconSection.find('.selected-dashicon').val(iconValue);
                $iconSection.find('.dashicon-option[data-dashicon="' + iconValue + '"]').addClass('selected');
            }
            
            // Update preview
            updateIconPreview($iconSection, iconValue, iconType);
        }
    }

    // Clear icon selection
    function clearIconSelection($iconSection) {
        $iconSection.find('.selected-emoji, .selected-dashicon').val('');
        $iconSection.find('.emoji-option, .dashicon-option').removeClass('selected');
        updateIconPreview($iconSection, '', '');
        
        console.log('WMO Icon Picker: Cleared icon selection');
    }

    // Auto-save icon function
    function wmoAutoSaveIcon(menuSlug, iconType, iconValue) {
        // Clear existing timeout
        if (iconAutoSaveTimeouts[menuSlug]) {
            clearTimeout(iconAutoSaveTimeouts[menuSlug]);
        }

        // Show saving indicator
        var $iconSection = $('.icon-section').filter(function() {
            return $(this).find('.enable-custom-icon').data('original-slug') === menuSlug;
        });
        wmoShowIconSavingIndicator($iconSection, 'Saving...', 'info');

        // Set new timeout
        iconAutoSaveTimeouts[menuSlug] = setTimeout(function() {
            $.ajax({
                url: wmo_ajax.ajax_url,
                method: 'POST',
                data: {
                    action: 'wmo_save_icon',
                    menu_id: menuSlug,
                    icon_type: iconType,
                    icon_value: iconValue,
                    nonce: wmo_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        wmoShowIconSavingIndicator($iconSection, 'Saved!', 'success');
                        console.log('WMO Icon Picker: Icon saved successfully for', menuSlug);
                    } else {
                        wmoShowIconSavingIndicator($iconSection, 'Error saving', 'error');
                        console.error('WMO Icon Picker: Icon save failed:', response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    wmoShowIconSavingIndicator($iconSection, 'Network error', 'error');
                    console.error('WMO Icon Picker: Icon save AJAX error:', textStatus, errorThrown);
                }
            });
        }, 500);
    }

    // Show saving indicator for icons
    function wmoShowIconSavingIndicator($iconSection, message, type) {
        var $indicator = $iconSection.find('.wmo-icon-saving-indicator');
        
        if ($indicator.length === 0) {
            $indicator = $('<div class="wmo-icon-saving-indicator"></div>');
            $iconSection.find('.icon-settings').append($indicator);
        }
        
        $indicator.text(message).removeClass('success error info').addClass(type);
        
        if (type === 'success') {
            setTimeout(function() {
                $indicator.fadeOut();
            }, 2000);
        }
    }

    // Initialize everything when document is ready
    $(document).ready(function() {
        console.log('WMO Icon Picker: Document ready, initializing icon picker');
        
        // Initialize icon pickers
        initIconPickers();
        
        // Apply existing icons on page load with delay
        setTimeout(function() {
            console.log('WMO Icon Picker: Applying existing icons on page load');
            loadExistingIcons();
        }, 1000);
    });

})(jQuery);
