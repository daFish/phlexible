Ext.define('Phlexible.gui.menuhandle.handle.Menu', {
    extend: 'Phlexible.gui.menuhandle.handle.Handle',

    menu: [],

    createConfig: function (data) {
        if (!data.children || !Ext.isArray(data.children)) {
            return null;
        }

        var config = this.createBasicConfig();

        if (data.children && Ext.isArray(data.children)) {
            subMenu = [];

            Ext.each(data.children, function (menuItem) {
                var handleName, handler;

                if (!Phlexible.Handles.has(menuItem.handle)) {
                    Phlexible.Logger.error('Invalid handle in:', menuItem);
                    return;
                }

                if (menuItem.roles) {
                    var allowed = false;
                    Ext.each(menuItem.roles, function(role) {
                        if (Phlexible.User.isGranted(role)) {
                            allowed = true;
                            return false;
                        }
                    });
                    if (!allowed) {
                        return;
                    }
                }

                handleName = Phlexible.Handles.get(menuItem.handle);
                if (Ext.isFunction(handleName)) {
                    handler = handleName();
                } else {
                    handler = Ext.create(handleName);
                }

                if (menuItem.parameters) {
                    handler.setParameters(menuItem.parameters);
                }

                subMenu.push(handler.createConfig(menuItem));
            }, this);

            if (subMenu.length) {
                subMenu.sort(function(a,b) {
                    return (a.text > b.text) - (b.text > a.text);
                } );
                config.menu = subMenu;
            } else {
                config.hidden = true;
            }
        }

        return config;
    }
});
