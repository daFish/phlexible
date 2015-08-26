/**
 * Main view
 *
 * Input params:
 * - id (optional)
 *   Set focus on specific user
 */
Ext.define('Phlexible.user.view.MainController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.user.main',

    layout: 'fit',
    iconCls: Phlexible.Icon.get('users'),
    border: false,

    routes: [
        'user'
    ],

    init: function() {
        this.callParent(arguments);
    },

    loadParams: function(params) {
        this.getComponent('tabPanel').getComponent('users').loadParams(params);
    }
});
