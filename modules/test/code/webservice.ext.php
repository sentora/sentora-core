<?php

/**
 * An example of a webservice!!
 *
 * @author ballen
 */
class webservice extends ws_xmws {

    

    function StaticDataReturnExample() {
        
        $request_data = $this->RawXMWSToArray($this->wsdata);
        
        $customcontent = "This is just some standard text that I'm sending back in my response! You said your name was: " . $request_data['content'] . "";

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('responsecode', '4328');
        $dataobject->addItemValue('content', $customcontent);
        
        // If this is declared 'RequireUserAuth()'then it will check that the user is authenticated first and if successful will contiue otherwise the request will fail.
        $this->RequireUserAuth();
        
        // As long as the API key matches (done automatically in the xmws class and if RequireUserAuth is declared and successfull then the request can now be processed!
        $this->SendResponse($dataobject->getDataObject());
    }

}

?>
