(function() {
   var importDoc = document._currentScript.ownerDocument;

   window.azuploader = function (setting) {
      var f = {};

      f.baseURL = setting.baseURL;
      f.backOverlay = null;
      f.divContent = null;
      f.template = importDoc.querySelector("template#azuploader");;

      f._id = function (id){
         return document.getElementById(id);
      }

      f.show = function () {
         if (f.backOverlay) f.backOverlay.parentNode.removeChild(f.backOverlay);
         f.template = importDoc.querySelector("template#azuploader");

         var clone = importDoc.importNode(template.content, true);

         document.body.appendChild(clone);
      }

      f.on = function(){
         if (setting.button) {
            f._id(setting.button).addEventListener("click", function(e){
               f.show();
               f.ajax = new ajax(f);
               f.ajax.get(f.baseURL+'/upload.php');
            });
         }
      }

      return f;
   }
})();
