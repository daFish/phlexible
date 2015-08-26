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
                '<colgroup><col width="100" /><col width="*" /></colgroup>',
                '<tr><th>{[Phlexible.elements.Strings.title]}:</th><td>{title}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.type]}:</th><td>{contentType}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.version]}:</th><td>{contentVersion}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.locale]}:</th><td>{locale}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.id]}:</th><td>{nodeId}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.workspace]}:</th><td>{workspace}</td></tr>',

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
        var store = this.getComponent(0).getStore(),
            node = element.getTreeNode();
        store.removeAll();

        var r = new Ext.data.Record({
            title: node.attributes.backendTitle || node.attributes.title,
            nodeId: element.getNodeId(),
            workspace: node.attributes.workspace,
            contentType: node.attributes.contentType,
            contentId: node.attributes.contentId,
            contentVersion: node.attributes.contentVersion,
            locale: node.attributes.locale,
            language: element.getLanguage(),
            masterLanguage: element.getMasterLanguage(),
            createdBy: element.getCreatedBy(),
            createdAt: element.getCreatedAt(),
            modifiedAt: node.attributes.modifiedAt,
            modifiedBy: node.attributes.modifiedBy,
            publishedAt: node.attributes.publishedAt,
            publishedBy: node.attributes.publishedBy,
            type: node.attributes.publishedBy
        });
        store.add(r);
    }
});

Ext.reg('tree-accordion-quickinfo', Phlexible.tree.view.accordion.QuickInfo);
