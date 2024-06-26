<?php

namespace QUI\WebsiteLocker;

use QUI;
use QUI\Exception;
use QUI\Rewrite;
use QUI\System\Log;
use QUI\WebsiteLocker\Controls\WebsiteLocker;
use Symfony\Component\HttpFoundation\Response;

use function class_exists;

class EventHandler
{
    protected static ?string $instanceLockPassword = null;

    /**
     * @param Rewrite $Rewrite
     * @param string $url
     *
     * @return void
     * @throws \Exception
     */
    public static function onRequest(Rewrite $Rewrite, string $url): void
    {
        try {
            $conf = $Rewrite->getProject()->getConfig();
        } catch (Exception $Exception) {
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

        if (
            isset($_REQUEST['website-locker-pass'])
            && $_REQUEST['website-locker-pass'] === $conf['WebsiteLocker.passwd']
        ) {
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
            'backgroundColor' => $conf['WebsiteLocker.backgroundColor'],
            'backgroundImage' => $conf['WebsiteLocker.backgroundImage'],
            'logo' => $conf['WebsiteLocker.logo']
        ]);

        $Response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        $Response->setContent($Control->create());
        $Response->send();
        exit;
    }

    /**
     * @throws \Exception
     */
    public static function onSiteStart(): void
    {
        try {
            $Site = QUI::getRewrite()->getSite();
        } catch (Exception $Exception) {
            Log::writeException($Exception);

            return;
        }

        if (!$Site->getAttribute('quiqqer.website.locker.status')) {
            return;
        }

        $Site->setAttribute('nocache', true);

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
        if (
            isset($_POST['site-lock-' . $Site->getId()])
            && isset($_POST['password'])
            && $_POST['password'] == $password
        ) {
            QUI::getSession()->set('website-locker-pass-' . $Site->getId(), $password);
        }

        // password
        if (QUI::getSession()->get('website-locker-pass-' . $Site->getId()) === $password) {
            return;
        }

        $Control = new QUI\WebsiteLocker\Controls\SiteLock([
            'title' => $Site->getAttribute('quiqqer.website.locker.title'),
            'description' => $Site->getAttribute('quiqqer.website.locker.description'),
            'Site' => $Site,
            'backgroundImage' => $Site->getAttribute('quiqqer.website.locker.background')
        ]);


        $Response = QUI::getGlobalResponse();
        $Response->setContent($Control->create());
        $Response->setStatusCode(Response::HTTP_FORBIDDEN);
        $Response->prepare(QUI::getRequest());
        $Response->send();

        exit;
    }

    public static function onProjectConfigSave(string $projectName, array $config, array $sentConfigParams): void
    {
        if (!QUI::getPackageManager()->isInstalled('quiqqer/cache')) {
            return;
        }

        if (class_exists('QUI\Cache\Handler')) {
            QUI\Cache\Handler::init()->clearCache();
        }
    }

    //region user login / logout

    public static function onUserLogoutBegin(QUI\Interfaces\Users\User $User): void
    {
        $password = QUI::getSession()->get('website-locker-pass');

        if (!empty($password)) {
            self::$instanceLockPassword = $password;
        }
    }

    public static function onUserLoginStart(): void
    {
        $password = QUI::getSession()->get('website-locker-pass');

        if (!empty($password)) {
            self::$instanceLockPassword = $password;
        }
    }

    public static function onUserLogout(QUI\Interfaces\Users\User $User): void
    {
        if (!empty(self::$instanceLockPassword)) {
            QUI::getSession()->set('website-locker-pass', self::$instanceLockPassword);
        }
    }

    public static function onUserLogin(): void
    {
        if (!empty(self::$instanceLockPassword)) {
            QUI::getSession()->set('website-locker-pass', self::$instanceLockPassword);
        }
    }

    //endregion
}
