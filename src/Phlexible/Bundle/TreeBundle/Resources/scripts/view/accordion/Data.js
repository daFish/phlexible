Ext.provide('Phlexible.tree.view.accordion.Data');

Phlexible.tree.view.accordion.Data = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.information,
    tabTip: Phlexible.elements.Strings.information,
    cls: 'p-elements-data-accordion',
    iconCls: 'p-element-information-icon',
    autoHeight: true,
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
                    'eid',
                    'version',
                    'language',
                    'masterLanguage',
                    'createdBy',
                    'createdAt',
                    'elementtypeName',
                    'elementtypeRevision',
                    'isPublished',
                    'publishedVersion',
                    'publishedAt',
                    'publishedBy'
                ]
            }),
            tpl: new Ext.XTemplate(
                '<tpl for=".">',
                '<div class="data-wrap">',
                '<table style="width:100%">',

                '<tr><td colspan="2" style="padding-top: 3px"><div style="float: left; font-style: italic; margin-right: 5px;">{[Phlexible.elements.Strings.element]}</div><hr /><div style="clear: left;" /></td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.eid]}:</th><td>{eid}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.version]}:</th><td>{version}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.author]}:</th><td>{createdBy}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.created]}:</th><td>{createdAt}</td></tr>',

                '<tpl if="isPublished">',
                '<tr><td colspan="2" style="padding-top: 3px"><div style="float: left; font-style: italic; margin-right: 5px;">{[Phlexible.elements.Strings.online_version]}</div><hr /><div style="clear: left;" /></td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.version]}:</th><td>{publishedVersion}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.publisher]}:</th><td>{publishedBy}</td></tr>',
                '<tr><th>{[Phlexible.elements.Strings.published]}:</th><td>{publishedAt}</td></tr>',
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

        Phlexible.tree.view.accordion.Data.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        var store = this.getComponent(0).getStore();
        store.removeAll();
        var r = new Ext.data.Record({
            eid: element.getEid(),
            version: element.getVersion(),
            language: element.getLanguage(),
            masterLanguage: element.getMasterLanguage(),
        });
        store.add(r);
    }
});

Ext.reg('tree-accordion-data', Phlexible.tree.view.accordion.Data);
