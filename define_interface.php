<?php
// defines
interface IController{
    function Execute();
}

interface IHttpRequest{
    function SetParams($url, $method, $params);
    function ParseBody($key);
    function GetMethodType();
    function GetRequestUrl();
    function GetHeaders();
    function IsValidParameter();
}
interface IHttpResponse{
    function SendResponse(int $code, string $description, Array $message);
}

abstract class AHttpRequest{
    protected $Headers=array();
    protected $Method="";
    protected $Url="";
    protected $Params;
}

abstract class AHttpResponse {
    public $Headers=array();
    public $ResposeCode = -1;
    public $ResponseDescripsion ="";
}

class HttpRequest extends AHttpRequest implements IHttpRequest{
    function SetParams($url, $method, $params)
    {
        $this->Url = $url;
        $this->Method = $method;
        $this->Params=$params;
    }

    function ParseBody($key){
        if (array_key_exists($key, $this->Params)){
            return $this->Params[$key];
        }
        return null;
    }

    function IsValidParameter()
    {
        return "testIsValidParameter";
    }

    function GetMethodType(){
        return $this->Method;
    }

    function GetRequestUrl(){
        return $this->Url;
    }

    function GetHeaders(){
        return $this->Headers;
    }
}

class HttpResponse extends AHttpResponse implements IHttpResponse{
    function SendResponse(int $code, string $description, Array $message = null): string{
        http_response_code($code);
        header("Content-Type: application/json");
        
        if (!$message){
            return "";
        }
        return json_encode($message);
    }
}

class HttpContext {
    public $Request;
    public $Response;

    function __construct(string $url)
    {
        $request = new HttpRequest();
        $response = new HttpResponse();  

        if ($_SERVER["REQUEST_METHOD"] === "GET")
        {
            $request->SetParams($url, "GET", $_GET);
        }
        elseif($_SERVER["REQUEST_METHOD"] === "POST")
        {
            $request->SetParams($url, "POST", $_POST);
        }

        $this->Request = $request;
        $this->Response = $response;
    }
}
?>
