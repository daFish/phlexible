Ext.define('Phlexible.message.view.MainController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.message.main',

    onDeleteFilter: function () {
        if (Phlexible.User.isGranted('ROLE_MESSAGE_SUBSCRIPTIONS')) {
            this.getView().getComponent('view').getComponent('subscriptions').reloadSubscriptions();
        }
    }
});
