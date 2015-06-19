Ext.provide('Phlexible.datasources.field.SuggestField');

Ext.require('Ext.ux.form.SuperBoxSelect');

Phlexible.datasources.field.Suggest = Ext.extend(Ext.ux.form.SuperBoxSelect, {
    onResize: function (w, h, rw, rh) {
        Phlexible.datasources.field.Suggest.superclass.onResize.call(this, w, h, rw, rh);

        this.wrap.setWidth(this.width + 20);
    }
});
Ext.reg('datasources-field-suggest', Phlexible.datasources.field.Suggest);
