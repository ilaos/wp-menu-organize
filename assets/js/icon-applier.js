(function($) {
    $(document).ready(function() {
        if (typeof wmo_saved_icons !== 'undefined') {
            $.each(wmo_saved_icons, function(menuSlug, iconData) {
                var $menuItem = $('#adminmenu li#menu-' + menuSlug + ' .wp-menu-image'); // Adjusted selector based on debug.log (e.g., #menu-dashboard)
                if (!$menuItem.length) {
                    $menuItem = $('#adminmenu li#toplevel_page_' + menuSlug + ' .wp-menu-image'); // Fallback for some slugs
                }
                if ($menuItem.length) {
                    // Store original if not already
                    if (!$menuItem.data('original-icon')) {
                        $menuItem.data('original-icon', $menuItem.html());
                    }
                    $menuItem.empty();
                    if (iconData.type === 'dashicon') {
                        $menuItem.append('<span class="dashicons ' + iconData.value + '"></span>');
                    } else if (iconData.type === 'emoji') {
                        $menuItem.append('<span class="wmo-emoji-icon">' + iconData.value + '</span>');
                    }
                }
            });
        }
    });
})(jQuery);
