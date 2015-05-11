/**
 * Icon helper
 */
Ext.define('Phlexible.gui.util.Icon', {
    config: {
        /**
         * @cfg {String} prefix CSS icon prefix
         */
        prefix: 'p-icon-',

        /**
         * @cfg {String} path Icon file path
         */
        path: '/bundles/brainbitsfugueicons/icons/fugue',

        /**
         * @cfg {String} extension Icon file extension
         */
        extension: '.png',

        /**
         * @cfg {Boolean} log Log to console?
         */
        log: false
    },

    /**
     * Constructor
     *
     * @param {Object} config
     */
    constructor: function(config) {
        this.initConfig(config);

        this.names = [];
        this.css = Ext.util.CSS.createStyleSheet('', 'icons');

        this.ADD     = 'plus-circle';
        this.DELETE  = 'minus-circle';
        this.EDIT    = 'pencil';
        this.ERROR   = 'exclamation-circle';
        this.FILTER  = 'funnel';
        this.NOK     = 'cross-circle';
        this.OK      = 'tick-circle';
        this.PREVIEW = 'magnifier';
        this.RELOAD  = 'arrow-circle-315';
        this.RESTORE = 'arrow-circle-135-left';
        this.RESET   = 'arrow-return-180-left';
        this.SAVE    = 'disk-black';
    },

    /**
     * Return all created CSS rules
     *
     * @returns {Object}
     */
    getNames: function() {
        return this.names;
    },

    /**
     * Return CSS rule for the given icon
     *
     * @param {String} icon
     * @return {String}
     */
    getName: function(icon) {
        if (this.has(icon)) {
            return this.names[icon];
        }

        return this.createRule(icon);
    },

    /**
     * Is a rule for the given icon defined?
     *
     * @param {String} icon
     * @return {Boolean}
     */
    has: function(icon) {
        return !!this.names[icon];
    },

    /**
     * Create an icon rule. Returns the created rule
     *
     * @param {String} icon
     * @return {String}
     */
    createRule: function(icon) {
        var name = this.config.prefix + icon,
            rule = '.' + name,
            property;

        if (Ext.util.CSS.getRule(rule, true)) {
            if (this.getLog()) {
                Phlexible.Logger.info('Icon rule already exists: ' + name);
            }
            return name;
        }

        property = 'background-image: url(' + this.getPath() + '/16/' + icon + this.getExtension() + ') !important;';
        Ext.util.CSS.createRule(this.css, rule, property);

        this.names[icon] = name;

        if (this.getLog()) {
            Phlexible.Logger.debug('Added CSS icon rule:', icon, name, rule, property);
        }

        return name;
    },

    /**
     * Return icon class
     *
     * @param {String} icon
     * @returns {String}
     */
    get: function(icon) {
        if (!icon) {
            return '';
        }

        // are we already prefixed?
        if (icon.substr(0, this.getPrefix().length) === this.getPrefix()) {
            if (this.getLog()) {
                Phlexible.Logger.info('Icon already prefixed: ' + icon);
            }
            return icon;
        }

        return this.getName(icon);
    },

    /**
     * Create inline icon image tag
     * Looks up icon
     *
     * @param {String} icon
     * @param {Object} attr
     * @return {String}
     */
    inline: function(icon, attr) {
        iconCls = this.get(icon);

        return this.inlineDirect(iconCls, attr);
    },

    /**
     * Create inline icon image tag with text
     *
     * @param {String} icon
     * @param {String} text
     * @param {Object} attr
     * @return {String}
     */
    inlineText: function(icon, text, attr) {
        var v = this.inline(icon, attr);

        if (text) {
            v += ' ' + text;
        }

        return v;
    },

    /**
     * Create inline icon image tag
     * Uses given iconCls
     *
     * @param {String} iconCls
     * @param {Object} attr
     * @return {String}
     */
    inlineDirect: function(iconCls, attr) {
        attr = attr || {};

        var s, i, extraCls = attr.cls || '';

        if (attr.cls) {
            delete attr.cls;
        }

        attr = Ext.applyIf(attr, {
            src: Ext.BLANK_IMAGE_URL,
            width: 16,
            height: 16,
            'class': 'p-inline-icon ' + iconCls + ' ' + extraCls
        });

        s = '<img';

        for (i in attr) {
            s += ' ' + i + '="' + attr[i] + '"';
        }

        s+= ' />';

        return s;
    }
});

