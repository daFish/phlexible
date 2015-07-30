Ext.provide('Phlexible.tree.view.accordion.Versions');

Ext.require('Phlexible.tree.model.Version');

Phlexible.tree.view.accordion.Versions = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.versions,
    tabTip: Phlexible.elements.Strings.versions,
    cls: 'p-tree-versions',
    iconCls: 'p-element-version-icon',
    autoScroll: true,

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            scope: this
        });

        this.viewConfig = {
            deferEmptyText: false,
            emptyText: this.strings.no_versions_found
        };

        this.store = new Ext.data.JsonStore({
            fields: Phlexible.tree.model.Version,
            id: 'version'
        });

        this.columns = [
            {
                header: this.strings.version,
                dataIndex: 'version',
                width: 60,
                renderer: function (v, md, r) {
                    if (r.data.isPublished) {
                        md.attr += 'style="font-weight: bold;"';
                    }
                    if (r.data.wasPublished) {
                        md.attr += 'style="font-style: italic;"';
                    }
                    return v;
                }.createDelegate(this)
            },{
                header: '&nbsp;',
                dataIndex: 'isPublished',
                width: 40,
                renderer: function (v, md, r) {
                    if (r.data.isPublished) {
                        return Phlexible.inlineIcon('p-element-publish-icon');
                    }
                    if (r.data.wasPublished) {
                        return Phlexible.inlineIcon('p-element-set_offline-icon');
                    }
                    return '';
                }.createDelegate(this)
            },
            {
                header: this.strings.date,
                dataIndex: 'createdAt',
                width: 120,
                renderer: function (v, md, r) {
                    if (!v) return '';
                    if (r.data.isPublished) {
                        md.attr += 'style="font-weight: bold;"';
                    }
                    if (r.data.wasPublished) {
                        md.attr += 'style="font-style: italic;"';
                    }
                    return v.format('Y-m-d H:i:s');
                }.createDelegate(this)
            }
        ];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.on({
            rowdblclick: function (grid, index) {
                var r = this.store.getAt(index);
                this.fireEvent('loadVersion', r.get('version'));
            },
            scope: this
        });

        Phlexible.tree.view.accordion.Versions.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        this.element = element;

        this.store.loadData(element.getVersions());

        this.language = element.getLanguage();
        this.version = element.getVersion();

        var r = this.store.getById(this.version);
        if (r) {
            this.getSelectionModel().selectRecords([r]);
        }
    }
});

Ext.reg('tree-accordion-versions', Phlexible.tree.view.accordion.Versions);
