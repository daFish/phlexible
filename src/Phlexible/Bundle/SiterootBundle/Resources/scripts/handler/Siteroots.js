Ext.define('Phlexible.siteroot.handler.Siteroots', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.siteroot.view.Main'],

    text: '_siteroots',
    iconCls: Phlexible.Icon.get('globe'),
    name: 'siteroot.main'
});