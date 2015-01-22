Ext.define('Phlexible.mediatemplate.view.TemplatesGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.mediatemplates-list',

    title: '_TemplatesGrid',
    border: true,

    titleText: '_titleText',
    addText: '_addText',
    imageText: '_imageText',
    videoText: '_videoText',
    audioText: '_audioText',
    pdf2swfText: '_pdf2swfText',
    noFilterText: '_noFilterText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            fields: ['key', 'type'],
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('mediatemplates_templates_list'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'templates',
                    idProperty: 'key'
                },
                extraParams: this.storeExtraParams
            },
            sorters: [{
                property: 'key',
                direction: 'ASC'
            }],
            autoLoad: true
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.titleText,
                dataIndex: 'key',
                width: 35,
                flex: 1,
                sortable: true,
                renderer: function (v, md, r) {
                    return Phlexible.Icon.inline(Phlexible.mediatemplate.TemplateIcons[r.get('type')]) + ' ' + v;
                }
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            itemId: 'tbar',
            dock: 'top',
            items: [
                {
                    text: this.addText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                    menu: [
                        {
                            text: this.imageText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.image,
                            handler: this.newImageTemplate,
                            scope: this
                        },
                        {
                            text: this.videoText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.video,
                            handler: this.newVideoTemplate,
                            scope: this
                        },
                        {
                            text: this.audioText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.audio,
                            handler: this.newAudioTemplate,
                            scope: this
                        },
                        {
                            text: this.pdf2swfText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.pdf2swf,
                            handler: this.newPdfTemplate,
                            scope: this
                        }
                    ]
                },
                '->',
                {
                    text: this.noFilterText,
                    itemId: 'filter',
                    iconCls: Phlexible.Icon.get('funnel'),
                    menu: [
                        {
                            text: this.noFilterText,
                            iconCls: Phlexible.Icon.get('funnel'),
                            filter: '',
                            handler: this.toggleFilter,
                            scope: this
                        },
                        {
                            text: this.imageText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.image,
                            filter: 'image',
                            handler: this.toggleFilter,
                            scope: this
                        },
                        {
                            text: this.videoText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.video,
                            filter: 'video',
                            handler: this.toggleFilter,
                            scope: this
                        },
                        {
                            text: this.audioText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.audio,
                            filter: 'audio',
                            handler: this.toggleFilter,
                            scope: this
                        },
                        {
                            text: this.pdf2swfText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.pdf2swf,
                            filter: 'pdf2swf',
                            handler: this.toggleFilter,
                            scope: this
                        }
                    ]
                }
            ]
        }];
    },

    initMyListeners: function() {
        this.on({
            rowdblclick: function (grid, record) {
                this.fireEvent('templatechange', record);
            },
            scope: this
        });
    },

    toggleFilter: function (btn) {
        if (btn.filter === undefined) {
            return;
        }
        this.getDockedComponent('tbar').getComponent('filter').setIconCls(btn.iconCls);
        this.getDockedComponent('tbar').getComponent('filter').setText(btn.text);
        if (!btn.filter) {
            this.getStore().clearFilter();
        } else {
            this.getStore().filter('type', btn.filter);
        }
    },

    newImageTemplate: function () {
        this.newTemplate('image');
    },

    newVideoTemplate: function () {
        this.newTemplate('video');
    },

    newAudioTemplate: function () {
        this.newTemplate('audio');
    },

    newPdfTemplate: function () {
        this.newTemplate('pdf');
    },

    newTemplate: function (type) {
        if (!type || (type != 'image' && type != 'video' && type != 'audio' && type != 'pdf')) {
            return;
        }

        Ext.MessageBox.prompt('_title', '_title', function (btn, key) {
            if (btn !== 'ok') {
                return;
            }

            Ext.Ajax.request({
                url: Phlexible.Router.generate('mediatemplates_templates_create'),
                params: {
                    type: type,
                    key: key
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        Phlexible.success(data.msg);

                        // store reload
                        this.store.reload({
                            callback: function (template_id) {
                                var r = this.store.getById(template_id);
                                var index = this.store.indexOf(r);
                                this.selModel.selectRange(index);
                                this.fireEvent('create', template_id, r.get('key'), r.get('type'));
                            }.createDelegate(this, [data.id])
                        });
                    } else {
                        Ext.Msg.alert('Failure', data.msg);
                    }
                },
                scope: this

            });
        }, this);
    }
});
