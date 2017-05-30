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

        $request = $this->getInputs();
       
        
        // nếu tồn tại request
        if(isset($request) && !empty($request))
        {   

            // mếu tồn tại cookie thì không cho tài khoản đăng nhập
            
            if(!isset($_COOKIE['lock'])){

                // kiểm tra nếu username trống và thuộc định dạng cho phép
                if(!empty($request['username']) && preg_match('/^[\w\'.-]{3,20}$/i', $request['username'])){

                    $username = $request['username'];

                }else{

                    $username = "";

                    $_SESSION['error_user'] = " Tên đăng nhập không thuốc định dang cho phép hoặc quá ngắn .";

                    // chuyển trang
                    $this->redirectTo('/login');

                }

                // kiểm tra nếu passwor không trống và thuộc định dạng cho phép
                if(!empty($request['password']) && preg_match('/^[\w\'.-]{8,20}$/i', $request['password'])){

                    // hash password
                    $password = md5($request['password']);
                    

                }else{

                    $password = "";
                    $_SESSION['error_passwor'] = "Mật khẩu không đúng định dạng ( ít nhất 8 ký tự ) .";

                    $this->redirectTo('/login');
                }


                if(isset($request['remember-me'])){

                    $remember = $request['remember-me'];
                   
                }


                // kiểm tra  username và passwor  có tồn tại trong csdl
                $user = $this->getUserByUsername($username,$password);

                // gán trạng thái đăng nhập của user
                if(!isset($_SESSION['status']))
                {
                    $_SESSION['status'] = 0;
                }else{
                    $_SESSION['status']= $_SESSION['status'];
                }


                // nếu username và passwod đúng 
                
                if(!empty($user))
                {
                    // lưu thông tin user vào biến session 
                    $_SESSION['user'] = $user;

                    unset($_SESSION['status']);

                    // lưu lại thông tin tài khoản khi người dùng kích vào Remember me
                    if(isset($remember))
                    {
                       setcookie('username',$request['username'],time()+86400);
                       setcookie('password',$request['password'],time()+86400);
                    }

                    $_SESSION['success'] = " Cám ơn bạn đã đăng nhập";

                    // chuyển trang 
                    
                    $this->redirectTo('/login');

                }else
                {
                    $_SESSION['status'] = $_SESSION['status'] +1;

                    // nếu user đăng nhập quá 5 lần
                    if($_SESSION['status'] >5)
                    {
                        // khóa sau 15p
                        setcookie('lock','lock',time()+900);

                    }
                    // chuyển trang
                    $this->redirectTo('/login');
                    
                }  
             
            }else{
                $_SESSION['time'] = "Xin lỗi tài khoản của bạn đã bị khóa .Hãy đăng nhập lại sau 15p";

                $this->redirectTo('/login');

            }
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
