(function() {
   var importDoc = document._currentScript.ownerDocument;

   var ajax = function (obj) {
      this.post = function (target, data){
         var xmlhttp = new XMLHttpRequest();

         xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
               try {
                  var response = JSON.parse(xmlhttp.responseText);
                  obj.onsuccess( response );
               } catch (e) {
                  console.log(e);
                  console.log(xmlhttp.responseText);
               }
            }
         }

         xmlhttp.onprogress = function (evt) {
            var percentComplete = (evt.loaded / evt.total) * 100;
            obj.onprogress( percentComplete );
         }

         xmlhttp.open("POST", target , true);
         xmlhttp.send(data);
      }

      this.get = function (target) {
         var xmlhttp = new XMLHttpRequest();

         xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
               try {
                  var response = JSON.parse(xmlhttp.responseText);
                  obj.onsuccess( response );
               } catch (e) {
                  console.log(e);
                  console.log(xmlhttp.responseText);
               }
            }
         }

         xmlhttp.onprogress = function (evt) {
            var percentComplete = (evt.loaded / evt.total) * 100;
            obj.onprogress(percentComplete);
         }

         xmlhttp.open("GET", target , true);
         xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
         xmlhttp.send();
      }
   }

   window.azuploader = function (setting) {
      var f = {};

      f.baseURL = setting.baseURL;
      f.backOverlay = null;
      f.divContent = null;

      f._id = function (id){
         return document.getElementById(id);
      }

      f.show = function () {
         if (f.backOverlay) f.backOverlay.parentNode.removeChild(f.backOverlay);
         f.template = importDoc.querySelector("template#azuploader");

         clone = importDoc.importNode(f.template.content, true);

         var buttonClose = clone.getElementById('azbutton-close');

         f.backOverlay = clone.getElementById('azuploader-back-overlay');
         f.listContent = clone.getElementById('azlist-files');
         f.listFolder = clone.getElementById('azlist-folder');
         f.navigation = clone.getElementById('aznavigation');
         f.filesElm = clone.getElementById('azfiles');
         f.formUpload = clone.getElementById('azformupload');
         document.body.appendChild(clone);

         buttonClose.onclick = function() {
            if (f.backOverlay) f.backOverlay.parentNode.removeChild(f.backOverlay);
            f.backOverlay = null;
         }

         f.formUpload.addEventListener('submit', function(e) {
            f.uploadFiles(this.action);

            if(e.preventDefault) e.preventDefault();
            else e.returnValue = false;
         });

         f.backOverlay.addEventListener('click', function(){
            // if (f.backOverlay) this.parentNode.removeChild(this);
            // f.backOverlay = null;
         });
      }

      f.on = function(){
         f.ajax = new ajax(f);

         if (setting.button) {
            f._id(setting.button).addEventListener("click", function(e){
               f.show();
               f.ajax.get(f.baseURL+"/api/read.php?request=folder");
               f.ajax.get(f.baseURL+"/api/read.php?request=allfiles");
               f.ajax.get(f.baseURL+"/api/read.php?request=navigation");
            });
         }
      }

      f.onprogress = function(e){

      }

      f.onsuccess = function( response ){
         switch (response.target) {
            case 'azlist-files':
               f.loadContent(response.files);
               f.formUpload.action = f.baseURL + "/api/upload.php?" + response.position;
               break;
            case 'navigation':
               f.loadNavigation(response.navigation);
               break;
            case 'azlist-folder':
               f.loadfolder(response.folder);
               break;
         }
      }

      f.uploadFiles = function(action){
         var formData = new FormData();

         for(var i = 0; i < f.filesElm.files.length; i++){
            formData.append('files[]' , f.filesElm.files[i]);
         }

         f.ajax.post(action, formData);
      }

      f.loadContent = function(files){
         f.listContent.innerHTML = "";

         for (var i = 0; i < files.length; i++) {
            var container = document.createElement('div')
               , span = document.createElement('span');

            container.classList.add('azuploader-container');
            switch (files[i].type) {
               case 'folder':
                  container.innerHTML = "<div class='azuploader-crop'> <img src='./userfiles/folder.jpg'> </div>";

                  container.classList.add('azuploader-folder');
                  span.classList.add('azuploader-title');

                  span.innerHTML = files[i].name;
                  container.appendChild(span);

                  (function (i) {
                     container.addEventListener('click', function(){
                        f.ajax.get(f.baseURL + '/api/read.php?request=allfiles&' + files[i].loc);
                        f.ajax.get(f.baseURL + '/api/read.php?request=navigation&' + files[i].loc);
                     });
                  }(i));
                  break;
               case 'image':
                  var getName = document.createElement('a');

                  container.innerHTML = "<div class='azuploader-crop'> <img src='" + files[i].loc + "'> </div>";

                  getName.innerHTML = 'Get Name';

                  span.classList.add('azuploader-span-method');
                  span.appendChild(getName);

                  container.appendChild(span);
                  break;
               default:

            }

            f.listContent.appendChild(container);
         }
      }

      f.loadfolder = function(folder){
         for (var i in folder) {
            var elmFolder = document.createElement('li');

            elmFolder.innerHTML = folder[i].name;
            f.listFolder.appendChild(elmFolder);
         }
      }

      f.loadNavigation = function(navigation){
         var listNavigation = document.createElement('span');
         for (var i = navigation.length-1; i >= 0; i--) {
            var elmNavigation = document.createElement('a');

            elmNavigation.innerHTML = navigation[i].name;
            (function (i) {
               elmNavigation.onclick = function(e){
                  f.ajax.get(f.baseURL + '/api/read.php?request=allfiles&' + navigation[i].loc);
                  f.ajax.get(f.baseURL + '/api/read.php?request=navigation&' + navigation[i].loc);

                  e.preventDefault();
               }
            }(i));

            var slace = document.createTextNode(' / ');
            listNavigation.appendChild(elmNavigation);
            listNavigation.appendChild(slace);
         }

         f.navigation.innerHTML = '';
         f.navigation.appendChild(listNavigation);
      }

      return f;
   }
})();
