/*
	Copyright (c) 2004-2008, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["dojoaddons.FormBuilder.PropertiesDialog"]){dojo._hasResource["dojoaddons.FormBuilder.PropertiesDialog"]=true;dojo.provide("dojoaddons.FormBuilder.PropertiesDialog");dojo.require("dijit._Widget");dojo.require("dijit._Templated");dojo.require("dijit.InlineEditBox");dojo.require("dijit.form.TextBox");dojo.require("dojoaddons.ShadowDialog");dojo.require("dojoaddons.FormBuilder.Language");dojo.declare("dojoaddons.FormBuilder.PropertiesDialog",[dijit._Widget],{currentDijit:null,dialog:null,inputType:"",propsBackup:null,currentLang:"",postCreate:function(){this.lang=new dojoaddons.FormBuilder.Language();dojo.subscribe("PropertiesCalled",this,"showProps");dojo.subscribe("propertiesDailogSave",this,"_onDialogSave");dojo.subscribe("propertiesDailogCancel",this,"_onDialogCancel");dojo.subscribe("addButtonToGroup",this,"_onAddButtonToGroup");dojo.subscribe("deleteButtonFromGroup",this,"_onDeleteButtonFromGroup");dojo.subscribe("switchDialogTabs",this,"_onSwitchDialogTabs");dojo.subscribe("changePreselection",this,"_onChangePreselection");var _1=dojo.doc.createElement("div");dojo.body().appendChild(_1);this.dialog=new dojoaddons.ShadowDialog({title:this.lang.get("properties")},_1);this.dialog.onCancel=dojo.hitch(this,"_onDialogCancel");this.dialog.startup();},showProps:function(_2){this.inputType="default";this.currentDijit=null;this.currentDijit=_2;this.propsBackup=dojo.clone(this.currentDijit.props);this.currentLang=this.currentDijit.languages[0];var _3=this["render"+this.currentDijit.props.Type.dijit+"Dialog"](this.currentDijit.props,this.currentDijit.languages)+this.renderDialogButtons();this.dialog.setContent(_3);this.dialog.titleNode.innerHTML=this.lang.get(this.currentDijit.props.Type.dijit)+" "+this.lang.get("properties");this.dialog.show();},renderDialogButtons:function(){var _4="<div id=\"dialogMsgBox\"></div><div class=\"dialogButtonBar\"><button class=\"ShadowDialogSaveButton\" onclick=\"dojo.publish('propertiesDailogSave');\">"+this.lang.get("save")+"</button><button class=\"ShadowDialogCancelButton\" onclick=\"dojo.publish('propertiesDailogCancel');\">"+this.lang.get("cancel")+"</button></div>";return _4;},renderButtonInputs:function(_5,_6){return "<div class=\"dialogButtonsBox\"><h1>"+this.lang.get("buttons")+"</h1><div class=\"inputWrapperBox dialogButtonSet\">"+this.renderButtonInputsTable(_5,_6)+this.renderAddButton()+"</div></div>";},renderButtonInputsTable:function(_7,_8){var _9="<table>";for(var i=0;i<_7.length;i++){_9+=this.renderButtonInputRow(i,_8,_7[i]);}return _9+"</table>";},renderButtonInputRow:function(i,_c,_d){return "<tr class=\"buttonInputRow_"+i+"\"><td><input type=\"text\" class=\"dialogItemInput button_"+i+"\" name=\"button_"+i+"_"+_c+"\" value=\""+_d+"\" /></td><td><div class=\"formButtonDelete\" onclick=\"dojo.publish('deleteButtonFromGroup',["+i+"]);\" title=\""+this.lang.get("deleteButtonTitle")+"\"><span>x</span></div></td>"+(this.inputType=="checkboxGroup"?this.renderButtonPreselectInput(i):"")+"</tr>";},renderButtonPreselectInput:function(nr){if(!this.currentDijit.props.preselection[nr]){this.currentDijit.props.preselection[nr]=false;}return "<td>preselected <input class=\"buttonPreselection_"+nr+"\" type=\"checkbox\" "+(this.currentDijit.props.preselection[nr]?" checked=\"checked\" ":"")+" onclick=\"dojo.publish('changePreselection',["+nr+",this.checked]);\" /></td>";},renderAddButton:function(){return "<div class=\"formButtonAdd\" onclick=\"dojo.publish('addButtonToGroup');\" title=\""+this.lang.get("addButtonTitle")+"\" ><span>+</span></div>";},renderTextInputDialog:function(_f,_10){var _11="";for(var i=0;i<_10.length;i++){var _13=_10[i];var _14=(i===0?"":"dialogInactiveTabPane");_11+="<div id=\"dialogLangTab_"+_13+"\" class=\"dialogTabPane "+_14+"\">";_11+=this.renderHeadlineBox("label_"+_13,_f["label_"+_13]);_11+=this.renderDescriptionBox("addText_"+_13,_f["addText_"+_13]);_11+="<h1>"+this.lang.get("defaultValue")+"</h1><div class=\"inputWrapperBox\"><table><tr><td class=\"dialogLabel\">"+this.lang.get("defaultValueLabel")+"</td><td class=\"dialogInput\"><input type=\"text\" class=\"dialogItemInput\" name=\"value_"+_13+"\" value=\""+(_f["value_"+_13]||this.lang.get("startupDefaultValue"))+"\" /></td></tr></table></div>";_11+="</div>";}_11+=this.renderRequiredBox(_f.required);if(_10.length>1){_11=this.renderLangTabs(_10)+_11;}return _11;},renderFreeTextDialog:function(_15,_16){var _17="";for(var i=0;i<_16.length;i++){var _19=_16[i];var _1a=(i===0?"":"dialogInactiveTabPane");_17+="<div id=\"dialogLangTab_"+_19+"\" class=\"dialogTabPane "+_1a+"\">";_17+="<h1>"+this.lang.get("freeText")+"</h1><div class=\"inputWrapperBox\"><table><tr><td class=\"dialogLabel\">"+this.lang.get("freeTextLabel")+"</td><td class=\"dialogInput\"><textarea class=\"dialogItemInput\" name=\"text_"+_19+"\">"+(_15["text_"+_19]||this.lang.get("startupFreeText"))+"</textarea></td></tr></table></div>";_17+="</div>";}if(_16.length>1){_17=this.renderLangTabs(_16)+_17;}return _17;},renderTextFieldDialog:function(_1b,_1c){var _1d="";for(var i=0;i<_1c.length;i++){var _1f=_1c[i];var _20=(i===0?"":"dialogInactiveTabPane");_1d+="<div id=\"dialogLangTab_"+_1f+"\" class=\"dialogTabPane "+_20+"\">";_1d+=this.renderHeadlineBox("label_"+_1f,_1b["label_"+_1f]);_1d+=this.renderDescriptionBox("addText_"+_1f,_1b["addText_"+_1f]);_1d+="<h1>"+this.lang.get("defaultValue")+"</h1><div class=\"inputWrapperBox\"><table><tr><td class=\"dialogLabel\">"+this.lang.get("defaultValueLabel")+"</td><td class=\"dialogInput\"><input type=\"text\" class=\"dialogItemInput\" name=\"value_"+_1f+"\" value=\""+(_1b["value_"+_1f]||this.lang.get("startupDefaultValue"))+"\" /></td></tr></table></div>";_1d+="</div>";}_1d+=this.renderRequiredBox(_1b.required);if(_1c.length>1){_1d=this.renderLangTabs(_1c)+_1d;}return _1d;},renderRadioGroupDialog:function(_21,_22){this.inputType="radioGroup";var _23="";for(var i=0;i<_22.length;i++){var _25=_22[i];var _26=(i===0?"":"dialogInactiveTabPane");_23+="<div id=\"dialogLangTab_"+_25+"\" class=\"dialogTabPane "+_26+"\">";_23+=this.renderHeadlineBox("label_"+_25,_21["label_"+_25]);_23+=this.renderDescriptionBox("addText_"+_25,_21["addText_"+_25]);if(_21["buttons_"+_25]===undefined){_21["buttons_"+_25]=dojo.clone(_21["buttons_"+_22[0]]);this.currentDijit.props["buttons_"+_25]=_21["buttons_"+_25];}_23+=this.renderButtonInputs(_21["buttons_"+_25],_25);_23+="</div>";}_23+=this.renderRequiredBox(_21.required);if(_22.length>1){_23=this.renderLangTabs(_22)+_23;}return _23;},renderCheckboxGroupDialog:function(_27,_28){this.inputType="checkboxGroup";var _29="";for(var i=0;i<_28.length;i++){var _2b=_28[i];var _2c=(i===0?"":"dialogInactiveTabPane");_29+="<div id=\"dialogLangTab_"+_2b+"\" class=\"dialogTabPane "+_2c+"\">";_29+=this.renderHeadlineBox("label_"+_2b,_27["label_"+_2b]);_29+=this.renderDescriptionBox("addText_"+_2b,_27["addText_"+_2b]);if(_27["buttons_"+_2b]===undefined){_27["buttons_"+_2b]=dojo.clone(_27["buttons_"+_28[0]]);this.currentDijit.props["buttons_"+_2b]=_27["buttons_"+_2b];}_29+=this.renderButtonInputs(_27["buttons_"+_2b],_2b);_29+="</div>";}_29+=this.renderRequiredBox(_27.required);if(_28.length>1){_29=this.renderLangTabs(_28)+_29;}return _29;},renderLangTabs:function(_2d){var _2e="<div id=\"dialogTabs\">";for(var i=0;i<_2d.length;i++){var _30=(i===0?"activeDialogTab":"");_2e+="<div class=\"dialogTab "+_30+"\" id=\"dialogTab_"+_2d[i]+"\" onclick=\"dojo.publish('switchDialogTabs',['"+_2d[i]+"'])\">"+this.lang.get(_2d[i])+"</div>";}return _2e+"</div>";},renderHeadlineBox:function(_31,_32){_32=_32||this.lang.get("startupLabel");return "<div class=\"dialogHeadlineBox\"><h1>"+this.lang.get("headline")+"</h1><div class=\"inputWrapperBox\"><table><tr><td class=\"dialogLabel\">"+this.lang.get("headlineLabel")+"</td><td class=\"dialogInput\"><input type=\"text\" class=\"dialogItemInput\" name=\""+_31+"\" value=\""+_32+"\" /></td></tr></table></div></div>";},renderDescriptionBox:function(_33,_34){_34=_34||this.lang.get("startupDescription");return "<div class=\"dialogDescriptionBox\"><h1>"+this.lang.get("addText")+"</h1><div class=\"inputWrapperBox\"><table><tr><td class=\"dialogLabel\">"+this.lang.get("addTextLabel")+"</td><td class=\"dialogInput\"><textarea class=\"dialogItemInput\" name=\""+_33+"\">"+_34+"</textarea></td></tr></table></div></div>";},renderRequiredBox:function(_35){return "<div class=\"dialogOptionsBox\"><h1>"+this.lang.get("required")+"</h1><div class=\"inputWrapperBox\">"+this.lang.get("requiredLabel")+"<input class=\"dialogItemInput\" name=\"required\" type=\"checkbox\" "+(_35?"checked=\"checked\" ":"")+"/></div></div>";},htmlentities:function(_36){var _37="ENT_QUOTES";_36=_36.toString();_36=_36.replace(/&/g,"&amp;");_36=_36.replace(/</g,"&lt;");_36=_36.replace(/>/g,"&gt;");_36=_36.replace(/"/g,"&quot;");_36=_36.replace(/'/g,"&#039;");return _36;},_onChangePreselection:function(nr,_39){this.currentDijit.props.preselection[nr]=_39;dojo.query(".buttonPreselection_"+nr).forEach(function(_3a){_3a.checked=_39?"checked":"";});},_onAddButtonToGroup:function(){var _3b=dojo.mixin(this.currentDijit.props,{});var _3c=this.currentDijit.languages;for(var i=0;i<_3c.length;i++){_3b["buttons_"+_3c[i]]=[];dojo.query("#dialogLangTab_"+_3c[i]+" .dialogButtonSet .dialogItemInput").forEach(function(_3e){_3b["buttons_"+_3c[i]].push(_3e.value);});_3b["buttons_"+_3c[i]].push(this.lang.get("newButton"));var box=dojo.query("#dialogLangTab_"+_3c[i]+" .dialogButtonSet")[0];box.innerHTML=this.renderButtonInputsTable(_3b["buttons_"+_3c[i]],_3c[i])+this.renderAddButton();}},_onDeleteButtonFromGroup:function(nr){var _41=dojo.mixin(this.currentDijit.props,{});var _42=this.currentDijit.languages;var _43={};var _44=this.currentDijit.props["buttons_"+_42[0]].length;for(var a=0;a<_44;a++){if(a<nr){_43[a]=_41.preselection[a];}if(a>nr){_43[a-1]=_41.preselection[a];}}_41.preselection=_43;for(var i=0;i<_42.length;i++){var _47=[];dojo.query("#dialogLangTab_"+_42[i]+" .dialogButtonSet .dialogItemInput").forEach(function(_48){if(!dojo.hasClass(_48,"button_"+nr)){_47.push(_48.value);}});_41["buttons_"+_42[i]]=_47;var box=dojo.query("#dialogLangTab_"+_42[i]+" .dialogButtonSet")[0];box.innerHTML=this.renderButtonInputsTable(_47,_42[i])+this.renderAddButton();}},_onSwitchDialogTabs:function(_4a){this.currentLang=_4a;for(var i=0;i<this.currentDijit.languages.length;i++){var _4c=this.currentDijit.languages[i];var _4d=dojo.byId("dialogLangTab_"+_4c);var tab=dojo.byId("dialogTab_"+_4c);if(_4a==_4c){dojo.removeClass(_4d,"dialogInactiveTabPane");dojo.addClass(tab,"activeDialogTab");}else{dojo.addClass(_4d,"dialogInactiveTabPane");dojo.removeClass(tab,"activeDialogTab");}}},_onDialogSave:function(){var _4f=this;var _50={};var _51=null;dojo.query(".dialogItemInput").forEach(function(_52){if(_52.type=="checkbox"){_50[_52.name]=_52.checked;}else{_50[_52.name]=_4f.htmlentities(_52.value);}});if(this.inputType=="checkboxGroup"||this.inputType=="radioGroup"){var _53={};for(_51 in _50){var _54=_51.split("_");if(_54[0]=="button"){var _55=_54[2];if(!_53["buttons_"+_55]){_53["buttons_"+_55]=[];}_53["buttons_"+_55][_54[1]]=_4f.htmlentities(_50[_51]);}else{_53[_51]=_4f.htmlentities(_50[_51]);}}_50=_53;var _56=_50["buttons_"+this.currentDijit.languages[0]]?_50["buttons_"+this.currentDijit.languages[0]].length:0;var min=(this.inputType=="radioGroup"?2:1);if(_56<min){dojo.byId("dialogMsgBox").innerHTML=this.inputType=="radioGroup"?this.lang.get("notEnoughRadiobuttons"):this.lang.get("notEnoughCheckboxes");return false;}}for(_51 in _50){this.currentDijit.setSingleProp(_51,_50[_51]);}this.currentDijit.updateHTML();this.dialog.hide();dojo.publish("saveWorkspace");},_onDialogCancel:function(){this.currentDijit.props=this.propsBackup;this.dialog.hide();}});}