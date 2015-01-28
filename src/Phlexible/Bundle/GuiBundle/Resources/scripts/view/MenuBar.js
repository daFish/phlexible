/**
 * Menu bar
 */
Ext.define('Phlexible.gui.view.MenuBar', {
    extend: 'Ext.Toolbar',
    requires: ['Phlexible.gui.view.MenuBarController'],

    xtype: 'gui.menubar',

    controller: 'gui.menubar',

    items: [{
        xtype: 'buttongroup',
        itemId: 'tray',
        hidden: true,
        defaults: {
            scale: 'small'
        }
    }]
});
