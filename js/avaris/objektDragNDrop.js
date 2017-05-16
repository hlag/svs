define(["dojo/_base/declare",
        "dojo/dom-construct",
        "dojo/dom",
        "dojo/_base/fx",
        "dojo/dom-style",
        "dojo/query",
        "dojo/_base/array",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/request/xhr"],
    function (declare,
              domConstruct,
              dom,
              dojoFx,
              style,
              query,
              array,
              on,
              domAttr,
              xhr) {
        return declare('avaris/objektDragNDrop', [], {
            pl_id: '',

            constructor: function () {
                this.pl_id = dom.byId('pl_id').value;
                this.connectBloecke();


            },
            connectBloecke: function () {
                var drake = dragula([document.getElementById('playlistRahmen')]);
                drake.on('drop', dojo.hitch(this, function (el, target, source, sibling) {
                    var blockId = domAttr.get(el, 'data-blockId');
                    if (sibling != null) {
                        var sibling_id = domAttr.get(sibling, 'data-blockId');
                    }
                    else {
                        var sibling_id = 0;
                    }
                    this.resort(blockId, sibling_id);
                }));
            },

            newPlaylistBlock: function (pl_id) {
                dojo.xhrPost({
                    url: '/ajax/ajax.php',
                    postData: 'pl_id=' + pl_id + '&cmd=newBlock',
                    handleAs: 'text',
                    load: function (resp) {
                        dojo.byId('playlistBlockZiel').outerHTML = resp;
                    }
                })
            },
            resort: function (blockId, sibling_id) {
                var url = '/ajax/ajax.php';
                xhr(url, {
                    method: 'post',
                    handleAs: 'json',
                    data: 'cmd=sortBlock&blockId=' + blockId + '&sibling_id=' + sibling_id +'&pl_id='+this.pl_id,
                }).then(dojo.hitch(this, function (resp) {
                    this.changeValues(resp['v']);
                    this.changeClasses(resp['c']);
                }));


            },
            changeClasses: function (data) {
                for (var key in data) {
                    node = dojo.byId(data[key]['id']);
                    dojo.removeClass(node, 'hidden');
                    dojo.addClass(node, data[key]['v']);
                }
            },

            changeValues: function (data) {
                for (var key in data) {
                    dojo.byId(data[key]['id']).innerHTML = data[key]['v'];
                }
            }


        });
    });


function z(r) {
    console.log(r);
}