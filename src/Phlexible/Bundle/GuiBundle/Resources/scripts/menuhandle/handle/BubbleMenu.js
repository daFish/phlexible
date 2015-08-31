Ext.define('Phlexible.gui.menuhandle.handle.BubbleMenu', {
    extend: 'Phlexible.gui.menuhandle.handle.Menu',

    createConfig: function (data) {
        if (data.children && Ext.isArray(data.children) && data.children.length === 1) {
            var handleName, handler;

            if (!Phlexible.Handles.has(data.children[0].handle)) {
                Phlexible.Logger.error('Invalid handle in:', data.children[0]);
                return;
            }

            handleName = Phlexible.Handles.get(data.children[0].handle);
            if (Ext.isFunction(handleName)) {
                handler = handleName();
            } else {
                handler = Ext.create(handleName);
            }

            if (data.children[0].parameters) {
                handler.setParameters(data.children[0].parameters);
            }

            return handler.createConfig(data.children[0]);
        }

        return this.callParent([data]);
    }
});
