<?php
/**
 * Created by PhpStorm.
 * User: gerd
 * Date: 21.05.19
 * Time: 12:26
 */

namespace QUI\WebsiteLocker;

use QUI;
use QUI\System\Log;
use QUI\WebsiteLocker\Controls\WebsiteLocker;
use Symfony\Component\HttpFoundation\Response;

class EventHandler
{
    /**
     * @param \QUI\Rewrite $Rewrite
     * @param string $url
     *
     * @return void
     */
    public static function onRequest($Rewrite, $url)
    {
        try {
            $conf = $Rewrite->getProject()->getConfig();
        } catch (\QUI\Exception $Exception) {
            Log::writeException($Exception);

            return;
        }

        // pass module is off
        if (!isset($conf['WebsiteLocker.locked']) || !$conf['WebsiteLocker.locked']) {
            return;
        }

        // password is correct
        if (QUI::getSession()->get('website-locker-pass') === $conf['WebsiteLocker.passwd']) {
            return;
        }

        if (isset($_REQUEST['website-locker-pass'])
            && $_REQUEST['website-locker-pass'] === $conf['WebsiteLocker.passwd']) {
            // login
            QUI::getSession()->set('website-locker-pass', $_REQUEST['website-locker-pass']);

            return;
        }

        $Response = QUI::getGlobalResponse();

        if (!isset($conf['WebsiteLocker.placeholder'])) {
            $conf['WebsiteLocker.placeholder'] = '';
        }

        $Control = new WebsiteLocker([
            'interactiveBackground' => $conf['WebsiteLocker.interactiveBackground'],
            'backgroundColor'       => $conf['WebsiteLocker.backgroundColor'],
            'backgroundImage'       => $conf['WebsiteLocker.backgroundImage'],
            'placeholder'           => $conf['WebsiteLocker.placeholder']
        ]);

        $Response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        $Response->setContent($Control->create());
        $Response->send();
        exit;
    }

    /**
     * @param QUI\Template $Template
     * @param QUI\Projects\Site $Site
     */
    public static function onSiteStart()
    {
        try {
            $Site = QUI::getRewrite()->getSite();
        } catch (QUI\Exception $Exception) {
            Log::writeException($Exception);

            return;
        }

        if (!$Site->getAttribute('quiqqer.website.locker.status')) {
            return;
        }

        // password
        $password = $Site->getAttribute('quiqqer.website.locker.password');

        if (empty($password)) {
            $conf = $Site->getProject()->getConfig();

            if (empty($conf['WebsiteLocker.locked'])) {
                return;
            }

            $password = $conf['WebsiteLocker.locked'];
        }

        // password input
        if (isset($_POST['site-lock-'.$Site->getId()])
            && isset($_POST['password'])
            && $_POST['password'] == $password
        ) {
            QUI::getSession()->set('website-locker-pass-'.$Site->getId(), $password);
        }

        // password
        if (QUI::getSession()->get('website-locker-pass-'.$Site->getId()) === $password) {
            return;
        }

        $Control = new QUI\WebsiteLocker\Controls\SiteLock([
            'title'       => $Site->getAttribute('quiqqer.website.locker.title'),
            'description' => $Site->getAttribute('quiqqer.website.locker.description'),
            'Site'        => $Site
        ]);

        echo $Control->create();
        exit;
    }
}
