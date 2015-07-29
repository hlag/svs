require(["dojo/_base/declare", "../.././_Widget", "../.././_Templated", "dojo/text!./backEnd/dojoTemplates/titlePane.htm"],
        function(declare, _Widget, _Templated, template) {
     
    return declare("dojoaddons.TitlePane", [_Widget, _Templated, ], {
        templateString: template
        //  your custom code goes here
    });
     
});