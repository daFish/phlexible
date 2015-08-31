Ext.define('Phlexible.mediatemplate.view.Preview', {
    extend: 'Ext.panel.Panel',
    xtype: 'mediatemplate.preview',

    cls: 'p-mediatemplate-preview',
    border: true,
    bodyPadding: 5,
    autoScroll: true,
    disabled: true,

    noPreviewAvailableText: '_noPreviewAvailableText',
    debugText: '_debugText',

    initComponent: function () {
        this.html = this.noPreviewAvailableText;

        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            dock: 'right',
            xtype: 'panel',
            itemId: 'debug',
            headerPosition: 'left',
            title: this.debugText,
            width: 300,
            collapsible: true,
            collapsed: true,
            html: '&nbsp;'
        }];
    },

    getEmptyPanel: function() {
        return this.getComponent('empty');
    },

    getDebugPanel: function() {
        return this.getDockedComponent('debug');
    },

    getPreviewPanel: function() {
        return this.getComponent('preview');
    },

    clear: function () {
        this.getEmptyPanel().body.update('&nbsp;');
        this.getDebugPanel().body.update('&nbsp;');
        this.getPreviewPanel().body.update('&nbsp;');
    },

    requestPreview: function (mediaTemplate, url, domHelper, resultFormatter, debug, file) {
        var data = mediaTemplate.getData();
        data.createdAt = Ext.Date.format(data.modifiedAt, 'Y-m-d H:i:s');
        data.modifiedAt = Ext.Date.format(data.modifiedAt, 'Y-m-d H:i:s');

        Ext.Ajax.request({
            url: url,
            params: {
                params: Ext.encode(data),
                file: file
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.updatePreview(data.data, domHelper, resultFormatter, debug);
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

    updatePreview: function (data, domHelper, resultFormatter) {
        this.setTitle(resultFormatter(data));

        this.getDebugPanel().update('<pre>' + JSON.stringify(data, null, '  ') + '</pre>');

        this.update(domHelper(data));

        if (data.height) {
            //this.setHeight(data.height + 20);
        }

        this.enable();
    }
});
