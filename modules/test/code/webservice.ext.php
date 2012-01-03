<?php

/**
 * An example of a webservice!!
 *
 * @author ballen
 */
class xmwswebservice extends ws_xmws {

    

    function StaticDataReturnExample() {
        // We first grab all the data from the requst like so
        $request_data = $this->RawXMWSToArray($this->wsdata);
        
        $customcontent = "This is just some standard text that I'm sending back in my response! You said your name was: " . $request_data['content'] . "";

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', ''); // Can be left blank if you just want to use the standard 'success' code (1101)
        $dataobject->addItemValue('content', $customcontent);
        
        // If this is declared 'RequireUserAuth()'then it will check that the user is authenticated first and if successful will contiue otherwise the request will fail.
        $this->RequireUserAuth();
        
        // As long as the API key matches (done automatically in the xmws class and if RequireUserAuth is declared and successfull then the request can now be processed!
        return $dataobject->getDataObject();
    }
    
    function AnotherDataReturnExample() {
        
        $request_data = $this->RawXMWSToArray($this->wsdata);
        
        $customcontent = "Some more names are like: " . $request_data['content'] . "";

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $customcontent);
        

        
        // As long as the API key matches (done automatically in the xmws class and if RequireUserAuth is declared and successfull then the response can now we sent.
        return $dataobject->getDataObject();
    }

}

?>
