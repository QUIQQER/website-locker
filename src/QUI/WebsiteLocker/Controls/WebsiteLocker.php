<?php
/**
 * Created by PhpStorm.
 * User: gerd
 * Date: 21.05.19
 * Time: 14:07
 */

namespace QUI\WebsiteLocker\Controls;

use QUI\System\Log;
use QUI;

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

        $Site    = $this->getSite();

        $Engine->assign([
            'Site'                  => $Site,
            'title'                 => $this->getAttribute('cardTitle'),
            'description'           => $this->getAttribute('description'),
            'logo'                  => $this->getAttribute('logo'),
            'interactiveBackground' => $this->getAttribute('interactiveBackground'),
            'backgroundColor'       => $this->getAttribute('backgroundColor'),
            'backgroundImage'       => $this->getAttribute('backgroundImage'),
            'placeholder'           => $this->getAttribute('placeholder')
        ]);

        return $Engine->fetch(dirname(__FILE__) . '/WebsiteLocker.html');
    }

    /**
     * Return the current site
     *
     * @return false|mixed|QUI\Projects\Site|null
     */
    public function getSite()
    {
        if ($this->getAttribute('Site')) {
            return $this->getAttribute('Site');
        }

        return QUI::getRewrite()->getSite();
    }
}
