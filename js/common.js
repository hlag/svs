dojo.require("dijit.Dialog");
dojo.require("dijit/focus");
dojo.require('dojo.dnd.Source');
dojo.require('dojo/dom-attr');
dojo.subscribe('/dnd/drop', this, 'endDrop');

dojo.require("dojox.widget.Toaster");

var tools = dojo.mixin({


    init: function() {
        /* setup toaster */
        var toasterNode = dojo.doc.createElement('div');
        toasterNode.id="globalToaster";
        dojo.body().appendChild(toasterNode);
        this.toaster = new dojox.widget.Toaster({messageTopic: 'globalToasterMessage', duration: 3000},toasterNode);


       new Uploader({
            url: '/godot/Bildupload/uploaderfnc.php',
            dropTarget: 'uploader',
            language: 1,
            languageShort: 'de',
            maxKBytes: 5000000,
            maxNumFiles: 10
        });
    }

},tools);

dojo.addOnLoad(function(){tools.init();});


var node;
function endDrop(source, nodeX) {
    node = nodeX;
    setTimeout(dojo.hitch(this, 'workOnDOM'), 300);
}

function workOnDOM() {
    var Liste = dojo.attr(node[0].id, 'liste');
    z(Liste);
    deliquent = node[0];
    parent = deliquent.parentNode;
    childs = parent.childNodes;

    if(Liste == 'genre')
    {
        dojo.xhrPost({
            url: '/ajax/ajax.php',
            handleAs: 'json',
            postData: 'cmd=setGenre&song_id=' + node[0].id +  '&g_id=' + parent.id,
            load: function (resp) {

            }
        });
    }
    else {
        var x = 0;
        for (var key in childs) {
            if (childs[key].id) {
                x++;

                if (node[0].id == childs[key].id) {
                    dojo.xhrPost({
                        url: '/ajax/ajax.php',
                        handleAs: 'json',
                        postData: 'cmd=sortSong&song_id=' + node[0].id + '&position=' + x + '&parent_id=' + parent.id,
                        load: function (resp) {
                            changeValues(resp['v']);
                            changeClasses(resp['c']);
                        }
                    });
                }

            }

        }
    }
}

function changeClasses(data) {
    for (var key in data) {
        node = dojo.byId(data[key]['id']);

        dojo.removeClass(node, 'hidden');
        dojo.addClass(node, data[key]['v']);
    }
}

function changeValues(data) {
    for (var key in data) {
        dojo.byId(data[key]['id']).innerHTML = data[key]['v'];
    }
}


function newPlaylistBlock(pl_id) {
    dojo.xhrPost({
        url: '/ajax/ajax.php',
        postData: 'pl_id=' + pl_id + '&cmd=newBlock',
        handleAs: 'text',
        load: function (resp) {
            dojo.byId('playlistBlockZiel').outerHTML = resp;
        }
    });
    dojo.subscribe('/dnd/drop', this, 'endDrop');

}
function deleteblock(pb_id) {
    dojo.xhrPost({
        url: '/ajax/ajax.php',
        postData: 'pb_id=' + pb_id + '&cmd=deleteBlock'
    });
    dojo.byId('block_' + pb_id).outerHTML = '';
}

function editName(pb_id) {

    popup = new dijit.Dialog();
    popup.attr("style", "width: 500px");
    popup.attr("style", "height: 200px");
    dojo.xhrGet({
        url: 'ajax/ajax.php?cmd=editName&pb_id=' + pb_id + '&rnd=' + Date.now(),
        handleAs: 'text',
        load: function (resp) {
            popup.attr("content", resp);
            popup.show();

        }
    });
}
function getSingleDatum(table, field, id, id_name) {
    console.log(table + '&field=' + field + '&id=' + id + '&id_name=' + id_name);
    popup = new dijit.Dialog();
    popup.attr("style", "width: 500px");
    popup.attr("style", "height: 200px");
    dojo.xhrGet({
        url: '/ajax/ajax.php?cmd=getSingleDatum&table=' + table + '&field=' + field + '&id=' + id + '&id_name=' + id_name + '&rnd=' + Date.now(),
        handleAs: 'text',
        load: function (resp) {
            popup.attr("content", resp);
            popup.show();

        }
    });
}


