/* global GplCart, jQuery, CodeMirror */
(function (GplCart, $) {

    "use strict";

    /**
     * Setup Code Mirror
     * @returns {undefined}
     */
    GplCart.onload.setCodemirror = function () {

        if (typeof CodeMirror === 'undefined') {
            return;
        }

        var textarea,
                map,
                ext,
                mode,
                settings,
                readonly = false,
                element = $('*[data-codemirror="true"]');

        textarea = element.get(0);

        if ($.isEmptyObject(textarea)) {
            return;
        }

        map = {
            css: {name: 'css'},
            twig: {name: 'twig'},
            js: {name: 'javascript'},
            php: {name: 'htmlmixed'}
        };

        if (GplCart.settings.editor) {
            if (GplCart.settings.editor.file_extension) {
                ext = GplCart.settings.editor.file_extension;
            }
            if (GplCart.settings.editor.readonly) {
                readonly = true;
            }
        }

        mode = map[ext] || map.php;

        settings = {
            mode: mode,
            lineNumbers: true,
            readOnly: readonly,
            theme: GplCart.settings.codemirror.theme
        };

        CodeMirror.fromTextArea(textarea, settings);
    };

})(GplCart, jQuery);