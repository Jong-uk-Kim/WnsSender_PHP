<?php
include "define_interface.php";

class PushController implements IController{
    protected $HttpContext = null;

    // function mapping table
    // if received request path 'echo', then execute PushEchoMessage
    protected static $Mapper = array('echo' => "PushEchoMessage" );

    /*
    setting up uwp project secret
    reference: https://msdn.microsoft.com/library/windows/apps/hh465407
               https://docs.microsoft.com/ko-kr/windows/uwp/design/shell/tiles-and-notifications/windows-push-notification-services--wns--overview
    */
    private static $WnsEnvironment = array();


    function __construct($url){
        $this->HttpContext = new HttpContext($url);
    }

    function Execute()
    {
        try
        {
            $request = substr($this->HttpContext->Request->GetRequestUrl(), 1);

            if (!$request)
            {
                return self::index();
            }

            // Execute api process
            $func=self::$Mapper[$request];
            return self::$func();
        }
        catch (Exception $e)
        {
            return $this->HttpContext->Response->SendResponse(500, "catched unknown error");
        }
    }

    function index()
    {
        return $this->HttpContext->Response->SendResponse(200, "processed succeded", null);
    }

    // this function is testing to wns push when register channel
    function PushEchoMessage()
    {
        $channel = $this->HttpContext->Request->ParseBody('channel');

        if (!$channel){
            return $this->HttpContext->Response->SendResponse(400, "channel parameter can't be null", null);
        }

        // get grant from wns server
        
        // get authorization from WNS
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://login.live.com/accesstoken.srf');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // if has verify certificate then delete this code
        // this option disable ssl verify check
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(self::$WnsEnvironment));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type:application/x-www-form-urlencoded", "Aceept:application/json"));

        $result = curl_exec($curl);
        curl_close($curl);

        if ($result === false)
        {
            $errorMessage = curl_errno($curl)."/".curl_error($curl);
            return $this->HttpContext->Response->SendResponse(500, "failed to interal operation", $errorMessage);
        }

        $accessToken = json_decode($result, true);

        // request window notification push
        $curl = curl_init();

        /*
         notification paylod (wns/raw)
         if you using wns/raw then must be check your client notification process logic
         reference: https://msdn.microsoft.com/library/windows/apps/hh465435
        */ 
        $content=json_encode(array('type'=>'test', 'target'=>'testTarget', 'title'=> 'noti_test', 'message'=>'hello wns'));
        $headers =  array(
            "Content-Type:application/octet-stream",
            'Content-Length:'.strlen($content),
            'X-WNS-Type:wns/raw',
            'Authorization:Bearer '.$accessToken['access_token']);

        var_dump($headers);
        var_dump($accessToken['access_token']);
        var_dump('   lenth:  '.strlen($content));

        curl_setopt($curl, CURLOPT_URL, $channel);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // if has verify certificate then delete this code
        // this option disable ssl verify check
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

        var_dump($curl);

        $result = curl_exec($curl);

        $errorMessage = curl_errno($curl)."/".curl_error($curl);

        var_dump($errorMessage);
        var_dump(curl_getinfo($curl));
        curl_close($curl);

        

        if ($result === false)
        {
            $errorMessage = curl_errno($curl)."/".curl_error($curl);
            return $this->HttpContext->Response->SendResponse(500, "failed to interal operation", $errorMessage);
        }


        return $this->HttpContext->Response->SendResponse(200, "Succeded", null);
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

            $controller = null;
            switch($rootPath[0])
            {
                case "Test":
                    $controller = new PushController($subUrl);
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
