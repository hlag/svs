//>>built
define(["../../../.","dojo","dojox","dojo/i18n!../../.././nls/loading","dojo/require!../../.././Tooltip"],function(_1,_2,_3){
_2.provide("dojox.widget.DynamicTooltip");
_2.experimental("dojox.widget.DynamicTooltip");
_2.require("dijit.Tooltip");
_2.requireLocalization("dijit","loading");
_2.declare("dojox.widget.DynamicTooltip",_1.Tooltip,{hasLoaded:false,href:"",label:"",preventCache:false,postMixInProperties:function(){
this.inherited(arguments);
this._setLoadingLabel();
},_setLoadingLabel:function(){
if(this.href){
this.label=_2.i18n.getLocalization("dijit","loading",this.lang).loadingState;
}
},_setHrefAttr:function(_4){
this.href=_4;
this.hasLoaded=false;
},loadContent:function(_5){
if(!this.hasLoaded&&this.href){
this._setLoadingLabel();
this.hasLoaded=true;
_2.xhrGet({url:this.href,handleAs:"text",tooltipWidget:this,load:function(_6,_7){
this.tooltipWidget.label=_6;
this.tooltipWidget.close();
this.tooltipWidget.open(_5);
},preventCache:this.preventCache});
}
},refresh:function(){
this.hasLoaded=false;
},open:function(_8){
_8=_8||(this._connectNodes&&this._connectNodes[0]);
if(!_8){
return;
}
this.loadContent(_8);
this.inherited(arguments);
}});
});
