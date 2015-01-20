Phlexible.Handles.add('mediatemplates', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.XtypeHandle', {
        text: Phlexible.mediatemplates.Strings.mediatemplates,
        iconCls: Phlexible.Icon.get('image-select'),
        xtype: 'mediatemplates-main'
    });
});
