Ext.define('Phlexible.mediatemplates.view.pdf2swf.PreviewPanel', {
    extend: 'Phlexible.mediatemplates.view.BasePreviewPanel',
    alias: 'widget.mediatemplates-pdf2swf-preview',

    createUrl: function () {
        return Phlexible.Router.generate('mediatemplates_preview_pdf');
    },

    getResult: function (data) {
        var s = '';
        if (data.template) {
            s += data.template;
        }
        if (data.format) {
            s += ', ' + data.format;
        }
        if (data.size) {
            s += ', ' + Phlexible.Format.size(data.size);
        }
        return s;
    },

    createPreviewDomHelperConfig: function (data) {
        return {
            tag: 'embed',
            src: Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()}),
            width: 400,
            height: 500,
            wmode: 'transparent',
            type: data.mimetype
        };
    }
});
