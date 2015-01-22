Ext.define('Phlexible.gui.handler.Help', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.gui.view.Help'],

    text: '_help',
    iconCls: Phlexible.Icon.get('book-question'),
    name: 'gui-help'
});
