Ext.define('Phlexible.mediatemplate.view.List', {
    extend: 'Ext.grid.GridPanel',
    xtype: 'mediatemplate.list',

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
            model: 'Phlexible.mediatemplate.model.MediaTemplate',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('phlexible_mediatemplate_get_mediatemplates'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'mediatemplates',
                    idProperty: 'key',
                    totalProperty: 'count'
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
                            handler: this.createImageTemplate,
                            scope: this
                        },
                        {
                            text: this.videoText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.video,
                            handler: this.createVideoTemplate,
                            scope: this
                        },
                        {
                            text: this.audioText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.audio,
                            handler: this.createAudioTemplate,
                            scope: this
                        },
                        {
                            text: this.pdf2swfText,
                            iconCls: Phlexible.mediatemplate.TemplateIcons.pdf2swf,
                            handler: this.createPdfTemplate,
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
            rowdblclick: function (grid, mediaType) {
                this.fireEvent('loadTemplate', mediaType);
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

    createImageTemplate: function () {
        this.createTemplate('image');
    },

    createVideoTemplate: function () {
        this.createTemplate('video');
    },

    createAudioTemplate: function () {
        this.createTemplate('audio');
    },

    createPdfTemplate: function () {
        this.createTemplate('pdf');
    },

    createTemplate: function (type) {
        this.fireEvent('createTemplate', type);
    }
});
