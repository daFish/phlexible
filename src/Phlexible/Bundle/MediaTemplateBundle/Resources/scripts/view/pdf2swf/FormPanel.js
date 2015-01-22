Ext.define('Phlexible.mediatemplate.view.pdf2swf.FormPanel', {
    extend: 'Ext.form.FormPanel',
    alias: 'widget.mediatemplates-pdf2swf-form',

    title: '_FormPanel',
//    labelWidth: 80,
    labelAlign: 'top',
    disabled: true,
    layout: 'accordion',

    debugPreview: false,

    pdf2swfText: '_pdf2swf',
    resolutionText: '_resolutionText',
    resolutionHelpText: '_resolutionHelpText',
    framerateText: '_framerateText',
    framerateHelpText: '_framerateHelpText',
    qualityText: '_qualityText',
    qualityHelpText: '_qualityHelpText',
    linksText: '_linksText',
    enabledText: '_enabledText',
    disabledText: '_disabledText',
    linksDisabledHelpText: '_linksDisabledHelpText',
    newWindowText: '_newWindowText',
    linksnewWindowHelpText: '_linksnewWindowHelpText',
    zlibText: '_zlibText',
    zlibHelpText: '_zlibHelpText',
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
                xtype: 'panel',
                layout: 'form',
                title: this.pdf2swfText,
                iconCls: Phlexible.mediatemplate.TemplateIcons.pdf2swf,
                bodyPadding: 5,
                border: false,
                autoScroll: true,
                items: [
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'resolution',
                        fieldLabel: this.resolutionText,
                        helpText: this.resolutionHelpText,
                        minValue: 1
                    },
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'framerate',
                        fieldLabel: this.framerateText,
                        helpText: this.framerateHelpText,
                        minValue: 1
                    },
                    {
                        xtype: 'numberfield',
                        width: 280,
                        name: 'jpeg_quality',
                        fieldLabel: this.qualityText,
                        helpText: this.qualityHelpText,
                        minValue: 0,
                        maxValue: 100
                    },
                    {
                        xtype: 'checkbox',
                        width: 280,
                        name: 'links_disable',
                        fieldLabel: this.linksText,
                        boxLabel: this.disabledText,
                        helpText: this.linksDisabledHelpText
                    },
                    {
                        xtype: 'checkbox',
                        width: 280,
                        name: 'links_new_window',
                        fieldLabel: this.linksText,
                        boxLabel: this.newWindowText,
                        helpText: this.linksnewWindowHelpText
                    },
                    {
                        xtype: 'checkbox',
                        width: 280,
                        name: 'zlib_enable',
                        fieldLabel: this.zlibText,
                        boxLabel: this.enabledText,
                        helpText: this.zlibHelpText
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

                        if (values.method) {
                            values.xmethod = values.method;
                            delete values.xmethod;
                        }
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
