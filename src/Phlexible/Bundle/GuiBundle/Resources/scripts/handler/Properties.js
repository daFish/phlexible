Ext.define('Phlexible.gui.handler.Properties', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.gui.view.PropertiesPanel'],

    text: '_properties',
    iconCls: Phlexible.Icon.get('property'),
    name: 'gui-properties'
});
