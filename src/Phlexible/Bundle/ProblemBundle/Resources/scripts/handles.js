Phlexible.Handles.add('problems', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.XtypeHandle', {
        text: Phlexible.problem.view.ProblemsGrid.prototype.title,
        iconCls: Phlexible.Icon.get('exclamation'),
        xtype: 'problem-list'
    });
});
