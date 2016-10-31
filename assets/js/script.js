$(function() {
    var maxImageWidth = 800,
        maxImageHeight = 800,
        dzPreviewItem = null,
        currCanvasFile = ""
        thisDropzone = null;

    $('.dropzone').on('click', '.dz-preview', function() {
        /*
        var imagePathSegments = $(this).find('.dz-image img').prop('src').split("/");
        var imagePath = imagePathSegments[imagePathSegments.length - 3] +
                        "/" +
                        imagePathSegments[imagePathSegments.length - 1];
        */

        dzPreviewItem = $(this);

        var imagePath = $(this).find('.dz-image img').data('dz-raw');
        currCanvasFile = imagePath.split("/")[1];

        $('#editable').prop('src', imagePath);
        $('.cropper-canvas img').prop('src', imagePath);
        $('.cropper-view-box img').prop('src', imagePath);
    });

    $('.modal').on('click', '.canvasGet', function() {
        var imageData = document.getElementById("myCanvas").toDataURL();

        $.ajax({
            type: "POST",
            url: "includes/processor.php",
            data: {"imageData":imageData,
                   "file":currCanvasFile,
                   "EndPoint":"image/saveCropped"},
            dataType: "json",
            success: function (result) {
                if(result.Code == "yep") {
                    if(dzPreviewItem != null || dzPreviewItem != undefined) {
                        dzPreviewItem.addClass("dz-cropped");

                        console.log("dz-cropped attribute added successfully...");
                    }
                    else {
                        console.log("dz-cropped attribute not added successfully...");
                    }

                    console.log("Cropped image saved successfully...");
                }
            }
        });
    });

    Dropzone.options.myDropzone = {
        /*
        accept: function(file, done) {

            if(file.type != "image/jpeg" && file.type != "image/png") {
                done("Error! Files of this type are not accepted");
            }
            else {
                done();
            }

            file.acceptDimensions = done;
            file.rejectDimensions = function() {
                done("Error! Accepted dimension is X by X px");
            };
        },
        */

        init: function() {
            //var thisDropzone = this;
            thisDropzone = this;

            /*
            thisDropzone.on("success", function(file, responseText) {
                console.log(responseText);
            });
            */

            thisDropzone.on("maxfilesexceeded", function(file) {
                thisDropzone.removeFile(file);
                console.log("maxfilesexceeded for: " + file.name);
            });

            thisDropzone.on("addedfile", function(file) {
                console.log(file.type);
                if(file.type != "image/jpeg" && file.type != "image/png") {
                    thisDropzone.removeFile(file);
                    console.log("invalid mime type for: " + file.name);
                }
                else {
                    console.log("valid mime type for: " + file.name);

                    //$('.dropzone .dz-preview .dz-image img');
                    $('.dropzone').find('.dz-preview .dz-image img').last().attr("data-dz-raw", "uploads/" + file.name);
                }
            });

            thisDropzone.on("removedfile", function(file) {
                if(thisDropzone.getAcceptedFiles().length != thisDropzone.options.maxFiles) {
                    console.log("removedfile with acceptedFiles if case: " + (parseInt(thisDropzone.getAcceptedFiles().length) - 1));

                    $.ajax({
                        type: "POST",
                        url: "includes/processor.php",
                        data: {"file":file.name,
                               "EndPoint":"image/remove"},
                        dataType: "json",
                        success: function (result) {
                            if(result.Code == "yep") {
                                console.log(file.name + " removed successfully...");
                            }
                        }
                    });
                }
                else {
                    console.log("removedfile with acceptedFiles else case: " + (parseInt(thisDropzone.getAcceptedFiles().length) - 1));
                }
            });

            /*
            thisDropzone.on("thumbnail", function(file) {
                if (file.width > maxImageWidth || file.height > maxImageHeight) {
                    file.rejectDimensions()
                }
                else {
                    file.acceptDimensions();
                }
            });
            */

            $.ajax({
                type: "POST",
                url: "includes/processor.php",
                data: {"EndPoint":"image/fetchAll"},
                dataType: "json",
                success: function (result) {
                    console.log(JSON.stringify(result));

                    if(result.Code == "yep") {
                        $.each(result.Data.images, function(key, value){
                            var mockFile = {name:value.name,
                                            size:value.size,
                                            type:value.type,
                                            accepted:true};

                            thisDropzone.emit("addedfile", mockFile);
                            thisDropzone.emit("thumbnail", mockFile, "uploads/small_thumb/" + value.name);
                            thisDropzone.emit("complete", mockFile);
                            thisDropzone.files.push(mockFile);

                            /*
                            thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                            thisDropzone.options.thumbnail.call(thisDropzone, mockFile, "uploads/" + value.name);
                            */
                        });

                        /*
                        thisDropzone.options.maxFiles = thisDropzone.options.maxFiles - totalFilesUploaded;
                        thisDropzone.options.acceptedFiles = totalFilesUploaded;
                        */

                        if(thisDropzone.getAcceptedFiles().length == thisDropzone.options.maxFiles) {
                            thisDropzone.element.classList.add("dz-max-files-reached");
                            console.log("foreach loop with acceptedFiles if case: " + (parseInt(thisDropzone.getAcceptedFiles().length) - 1));
                        }
                        else {
                            thisDropzone.element.classList.remove("dz-max-files-reached");
                            console.log("foreach loop with acceptedFiles else case: " + (parseInt(thisDropzone.getAcceptedFiles().length) - 1));
                        }
                    }
                },
                error: function (result) {
                    console.log(JSON.stringify(result));
                }
            });
        }
    }
});