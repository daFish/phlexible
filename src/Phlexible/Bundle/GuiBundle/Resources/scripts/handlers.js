Phlexible.Storage.set('menu', 'main', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Array');
});

Phlexible.Storage.set('menu', 'menus', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Array');
});

Phlexible.Storage.set('menu', 'tray', function() {
    return Ext.create('Phlexible.gui.menuhandle.handle.Array');
});

Ext.require('Phlexible.gui.handler.Account');
Phlexible.Storage.set('menu', 'account', 'Phlexible.gui.handler.Account');

Ext.require('Phlexible.gui.handler.Administration');
Phlexible.Storage.set('menu', 'administration', 'Phlexible.gui.handler.Administration');

Ext.require('Phlexible.gui.handler.Configuration');
Phlexible.Storage.set('menu', 'configuration', 'Phlexible.gui.handler.Configuration');

Ext.require('Phlexible.gui.handler.Debug');
Phlexible.Storage.set('menu', 'debug', 'Phlexible.gui.handler.Debug');

Ext.require('Phlexible.gui.handler.Tools');
Phlexible.Storage.set('menu', 'tools', 'Phlexible.gui.handler.Tools');

Ext.require('Phlexible.gui.menuhandle.handle.FillHandle');
Phlexible.Storage.set('menu', 'fill', 'Phlexible.gui.menuhandle.handle.FillHandle');

Ext.require('Phlexible.gui.menuhandle.handle.SeparatorHandle');
Phlexible.Storage.set('menu', 'separator', 'Phlexible.gui.menuhandle.handle.SeparatorHandle');

Ext.require('Phlexible.gui.menuhandle.handle.SpacerHandle');
Phlexible.Storage.set('menu', 'spacer', 'Phlexible.gui.menuhandle.handle.SpacerHandle');

Ext.require('Phlexible.gui.handler.Bundles');
Phlexible.Storage.set('menu', 'bundles', 'Phlexible.gui.handler.Bundles');

Ext.require('Phlexible.gui.handler.Help');
Phlexible.Storage.set('menu', 'help', 'Phlexible.gui.handler.Help');

Ext.require('Phlexible.gui.handler.Properties');
Phlexible.Storage.set('menu', 'properties', 'Phlexible.gui.handler.Properties');

Ext.require('Phlexible.gui.handler.PhpInfo');
Phlexible.Storage.set('menu', 'phpinfo', 'Phlexible.gui.handler.PhpInfo');
