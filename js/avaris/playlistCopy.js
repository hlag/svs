define([
        "dojo/_base/declare",
        "dojo/dom-construct",
        "dojo/dom",
        "dojo/_base/fx",
        "dojo/dom-style",
        "dojo/query",
        "dojo/_base/array",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/request/xhr",
        "dojo/_base/lang"],

    function (declare,
              domConstruct,
              dom,
              dojoFx,
              style,
              query,
              array,
              on,
              domAttr,
              xhr,
              lang) {
        return declare('avaris/playlistCopy', [], {
            popup: null,
            pl_id: null,

            constructor: function () {
                this.pl_id = domAttr.get(dom.byId('copyPlaylist'), 'data-pl_id');
                this.connectButton();
            },

            connectButton: function () {
                on(dom.byId('copyPlaylist'), "click", dojo.hitch(this, function () {
                    this.copyPopup();
                }));
            },


            copyPopup: function () {
                this.popup = new dijit.Dialog();
                this.popup.attr("title", 'Playlist kopieren');
                xhr('/ajax/ajax.php?cmd=getCopyPopup&pl_id=' + this.pl_id, {
                    handleAs: 'txt'
                }).then(dojo.hitch(this, function (resp) {
                    this.popup.attr("content", resp);
                    this.popup.show();
                    this.connectCopyButton();
                }));


            },

            connectCopyButton: function () {
                on(dom.byId('copyPlaylistReally'), "click", dojo.hitch(this, function () {
                    this.copyPlaylist();
                }));
            },

            copyPlaylist: function () {
                var pl_name = dom.byId('id_pl_name').value;
                var pl_datum = dom.byId('id_pl_datum').value;
                var old_pl_id = dom.byId('id_old_pl_id').value;
                var url = '/ajax/ajax.php';
                xhr(url, {
                    method: 'POST',
                    data: 'cmd=copyPlayList&old_pl_id='+old_pl_id+'&pl_name='+pl_name+'&pl_datum='+pl_datum,

                }).then(dojo.hitch(this, function (resp) {

                    })
                );
            },

            getBuchungsInput: function (rows, gesamtBetrag) {
                var node = lang.clone(rows[rows.length - 1]);
                var id_new = new Date().getTime().toString();
                var matches = node.id.match(/gegenBuchung_(\d+)/);
                var needle = 'bp\\[' + matches[1].toString() + '\\]';
                var html = node.innerHTML;
                var replacer = new RegExp(needle, "g");
                node.innerHTML = html.replace(replacer, 'bp[' + id_new + ']');
                node.id = 'gegenBuchung_' + id_new;
                rows[rows.length] = node;
                var inputs = dom.byId('inputs');
                domConstruct.place(node, inputs);
                domConstruct.place(node, inputs);

                domAttr.set(dom.byId('id_bp[' + id_new + '][' + habenOderSollGegenKonto + ']'), 'value', gesamtBetrag.toFixed(2));
                domAttr.set(dom.byId('id_bp[' + id_new + '][posten_id]'), 'value', id_new);

                on(dom.byId('id_bp[' + id_new + '][' + habenOderSollGegenKonto + ']'), "change", dojo.hitch(this, function () {
                    this.searchBetraege();
                }));
            },

            controlKontoBelegnummerInput: function () {
                var rows = dojo.query('.gegenBuchung');
                if (rows.length > 1) {
                    dom.byId('kontoBelegInput').innerHTML = '<div class="form-group"><label for="id_p[belegnummer]" class="col-sm-2 control-label">Sammel-Beleg</label><div class="col-sm-10"><input type="text" class="form-control " id="id_p[belegnummer]" name="p[belegnummer]" value="" placeholder="P[belegnummer]"></div><div class="clearfix"></div> </div>';

                }
                else {
                    dom.byId('kontoBelegInput').innerHTML = '';
                }


            }

        });
    });


function z(r) {
    console.log(r);
}