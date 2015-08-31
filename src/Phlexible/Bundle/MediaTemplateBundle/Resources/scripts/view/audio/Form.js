Ext.define('Phlexible.mediatemplate.view.audio.Form', {
    extend: 'Ext.form.FormPanel',
    requires: [
        'Phlexible.mediatemplate.view.audio.Fields'
    ],
    xtype: 'mediatemplate.audio.form',

    iconCls: Phlexible.mediatemplate.TemplateIcons.audio,
    autoScroll: true,
    labelAlign: 'top',
    disabled: true,
    layout: 'accordion',

    audioText: '_audioText',
    bitrateText: '_bitrateText',
    bitrateHelpText: '_bitrateHelpText',
    samplerateText: '_samplerateText',
    samplerateHelpText: '_samplerateHelpText',
    samplebitsText: '_samplebitsText',
    samplebitsHelpText: '_samplebitsHelpText',
    channelsText: '_channelsText',
    channelsHelpText: '_channelsHelpText',
    saveText: '_saveText',
    previewText: '_previewText',

    initComponent: function () {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatemplate.audio.fields'
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            itemId: 'tbar',
            items: [
                {
                    itemId: 'saveBtn',
                    text: this.saveText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                    formBind: true,
                    handler: function() {
                        this.fireEvent('save');
                    },
                    scope: this
                },
                '->',
                {
                    text: this.previewText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.PREVIEW),
                    formBind: true,
                    handler: function () {
                        this.fireEvent('preview');
                    },
                    scope: this
                }
            ]
        }];
    },

    initMyListeners: function() {
        this.on({
            clientvalidation: function (f, valid) {
                //this.getDockedComponent('tbar').getComponent('saveBtn').setDisabled(!valid);
            },
            scope: this
        });
    }
});
