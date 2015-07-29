dojo.provide("avarisgodot.ConnectionManager");

dojo.require("dijit._Widget");

dojo.declare("avarisgodot.ConnectionManager",
    null,
{
    connections: [],
    id: null,

    constructor: function()
    {
        console.log("constru");
    },

    add: function(id, event, context, method, remove)
    {
        if (remove)
        {
           this.removeFromID(id);
        }
        var handle = dojo.connect(dojo.byId(id), event, context, method);
        this.connections.push({id:id,handle: handle});
        console.log(this.connections);
    },

    removeFromID: function(id)
    {
        this.id = id;
        dojo.forEach(this.connections,dojo.hitch(this,'remove'));
    },

    remove: function(item, i)
    {
        if (this.connections[i].id==this.id)
        {
            console.log("disconnect");
            dojo.disconnect(this.connections[i].handle);
            this.connections.splice(i,1);
        }

    }
});