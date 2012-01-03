<?php

/**
 * An example of a webservice!!
 *
 * @author ballen
 */
class xmwswebservice extends ws_xmws {

    /**
     * This example will require the user to be fully authenticated before the response and any action is carried out.
     */
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
    
    /**
     * Just send a simple string response with the value of the data sent in the <content> tag at the end of 'Some more names are like:'
     */
    function AnotherDataReturnExample() {
        
        $customcontent = "Some more names are like: " . $this->wsdataarray['content'] . "";

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $customcontent);
        

        
        // As long as the API key matches (done automatically in the xmws class and if RequireUserAuth is declared and successfull then the response can now we sent.
        return $dataobject->getDataObject();
    }
    
    /**
     * Requesting functionality from the modules controller.ext.php!
     */
    function TestMe(){
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $this->currentmodule->wsReturnMyName($this->wsdataarray['content']));
        return $dataobject->getDataObject();
    }

}

?>
