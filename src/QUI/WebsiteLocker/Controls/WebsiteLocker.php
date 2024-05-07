<?php

namespace QUI\WebsiteLocker\Controls;

use QUI;
use QUI\Control;
use QUI\Exception;

class WebsiteLocker extends Control
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
     * @throws Exception
     */
    public function getBody(): string
    {
        $Engine = QUI::getTemplateManager()->getEngine();
        $Site = $this->getSite();

        $Engine->assign([
            'Site' => $Site,
            'logo' => $this->getAttribute('logo'),
            'interactiveBackground' => $this->getAttribute('interactiveBackground'),
            'backgroundColor' => $this->getAttribute('backgroundColor'),
            'backgroundImage' => $this->getAttribute('backgroundImage'),
        ]);

        return $Engine->fetch(dirname(__FILE__) . '/WebsiteLocker.html');
    }

    /**
     * Return the current site
     *
     * @return QUI\Interfaces\Projects\Site
     * @throws Exception
     */
    public function getSite(): QUI\Interfaces\Projects\Site
    {
        if ($this->getAttribute('Site')) {
            return $this->getAttribute('Site');
        }

        return QUI::getRewrite()->getSite();
    }
}
