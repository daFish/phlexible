Ext.define('Phlexible.user.window.SuccessorWindow', {
    extend: 'Phlexible.gui.util.Dialog',

    title: '_SuccessorWindow',
    width: 400,
    height: 220,

    textHeader: '_textHeader',
    textDescription: '_textDescription',
    textOk: '_textOk',
    textCancel: '_textCancel',

    userId: null,

    getFormItems: function () {
        return [
            {
                xtype: 'combo',
                hiddenName: 'successor',
                fieldLabel: this.successorText,
                anchor: '-80',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('users_successor_list', {userId: this.userId}),
                    fields: ['id', 'name'],
                    id: 'id'
                }),
                displayField: 'name',
                valueField: 'id',
                mode: 'remote',
                allowBlank: false,
                triggerAction: 'all',
                editable: false,
                selectOnFocus: true
            }
        ];
    }
});
