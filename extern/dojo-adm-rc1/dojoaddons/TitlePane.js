/*
	Copyright (c) 2004-2008, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["dojoaddons.TitlePane"])
{
    dojo._hasResource["dojoaddons.TitlePane"]=true;
    dojo.provide("dojoaddons.TitlePane");
    dojo.require("dijit.TitlePane");
    dojo.require("dijit.Tooltip");
    dojo.declare("dojoaddons.TitlePane",[dijit.TitlePane],
    {
        helpUrl:"",
        helpText:"",
        persist:true,
        tooltip:null,
        templateString:"<div class=\"${baseClass}\"><div dojoAttachEvent=\"onkeypress: _onTitleKey\" tabindex=\"0\" waiRole=\"button\" class=\"dijitTitlePaneTitle\" dojoAttachPoint=\"titleBarNode,focusNode\"><div dojoAttachPoint=\"arrowNode\" class=\"dijitInline dijitArrowNode\"  dojoAttachEvent=\"onclick:toggle,onfocus:_handleFocus,onblur:_handleFocus\"><span dojoAttachPoint=\"arrowNodeInner\" class=\"dijitArrowNodeInner\"></span></div><div dojoAttachPoint=\"helpNode\" class=\"dijitHelpNode\" dojoAttachEvent=\"onclick:callHelp\"><span dojoAttachPoint=\"dijitHelpNodeInner\" class=\"dijitHelpNodeInner\">?</span></div><div dojoAttachPoint=\"titleNode\" class=\"dijitTitlePaneTextNode\"></div></div><div class=\"dijitTitlePaneContentOuter\" dojoAttachPoint=\"hideNode\"><div class=\"dijitReset\" dojoAttachPoint=\"wipeNode\"><div class=\"dijitTitlePaneContentInner\" dojoAttachPoint=\"containerNode\" waiRole=\"region\" tabindex=\"-1\"></div></div></div></div>",

        postCreate:function()
        {
            this.set('title',this.title);
            if(!this.open)
            {
                this.hideNode.style.display=this.wipeNode.style.display="none";
            }
            this._setCss();
            dojo.setSelectable(this.titleNode,false);
            this.inherited(arguments);
            dijit.setWaiState(this.containerNode,"labelledby",this.titleNode.id);
            dijit.setWaiState(this.focusNode,"haspopup","true");
            var _1=this.hideNode,_2=this.wipeNode;
            this._wipeIn=dojo.fx.wipeIn(
                            {
                                node:this.wipeNode,
                                duration:this.duration,
                                beforeBegin:function()
                                {
                                    _1.style.display="";
                                }
                            });
            this._wipeOut=dojo.fx.wipeOut(
                            {
                                node:this.wipeNode,duration:this.duration,
                                onEnd:function()
                                {
                                    _1.style.display="none";
                                }
                            });
            if(this.helpText!=="")
            {
                this.helpNode.id=this.id+"_helpNode";
                this.tooltip= new dijit.Tooltip(
                                        {
                                            label:this.helpText,
                                            connectId:[this.helpNode.id],
                                            showDelay:200
                                        });
            }
            if(this.persist)
            {
                this.restoreState();
            }
        },

        toggle:function()
        {
            dojo.forEach([this._wipeIn,this._wipeOut],function(_3)
                            {
                                if(_3.status()=="playing")
                                    {
                                        _3.stop();
                                    }
                                });
            this[this.open?"_wipeOut":"_wipeIn"].play();
            this.open=!this.open;
            //this._loadCheck();
            this._setCss();
            if(this.persist)
            {
                this.saveState();
            }
        },

        _handleFocus:function(e){},
        
        saveState:function()
        {
            dojo.cookie("TitlePane_"+this.id+"_state",(this.open?"open":"closed"));
        },
        
        restoreState:function()
        {
            if(dojo.cookie("TitlePane_"+this.id+"_state")=="closed")
            {
                if(dojo.isIE>6)
                {
                    window.setTimeout(dojo.hitch(this,"_closePane"),50);
                }
                else
                {
                    this._closePane();
                }
            }
        },
        
        _closePane:function()
        {
            this.open=false;
            this.hideNode.style.display=this.wipeNode.style.display="none";
            this._setCss();
        },
        
        callHelp:function()
        {
            dojo.publish("helpCalled",[this.helpUrl]);
        }
    }
);
}