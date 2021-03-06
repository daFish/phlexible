Ext.define('Phlexible.mediamanager.util.Bullets', {
    extend: 'Ext.util.Observable',

    bullets: '',

    presentBullet: function (values) {
        if (!values.present) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_cross.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        }
        return '';
    },
    usageBullet: function (values) {
        if (!values.usageStatus) {
            return '';
        }
        if (values.usageStatus && 8) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_green.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        } else if (values.usageStatus && 4) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_yellow.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        } else if (values.usageStatus && 2) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_gray.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        } else if (values.usageStatus && 1) {
            return '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_black.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
        }
        return '';
    },
    buildBullets: function (values) {
        var bullets = '';
        bullets += this.presentBullet(values);
        bullets += this.usageBullet(values);
        return bullets;
    },
    getWithTrailingSpace: function (values) {
        var bullets = this.buildBullets(values);
        if (bullets) {
            bullets += ' ';
        }
        return bullets;
    }
});