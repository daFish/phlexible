Phlexible.Handles.add('main', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Group');
});

Phlexible.Handles.add('menus', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Group');
});

Phlexible.Handles.add('tray', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Group');
});

Ext.require('Phlexible.gui.handler.Account');
Phlexible.Handles.add('account', 'Phlexible.gui.handler.Account');

Ext.require('Phlexible.gui.handler.Administration');
Phlexible.Handles.add('administration', 'Phlexible.gui.handler.Administration');

Ext.require('Phlexible.gui.handler.Configuration');
Phlexible.Handles.add('configuration', 'Phlexible.gui.handler.Configuration');

Ext.require('Phlexible.gui.handler.Debug');
Phlexible.Handles.add('debug', 'Phlexible.gui.handler.Debug');

Ext.require('Phlexible.gui.handler.Tools');
Phlexible.Handles.add('tools', 'Phlexible.gui.handler.Tools');

Ext.require('Phlexible.gui.menuhandle.handle.FillHandle');
Phlexible.Handles.add('fill', 'Phlexible.gui.menuhandle.handle.FillHandle');

Ext.require('Phlexible.gui.menuhandle.handle.SeparatorHandle');
Phlexible.Handles.add('separator', 'Phlexible.gui.menuhandle.handle.SeparatorHandle');

Ext.require('Phlexible.gui.menuhandle.handle.SpacerHandle');
Phlexible.Handles.add('spacer', 'Phlexible.gui.menuhandle.handle.SpacerHandle');

Ext.require('Phlexible.gui.handler.Bundles');
Phlexible.Handles.add('bundles', 'Phlexible.gui.handler.Bundles');

Ext.require('Phlexible.gui.handler.Help');
Phlexible.Handles.add('help', 'Phlexible.gui.handler.Help');

Ext.require('Phlexible.gui.handler.Properties');
Phlexible.Handles.add('properties', 'Phlexible.gui.handler.Properties');

Ext.require('Phlexible.gui.handler.PhpInfo');
Phlexible.Handles.add('phpinfo', 'Phlexible.gui.handler.PhpInfo');