function saveSingleData(table, field, id, id_name) {

    dojo.xhrPost({
        url: '/ajax/ajax.php',
        form: 'myform',
        handleAs: 'text',
        load: function (resp) {
            console.log(table + '_' + field + '_' + id);
            resp = resp.replace(/\n/g, '<br>');
            dojo.byId(table + '_' + field + '_' + id).innerHTML = resp;
        }
    });
    popup.destroy();

}

function getArr(id) {
    dojo.xhrGet({
        url: 'ajax/ajax.php?cmd=getArrangement&id=' + id,
        load: showArrangement,
        handleAs: 'text'
    });
}
function showArrangement(song) {
    if (typeof popup != 'undefined')
        popup.destroy();

    popup = new dijit.Dialog();
    popup.attr("content", song);
    popup.attr("style", "width: 800px");
    popup.show();


}

function textarea(tb, id) {
    node = dojo.byId(tb + '_' + id);
    text = node.innerHTML.replace(/<br>/g, "\n");
    dojo.attr(node, "onclick", "");
    node.innerHTML = '<textarea class="form-control" rows="3" id="text_' + tb + '_' + id + '"' +
    ' onkeyup="saveArr(' + id + ', \'' + tb + '\')" ' +
    ' onblur="saveArrAndExit(' + id + ', \'' + tb + '\')" >' + text + '</textarea>';
    dojo.byId('text_' + tb + '_' + id).focus();
}
function saveArrAndExit(id, tb) {
    saveArr(id, tb);
    node = dojo.byId(tb + '_' + id);
    dojo.attr(node, "onclick", "textarea('" + tb + "', " + id + ")");
    text = dojo.byId('text_' + tb + '_' + id).value;
    text = text.replace(/\n/g, '<br>');
    dojo.byId(tb + '_' + id).innerHTML = text;
}


function saveArr(id, tb) {
    text = dojo.byId('text_' + tb + '_' + id).value;
    dojo.xhrPost({
        url: 'ajax/ajax.php',
        postData: 'cmd=saveArrangement&text=' + text + '&feld=' + tb + '&id=' + id
    });
}


function submitInstrument(id, instrument) {
    dojo.xhrPost({
        url: 'ajax/ajax.php',
        postData: 'cmd=saveInstrument&instrument=' + instrument + '&id=' + id
    });
}

function submitStatus(id, status) {
    dojo.xhrPost({
        url: 'ajax/ajax.php',
        postData: 'cmd=saveStatus&status=' + status + '&id=' + id
    });
    var nodes = dojo.query('#song_' + id + '  > .indikator');
    dojo.forEach(nodes, function (node) {

        dojo.removeClass(node, "strong");
        dojo.removeClass(node, "text-warning");
        dojo.removeClass(node, "text-success");
        dojo.removeClass(node, "text-danger");
        dojo.removeClass(node, "text-muted");
        z(status);

        switch (status) {
            case 1:
                dojo.addClass(node, "text-muted");
                break;

            case 2:
                dojo.addClass(node, "strong");
                dojo.addClass(node, "text-success");
                break;

            case 3:
                dojo.addClass(node, "strong");
                dojo.addClass(node, "text-muted");
                break;

            case 4:
                dojo.addClass(node, "strong");
                break;

            case 5:
                dojo.addClass(node, "strong");
                dojo.addClass(node, "text-warning");
                break;
        }

    });
}
function muckerMenue(id, musiker) {
    console.log(id);

    popup = new dijit.Dialog();
    popup.attr("title", 'Status-Infos: ');

    dojo.xhrGet({
        url: 'ajax/ajax.php?cmd=getMuckerMeinung&id=' + id + '&m=' + musiker,
        load: function (resp) {
            popup.attr("content", resp);

        },
        handleAs: 'text'
    });

    popup.show();

}

