Ext.define('Phlexible.mediatemplate.view.image.Form', {
    extend: 'Ext.form.FormPanel',
    requires: [
        'Ext.ux.colorpick.Field'
    ],
    xtype: 'mediatemplate.image.form',

    iconCls: Phlexible.mediatemplate.TemplateIcons.image,
    labelAlign: 'top',
    disabled: true,
    layout: 'accordion',

    previewFile: null,

    imageText: '_imageText',
    selectMethodText: '_selectMethodText',
    methodText: '_methodText',
    methodHelpText: '_methodHelpText',
    widthText: '_widthText',
    widthHelpText: '_widthHelpText',
    heightText: '_heightText',
    heightHelpText: '_heightHelpText',
    selectScaleText: '_selectScaleText',
    scaleHelpText: '_scaleHelpText',
    scaleText: '_scaleText',
    forWebHelpText: '_forWebHelpText',
    forWebText: '_forWebText',
    selectFormatText: '_selectFormatText',
    formatText: '_formatText',
    formatHelpText: '_formatHelpText',
    colorspaceText: '_colorspaceText',
    colorspaceHelpText: '_colorspaceHelpText',
    keepDepthText: '_keepDepthText',
    depthHelpText: '_depthHelpText',
    depthText: '_depthText',
    noCompressionText: '_noCompressionText',
    tiffCompressionText: '_tiffCompressionText',
    tiffCompressionHelpText: '_tiffCompressionHelpText',
    qualityText: '_qualityText',
    qualityHelpText: '_qualityHelpText',
    selectCompressionText: '_selectCompressionText',
    compressionText: '_compressionText',
    compressionHelpText: '_compressionHelpText',
    selectFilterTypeText: '_selectFilterTypeText',
    filterTypeText: '_filterTypeText',
    filterTypeHelpText: '_filterTypeHelpText',
    emptyBackgroundColorText: '_emptyBackgroundColorText',
    backgroundColorText: '_backgroundColorText',
    backgroundColorHelpText: '_backgroundColorHelpText',
    methodWidthText: '_methodWidthText',
    methodWidthHelpText: '_methodWidthHelpText',
    methodHeightText: '_methodHeightText',
    methodHeightHelpText: '_methodHeightHelpText',
    methodExactText: '_methodExactText',
    methodExactHelpText: '_methodExactHelpText',
    methodExactFitText: '_methodExactFitText',
    methodExactFitHelpText: '_methodExactFitHelpText',
    methodFitText: '_methodFitText',
    methodFitHelpText: '_methodFitHelpText',
    methodCropText: '_methodCropText',
    methodCropHelpText: '_methodCropHelpText',
    scaleUpDownText: '_scaleUpDownText',
    scaleUpText: '_scaleUpText',
    scaleDownText: '_scaleDownText',
    depth8BitPerChannelText: '_depth8BitPerChannelText',
    depth16BitPerChannelText: '_depth16BitPerChannelText',
    saveText: '_saveText',
    previewText: '_previewText',
    keepFormatText: '_keepFormatText',
    colorspaceRgbText: '_colorspaceRgbText',
    colorspaceCmykText: '_colorspaceCmykText',
    colorspaceGrayText: '_colorspaceGrayText',
    keepColorspaceText: '_keepColorspaceText',
    requiredValuesText: '_requiredValuesText',
    saveDescriptionText: '_saveDescriptionText',

    initComponent: function () {
        this.previewSizes = [
            [1000, 600],
            [600, 1000],
            [1000, 1000],
            [500, 350],
            [160, 120],
            [120, 160],
            [120, 120],
            [800, 600, 'with alpha channel']
        ];

        this.initMyItems();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent();
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'panel',
                layout: 'form',
                title: this.imageText,
                iconCls: Phlexible.mediatemplate.TemplateIcons.image,
                bodyPadding: 5,
                border: false,
                autoScroll: true,
                items: [
                    {
                        xtype: 'combo',
                        itemId: 'methodField',
                        name: 'method',
                        bind: {
                            value: '{list.selection.method}'
                        },
                        flex: 1,
                        fieldLabel: this.methodText,
                        emptyText: this.selectMethodText,
                        helpText: this.methodHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'name', 'help'],
                            data: [
                                {id: 'width', name: this.methodWidthText, help: this.methodWidthHelpText},
                                {id: 'height', name: this.methodHeightText, help: this.methodHeightHelpText},
                                {id: 'exact', name: this.methodExactText, help: this.methodExactHelpText},
                                {id: 'exactFit', name: this.methodExactFitText, help: this.methodExactFitHelpText},
                                {id: 'fit', name: this.methodFitText, help: this.methodFitHelpText},
                                {id: 'crop', name: this.methodCropText, help: this.methodCropHelpTex}
                            ]
                        }),
                        tpl: '<tpl for=".">' +
                                 '<div class="x-boundlist-item">{name}<div style="line-height: 14px; font-size: 11px; color: gray;">{help}</div></div>' +
                             '</tpl>',
                        displayField: 'name',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false,
                        allowBlank: false,
                        listeners: {
                            change: this.updateMethod,
                            //select: this.updateMethod,
                            scope: this
                        }
                    },
                    {
                        xtype: 'numberfield',
                        itemId: 'widthField',
                        name: 'width',
                        bind: {
                            value: '{list.selection.width}'
                        },
                        flex: 1,
                        fieldLabel: this.widthText,
                        helpText: this.widthHelpText
                    },
                    {
                        xtype: 'numberfield',
                        itemId: 'heightField',
                        name: 'height',
                        bind: {
                            value: '{list.selection.height}'
                        },
                        flex: 1,
                        fieldLabel: this.heightText,
                        helpText: this.heightHelpText
                    },
                    {
                        xtype: 'combo',
                        itemId: 'scaleField',
                        name: 'scale',
                        bind: {
                            value: '{list.selection.scale}'
                        },
                        flex: 1,
                        fieldLabel: this.scaleText,
                        helpText: this.scaleHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'name'],
                            data: [
                                {id: 'updown', name: this.scaleUpDownText},
                                {id: 'down', name: this.scaleDownText},
                                {id: 'up', name: this.scaleUpTex}
                            ]
                        }),
                        displayField: 'name',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        emptyText: this.selectScaleText,
                        editable: false,
                        allowBlank: false
                    },
                    {
                        xtype: 'checkbox',
                        itemId: 'forWebField',
                        name: 'forWeb',
                        bind: {
                            value: '{list.selection.forWeb}'
                        },
                        flex: 1,
                        hideLabel: true,
                        boxLabel: this.forWebText,
                        helpText: this.forWebHelpText,
                        listeners: {
                            change: function (c, checked) {
                                this.updateFormat();
                                this.updateQuality();
                                this.updateColorspace();
                                this.updateColorDepth();
                                this.updateTiffCompression();
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'combo',
                        itemId: 'formatField',
                        name: 'format',
                        bind: {
                            value: '{list.selection.format}'
                        },
                        flex: 1,
                        value: '',
                        fieldLabel: this.formatText,
                        emptyText: this.selectFormatText,
                        helpText: this.formatHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'name'],
                            data: [
                                //{id: '', name: this.keepFormatText},
                                {id: 'gif', name: 'GIF'},
                                {id: 'jpg', name: 'JPG'},
                                {id: 'png', name: 'PNG'},
                                {id: 'tif', name: 'TIF'},
                                {id: 'bmp', name: 'BMP'}
                            ]
                        }),
                        displayField: 'name',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false,
                        allowBlank: false,
                        listeners: {
                            change: function (c, r) {
                                this.updateColorspace();
                                this.updateQuality();
                                this.updateColorDepth();
                                this.updateTiffCompression();
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'combo',
                        itemId: 'colorspaceField',
                        name: 'colorspace',
                        bind: {
                            value: '{list.selection.colorspace}'
                        },
                        value: '',
                        flex: 1,
                        fieldLabel: this.colorspaceText,
                        helpText: this.colorspaceHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'name'],
                            data: [
                                {id: '', name: this.keepColorspaceText},
                                {id: 'rgb', name: this.colorspaceRgbText},
                                {id: 'cmyk', name: this.colorspaceCmykText},
                                {id: 'gray', name: this.colorspaceGrayText}
                            ]
                        }),
                        displayField: 'name',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false
                    },
                    {
                        xtype: 'combo',
                        itemId: 'colorDepthField',
                        name: 'depth',
                        bind: {
                            value: '{list.selection.depth}'
                        },
                        value: '',
                        flex: 1,
                        fieldLabel: this.depthText,
                        helpText: this.depthHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'name'],
                            data: [
                                {id: '', name: this.keepDepthText},
                                {id: '8', name: this.depth8BitPerChannelText},
                                {id: '16', name: this.depth16BitPerChannelText}
                            ]
                        }),
                        displayField: 'name',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false
                    },
                    {
                        xtype: 'combo',
                        itemId: 'tiffCompressionField',
                        name: 'tiffCompression',
                        bind: {
                            value: '{list.selection.tiffCompression}'
                        },
                        value: '',
                        fieldLabel: this.tiffCompressionText,
                        helpText: this.tiffCompressionHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'name'],
                            data: [
                                {id: 'none', name: this.noCompressionText},
                                {id: 'zip', name: 'ZIP'},
                                {id: 'lzw', name: 'LZW'}
                            ]
                        }),
                        displayField: 'name',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false
                    },
                    {
                        xtype: 'numberfield',
                        itemId: 'qualityField',
                        name: 'quality',
                        bind: {
                            value: '{list.selection.quality}'
                        },
                        flex: 1,
                        fieldLabel: this.qualityText,
                        helpText: this.qualityHelpText,
                        allowBlank: false,
                        minValue: 1,
                        maxValue: 100
                    },
                    {
                        xtype: 'combo',
                        itemId: 'compressionField',
                        name: 'compression',
                        bind: {
                            value: '{list.selection.compression}'
                        },
                        flex: 1,
                        fieldLabel: this.compressionText,
                        emptyText: this.selectCompressionText,
                        helpText: this.compressionHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'name'],
                            data: [
                                {id: 0, name: '0 - Huffman'},
                                {id: 1, name: '1 - Fastest'},
                                {id: 2, name: '2'},
                                {id: 3, name: '3'},
                                {id: 4, name: '4'},
                                {id: 5, name: '5'},
                                {id: 6, name: '6'},
                                {id: 7, name: '7 (default)'},
                                {id: 8, name: '8'},
                                {id: 9, name: '9 - Best'}
                            ]
                        }),
                        displayField: 'name',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        allowBlank: false,
                        editable: false,
                        listeners: {
                            change: this.updateCompression,
                            scope: this
                        }
                    },
                    {
                        xtype: 'combo',
                        itemId: 'filterTypeField',
                        name: 'filtertype',
                        bind: {
                            value: '{list.selection.filtertype}'
                        },
                        flex: 1,
                        emptyText: this.selectFilterTypeText,
                        fieldLabel: this.filterTypeText,
                        helpText: this.filterTypeHelpText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'name'],
                            data: [
                                {id: 0, name: '0 - ' + this.noCompressionText},
                                {id: 1, name: '1 - Sub'},
                                {id: 2, name: '2 - Up'},
                                {id: 3, name: '3 - Average'},
                                {id: 4, name: '4 - Paeth'},
                                {id: 5, name: '5 (default) - Adaptive filtering'},
                                {id: 6, name: '6 - Adaptive filtering with minimum-sum-of-absolute-values'},
                                {id: 7, name: '7 - LOCO'},
                                {id: 8, name: '8 - zlib Z_RLE with adaptive PNG filtering'},
                                {id: 9, name: '9 - zlib Z_RLE with no PNG filtering'}
                            ]
                        }),
                        displayField: 'name',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false,
                        allowBlank: false,
                        listeners: {
                            change: this.updateFiltertype,
                            scope: this
                        }
                    },
                    {
                        itemId: 'backgroundColorField',
                        xtype: 'textfield',
                        name: 'backgroundcolor',
                        bind: {
                            value: '{list.selection.backgroundcolor}'
                        },
                        flex: 1,
                        emptyText: this.emptyBackgroundColorText,
                        fieldLabel: this.backgroundColorText,
                        helpText: this.backgroundColorHelpText
                    }
                ]
            }
        ];
    },

    initMyDockedItems: function() {
        var previewBtns = [];
        Ext.each(this.previewSizes, function (item) {
            previewBtns.push({
                text: item[0] + 'x' + item[1] + (item[2] ? ' ' + item[2] : ''),
                handler: function () {
                    this.doPreviewSize(item[0], item[1]);
                },
                scope: this
            });
        }, this);

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
                    xtype: 'splitbutton',
                    itemId: 'previewBtn',
                    text: this.previewText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.PREVIEW),
                    formBind: true,
                    handler: function () {
                        this.doPreview();
                    },
                    scope: this,
                    menu: previewBtns
                }
            ]
        }];
    },

    initMyListeners: function() {
        this.on({
            clientvalidation: function (f, valid) {
                this.getDockedComponent('tbar').getComponent('saveBtn').setDisabled(!valid);
            },
            render: function () {
                this.setPreviewSize(this.previewSizes[0][0], this.previewSizes[0][1]);
            },
            scope: this
        });

    },

    setPreviewSize: function (width, height) {
        this.previewFile = width + '_' + height;
        this.getDockedComponent('tbar').getComponent('previewBtn').setText(this.previewText + ' ' + width + 'x' + height);
    },

    doPreviewSize: function (width, height) {
        this.setPreviewSize(width, height);
        this.doPreview();
    },

    doPreview: function () {
        this.fireEvent('preview', this.previewFile);
    },

    updateMethod: function () {
        var method = this.getComponent(0).getComponent('methodField').getValue();

        if (method === 'width') {
            this.getComponent(0).getComponent('widthField').enable();
            this.getComponent(0).getComponent('heightField').disable();
        }
        else if (method === 'height') {
            this.getComponent(0).getComponent('widthField').disable();
            this.getComponent(0).getComponent('heightField').enable();
        }
        else {
            this.getComponent(0).getComponent('widthField').enable();
            this.getComponent(0).getComponent('heightField').enable();
        }
    },

    updateFormat: function () {
        var optimize = this.getComponent(0).getComponent('forWebField');
        var format = this.getComponent(0).getComponent('formatField');

        if (optimize.getValue()) {
            format.store.loadData([
                {id: 'gif', name: 'GIF'},
                {id: 'jpg', name: 'JPG'},
                {id: 'png', name: 'PNG'}
            ]);
        } else {
            format.store.loadData([
                {id: '', name: this.keepFormatText},
                {id: 'gif', name: 'GIF'},
                {id: 'jpg', name: 'JPG'},
                {id: 'png', name: 'PNG'},
                {id: 'tif', name: 'TIF'},
                {id: 'bmp', name: 'BMP'}
            ]);
        }
    },

    updateColorspace: function () {
        var optimize = this.getComponent(0).getComponent('forWebField');
        var formatValue = this.getComponent(0).getComponent('formatField').getValue;
        var colorspace = this.getComponent(0).getComponent('colorspaceField');

        if (optimize.getValue()) {
            if (formatValue == 'jpg' || formatValue == 'tif') {
                colorspace.setValue('rgb');
            }
            colorspace.store.loadData([
                {id: 'rgb', name: this.colorspaceRgbText},
                {id: 'gray', name: this.colorspaceGrayText}
            ]);
        } else {
            colorspace.store.loadData([
                {id: '', name: this.keepColorspaceText},
                {id: 'rgb', name: this.colorspaceRgbText},
                {id: 'cmyk', name: this.colorspaceCmykText},
                {id: 'gray', name: this.colorspaceGrayText}
            ]);
        }
    },

    updateColorDepth: function () {
        var format = this.getComponent(0).getComponent('formatField');
        var colorDepth = this.getComponent(0).getComponent('colorDepthField');

        var formatValue = format.getValue();
        if (formatValue != 'tif') {
            colorDepth.setValue('');
            colorDepth.disable();
            return;
        }

        colorDepth.enable();
    },

    updateTiffCompression: function () {
        var format = this.getComponent(0).getComponent('formatField');
        var tiffcompression = this.getComponent(0).getComponent('tiffCompressionField');

        var formatValue = format.getValue();
        var tiffcompressionValue = tiffcompression.getValue();


        if (formatValue != 'tif') {
            tiffcompression.setValue('');
            tiffcompression.disable();
            return;
        }

        if (tiffcompressionValue === '') {
            tiffcompression.setValue('none');
        }
        tiffcompression.enable();
    },

    updateCompression: function() {
        var format = this.getComponent(0).getComponent('formatField'),
            quality = this.getComponent(0).getComponent('qualityField'),
            compression = this.getComponent(0).getComponent('compressionField'),
            q;

        if (format.getValue() !== 'png') {
            return;
        }

        q = quality.getValue();
        q = compression.getValue() * 10 + q % 10;

        quality.setValue(q);
    },

    updateFiltertype: function() {
        var format = this.getComponent(0).getComponent('formatField'),
            quality = this.getComponent(0).getComponent('qualityField'),
            filtertype = this.getComponent(0).getComponent('filterTypeField');

        if (format.getValue() !== 'png') {
            return;
        }

        q = quality.getValue();
        q = Math.floor(q / 10) * 10 + filtertype.getValue();

        quality.setValue(q);
    },

    updateQuality: function () {
        var format = this.getComponent(0).getComponent('formatField'),
            tiffcompression = this.getComponent(0).getComponent('tiffCompressionField'),
            quality = this.getComponent(0).getComponent('qualityField'),
            compression = this.getComponent(0).getComponent('compressionField'),
            filtertype = this.getComponent(0).getComponent('filterTypeField');

        var formatValue = format.getValue();
        if (formatValue == 'jpg') {
            quality.show();
            quality.enable();
            compression.hide();
            compression.disable();
            filtertype.hide();
            filtertype.disable();
            tiffcompression.hide();
            tiffcompression.disable();
        }
        else if (formatValue == 'tif') {
            quality.hide();
            quality.disable();
            compression.hide();
            compression.disable();
            filtertype.hide();
            filtertype.disable();
            tiffcompression.show();
            tiffcompression.enable();
        }
        else if (formatValue == 'png') {
            quality.hide();
            quality.disable();
            compression.show();
            compression.enable();
            filtertype.show();
            filtertype.enable();
            tiffcompression.hide();
            tiffcompression.disable();
        }
        else {
            quality.hide();
            quality.disable();
            compression.hide();
            compression.disable();
            filtertype.hide();
            filtertype.disable();
            tiffcompression.hide();
            tiffcompression.disable();
        }
    }
});
