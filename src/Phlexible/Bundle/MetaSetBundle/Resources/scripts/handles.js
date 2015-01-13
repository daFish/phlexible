Phlexible.Handles.add('metasets', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.XtypeHandle', {
        text: Phlexible.metasets.Strings.metasets,
        iconCls: Phlexible.Icon.get('weather-clouds'),
        xtype: 'metasets-main'
    });
});
