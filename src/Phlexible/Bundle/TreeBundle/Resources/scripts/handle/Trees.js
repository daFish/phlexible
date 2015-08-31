/**
 * Trees menu item
 */
Ext.define('Phlexible.tree.handle.Trees', {
    extend: 'Phlexible.gui.menuhandle.handle.BubbleMenu',
    requires: ['Phlexible.tree.view.Main'],

    text: '_trees',
    iconCls: Phlexible.Icon.get('documents'),
    name: 'tree.main'
});
