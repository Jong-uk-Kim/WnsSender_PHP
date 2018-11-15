<?php
include "define_interface.php";

class TestController implements IController{
    protected $baseRoute="test";
    protected $HttpContext = null;

    function __construct($url){
        $this->HttpContext = new HttpContext($url);
    }

    function Execute()
    {
        try
        {
            // Execute api process

            // setting up response

            // execute send response on the HttpResponse::sendresponse function
            return $this->HttpContext->Response->SendResponse(200, "test", array('test'=>'test'));
        }
        catch (Exception $e)
        {
            return $this->HttpContext->Response->SendResponse(500, "catched unknown error");
        }
    }

    function RegisterChannel()
    {

    }
}

class ErrorController implements IController
{
    protected $HttpContext = null;
    function __construct()
    {
        $this->HttpContext = new HttpContext("null");
    }

    function Execute()
    {
        return $this->HttpContext->Response->SendResponse("404", "page not found");
    }
}

function InjectService(){

    try{
            $absolutePath = substr($_SERVER["PATH_INFO"], 1);
            $rootPath = explode("/", $absolutePath);
            $subUrl = substr($absolutePath, strlen($rootPath[0]));

            echo $absolutePath;
            echo $rootPath[0];
            echo $subUrl;

            $controller = null;
            switch($rootPath[0])
            {
                case "Test":
                    $controller = new TestController($subUrl);
                break;

                case "Test2":
                    
                break;
                
                default:
                $controller = new ErrorController();
                break;

            }

            echo $controller->Execute();
        }
        catch(Exception $e)
        {
            
        }
    }

    InjectService();
?>
