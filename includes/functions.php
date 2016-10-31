<?php

/*#############################
* Developer: Mohammad Sharaf Ali
* Description: Helper methods
* Version: 1.0
* Date: 31-10-2016
*/#############################


#function to prepare fod processor response
function prepareProcessorResponse($code, $data) {
    $processorResponse['Code'] = $code;
    $processorResponse['Data'] = $data;

    return $processorResponse;
}

#function to decode request data
function decodeData($data) {
    #return data in associative array
    return json_decode($data, true);
}

#function to encode reponse data
function encodeData($data) {
    #return data as plain (without unicodes)
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}
