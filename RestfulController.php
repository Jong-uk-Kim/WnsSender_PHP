<?php
include "define_interface.php";

class TestController  implements IController {
    protected $baseRoute="test";
    protected $HttpContext = null;

    function __construct(){
        $HttpContext = new HttpContext($_SERVER);
    }   

    function Execuate()
    {
        $jsonTest = Array([test]=>"test");
        $HttpContext->$Response.SendResponse(200, $jsonTest);
    }
}

class ErrorController
{
    protected $HttpContext = null;
    function SendError()
    {
        $HttpContext = new HttpContext();
        return $HttpContext->$Response.SendResponse("404", "page not found", "");
    }
}

function InjectService(){

        $rootPath = explode("/", $_SERVER["PATH_INFO"], 0);
		$rootPath = substr($_SERVER["PATH_INFO"], 1);
        echo($rootPath);

        try{
            $controlName = $rootPath+"Controller";
            echo($controlName);

            switch($controlName)
            {
                case "TestController":
                $controller = new TestController();
                $controller.Execuate();
                break;
            }
        }
        catch(Exception $e)
        {

        }
    }
?>
<<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
 <?php InjectService();?>
</body>
</html>