Phlexible.Handles.add('main', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Group');
});

Phlexible.Handles.add('menus', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Group');
});

Phlexible.Handles.add('tray', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Group');
});

Phlexible.Handles.add('account', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Menu', {
        iconCls: 'p-gui-account-icon',
        getText: function () {
            return Phlexible.App.getUser().getDisplayName();
        }
    });
});

Phlexible.Handles.add('administration', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Menu', {
        text: Phlexible.gui.Strings.administration,
        iconCls: 'p-gui-menu_admin-icon'
    });
});

Phlexible.Handles.add('configuration', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Menu', {
        text: Phlexible.gui.Strings.configuration,
        iconCls: 'p-gui-menu_config-icon'
    });
});

Phlexible.Handles.add('debug', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Menu', {
        text: Phlexible.gui.Strings.debug,
        iconCls: 'p-gui-menu_debug-icon'
    });
});

Phlexible.Handles.add('tools', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Menu', {
        text: '_tools',
        iconCls: 'p-gui-menu_tools-icon'
    });
});

Phlexible.Handles.add('fill', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.FillHandle');
});

Phlexible.Handles.add('separator', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.SeparatorHandle');
});

Phlexible.Handles.add('spacer', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.SpacerHandle');
});

Phlexible.Handles.add('bundles', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.XtypeHandle', {
        text: Phlexible.gui.Strings.bundles,
        iconCls: 'p-gui-manager-icon',
        xtype: 'gui-bundles'
    });
});

Phlexible.Handles.add('help', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.XtypeHandle', {
        text: Phlexible.gui.Strings.help,
        iconCls: 'p-gui-help-icon',
        xtype: 'gui-help'
    });
});

Phlexible.Handles.add('properties', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.XtypeHandle', {
        text: Phlexible.gui.Strings.properties,
        iconCls: 'p-gui-php-icon',
        xtype: ''
    });
});

Phlexible.Handles.add('phpinfo', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.WindowHandle', {
        text: 'PHP Info',
        iconCls: 'p-gui-php-icon',
        window: 'Phlexible.gui.PhpInfoWindow'
    });
});
