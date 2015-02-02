Ext.define('Phlexible.mediatemplate.view.video.Form', {
    extend: 'Ext.form.FormPanel',
    requires: ['Phlexible.mediatemplate.view.audio.Fields'],

    xtype: 'mediatemplate.video.form',

//    labelWidth: 80,
    autoScroll: true,
    labelAlign: 'top',
    disabled: true,
    layout: {
        type: 'accordion',
        fill: false,
        multi: true,
    },

    debugPreview: false,

    videoText: '_videoText',
    widthText: '_widthText',
    widthHelpText: '_widthHelpText',
    heightText: '_heightText',
    heightHelpText: '_heightHelpText',
    forWebText: '_forWebText',
    forWebHelpText: '_forWebHelpText',
    formatText: '_formatText',
    formatHelpText: '_formatHelpText',
    matchFormatText: '_matchFormatText',
    matchFormatHelpText: '_matchFormatHelpText',
    keepBitrateText: '_keepBitrateText',
    bitrateText: '_bitrateText',
    bitrateHelpText: '_bitrateHelpText',
    keepFramerateText: '_keepFramerateText',
    framerateText: '_framerateText',
    framerateHelpText: '_framerateHelpText',
    deinterlaceText: '_deinterlaceText',
    deinterlaceHelpText: '_deinterlaceHelpText',
    saveText: '_save',
    previewText: '_preview',
    debugText: '_debug',

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
                title: this.videoText,
                iconCls: Phlexible.mediatemplate.TemplateIcons.video,
                bodyStyle: 'padding: 5px',
                border: false,
                autoScroll: true,
                items: [
                    {
                        xtype: 'numberfield',
                        itemId: 'videoWidthField',
                        flex: 1,
                        name: 'video_width',
                        fieldLabel: this.widthText,
                        helpText: this.widthHelpText
                    },
                    {
                        xtype: 'numberfield',
                        itemId: 'videoHeightField',
                        flex: 1,
                        name: 'video_height',
                        fieldLabel: this.heightText,
                        helpText: this.heightHelpText
                    },
                    {
                        xtype: 'checkbox',
                        itemId: 'videoForWebField',
                        flex: 1,
                        name: 'for_web',
                        hideLabel: true,
                        boxLabel: this.forWebText,
                        helpText: this.forWebHelpText,
                        listeners: {
                            check: function (c, checked) {
                                this.updateForWeb();
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'combo',
                        itemId: 'videoFormatField',
                        flex: 1,
                        name: 'format',
                        value: '',
                        fieldLabel: this.formatText,
                        helpText: this.formatHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'format'],
                            data: [
                                ['', this.keepFormatText],
                                ['flv', 'FLV'],
                                ['mp4', 'MP4'],
                                ['ogg', 'OGG'],
                                ['wmv', 'WMV'],
                                ['wmv3', 'WMV3'],
                                ['webm', 'WEBM'],
                                ['3gp', '3GP']
                            ]
                        }),
                        displayField: 'format',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false,
                        listeners: {
                            select: function (c) {
                                this.updateFormat();
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'checkbox',
                        itemId: 'videoMatchFormatField',
                        flex: 1,
                        name: 'match_format',
                        hideLabel: true,
                        boxLabel: this.matchFormatText,
                        helpText: this.matchFormatHelpText
                    },
                    {
                        xtype: 'combo',
                        itemId: 'videoBitrateField',
                        flex: 1,
                        name: 'video_bitrate',
                        value: '',
                        fieldLabel: this.bitrateText,
                        helpText: this.bitrateHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'bitrate'],
                            data: [
                                ['', this.keepBitrateText],
                                ['300k', '300k'],
                                ['500k', '500k'],
                                ['800k', '800k'],
                                ['1000k', '1000k'],
                                ['2000k', '2000k']
                            ]
                        }),
                        valueField: 'id',
                        displayField: 'bitrate',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false
                    },
                    {
                        xtype: 'combo',
                        itemId: 'videoFramerateField',
                        flex: 1,
                        value: '',
                        name: 'video_framerate',
                        fieldLabel: this.framerateText,
                        helpText: this.framerateHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'framerate'],
                            data: [
                                ['', this.keepFramerateText],
                                ['5', '5'],
                                ['10', '10'],
                                ['15', '15'],
                                ['20', '20'],
                                ['25', '25']
                            ]
                        }),
                        valueField: 'id',
                        displayField: 'framerate',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false
                    },
                    {
                        xtype: 'checkbox',
                        itemId: 'videoDeinterlaceField',
                        flex: 1,
                        name: 'deinterlace',
                        hideLabel: true,
                        boxLabel: this.deinterlaceText,
                        helpText: this.deinterlaceHelpText
                    }
                ]
            },
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
                    itemId: 'saveBtn',
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
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
            format.store.loadData([
                ['flv', 'FLV'],
                ['mp4', 'MP4'],
                ['ogg', 'OGG']
            ]);
            format.setValue('flv');
        } else {
            format.store.loadData([
                ['', this.keepFormatText],
                ['flv', 'FLV'],
                ['mp4', 'MP4'],
                ['ogg', 'OGG'],
                ['wmv', 'WMV'],
                ['wmv3', 'WMV3'],
                ['webm', 'WEBM'],
                ['3gp', '3GP']
            ]);
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

    loadParameters: function (key, parameters) {
        this.template_key = key;

        this.getForm().reset();
        this.getForm().setValues(parameters);

        this.updateForWeb();

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
