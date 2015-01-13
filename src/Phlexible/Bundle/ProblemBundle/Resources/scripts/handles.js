Phlexible.Handles.add('problems', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.XtypeHandle', {
        text: Phlexible.problems.Strings.problems,
        iconCls: Phlexible.Icon.get('exclamation'),
        xtype: 'problems-list'
    });
});
