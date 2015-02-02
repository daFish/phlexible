Ext.define('Phlexible.mediatemplate.view.audio.Form', {
    extend: 'Ext.form.FormPanel',
    requires: ['Phlexible.mediatemplate.view.audio.Fields'],

    xtype: 'mediatemplate.audio.form',

    title: '_FormPanel',
//    labelWidth: 80,
    autoScroll: true,
    labelAlign: 'top',
    disabled: true,
    layout: 'accordion',

    debugPreview: false,

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
    debugText: '_debugText',

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
                    text: this.saveText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                    itemId: 'saveBtn',
                    handler: this.saveParameters,
                    scope: this
                },
                '->',
                {
                    xtype: 'splitbutton',
                    text: this.previewText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.PREVIEW),
                    handler: function () {
                        var values = this.getForm().getValues();

                        values.template = this.template_key;
                        values.debug = this.debugPreview;

                        this.fireEvent('preview', values, this.debugPreview);
                    },
                    scope: this,
                    menu: [
                        {
                            text: this.debugText,
                            checked: this.debugPreview,
                            checkHandler: function (checkItem, checked) {
                                this.debugPreview = checked;
                            },
                            scope: this
                        }
                    ]
                }
            ]
        }]
    },

    initMyListeners: function() {
        this.on({
            clientvalidation: function (f, valid) {
                this.getDockedComponent('tbar').getComponent('saveBtn').setDisabled(!valid);
            },
            scope: this
        });
    },

    loadParameters: function (key, parameters) {
        this.template_key = key;

        this.getForm().reset();
        this.getForm().setValues(parameters);

        this.enable();
    },

    saveParameters: function () {
        this.getForm().submit({
            url: Phlexible.Router.generate('mediatemplates_form_save'),
            params: {
                template_key: this.template_key
            },
            success: function (form, action) {
                var data = Ext.decode(action.response.responseText);
                if (data.success) {
                    Phlexible.success('Success', data.msg);
                    this.fireEvent('paramssave');
                }
                else {
                    Ext.Msg.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    }

});
