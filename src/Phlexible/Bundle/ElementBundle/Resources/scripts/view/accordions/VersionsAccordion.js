Ext.provide('Phlexible.elements.accordion.Versions');

Phlexible.elements.accordion.Versions = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.versions,
    cls: 'p-elements-versions-accordion',
    iconCls: 'p-element-version-icon',
    border: false,
    autoHeight: true,
    autoScroll: true,
    viewConfig: {
        deferEmptyText: false,
        emptyText: Phlexible.elements.Strings.no_versions_found,
        forceFit: true
    },

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            fields: [
                {name: 'version', type: 'int'},
                {name: 'format', type: 'int'},
                {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
                {name: 'isPublished', type: 'boolean'},
                {name: 'wasPublished', type: 'boolean'}
            ],
            id: 'version'
        });

        this.columns = [
            {
                header: this.strings.version,
                dataIndex: 'version',
                width: 40,
                renderer: function (v, md, r) {
                    if (this.element.getTreeNode().attributes.publishedVersion == r.data.version) {
                        md.attr += 'style="font-weight: bold;"';
                    }
                    if (r.data.was_published) {
                        md.attr += 'style="font-style: italic;"';
                    }
                    return v;
                }.createDelegate(this)
            },
            {
                header: this.strings.date,
                dataIndex: 'createdAt',
                width: 80,
                renderer: function (v, md, r) {
                    if (!v) return '';
                    if (this.element.getTreeNode().attributes.publishedVersion == r.data.version) {
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

        Phlexible.elements.accordion.Versions.superclass.initComponent.call(this);
    },

    load: function (element) {
        this.element = element;

        this.setTitle(this.strings.versions + ' [' + element.getVersions().length + ']');
        this.store.loadData(element.getVersions());

        this.language = element.getLanguage();
        this.version = element.getVersion();

        var r = this.store.getById(this.version);
        if (r) {
            this.getSelectionModel().selectRecords([r]);
        }
    }
});

Ext.reg('elements-versionsaccordion', Phlexible.elements.accordion.Versions);
