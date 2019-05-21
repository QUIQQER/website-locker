<?php
/**
 * Created by PhpStorm.
 * User: gerd
 * Date: 21.05.19
 * Time: 14:07
 */

namespace QUI\WebsiteLocker\Controls;

use QUI\System\Log;

class WebsiteLocker extends \QUI\Control
{
    /**
     * ContractList constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        Log::writeRecursive($attributes);
//        $this->setAttributes([
//
//        ]);

        parent::__construct($attributes);

        $this->setAttributes([
//            'data-qui' => 'package/pcsg-projects/FEVES/bin/controls/ContractList'
        ]);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        try {
            $Engine = \QUI::getTemplateManager()->getEngine();
        } catch (\QUI\Exception $Exception) {
            return '';
        }

        $path = $this->getAttribute('url_path');

        if(empty($path)){
            $path = '/';
        }

        $Engine->assign([
            'url_path' => $path
        ]);

        return $Engine->fetch(dirname(__FILE__) . '/WebsiteLocker.html');
    }

}