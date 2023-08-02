<?php

namespace QUI\WebsiteLocker\Controls;

use QUI;
use QUI\Exception;

use function dirname;

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
            'title' => false,
            'description' => false,
            'Site' => false,
            'backgroundImage' => false
        ]);

        parent::__construct($attributes);

        $this->addCSSFile(dirname(__FILE__) . 'SiteLock.css');
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        try {
            $Engine = \QUI::getTemplateManager()->getEngine();
        } catch (Exception $Exception) {
            return '';
        }

        $Site = $this->getSite();
        $Project = $Site->getProject();
        $logo = '';
        $bgImage = $this->getAttribute('backgroundImage');
        $title = $this->getAttribute('title');
        $description = $this->getAttribute('description');
        $logoType = $this->getSite()->getAttribute('quiqqer.website.locker.logo');

        if ($title == '') {
            $title = QUI::getLocale()->get(
                'quiqqer/website-locker',
                'website-locker.control.title'
            );
        }

        if ($description == '') {
            $description = QUI::getLocale()->get(
                'quiqqer/website-locker',
                'website-locker.control.text'
            );
        }

        if ($logoType === "projectLogo") {
            $Logo = $Project->getMedia()->getLogoImage();

            if ($Logo) {
                $logo = '<img src="' . $Logo->getSizeCacheUrl(300, 100) . '" class="logo" />';
            }
        }

        if ($logoType === "ownImage") {
            $OwnImage = $this->getSite()->getAttribute('quiqqer.website.locker.ownImage');

            if ($OwnImage) {
                $logo = '<img src="' . $OwnImage . '" class="logo" />';
            }
        }

        if (!empty($bgImage)) {
            $Engine->assign('backgroundImage', $this->getAttribute('backgroundImage'));
        }

        $Engine->assign([
            'Site' => $Site,
            'title' => $title,
            'description' => $description,
            'logo' => $logo,
            'logoType' => $logoType
        ]);

        return $Engine->fetch(dirname(__FILE__) . '/SiteLock.html');
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
