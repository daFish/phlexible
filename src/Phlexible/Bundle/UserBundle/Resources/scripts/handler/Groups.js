/**
 * Groups menu item
 */
Ext.define('Phlexible.user.handler.Groups', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.user.view.groups.Main'],

    text: '_groups',
    iconCls: Phlexible.Icon.get('users'),
    name: 'user.groups.main'
});
