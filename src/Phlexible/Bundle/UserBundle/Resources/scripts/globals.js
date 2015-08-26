Ext.require('Phlexible.user.options.Preferences');
Phlexible.PluginManager.prepend('userOptionCards', 'user.options-preferences');

Ext.require('Phlexible.user.options.Password');
Phlexible.PluginManager.prepend('userOptionCards', 'user.options-password');

Ext.require('Phlexible.user.options.Details');
Phlexible.PluginManager.prepend('userOptionCards', 'user.options-details');

Ext.require('Phlexible.user.edit.Properties');
Phlexible.PluginManager.prepend('userEditCards', {
    xtype: 'user.edit-properties'
});

Ext.require('Phlexible.user.edit.Groups');
Phlexible.PluginManager.prepend('userEditCards', {
    xtype: 'user.edit-groups'
});

Ext.require('Phlexible.user.edit.Roles');
Phlexible.PluginManager.prepend('userEditCards', {
    xtype: 'user.edit-roles'
});

Ext.require('Phlexible.user.edit.Account');
Phlexible.PluginManager.prepend('userEditCards', {
    xtype: 'user.edit-account'
});

Ext.require('Phlexible.user.edit.Preferences');
Phlexible.PluginManager.prepend('userEditCards', {
    xtype: 'user.edit-preferences'
});

Ext.require('Phlexible.user.edit.Comment');
Phlexible.PluginManager.prepend('userEditCards', {
    xtype: 'user.edit-comment'
});

Ext.require('Phlexible.user.edit.Password');
Phlexible.PluginManager.prepend('userEditCards', {
    xtype: 'user.edit-password'
});

Ext.require('Phlexible.user.edit.Details');
Phlexible.PluginManager.prepend('userEditCards', {
    xtype: 'user.edit-details'
});
