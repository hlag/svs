dojo.require('dijit.Dialog');
dojo.provide("dojoaddons.ShadowDialog");
dojo.declare(
     'dojoaddons.ShadowDialog',
     [dijit.Dialog,dijit._Widget, dijit._Templated],
     {
          popup : null,
          widgetsInTemplate: true,
          foo: 'foo...',
          bar: 'bar!',
          templateString:"<div class=\"dijitDialog ShadowDialog\" tabindex=\"-1\" waiRole=\"dialog\" waiState=\"labelledby-${id}_title\">\r\n\t<div dojoAttachPoint=\"shadowBodyNode\" class=\"dijitDialogShadowBody\">\r\n\t<div class=\"shadowDialogInnerWrapper\"><div dojoAttachPoint=\"titleBar\" class=\"dijitDialogTitleBar\">\r\n\t<span dojoAttachPoint=\"titleNode\" class=\"dijitDialogTitle\" id=\"${id}_title\">${title}</span>\r\n\t<span dojoAttachPoint=\"closeButtonNode\" class=\"dijitDialogCloseIcon\" dojoAttachEvent=\"onclick: onCancel\">\r\n\t\t<span dojoAttachPoint=\"closeText\" class=\"closeText\">x</span>\r\n\t</span>\r\n\t</div>\r\n\t\t<div dojoAttachPoint=\"containerNode\" class=\"dijitDialogPaneContent\"></div>\r\n</div></div>\r\n<div class=\"dijitDialogShadowFooter\"></div>\r\n</div>\r\n",
          //templatePath: dojo.moduleUrl('my', 'customPopup.html'),

          // * set variables for the template
          postMixInProperties: function() {
               this.popup = new dijit.Dialog({});
               this.inherited(arguments);
          },
           postCreate: function() {
               //this.closeButton.onClick=dojo.hitch(this,'hide');
               this.inherited(arguments);
           },
           show: function(){
               this.popup.attr("content", this.domNode);
               this.popup.show();
            },
            hide: function(){
               this.popup.destroy();
            }
      }
);

