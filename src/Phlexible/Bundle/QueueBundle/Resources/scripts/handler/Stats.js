Ext.define('Phlexible.queue.handler.Stats', {
    extend: 'Phlexible.gui.menuhandle.handle.WindowHandle',
    requires: ['Phlexible.queue.window.QueueStatsWindow'],

    text: '_queue',
    iconCls: Phlexible.Icon.get('application-task'),
    name: 'Phlexible.queue.window.QueueStatsWindow'
});
