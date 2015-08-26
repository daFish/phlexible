/*
 Ext.tree.TreeNodeUI.prototype.initEvents = Ext.tree.TreeNodeUI.prototype.initEvents.createSequence(function(){
 if(this.node.attributes.tipCfg){
 var o = this.node.attributes.tipCfg;
 o.target = Ext.id(this.textNode);
 Ext.QuickTips.register(o);
 }
 });
 */

/*
Ext.form.Field.prototype.show = Ext.form.Field.prototype.show.createSequence(function () {
    if (this.getEl().up('div.x-form-item')) {
        this.getEl().up('div.x-form-item').setVisibilityMode(Ext.Element.DISPLAY).show();
    }
});
Ext.form.Field.prototype.hide = Ext.form.Field.prototype.hide.createSequence(function () {
    if (this.getEl().up('div.x-form-item')) {
        this.getEl().up('div.x-form-item').setVisibilityMode(Ext.Element.DISPLAY).hide();
    }
});

Ext.data.Store.prototype.onMetaChange = function (meta, rtype, o) {
    this.recordType = rtype;
    this.fields = rtype.prototype.fields;
    delete this.snapshot;
    if (meta.sortInfo)this.sortInfo = meta.sortInfo;
    this.modified = [];
    this.fireEvent('metachange', this, this.reader.meta);
};
*/