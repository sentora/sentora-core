<?php

/**
 * An example of a webservice!!
 *
 * @author ballen
 */
class webservice extends ws_xmws {

    

    function StaticDataReturnExample() {
        // Lets request that the user must be authenticated to request this!
        //$this->RequireUserAuth($raw_request);
        echo $this->wsdata;
        $array_request = $this->RawXMWSToArray($this->wsdata);
        print_r($array_request);
        
        $customcontent = "This is just some standard text that I'm sending back in my response! You said your name was: " . $array_request['content'] . "";

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('responsecode', '4328');
        $dataobject->addItemValue('content', $customcontent);

        // If the RequireUserAuth is valid then we can carry on..
        echo $this->SendResponse($dataobject->getDataObject());
    }

}

?>
