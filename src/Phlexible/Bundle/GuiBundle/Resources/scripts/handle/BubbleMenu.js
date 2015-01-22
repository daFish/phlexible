Ext.define('Phlexible.gui.menuhandle.handle.BubbleMenu', {
    extend: 'Phlexible.gui.menuhandle.handle.Menu',

    createConfig: function (data) {
        if (data.menu && Ext.isArray(data.menu) && data.menu.length === 1) {
            var handleName, handler;

            if (!Phlexible.Handles.has(data.menu[0].handle)) {
                console.error('Invalid handle in:', data.menu[0]);
                return;
            }

            handleName = Phlexible.Handles.get(data.menu[0].handle);
            handleName = Phlexible.Handles.get(menuItem.handle);
            if (Ext.isFunction(handleName)) {
                handler = handleName();
            } else {
                handler = Ext.create(handleName);
            }

            if (data.menu[0].parameters) {
                handler.setParameters(data.menu[0].parameters);
            }

            return handler.createConfig(data.menu[0]);
        }

        return this.callParent([data]);
    }
});