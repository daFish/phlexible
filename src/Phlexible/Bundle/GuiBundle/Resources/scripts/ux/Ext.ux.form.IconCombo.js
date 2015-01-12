/**
 * Ext.ux.IconCombo Extension Class for Ext 4.2 Library
 *
 * @author  Daniel Kuhnley
 * @class Ext.ux.IconCombo
 * @extends Ext.form.field.ComboBox
 */
Ext.define('Ext.ux.form.IconCombo',{
    extend:'Ext.form.field.ComboBox',
    alias:'widget.iconcombo',

    initComponent:function() {

        Ext.apply(this, {
            scope:this,
            listConfig: {
                scope:this,
                iconClsField:this.iconClsField,
                getInnerTpl: function() {
                    return '<tpl for=".">' +
                        '<div class="x-combo-list-item ux-icon-combo-item ' +
                        '{' + this.iconClsField + '}">' +
                        '{' + this.displayField + '}' +
                        '</div></tpl>';
                }
            },
            fieldSubTpl: [
                '<div class="{hiddenDataCls}" role="presentation"></div>',
                '<div class="ux-icon-combo-wrap"><input id="{id}" type="{type}" {inputAttrTpl}',
                '<tpl if="value"> value="{value}"</tpl>',
                '<tpl if="name"> name="{name}"</tpl>',
                '<tpl if="placeholder"> placeholder="{placeholder}"</tpl>',
                '<tpl if="size"> size="{size}"</tpl>',
                '<tpl if="maxLength !== undefined"> maxlength="{maxLength}"</tpl>',
                '<tpl if="readOnly"> readonly="readonly"</tpl>',
                '<tpl if="disabled"> disabled="disabled"</tpl>',
                '<tpl if="tabIdx"> tabIndex="{tabIdx}"</tpl>',
                '<tpl if="fieldStyle"> style="{fieldStyle}"</tpl>',
                'class="{fieldCls} {typeCls}" autocomplete="off" /></div>',
                {
                    compiled: true,
                    disableFormats: true
                }]
        });

        // call parent initComponent
        this.callParent(arguments);

    }, // end of function initComponent

    onRender:function(ct, position) {
        // call parent onRender
        this.callParent(arguments);

        // adjust styles
        this.el.down('div[class=ux-icon-combo-wrap]').applyStyles({
            position: 'relative'
        });
        this.el.down('input').addCls('ux-icon-combo-input');

        // add div for icon
        this.icon = Ext.core.DomHelper.append(this.el.down('div[class=ux-icon-combo-wrap]'), {
            tag: 'div',
            style:'position:absolute'
        });
    }, // end of function onRender

    setIconCls: function() {
        if (this.rendered) {
            var rec = this.store.findRecord(this.valueField, this.getValue());
            if (rec) {
                this.icon.className = 'ux-icon-combo-icon ' + rec.get(this.iconClsField);
            } else {
                this.icon.className = '';
            }
        } else {
            this.on('render', this.setIconCls, this, {
                single: true
            } );
        }
    }, // end of function setIconCls

    setValue: function(value) {
        this.callParent(arguments);
        this.setIconCls();
    } // end of function setValue
});
