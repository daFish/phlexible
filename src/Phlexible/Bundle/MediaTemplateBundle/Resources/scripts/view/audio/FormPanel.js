Ext.define('Phlexible.mediatemplates.audio.FormPanel', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.mediatemplates-audio-form',

    title: Phlexible.mediatemplates.Strings.audio_template,
    strings: Phlexible.mediatemplates.Strings,
//    labelWidth: 80,
    autoScroll: true,
    labelAlign: 'top',
    disabled: true,
    layout: 'accordion',

    debugPreview: false,

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'panel',
                layout: 'form',
                title: this.strings.audio,
                iconCls: Phlexible.mediatemplates.TemplateIcons.audio,
                bodyStyle: 'padding: 5px',
                border: false,
                autoScroll: true,
                items: [
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'bitrate'],
                            data: Phlexible.mediatemplates.AudioBitrates
                        }),
                        valueField: 'id',
                        displayField: 'bitrate',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.strings.bitrate,
                        name: 'audio_bitrate',
                        width: 280,
                        listWidth: 280,
                        helpText: this.strings.help_bitrate
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'samplerate'],
                            data: Phlexible.mediatemplates.AudioSamplerates
                        }),
                        valueField: 'id',
                        displayField: 'samplerate',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.strings.samplerate,
                        name: 'audio_samplerate',
                        width: 280,
                        listWidth: 280,
                        helpText: this.strings.help_samplerate
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'samplebits'],
                            data: Phlexible.mediatemplates.AudioSamplebits
                        }),
                        valueField: 'id',
                        displayField: 'samplebits',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.strings.samplebits,
                        name: 'audio_samplebits',
                        width: 280,
                        listWidth: 280,
                        helpText: this.strings.help_samplebits
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'channels'],
                            data: Phlexible.mediatemplates.AudioChannels
                        }),
                        valueField: 'id',
                        displayField: 'channels',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.strings.channels,
                        name: 'audio_channels',
                        width: 280,
                        listWidth: 280,
                        helpText: this.strings.help_channels
                    }
                ]
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
                    text: this.strings.save,
                    iconCls: Phlexible.Icons.get(Phlexible.Icon.SAVE),
                    itemId: 'saveBtn',
                    handler: this.saveParameters,
                    scope: this
                },
                '->',
                {
                    xtype: 'splitbutton',
                    text: this.strings.preview,
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
                            text: this.strings.debug,
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
