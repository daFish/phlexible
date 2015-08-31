/**
 * Options window
 */
Ext.define('Phlexible.user.window.OptionsWindow', {
    extend: 'Ext.Window',
    alias: 'widget.users-optionswindow',

    plain: true,
    constrain: true,
    cls: 'p-users-options-window',
    iconCls: Phlexible.Icon.get('card-address'),
    width: 600,
    minWidth: 600,
    height: 400,
    minHeight: 400,
    layout: 'fit',
    border: true,
    modal: true,
    activeItem: 0,

    closeText: '_close',

    initComponent: function() {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        var cards = [];

        Phlexible.Storage.each('userOptionCards', function (xtype) {
            cards.push({
                xtype: xtype
            });
        });

        this.items = [{
            xtype: 'tabpanel',
            tabPosition: 'left',
            tabRotation: 0,
            tabBar: {
                border: false
            },
            defaults: {
                textAlign: 'left',
                bodyPadding: 15
            },
            border: false,
            activeTab: 0,
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
