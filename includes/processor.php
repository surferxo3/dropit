<?php

/*#############################
* Developer: Mohammad Sharaf Ali
* Description: Middleware to handle request(s) from client-end.
* Version: 1.0
* Date: 31-10-2016
*/#############################

require_once 'constants.php';
require_once 'functions.php';
require_once 'MImage.php';

use \MSharaf\MImage as MImage;

if(isset($_POST) && count($_POST) > 0) {
    $image = new MImage();

    if (isset($_POST['EndPoint']) && $_POST['EndPoint'] == 'image/store') {
        if (!empty($_FILES)) {
            $imageCollection = null;

            $tempFile = $_FILES['file']['tmp_name'];
            $modifiedName = $_FILES['file']['name'];
            $targetFile = $uploadTargetFolder . $modifiedName;
            $targetFileThumb = $thumbTargetFolder . $modifiedName;

            move_uploaded_file($tempFile, $targetFile);

            $image->load($targetFile);
            $image->resizeToHeight(120);
            $image->save($targetFileThumb);

            $imageObj['name'] = $modifiedName;
            $imageObj['size'] = filesize($targetFile);
            //$imageObj['path'] = $targetFile;
            $imageObj['type'] = $image->getMimeType($targetFile);
            $imageCollection['images'] = $imageObj;

            $response = prepareProcessorResponse($successResponse, $imageCollection);
        } else {
            $response = prepareProcessorResponse($failureResponse, null);
        }
    } else if (isset($_POST['EndPoint']) && $_POST['EndPoint'] == 'image/fetchAll') {
        $imageCollection = null;
        $validFileFlag = false;

        $files = scandir($uploadTargetFolder);

        if ($files !== false) {
            foreach ($files as $file) {
                if (!in_array($file, $ignoreFilesOrFolders)) {
                    $targetFile =  $uploadTargetFolder . $file;

                    $imageObj['name'] = $file;
                    $imageObj['size'] = filesize($targetFile);
                    //$imageObj['path'] = $targetFile;
                    $imageObj['type'] = $image->getMimeType($targetFile);
                    $imageCollection['images'][] = $imageObj;

                    $validFileFlag = true;
                }
            }

            if ($validFileFlag) {
                $response = prepareProcessorResponse($successResponse, $imageCollection);
            } else {
                $response = prepareProcessorResponse($failureResponse, null);
            }
        } else {
            $response = prepareProcessorResponse($failureResponse, null);
        }
    } else if (isset($_POST['EndPoint']) && $_POST['EndPoint'] == 'image/saveCropped') {
        $targetFile = $cropTargetFolder. $_POST['file'];

        $image->decodeImageBase64($_POST['imageData'], $targetFile);

        $response = prepareProcessorResponse($successResponse, null);
    } else if (isset($_POST['EndPoint']) && $_POST['EndPoint'] == 'image/remove') {
        $targetFile = $uploadTargetFolder. $_POST['file'];
        $targetFileThumb = $thumbTargetFolder. $_POST['file'];
        $targetFileCrop = $cropTargetFolder. $_POST['file'];

        $image->removeFile($targetFile);
        $image->removeFile($targetFileThumb);
        $image->removeFile($targetFileCrop);

        $response = prepareProcessorResponse($successResponse, null);
    } else {
        $response = prepareProcessorResponse($failureResponse, null);
    }
} else {
    $response = prepareProcessorResponse($failureResponse, null);
}

echo encodeData($response);
 