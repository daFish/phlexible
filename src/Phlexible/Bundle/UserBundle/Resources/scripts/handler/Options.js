/**
 * Options menu item
 */
Ext.define('Phlexible.user.handler.Options', {
    extend: 'Phlexible.gui.menuhandle.handle.WindowHandle',
    requires: ['Phlexible.user.window.OptionsWindow'],

    text: '_options',
    iconCls: Phlexible.Icon.get('equalizer'),
    name: 'Phlexible.user.window.OptionsWindow'
});
