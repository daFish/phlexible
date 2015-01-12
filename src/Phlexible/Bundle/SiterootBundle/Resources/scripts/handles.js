Phlexible.Handles.add('siteroots', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.XtypeHandle', {
        text: Phlexible.siteroots.Strings.siteroots,
        iconCls: Phlexible.Icon.get('globe'),
        xtype: 'siteroots-main'
    });
});