function sendMuckermeinung(id, musiker, status) {
    console.log(id);
    dojo.xhrPost({
        url: 'ajax/ajax.php',
        postData: 'cmd=saveMuckermeinung&musiker=' + musiker + '&status=' + status + '&id=' + id
    });

    var node = dojo.byId(musiker + '_' + id);
    dojo.removeClass(node, "text-success");
    dojo.removeClass(node, "text-danger");
    dojo.removeClass(node, "text-muted");
    dojo.removeClass(node, "fa-minus");
    dojo.removeClass(node, "fa-thumbs-down");
    dojo.removeClass(node, "fa-thumbs-up");

    z(status);

    switch (status) {
        case 1:
            dojo.addClass(node, "fa-minus");
            dojo.addClass(node, "text-muted");
            break;

        case 4:
            dojo.addClass(node, "text-danger");
            dojo.addClass(node, "fa-thumbs-down");
            break;

        case 5:
            dojo.addClass(node, "text-success");
            dojo.addClass(node, "fa-thumbs-up");
            break;
    }
    popup.destroy();
}


dojo.declare('pausenManager', null, {

    editPause: function (pb_id) {
        if (typeof popup != 'undefined')
            popup.destroy();
        popup = new dijit.Dialog();
        popup.attr("title", 'Pause');

        dojo.xhrGet({
            url: 'ajax/ajax.php?cmd=getPause&pb_id=' + pb_id,
            handleAs: 'text',
            load: function (resp) {
                popup.attr("content", resp);
            }
        });
        popup.show();
    },
    savePause: function (pb_id) {

        dojo.xhrPost({
            url: 'ajax/ajax.php',
            form: 'pausenForm',
            handleAs: 'json',
            load: function (resp) {
                z(resp['v']);
                changeValues(resp['v']);
                changeClasses(resp['c']);
            }
        });
        popup.destroy();
    }


});
var pausenManager = new pausenManager();


dojo.declare('playlistManager', null, {

    editPlaylistStart: function (pl_id) {
        if (typeof popup != 'undefined')
            popup.destroy();
        popup = new dijit.Dialog();
        popup.attr("title", 'Beginn');

        dojo.xhrGet({
            url: 'ajax/ajax.php?cmd=getPlaylistStart&pl_id=' + pl_id,
            handleAs: 'text',
            load: function (resp) {
                popup.attr("content", resp);
            }
        });
        popup.show();
    },
    savePlaylistStart: function (pl_id) {

        dojo.xhrPost({
            url: 'ajax/ajax.php',
            form: 'playlistStartForm',
            handleAs: 'json',
            load: function (resp) {
                changeValues(resp['v']);
                changeClasses(resp['c']);
            }
        });
        popup.destroy();
    }


});
var playlistManager = new playlistManager();


dojo.declare('songFilter', null, {
    karneval: true,


    filterSongs: function (g_id) {

        this.karneval = this.karneval ? false : true;

        nodes = dojo.query('#block_uebrige_2  >> .genre_' + g_id);
        for (var key in nodes)
            if (this.karneval)
                dojo.addClass(nodes[key], "hidden");
            else
                dojo.removeClass(nodes[key], "hidden");

    }
});
var songFilter = new songFilter();

