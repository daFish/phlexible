Ext.define('Phlexible.mediamanager.RenameFileWindow', {
    extend: 'Phlexible.gui.util.Dialog',

    title: Phlexible.mediamanager.Strings.rename_file,
    width: 400,
    minWidth: 400,
    height: 220,
    minHeight: 220,
    iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),

    textHeader: Phlexible.mediamanager.Strings.rename_file_header,
    textDescription: Phlexible.mediamanager.Strings.rename_file_desc,
    textOk: Phlexible.mediamanager.Strings.rename,
    textCancel: Phlexible.mediamanager.Strings.cancel,

    extraCls: 'p-mediamanager-renamefile',

    labelWidth: 70,

    formItems: [
        {
            fieldLabel: Phlexible.mediamanager.Strings.name,
            name: 'name',
            msgTarget: 'under'
        }
    ],

    /**
     * Return the Submit URL
     * @return string
     */
    getSubmitUrl: function () {
        return Phlexible.Router.generate('mediamanager_file_rename');
    }
});
