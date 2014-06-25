/*jsl:ignoreall*/
/*(c) Copyright 2008 Licensed under LGPL3. See details below*/

Ext.override(Array, {
    findBy: function(fn) {
        var result = [];
        for (var i = 0, len = this.length; i < len; i++){
            if(fn.call(this || scope, this[i])) {
                result.push(this[i]);
            }
        }
        if (result.length != 0) return result;
    }
});

//Ext.override(String, {
//    endsWith: function(s) {
//        return this.substring(this.length - s.length) == s;
//    }
//});

/**
 * Ext.ux.Sound
 * <br>
 * <br>See the following for a <a href="http://extjs-ux.org/repo/authors/jerrybrown5/trunk/Ext/ux/Sound/Demo.html">demo</a>
 * <br>
 * <br> Example Usage
 * <pre>
 *     Ext.ux.sound.play('http://example.com/track.mp3');
 * </pre>
 * <br>Forum thread: <a href="http://extjs.com/forum/showthread.php?t=47672">http://extjs.com/forum/showthread.php?t=47672</a>
 * @singleton
 * @class Ext.ux.Sound
 * @author Nigel White aka Animal; released and published by Jerry Brown
 * @license Ext.ux.Sound is licensed under the terms of
 * the Open Source <a href="http://www.gnu.org/licenses/lgpl.html">LGPL 3.0 license</a>.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 * @version 0.2
 */
Ext.namespace('Ext.ux');

Ext.ux.Sound = (function(){

    var hasFlash = (navigator.plugins && Array.prototype.findBy.call(navigator.plugins, function(p){return p.name.indexOf('Flash')!==-1;}));

//  Disabled if Windows Gecko without the QuickTime plugin
    var  FFWin = (Ext.isGecko && Ext.isWindows), enabled = !FFWin || (navigator.plugins && Array.prototype.findBy.call(navigator.plugins, function(p){return p.name.indexOf('QuickTime')!==-1;}));
    if (!enabled) {
        return {
            enable: Ext.emptyFn,
            disable: Ext.emptyFn,
            play: Ext.emptyFn
        };
    }

    var tracks = {};

    return {

    /**
     * @cfg enable Enables sound playback if playback is possible for the browser.
     */
        enable: function(){
            enabled = true;
        },

    /**
     * @cfg disable Disables sound playback
     */
        disable: function(){
            enabled = false;
        },

        /**
         * @cfg play Plays a sound.
         *
         * @param url {String} The URL of the sound track.
         * @param options {Object} A config object which may contain the following options:<ul class="mdetail-params">
         * <li><b>track</b> : String
         *  <div class="mdesc">The name of the track. (defaults to 'global')</div></li>
         * <li><b>replace</b> : Boolean
         *  <div class="mdesc">True to stop any previously started playback of the named track.</div></li>
         * </ul>
         */
        play: function(url, options){
            if(!enabled) return;

            var options = Ext.apply({
              track: 'global',
              url: url,
              replace: false
            }, options);

            if(options.replace && tracks[options.track]) {
                for (var i = 0; i <= tracks[options.track].id; i++) {
                    var sound = Ext.get('sound_' + options.track + '_' + i);
                    sound.dom.Stop && sound.dom.Stop();
                    sound.remove();
                }
                tracks[options.track] = null;
            }

            if(tracks[options.track]) {
                tracks[options.track].id++;
            } else {
                tracks[options.track] = { id: 0 }
            }

            options.id = tracks[options.track].id;
            var sound;

//          Flash not working yet.
            if (options.url.match(/\.swf$/) && hasFlash) {
                var objectId = 'sound_' + options.track + '_' + options.id;
                var SWFconfig = {
                  tag: 'object',
                  cls: 'x-hide-offsets',
                  cn: [
                      {tag: 'embed',
                          src: options.url,
                          type: 'application/x-shockwave-flash',
                          quality: 'high'
                      },
                      {tag: 'param', name: 'quality', value: 'high'},
                      {tag: 'param', name: 'movie', value: options.url}
                    ]
                };

                if (Ext.isIE) {
                    SWFconfig.classid = "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000";
                    SWFconfig.codebase = "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"
                    SWFconfig.id = objectId;
                } else {
                    SWFconfig.cn[0].id = objectId;
                }

                Ext.DomHelper.useDom = true;
                Ext.getBody().createChild(SWFconfig, null, true);
                Ext.getDom(objectId).Play();
                Ext.DomHelper.useDom = false;
                return;
            } else if (Ext.isIE) {
                sound = document.createElement('bgsound');
                sound.setAttribute('src',options.url);
                sound.setAttribute('loop','1');
                sound.setAttribute('autostart','true');
            } else if (FFWin && !options.url.match(/\.wav$/)) {
                sound = document.createElement('object');
                sound.setAttribute('type','audio/mpeg');
                sound.setAttribute('data',options.url);
            } else {
                sound = document.createElement('embed');
                sound.setAttribute('src',options.url);
                sound.setAttribute('hidden','true');
                sound.setAttribute('loop','false');
                sound.setAttribute('autostart','true');
            }
            sound.className = 'x-hide-offsets';
            sound.setAttribute('id','sound_'+options.track+'_'+options.id);
            document.body.appendChild(sound);
        }
    };
})();

