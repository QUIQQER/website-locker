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
        parent::__construct($attributes);
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

        if (empty($path)) {
            $path = '/';
        }

        $Engine->assign([
            'url_path'              => $path,
            'interactiveBackground' => $this->getAttribute('interactiveBackground'),
            'backgroundColor'       => $this->getAttribute('backgroundColor'),
            'backgroundImage'       => $this->getAttribute('backgroundImage')
        ]);

        return $Engine->fetch(dirname(__FILE__).'/WebsiteLocker.html');
    }
}
