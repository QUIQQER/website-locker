<?php

namespace QUI\WebsiteLocker\Controls;

use QUI;

/**
 * Class SiteLock
 *
 * @package QUI\WebsiteLocker\Controls
 */
class SiteLock extends QUI\Control
{
    /**
     * ContractList constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        // defaults
        $this->setAttributes([
            'title'           => false,
            'description'     => false,
            'Site'            => false,
            'backgroundImage' => false
        ]);

        parent::__construct($attributes);

        $this->addCSSFile(\dirname(__FILE__).'SiteLock.css');
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
        $Project = $Site->getProject();
        $logo    = '';
        $bgImage = $this->getAttribute('backgroundImage');

        $Logo = $Project->getMedia()->getLogoImage();

        if ($Logo) {
            $logo = '<img src="'.$Logo->getSizeCacheUrl(300, 100).'" class="logo" />';
        }

        if (!empty($bgImage)) {
            $Engine->assign('backgroundImage', $this->getAttribute('backgroundImage'));
        }

        $Engine->assign([
            'Site'        => $Site,
            'title'       => $this->getAttribute('title'),
            'description' => $this->getAttribute('description'),
            'logo'        => $logo
        ]);

        return $Engine->fetch(\dirname(__FILE__).'/SiteLock.html');
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
