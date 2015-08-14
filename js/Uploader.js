dojo.require('dojo.cookie');
dojo.require("dojox.widget.Toaster");

dojo.declare('Uploader', null,
{
    maxKBytes: 30000,	   // in kbytes limited by php.ini directive upload_max_filesize
    maxNumFiles: 1, 	   // limited by php.ini directive max_file_uploads
    bytesOverall: 0,
    barOverall: null,
    fileStatus: {
            numCompleted: 0,     // number of completely uploaded files
            numAborted: 0,			// number of canceled files
            numProgressDone: 0,	// number of files where upload progress is 100%
            numError: 0          // number of files with error
    },
    files: [],           // files that will be uploaded after checking their length and max allowed number of uploads
    progressBars: [],    // keeps track of created bars
    displayTarget: null, // If null, progress is displayed in a dialog, otherwise provide element id
    dropTarget: null,
    rememberConfirmDelete: false,


    constructor: function(props)
    {
        //console.log("test");
        props.dropTarget = dojo.byId(props.dropTarget);
        dojo.safeMixin(this, props);

        //console.log(props);
        this.maxKBytes *= 1048;    // e.g. * (1024 + 24)
        
        // add drag and drop events
        dojo.connect(window, 'dragover', function(evt) {
                dojo.stopEvent(evt);
        });
        dojo.connect(window, 'drop', function(evt) {
                dojo.stopEvent(evt);
        });
        dojo.connect(this.dropTarget, 'dragenter', function() {
                dojo.addClass(this, 'targetActive');
        });
        dojo.connect(this.dropTarget, 'dragleave', function(evt) {
                dojo.removeClass(this, 'targetActive');
        });
        dojo.connect(this.dropTarget, 'mouseout', function(evt) {
                dojo.removeClass(this, 'targetActive');
        });
        dojo.connect(this.dropTarget, 'drop', this, function(evt) {
                var files = evt.dataTransfer.files;
                this.reset();
                this.addFiles(files);
                //dojo.removeClass(this.dropTarget, 'targetActive');
         });
    },

    addFiles: function(files)
    {
                var dfds = [], idx;
		dfds[0] = new dojo.Deferred();
		dfds[0].resolve(false);

		// exclude files that are to large
		// and chain deferreds so the get fired one after the other
		this.files = dojo.filter(files, function(file)
                {
			idx = dfds.length - 1;
			var self = this;
			if (file.size > this.maxKBytes)
                        {
                            
                                dojo.publish('globalToasterMessage',[{message:'Maximale Größe: ', type: 'message'}]);
                                dfds[idx + 1] = dfds[idx].then(function(remember) {
					if (!remember) {
						return files.length > 1 ? self.confirmFileSize(file.fileName) : self.confirmFileSizeSingle(file.fileName);
					}
					else {
						var dfd = new dojo.Deferred();
						dfd.resolve(true);
						return dfd;
					}
				});
				return false;
			}
			else {
				this.bytesOverall += file.size;
				return true;
			}
		}, this);
		// limit number of files you can upload
		if (this.files.length > this.maxNumFiles)
                {
			this.files = this.files.slice(0, this.maxNumFiles);
			idx = dfds.length - 1;
			dfds[idx + 1] = dfds[idx].then(dojo.hitch(this, function()
                        {
				dojo.publish('globalToasterMessage',[{message:'Maximale Anzahl der Files überschritten: '+this.maxNumFiles, type: 'warning'}]);
                                return this.confirmNumFileLimit(this.maxNumFiles);
			}));
		}

		dfds[dfds.length - 1].then(dojo.hitch(this, function() {
			//this.createBars();
			this.uploadFiles();
			dfds = null;   // free memory
		}));
	},

        uploadFiles: function()
        {
		var i = 0, len = this.files.length;
		for (; i < len; i++)
                {
                    //console.log(this.files[i]);
                    var file = this.files[i];
                    //console.log(file);
                    dojo.publish('globalToasterMessage',[{message:'Uploading File: '+file.name, type: 'message'}]);
                    this.upload(this.files[i]);
		}
		dojo.subscribe('upload/progress/done', this, function()
                {
			var stat = this.fileStatus;
			stat.numProgressDone++;
			if (stat.numProgressDone + stat.numAborted === len) {
				this.barOverall.update({
					indeterminate: true
				});
			}
		});
	},

        upload: function(file) {
		// Use native XMLHttpRequest instead of XhrGet since dojo 1.5 does not allow to send binary data as per docs
		var req = new XMLHttpRequest();
		var dfd = this.setReadyStateChangeEvent(req);
		//this.setProgressEvent(req, bar);
		//bar.upload();
                //console.log(tools.imageSelect.value);
                //console.log(dojo.byId('pfad').value)

		req.open('post','ajax/upload.php', false);
		req.overrideMimeType("text/plain; charset=x-user-defined-binary");
		req.setRequestHeader("Cache-Control", "no-cache");
		req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		req.setRequestHeader("X-File-Name", file.name);
		req.setRequestHeader("X-File-Size", file.size);
		req.setRequestHeader("Content-Type", "application/octet-stream; charset=UTF-8");
        req.setRequestHeader('X-Song-ID', dojo.byId('song_id').value);
		req.send(file);

            this.changeInfos(req.responseText);


		return dfd;
	},


    changeInfos: function (responseText)
    {
        dojo.byId('song_id').value = responseText;

        console.log(responseText);

        dojo.byId('test').innerHTML = '<meta http-equiv="refresh" content="0;http://www.svs.hlag/index.php?idt=editSong&song_id='+responseText+'">';
    },

        /**
	 * Displays upload status and errors.
	 * @param {XMLHttpRequest} req
	 */
	setReadyStateChangeEvent: function(req) {
		var dfd = new dojo.Deferred();
		dojo.connect(req, 'readystatechange', this, function() {
			var err = null;
			if (req.readyState == 4) {
				if (req.status == 200 || req.status == 201) {
					window.setTimeout(function() {
						//bar.complete();
                                                dojo.publish('globalToasterMessage',[{message:'Upload completed', type: 'message'}]);
                                               // BilderUpdater.updateBilder(dojo.byId('articleID').value, langShort, language);
                                                //console.log("upload ende");
						dfd.resolve();
					}, 500);
				}
				else {
					// server error or user aborted (canceled)
 					if (req.status === 0 && (bar.aborted || bar.paused)) {
						// User canceled or paused upload. Not an error.
						dfd.resolve();
					}
					else {
					   err = {
							statusCode: req.status,
							statusText: req.statusText,
							responseText: req.responseText
						};
						if (req.statusText == '') {
							err.responseText = 'Unknown error.';
						}
						//bar.error(err);
						dfd.reject();
						this.fileStatus.numError++;
					}

				}
				req = null;
				//bar.xhr = null;
			}
		});

		return dfd;
	},

        reset: function() {
		this.bytesOverall = 0;
		this.barOverall = null;
		this.fileStatus =  {
			numCompleted: 0,
			numAborted: 0,
			numProgressDone: 0,
			numError: 0
		};
		this.files = [];
		this.progressBars = [];
	}
});
