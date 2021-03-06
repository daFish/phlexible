Ext.define('Phlexible.gui.Actions', {
    extend: 'Ext.window.Window',

    title: '_Actions',
    width: 400,
    height: 72,
    resizable: false,
    modal: true,
    bodyStyle: 'padding: 10px;',
    closeAction: 'hide',
    items: [
        {
            xtype: 'combo',
            width: 360,
            store: Ext.create('Ext.data.Store', {
                fields: ['text', 'iconCls', 'handler', 'menu'],
                sortInfo: {field: 'text', direction: 'ASC'}
            }),
            editable: true,
            typeAhead: true,
            displayField: 'text',
            mode: 'local',
            triggerAction: 'all',
            selectOnFocus: false,
            forceSelection: true,
            anchor: '-10',
            tpl: '<tpl for="."><div class="x-combo-list-item">{[Phlexible.inlineIcon(values.iconCls)]} {text}</div></tpl>',
            listeners: {
                select: 'onSelect'
            }
        }
    ],
    listeners: {
        hide: function (c) {
            c.getComponent(0).reset();
        },
        show: function (c) {
            c.getComponent(0).focus();
        }
    },
    onSelect: function (c, r) {
        if (!r || !r.data.handler || !r.data.menu) {
            return;
        }

        r.data.handler(r.data.menu);
        Phlexible.gui.Actions.hide();
    }
});
