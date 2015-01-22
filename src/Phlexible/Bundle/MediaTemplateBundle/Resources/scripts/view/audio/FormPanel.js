Ext.define('Phlexible.mediatemplate.view.audio.FormPanel', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.mediatemplates-audio-form',

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

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatemplates-audio-fields'
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
                    iconCls: Phlexible.Icons.get(Phlexible.Icon.SAVE),
                    itemId: 'saveBtn',
                    handler: this.saveParameters,
                    scope: this
                },
                '->',
                {
                    xtype: 'splitbutton',
                    text: this.previewText,
                    iconCls: Phlexible.Icons.get(Phlexible.Icon.PREVIEW),
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

    loadParameters: function (template_key) {
        this.disable();
        this.template_key = template_key;

        this.getForm().reset();
        this.getForm().load({
            url: Phlexible.Router.generate('mediatemplates_form_load'),
            params: {
                template_key: template_key
            },
            success: function (form, data) {
                this.enable();

                this.fireEvent('paramsload');
            },
            scope: this
        });

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
