Ext.require('Phlexible.elementtype.configuration.FieldConfiguration');
Ext.require('Phlexible.cms.configuration.FieldConfigurationFile');

Phlexible.elementtype.configuration.field.Configurations.prototype.initMyItems = Ext.Function.createSequence(
    Phlexible.elementtype.configuration.field.Configurations.prototype.initMyItems,
    function() {
        this.items.push({
            xtype: 'cms-configuration-field-configuration-file',
            additional: true
        });
    }
);
