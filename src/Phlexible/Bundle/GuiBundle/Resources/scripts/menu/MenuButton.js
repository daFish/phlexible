/**
 * Menu button
 */
Ext.define('Phlexible.gui.menu.MenuButton', {
    extend: 'Ext.Button',
    alias: 'widget.menu-button',

    setIconCls: function(cls){
        if (this.scale === 'medium' && this.iconCls24) {
            cls = this.iconCls24;
        }
        else if(this.scale === 'large' && this.iconCls32) {
            cls = this.iconCls32;
        }

        this.callParent(arguments);
    }
});