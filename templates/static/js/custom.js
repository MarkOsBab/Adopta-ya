window.onload = function () {
    var fileUpload = document.getElementById("imagen");
    fileUpload.onchange = function () {
        if (typeof (FileReader) != "undefined") {
            var dvPreview = document.getElementById("dvPreview");
            dvPreview.innerHTML = "";
            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp)$/;
            for (var i = 0; i < fileUpload.files.length; i++) {
                var file = fileUpload.files[i];
                if (regex.test(file.name.toLowerCase())) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var img = document.createElement("IMG");
                        img.height = "150";
                        img.width = "150";
                        img.style.padding = "3px";
                        img.draggable = false;
                        img.src = e.target.result;
                        dvPreview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                } else {
                    alertify.alert('Error!', 'El archivo '+file.name+' no es válido');
                    dvPreview.innerHTML = "";
                    return false;
                }
            }
        } else {
            alert("Este navegador no soporta HTML5 FileReader.");
        }
    }
};

function fun(){ 
    document.getElementById('whyAdopta').value=''; 
    document.getElementById('imagen').value=''; 
    document.getElementById('title').value=''; 
} 

$(document).ready(function(){
    //var altoVentana = $('#Ventana').css('height').replace("px","");//usa este para que desaparezca despues de pasar el alto del div
    var altoVentana = 0;
    var posicionVentana = document.getElementById("backTop").offsetTop;
    $(window).scroll(function(event){
        var posicionScroll = $(this).scrollTop();
        if (posicionScroll < (parseInt(posicionVentana)+parseInt(altoVentana))){
            $("#backTop").css("display","none");
        } else {
            $("#backTop").css("display","");
        }

    });
});

$("#see_profile_image").on("click", function() {
   $('#imagepreview').attr('src', $('#imageresource').attr('src')); // here asign the image to the modal when the user click the enlarge link
   $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
});

function getFile() {
  document.getElementById("upfile").click();
}

function sub(obj) {
  var file = obj.value;
  var fileName = file.split("\\");
  document.getElementById("yourBtn").innerHTML = fileName[fileName.length - 1];
  document.change_profile_image.submit();
  event.preventDefault();
}

$(() => {
  $('button').on('click', e => {
    let spinner = $(e.currentTarget).find('span')
    spinner.removeClass('d-none')
    setTimeout(_ => spinner.addClass('d-none'), 2000)
  })
})

$(() => {
  $('a').on('click', e => {
    let spinner = $(e.currentTarget).find('span')
    spinner.removeClass('d-none')
    setTimeout(_ => spinner.addClass('d-none'), 2000)
  })
})