/**
 * Notification helper
 */
Ext.define('Phlexible.gui.util.Notify', {
    successText: '_success',
    failureText: '_failure',
    requires: ['Ext.ux.window.Notification'],

    /**
     * Notify
     *
     * @param {String} title
     * @param {String} text
     * @param {Object} extraParams
     */
    notify: function(title, text, extraParams){
        Ext.create({
            xtype: 'uxnotification',
            corner: 't',
            //manager: 'demo1panel3',
            iconCls: Phlexible.Icon.get('information'),
            slideInAnimation: 'easeIn',
            slideBackAnimation: 'bounceOut',
            slideInDuration: 400,
            slideBackDuration: 400,
            hideDuration: 400,
            autoCloseDelay: 7000,
            closable: false,
            spacing: 20,
            width: 500,
            //title: title,
            header: false,
            html: text
        }).show();
    },

    /**
     * Shortcut for success messages
     *
     * Example:
     *     Phlexible.Notify.success('Action succeeded!');
     *
     * @param {String} msg
     * @param {String} headerText
     */
    success: function(msg, headerText) {
        if (!headerText) {
            headerText = this.successText;
        }
        this.notify(headerText, msg);
    },

    /**
     * Shortcut for failure messages
     *
     * Example:
     *     Phlexible.Notify.failure('Something went wrong...');
     *
     * @param {String} msg
     * @param {String} headerText
     */
    failure: function(msg, headerText) {
        if (!headerText) {
            headerText = this.failureText;
        }
        Ext.MessageBox.alert(headerText, msg);
    }
});
