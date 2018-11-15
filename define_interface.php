<?php
// defines
interface IController{
    function Execuate();
}

interface IHttpRequest{
    function SetParams(string $url, object $request, string $method="");
}
interface IHttpResponse{
    function SendResponse(int $code, string $description, string $message);
}

abstract class AHttpRequest implements IHttpRequest{
    protected $Message="";
    protected $Body="";
    protected $Headers=array();
    protected $Method="";
    protected $Url="";

    abstract protected function ParseBody();
    abstract protected function IsValidParameter();
}

abstract class AHttpResponse implements IHttpResponse{
    protected $Headers=array();
    protected $ResposeCode = -1;
    protected $ResponseDescripsion ="";
}

class HttpRequest extends AHttpRequest{
    public function SetParams(string $url, object $request, string $method="")
    {
        $this->$Url = $url;

        if(method === ""){
            throw new Exception();
        }

        $this->$Method = $method;
        switch($this->$Method)        
        {
            case "POST":
            break;
            case "GET":
            break;
        }
    }

    function ParseBody(){
        return "testParseBody";
    }

    function IsValidParameter()
    {
        return "testIsValidParameter";
    }
}

class HttpResponse extends AHttpResponse{
    function SendResponse(int $code, string $description, string $message){
        http_response_code($code);
        header("Content-Type: application/json");
        return json_encode($message);
    }
}

class HttpContext {
    public $Request = null;
    public $Response = null;

    function __construct(object $globalServer)
    {
        $Request = new HttpRequest();
        $Response = new HttpResponse();    

        if ($globalServer["REQUEST_METHOD"] === "GET")
        {
            echo($_REQUEST->$_GET);
            $globalServer.SetParams(explode("/", substr($globalServer["PATH_INFO"], 1)), $_REQUEST->$_GET, "GET");
        }
        elseif($globalServer["REQUEST_METHOD"] === "POST")
        {
            $globalServer.SetParams(explode("/", substr($globalServer["PATH_INFO"], 1)), $_REQUEST->$_POST, "POST");
        }
    }
}
?>
