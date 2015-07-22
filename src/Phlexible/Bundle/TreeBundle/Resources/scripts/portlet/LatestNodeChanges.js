Ext.provide('Phlexible.tree.model.LatestNodeChange');
Ext.provide('Phlexible.tree.portlet.LatestNodeChanges');

Ext.require('Ext.ux.Portlet');

Phlexible.tree.model.LatestNodeChange = Ext.data.Record.create([
    {name: 'nodeId', type: 'int'},
    {name: 'language', type: 'string'},
    {name: 'version', type: 'int'},
    {name: 'title', type: 'string'},
    {name: 'icon', type: 'string'},
    {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'modifyUserId', type: 'string'},
    {name: 'menu'}
]);

Phlexible.tree.portlet.LatestNodeChanges = Ext.extend(Ext.ux.Portlet, {
    title: Phlexible.elements.Strings.latest_element_changes,
    strings: Phlexible.elements.Strings,
    iconCls: 'p-element-portlet-icon',
    extraCls: 'elements-portlet',
    bodyStyle: 'padding: 5px',

    initComponent: function () {
        this.store = new Ext.data.SimpleStore({
            fields: Phlexible.tree.model.LatestNodeChange,
            id: 'nodeId',
            sortInfo: {field: 'modifieddAt', direction: 'DESC'}
        });

        var data = this.record.get('data');
        if (data) {
            Ext.each(data, function (item) {
                item.time = new Date(item.time * 1000);
                this.add(new Phlexible.tree.model.LatestNodeChange(item, item.nodeId));
            }, this.store);
        }

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'tr.elements-wrap',
                overClass: 'elements-wrap-over',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.strings.no_recent_elements,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: new Ext.XTemplate(
                    '<table width="100%" cellpadding="0" cellspacing="0" border="0">',
                    '<colgroup>',
                    '<col width="20" />',
                    '<col />',
                    '<col width="20" />',
                    '<col width="25" />',
                    '<col width="80" />',
                    '<col width="110" />',
                    '</colgroup>',
                    '<tr>',
                    '<th colspan="2">{[Phlexible.elements.Strings.title]}</th>',
                    '<th qtip="{[Phlexible.elements.Strings.language]}">{[Phlexible.elements.Strings.language.substring(0,1)]}.</th>',
                    '<th qtip="{[Phlexible.elements.Strings.version]}">{[Phlexible.elements.Strings.version.substring(0,1)]}.</th>',
                    '<th>{[Phlexible.elements.Strings.author]}</th>',
                    '<th>{[Phlexible.elements.Strings.date]}</th>',
                    '</tr>',
                    '<tpl for=".">',
                    '<tr class="elements-wrap" id="elements_last_{nodeId}">',
                    '<td class="elements-portlet-icon"><img src="{icon}" title="{title}" width="18" height="18"/></td>',
                    '<td class="elements-portlet-title">{title}</td>',
                    '<td class="elements-portlet-language">{[Phlexible.inlineIcon("p-gui-" + values.language + "-icon")]}</td>',
                    '<td class="elements-portlet-version">{version}</td>',
                    '<td class="elements-portlet-author">{modifyUserId}</td>',
                    '<td class="elements-portlet-date">{modifiedAt:date("Y-m-d H:i:s")}</td>',
                    '</tr>',
                    '</tpl>',
                    '</table>'
                ),
                listeners: {
                    click: {
                        fn: function (c, index, node) {
                            var r = c.getStore().getAt(index);
                            if (!r) return;
                            var menu = r.data.menu;
                            if (menu && menu.handler) {
                                var handler = menu.handler;
                                if (typeof handler == 'string') {
                                    handler = Phlexible.evalClassString(handler);
                                }
                                handler(menu);
                            }
                        },
                        scope: this
                    }
                }
            }
        ];

        Phlexible.tree.portlet.LatestNodeChanges.superclass.initComponent.call(this);
    },

    updateData: function (data) {
        var latestElementsMap = [];

        for (var i = data.length - 1; i >= 0; i--) {
            var row = data[i];
            latestElementsMap.push(row.nodeId);
            var r = this.store.getById(row.nodeId);
            if (!r) {
                row.time = new Date(row.time * 1000);
                this.store.insert(0, new Phlexible.tree.model.LatestNodeChange(row, row.nodeId));

                Ext.fly('elements_last_' + row.nodeId).frame('#8db2e3', 1);
            }
        }

        for (var i = this.store.getCount() - 1; i >= 0; i--) {
            var r = this.store.getAt(i);
            if (latestElementsMap.indexOf(r.id) == -1) {
                this.store.remove(r);
            }
        }

        this.store.sort('modifieddAt', 'DESC');
    }
});

Ext.reg('tree-portlet-late-node-changes', Phlexible.tree.portlet.LatestNodeChanges);
