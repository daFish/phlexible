Ext.define('Phlexible.metaset.validator.Options', {
    extend: 'Ext.data.validator.Validator',
    alias: 'data.validator.metaset-options',
    validate: function(value, field) {
        if ((field.get('type') === 'select' || field.get('type') === 'suggest') && !field.get('options')) {
            return false;
        }

        return true;
    }
});
