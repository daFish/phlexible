/**
 * Iframe panel
 */
Ext.define('Phlexible.gui.IframePanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.gui-iframepanel',
    requires: ['Ext.panel.Panel', 'Ext.toolbar.*'],

    border: false,
    header: false,

    /**
     * @property {String} src
     * URI of the page to be shown in the iframe
     */
    src: 'about:blank',

    /**
     * @property {Boolean} enableMask
     * `true` to enable load masking
     */
    enableMask: true,

    /**
     * @property {Boolean} enableReload
     * `true` to enable show "reload" button
     */
    enableReload: false,

    /**
     * @property {Boolean} enableHome
     * `true` to enable show "home" button
     */
    enableHome: false,

    /**
     * @property {Boolean} enableAddress
     * `true` to show address bar
     */
    enableAddress: false,

    /**
     * @property {Boolean} enableWindow
     * `true` to show "open in new window" button
     */
    enableWindow: false,

    /**
     * @property {Number} addressWidth
     * Width of the address bar
     */
    addressWidth: 300,

    /**
     * @property {String} homeIconCls
     * Icon for home button
     */
    homeIconCls: Phlexible.Icon.get('home'),

    /**
     * @property {String} reloadIconCls
     * Icon for reload button
     */
    reloadIconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),

    /**
     * @property {String} openInNewWindowIconCls
     * Icon for open in new window button
     */
    openInNewWindowIconCls: Phlexible.Icon.get('application-browser'),

    /**
     * @property {String} homeText
     * Text of the "home" button
     */
    homeText: '_home',

    /**
     * @property {String} reloadText
     * Text of the "reload" button
     */
    reloadText: '_reload',

    /**
     * @property {String} openInNewWindowText
     * Text of the "open in new window" button
     */
    openInNewWindowText: '_open_in_new_window',

    initComponent: function() {
        if (this.enableReload || this.enableWindow || this.enableHome || this.enableAddress) {
            this.tbar = [];

            var config;

            if (this.enableHome) {
                config = {
                    iconCls: this.homeIconCls,
                    handler: this.goHome,
                    scope: this
                };
                if (this.enableAddress) {
                    config.qtip = this.homeText;
                } else {
                    config.text = this.homeText;
                }
                this.tbar.push(config);
            }

            if (this.enableReload) {
                config = {
                    iconCls: this.reloadIconCls,
                    handler: function() {
                        this.reload();
                    },
                    scope: this
                };
                if (this.enableAddress) {
                    config.qtip = this.reloadText;
                } else {
                    config.text = this.reloadText;
                }
                this.tbar.push(config);
            }

            if (this.enableAddress) {
                this.tbar.push({
                    xtype: 'textfield',
                    itemId: 'adressField',
                    width: this.addressWidth || 300,
                    readOnly: true,
                    value: this.src
                });
            }

            if (this.enableWindow) {
                this.tbar.push('->');
                this.tbar.push({
                    text: this.openInNewWindowText,
                    iconCls: this.openInNewWindowIconCls,
                    handler: this.openWindow,
                    scope: this
                });
            }
        }

        this.updateHthml();

        this.callParent(arguments);
    },

    updateHthml: function() {
        this.html = '<iframe id="iframe-' + this.id + '" style="overflow:auto;width:100%;height:100%;"'+
            ' frameborder="0"  src="' + this.src + '"></iframe>';
    },

    /**
     * Return the current URI
     *
     * @return {String} The current URI
     */
    getSrc: function() {
        return this.src;
    },

    /**
     * Set new URI
     *
     * @param {String} src The new URI
     */
    setSrc: function(src){
        this.src = src;
        var iframe = this.getDOM();
        if (iframe) {
            iframe.src = src;
        }
    },

    /**
     * Reset iframe
     */
    reset: function() {
        var iframe = this.getDOM();
        var iframeParent = iframe.parentNode;
        if (iframe && iframeParent) {
            iframe.src = 'about:blank';
            iframe.parentNode.removeChild(iframe);
        }

        iframe = document.createElement('iframe');
        iframe.frameBorder = 0;
        iframe.src = this.src;
        iframe.id = 'iframe-' + this.id;
        iframe.style.overflow = 'auto';
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        iframeParent.appendChild(iframe);
    },

    /**
     * Reload iframe
     */
    reload: function(){
        this.setSrc(this.getSrc());
    },

    /**
     * Return dom node
     * @return {HTMLElement}
     */
    getDOM: function() {
        return document.getElementById('iframe-' + this.id);
    },

    /**
     * Return document
     * @return {HTMLElement}
     */
    getDocument: function() {
        var iframe = this.getDOM();
        iframe = (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
        return iframe.document;
    },

    destroy: function() {
        var iframe = this.getDOM();
        if (iframe && iframe.parentNode) {
            iframe.src = 'about:blank';
            iframe.parentNode.removeChild(iframe);
        }
        this.callParent(arguments);
    },

    //call this to manually change content.
    //don't call until component is rendered!!!
    update: function(content) {
        this.setSrc('about:blank');
        var doc;
        try {
            doc = this.getDocument();
            doc.open();
            doc.write(content);
            doc.close();
        } catch(err) {
            // reset if any permission issues
            this.reset();
            doc = this.getDocument();
            doc.open();
            doc.write(content);
            doc.close();
        }
    },

    /**
     * Open current src in new window
     */
    openWindow: function() {
        var src = this.getSrc() || 'about:blank';

        window.open(src);
    },

    /**
     * Return to initial URI
     */
    goHome: function() {
        this.setSrc(this.homeSrc);
    },

    /** @private */
    setMask: function() {
        if (this.enableMask) {
            this.el.mask(this.maskMessage);
        }
    },

    removeMask: function() {
        if (this.enableMask) {
            this.el.unmask();
        }
    },

    loadHandler: function() {
        this.src = this.getIframeEl().dom.src;
        this.removeMask();
    }
});
