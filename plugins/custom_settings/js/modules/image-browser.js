define(['lib/knockout', 'modules/state', 'modules/globals'], function(ko, state, globals) {
  
  var cName    = 'img-browser'
    , cTmpl    = 'image-browser'
    , $        = globals.$
    , config   = function() { return window.GS.config; }
    , isLoaded = false;
  
  function ImgBrowser() {
    var self = this
      , sub;
      
    this.uploadDir  = ko.observableArray([]);
    this.activeDir  = ko.observableArray([]);
    this.activeImg  = ko.observable();
    this.activeImgN = ko.observable('');
    this.folderPath = ko.observableArray(['uploads']);
    this.active     = state.imgBrowserActive;  // this is from stateMgr because Browse buttons should be able to trigger it
  
    sub = this.active.subscribe(function() { 
      self.load.call(null, self, sub);
    });
  }
  
  /** @method ImgBrowser.load
   *  @descr Fills uploadDir with contents from AJAX call the first time a 'Browse' button is clicked
   *  @param {object} context - Because the function is called inside a subscription, we need to keep a ref to self
   *  @param {function} sub - Because the function should only execute once, the subscription should be disposed in the success callback
   */
  ImgBrowser.prototype.load = function(context, sub) {
    var conf = config()
      , data;
      
    if (!isLoaded) {        
      $.ajax({ 
          url: conf.handler
        , type: 'GET'
        , data: {
            action: 'loadImageBrowser'
          ,  path: conf.handler
          , contentType: 'application/json; charset=utf-8'
          , dataType: 'json'
          , requestToken: conf.requestToken
          ,  adminDir: conf.adminDir
          ,  id: conf.id 
        }
      })
      .done(function(data, status, jqXHR) {
        data = JSON.parse(data);
        context.uploadDir(data);
        context.activeDir(data);
        sub.dispose();
      });
    }
  };
  
  /** @method ImgBrowser.setFolder
   *  @descr Sets the active folder both from the main view & the crumbs nav.
   */
  ImgBrowser.prototype.setFolder = function(data, e) {
    // crumbs nav
    if (typeof data === 'string') {
      var setFolder = this.findFolderByName(data)
        , setPath   = setFolder.path.split('/');
      this.folderPath(setPath.splice(0, setPath.length-1));
      this.activeDir(setFolder);
      return;
    }
    // main view
    var setFolder = this.findFolder(data)
      , setPath   = data.path.split('/');
    this.folderPath(setPath.splice(0, setPath.length-1));
    this.activeDir(setFolder);
  };
  
  /** @method ImgBrowser.findFolderByName
   *  @descr Recursively iterates over the directory structure in uploadDir
   *         to find a folder by name (used when a user clicks a folder in the nav crumbs)
   */
  ImgBrowser.prototype.findFolderByName = function(name, startAt) {
    var media = startAt || this.uploadDir()  
      , result = null;
      
    if (name === 'uploads')
      return media;
      
    for (var i = 0; i < media.children.length; i++) {
      if (media.children[i].folder === name) {
        result = media.children[i];
      } else if (media.children[i].children)
        result = this.setFolderFromName(name, media.children[i]);
    }
    return result;
  };
  
  /** @method ImgBrowser.findFolder
   *  @descr Recursively iterates over a folder's path property to find a folder by map 
   *  (used when a user clicks a folder in the main view)
   */
  ImgBrowser.prototype.findFolder = function(data, startAt) {
    var result = this.uploadDir()
      , path = data.path.split('/');
      
    for (var i = 1; i < path.length-1; i++) {
      for (var j = 0; j < result.children.length; j++) {
        if (result.children[j].folder === path[i])
          result = result.children[j];
      }
    }
    return result;
  };
  
  /** @method ImgBrowser.isActiveImg
   *  @descr Determines whether an image should have the 'active' class
   */
  ImgBrowser.prototype.isActiveImg = function(data) {
    return data.name === this.activeImgN();
  };
  
  /** @method ImgBrowser.selectImg
   *  @descr Called on image click. Fills selection with the image clicked
   */
  ImgBrowser.prototype.selectImg = function(data) {
    this.activeImg(data);
  };
  
  /** @method ImgBrowser.setImgName
   *  @descr Called on image mouseover. Displays the name of the active image in status bar
   */
  ImgBrowser.prototype.setImgName = function(data) {
    this.activeImgN(data.name);
  };
  
  /** @method ImgBrowser.resetImgName
   *  @descr Called on image mouseout
   */
  ImgBrowser.prototype.resetImgName = function(data) {
    this.activeImgN(this.activeImg() ? this.activeImg().name : '');
  };
  
  /** @method ImgBrowser.set
   *  @descr Fills the setting input with the selected image's path
   */
  ImgBrowser.prototype.set = function() {
    state.imgBrowserSetting.data.value(this.activeImg().path);
    this.dispose();
  };
  
  /** @method ImgBrowser.dispose
   *  @descr Unlike other dispose methods, this one is not called by Knockout
   *         but instead cleans the selection when a new image setting is selected
   */
  ImgBrowser.prototype.dispose = function() {
    this.active(false);
    this.activeImg(null);
    this.activeDir(this.uploadDir());
    this.folderPath(['uploads']);
  };
  
  /* ------  component registration ------ */
  
  ko.components.register(cName, {
    viewModel: ImgBrowser,
    template: { require: 'text!templates/' + cTmpl + '.html' }
  });
});
