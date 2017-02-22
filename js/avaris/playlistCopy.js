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
                this.connectCopyButton();
                this.connectDeleteButton();
            },

            connectCopyButton: function () {
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
                    this.connectCopyConfirmButton();
                }));


            },

            connectCopyConfirmButton: function () {
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
                    data: 'cmd=copyPlayList&old_pl_id=' + old_pl_id + '&pl_name=' + pl_name + '&pl_datum=' + pl_datum,

                }).then(dojo.hitch(this, function (resp) {
                        document.location.href = 'index.php?idt=playlist&pl_id=' + resp;
                    })
                );
            },

            connectDeleteButton: function () {
                on(dom.byId('deletePlaylist'), "click", dojo.hitch(this, function () {
                    this.deletePopup();
                }));
            },

            deletePopup: function () {
                this.popup = new dijit.Dialog();
                this.popup.attr("title", 'Playlist löschen');
                xhr('/ajax/ajax.php?cmd=getDeletePopup&pl_id=' + this.pl_id, {
                    handleAs: 'txt'
                }).then(dojo.hitch(this, function (resp) {
                    this.popup.attr("content", resp);
                    this.popup.show();
                    this.connectDeleteConfirmButton();
                }));
            },

            connectDeleteConfirmButton: function () {
                on(dom.byId('deletePlaylistReally'), "click", dojo.hitch(this, function () {
                    this.deletePlaylist();
                }));
            },
            deletePlaylist: function () {

                var url = '/ajax/ajax.php';
                xhr(url, {
                    method: 'POST',
                    data: 'cmd=deletePlaylist&pl_id=' + this.pl_id,

                }).then(dojo.hitch(this, function (resp) {
                        document.location.href = 'index.php?idt=playlist';
                    })
                );
            }

        });
    });


function z(r) {
    console.log(r);
}