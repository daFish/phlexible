Phlexible.Storage.set('menu', 'searchbox', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Handle', {
        createConfig: function() {
            return Ext.create('Phlexible.search.form.SearchBox', {
                width: 150
            });
        }
    });
});

Phlexible.Storage.set('menu', 'searchboxseparator', 'Phlexible.gui.menuhandle.handle.SeparatorHandle');
