Ext.define('Phlexible.task.handler.Tasks', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: ['Phlexible.task.view.MainPanel'],

    text: '_tasks',
    iconCls: Phlexible.Icon.get('clipboard-task'),
    name: 'tasks-main'
});