Ext.require('Phlexible.user.options.Preferences');
Phlexible.Storage.prepend('userOptionCards', 'user.options-preferences');

Ext.require('Phlexible.user.options.Password');
Phlexible.Storage.prepend('userOptionCards', 'user.options-password');

Ext.require('Phlexible.user.options.Details');
Phlexible.Storage.prepend('userOptionCards', 'user.options-details');

Ext.require('Phlexible.user.edit.Properties');
Phlexible.Storage.prepend('userEditCards', {
    xtype: 'user.edit-properties'
});

Ext.require('Phlexible.user.edit.Groups');
Phlexible.Storage.prepend('userEditCards', {
    xtype: 'user.edit-groups'
});

Ext.require('Phlexible.user.edit.Roles');
Phlexible.Storage.prepend('userEditCards', {
    xtype: 'user.edit-roles'
});

Ext.require('Phlexible.user.edit.Account');
Phlexible.Storage.prepend('userEditCards', {
    xtype: 'user.edit-account'
});

Ext.require('Phlexible.user.edit.Preferences');
Phlexible.Storage.prepend('userEditCards', {
    xtype: 'user.edit-preferences'
});

Ext.require('Phlexible.user.edit.Comment');
Phlexible.Storage.prepend('userEditCards', {
    xtype: 'user.edit-comment'
});

Ext.require('Phlexible.user.edit.Password');
Phlexible.Storage.prepend('userEditCards', {
    xtype: 'user.edit-password'
});

Ext.require('Phlexible.user.edit.Details');
Phlexible.Storage.prepend('userEditCards', {
    xtype: 'user.edit-details'
});
