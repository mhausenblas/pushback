<?php
//function show( $v, $dumb_mode = false ) {
//	echo '<pre style="text-align:left;">';
//	if( !$dumb_mode ) {
//		switch( gettype( $v ) ) {
//			case 'array':
//				print_r($v);
//				break;
//			default:
//				var_export($v);
//		}
//	} else {
//		var_dump($v);
//	}
//	echo '</pre>';
//}


class Tools{

	public static function show($v, $dumb_mode = false){
		echo '<pre style="text-align:left;">';
		if( !$dumb_mode ) {
			switch( gettype( $v ) ) {
				case 'array':
					print_r($v);
					break;
				default:
					var_export($v);
			}
		} else {
			var_dump($v);
		}
		echo '</pre>';
	}
}


class Array_walk_recursive3 {

    public $status;
    public $input;
    private $depth = -1;
    private $userdata, $funcname;
    private $callbackresult = array(); 
    private $debug; 
    
    public function __construct($input, $funcname,$debug=false,$userdata = "") {
        $this->input = $input;
        $this->funcname = $funcname;
        $this->userdata = $userdata;
        $this->status = $this->array_walk_recursive($this->input);
        $this->debug = $debug;
    }
   
    public function getCallbackResult(){
    	return $this->callbackresult;
    }
    
    private function test_print(&$value, &$key)
    {
    	// we are passing value key by reference and can edit them here if needed 
//        echo str_repeat("  ", $this->depth)."$key holds ";
//        if (!is_array($value)) {
//            echo $value;
//
//            //if (trim($value) == "banana") {
//            ///    $value = "cherry";
//            //   $key = "c";
//            //}
//        }
//        echo "\n";
        
    	if( $this->isInMappingArray($key)===true ){
			global $mappingRDFid2JIRAid; 
    		$val = $this->input[$key.'.val']['http://www.w3.org/1999/02/22-rdf-syntax-ns#value'][0]['value'];
    		$jiraId = $mappingRDFid2JIRAid[$key]; 
			
    		if($this->debug){
    			echo '<br />extracted from graph for key: '.$key.' value: '.$val.' jiraId: '.$jiraId.'<br />';
    		}
			
    		// from this I can construct the SOAP call to update an issue
    	
    		$this->callbackresult[]=array('id'=>$jiraId , 'values'=>array($val));
    	}
    }
   
    private function isInMappingArray($k){
	global $mappingRDFid2JIRAid;
	foreach ($mappingRDFid2JIRAid as $key => $value){
		if($key===$k  ){
			return true;
		}
	}
	return false;
}
    
    
    private function array_walk_recursive(&$input) {
        $funcname = array(&$this, $this->funcname);
       if (!is_callable($funcname)) {

           return false;
       }
   
       if (!is_array($input)) {
           return false;
       }

        $this->depth++;
   
       foreach (array_keys($input) AS $keyIdx => $key) {
            $saved_value = $input[$key];
            $saved_key = $key;
            call_user_func_array($funcname, array(&$input[$saved_key], &$key));
   
            if ($input[$saved_key] !== $saved_value || $saved_key !== $key) {
                $saved_value = $input[$saved_key];

                unset($input[$saved_key]);
                $input[$key] = $saved_value;
            }
           if (is_array($input[$key])) {
                if (!$this->array_walk_recursive($input[$key], $funcname)) return false;
                $this->depth--;
           }
       }   
       return true;
    }

}


class JiraSoapClient{
	private $username= 'admin';
	private $password='admin';
	private $wsdl = "http://localhost:8080/rpc/soap/jirasoapservice-v2?wsdl";
	private $client;
	private $debug;
	private $debugDumpFunction = false;
	private $debugDumpTypes = false;
	
  	public function __construct($username, $password, $wsdl,$debug=false) {
        $this->username = $username;
        $this->password = $password;
        $this->wsdl = $wsdl;
    	$this->debug=$debug;
        
	  	try {
		
		$this->client = new SoapClient($this->wsdl);
		$this->printDebug($this->client);
		
		if($this->debugDumpTypes){
			echo("\nDumping client types:\n");
		    Tools::show($this->client->__getTypes());
		}
		if($this->debugDumpFunction){
		    echo("\nDumping client object functions:\n");
		    Tools::show($this->client->__getFunctions());
		}
  	
	  	
	  	}catch (Exception $e) {
			print_r($e);
		}
	}
	
	public function updateIssue($issueObjectArray){
		//find and get the id 
		
		try{
		$jiraInfo[jiraIssueId]='DERIPUSHBACK-1';
		
		$N = count($issueObjectArray);
		for($i=0;$i<$N;$i++){
			if($issueObjectArray[$i]['id']=='id'){
				$jiraInfo[jiraIssueId] = $issueObjectArray[$i]['values'][0]; 		
			}
		}
		
		// remove the id from array this is not neccesary
		$arrayOf_tns1_RemoteFieldValue1 = $issueObjectArray;
		
		//call jira
		$createCommentToken = $this->client->login($this->username,$this->password);
		$this->printDebug($createCommentToken);
		
		$emoteIssue = $this->client->updateIssue($createCommentToken, $jiraInfo[jiraIssueId],$arrayOf_tns1_RemoteFieldValue1);
		
		$this->printDebug("updateIssue result");
		$this->printDebug($emoteIssue);

		$this->client->logout();
		}catch (Exception $e){
			return $e->getMessage();
		}
		return true;
	}
	
	private function printDebug($object){
		if($this->debug){
			echo '<br />============================<br />';
				Tools::show($object);
			echo '<br />============================<br />';
		}
	}
}

?>