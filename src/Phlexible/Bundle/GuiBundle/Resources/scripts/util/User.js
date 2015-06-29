/**
 * User
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
        password: '',
        previousUsername: null,
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
        Phlexible.Logger.debug('User.isGranted(' + role + ') === ' + result);
        return result;
    },

    /**
     * Is the user impersonated?
     *
     * @return {Boolean}
     */
    isImpersonated: function() {
        return !!this.getPreviousUsername();
    },

    /**
     * Return a single property
     *
     * @param {String} key
     * @param {String} defaultValue
     * @returns {*}
     */
    getProperty: function(key, defaultValue) {
        if (this.getProperties()[key]) {
            return this.getProperties()[key];
        }

        if (defaultValue) {
            return defaultValue;
        }

        return null;
    },

    /**
     * Set a single property
     *
     * @param {String} key
     * @param {String} value
     * @returns {Phlexible.gui.util.User}
     */
    setProperty: function(key, value) {
        if (this.getProperties()[key] !== value) {
            var dummy = {},
                properties;
            dummy[key] = value;
            properties = Ext.applyIf(dummy, this.getProperties());
            this.setProperties(properties);
        }

        return this;
    },

    /**
     * Commit changes
     *
     * @param {Function} callback
     * @param {Function} scope
     * @returns {Phlexible.gui.util.User}
     */
    commit: function(callback, scope) {
        Ext.Ajax.request({
            url: Routing.generate('phlx_users_options_update'),
            params: this.changes,
            success: function(response) {
                var result = Ext.decode(response.responseText);

                if (result.success) {
                    this.changes = {};

                    this.fireEvent('commit', this);

                    if (callback) {
                        callback.call(scope, this);
                    }
                } else {
                    this.fireEvent('commitFailed', this);
                }
            },
            scope: this
        });

        return this;
    },

    /**
     * Commit changes
     *
     * @returns {Phlexible.gui.util.User}
     */
    reject: function() {
        Ext.Object.each(this.changes, function(key, value) {
            if (key.substr(0, 9) === 'property#') {
                this.properties[key.substr(9)] = value;
            } else {
                this[key] = value;
            }
        }, this);
        this.changes = {};

        this.fireEvent('reject', this);

        return this;
    },

    /**
     * @param {String} firstname
     * @private
     */
    applyFirstname: function(firstname) {
        if (firstname !== this.firstname) {
            if (this.changes.firstname) {
                this.changes.firstname.new = firstname;
            } else {
                this.changes.firstname = {old: this.firstname, new: firstname};
            }
        }
        return firstname;
    },

    /**
     * @param {String} lastname
     * @private
     */
    applyLastname: function(lastname) {
        if (lastname !== this.lastname) {
            if (this.changes.lastname) {
                this.changes.lastname.new = lastname;
            } else {
                this.changes.lastname = {old: this.lastname, new: lastname};
            }
        }
        return lastname;
    },

    /**
     * @param {String} password
     * @private
     */
    applyPassword: function(password) {
        this.changes.password = {old: null, new: password};

        return password;
    },

    /**
     * @param {String} email
     * @private
     */
    applyEmail: function(email) {
        if (email !== this.email) {
            if (this.changes.email) {
                this.changes.email.new = email;
            } else {
                this.changes.email = {old: this.email, new: email};
            }
        }
        return email;
    },

    /**
     * @param {Object} properties
     * @private
     */
    applyProperties: function(properties) {
        Ext.Object.each(properties, function(key, value) {
            if (value !== this.properties[key]) {
                if (this.changes[key]) {
                    this.changes['property#' + key].new = value;
                } else {
                    this.changes['property#' + key] = {old: this.properties[key], new: value};
                }
            }
        }, this);

        return properties;
    }
});
