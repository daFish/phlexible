Ext.require('Phlexible.datasources.configuration.FieldConfigurationSuggest');
Ext.require('Phlexible.datasources.window.MetaSuggestWindow');
Ext.require('Phlexible.datasources.window.SuggestConfigurationWindow');
Ext.require('Phlexible.metasets.util.Fields');

Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems =
    Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems.createSequence(function() {
        this.items.push({
            xtype: 'datasources-configuration-field-configuration-suggest',
            additional: true
        });
    });

Phlexible.metasets.util.Fields.prototype.initFields =
    Phlexible.metasets.util.Fields.prototype.initFields.createSequence(function() {
        this.set('suggest', {
            title: 'Suggest',
            beforeEdit: function (grid, field, record) {
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
    });
