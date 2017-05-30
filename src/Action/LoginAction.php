<?php

namespace App\Action;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Container;

class LoginAction
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Constructor
     *
     * @param \Slim\Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        // TODO: write login action code
        
        // 
        $request = $this->getInputs();
        
        // nếu tồn tại request user
        if(isset($request) && !empty($request))
        {   
            // mếu tồn tại cookie thì không cho tài khoản đăng nhập
            if(!isset($_COOKIE['lock']))
            {
                // kiểm tra nếu username trống và thuộc định dạng cho phép
                if(!empty($request['username']) && preg_match('/^[\w\'.-]{3,20}$/i', $request['username']))
                {
                    $username = $request['username'];
                }else
                {
                    $username = "";
                    $_SESSION['error_user'] = " Tên đăng nhập không thuốc định dang cho phép hoặc quá ngắn .";

                }
                // kiểm tra nếu passwor không trống và thuộc định dạng cho phép
                if(!empty($request['password']) && preg_match('/^[\w\'.-]{8,20}$/i', $request['password']))
                {
                    // hash password
                    $password = md5($request['password']);
                }else
                {
                    $password = "";
                    $_SESSION['error_passwor'] = "Mật khẩu không đúng định dạng ( ít nhất 8 ký tự ) .";

                }
                // kiểm tra  username và passwor  có tồn tại trong csdl
                $user = $this->getUserByUsername($username,$password);

                if(!isset($_SESSION['status']))
                {
                    $_SESSION['status'] = 0;
                }else{
                    $_SESSION['status']= $_SESSION['status'];
                }
                // nếu username và passwod đúng 
                
                if(!empty($user))
                {

                    $_SESSION['user'] = $user;
                    unset($_SESSION['status']);

                }else
                {
                    echo $_SESSION['status'] = $_SESSION['status'] +1;

                    if($_SESSION['status'] >5)
                    {
                        //setcookie('lock','lock',time()+900);
                    }
                    hea
                }  
                var_dump($user);

            }else{
                $_SESSION['time'] = "Xin lỗi tài khoản của bạn đã bị khóa .Hãy đăng nhập lại sau 15p";
            }
        

           //setcookie('lock','lock',time()-900);

    }else{

    }

}


    /**
     * @return array
     */
    protected function getInputs()
    {
        return (array) $this->request->getParsedBody();
    }

    /**
     * @param string $username
     * @return array|bool
     */
    protected function getUserByUsername($username,$password)
    {
        $users = $this->app->get('users')['form'];

        foreach ($users as $user) {
            if ($username === $user['username'] && $password == $user['password']) {
                return $user;

            }
        }

        return false;
    }

    /**
     * @param string $url
     * @param int    $statusCode
     * @return Response
     */
    protected function redirectTo($url, $statusCode = 301)
    {
        return $this->response->withStatus($statusCode)
        ->withHeader('Location', $url);
    }
}
