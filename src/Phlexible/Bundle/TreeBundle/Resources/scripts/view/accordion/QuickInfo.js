Ext.provide('Phlexible.tree.view.accordion.QuickInfo');

Phlexible.tree.view.accordion.QuickInfo = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    //title: Phlexible.elements.Strings.data,
    cls: 'p-tree-quickinfo',
    //autoHeight: true,
    //startPinned: true,
    //plugins: [Ext.ux.plugins.ToggleCollapsible],

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            scope: this
        });

        this.items = new Ext.DataView({
            store: new Ext.data.SimpleStore({
                id: 'dummy_id',
                fields: [
                    'title',
                    'nodeId',
                    'status',
                    'version',
                    'language',
                    'masterLanguage',
                    'elementtypeName'
                ]
            }),
            tpl: new Ext.XTemplate(
                '<tpl for=".">',
                '<div class="data-wrap">',
                '<table style="width:100%">',

                '<tr><th>{[Phlexible.elements.Strings.title]}:</th><td>{title}</td></tr>',

                '<tr><th>{[Phlexible.elements.Strings.type]}:</th><td>{elementtypeName}</td></tr>',

                '<tr><th>{[Phlexible.elements.Strings.id]}:</th><td>{nodeId} ({language}, v{version})</td></tr>',

                '<tr><th>{[Phlexible.elements.Strings.status]}:</th>',
                '<tpl if="!status || status == Phlexible.tree.STATUS_OFFLINE"><td>{[Phlexible.elements.Strings.not_published]}</td></tpl>',
                '<tpl if="status && status == Phlexible.tree.STATUS_ONLINE"><td style="color: green">{[Phlexible.elements.Strings.published]}</td></tpl>',
                '<tpl if="status && status == Phlexible.tree.STATUS_ASYNC"><td style="color: red">{[Phlexible.elements.Strings.published_async]}</td></tpl>',
                '</tr>',
                '<tpl if="values.masterLanguage != values.language">',
                '<tr><th>{[Phlexible.elements.Strings.masterlanguage]}:</th><td style="color: red;">{[Phlexible.inlineIcon("p-gui-"+values.masterLanguage+"-icon")]} {[Phlexible.gui.Strings[values.masterLanguage]]}</td></tr>',
                '</tpl>',

                '</table>',
                '</div>',
                '</tpl>'
            ),
            autoHeight: true,
            singleSelect: true,
            overClass: 'x-view-over',
            itemSelector: 'div.data-wrap'
        });

        Phlexible.tree.view.accordion.QuickInfo.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        var store = this.getComponent(0).getStore();
        store.removeAll();
        var r = new Ext.data.Record({
            title: element.getTreeNode().attributes.backendTitle,
            nodeId: element.getNodeId(),
            status: !element.getTreeNode().attributes.isPublished
                ? Phlexible.tree.STATUS_OFFLINE
                : (element.getTreeNode().attributes.isAsync
                    ? Phlexible.tree.STATUS_ASYNC
                    : Phlexible.tree.STATUS_ONLINE),
            version: element.getVersion(),
            language: element.getLanguage(),
            masterLanguage: element.getMasterLanguage(),
            elementtypeName: element.getElementtypeName()
        });
        store.add(r);
    }
});

Ext.reg('tree-accordion-quickinfo', Phlexible.tree.view.accordion.QuickInfo);
