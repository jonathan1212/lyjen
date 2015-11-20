<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Session\Storage;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;
use Zend\Session\SessionManager;
use Zend\Session\Container;

use App\Model\Entity\RestrictLoginEntity;

class DbSessionStorage
{
    protected $tableName = 't_session';
    protected $adapter;
    protected $tblgw;
    protected $sessionConfig;
    protected $sessionManager;
    protected $request;
    protected $container;

    public function __construct($_sessionConfig = null, $_request = null)
    {
        $this->adapter = GlobalAdapterFeature::getStaticAdapter();
        $this->tblgw = new TableGateway($this->tableName, $this->adapter);
        $this->sessionConfig = $_sessionConfig;
        $this->request = $_request;
    }

    public function setSessionStorage()
    {
        $options = new DbTableGatewayOptions();
        $options->setDataColumn('session_data')
            ->setIdColumn('session_id')
            ->setLifetimeColumn('life_time')
            ->setModifiedColumn('update_time')
            ->setNameColumn('session_name');

        $handler = new DbTableGateway($this->tblgw, $options);
        $this->sessionManager = new SessionManager();
        $this->sessionManager->setSaveHandler($handler);
        if ($this->sessionConfig) {
            $options = gv('options', $this->sessionConfig);
            $sessionConfig = new \Zend\Session\Config\SessionConfig();
            $sessionConfig->setOptions($options);
            $this->sessionManager->setConfig($sessionConfig);
        }
        $this->sessionManager->setSaveHandler($handler);
        Container::setDefaultManager($this->sessionManager);
        $this->sessionManager->start();

        $this->container = new Container('init');
        if (!$this->container->init) {
            $request = $this->request;
            $this->container->init = 1;
            $this->container->remoteAddr = $request->getServer()->get('REMOTE_ADDR');
            $this->container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');
            $this->container->create = time();
        }

        // update session id
        if (SESSION_ID_EXPIRE < time() - $this->container->create) {
            $this->sessionManager->regenerateId(true);
            $this->container->create = time();
        }
        return $this->sessionManager;
    }

    public function getSessionStorage()
    {
        if ($this->sessionManager) {
            return $this->sessionManager;
        }
        else {
            return $this->setSessionStorage();
        }
    }

    public function renewRestrictLogin($_user_no)
    {
        if (!RESTRICT_LOGIN || !$_user_no) {
            return;
        }
        $restrict = new RestrictLoginEntity();
        $row = $restrict->sessionCheck($_user_no);
        if (!$row) {
            $restrict->db()->insertRecord($_user_no);
        }
    }

    public function isValid()
    {
        $valid = true;
        $validators = gv('validators', $this->sessionConfig);

        foreach ($validators as $validator) {
            switch ($validator) {
                case 'Zend\Session\Validator\HttpUserAgent':
                    $validator = new $validator($this->container->httpUserAgent);
                    break;
                case 'Zend\Session\Validator\RemoteAddr':
                    $validator = new $validator($this->container->remoteAddr);
                    break;
                default :
                    $validator = new $validator();
            }
            $valid = $validator->isValid() ? $valid : false;
        }
        return $valid;
    }

    public function getSessionConfig()
    {
        return $this->sessionConfig;
    }

}