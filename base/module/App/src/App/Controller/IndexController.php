<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace App\Controller;
use Zend\View\Model\ViewModel;
use Zend\Validator\StringLength;
use Zend\Validator\Regex;

//use Zend\Mail\Transport\Sendmail as SendmailTransport;

use App\Controller\AbstractController;
use App\Model\Entity\UserEntity;
use App\Model\Entity\RestrictLoginEntity;
use App\Model\Table\TemporaryIdTable;


use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;
class IndexController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * top input auth information page
     * @return ViewModel
     */
    public function indexAction()
    {
        $e = $this->getEvent();
        $storage = $e->getApplication()->getServiceManager()->get('Session\Storage\DbSessionStorage');
        $storage->getSessionStorage()->regenerateId(true);

        $this->layout('layout/index');

        // get value from container
        $login_id = $this->container()->get('login_id');
        if ($login_id) {
            $this->container()->clear('login_id');
        }

        $err_msg = $this->container()->get('err_msg');
        if ($err_msg) {
            $this->container()->clear('err_msg');
        }
        else {
            $fm = $this->flashMessenger();
            $err_msg = gv('0', $fm->getMessages());
        }

        $user_no = $this->container()->get('user_no');
        if ($user_no) {
            $this->container()->clear('user_no');
            $this->container()->set('compulsion', $user_no);
        }
        $forget =  $this->container()->get('forget');
        if ($forget) {
            $this->container()->clear('forget');
        }

        // create token_id
        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);

        $values = array(
            'token_id' => $token_id,
            'login_id' => $login_id,
            'logout_link' => ($user_no ? true : false),
            'remind' => $forget,
            'err_msg' => $err_msg,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/index/index.phtml');
        return $view;
    }

    /**
     * login auth
     */
    public function loginAction()
    {
        $e = $this->getEvent();
        $storage = $e->getApplication()->getServiceManager()->get('Session\Storage\DbSessionStorage');
        $storage->getSessionStorage()->regenerateId(true);

        $this->layout('');

        $id = $this->params()->fromPost('login_id');
        $pw = $this->params()->fromPost('login_pw');
        $key = $this->params()->fromPost('key_id');
        $token_id = $this->params()->fromPost('token_id');
        $sess_token_id = $this->container()->get('token_id');
        $uri = $this->container()->get('uri');
        $this->container()->clear('token_id');

        // when can't get require item
        if (!$id || !$pw || !$key || !$token_id || !$sess_token_id
                || $token_id != $sess_token_id) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'index',
            ));
        }

        $user = new UserEntity();
        $row = $user->db()->getLoginInfo($id, $key);

        $success = false;
        $ngCount = false;
        $message = null;
        $toRoute = array('controller' => 'index');
        if (!$row || !$row->user_no) {
            $message = "Unknown account";
//            $message = "アカウントは不明です。";
        }
        else if (LOGIN_FAILED_COUNT && LOGIN_FAILED_COUNT <= $row->ng_count) {
            $message = "Account is locked";
//            $message = "アカウントはロックされています。";
        }
        else if (!$row->login_pw || md5($row->login_pw . $token_id) != $pw) {
            $message = "Failed";
//            $message = "ログインに失敗しました。";
            $this->container()->set('forget', true);
            $ngCount = true;
        }
        else if ($row->initial_flag || EXPIRE_PW < $row->past_day) {
            $message = "Please change password";
//            $message = "パスワードの変更が必要です。";
            $toRoute = array(
                'controller' => 'index',
                'action' => 'change-pw',
            );
        }
        else {
            $success = true;
        }

        // save login error number
        if ($ngCount){
            $user->db()->insertLoginFailed($row->user_no);
        } 

        // check duplication login & limit duplication login data INSERT
        if (RESTRICT_LOGIN && $success) {
            
            $user->db()->deleteLocked($row->user_no);
            
            $restrict = new RestrictLoginEntity();
            // cleaning
            $restrict->db()->clean();

            $ret = $restrict->restrictCheck($row->user_no);
            if ('error' === $ret) {
                $this->container()->set('user_no', $row->user_no);
                $message = "Not logout";
//                $message = "ログアウトしていません。";
                $success = false;
            }
            else if (!$ret) {
                $message = "Failed";
//                $message = "ログインに失敗しました。";
                $success = false;
            }
        }

        // save error message
        if ($message) {
            $this->flashMessenger()->addMessage($message);
        }

        // failure auth
        if (!$success) {
            $this->container()->set('login_id', $id);
            return $this->redirect()->toRoute('app', $toRoute);
        }

        $this->container()->setContainer('user_auth');
        $this->container()->set('user_no', $row->user_no);
        $this->container()->set('user_name', $row->user_name);
        $this->container()->set('branch_no', $row->branch_no);
        $this->container()->set('branch_name', $row->branch_name);
        $this->container()->set('timezone', $row->timezone);
        $this->container()->set('lang_id', $row->lang_id);
        $this->container()->set('resource_id', $row->resource_id);
        $this->container()->set('approver', $row->approver);
        $this->container()->set('admin', $row->admin);

        setcookie('lang_id', $row->lang_id, time() + (60 * 60 * 24 * 30), '/', BASE_DOMAIN);
        setcookie('resource_id', $row->resource_id, time() + (60 * 60 * 24 * 30), '/', BASE_DOMAIN);
        setcookie('timezone', $row->timezone, time() + (60 * 60 * 24 * 30), '/', BASE_DOMAIN);

        $this->container()->clean('index');
        
        if ($uri) {
            return $this->redirect()->toUrl($uri);
        }
        else {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'menu',
                'action' => 'top',
            ));
        }

        $view = new ViewModel();
        $view->setTerminal(true);
        return $view;
    }

    /**
     * logout
     */
    public function logoutAction()
    {
        if (RESTRICT_LOGIN) {
            $user_no = $this->container()->get('compulsion');
            if ($user_no) {
                $this->container()->clear('user_no');
            }
            else {
                $this->container()->setContainer('user_auth');
                $user_no = $this->container()->get('user_no');
            }

            if ($user_no) {
                $user = new UserEntity();
                $user->db()->deleteSession($user_no);
            }
        }

        $e = $this->getEvent();
        $storage = $e->getApplication()->getServiceManager()->get('Session\Storage\DbSessionStorage');
        $storage->getSessionStorage()->destroy();

        $this->layout('');
        $view = new ViewModel();
        $view->setTerminal(true);
        return $this->redirect()->toUrl(BASE_URL);
    }

    /**
     * password change page
     * @return ViewModel
     */
    public function changePwAction()
    {
        $this->layout('layout/index');

        // create token_id
        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);

        $fm = $this->flashMessenger();
        $values = array(
            'token_id' => $token_id,
            'login_id' => $this->container()->get('login_id'),
            'err_msg' => gv('0', $fm->getMessages()),
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/index/change-pw.phtml');
        return $view;
    }

    /**
     * change password processing
     * @return ViewModel
     */
    public function updatePwAction()
    {
        $id = $this->params()->fromPost('login_id');
        $pw = $this->params()->fromPost('login_pw');
        $key = $this->params()->fromPost('key_id');
        $new_pw = $this->params()->fromPost('new_pw');
        $token_id = $this->params()->fromPost('token_id');
        $sess_token_id = $this->container()->get('token_id');
        $this->container()->clear('token_id');

        // when can't get require item
        if (!$id || !$pw || !$key || !$new_pw || !$token_id || !$sess_token_id
                || $token_id != $sess_token_id) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'index',
            ));
        }

        $this->container()->set('login_id', $id);

        $user = new UserEntity();
        $row = $user->db()->getLoginInfo($id, $key);

        $success = false;
        $ngCount = false;
        $message = null;
        if (!$row->user_no) {
            $message = "Unknown account";
//            $message = "アカウントは不明です。";
        }
        else if (LOGIN_FAILED_COUNT && LOGIN_FAILED_COUNT <= $row->ng_count) {
            $message = "Account is locked";
//            $message = "アカウントはロックされています。";
        }
        else if (!$row->login_pw || md5($row->login_pw . $token_id) != $pw) {
            $message = "Failed";
//            $message = "認証に失敗しました。";
            $ngCount = true;
        }
        else if ($id == $new_pw) {
            $message = "Don't use same password as ID";
//            $message = "ログインIDと同じパスワードは使用できません。";
        }
        else {
            $success = true;
        }

        // save login error number
        if (!$success && $ngCount) {
            $user->db()->insertLoginFailed($row->user_no);
        }

        if ($success) {
            $ret = $user->db()->checkLoginPw($row->user_no, $new_pw);

            if ($ret) {
                $message = "Don't use same password as past one.";
//                $message = "過去利用したパスワードは設定出来ません。";
                $success = false;
            }
        }
        $tmp_message = "Confirm password policy\n";
