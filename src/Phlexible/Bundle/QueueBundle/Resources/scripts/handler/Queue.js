Ext.define('Phlexible.queue.handler.Queue', {
    extend: 'Phlexible.gui.menuhandle.handle.XtypeHandle',
    requires: [
        'Phlexible.queue.view.Main'
    ],

    text: '_queue',
    iconCls: Phlexible.Icon.get('application-task'),
    name: 'queue.main'
});
