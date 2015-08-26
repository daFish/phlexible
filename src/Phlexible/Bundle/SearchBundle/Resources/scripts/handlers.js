Phlexible.Handles.add('searchbox', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Handle', {
        createConfig: function() {
            return Ext.create('Phlexible.search.form.SearchBox', {
                width: 150
            });
        }
    });
});

Phlexible.Handles.add('searchboxseparator', 'Phlexible.gui.menuhandle.handle.SeparatorHandle');
