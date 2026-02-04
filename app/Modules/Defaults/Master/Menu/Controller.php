<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Master\Menu;

use Phalcon\Mvc\Controller AS BaseController;

/**
 * @routeGroup("/master/menu")
 */
class Controller extends BaseController
{
    /**
     * @routeGet("/")
     */
    public function indexAction()
    {
        echo '[default] /master/menu';
    }
    
    /**
     * @routeGet("/datatable")
     */
    public function datatableAction()
    {
        echo '[default] /master/menu/datatable';
    }
    
    /**
     * @routeGet("/create")
     */
    public function createAction()
    {
        echo '[default] /master/menu/create';
    }
    
    /**
     * @routeGet("/update")
     */
    public function updateAction()
    {
        echo '[default] /master/menu/update';
    }
    
    /**
     * @routeGet("/delete")
     */
    public function deleteAction()
    {
        echo '[default] /master/menu/delete';
    }
}