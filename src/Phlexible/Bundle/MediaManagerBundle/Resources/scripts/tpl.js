Ext.namespace('Phlexible.mediamanager.templates');

Phlexible.mediamanager.templates.StartGroup = new Ext.XTemplate(
    '<div id="{groupId}" class="x-grid-group {cls}">',
    '<div id="{groupId}-hd" class="x-grid-group-hd" style="{style}"><div>{text}</div></div>',
    '<div id="{groupId}-bd" class="x-grid-group-body">'
);

Phlexible.mediamanager.templates.UsedString =
    '{[Phlexible.mediamanager.Bullets.getWithTrailingSpace(values.record.data)]}'
    /*
     +
     '<tpl if="!values.record.data.present">' +
     '<img src="' + Phlexible.bundleAsset('/mediamanager/images/bullet_cross.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     '</tpl>' +
     '<tpl if="values.record.data.usageStatus&8">' +
     '<img src="' + Phlexible.bundleAsset('/mediamanager/images/bullet_green.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     '</tpl>' +
     '<tpl if="values.record.data.usageStatus&4">' +
     '<img src="' + Phlexible.bundleAsset('/mediamanager/images/bullet_yellow.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     '</tpl>' +
     '<tpl if="values.record.data.usageStatus&2">' +
     '<img src="' + Phlexible.bundleAsset('/mediamanager/images/bullet_gray.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     '</tpl>' +
     '<tpl if="values.record.data.usageStatus&1">' +
     '<img src="' + Phlexible.bundleAsset('/mediamanager/images/bullet_black.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     '</tpl>' +
     '<tpl if="values.record.data.usageStatus"> </tpl>' +
     '<tpl if="values.record.data.focal">' +
     '<img src="' + Phlexible.bundleAsset('/focalpoint/images/bullet_focal.gif')+'" width="8" height="12" style="vertical-align: middle;" />' +
     ' ' +
     '</tpl>'
     */
;

Phlexible.mediamanager.templates.NameString =
    '<tpl if="values.record.data.hidden"><span style="text-decoration: line-through;"></tpl>' +
        '{values.record.data.name}' +
        '<tpl if="values.record.data.hidden"></span></tpl>';

Phlexible.mediamanager.templates.DragSingle = new Ext.XTemplate(
    '<div class="p-dragsingleThumbnails">',
    '<img class="thumb" src="{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values[0].data.id, template_key: \"_mm_large\"})]}" />',
    '</div>'
);

Phlexible.mediamanager.templates.DragMulti = new Ext.XTemplate(
    '<div class="p-dragmultiThumbnails">',
    '<tpl for="values">',
    '<img class="thumb" src="{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.data.id, template_key: \"_mm_medium\"})]}" />',
    '</tpl>',
    '</div>'
);

