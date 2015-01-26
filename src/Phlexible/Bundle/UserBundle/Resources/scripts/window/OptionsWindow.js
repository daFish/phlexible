/**
 * Options window
 */
Ext.define('Phlexible.user.window.OptionsWindow', {
    extend: 'Ext.Window',
    alias: 'widget.users-optionswindow',

    title: '_options',
    plain: true,
    constrain: true,
    cls: 'p-users-options-window',
    iconCls: Phlexible.Icon.get('card-address'),
    width: 600,
    minWidth: 600,
    height: 400,
    minHeight: 400,
    layout: 'border',
    border: true,
    modal: true,
    activeItem: 0,
//    deferredRender: true,

    closeText: '_close',

    initComponent: function() {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        var xtypes = Phlexible.PluginManager.get('userOptionCards'),
            cards = [],
            buttons = [];

        Ext.each(xtypes, function (xtype) {
            var cls = Ext.ClassManager.getByAlias('widget.' + xtype);

            buttons.push({
                text: cls.prototype.title,
                iconCls: cls.prototype.iconCls,
                margin: '0 0 5 0',
                width: 135,
                textAlign: 'left',
                toggleHandler: function (btn, state) {
                    if (state) {
                        this.getComponent(1).getLayout().setActiveItem('panel-' + xtype);
                    }
                },
                scope: this
            });

            cards.push({
                xtype: xtype,
                itemId: 'panel-' + xtype,
                header: false
            });
        }, this);

        buttons[0].pressed = true;

        this.items = [{
            xtype: 'buttongroup',
            region: 'west',
            width: 150,
            columns: 1,
            margin: 5,
            padding: 5,
            defaults: {
                enableToggle: true,
                toggleGroup: 'card'
            },
            items: buttons
        }, {
            region: 'center',
            layout: 'card',
            border: false,
            margin: '5 5 5 0',
            items: cards
        }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                text: this.closeText,
                handler: this.close,
                scope: this
            }]
        }];
    }
});
