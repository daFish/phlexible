Ext.require('Phlexible.elementtypes.configuration.FieldConfiguration');
Ext.require('Phlexible.cms.configuration.FieldConfigurationFile');

Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems =
    Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems.createSequence(function() {
        this.items.push({
            xtype: 'cms-configuration-field-configuration-file',
            additional: true
        });
    });