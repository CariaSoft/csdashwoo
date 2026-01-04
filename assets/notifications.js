jQuery(function($) {
    // Notification panel functionality
    let notificationPanel = $(
        '<div id="csdashwoo-notification-panel" class="csdashwoo-notif-panel" style="display:none;">' +
        '<div class="csdashwoo-notif-header">' +
        '<h3>Bildirimler</h3>' +
        '<span class="csdashwoo-close-panel">&times;</span>' +
        '</div>' +
        '<div class="csdashwoo-notif-content">' +
        '<div class="csdashwoo-notif-list"></div>' +
        '</div>' +
        '</div>'
    );

    $('body').append(notificationPanel);

    // Show notification panel when clicking the icon
    $(document).on('click', '#wp-admin-bar-csdashwoo-notifications', function(e) {
        e.preventDefault();
        $('#csdashwoo-notification-panel').toggle();
        loadNotifications();
    });

    // Close panel when clicking the close button
    $(document).on('click', '.csdashwoo-close-panel', function() {
        $('#csdashwoo-notification-panel').hide();
    });

    // Close panel when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#csdashwoo-notification-panel, #wp-admin-bar-csdashwoo-notifications').length) {
            $('#csdashwoo-notification-panel').hide();
        }
    });

    // Dismiss notification
    $(document).on('click', '.csdashwoo-dismiss-notif', function() {
        let notifId = $(this).data('id');
        let notifElement = $(this).closest('.csdashwoo-notif-item');

        $.post(csdashwoo_notif.ajax_url, {
            action: 'csdashwoo_dismiss_notification',
            nonce: csdashwoo_notif.nonce,
            id: notifId
        }, function(response) {
            if (response.success) {
                notifElement.fadeOut(300, function() {
                    $(this).remove();
                    updateNotificationCount();
                });
            }
        });
    });

    // Load notifications
    function loadNotifications() {
        $.post(csdashwoo_notif.ajax_url, {
            action: 'csdashwoo_get_notifications',
            nonce: csdashwoo_notif.nonce
        }, function(response) {
            if (response.success) {
                renderNotifications(response.data.notifications);
            }
        });
    }

    // Render notifications
    function renderNotifications(notifications) {
        let list = $('.csdashwoo-notif-list');
        list.empty();

        if (notifications.length === 0) {
            list.html('<p class="no-notifications">Yeni bildiriminiz yok</p>');
            return;
        }

        $.each(notifications, function(index, notif) {
            let itemClass = notif.read ? 'read' : 'unread';
            let notifItem = $(
                '<div class="csdashwoo-notif-item ' + itemClass + '" data-id="' + notif.id + '">' +
                '<div class="csdashwoo-notif-title">' + notif.title + '</div>' +
                '<div class="csdashwoo-notif-message">' + notif.message + '</div>' +
                '<div class="csdashwoo-notif-meta">' +
                '<span class="csdashwoo-notif-date">' + notif.date + '</span>' +
                '<button class="csdashwoo-dismiss-notif" data-id="' + notif.id + '">Kapat</button>' +
                '</div>' +
                '</div>'
            );

            if (notif.link) {
                notifItem.on('click', function() {
                    window.location.href = notif.link;
                });
            }

            list.append(notifItem);
        });
    }

    // Update notification count in admin bar
    function updateNotificationCount() {
        $.post(csdashwoo_notif.ajax_url, {
            action: 'csdashwoo_get_notifications',
            nonce: csdashwoo_notif.nonce
        }, function(response) {
            if (response.success) {
                let count = response.data.notifications.length;
                let icon = $('#wp-admin-bar-csdashwoo-notifications .ab-item');
                if (count > 0) {
                    icon.html('🔔 <span class="notif-count">' + count + '</span>');
                } else {
                    icon.html('🔔');
                }
            }
        });
    }
});