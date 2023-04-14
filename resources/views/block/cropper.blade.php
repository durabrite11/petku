 <!-- Modal -->
 <div class="modal fade" id="modal-cropper" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Cropper</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="img-container">
              <img id="image" src="https://picsum.photos/200/300/?blur" alt="Picture">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-save-cropper" id="btn-save-cropper">Save</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
<script>
window.addEventListener('DOMContentLoaded', function () {
    var image = document.getElementById('image');
    var cropBoxData;
    var canvasData;
    var cropper;
    var filename ="";
    var filetype ="";
    $('#modal-cropper').on('shown.bs.modal', function () {
        cropper = new Cropper(image, {
            autoCropArea: 0.5,
            aspectRatio: 16 / 9,
            ready: function () {
            croppable = true;
            //Should set crop box data first here
            cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
            }
        });
    }).on('hidden.bs.modal', function () {
        $(".modal-cropper").val();
        cropBoxData = cropper.getCropBoxData();
        canvasData = cropper.getCanvasData();
        cropper.destroy();
    });
    $(".modal-cropper").change(function(){
        var input = this;
        var url = $(this).val();
        var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
        if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg"))
        {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#image').attr('src', e.target.result);
            }
            filename = input.files[0].name;
            filetype = input.files[0].type;
            reader.readAsDataURL(input.files[0]);
            $("#modal-cropper").modal();
        }
    })
    $(".{{$classPreview}}").click(function(){
      $(".modal-cropper").click();
    })

    var button = document.getElementById('btn-save-cropper');
    button.onclick = function () {
        var croppedCanvas;
        var roundedCanvas;
        var roundedImage;
        if (!croppable) {
          return;
        }
        // Crop
        croppedCanvas = cropper.getCroppedCanvas();
        console.log(filename);
        croppedCanvas.toBlob(function (blob) {
            roundedCanvas = croppedCanvas;
            var srcImage = roundedCanvas.toDataURL();
            let file = new File([blob], filename, {type:filetype, lastModified:new Date().getTime()}, 'utf-8');
            var form = $(".{{$formId}}");
            $(".{{$fieldName}}").remove();
            $('<input>').attr({
                type: 'hidden',
                class: '{{$fieldName}}',
                name: '{{$fieldName}}[name]',
                value: filename
            }).appendTo('form');
            $('<input>').attr({
                type: 'hidden',
                class: '{{$fieldName}}',
                name: '{{$fieldName}}[type]',
                value: filetype
            }).appendTo('form');
            $('<input>').attr({
                type: 'hidden',
                class: '{{$fieldName}}',
                name: '{{$fieldName}}[image]',
                value: srcImage
            }).appendTo('form');
            $(".{{$classPreview}}").attr("src", srcImage);
            $("#modal-cropper").modal("hide");
        });
      };
});
</script>