//        $tmp_message = "パスワードポリシーに違反しています。\n";

        if ($success) {
            $validate = new StringLength();
            $validate->setOptions(array(
                'min' => (int) PW_MIN_LENGTH,
                'max' => (int) PW_MAX_LENGTH,
                'encoding' => 'UTF-8',
            ));
            $ret = $validate->isValid($new_pw);
            if (!$ret) {
                $message = $tmp_message . current($validate->getMessages());
                $success = false;
            }
        }

        if ($success && strlen(PW_REGEX_PATTERN)) {
            unset($validate);
            $validate = new Regex(array(
                'pattern' => PW_REGEX_PATTERN,
            ));
            $ret = $validate->isValid($new_pw);
            if (!$ret) {
                $message = $tmp_message . current($validate->getMessages());
                $success = false;
            }
        }

        // save error message & redirect to input form
        if ($message || !$success) {
            $this->flashMessenger()->addMessage($message);
            return $this->redirect()->toRoute('app', array(
                'controller' => 'index',
                'action' => 'change-pw',
            ));
        }

        $ret = $user->changePw($row->user_no, $new_pw, 0);
        $message .= 'Change password '
//        $message .= 'パスワードの変更に '
                . ($ret ?  'success' : 'failed');
//                . ($ret ?  '成功しました。' : '失敗しました。');
        $this->flashMessenger()->addMessage($message);
        if ($ret) {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'index',
            ));
        }
        else {
            return $this->redirect()->toRoute('app', array(
                'controller' => 'index',
                'action' => 'change-pw',
            ));
        }

        $view = new ViewModel();
        $view->setTerminal(true);
        return $view;
    }

    /**
     * re-issue password1
     * @return ViewModel
     */
    public function remindAction()
    {
        $this->layout('layout/index');

        $message = '';
        $success = false;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $token_id = $this->container()->get('token_id');
            $this->container()->clear('token_id');

            if ($token_id != $request->getPost('token_id')) {
                $message = "Not allowed";
//                $message = "許可されていません。";
            }
            else {
                $db = new UserEntity();
                $row = $db->db()->getRemindUserChk(
                    $request->getPost('login_id'), $request->getPost('email')
                );

                if (!$row || !$row->user_no || !$row->email) {
                    $message = "Failed";
//                    $message = "処理失敗";
                }
                else {
                    $tmp_id = hash_hmac('sha256', uniqid(time() . microtime()), false);
                    $tmp = new TemporaryIdTable();
                    $data = array(
                        'tmp_id' => $tmp_id,
                        'life_time' => 60 * 60 * 24,
                    );
                    $tmp->exchanegArray($data);
                    $success = $tmp->insertRecord($row->user_no);
                }

                // send url for initialization
                if ($success) {
                    
                    $mail = new Message();
                    $mail->setEncoding("UTF-8");

                    
                    $mail->addFrom(AUTO_MAIL_FROM)
                            ->addTo($row->email)
                            ->setSubject($this->translator()->translate("About initialize password"));
//                            ->setSubject($this->translator()->translate("パスワード初期化について"));

                    $body_tpl = file_get_contents(APP_DIR . '/tpl/remind-mail.txt');
                    $body_tpl = trim(str_replace("\r\n", "\n", $body_tpl));
                    $body_tpl = $this->translator()->translate($body_tpl);
                    $body_tpl .= "\n\n\n" . "URL:" . BASE_URL . "/index/remind-store/" . $tmp_id;
                    $body_tpl .= "\n\n" . $this->translator()->translate("This URL is available 24 hours");
//                    $body_tpl .= "\n\n" . $this->translator()->translate("本URLの有効期間は24時間です。");
                    $mail->setBody($body_tpl);

                    
                    
                   $transport = new SmtpTransport();
                    $options   = new SmtpOptions(array(
                        'host'              => 'smtp.gmail.com',
                        'connection_class'  => 'plain',
                        'connection_config' => array(
                            'ssl'       => 'tls',
                            'username' => AUTO_MAIL_FROM,
                            'password' => AUTO_MAIL_FROM_PASSWORD,
                        ),
                        'port' => 587,
                    ));

                    $transport->setOptions($options);
            }

                if ($success) {
                    $success = $transport->send($mail);
                }
                else if ($success && IS_TEST) {
                    $logger = new \Zend\Log\Logger();
                    $writer = new \Zend\Log\Writer\Stream(APP_DIR . '/log/debug.txt');
                    $logger->addWriter($writer);
                    $logger->log(\Zend\Log\Logger::DEBUG, print_r($mail, 1));
                }
                
                if ($success) {
                    $this->flashMessenger()->addMessage('Please confirm your e-mail');
//                    $this->flashMessenger()->addMessage('メールを確認してください。');
                    return $this->redirect()->toRoute('app', array(
                        'controller' => 'index',
                    ));
                } 
            }
        }

        // create token_id
        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);

        $values = array(
            'token_id' => $token_id,
            'login_id' => $this->container()->get('login_id'),
            'err_msg' => $message,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/index/remind.phtml');
        return $view;
    }

    /**
     * re-issue password2
     * @return ViewModel
     */
    public function remindStoreAction()
    {
        $this->layout('layout/index');

        // get temporary id
        $id = (string) $this->params()->fromRoute('id');
        if (!$id) {
            $this->flashMessenger()->addMessage('Not allowed');
//            $this->flashMessenger()->addMessage('許可されていません。');
            return $this->redirect()->toRoute('app', array(
                'controller' => 'index',
            ));
        }

        $tmp = new TemporaryIdTable();
        $row = $tmp->getFetchOne($id);

        if (!$row || !isset($row->create_user) || !$row->create_user) {
            $this->flashMessenger()->addMessage('Not allowed');
//            $this->flashMessenger()->addMessage('許可されていません。');
            return $this->redirect()->toRoute('app', array(
                'controller' => 'index',
            ));
        }

        $success = false;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $token_id = $this->container()->get('token_id');
            $this->container()->clear('token_id');

            if ($token_id != $request->getPost('token_id')) {
                $this->flashMessenger()->addMessage('Not allowed');
//                $this->flashMessenger()->addMessage('許可されていません。');
                return $this->redirect()->toRoute('app', array(
                    'controller' => 'index',
                ));
            }

            $db = new UserEntity();

            $chk = true;
            $i = 0;
            while ($chk) {
                $new_pw = make_rand_str(8, 3);
                $chk = $db->db()->checkLoginPw($row->create_user, $new_pw);
                ++ $i;
                if (!$chk || 10 < $i) { // avoid infinite loop
                    break;
                }
            }

            if (!$chk) {
                $success = $db->changePw($row->create_user, $new_pw, 1);
            }

            if ($success) {
                $mail = new Message();
                $mail->setEncoding("UTF-8");
                
                $mail->addFrom(AUTO_MAIL_FROM)
                        ->addTo($row->email)
                        ->setSubject($this->translator()->translate("About initialize password"));
//                        ->setSubject($this->translator()->translate("パスワード初期化について"));

                $body_tpl = file_get_contents(APP_DIR . '/tpl/remind-store-mail.txt');
                $body_tpl = trim(str_replace("\r\n", "\n", $body_tpl));
                $body_tpl = $this->translator()->translate($body_tpl);
                $body_tpl .= "\n\n" . "PW:" . $new_pw;
                $body_tpl .= "\n\n\n" . "URL:" . BASE_URL;
                $mail->setBody($body_tpl);
                   
                $transport = new SmtpTransport();
                    $options   = new SmtpOptions(array(
                        'host'              => 'smtp.gmail.com',
                        'connection_class'  => 'plain',
                        'connection_config' => array(
                            'ssl'       => 'tls',
                            'username' => AUTO_MAIL_FROM,
                            'password' => AUTO_MAIL_FROM_PASSWORD,
                        ),
                        'port' => 587,
                    ));

                    $transport->setOptions($options);
            }

            if($success){
                $success = $transport->send($mail);
            }
            else if (IS_TEST) {
                $logger = new \Zend\Log\Logger();
                $writer = new \Zend\Log\Writer\Stream(APP_DIR . '/log/debug.txt');
                $logger->addWriter($writer);
                $logger->log(\Zend\Log\Logger::DEBUG, print_r($mail, 1));
            }
            

            $tmp->finishRecord($id);
            $message = $success ? 'Please confirm e-mail' : 'Failed';
//            $message = $success ? 'メールを確認してください。' : '処理失敗';
            $this->flashMessenger()->addMessage($message);
            return $this->redirect()->toRoute('app', array(
                'controller' => 'index',
            ));
        }

        // create token_id
        $token_id = make_token_id();
        $this->container()->set('token_id', $token_id);

        $values = array(
            'token_id' => $token_id,
            'id' => $id,
        );

        $view = new ViewModel($values);
        $view->setTemplate('/' . VIEW_DIR . '/index/remind-store.phtml');
        return $view;
    }

    /**
     * re-issue password2
     * @return ViewModel
     */
    public function remindCancelAction()
    {
        // temporary id を取得
        $id = (string) $this->params()->fromRoute('id');
        if (!$id) {
            $this->flashMessenger()->addMessage('Not allowed');
//            $this->flashMessenger()->addMessage('許可されていません。');
            return $this->redirect()->toRoute('app', array(
                'controller' => 'index',
            ));
        }

        $tmp = new TemporaryIdTable();
        $row = $tmp->getFetchOne($id);

        if (!$row || !isset($row->create_user) || !$row->create_user) {
            $this->flashMessenger()->addMessage('Not allowed');
//            $this->flashMessenger()->addMessage('許可されていません。');
            return $this->redirect()->toRoute('app', array(
                'controller' => 'index',
            ));
        }

        $success = $tmp->finishRecord($id);
        $message = $success ? 'Cancelled' : 'Failed';
//        $message = $success ? 'キャンセルしました。' : '処理失敗';
        $this->flashMessenger()->addMessage($message);
        return $this->redirect()->toRoute('app', array(
            'controller' => 'index',
        ));
    }
}