/**
 * Tree menu item
 */
Ext.define('Phlexible.tree.handle.Tree', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.tree.view.Main'],

    text: '_tree',
    iconCls: Phlexible.Icon.get('document'),
    name: 'tree.main',

    getIdentifier: function () {
        return (this.getName() + '_' + this.parameters.siterootId).replace(/[^a-zA-Z0-9_]/g, '_');
    },

    getText: function() {
        return this.parameters.title;
    }
});
