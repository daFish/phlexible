Ext.require('Phlexible.datasource.configuration.FieldConfigurationSuggest');
Ext.require('Phlexible.datasource.window.MetaSuggestWindow');
Ext.require('Phlexible.datasource.window.SuggestConfigurationWindow');
Ext.require('Phlexible.metaset.util.Fields');

Phlexible.elementtype.configuration.field.Configurations.prototype.initMyItems = Ext.Function.createSequence(
    Phlexible.elementtype.configuration.field.Configurations.prototype.initMyItems,
    function() {
        this.items.push({
            xtype: 'datasource.configuration.field.suggest',
            additional: true
        });
    }
);

Phlexible.metaset.util.Fields.prototype.initFields = Ext.Function.createSequence(
    Phlexible.metaset.util.Fields.prototype.initFields,
    function() {
        this.set('suggest', {
            title: 'Suggest',
            beforeEditCallback: function (grid, field, record) {
                if (grid.master !== undefined) {
                    var isSynchronized = (1 == record.get('synchronized'));

                    // skip editing english values if language is synchronized
                    if (!grid.master && isSynchronized) {
                        return false;
                    }
                }

                var w = new Phlexible.datasources.window.MetaSuggestWindow({
                    record: record,
                    valueField: field,
                    metaLanguage: grid.language,
                    listeners: {
                        store: function () {
                            grid.validateMeta();
                        }
                    }
                });

                w.show();

                return false;
            },
            configure: function (record) {
                var w = new Phlexible.datasources.window.SuggestConfigurationWindow({
                    options: record.get('options'),
                    listeners: {
                        select: function (options) {
                            record.set('options', options);
                        },
                        scope: this
                    }
                });
                w.show();
            },
            validate: function (record) {
                if (!record.get('options')) {
                    Ext.MessageBox.alert(Phlexible.datasources.Strings.failure, Phlexible.datasources.Strings.suggest_needs_options);
                    return false;
                }

                return true;
            }
        });
    }
);
