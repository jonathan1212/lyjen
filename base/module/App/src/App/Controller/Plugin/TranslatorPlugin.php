<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * plugin to easily use the translation function
 */

namespace App\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class TranslatorPlugin extends AbstractPlugin
{
    protected $translator;

    /**
     * get translated word
     * @param type $_str : target word
     * @return type
     */
    public function translate($_str)
    {
        if (!$_str) {
            return $_str;
        }

        if (!$this->translator) {
            $sl = $this->getController()->getServiceLocator();
            $this->translator = $sl->get('translator');
        }

        return $this->translator->translate($_str);
    }
}