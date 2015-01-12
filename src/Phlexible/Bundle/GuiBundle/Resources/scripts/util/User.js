/**
 * Icon helper
 */
Ext.define('Phlexible.gui.util.User', {
    extend: 'Ext.util.Observable',
    $configPrefixed: false,

    config: {
        id: 0,
        firstname: '',
        lastname: '',
        displayName: '',
        username: '',
        email: '',
        emailHash: '',
        impersonated: false,
        roles: [],
        options: {},
        properties: {}
    },

    /**
     * Is access granted?
     *
     * @param {String} role
     * @returns {Boolean}
     */
    isGranted: function(role) {
        var result = this.getRoles().indexOf(role) !== -1;
        Phlexible.console.debug('User.isGranted(' + role + ') === ' + result);
        return result;
    },

    /**
     * Is the user impersonated?
     *
     * @return {Boolean}
     */
    isImpersonated: function() {
        return !!this.getImpersonated();
    }
});

