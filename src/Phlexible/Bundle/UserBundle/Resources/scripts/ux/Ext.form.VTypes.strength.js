Ext.apply(Ext.form.VTypes, {
    strength: function(val, field) {
        return field.score > field.strength;
    },
    strengthText: 'Password is not strong enough'
});