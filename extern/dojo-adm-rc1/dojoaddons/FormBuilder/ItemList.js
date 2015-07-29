/*
	Copyright (c) 2004-2008, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["dojoaddons.FormBuilder.ItemList"]){dojo._hasResource["dojoaddons.FormBuilder.ItemList"]=true;dojo.provide("dojoaddons.FormBuilder.ItemList");dojo.require("dijit._Widget");dojo.require("dijit._Templated");dojo.require("dojoaddons.FormBuilder.Language");dojo.declare("dojoaddons.FormBuilder.ItemList",[dijit._Widget,dijit._Templated],{templateString:"<div></div>",items:[{"type":"TextInput","label":"Add text input"},{"type":"TextField","label":"Add textfield"},{"type":"FreeText","label":"Add free text"},{"type":"RadioGroup","label":"Add radio buttons"},{"type":"CheckboxGroup","label":"Add checkboxes"}],postCreate:function(){this.lang=new dojoaddons.FormBuilder.Language();this.buildItems();},buildItems:function(){var _1=this;dojo.forEach(this.items,function(_2){var _3=_1._buildItem(_2);dojo.connect(_3,"onclick",_1,"_onclick");_1.domNode.appendChild(_3);});},_buildItem:function(_4){var _5=document.createElement("div");_5.formType=_4.type;_5.innerHTML=_5.title=this.lang.get(_4.type);_5.className=_4.type;return _5;},_onclick:function(_6){dojo.publish("FormItemClicked",[_6.target]);}});}