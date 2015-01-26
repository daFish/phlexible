/**
 * Users menu item
 */
Ext.define('Phlexible.user.handler.Users', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.user.view.MainPanel'],

    text: '_users',
    iconCls: Phlexible.Icon.get('users'),
    name: 'user-main'
});
