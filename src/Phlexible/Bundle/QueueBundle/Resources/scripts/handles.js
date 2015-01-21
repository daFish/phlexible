Phlexible.Handles.add('queue', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.WindowHandle', {
        text: Phlexible.queue.Strings.queue,
        iconCls: Phlexible.Icon.get('application-task'),
        window: 'Phlexible.queue.window.QueueStatsWindow'
    });
});
