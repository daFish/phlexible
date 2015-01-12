/**
 * Abstract infobar
 */
Ext.define('Phlexible.dashboard.infobar.AbstractInfoBar', {
    extend: 'Ext.container.Container',

    TYPE_NOTICE: 'notice',
    TYPE_URGENT: 'urgent',

    frame: true,
    border: true,
    padding: 10,
    style: 'border-bottom: 1px solid #99bce8 !important; background-color: #dfe9f6;',

    type: null,

    infoText: '_no_text',

    initComponent: function(){
        this.cls = 'p-dashboard-infobar';

        switch (this.type) {
            case this.TYPE_NOTICE:
                this.cls += ' p-dashboard-infobar-notice';
                this.iconCls = Phlexible.Icon.get('exclamation');
                break;

            case this.TYPE_URGENT:
                this.cls += ' p-dashboard-infobar-urgent';
                this.iconCls = Phlexible.Icon.get('exclamation-red-frame');
                break;
        }

        this.tpl = this.createTpl();

        this.callParent(arguments);
    },

    getInfoText: function() {
        return this.infoText;
    },

    createTpl: function() {
        return new Ext.XTemplate(
            this.iconCls ? '<div style="float: left;">' + Phlexible.Icon.inline(this.iconCls) + '</div>' : '',
            this.getInfoText()
        );
    }
});