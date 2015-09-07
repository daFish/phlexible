/**
 * Menu bar
 */
Ext.define('Phlexible.gui.view.MenuBar', {
    extend: 'Ext.Toolbar',
    requires: ['Phlexible.gui.view.MenuBarController'],

    xtype: 'gui.menubar',

    controller: 'gui.menubar',

    height: 40,
    items: [{
        xtype: 'tbtext',
        itemId: 'logo',
        text: '<div style="width:111px;height:28px;background: url(\'/logo.png\')" align="center"></div>'
    },{
        xtype: 'buttongroup',
        itemId: 'tray',
        hidden: true,
        defaults: {
            scale: 'small'
        }
    }]
});
