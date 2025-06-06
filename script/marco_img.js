document.getElementById("imagen").addEventListener("change", function(event) {
  const reader = new FileReader();
  reader.onload = function(e) {
    const preview = document.getElementById("preview");
    const uploadText = document.getElementById("upload-text");

    preview.src = e.target.result;
    preview.style.display = "block";
    uploadText.style.display = "none";
  }
  reader.readAsDataURL(event.target.files[0]);
});
