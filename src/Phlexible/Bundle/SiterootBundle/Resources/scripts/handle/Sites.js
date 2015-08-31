Ext.define('Phlexible.site.handler.Sites', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.site.view.Main'],

    text: '_sites',
    iconCls: Phlexible.Icon.get('globe'),
    name: 'site.main'
});
