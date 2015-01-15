Phlexible.Handles.add('media', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.XtypeHandle', {
        text: Phlexible.mediamanager.Strings.media,
        iconCls: Phlexible.Icon.get('images'),
        xtype: 'mediamanager-main'
    });
});
