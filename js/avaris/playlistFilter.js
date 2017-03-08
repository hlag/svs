define([
        "dojo/_base/declare",
        "dojo/dom-construct",
        "dojo/dom",
        "dojo/_base/fx",
        "dojo/fx",
        "dojo/dom-style",
        "dojo/dom-class",
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
              domClass,
              query,
              array,
              on,
              domAttr,
              xhr,
              lang) {
        return declare('avaris/playlistFilter', [], {
            pl_id: null,
            songs: null,

            constructor: function () {
                this.connectButtons();

            },

            connectButtons: function () {
                on(dom.byId('venus'), "click", dojo.hitch(this, function () {
                    this.searchSongs();
                    this.filterSongs('venus');
                }));
                on(dom.byId('mars'), "click", dojo.hitch(this, function () {
                    this.searchSongs();
                    this.filterSongs('mars');
                }));

                on(dom.byId('venus-mars'), "click", dojo.hitch(this, function () {
                    this.searchSongs();
                    this.filterSongs('venusMars');
                }));
                on(dom.byId('nix'), "click", dojo.hitch(this, function () {
                    this.searchSongs();
                    this.filterSongs('nix');
                }));
            },

            searchSongs: function () {
                this.songs = Array();
                var nodes = dojo.query('#block_uebrige  >> .song');
                array.forEach(nodes, dojo.hitch(this, function (songRow) {
                    var song_id = domAttr.get(songRow, 'data-song_id');
                    this.songs[song_id] = new Object();
                    this.songs[song_id].song_id = song_id;
                    this.songs[song_id].exists = true;
                    this.songs[song_id].isVenus = domClass.contains(songRow, "venus");
                    this.songs[song_id].isMars = domClass.contains(songRow, "mars");
                    this.songs[song_id].displayed = style.get(songRow, 'display') == 'block' ? true : false;
                    this.songs[song_id].title = domAttr.get(songRow, 'data-title');
                }));
            },


            schowSongs: function () {
                for (var song_id = 0; song_id < this.songs.length; song_id++) {
                    if (typeof this.songs[song_id] != 'undefined') {
                        this.wipeInSong(song_id);
                    }

                }
            },


            filterSongs: function (saenger) {
                for (var song_id = 0; song_id < this.songs.length; song_id++) {
                    if (typeof this.songs[song_id] != 'undefined') {
                        switch (saenger) {
                            case 'venus':
                                if (this.songs[song_id].isVenus) {
                                    this.wipeInSong(song_id);
                                } else {
                                    this.wipeOutSong(song_id);
                                }
                                break;
                            case 'mars':
                                if (this.songs[song_id].isMars) {
                                    this.wipeInSong(song_id);
                                } else {
                                    this.wipeOutSong(song_id);
                                }
                                break;
                            case 'venusMars':
                                if (this.songs[song_id].isMars && this.songs[song_id].isVenus) {
                                    this.wipeInSong(song_id);
                                } else {
                                    this.wipeOutSong(song_id);
                                }
                                break;
                            case 'nix':
                                this.wipeInSong(song_id);
                                break;

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
                }

            }
        });
    });


function z(r) {
    console.log(r);
}