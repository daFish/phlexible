Ext.define('Phlexible.elementtype.handler.Elementtypes', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.elementtype.view.MainPanel'],

    text: '_elementtypes',
    iconCls: Phlexible.Icon.get('tree'),
    component: 'elementtype-main'
});