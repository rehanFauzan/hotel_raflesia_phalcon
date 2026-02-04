<?php

declare(strict_types=1);

namespace App\Modules\Defaults\GantiPassword;

use Phalcon\Mvc\Controller AS BaseController;

/**
 * @routeGroup("/ganti-password")
 */
class Controller extends BaseController
{
    /**
     * @routeGet("/")
     */
    public function indexAction()
    {
        echo '[default] /ganti-password';
    }
    
    /**
     * @routeGet("/set-password")
     */
    public function setPasswordAction()
    {
        echo '[default] /ganti-password/set-password';
    }
}