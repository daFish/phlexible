Ext.define('Phlexible.mediatemplates.view.video.FormPanel', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.mediatemplates-video-form',

    title: Phlexible.mediatemplates.Strings.video_template,
    strings: Phlexible.mediatemplates.Strings,
//    labelWidth: 80,
    autoScroll: true,
    labelAlign: 'top',
    disabled: true,
    layout: 'accordion',

    debugPreview: false,

    initComponent: function () {
        this.initMyItems();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'panel',
                layout: 'form',
                title: this.strings.video,
                iconCls: Phlexible.mediatemplates.TemplateIcons.video,
                bodyStyle: 'padding: 5px',
                border: false,
                autoScroll: true,
                items: [
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'video_width',
                        fieldLabel: this.strings.width,
                        helpText: this.strings.help_width_video
                    },
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'video_height',
                        fieldLabel: this.strings.height,
                        helpText: this.strings.help_height_video
                    },
                    {
                        xtype: 'checkbox',
                        width: 280,
                        name: 'for_web',
                        hideLabel: true,
                        boxLabel: this.strings.for_web,
                        helpText: this.strings.help_for_web_video,
                        listeners: {
                            check: function (c, checked) {
                                this.updateForWeb();
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'format'],
                            data: Phlexible.mediatemplates.VideoFormats
                        }),
                        displayField: 'format',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.strings.format,
                        name: 'format',
                        width: 280,
                        listWidth: 280,
                        helpText: this.strings.help_format,
                        listeners: {
                            select: function (c) {
                                this.updateFormat();
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'checkbox',
                        width: 280,
                        name: 'match_format',
                        hideLabel: true,
                        boxLabel: this.strings.match_format,
                        helpText: this.strings.help_match_format
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'bitrate'],
                            data: Phlexible.mediatemplates.VideoBitrates
                        }),
                        valueField: 'id',
                        displayField: 'bitrate',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.strings.bitrate,
                        name: 'video_bitrate',
                        width: 280,
                        listWidth: 280,
                        helpText: this.strings.help_bitrate
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'framerate'],
                            data: Phlexible.mediatemplates.VideoFramerates
                        }),
                        valueField: 'id',
                        displayField: 'framerate',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.strings.framerate,
                        name: 'video_framerate',
                        width: 280,
                        listWidth: 280,
                        helpText: this.strings.help_framerate
                    },
                    {
                        xtype: 'checkbox',
                        width: 280,
                        name: 'deinterlace',
                        hideLabel: true,
                        boxLabel: this.strings.deinterlace,
                        helpText: this.strings.help_deinterlace
                    }
                ]
            },
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
                    itemId: 'saveBtn',
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                    handler: this.saveParameters,
                    scope: this
                },
                '->',
                {
                    xtype: 'splitbutton',
                    text: this.strings.preview,
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
        }];
    },

    initMyListeners: function() {
        this.on({
            clientvalidation: function (f, valid) {
                this.getDockedComponent('tbar').getComponent('saveBtn').setDisabled(!valid);
            },
            scope: this
        });
    },

    updateForWeb: function () {
        var optimize = this.getComponent(0).getComponent(2);
        var format = this.getComponent(0).getComponent(3);

        if (optimize.getValue()) {
            format.store.loadData(Phlexible.mediatemplates.VideoFormatsWeb);
            format.setValue('flv');
        } else {
            format.store.loadData(Phlexible.mediatemplates.VideoFormats);
        }

        this.updateFormat();
    },

    updateFormat: function () {
        var format = this.getComponent(0).getComponent(3);
        var match = this.getComponent(0).getComponent(4);

        if (format.getValue()) {
            match.enable();
        } else {
            match.disable();
        }
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

        this.updateForWeb();
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
                    Phlexible.success(data.msg);
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
