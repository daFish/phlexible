Ext.define('Phlexible.gui.menuhandle.handle.Group', {
    extend: 'Phlexible.gui.menuhandle.handle.Handle',

    createConfig: function (data) {
        var btns = [];

        if (data.menu && Ext.isArray(data.menu)) {
            Ext.each(data.menu, function (menuItem) {
                var handleName, handler;

                if (!Phlexible.Handles.has(menuItem.handle)) {
                    console.error('Invalid handle in:', menuItem);
                    return;
                }

                if (menuItem.roles) {
                    var allowed = false;
                    Ext.each(menuItem.roles, function(role) {
                        if (Phlexible.App.isGranted(role)) {
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

                btns.push(handler.createConfig(menuItem));
            }, this);
        }

        return btns;
    }
});