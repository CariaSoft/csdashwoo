jQuery(document).ready(function($) {
    const menu = csdashwooMenu.menu;

    const $list = $('#csdashwoo-menu-list');

    // Menüyü render et
    function renderMenu() {
        $list.empty();
        Object.keys(menu).forEach(key => {
            const item = menu[key];
            const $li = $(`
                <li data-slug="${key}" class="menu-item">
                    <div class="menu-title">${item.title}</div>
                    <select class="menu-roles" multiple>
                        <option value="administrator" ${item.roles.includes('administrator') ? 'selected' : ''}>Yönetici</option>
                        <option value="shop_manager" ${item.roles.includes('shop_manager') ? 'selected' : ''}>Mağaza Yöneticisi</option>
                        <option value="editor" ${item.roles.includes('editor') ? 'selected' : ''}>Editör</option>
                    </select>
                </li>
            `);
            $list.append($li);
        });
    }

    renderMenu();

    // Drag & drop
    $list.sortable({
        placeholder: "menu-placeholder",
        update: function() {
            // Sıralama değiştiğinde kaydedilebilir hale getir
        }
    });

    // Kaydet butonu
    $('#csdashwoo-menu-form').on('submit', function(e) {
        e.preventDefault();

        const newLayout = {};
        $list.find('li').each(function() {
            const slug = $(this).data('slug');
            const roles = $(this).find('.menu-roles').val() || [];
            newLayout[slug] = { roles };
        });

        $.post(csdashwooMenu.ajaxurl, {
            action: 'csdashwoo_save_menu',
            nonce: csdashwooMenu.nonce,
            menu_layout: newLayout
        }, function(response) {
            alert(response.data);
        });
    });
});