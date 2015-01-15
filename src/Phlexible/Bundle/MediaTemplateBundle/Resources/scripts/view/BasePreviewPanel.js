Ext.define('Phlexible.mediatemplates.BasePreviewPanel', {
    extend: 'Ext.panel.Panel',

    title: Phlexible.mediatemplates.Strings.preview,
    strings: Phlexible.mediatemplates.Strings,
    cls: 'p-mediatemplates-preview-panel',
    border: true,
    bodyStyle: 'padding: 5px',
    autoScroll: true,
    disabled: true,

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                itemId: 'empty',
                border: false,
                html: this.strings.no_preview_available,
                bodyPadding: '0 0 10 0'
            },
            {
                itemId: 'debug',
                xtype: 'fieldset',
                title: this.strings.debug,
                collapsible: true,
                autoHeight: true,
                hidden: true,
                items: [
                    {
                        border: false,
                        html: '&nbsp;'
                    }
                ]
            },
            {
                itemId: 'preview',
                border: false,
                html: '&nbsp;',
                bodyPadding: 10
            }
        ];
    },

    getEmptyPanel: function() {
        return this.getComponent('empty');
    },

    getDebugFieldSet: function() {
        return this.getComponent('debug');
    },

    getDebugPanel: function() {
        return this.getDebugFieldSet().getComponent(0);
    },

    getPreviewPanel: function() {
        return this.getComponent('preview');
    },

    clear: function () {
        this.getEmptyPanel().body.update('&nbsp;');
        this.getDebugPanel().body.update('&nbsp;');
        this.getPreviewPanel().body.update('&nbsp;');
    },

    createUrl: function () {
        throw new Error('createUrl() has to be implemented in Classes extending Phlexible.mediatemplates.BasePreviewPanel.');
    },

    createPreview: function (params, debug) {
        Ext.Ajax.request({
            url: this.createUrl(),
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.updatePreview(data.data, debug);
                }
                else {
                    this.clear();
                    this.updateFailure(data.msg);
                }
            },
            scope: this
        });
    },

    updateFailure: function (msg) {
        this.getEmptyPanel().body.update(msg);
    },

    getResult: Ext.emptyFn,
    createPreviewDomHelperConfig: Ext.emptyFn,

    updatePreview: function (data, debug) {
        this.getEmptyPanel().body.update(this.getResult(data));

        if (debug) {
            this.getDebugFieldSet().show();
            this.getDebugPanel().body.update('<pre>' + data.debug + '</pre>');
        }
        else {
            this.getDebugFieldSet().hide();
        }

        Ext.DomHelper.overwrite(this.getPreviewPanel().body, this.createPreviewDomHelperConfig(data));

        this.getPreviewPanel().setHeight(data.height);

        this.enable();
    }
});