/**
 * Ext.ux.ToastWindow
 *
 * @author  Edouard Fattal
 * @date	March 14, 2008
 *
 * @class Ext.ux.ToastWindow
 * @extends Ext.Window
 */

Ext.namespace("Ext.ux");


Ext.ux.NotificationMgr = {
    positions: []
};

Ext.ux.Notification = Ext.extend(Ext.Window, {
    initComponent: function(){
        Ext.apply(this, {
            iconCls: this.iconCls || 'x-icon-information',
            cls: 'x-notification',
            width: this.width || 200,
            height: this.height || 100,
            autoHeight: true,
            plain: false,
            draggable: false,
            resizable: false,
            shadow: false,
            bodyStyle: this.bodyStyle || 'text-align:center'
        });
        if(this.autoDestroy) {
            this.task = new Ext.util.DelayedTask(this.hide, this);
        } else {
            this.cls += ' fixed';
            this.closable = true;
        }
        Ext.ux.Notification.superclass.initComponent.call(this);
    },
    setMessage: function(msg){
        this.body.update(msg);
    },
    setTitle: function(title, iconCls){
        Ext.ux.Notification.superclass.setTitle.call(this, title, iconCls||this.iconCls);
    },
    onRender:function(ct, position) {
        Ext.ux.Notification.superclass.onRender.call(this, ct, position);
    },
    onDestroy: function(){
        Ext.ux.NotificationMgr.positions.remove(this.pos);
        Ext.ux.Notification.superclass.onDestroy.call(this);
    },
    cancelHiding: function(){
        if(this.autoDestroy) {
            this.addClass('fixed');
            this.task.cancel();
        }
    },
    afterShow: function(){
        Ext.ux.Notification.superclass.afterShow.call(this);
        Ext.fly(this.body.dom).on('click', this.cancelHiding, this);
        if(this.autoDestroy) {
            this.task.delay(this.hideDelay || 5000);
       }
    },
    animShow: function(){
        this.pos = 0;
        while(Ext.ux.NotificationMgr.positions.indexOf(this.pos)>-1)
            this.pos++;
        Ext.ux.NotificationMgr.positions.push(this.pos);
        this.setSize(this.width,this.height);
        this.el.alignTo(document, "br-br", [ -10, -40-((this.getSize().height+10)*this.pos) ]);
        this.el.slideIn('b', {
            duration: 1,
            callback: this.afterShow,
            scope: this
        });
    },
    animHide: function(){
           Ext.ux.NotificationMgr.positions.remove(this.pos);
        this.el.ghost("b", {
            duration: 1,
            remove: true
        });
    },

    focus: Ext.emptyFn

});
