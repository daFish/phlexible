Ext.define('Phlexible.mediatemplate.view.image.FormPanel', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.mediatemplates-image-form',

    title: '_FormPanel',
//    labelWidth: 80,
    labelAlign: 'top',
    disabled: true,
    layout: 'accordion',

    previewFile: null,
    debugPreview: false,

    imageText: '_imageText',
    selectMethodText: '_selectMethodText',
    methodText: '_methodText',
    methodHelpText: '_methodHelpText',
    widthText: '_widthText',
    widthHelpText: '_widthHelpText',
    heighText: '_heightText',
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
    debugText: '_debugText',
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
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'method', 'help'],
                            data: [
                                ['width', this.methodWidthText, this.methodWidthHelpText],
                                ['height', this.methodHeightText, this.methodHeightHelpText],
                                ['exact', this.methodExactText, this.methodExactHelpText],
                                ['exactFit', this.methodExactFitText, this.methodExactFitHelpText],
                                ['fit', this.methodFitText, this.methodFitHelpText],
                                ['crop', this.methodCropText, this.methodCropHelpText]
                            ]
                        }),
                        tpl: '<tpl for=".">' +
                                 '<div class="x-boundlist-item">{method}<div style="line-height: 14px; font-size: 11px; color: gray;">{help}</div></div>' +
                             '</tpl>',
                        displayField: 'method',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        emptyText: this.selectMethodText,
                        editable: false,
                        allowBlank: false,
                        fieldLabel: this.methodText,
                        name: 'xmethod',
                        width: 280,
                        listWidth: 650,
                        helpText: this.methodHelpText,
                        listeners: {
                            select: this.updateMethod,
                            scope: this
                        }
                    },
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'width',
                        fieldLabel: this.widthText,
                        helpText: this.widthHelpText
                    },
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'height',
                        fieldLabel: this.heightText,
                        helpText: this.heightHelpText
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'scale'],
                            data: [
                                ['updown', this.scaleUpDownText],
                                ['down', this.scaleDownText],
                                ['up', this.scaleUpText]
                            ]
                        }),
                        displayField: 'scale',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        emptyText: this.selectScaleText,
                        editable: false,
                        allowBlank: false,
                        fieldLabel: this.scaleText,
                        name: 'scale',
                        width: 280,
                        //listWidth: 550,
                        helpText: this.scaleHelpText
                    },
                    {
                        xtype: 'checkbox',
                        width: 280,
                        name: 'for_web',
                        hideLabel: true,
                        boxLabel: this.forWebText,
                        helpText: this.forWebHelpText,
                        listeners: {
                            check: function (c, checked) {
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
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'format'],
                            data: [
                                //['', Phlexible.mediatemplate.Strings.keep_format],
                                ['gif', 'GIF'],
                                ['jpg', 'JPG'],
                                ['png', 'PNG'],
                                ['tif', 'TIF'],
                                ['bmp', 'BMP']
                            ]
                        }),
                        displayField: 'format',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.formatText,
                        name: 'format',
                        width: 280,
                        listWidth: 280,
                        allowBlank: false,
                        emptyText: this.selectFormatText,
                        helpText: this.formatHelpText,
                        listeners: {
                            select: function (c, r) {
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
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'colorspace'],
                            data: [
                                ['', this.keepColorspaceText],
                                ['rgb', this.colorspaceRgbText],
                                ['cmyk', this.colorspaceCmykText],
                                ['gray', this.colorspaceGrayText]
                            ]
                        }),
                        displayField: 'colorspace',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.colorspaceText,
                        name: 'colorspace',
                        width: 280,
                        listWidth: 280,
                        helpText: this.colorspaceHelpText
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'depth'],
                            data: [
                                ['', this.keepDepthText],
                                ['8', this.depth8BitPerChannelText],
                                ['16', this.depth16BitPerChannelText]
                            ]
                        }),
                        displayField: 'depth',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.depthText,
                        name: 'depth',
                        width: 280,
                        listWidth: 280,
                        helpText: this.depthHelpText
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'tiffcompression'],
                            data: [
                                ['none', this.noCompressionText],
                                ['zip', 'ZIP'],
                                ['lzw', 'LZW']
                            ]
                        }),
                        displayField: 'tiffcompression',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.tiffCompressionText,
                        name: 'tiffcompression',
                        width: 280,
                        listWidth: 280,
                        helpText: this.tiffCompressionHelpText
                    },
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'quality',
                        fieldLabel: this.qualityText,
                        helpText: this.qualityHelpText,
                        allowBlank: false,
                        minValue: 1,
                        maxValue: 100
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'compression'],
                            data: [
                                ['0', '0 - Huffman'],
                                ['1', '1 - Fastest'],
                                ['2', '2'],
                                ['3', '3'],
                                ['4', '4'],
                                ['5', '5'],
                                ['6', '6'],
                                ['7', '7'],
                                ['8', '8'],
                                ['9', '9 - Best']
                            ]
                        }),
                        displayField: 'compression',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.compressionText,
                        name: 'compression',
                        width: 280,
                        listWidth: 280,
                        allowBlank: false,
                        emptyText: this.selectCompressionText,
                        helpText: this.compressionHelpText
                    },
                    {
                        xtype: 'combo',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'filtertype'],
                            data: [
                                ['0', this.noCompressionText],
                                ['1', 'Sub'],
                                ['2', 'Up'],
                                ['3', 'Average'],
                                ['4', 'Paeth'],
                                ['5', 'Adaptive filtering']
                            ]
                        }),
                        displayField: 'filtertype',
                        valueField: 'id',
                        typeAhead: false,
                        mode: 'local',
                        triggerAction: 'all',
                        value: '',
                        editable: false,
                        fieldLabel: this.filterTypeText,
                        name: 'filtertype',
                        width: 280,
                        listWidth: 280,
                        allowBlank: false,
                        emptyText: this.selectFilterTypeText,
                        helpText: this.filterTypeHelpText
                    },
                    {
                        type: 'colorfield', // TODO: colorfield
                        xtype: 'textfield',
                        width: 280,
                        name: 'backgroundcolor',
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
        previewBtns.push('-');
        previewBtns.push({
            text: this.debugText,
            checked: this.debugPreview,
            checkHandler: function (checkItem, checked) {
                this.debugPreview = checked;
            },
            scope: this
        });

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
                    itemId: 'previewBtn',
                    text: this.previewText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.PREVIEW),
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

    getSafeValues: function () {
        var values = this.getForm().getValues();

        if (values.method) {
            values.xmethod = values.method;
            delete values.xmethod;
        }
        if (values.for_web === 'true') {
            values.for_web = 1;
        } else {
            delete values.for_web;
        }
        if (values.format === 'png') {
            values.quality = values.compression * 10 + values.filtertype * 1;
        }
        delete values.compression;
        delete values.filtertype;

        return values;
    },

    doPreview: function () {
        values = this.getSafeValues();

        values.template = this.template_key;
        values.preview_image = this.previewFile;
        values.debug = this.debugPreview;

        this.fireEvent('preview', values, this.debugPreview);
    },

    updateMethod: function () {
        var method = this.getComponent(0).getComponent(0).getValue();

        if (method === 'width') {
            this.getComponent(0).getComponent(1).enable();
            this.getComponent(0).getComponent(2).disable();
        }
        else if (method === 'height') {
            this.getComponent(0).getComponent(1).disable();
            this.getComponent(0).getComponent(2).enable();
        }
        else {
            this.getComponent(0).getComponent(1).enable();
            this.getComponent(0).getComponent(2).enable();
        }
    },

    updateFormat: function () {
        var optimize = this.getComponent(0).getComponent(4);
        var format = this.getComponent(0).getComponent(5);

        if (optimize.getValue()) {
            format.store.loadData([
                ['gif', 'GIF'],
                ['jpg', 'JPG'],
                ['png', 'PNG']
            ]);
        } else {
            format.store.loadData([
                ['', this.keepFormatText],
                ['gif', 'GIF'],
                ['jpg', 'JPG'],
                ['png', 'PNG'],
                ['tif', 'TIF'],
                ['bmp', 'BMP']
            ]);
        }
    },

    updateColorspace: function () {
        var optimize = this.getComponent(0).getComponent(4);
        var formatValue = this.getComponent(0).getComponent(5).getValue;
        var colorspace = this.getComponent(0).getComponent(6);

        if (optimize.getValue()) {
            if (formatValue == 'jpg' || formatValue == 'tif') {
                colorspace.setValue('rgb');
            }
            colorspace.store.loadData([
                ['rgb', this.colorspaceRgbText],
                ['gray', this.colorspaceGrayText]
            ]);
        } else {
            colorspace.store.loadData([
                ['', this.keepColorspaceText],
                ['rgb', this.colorspaceRgbText],
                ['cmyk', this.colorspaceCmykText],
                ['gray', this.colorspaceGrayText]
            ]);
        }
    },

    updateColorDepth: function () {
        var format = this.getComponent(0).getComponent(5);
        var colorDepth = this.getComponent(0).getComponent(7);

        var formatValue = format.getValue();
        if (formatValue != 'tif') {
            colorDepth.setValue('');
            colorDepth.disable();
            return;
        }

        colorDepth.enable();
    },

    updateTiffCompression: function () {
        var format = this.getComponent(0).getComponent(5);
        var tiffcompression = this.getComponent(0).getComponent(8);

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

    updateQuality: function () {
        var format = this.getComponent(0).getComponent(5);
        var tiffcompression = this.getComponent(0).getComponent(8);
        var quality = this.getComponent(0).getComponent(9);
        var compression = this.getComponent(0).getComponent(10);
        var filtertype = this.getComponent(0).getComponent(11);

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

                this.updateMethod();
                this.updateFormat();
                this.updateColorspace();
                this.updateColorDepth();
                this.updateQuality();
                this.updateTiffCompression();

                if (data.result.data.quality) {
                    var quality = parseInt(data.result.data.quality, 10);
                    var compression = Math.floor(quality / 10);
                    var filtertype = quality % 10;

                    this.getComponent(0).getComponent(9).setValue(quality);
                    this.getComponent(0).getComponent(10).setValue(compression);
                    this.getComponent(0).getComponent(11).setValue(filtertype);
                }

                this.fireEvent('paramsload');
            },
            scope: this
        });

    },

    saveParameters: function () {
        if (!this.getForm().isValid()) {
            Ext.MessageBox.alert('Error', this.requiredValuesText);
            return;
        }

        Ext.MessageBox.confirm(this.saveText, this.saveDescriptionText, function (btn) {
            if (btn !== 'yes') {
                return;
            }

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
        }, this);
    }
});
