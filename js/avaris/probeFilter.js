define([
        "dojo/_base/declare",
        "dojo/dom-construct",
        "dojo/dom",
        "dojo/_base/fx",
        "dojo/fx",
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
              coreFx,
              style,
              query,
              array,
              on,
              domAttr,
              xhr,
              lang) {
        return declare('avaris/probeFilter', [], {
            pl_id: null,
            songs: null,

            constructor: function () {
                this.songs = Array();
                this.connectSelectBox();
                this.searchSongs();
                if(window.location.hash != '')
                {
                    var hash = window.location.hash.replace('#','');
                    var pl_id = parseInt(hash);
                    this.getPlaylistSongs(pl_id);
                }
            },

            connectSelectBox: function () {
                on(dom.byId('playlistSelect'), "change", dojo.hitch(this, function () {
                    this.getPlaylistSongs(dom.byId('playlistSelect').value);
                    window.location.hash = dom.byId('playlistSelect').value;
                }));
            },

            searchSongs: function () {
                var nodes = dojo.query(".songRow");
                array.forEach(nodes, dojo.hitch(this, function (songRow, i) {
                    var song_id = domAttr.get(songRow, 'data-songid');
                    this.songs[song_id] = new Object();
                    this.songs[song_id].song_id = song_id;
                    this.songs[song_id].exists = true;
                    this.songs[song_id].displayed = true;
                    this.songs[song_id].title = domAttr.get(songRow, 'data-title');
                }));
            },

            getPlaylistSongs: function (pl_id) {
                if (pl_id != 0) {

                    xhr('/ajax/ajax.php?cmd=getPlaylistSongs&pl_id=' + pl_id, {
                        handleAs: 'json'
                    }).then(dojo.hitch(this, function (playlistSongs) {
                        this.filterSongs(playlistSongs);
                    }));
                }
                else {
                    this.schowSongs();
                }
                setTimeout(this.countSongs, 2000);

            },

            countSongs: function () {
                var nodes = dojo.query(".zaehler")
                var count = 1;
                array.forEach(nodes, dojo.hitch(this, function (zaehler, i) {
                    var songid = domAttr.get(zaehler, 'data-songid')
                    var display = style.get(dom.byId('song_' + songid), "display");
                    if (display != 'none') {
                        zaehler.innerHTML = count + ')';
                        count++;
                    }

                }));
            },

            schowSongs: function () {
                for (var song_id = 0; song_id < this.songs.length; song_id++) {
                    if (typeof this.songs[song_id] != 'undefined') {
                        this.wipeInSong(song_id);
                        this.songs[song_id].displayed = true;
                    }

                }
            },


            filterSongs: function (playlistSongs) {
                for (var song_id = 0; song_id < this.songs.length; song_id++) {
                    if (typeof this.songs[song_id] != 'undefined') {
                        if (typeof playlistSongs[song_id] != 'undefined') {
                            this.wipeInSong(song_id);
                        }
                        else {
                            this.wipeOutSong(song_id);
                        }
                    }
                }
            },

            wipeInSong: function (song_id) {
                if (!this.songs[song_id].displayed) {
                    var node = dom.byId('song_' + song_id);
                    style.set(node, "display", "none");
                    coreFx.wipeIn({
                        node: node
                    }).play();
                    this.songs[song_id].displayed = true;
                }

            },

            wipeOutSong: function (song_id) {
                if (this.songs[song_id].displayed) {
                    var node = dom.byId('song_' + song_id);
                    style.set(node, {
                        height: "",
                        display: "block"
                    })
                    coreFx.wipeOut({
                        node: node
                    }).play();
                    this.songs[song_id].displayed = false;
                }

            }
        });
    });


function z(r) {
    console.log(r);
}