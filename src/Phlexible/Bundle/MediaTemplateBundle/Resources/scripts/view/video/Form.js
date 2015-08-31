Ext.define('Phlexible.mediatemplate.view.video.Form', {
    extend: 'Ext.form.FormPanel',
    requires: [
        'Phlexible.mediatemplate.view.audio.Fields'
    ],
    xtype: 'mediatemplate.video.form',

    iconCls: Phlexible.mediatemplate.TemplateIcons.video,
    autoScroll: true,
    labelAlign: 'top',
    disabled: true,
    layout: {
        type: 'accordion',
        fill: false,
        multi: true
    },

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
                        name: 'videoWidth',
                        bind: {
                            value: '{list.selection.videoWidth}'
                        },
                        fieldLabel: this.widthText,
                        helpText: this.widthHelpText
                    },
                    {
                        xtype: 'numberfield',
                        itemId: 'videoHeightField',
                        flex: 1,
                        name: 'videoHeight',
                        bind: {
                            value: '{list.selection.videoHeight}'
                        },
                        fieldLabel: this.heightText,
                        helpText: this.heightHelpText
                    },
                    {
                        xtype: 'checkbox',
                        itemId: 'videoForWebField',
                        flex: 1,
                        name: 'forWeb',
                        bind: {
                            value: '{list.selection.forWeb}'
                        },
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
                        bind: {
                            value: '{list.selection.format}'
                        },
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
                        name: 'matchFormat',
                        bind: {
                            value: '{list.selection.matchFormat}'
                        },
                        hideLabel: true,
                        boxLabel: this.matchFormatText,
                        helpText: this.matchFormatHelpText
                    },
                    {
                        xtype: 'combo',
                        itemId: 'videoBitrateField',
                        flex: 1,
                        name: 'videoBitrate',
                        bind: {
                            value: '{list.selection.videoBitrate}'
                        },
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
                        name: 'videoFramerate',
                        bind: {
                            value: '{list.selection.videoFramerate}'
                        },
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
                        bind: {
                            value: '{list.selection.deinterlace}'
                        },
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
    }
});
