/**
 * Error request listener
 */
Ext.define('Phlexible.gui.util.RequestListener', {
    //requires: ['Ext.MessageBox'],

    requestIdleText: '_request_idle',
    requestBusyText: '_request_busy',
    requestErrorText: '_request_error',

    /**
     * Constructor
     */
    constructor: function() {
        this.IDLE_ICON  = Phlexible.Icon.get('server');
        this.BUSY_ICON  = Phlexible.Icon.get('server-cast');
        this.ERROR_ICON = Phlexible.Icon.get('server--exclamation');
    },

    /**
     * Bind to connection
     *
     * @param {Phlexible.gui.util.Application} app
     * @param {Ext.data.Connection} connection
     */
    bind: function(app, connection) {
        connection.on({
            beforerequest: this.handleBeforeRequest,
            requestcomplete: this.handleRequestComplete,
            requestexception: this.handleRequestError,
            scope: this
        });

        app.getMenu().addTrayItem('request', {
            tooltip: this.requestIdleText,
            //cls: 'x-btn-icon',
            iconCls: this.IDLE_ICON,
            handler: this.start,
            scope: this
        });
    },

    /**
     * @param {Ext.data.Connection} conn
     * @param {Object} options
     * @param {Object} eOpts
     * @private
     */
    handleBeforeRequest: function(conn, options, eOpts) {
        this.setButtonBusy();
    },

    /**
     * @param {Ext.data.Connection} conn
     * @param {Object} response
     * @param {Object} options
     * @param {Object} eOpts
     * @private
     */
    handleRequestComplete: function(conn, response, options, eOpts) {
        this.setButtonIdle();

        if (!response.getResponseHeader) {
            return;
        }

        var responseType = response.getResponseHeader('X-Phlexible-Response'),
            result;

        if (responseType === 'result' || responseType === 'form') {
            try {
                result = Ext.decode(response.responseText);
            } catch (err) {
                return;
            }

            if (!result.message) {
                return;
            }

            if (result.success) {
                Phlexible.Notify.success(result.message);
            } else {
                Phlexible.Notify.failure(result.message);
            }
        }
    },

    /**
     * @param {Ext.data.Connection} conn
     * @param {Object} response
     * @param {Object} options
     * @param {Object} eOpts
     * @private
     */
    handleRequestError: function(conn, response, options, eOpts) {
        this.setButtonError();

        var result, window;

        try {
            result = Ext.decode(response.responseText);
        }
        catch (err) {
            return;
        }

        if (!result || !result.message) {
            Ext.MessageBox.alert('Error', 'The last request resulted in an error.');
            return;
        }

        window = Ext.create('Phlexible.gui.RequestErrorWindow', {
            responseStatus: response.status,
            responseStatusText: response.statusText,
            requestUrl: options.url,
            requestMethod: options.method || (options.params ? 'POST' : 'GET').toUpperCase() || '?',
            requestTimeout: options.timeout || Ext.Ajax.timeout,
            requestParams: options.params,
            exception: result
        });
        window.show();
    },

    setButtonIdle: function() {
        Phlexible.App.getMenu().updateTrayItem('request', {iconCls: this.IDLE_ICON, tooltip: this.requestIdleText});
    },

    setButtonBusy: function() {
        Phlexible.App.getMenu().updateTrayItem('request', {iconCls: this.BUSY_ICON, tooltip: this.requestBusyText});
    },

    setButtonError: function() {
        Phlexible.App.getMenu().updateTrayItem('request', {iconCls: this.ERROR_ICON, tooltip: this.requestErrorText});
    }
});
