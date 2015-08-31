Ext.define('Phlexible.gui.menuhandle.handle.Array', {
    extend: 'Phlexible.gui.menuhandle.handle.Handle',

    createConfig: function (data) {
        var btns = [];

        if (data.children && Ext.isArray(data.children)) {
            Ext.each(data.children, function (menuItem) {
                var handleName, handler, btnConfig;

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

                btnConfig = handler.createConfig(menuItem);
                if (btnConfig) {
                    btns.push(btnConfig);
                }
            }, this);
        }

        return btns;
    }
});
