Ext.define('Phlexible.mediatemplate.view.audio.Preview', {
    extend: 'Phlexible.mediatemplate.panel.PreviewPanel',
    requires: ['Phlexible.mediatemplate.panel.PreviewPanel'],

    xtype: 'mediatemplate.audio.preview',

    createUrl: function () {
        return Phlexible.Router.generate('mediatemplates_preview_audio');
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
        //var link = 'file=' + Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()});

        return {
            tag: 'audio',
            autoplay: 'autoplay',
            controls: 'controls',
            children: [
                {
                    tag: 'source',
                    src: Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()}),
                    type: data.mimetype
                }
            ]
            /*
             src: Phlexible.bundleAsset('/mediamanager/flash/player.swf'),
             width: 300,
             height: 20,
             allowfullscreen: 'false',
             allowscriptaccess: 'always',
             quality: 'high',
             wmode: 'transparent',
             flashvars: link,
             type: 'application/x-shockwave-flash'
             */
        };
    }
});