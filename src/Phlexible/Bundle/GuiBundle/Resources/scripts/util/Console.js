/**
 * Console logging wrapper. Supports Gecko/Firebug and Webkit based Browsers
 * For a complete list of logging methods see [http://getfirebug.com/wiki/index.php/Console_API]
 * Example usage:
 *     Phlexible.console = new Phlexible.gui.util.Console();
 *
 *     // Write a simple log message to the console
 *     Phlexible.console.log('Hello World!');
 *     // will output
 *     // > Hello World!
 *
 *     // Write multiple values to console
 *     Phlexible.console.info(1,2,3)
 *     // will output
 *     // > 1 2 3
 *
 *     // Disable logging
 *     Phlexible.console.disable();
 *
 *     // Enable logging
 *     Phlexible.console.enable();
 */
Ext.define('Phlexible.gui.util.Console', {
    /**
     * @method enable
     * Enable console
     */

    /**
     * @method disable
     * Disable console
     */

    /**
     * @method clear
     * Clear console
     */

    /**
     * @method log
     * Log with level "log"
     */

    /**
     * @method debug
     * Log with level "debug"
     */

    /**
     * @method info
     * Log with level "info"
     */

    /**
     * @method warn
     * Log with level "warn"
     */

    /**
     * @method error
     * Log with level "error"
     */

    /**
     * @method assert
     * Assert
     */

    /**
     * @method dir
     * Log
     */

    /**
     * @method dirxml
     * Log as xml
     */

    /**
     * @method trace
     * Trace
     */

    /**
     * @method group
     * Start group
     */

    /**
     * @method groupCollapsed
     * Start group collapsed
     */

    /**
     * @method groupEnd
     * End group
     */

    /**
     * @method time
     * Start timer
     */

    /**
     * @method timeEnd
     * End timer
     */

    /**
     * @method profile
     * Start profiler
     */

    /**
     * @method profileEnd
     * End profiler
     */

    /**
     * @method count
     * Count object
     */

    constructor: function() {
        // private
        var enabled = true,
            xcall = function(method, args){
                if (enabled && typeof window.console === 'object' && typeof window.console[method] === 'function') {
                    window.console[method].apply(window.console, args);
                }
            };

        // Enable / disable loggin
        this.enable = function() { enabled = true; };
        this.disable = function() { enabled = false; };
        this.isEnabled = function() { return enabled; };

        // console wrapper
        this.clear          = function() { var m = 'clear';          xcall(m, arguments); };
        this.log            = function() { var m = 'log';            xcall(m, arguments); };
        this.debug          = function() { var m = 'debug';          xcall(m, arguments); };
        this.info           = function() { var m = 'info';           xcall(m, arguments); };
        this.warn           = function() { var m = 'warn';           xcall(m, arguments); };
        this.error          = function() { var m = 'error';          xcall(m, arguments); };
        this.assert         = function() { var m = 'assert';         xcall(m, arguments); };
        this.dir            = function() { var m = 'dir';            xcall(m, arguments); };
        this.dirxml         = function() { var m = 'dirxml';         xcall(m, arguments); };
        this.trace          = function() { var m = 'trace';          xcall(m, arguments); };
        this.group          = function() { var m = 'group';          xcall(m, arguments); };
        this.groupCollapsed = function() { var m = 'groupCollapsed'; xcall(m, arguments); };
        this.groupEnd       = function() { var m = 'groupEnd';       xcall(m, arguments); };
        this.time           = function() { var m = 'time';           xcall(m, arguments); };
        this.timeEnd        = function() { var m = 'timeEnd';        xcall(m, arguments); };
        this.profile        = function() { var m = 'profile';        xcall(m, arguments); };
        this.profileEnd     = function() { var m = 'profileEnd';     xcall(m, arguments); };
        this.count          = function() { var m = 'count';          xcall(m, arguments); };
    }
});
