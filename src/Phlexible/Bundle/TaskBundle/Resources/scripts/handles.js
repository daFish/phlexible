Phlexible.Handles.add('tasks', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.XtypeHandle', {
        text: Phlexible.tasks.Strings.tasks,
        iconCls: Phlexible.Icon.get('clipboard-task'),
        xtype: 'tasks-main'
    });
});
