/**
 * Menu bar
 */
Ext.define('Phlexible.gui.view.MenuBar', {
    extend: 'Ext.Toolbar',
    requires: ['Phlexible.gui.view.MenuBarController'],

    xtype: 'gui.menubar',

    controller: 'gui.menubar',

    height: 38,
    items: [{
        xtype: 'tbtext',
        itemId: 'logo',
        //text: '<div style="width:93px;height:25px;margin-top:2px;background: url(\'/logo.png\')" align="center"></div>'
        text: '<div style="width:137px;height:25px;margin-top:3px;background: url(\'/logo_cms.png\')" align="center"></div>'
    },{
        xtype: 'buttongroup',
        itemId: 'tray',
        hidden: true,
        defaults: {
            scale: 'small'
        }
    }]
});
