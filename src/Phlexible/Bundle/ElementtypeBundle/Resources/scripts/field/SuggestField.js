Ext.define('Phlexible.elementtype.field.SuggestField', {
    extend: 'Ext.form.field.Tag',
    xtype: 'field.suggest',

    onResize: function (w, h, rw, rh) {
        Phlexible.elementtypes.field.Suggest.superclass.onResize.call(this, w, h, rw, rh);

        this.wrap.setWidth(this.width + 20);
    }
});
