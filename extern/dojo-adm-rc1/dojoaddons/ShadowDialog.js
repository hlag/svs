dojo.provide("dojoaddons.ShadowDialog");
dojo.require("dijit.Dialog");
dojo.declare("dojoaddons.ShadowDialog",
                [dijit.Dialog],
                {
                    templateString:"<div class=\"dijitDialog ShadowDialog\" tabindex=\"-1\" waiRole=\"dialog\" waiState=\"labelledby-${id}_title\">\r\n\t<div dojoAttachPoint=\"shadowBodyNode\" class=\"dijitDialogShadowBody\">\r\n\t<div class=\"shadowDialogInnerWrapper\"><div dojoAttachPoint=\"titleBar\" class=\"dijitDialogTitleBar\">\r\n\t<span dojoAttachPoint=\"titleNode\" class=\"dijitDialogTitle\" id=\"${id}_title\">${title}</span>\r\n\t<span dojoAttachPoint=\"closeButtonNode\" class=\"dijitDialogCloseIcon\" dojoAttachEvent=\"onclick: onCancel\">\r\n\t\t<span dojoAttachPoint=\"closeText\" class=\"closeText\">x</span>\r\n\t</span>\r\n\t</div>\r\n\t\t<div dojoAttachPoint=\"containerNode\" class=\"dijitDialogPaneContent\"></div>\r\n</div></div>\r\n<div class=\"dijitDialogShadowFooter\"></div>\r\n</div>\r\n",
   

    setContent:function(_1)
    {
        /*if(!this._isDownloaded)
        {
            this.href="";
            this._onUnloadHandler();
        }*/
        this._setContent(_1||"");
        this._isDownloaded=false;
        /*if(this.parseOnLoad)
        {
            //this._createSubWidgets();
        }*/
        if(this.doLayout!="false"&&this.doLayout!==false)
        {
            this._checkIfSingleChild();
            if(this._singleChild&&this._singleChild.resize)
            {
                this._singleChild.startup();
                this._singleChild.resize(this._contentBox||dojo.contentBox(this.containerNode||this.domNode));
            }
        }
        //this._onLoadHandler();
        if(dojo.isIE&&dojo.isIE<7)
        {
            this._fixDialogLayout();
        }
    },

    _fixDialogLayout:function()
    {
        var _2=this.shadowBodyNode;
        dojo.removeClass(_2,"IE6Fixed");
        var _3=dojo._getContentBox(_2).h;
        _2.style.height=_3+"px";
        dojo.addClass(_2,"IE6Fixed");
    },

    onFocus:function()
    {
        this.domNode.blur();
    }
}
);