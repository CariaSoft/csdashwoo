jQuery(function ($) {

    $("#active-menu, #all-menu").sortable({
        connectWith: ".menu-box",
        placeholder: "menu-placeholder"
    }).disableSelection();

    $("#save-menu").on("click", function () {
        let layout = [];

        $("#active-menu li").each(function () {
            layout.push($(this).data("slug"));
        });

        $.post(csdashwoo.ajax, {
            action: "csdashwoo_save_menu",
            nonce: csdashwoo.nonce,
            layout: layout
        }, function () {
            alert("Menü kaydedildi");
            location.reload();
        });
    });

});