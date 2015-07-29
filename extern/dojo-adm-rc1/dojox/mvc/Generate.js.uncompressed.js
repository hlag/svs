//>>built
define("dojox/mvc/Generate", [
	"dojo/_base/lang",
	"dojo/_base/declare",
	"./_Container",
	"./Group",
	"../../.././form/TextBox"
], function(lang, declare, Container){
	/*=====
		Container = dojox.mvc._Container;
		declare = dojo.declare;
	=====*/

	return declare("dojox.mvc.Generate", [Container], {
		// summary:
		//		A container that generates a view based on the data model its bound to.
		//
		// description:
		//		A generate introspects its data binding and creates a view contained in
		//		it that allows displaying the bound data. Child dijits or custom view
		//		components inside it inherit their parent data binding context from it.
	
		// _counter: [private] Integer
		//		A count maintained internally to always generate predictable widget
		//		IDs in the view generated by this container.
		_counter : 0,
	
		// defaultWidgetMapping: Object
		//		The mapping of types to a widget class. Set widgetMapping to override this. 
		//	
		_defaultWidgetMapping: {"String" : "dijit.form.TextBox"},
	
		// defaultClassMapping: Object
		//		The mapping of class to use. Set classMapping to override this. 
		//	
		_defaultClassMapping: {"Label" : "generate-label-cell", "String" : "generate-dijit-cell", "Heading" : "generate-heading", "Row" : "row"},
	
	
		// defaultIdNameMapping: Object
		//		The mapping of id and name to use. Set idNameMapping to override this. A count will be added to the id and name
		//	
		_defaultIdNameMapping: {"String" : "textbox_t"},
		
		////////////////////// PRIVATE METHODS ////////////////////////
	
		_updateBinding: function(){
			// summary:
			//		Regenerate if the binding changes.
			this.inherited(arguments);
			this._buildContained();
		},
	
		_buildContained: function(){
			// summary:
			//		Destroy any existing generated view, recreate it from scratch
			//		parse the new contents.
			// tags:
			//		private
			this._destroyBody();
	
			this._counter = 0;
			this.srcNodeRef.innerHTML = this._generateBody(this.get("binding"));
	
			this._createBody();
		},
	
		_generateBody: function(binding, hideHeading){
			// summary:
			//		Generate the markup for the view associated with this generate
			//		container.
			//	binding:
			//		The associated data binding to generate a view for.
			//	hideHeading:
			//		Whether the property name should be displayed as a heading.
			// tags:
			//		private
			var body = "";
			for(var prop in binding){
				if(binding[prop] && lang.isFunction(binding[prop].toPlainObject)){
					if(binding[prop].get(0)){
						body += this._generateRepeat(binding[prop], prop);
					}else if(binding[prop].value){
						// TODO: Data types based widgets
						body += this._generateTextBox(prop);
					}else{
						body += this._generateGroup(binding[prop], prop, hideHeading);
					}
				}
			}
			return body;
		},
	
		_generateRepeat: function(binding, repeatHeading){
			// summary:
			//		Generate a repeating model-bound view.
			//	binding:
			//		The bound node (a collection/array node) to generate a
			//		repeating UI/view for.
			//	repeatHeading:
			//		The heading to be used for this portion.
			// tags:
			//		private
			var headingClass = (this.classMapping && this.classMapping["Heading"]) ? this.classMapping["Heading"] : this._defaultClassMapping["Heading"];
			var repeat = '<div data-dojo-type="dojox.mvc.Group" data-dojo-props="ref: \'' + repeatHeading + '\'" + id="' + this.id + '_r' + this._counter++ + '">' +
						 '<div class="' + headingClass + '\">' + repeatHeading + '</div>';
			repeat += this._generateBody(binding, true);
			repeat += '</div>';
			return repeat;
		},
		
		_generateGroup: function(binding, groupHeading, hideHeading){
			// summary:
			//		Generate a hierarchical model-bound view.
			//	binding:
			//		The bound (intermediate) node to generate a hierarchical
			//		view portion for.
			//	groupHeading:
			//		The heading to be used for this portion.
			//	hideHeading:
			//		Whether the heading should be hidden for this portion.
			// tags:
			//		private
			var group = '<div data-dojo-type="dojox.mvc.Group" data-dojo-props="ref: \'' + groupHeading + '\'" + id="' + this.id + '_g' + this._counter++ + '">';
			if(!hideHeading){
				var headingClass = (this.classMapping && this.classMapping["Heading"]) ? this.classMapping["Heading"] : this._defaultClassMapping["Heading"];
				group += '<div class="' + headingClass + '\">' + groupHeading + '</div>';
			}
			group += this._generateBody(binding);
			group += '</div>';
			return group;
		},
	
		_generateTextBox: function(prop){
			// summary:
			//		Produce a widget for a simple value.
			//	prop:
			//		The data model property name.
			// tags:
			//		private
			// TODO: Data type based widget generation / enhanced meta-data
			var idname = this.idNameMapping ? this.idNameMapping["String"] : this._defaultIdNameMapping["String"];
			idname = idname + this._counter++; 
			var widClass = this.widgetMapping ? this.widgetMapping["String"] : this._defaultWidgetMapping["String"];
			var labelClass = (this.classMapping && this.classMapping["Label"]) ? this.classMapping["Label"] : this._defaultClassMapping["Label"];
			var stringClass = (this.classMapping && this.classMapping["String"]) ? this.classMapping["String"] : this._defaultClassMapping["String"];
			var rowClass = (this.classMapping && this.classMapping["Row"]) ? this.classMapping["Row"] : this._defaultClassMapping["Row"];
			
			return '<div class="' + rowClass + '\">' +
					'<label class="' + labelClass + '\">' + prop + ':</label>' +
					'<input class="' + stringClass + '\" data-dojo-type="' + widClass + '\" data-dojo-props="name: \'' + idname + "', ref: '" + prop + '\'" id="' +
					idname + '\"></input>' +
					'</div>';
		}
	});
});
