Ext.define('Phlexible.gui.util.Router', {
    /**
     * @param {Object} data
     * @see setData
     */
    constructor: function(data) {
        this.baseUrl = '';
        this.basePath = '';
        this.routes = {};
        if (Ext.isObject(data)) {
            this.setData(data);
        }
    },

    /**
     * Set routing data
     *
     * @param {Object} data Routing structure:
     * {
     *     baseUrl: 'xxx',
     *     basePath: 'yyy',
     *     routes: {
     *         my_route: {
     *             path: '/my/route/{key}/{name}',
     *             variables: ['key', 'name'],
     *             defaults: {name: 'test'}
     *         }
     *     }
     * }
     */
    setData: function (data) {
        if (data.baseUrl) {
            this.baseUrl = data.baseUrl;
        }
        if (data.basePath) {
            this.basePath = data.basePath;
        }
        if (data.routes) {
            this.routes = data.routes;
        }
    },

    /**
     * Dump routes
     *
     * @param {String} part Route name part for searches, optional
     */
    dump: function (part) {
        for (var key in this.routes) {
            var route = this.routes[key];
            if (!part || key.match(new RegExp(part))) {
                Phlexible.console.info(key, route.path);
            }
        }
    },

    /**
     * Generate URL
     *
     * @param {String} name Route name
     * @param {Object} parameters Route parameters
     * @return {String} Generated URL
     */
    generate: function (name, parameters) {
        if (!this.routes[name]) {
            throw new Error('Unknown route ' + name);
        }

        var route = this.routes[name],
            path = route.path,
            variables = route.variables,
            defaults = route.defaults;

        if (variables) {
            parameters = Ext.clone(parameters || {});
            Ext.each(variables, function (variable) {
                var placeholder = '{' + variable + '}';
                if (parameters[variable] !== undefined) {
                    path = path.replace(placeholder, parameters[variable]);
                    delete parameters[variable];
                    return;
                }
                if (defaults[variable] !== undefined) {
                    path = path.replace(placeholder, defaults[variable]);
                    return;
                }
                throw new Error('Missing parameter ' + variable + ' on route ' + name);
            });
            var query = '';
            for (var key in parameters) {
                if (typeof(parameters[key]) !== 'object')
                    query += '&' + key + '=' + parameters[key];
            }
            if (query) {
                path += '?' + query;
            }
        }

        return path;
    }
});
