/**
 * Logout menu item
 */
Ext.define('Phlexible.user.handler.Logout', {
    extend: 'Phlexible.gui.menuhandle.handle.HrefHandle',

    text: '_logout',
    iconCls: Phlexible.Icon.get('door-open-in'),
    getComponent: function() {
        return Phlexible.Config.get('users.logout_url');
    }
});
