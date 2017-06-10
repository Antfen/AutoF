(function (global, $width, $height, $file, $img) {
  function resampled(data) {
   ($img.lastChild || $img.appendChild(new Image)
   ).src = data;
  }

  function load(e) {
   Resample(
     this.result,
     this._width || null,
     this._height || null,
     resampled
   );
   
  }
 
  $file.addEventListener("change", function change() {
   var
    width = parseInt($width.value, 10),
    height = parseInt($height.value, 10),
    file
   ;
   if (!width && !height) {
    $file.parentNode.replaceChild(
     file = $file.cloneNode(false),
     $file
    );
    $file.removeEventListener("change", change, false);
    ($file = file).addEventListener("change", change, false);
   } else if(
    ($file.files || []).length &&
    /^image\//.test((file = $file.files[0]).type)
   ) {

    file = new FileReader;
    file.onload = load;
    file._width = width;
    file._height = height;
    file.readAsDataURL($file.files[0]);
   } else if (file) {
   
   } else {
  
   }
  }, false);
 }(
  this,
  document.getElementById("width"),
  document.getElementById("height"),
  document.getElementById("file"),
  document.getElementById("img")
 ));