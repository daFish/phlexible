Ext.provide('Phlexible.tree.view.FileSearch');

Phlexible.tree.view.FileSearch = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    iconCls: 'p-mediamanager-component-icon',
    layout: 'fit',
    bodyStyle: 'padding: 5px',
    autoScroll: true,

    initComponent: function() {
        this.items = {
            xtype: 'dataview',
            cls: 'p-elements-resource-media-panel',
            store: new Ext.data.JsonStore({
                url: Phlexible.Router.generate('tree_search_files'),
                baseParams: {
                    siterootId: this.element.getSiterootId(),
                    query: ''
                },
                root: 'results',
                fields: ['id', 'version', 'name', 'folder_id'],
                autoLoad: false
            }),
            itemSelector: 'div.p-elements-result-wrap',
            overClass: 'p-elements-result-wrap-over',
            style: 'overflow: auto',
            singleSelect: true,
            emptyText: this.strings.no_results,
            deferEmptyText: false,
            //autoHeight: true,
            tpl: new Ext.XTemplate(
                '<tpl for=".">',
                '<div class="p-elements-result-wrap" id="result-wrap-{id}" style="text-align: center">',
                '<div><img src="{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.id, template_key: \"_mm_large\"})]}" width="96" height="96" /></div>',
                '<span>{name}</span>',
                '</div>',
                '</tpl>'
            ),
            listeners: {
                render: function (c) {
                    var v = c;
                    this.imageDragZone = new Ext.dd.DragZone(v.getEl(), {
                        ddGroup: 'imageDD',
                        containerScroll: true,
                        getDragData: function (e) {
                            var sourceEl = e.getTarget(v.itemSelector, 10);
                            if (sourceEl) {
                                d = sourceEl.cloneNode(true);
                                d.id = Ext.id();
                                return v.dragData = {
                                    sourceEl: sourceEl,
                                    repairXY: Ext.fly(sourceEl).getXY(),
                                    ddel: d,
                                    record: v.getRecord(sourceEl)
                                };
                            }
                        },
                        getRepairXY: function () {
                            return this.dragData.repairXY;
                        }
                    });
                },
                contextmenu: function (view, index, node, event) {
                    var record = view.store.getAt(index);
                    if (!record) {
                        return;
                    }

                    if (this.imageSearchContextMenu) {
                        this.imageSearchContextMenu.destroy();
                    }

                    this.imageSearchContextMenu = new Ext.menu.Menu({
                        items: [
                            {
                                text: 'File Links',
                                handler: function (menu) {
                                    var window = new Phlexible.elements.FileLinkWindow({
                                        file_id: record.data.id,
                                        file_name: record.data.name
                                    });
                                    window.show();
                                },
                                scope: this
                            }
                        ]
                    });

                    event.stopEvent();
                    var coords = event.getXY();

                    this.imageSearchContextMenu.showAt([coords[0], coords[1]]);

                },
                scope: this
            }
        };

        this.tbar = [
            {
                xtype: 'textfield',
                emptyText: this.strings.media_search,
                enableKeyEvents: true,
                anchor: '-10',
                listeners: {
                    render: function (c) {
                        c.task = new Ext.util.DelayedTask(c.doSearch, this);
                    },
                    keyup: function (c, event) {
                        if (event.getKey() == event.ENTER) {
                            c.task.cancel();
                            c.doSearch();
                            return;
                        }

                        c.task.delay(500);
                    },
                    scope: this
                },
                doSearch: function () {
                    var view = this.getComponent(0);
                    var query = this.getTopToolbar().items.items[0].getValue();
                    if (!query) return;
                    var store = view.getStore();
                    store.baseParams.query = query;
                    store.load();
                }.createDelegate(this)
            }
        ];

        Phlexible.tree.view.FileSearch.superclass.initComponent.call(this);
    }
});

Ext.reg('tree-file-search', Phlexible.tree.view.FileSearch);
