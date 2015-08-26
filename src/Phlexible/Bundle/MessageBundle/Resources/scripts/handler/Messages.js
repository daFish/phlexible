Ext.define('Phlexible.message.handler.Messages', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.message.view.Main'],

    text: '_Messages',
    iconCls: Phlexible.Icon.get('resource-monitor'),
    name: 'message.main'
});
