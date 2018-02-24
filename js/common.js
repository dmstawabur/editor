/* global Gplcart, jQuery, CodeMirror */
(function (Gplcart, $) {

    "use strict";

    /**
     * Setup Code Mirror
     */
    Gplcart.onload.setCodemirror = function () {

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

        if (Gplcart.settings.editor) {

            if (Gplcart.settings.editor.file_extension) {
                ext = Gplcart.settings.editor.file_extension;
            }

            if (Gplcart.settings.editor.readonly) {
                readonly = true;
            }
        }

        mode = map[ext] || map.php;

        settings = {
            mode: mode,
            lineNumbers: true,
            readOnly: readonly,
            theme: Gplcart.settings.codemirror.theme
        };

        CodeMirror.fromTextArea(textarea, settings);
    };

})(Gplcart, jQuery);