/*
	Copyright (c) 2004-2008, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["dojoaddons.FormBuilder.Workspace"]){dojo._hasResource["dojoaddons.FormBuilder.Workspace"]=true;dojo.provide("dojoaddons.FormBuilder.Workspace");dojo.require("dijit._Widget");dojo.require("dijit._Templated");dojo.require("dojo.dnd.Source");dojo.require("dojoaddons.FormBuilder.Language");dojo.declare("dojoaddons.FormBuilder.Workspace",[dijit._Widget,dijit._Templated],{templateString:"<div dojoAttachPoint=\"workspace\"><div id=\"dragSpace\"></div></div>",dragSource:null,items:[],topics:null,languages:["de"],postCreate:function(){this.startSubscriptions();this.lang=new dojoaddons.FormBuilder.Language();},startSubscriptions:function(){var _1=this;this.topics=[dojo.subscribe("FormItemClicked",_1,"addItem"),dojo.subscribe("/dnd/drop",_1,"onDndEnd")];dojo.subscribe("saveWorkspace",this,"saveState");dojo.subscribe("deleteItem",this,"_onDeleteItem");},addItem:function(_2){var _3=_2.formType||_2.getAttribute("formType");var _4=this.buildItem(_3);this.items.push(_4);this.rebuildWorkspace();},_onDeleteItem:function(_5){_5.destroy();this.saveSortOrder();this.rebuildWorkspace();},buildItem:function(_6){var _7=null;var _8={languages:this.languages};if(_6=="TextInput"){_8["label_"+this.languages[0]]=this.lang.get("startupLabel");_8["addText_"+this.languages[0]]=this.lang.get("startupDescription");_8["value_"+this.languages[0]]=this.lang.get("startupDefaultValue");_7=new dojoaddons.FormBuilder.TextInput({"options":_8});}else{if(_6=="FreeText"){_8["text_"+this.languages[0]]=this.lang.get("startupFreeText");_7=new dojoaddons.FormBuilder.FreeText({"options":_8});}else{if(_6=="TextField"){_8["label_"+this.languages[0]]=this.lang.get("startupLabel");_8["addText_"+this.languages[0]]=this.lang.get("startupDescription");_8["value_"+this.languages[0]]=this.lang.get("startupDefaultValue");_7=new dojoaddons.FormBuilder.TextField({"options":_8});}else{if(_6=="RadioGroup"){_8["label_"+this.languages[0]]=this.lang.get("startupLabel");_8["addText_"+this.languages[0]]=this.lang.get("startupDescription");_8["buttons_"+this.languages[0]]=[this.lang.get("startupButton1"),this.lang.get("startupButton2")];_7=new dojoaddons.FormBuilder.RadioGroup({"options":_8});}else{if(_6=="CheckboxGroup"){_8["label_"+this.languages[0]]=this.lang.get("startupLabel");_8["addText_"+this.languages[0]]=this.lang.get("startupDescription");_8["buttons_"+this.languages[0]]=[this.lang.get("startupButton1"),this.lang.get("startupButton2")];_7=new dojoaddons.FormBuilder.CheckboxGroup({"options":_8});}}}}}return _7;},rebuildWorkspace:function(){this.workspace.removeChild(dojo.byId("dragSpace"));if(this.dragSource){this.dragSource.destroy();}var _9=document.createElement("div");_9.id="dragSpace";dojo.forEach(this.items,function(_a){_9.appendChild(_a.domNode);});this.workspace.appendChild(_9);this.dragSource=new dojo.dnd.Source("dragSpace");},findDijit:function(_b){for(_b=_b;!dojo.hasClass(_b,"dojoDndItem");_b=_b.parentNode){}return dijit.byId(_b.id);},onDndEnd:function(){console.log("dnd ended...");window.setTimeout(dojo.hitch(this,"saveSortOrder"),200);},saveSortOrder:function(){console.log("sorting...");var _c=[];var _d=this;dojo.query("#dragSpace div").forEach(function(_e){if(dojo.hasClass(_e,"dojoDndItem")){_c.push(_d.getDijitById(_e.id));}});this.items=_c;this.saveState();},getDijitById:function(id){var _10=null;dojo.forEach(this.items,function(_11){if(_11.id==id){_10=_11;}});return _10;},saveState:function(){var _12=[];dojo.forEach(this.items,function(_13){_12.push(_13.props);});console.dir(_12);console.log(dojo.toJson(_12));var _14={};_14.form_name="Testformular";_14.form_id=1;_14.form_structure=_12;var _15=dojo.toJson(_14);var qs=tools.toQueryString(_14);dojo.rawXhrPost({url:"/ajax_post_test.php",postData:qs,timeout:1000,handleAs:"text"});return _12;},restoreState:function(_17){console.log("restoring...");var _18=[{"Type":{"name":"Text input","dijit":"TextInput"},"multilanguage":true,"languages":["de","en"],"label_de":"Name","addText_de":"Type your name here:","value_de":"","label_en":"Name","addText_en":"geben Sie hier Ihren Namen ein:","value_en":""},{"Type":{"name":"Radio Group","dijit":"RadioGroup"},"multilanguage":true,"languages":["de","en"],"label_de":"Gender","addText_de":"specify your gender:","buttons_de":["male","female"],"buttons_en":["m�nnlich","weiblich"],"label_en":"Geschlecht","addText_en":"Geben Sie hier Ihr Geschlecht an:"},{"Type":{"name":"Free Text","dijit":"FreeText"},"multilanguage":true,"languages":["de","en"],"text_de":"Done, thank you!","text_en":"Fertig, Danke!"}];var x2=[{"Type":{"name":"Checkbox Group","dijit":"CheckboxGroup"},"required":false,"preselection":{"0":false,"1":false},"languages":["de","en"],"label_de":"keine Pflicht","addText_de":" ","buttons_de":["Auswahl 1","Auswahl 2"],"buttons_en":["Auswahl 1","Auswahl 2"],"label_en":"undefined","addText_en":"undefined"},{"Type":{"name":"Checkbox Group","dijit":"CheckboxGroup"},"required":true,"preselection":{"0":true,"1":false},"languages":["de","en"],"label_de":"Label","addText_de":" ","buttons_de":["Auswahl 1 (pre)","Auswahl 2"],"buttons_en":["Auswahl 1","Auswahl 2"],"label_en":"undefined","addText_en":"undefined"},{"Type":{"name":"Radio Group","dijit":"RadioGroup"},"required":true,"languages":["de","en"],"label_de":"Label","addText_de":" ","buttons_de":["Auswahl 1","Auswahl 2","Auswahl 3"],"buttons_en":["Auswahl 1","Auswahl 2","Neue Auswahl"],"label_en":"undefined","addText_en":"undefined"},{"Type":{"name":"Free Text","dijit":"FreeText"},"languages":["de","en"],"text_de":"Freier Text"},{"Type":{"name":"Textfield","dijit":"TextField"},"required":false,"languages":["de","en"],"label_de":"Label","addText_de":" ","value_de":" ","label_en":"undefined","addText_en":"undefined","value_en":"undefined"}];this.items=[];var _1a=_17||x2;console.log("data recieved:");console.dir(_1a);for(var i=0;i<_1a.length;i++){console.log("building item #"+i);var _1c={};for(var a in _1a[i]){if(a!="Type"){_1c[a]=_1a[i][a];}}console.log("created options:");console.log(_1c);var _1e=_1a[i].Type.dijit;console.log(_1e);var _1f=new dojoaddons.FormBuilder[_1e]({"options":_1c});console.log("created item:");console.log(_1f);this.items.push(_1f);}console.log("rebuilding workspace...");this.rebuildWorkspace();}});dojo.declare("dojoaddons.FormBuilder.TextInput",[dijit._Widget,dijit._Templated],{templateString:"<div class=\"dojoDndItem\"><h1 dojoAttachPoint=\"labelNode\"></h1><h2 dojoAttachPoint=\"descriptionNode\"></h2><div class=\"textinput\" dojoAttachPoint=\"inputNode\"></div><div class=\"formItemButtons\"><div class=\"formItemEditButton\" dojoattachevent=\"onclick:_onEdit\"><span>E</span></div><div class=\"formItemDeleteButton\" dojoattachevent=\"onclick:_onDelete\"><span>X</span></div></div><div class=\"clearer\"></div></div>",templateString:"<div class=\"dojoDndItem\"><table><tr><td><h1 dojoAttachPoint=\"labelNode\"></h1><h2 dojoAttachPoint=\"descriptionNode\"></h2></td><td><div class=\"textinput\" dojoAttachPoint=\"inputNode\"></div></td><td><div class=\"formItemButtons\"><div class=\"formItemEditButton\" dojoattachevent=\"onclick:_onEdit\"><span>E</span></div><div class=\"formItemDeleteButton\" dojoattachevent=\"onclick:_onDelete\"><span>X</span></div></div></td></tr></table></div></div>",options:{},props:{"Type":{"name":"Text input","dijit":"TextInput"},"required":false},languages:["de"],postCreate:function(){this.multilanguage=this.options.multilanguage;this.languages=this.options.languages;this.props=dojo.mixin(this.props,this.options);this.updateHTML();},setSingleProp:function(_20,_21){this.props[_20]=_21;},setAllProps:function(_22){for(var _23 in _22){this.setSingleProp(_23,_22[_23]);}},updateHTML:function(){this.inputNode.innerHTML=this.props["value_"+this.languages[0]];this.labelNode.innerHTML=this.props["label_"+this.languages[0]];this.descriptionNode.innerHTML=this.props["addText_"+this.languages[0]];},_onDelete:function(){dojo.publish("deleteItem",[this]);},_onEdit:function(){dojo.publish("PropertiesCalled",[this]);}});dojo.declare("dojoaddons.FormBuilder.FreeText",[dijit._Widget,dijit._Templated],{templateString:"<div class=\"dojoDndItem\"><p dojoAttachPoint=\"textNode\"></p><div class=\"formItemButtons\"><div class=\"formItemEditButton\" dojoattachevent=\"onclick:_onEdit\"><span>E</span></div><div class=\"formItemDeleteButton\" dojoattachevent=\"onclick:_onDelete\"><span>X</span></div></div><div class=\"clearer\"></div></div>",templateString:"<div class=\"dojoDndItem\"><table><td><p dojoAttachPoint=\"textNode\"></p></td><td><div class=\"formItemButtons\"><div class=\"formItemEditButton\" dojoattachevent=\"onclick:_onEdit\"><span>E</span></div><div class=\"formItemDeleteButton\" dojoattachevent=\"onclick:_onDelete\"><span>X</span></div></div></td></tr></table></div>",options:{},props:{"Type":{"name":"Free Text","dijit":"FreeText"}},languages:["de"],postCreate:function(){this.multilanguage=this.options.multilanguage;this.languages=this.options.languages;this.props=dojo.mixin(this.props,this.options);this.updateHTML();},setSingleProp:function(_24,_25){this.props[_24]=_25;},setAllProps:function(_26){for(var _27 in _26){this.setSingleProp(_27,_26[_27]);}},updateHTML:function(){this.textNode.innerHTML=this.props["text_"+this.languages[0]];},_onDelete:function(){dojo.publish("deleteItem",[this]);},_onEdit:function(){dojo.publish("PropertiesCalled",[this]);}});dojo.declare("dojoaddons.FormBuilder.TextField",[dijit._Widget,dijit._Templated],{templateString:"<div class=\"dojoDndItem\"><h1 dojoAttachPoint=\"labelNode\"></h1><h2 dojoAttachPoint=\"descriptionNode\"></h2><div class=\"textarea\" dojoAttachPoint=\"inputNode\"></div><div class=\"formItemButtons\"><div class=\"formItemEditButton\" dojoattachevent=\"onclick:_onEdit\"><span>E</span></div><div class=\"formItemDeleteButton\" dojoattachevent=\"onclick:_onDelete\"><span>X</span></div></div><div class=\"clearer\"></div></div>",templateString:"<div class=\"dojoDndItem\"><table><tr><td><h1 dojoAttachPoint=\"labelNode\"></h1><h2 dojoAttachPoint=\"descriptionNode\"></h2></td><td><div class=\"textarea\" dojoAttachPoint=\"inputNode\"></div></td><td><div class=\"formItemButtons\"><div class=\"formItemEditButton\" dojoattachevent=\"onclick:_onEdit\"><span>E</span></div><div class=\"formItemDeleteButton\" dojoattachevent=\"onclick:_onDelete\"><span>X</span></div></div></td></tr></table></div>",options:{},props:{"Type":{"name":"Textfield","dijit":"TextField"},"required":false},languages:["de"],postCreate:function(){this.multilanguage=this.options.multilanguage;this.languages=this.options.languages;this.props=dojo.mixin(this.props,this.options);this.updateHTML();},setSingleProp:function(_28,_29){this.props[_28]=_29;},setAllProps:function(_2a){for(var _2b in _2a){this.setSingleProp(_2b,_2a[_2b]);}},updateHTML:function(){this.inputNode.innerHTML=this.props["value_"+this.languages[0]];this.labelNode.innerHTML=this.props["label_"+this.languages[0]];this.descriptionNode.innerHTML=this.props["addText_"+this.languages[0]];},_onDelete:function(){dojo.publish("deleteItem",[this]);},_onEdit:function(){dojo.publish("PropertiesCalled",[this]);}});dojo.declare("dojoaddons.FormBuilder.RadioGroup",[dijit._Widget,dijit._Templated],{templateString:"<div class=\"dojoDndItem\"><h1 dojoAttachPoint=\"labelNode\"></h1><h2 dojoAttachPoint=\"descriptionNode\"></h2><div class=\"radioList\" dojoAttachpoint=\"radioListNode\"></div><div class=\"formItemButtons\"><div class=\"formItemEditButton\" dojoattachevent=\"onclick:_onEdit\"><span>E</span></div><div class=\"formItemDeleteButton\" dojoattachevent=\"onclick:_onDelete\"><span>X</span></div></div><div class=\"clearer\"></div></div>",templateString:"<div class=\"dojoDndItem\"><table><tr><td><h1 dojoAttachPoint=\"labelNode\"></h1><h2 dojoAttachPoint=\"descriptionNode\"></h2></td><td><div class=\"radioList\" dojoAttachpoint=\"radioListNode\"></div></td><td><div class=\"formItemButtons\"><div class=\"formItemEditButton\" dojoattachevent=\"onclick:_onEdit\"><span>E</span></div><div class=\"formItemDeleteButton\" dojoattachevent=\"onclick:_onDelete\"><span>X</span></div></div></td></tr></table></div>",text:"",buttons:[],options:{},props:{"Type":{"name":"Radio Group","dijit":"RadioGroup"},"required":false},languages:["de"],postCreate:function(){this.multilanguage=this.options.multilanguage;this.languages=this.options.languages;this.props=dojo.mixin(this.props,this.options);this.updateHTML();},renderRadioList:function(){var _2c="<ul class=\"radioListUL\">";for(var i=0;i<this.props["buttons_"+this.languages[0]].length;i++){_2c+="<li class=\""+i+"\">"+this.props["buttons_"+this.languages[0]][i]+"</li>";}_2c+="</ul>";return _2c;},setSingleProp:function(_2e,_2f){this.props[_2e]=_2f;},setAllProps:function(_30){for(var _31 in _30){this.setSingleProp(_31,_30[_31]);}},updateHTML:function(){this.radioListNode.innerHTML=this.renderRadioList();this.labelNode.innerHTML=this.props["label_"+this.languages[0]];this.descriptionNode.innerHTML=this.props["addText_"+this.languages[0]];},_onDelete:function(){dojo.publish("deleteItem",[this]);},_onEdit:function(){dojo.publish("PropertiesCalled",[this]);}});dojo.declare("dojoaddons.FormBuilder.CheckboxGroup",[dijit._Widget,dijit._Templated],{templateString:"<div class=\"dojoDndItem\"><h1 dojoAttachPoint=\"labelNode\"></h1><h2 dojoAttachPoint=\"descriptionNode\"></h2><div class=\"radioList\" dojoAttachpoint=\"radioListNode\"></div><div class=\"formItemButtons\"><div class=\"formItemEditButton\" dojoattachevent=\"onclick:_onEdit\"><span>E</span></div><div class=\"formItemDeleteButton\" dojoattachevent=\"onclick:_onDelete\"><span>X</span></div></div><div class=\"clearer\"></div></div>",templateString:"<div class=\"dojoDndItem\"><table><tr><td class=\"formBuilderLabelCell\"><h1 dojoAttachPoint=\"labelNode\"></h1><h2 dojoAttachPoint=\"descriptionNode\"></h2></td><td class=\"formBuilderInputCell\"><div class=\"radioList\" dojoAttachpoint=\"radioListNode\"></div></td><td class=\"formBuilderButtonCell\"><div class=\"formItemButtons\"><div class=\"formItemEditButton\" dojoattachevent=\"onclick:_onEdit\"><span>E</span></div><div class=\"formItemDeleteButton\" dojoattachevent=\"onclick:_onDelete\"><span>X</span></div></div></td></tr></table></div>",text:"",buttons:[],options:{},props:{"Type":{"name":"Checkbox Group","dijit":"CheckboxGroup"},"required":false,"preselection":{}},languages:["de"],postCreate:function(){this.multilanguage=this.options.multilanguage;this.languages=this.options.languages;this.props=dojo.mixin(this.props,this.options);this.updateHTML();},renderRadioList:function(){var _32="<ul class=\"checkboxListUL\">";for(var i=0;i<this.props["buttons_"+this.languages[0]].length;i++){_32+="<li class=\""+i+"\">"+this.props["buttons_"+this.languages[0]][i]+"</li>";}_32+="</ul>";return _32;},setSingleProp:function(_34,_35){this.props[_34]=_35;},setAllProps:function(_36){for(var _37 in _36){this.setSingleProp(_37,_36[_37]);}},updateHTML:function(){this.radioListNode.innerHTML=this.renderRadioList();this.labelNode.innerHTML=this.props["label_"+this.languages[0]];this.descriptionNode.innerHTML=this.props["addText_"+this.languages[0]];},_onDelete:function(){dojo.publish("deleteItem",[this]);},_onEdit:function(){dojo.publish("PropertiesCalled",[this]);}});}