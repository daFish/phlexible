Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.gui.menuhandle.handle.BubbleMenu');
Ext.require('Phlexible.elements.MainPanel');

Phlexible.Handles.add('tree', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        iconCls: 'p-element-component-icon',
        component: 'tree-main',

        getIdentifier: function () {
            return this.getComponent() + '_' + this.parameters.siteroot_id;
        },

        getText: function() {
            return this.parameters.title;
        }
    });
});

Phlexible.Handles.add('trees', function() {
    return new Phlexible.gui.menuhandle.handle.BubbleMenu({
        text: Phlexible.elements.Strings.websites,
        iconCls: 'p-element-component-icon',
        component: 'tree-main'
    });
});
