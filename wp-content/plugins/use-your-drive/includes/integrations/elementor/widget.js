(function ($) {
    'use strict';

    $(window).on('elementor/frontend/init', function () {
        elementor.channels.editor.on('wpcp:editor:edit_useyourdrive_shortcode', openShortcodeBuilder);
        elementorFrontend.hooks.addAction('frontend/element_ready/wpcp-useyourdrive.default', function () {
            $('.UseyourDrive').parent().trigger('inview');
        });
    });

    function openShortcodeBuilder(view) {
        window.wpcp_uyd_elementor_add_content = function (value) {
            view._parent.model.setSetting('shortcode', value);
            window.parent.jQuery('.elementor-control-shortcode textarea').trigger('input');
            window.modal_action.close();
            $('#wpcp-modal-action.UseyourDrive').remove();
        };

        if ($('#wpcp-modal-action.UseyourDrive').length > 0) {
            if (typeof window.modal_action !== 'undefined') {
                window.modal_action.close();
            }
            $('#wpcp-modal-action.UseyourDrive').remove();
        }

        /* Build the  Dialog */
        let modalbuttons = '';
        let modalheader = $(
            `<div class="wpcp-modal-header" tabindex="0">                          
                    <a tabindex="0" class="close-button"  onclick="window.modal_action.close();"><i class="eva eva-close eva-lg" aria-hidden="true"></i></a>
                </div>`
        );
        let modalbody = $('<div class="wpcp-modal-body" tabindex="0" style="display:none"></div>');
        let modalfooter = $(
            '<div class="wpcp-modal-footer" style="display:none"><div class="wpcp-modal-buttons"></div></div>'
        );
        let modaldialog = $(
            '<div id="wpcp-modal-action" class="UseyourDrive wpcp wpcp-modal wpcp-modal95 wpcp-modal-minimal light"><div class="modal-dialog"><div class="modal-content"><div class="loading"><div class="loader-beat"></div></div></div></div></div>'
        );

        $('body').append(modaldialog);

        var raw_content = view._parent.model.getSetting('shortcode', 'true');
        var shortcode = raw_content.replace('</p>', '').replace('<p>', '');
        var query = 'shortcode=' + WPCP_shortcodeEncode(shortcode);

        var $iframe_template = $(
            "<iframe src='" +
                UseyourDrive_vars.ajax_url +
                '?action=useyourdrive-getpopup&type=modules&callback=wpcp_uyd_elementor_add_content&' +
                query +
                "' width='100%' tabindex='-1' style='border:none' title=''></iframe>"
        );
        var $iframe = $iframe_template.appendTo(modalbody);

        $('#wpcp-modal-action.UseyourDrive .modal-content').append(modalheader, modalbody, modalfooter);

        $iframe.on('load', function () {
            $('.wpcp-modal-body').fadeIn();
            $('.wpcp-modal-footer').fadeIn();
            $('.modal-content .loading:first').fadeOut();
        });

        /* Open the Dialog */
        let modal_action = new RModal(document.getElementById('wpcp-modal-action'), {
            bodyClass: 'rmodal-open',
            dialogOpenClass: 'animated slideInDown',
            dialogCloseClass: 'animated slideOutUp',
            escapeClose: true,
        });
        document.addEventListener(
            'keydown',
            function (ev) {
                modal_action.keydown(ev);
            },
            false
        );
        modal_action.open();
        window.modal_action = modal_action;
    }
})(jQuery);
