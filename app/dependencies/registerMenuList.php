<?php

use App\Modules\Defaults\Auth\Model\MenuModel;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

$diContainer->setShared('menuModel', function () {
	return new MenuModel();
});
