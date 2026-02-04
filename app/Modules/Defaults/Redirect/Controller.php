<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Redirect;

use Phalcon\Mvc\Controller as BaseController;
use Core\Facades\Response;
use Core\Facades\Session;

/**
 * @routeGroup('/Redirect')
 */
class Controller extends BaseController
{
    /**
     * @routeGet("/")
     */
    public function indexAction() {}

    /**
     * @routeGet("/lost")
     */
    public function lostAction() {}
}
