Ext.provide('Phlexible.element.view.accordion.QuickInfo');

Phlexible.element.view.accordion.QuickInfo = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    //title: Phlexible.elements.Strings.data,
    cls: 'p-elements-data-accordion',
    border: false,
    //autoHeight: true,
    //startPinned: true,
    //plugins: [Ext.ux.plugins.ToggleCollapsible],

    initComponent: function () {
        this.items = new Ext.DataView({
            store: new Ext.data.SimpleStore({
                id: 'dummy_id',
                fields: [
                    'title',
                    'nodeId',
                    'teaserId',
                    'status',
                    'masterLanguage'
                ]
            }),
            tpl: new Ext.XTemplate(
                '<tpl for=".">',
                '<div class="data-wrap">',
                '<table style="width:100%">',

                '<tr><th>{[Phlexible.elements.Strings.title]}</th><td>{title}</td></tr>',

                '<tpl if="!teaserId">',
                '<tr><th>{[Phlexible.elements.Strings.id]}:</th><td>{nodeId}</td></tr>',
                '</tpl>',

                '<tpl if="teaserId">',
                '<tr><th>{[Phlexible.elements.Strings.id]}:</th><td>{teaserId}</td></tr>',
                '</tpl>',

                //'<tr><th>{[Phlexible.elements.Strings.author]}:</th><td>{author}</td></tr>',
                //'<tr><th>{[Phlexible.elements.Strings.created]}:</th><td>{create_date}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.status]}:</th>',
                '<tpl if="!status || status == Phlexible.elements.STATUS_OFFLINE"><td>{[Phlexible.elements.Strings.not_published]}</td></tpl>',
                '<tpl if="status && status == Phlexible.elements.STATUS_ONLINE"><td style="color: green">{[Phlexible.elements.Strings.published]}</td></tpl>',
                '<tpl if="status && status == Phlexible.elements.STATUS_ASYNC"><td style="color: red">{[Phlexible.elements.Strings.published_async]}</td></tpl>',
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

        Phlexible.element.view.accordion.QuickInfo.superclass.initComponent.call(this);
    },

    load: function (element) {
        var store = this.getComponent(0).getStore();
        store.removeAll();
        var r = new Ext.data.Record({
            title: element.getTreeNode().attributes.backendTitle,
            nodeId: element.getNodeId(),
            teaserId: element.getTeaserId(),
            status: !element.getTreeNode().attributes.isPublished
                ? Phlexible.element.STATUS_OFFLINE
                : (element.getTreeNode().attributes.isAsync
                    ? Phlexible.element.STATUS_ASYNC
                    : Phlexible.element.STATUS_ONLINE),
            masterLanguage: element.getMasterLanguage()
        });
        store.add(r);
    }
});

Ext.reg('elements-accordion-quickinfo', Phlexible.element.view.accordion.QuickInfo);
