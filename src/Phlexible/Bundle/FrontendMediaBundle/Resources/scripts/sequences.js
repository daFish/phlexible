Ext.require('Phlexible.elementtype.configuration.field.Configurations');
Ext.require('Phlexible.frontendmedia.configuration.FieldConfigurationFile');

Phlexible.elementtype.configuration.field.Configurations.prototype.initMyItems = Ext.Function.createSequence(
    Phlexible.elementtype.configuration.field.Configurations.prototype.initMyItems,
    function() {
        this.items.push({
            xtype: 'frontendmedia-configuration-field-configuration-file',
            additional: true
        });
    }
);