<?php
namespace App\Controller;

use Swoole\Client\CURL;

class Page extends \Swoole\Controller
{
    private $userTable = 'users';
    private $userPasswordSuffix = 'pretty';

    function index()
    {
        $this->session->start();
        if (!empty($_SESSION['isLogin']))
        {
            chatroom:
            $this->http->redirect('/page/chatroom/');
            return;
        }

        if (!empty($_GET['token']))
        {
            $curl = new CURL();
            $user = $curl->post($this->config['login']['get_user_info'] . '?token=' . urlencode($_GET['token']), [ 'ss' => '1']);
            if (empty($user))
            {
                login:
                $this->http->redirect($this->config['login']['passport'] . '?return_token=1&refer=' . urlencode($this->config['webim']['server']['origin']));
            }
            else
            {
                $_SESSION['isLogin'] = 1;
                $_SESSION['user'] = json_decode($user, true);
                goto chatroom;
            }
        }
        else
        {
            goto login;
        }
    }

    function chatroom()
    {
        $this->session->start();
        if (empty($_SESSION['isLogin']))
        {
            $this->http->redirect('/page/index');
            return;
        }
        $user = $_SESSION['user'];
        $this->assign('user', $user);
        $this->assign('debug', 'true');
        $this->display('page/chatroom.php');
    }

    function signup()
    {
        if ($this->is_post()) {
            $this->db->insert([
                'name' => htmlspecialchars($_POST['name']),
                'email' => htmlspecialchars($_POST['email']),
                'password' => $this->hashPassword($_POST['password']),
                'remember_token' => $this->userPasswordSuffix,
                'created_at' => date("Y-m-d H:i:s"),
            ], $this->userTable);
            return $this->json([], 0 , '注册成功');
        } else {
            $this->display('page/signup.php');
        }
    }

    function login()
    {
        if ($this->is_post()) {
            if (empty($_POST['name']) || empty($_POST['password'])) {
                return $this->json([], -1 ,'用户名或密码为空');
            }
            $sql = sprintf("SELECT * FROM %s WHERE name='%s' AND password='%s'",
                $this->userTable, htmlspecialchars($_POST['name']), $this->hashPassword($_POST['password']));
            $user = $this->db->query($sql);
            $user = $user->fetch();
            if (!$user) {
                return $this->json([], -1, '账号或密码错误');
            }
            $user = [
                'id' => $user['id'],
                'email' => $user['email'],
                'nickname' => $user['name'],
                'avatar' => $user['avatar'],
            ];

            $this->session->start();
            $_SESSION['isLogin'] = 1;
            $_SESSION['user'] = $user;
            return $this->json($user, 0 ,'登录成功');
        } else {
            $this->session->start();
            if (!empty($_SESSION['isLogin']))
            {
                $this->http->redirect('/page/chatroom/');
                return;
            }
            $this->display("page/login.php");
        }
    }

    public function  logout()
    {
        $this->session->start();
        session_unset();
        session_destroy();

        $this->http->redirect("/page/login");
    }

    /**
     * 用flash添加照片
     */
    function upload()
    {
        if ($_FILES)
        {
            global $php;
            $php->upload->thumb_width = 136;
            $php->upload->thumb_height = 136;
            $php->upload->thumb_qulitity = 100;
            $up_pic = $php->upload->save('Filedata');
            if (empty($up_pic))
            {
                echo '上传失败，请重新上传！ Error:' . $php->upload->error_msg;
            }
            echo json_encode($up_pic);
        }
        else
        {
            echo "Bad Request\n";
        }
    }


    /**
     *@todo: 判断是否为post
     */
    function is_post()
    {
        return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='POST';
    }

    /**
     *@todo: 判断是否为get
     */
    function is_get()
    {
        return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='GET';
    }

    function is_ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH'])=='XMLHTTPREQUEST';
    }

    /**
     *@todo: 判断是否为命令行模式
     */
    function is_cli()
    {
        return (PHP_SAPI === 'cli' OR defined('STDIN'));
    }

    private function hashPassword($password)
    {
        return md5(trim($password) . $this->userPasswordSuffix);
    }
        /*array (
                'id' => '11195',
                'username' => 'QQ????',
                'usertype' => '0',
                'nickname' => '随风而逝',
                'realname' => '',
                'intro' => '',
                'sex' => '1',
                'email' => '',
                'mobile' => '',
                'mobile_verification' => '0',
                'php_level' => '0',
                'skill' => '',
                'company' => '',
                'blog' => '',
                'birth_year' => '1990',
                'work_year' => '0',
                'avatar' => 'http://qzapp.qlogo.cn/qzapp/221403/70F26C325A0E65B2B5B65BF04ADE328A/100',
                'education' => '',
                'certificate' => '',
                'province' => '江西',
                'city' => '九江',
                'active_days' => '0',
                'vip' => '0',
                'gold' => '0',
                'weixin_uid' => '',
                'qq_uid' => '70F26C325A0E65B2B5B65BF04ADE328A',
                'weibo_uid' => '',
                'login_times' => '0',
            );*/

}