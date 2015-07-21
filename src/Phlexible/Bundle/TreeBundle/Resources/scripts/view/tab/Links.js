Ext.provide('Phlexible.tree.view.tab.Links');

Ext.require('Phlexible.tree.model.Link');

Phlexible.tree.view.tab.Links = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.elements.Strings.links,
    strings: Phlexible.elements.Strings,
    iconCls: 'p-element-tab_link-icon',
    autoExpandColumn: 2,
    viewConfig: {
        emptyText: Phlexible.elements.Strings.no_links_found
    },

    includeIncoming: 0,

    initComponent: function () {
        this.element.on('load', this.onLoadElement, this);

        // create the data store
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('tree_links'),
            root: 'links',
            totalProperty: 'total',
            id: 'id',
            fields: Phlexible.tree.model.Link
        });

        // create the column model
        this.columns = [
            {
                header: this.strings.type,
                width: 100,
                dataIndex: 'type',
                renderer: function (v, md, r) {
                    if (r.data.iconCls) {
                        v = Phlexible.inlineIcon(r.data.iconCls) + ' ' + v;
                    }

                    return v;
                }
            },
            {
                header: this.strings.language,
                width: 50,
                dataIndex: 'language'
            },
            {
                header: this.strings.version,
                width: 50,
                dataIndex: 'version'
            },
            {
                header: this.strings.field,
                width: 250,
                dataIndex: 'field'
            },
            {
                header: this.strings.content,
                width: 300,
                dataIndex: 'target'
            }
        ];

        // create the selection model
        this.selModel = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.bbar = new Ext.PagingToolbar({
            pageSize: 25,
            store: this.store,
            displayInfo: true,
            displayMsg: this.strings.paging_display_msg,
            emptyMsg: this.strings.paging_empty_msg,
            beforePageText: this.strings.paging_before_page_text,
            afterPageText: this.strings.paging_after_page_text
        });

        this.tbar = [
            {
                text: this.strings.include_incoming_links,
                iconCls: 'p-fields-field_link-icon',
                enableToggle: true,
                pressed: this.includeIncoming,
                toggleHandler: function (btn, state) {
                    this.includeIncoming = state ? 1 : 0;

                    this.store.baseParams.incoming = this.includeIncoming;

                    this.store.reload();
                },
                scope: this
            }
        ];

        this.on({
            show: {
                fn: function () {
                    if (this.store.baseParams.nodeId != this.element.getNodeId() ||
                        this.store.baseParams.version != this.element.getVersion() ||
                        this.store.baseParams.language != this.element.getLanguage()) {
                        this.onRealLoad(this.element.getNodeId(), this.element.getVersion(), this.element.getLanguage());
                    }
                },
                scope: this
            },
            rowdblclick: {
                fn: function (grid, rowIndex) {
                    var record = grid.store.getAt(rowIndex);
                    if (record) {
                        var link = record.get('link');

                        if (link && link.handler) {
                            var handler = link.handler;
                            if (typeof handler == 'string') {
                                handler = Phlexible.evalClassString(handler);
                            }
                            handler(link);
                        }
                    }
                },
                scope: this
            }
        });

        Phlexible.tree.view.tab.Links.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        this.store.removeAll();

        if (element.getElementtypeType() === Phlexible.elementtypes.TYPE_FULL) {
            this.enable();

            if (!this.hidden) {
                this.onRealLoad(this.element.getNodeId(), this.element.getVersion(), this.element.getLanguage());
            }
        } else {
            this.disable();
        }
    },

    onRealLoad: function (nodeId, version, language) {
        this.store.baseParams = {
            nodeId: nodeId,
            version: version,
            language: language,
            incoming: this.includeIncoming
        };

        this.store.load();
    }
});

Ext.reg('tree-tab-links', Phlexible.tree.view.tab.Links);
