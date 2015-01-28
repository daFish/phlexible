Ext.define('Phlexible.gui.view.Main', {
    extend: 'Ext.container.Container',
    requires: [
        'Phlexible.dashboard.view.Dashboard',
        'Phlexible.gui.view.MainController',
        'Phlexible.gui.view.MainModel',
        'Phlexible.gui.view.MenuBar'
    ],

    xtype: 'gui.main',

    controller: 'gui.main',
    viewModel: {
        type: 'gui.main'
    },

    layout: 'fit',

    items: [{
        xtype: 'tabpanel',
        itemId: 'main',
        border: false,
        tabPosition: 'bottom',
        enableTabScroll: true,
        activeTab: 0,
        dockedItems: [{
            xtype: 'gui.menubar',
            itemId: 'menubar',
            menu: Phlexible.Menu
        }],
        items: [{
            xtype: 'dashboard.dashboard'
        }]
    }]
});