dojo.require("dojo.fx");
dojo.declare('playedManager', null, {
    karneval: true,


    changePlayedStatus: function (ps_id) {
        if (typeof popup != 'undefined')
            popup.destroy();
        popup = new dijit.Dialog();
        popup.attr("title", 'Song gespielt');

        dojo.xhrGet({
            url: 'ajax/ajax.php?cmd=getPlayedStatus&ps_id=' + ps_id,
            handleAs: 'text',
            load: function (resp) {
                popup.attr("content", resp);
            }
        });
        popup.show();
    },
    savePlayedStatus: function (ps_id, status) {

        dojo.xhrPost({
            url: 'ajax/ajax.php',
            postData: 'cmd=savePlayedStatus&ps_id=' + ps_id + '&status=' + status,
            handleAs: 'json',
            load: function (resp) {

                var node = dojo.byId('icon_' + resp.ps_id);
                dojo.removeClass(node, 'fa-question');
                dojo.removeClass(node, 'fa-thumbs-down');
                dojo.removeClass(node, 'fa-thumbs-up');
                dojo.removeClass(node, 'fa-thumbs-up');
                dojo.removeClass(node, 'fa-minus');
                dojo.addClass(node, resp.iconClass);


                var node = dojo.byId('played_class_' + resp.ps_id);
                dojo.removeClass(node, 'text-muted');
                dojo.removeClass(node, 'text-danger');
                dojo.removeClass(node, 'text-warning');
                dojo.removeClass(node, 'text-success');
                dojo.addClass(node, resp.class);

                if(resp.class == 'text-muted'){
                    dojo.byId('erfolg_class_' + resp.ps_id).outerHTML = '';
                }


            }
        });
        popup.destroy();
    },
    updateProbeDatum: function (id) {
        var wipeArgs = {
            node: "song_"+id,
            duration: 1000
        };
        dojo.fadeOut(wipeArgs).play();
        dojo.xhrPost({
            url: 'ajax/ajax.php',
            postData: 'cmd=updateProbeDatum&id=' + id
        });
    }
});
var playedManager = new playedManager();
dojo.declare('erfolgManager', null, {
    karneval: true,


    changeErfolgStatus: function (ps_id) {
        if (typeof popup != 'undefined')
            popup.destroy();
        popup = new dijit.Dialog();
        popup.attr("title", 'Tanz-Erfolg');

        dojo.xhrGet({
            url: 'ajax/ajax.php?cmd=getErfolgStatus&ps_id=' + ps_id,
            handleAs: 'text',
            load: function (resp) {
                popup.attr("content", resp);
            }
        });
        popup.show();
    },
    changeClasses: function (status){
        for(var x = 0; x < 5; x++) {

            if (status >= x) {
                var node = dojo.byId('erfolg_button_' + x);
                dojo.removeClass(node, 'btn-muted');
                dojo.addClass(node, 'btn-danger');
            }
            else {
                var node = dojo.byId('erfolg_button_' + x);
                dojo.removeClass(node, 'btn-danger');
                dojo.addClass(node, 'btn-muted');
            }


         }

    },
    saveErfolgStatus: function (ps_id, status) {

        dojo.xhrPost({
            url: 'ajax/ajax.php',
            postData: 'cmd=saveErfolgStatus&ps_id=' + ps_id + '&status=' + status,
            handleAs: 'text',
            load: function (resp) {
                dojo.byId('erfolg_class_'+ps_id).outerHTML = resp;
            }
        });
        popup.destroy();
    }
});
var erfolgManager = new erfolgManager();
dojo.declare('websiteActive', null, {



    toggle: function (id, variable) {

        dojo.xhrPost({
            url: 'ajax/ajax.php',
            postData: 'cmd=toggleWebsiteActive&id=' + id +'&var='+variable,
            handleAs: 'json',
            load: function (resp) {

                var node = dojo.byId(variable+'_aktiv_' + id);
                dojo.removeClass(node, 'text-muted');
                dojo.removeClass(node, 'text-success');
                dojo.removeClass(node, 'fa-toggle-on');
                dojo.removeClass(node, 'fa-toggle-off');
                dojo.removeClass(node, 'fa-thumbs-o-up');
                dojo.removeClass(node, 'fa-thumbs-up');
                dojo.addClass(node, resp.class);
                dojo.addClass(node, resp.icon);


            }
        });
    }
});
var websiteActive = new websiteActive();

var tabs = new Array();
function countBPM()
{
    tempTime = new Date().getTime();
    dauer = calculateBPM(tempTime);
    dojo.byId('tabber').innerHTML = dauer;
};

function calculateBPM(tempTime)
{
    if(tabs[1])
    {
        var dauer = (tabs[tabs.length-1] - tabs[0]) / (tabs.length -1);
        if((tempTime - tabs[tabs.length-1]) > dauer * 3)
            tabs =  new Array();
    };
    if(tabs[0] && !tabs[1])
    {

        if((tempTime - tabs[0]) > 2000)
            tabs =  new Array();
    };

    tabs[tabs.length] = tempTime;
    if(tabs[1])
        bpm = (60000 *(tabs.length -1) /(tabs[tabs.length-1] - tabs[0])).toFixed(1) ;
    else {

        bpm = 'bpm';
    }
    return bpm;
};



dojo.declare('edit', null, {
    getPlaylistDatum: function (pl_id){
        popup = new dijit.Dialog();
        popup.attr("style", "width: 500px");
        popup.attr("style", "height: 200px");
        dojo.xhrGet({
            url: '/ajax/ajax.php?cmd=getPlaylistDatum&pl_id=' + pl_id + '&rnd=' + Date.now(),
            handleAs: 'text',
            load: function (resp) {
                popup.attr("content", resp);
                popup.show();

            }
        });
    }



});
var edit = new edit();
function z(v) {
    console.log(v);
}