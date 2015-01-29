/**
 * Poller
 *
 * Usage:
 *     var poller = Ext.create('Phlexible.gui.util.Poller', {
 *         interval: 20000
 *     });
 *     poller.start();
 *
 * @param {Object} config A config object that sets properties.
 */
Ext.define('Phlexible.gui.util.Poller', {
    extend: 'Ext.util.Observable',

    config: {
        /**
         * @cfg {Boolean} autoStart Autostart poller after instanciation
         */
        autoStart: true,
        /**
         * @cfg {Boolean} noButton Don't register tray button
         */
        noButton: false,
        /**
         * @cfg {Number} interval Poll interval in microseconds
         */
        interval: 30000,
        /**
         * @cfg {Number} timeout Poll timeout in seconds
         */
        timeout: 30
    },

    /**
     * @cfg {Object} task Poll task
     */
    task: null,

    /**
     * @cfg {Boolean} active Current activation state
     */
    active: false,

    /**
     * @cfg {Boolean} paused Current pause state
     */
    paused: false,

    pollerOfflineText: '_poller_offline',
    pollerOnlineText: '_poller_online',
    pollerBusyText: '_poller_busy',
    pollerPausedText: '_poller_paused',

    /**
     * @event message
     * Fires when a new message arrives.
     * @param {Object} message
     */

    /**
     * Construcotr
     * @param {Object} config
     */
    constructor: function(config){
        this.initConfig(config);

        this.OFFLINE_ICON = Phlexible.Icon.get('network-status-offline');
        this.ONLINE_ICON  = Phlexible.Icon.get('network-status');
        this.BUSY_ICON    = Phlexible.Icon.get('network-status-busy');
        this.PAUSE_ICON   = Phlexible.Icon.get('network-status-away');

        this.lastPoll = new Date();

        this.callParent(arguments);

    },

    /**
     * Bind to connection
     *
     * @param {Phlexible.gui.util.Application} app
     */
    bind: function(app) {
        Phlexible.Logger.debug('Poller.bind()');

        app.getMenu().addTrayItem('poller', {
            tooltip: this.pollerOfflineText,
            cls: 'x-btn-icon',
            iconCls: this.OFFLINE_ICON,
            handler: this.start,
            scope: this
        });

        this.task = this.createTask();

        if (this.config.autoStart) {
            this.start();
        }
    },

    /**
     * Return task
     *
     * @return {Ext.util.TaskRunner.Task}
     */
    getTask: function() {
        return this.task;
    },

    /**
     * Poll
     */
    poll: function() {
        Phlexible.Logger.debug('Poller.poll()');

        this.setButtonBusy();

        this.lastPoll = new Date();

        Ext.Ajax.request({
            url: Phlexible.Router.generate('gui_poll'),
            disableCaching: true,
            scope: this,
            success: this.processResponse,
            failure: function(response, options) {
                Phlexible.console.error(response);
                Phlexible.console.error(options);
            }
        });
    },

    /**
     * Return active
     * @returns {Boolean}
     */
    isActive: function() {
        return this.active;
    },

    /**
     * Return paused
     * @returns {Boolean}
     */
    isPaused: function() {
        return this.paused;
    },

    /**
     * Start poller
     */
    start: function(){
        Phlexible.Logger.debug('Poller.start()');

        this.setButtonIdle(this.stop);
        this.active = true;
        this.paused = false;
        this.task.start();

        if (new Date() - this.lastPoll > this.config.interval) {
            this.poll();
        }

        Ext.getWin().on('focus', this.start, this);
        Ext.getWin().on('blur', this.pause, this);
    },

    /**
     * Stop poller
     */
    stop: function() {
        Phlexible.Logger.debug('Poller.stop()');

        this.task.stop();
        this.paused = false;
        this.active = false;
        this.setButtonInactive(this.start);

        Ext.getWin().un('focus', this.start, this);
        Ext.getWin().un('blur', this.pause, this);
    },

    /**
     * Pause poller
     */
    pause: function() {
        Phlexible.Logger.debug('Poller.pause()');

        if (!this.isPaused() && this.isActive()) {
            this.task.stop();
            this.paused = true;
            this.setButtonPause(this.start);
        }
    },

    /**
     * Create task
     *
     * @return {Ext.util.TaskRunner.Task}
     * @private
     */
    createTask: function() {
        return Ext.TaskManager.newTask({
            run: this.poll,
            interval: this.config.interval,
            scope: this
        });
    },

    /**
     * Process message response
     *
     * @param {Object} response
     * @private
     */
    processResponse: function(response){
        this.setButtonIdle();

        if (response.responseText) {
            var messages = Ext.decode(response.responseText);
            if (Ext.isArray(messages)) {
                Ext.each(messages, function(message) {
                    Phlexible.Logger.debug('Poller.Message: ' + Ext.encode(message));
                    this.fireEvent('message', message);
                }, this);
            }
        }
    },

    /**
     * @param {Function} handler
     * @private
     */
    setButtonInactive: function(handler) {
        Phlexible.App.getMenu().updateTrayItem('poller', {
            iconCls: this.OFFLINE_ICON,
            tooltip: this.pollerOfflineText,
            handler: handler
        });
    },

    /**
     * @param {Function} handler
     * @private
     */
    setButtonBusy: function(handler) {
        Phlexible.App.getMenu().updateTrayItem('poller', {
            iconCls: this.BUSY_ICON,
            tooltip: this.pollerBusyText,
            handler: handler
        });
    },

    /**
     * @param {Function} handler
     * @private
     */
    setButtonIdle: function(handler) {
        Phlexible.App.getMenu().updateTrayItem('poller', {
            iconCls: this.ONLINE_ICON,
            tooltip: this.pollerOnlineText,
            handler: handler
        });
    },

    /**
     * @param {Function} handler
     * @private
     */
    setButtonPause: function(handler) {
        Phlexible.App.getMenu().updateTrayItem('poller', {
            iconCls: this.PAUSE_ICON,
            tooltip: this.pollerPausedText,
            handler: handler
        });
    }
});
