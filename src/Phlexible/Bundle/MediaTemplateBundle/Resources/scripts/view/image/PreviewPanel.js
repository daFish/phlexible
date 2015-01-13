Ext.define('Phlexible.mediatemplates.image.PreviewPanel', {
    extend: 'Phlexible.mediatemplates.BasePreviewPanel',
    alias: 'widget.mediatemplates-image-preview',

    createUrl: function () {
        return Phlexible.Router.generate('mediatemplates_preview_image');
    },

    initComponent: function () {
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                xtype: 'tbtext',
                text: this.strings.change_background_color_to
            },
                {
                    text: this.strings.white,
                    handler: function () {
                        this.getPreviewFieldSet().body.setStyle('background-color', '#FFFFFF');
                        this.getTopToolbar().items.items[4].setValue('#FFFFFF');
                    },
                    scope: this
                },
                {
                    text: this.strings.black,
                    handler: function () {
                        this.getPreviewFieldSet().body.setStyle('background-color', '#000000');
                        this.getTopToolbar().items.items[4].setValue('#000000');
                    },
                    scope: this
                },
                {
                    text: this.strings.random,
                    handler: function () {
                        var red = Math.floor(Math.random() * 255);
                        var green = Math.floor(Math.random() * 255);
                        var blue = Math.floor(Math.random() * 255);
                        var color = this.toColor(red, green, blue);
                        this.getPreviewFieldSet().body.setStyle('background-color', color);
                        this.getTopToolbar().items.items[4].setValue(color);
                    },
                    scope: this
                },
                {
                    type: 'colorfield', // TODO: colorfield
                    xtype: 'textfield',
                    value: '#FFFFFF',
                    enableKeyEvents: true,
                    listeners: {
                        keyup: function (f, e) {
                            if (e.keyCode === 13) {
                                this.getPreviewFieldSet().body.setStyle('background-color', f.getValue());
                            }
                        },
                        select: function (f, v) {
                            if (this.getPreviewFieldSet().rendered) {
                                this.getPreviewFieldSet().body.setStyle('background-color', v);
                            }
                        },
                        scope: this
                    }
                }]
        }]
    },

    toColor: function (r, g, b) {
        var result = "#";

        if (r >= 0 && r <= 15) {
            result += "0" + r.toString(16);
        }
        else if (r >= 16 && r <= 255) {
            result += r.toString(16);
        }

        if (g >= 0 && g <= 15) {
            result += "0" + g.toString(16);
        }
        else if (g >= 16 && g <= 255) {
            result += g.toString(16);
        }

        if (b >= 0 && b <= 15) {
            result += "0" + b.toString(16);
        }
        else if (b >= 16 && b <= 255) {
            result += b.toString(16);
        }

        return result.toUpperCase();

    },

    createPreview: function (params, debug) {
        if (params['method']) {
            params.xmethod = params['method'];
            delete params['method'];
        }

        Phlexible.mediatemplates.image.PreviewPanel.superclass.createPreview.call(this, params, debug);
    },

    getResult: function (data) {
        var s = '';
        if (data.template) {
            s += data.template + ', ';
        }
        s += data.width + ' x ' + data.height;
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
            tag: 'img',
            alt: 'Loading image preview',
            src: Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()})
        };
    }
});
