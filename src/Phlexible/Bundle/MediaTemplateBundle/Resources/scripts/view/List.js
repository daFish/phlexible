Ext.define('Phlexible.mediatemplate.view.List', {
    extend: 'Ext.grid.GridPanel',
    xtype: 'mediatemplate.list',

    iconCls: Phlexible.Icon.get('image-resize'),
    border: true,

    titleText: '_titleText',
    addText: '_addText',
    imageText: '_imageText',
    videoText: '_videoText',
    audioText: '_audioText',
    noFilterText: '_noFilterText',

    initComponent: function () {
        this.initMyColumns();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.titleText,
                dataIndex: 'key',
                flex: 1,
                sortable: true,
                renderer: function (v, md, r) {
                    if (r.dirty) {
                        md.tdCls = 'x-grid-dirty-cell';
                    }
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
                            '-',
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
                        }
                    ]
                }
            ]
        }];
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

    createTemplate: function (type) {
        this.fireEvent('createTemplate', type);
    }
});
