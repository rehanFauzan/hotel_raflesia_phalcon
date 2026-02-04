<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Index;

use Phalcon\Mvc\Controller as BaseController;
use Core\Facades\Response;
use Core\Facades\Session;

class Controller extends BaseController
{
    /**
     * @routeGet("/")
     */
    public function indexAction()
    {
        if (Session::has('user')) {
            // $pdam_id = Session::get('user')['pdam_id'];
            return Response::redirect('/panel/dashboard');
        } else {
            // return Response::redirect('/Redirect/lost');
            return Response::redirect('/auth/login');
            // return "Anda mau ke mana";
        }
    }
}
