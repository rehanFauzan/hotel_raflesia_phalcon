<?php $this->macros['renderSubmenu'] = function($__p = null) { if (isset($__p[0])) { $menuList = $__p[0]; } else { if (array_key_exists("menuList", $__p)) { $menuList = $__p["menuList"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: menuList");  } } if (isset($__p[1])) { $parentId = $__p[1]; } else { if (array_key_exists("parentId", $__p)) { $parentId = $__p["parentId"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: parentId");  } }  ?><?php foreach ($menuList as $menu) { ?>
        <?php if ($menu->has_children) { ?>
            <li class="dropdown">
                <a class="dropdown-item dropdown-toggle" id="<?= $menu->id_menu ?>" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <div class="dropdown-item-wrapper">
                        <span class="uil fs-8 uil-angle-right lh-1 dropdown-indicator-icon"></span>
                        <span>
                            <?php if (!empty($menu->icon)) { ?>
                                <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                            <?php } ?>
                            <?= $menu->nama_menu ?>
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                </ul>
            </li>
        <?php } else { ?>
            <li>
                <a class="dropdown-item" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?><?php }; $this->macros['renderSubmenu'] = \Closure::bind($this->macros['renderSubmenu'], $this); ?><ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
    <?php foreach ($this->menuModel->getUserMenuList() as $menu) { ?>
        <?php if ($menu->jenis == 0 && empty($menu->link_menu)) { ?>
            <?php if ($menu->has_children) { ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="uil fs-8 me-2 uil-<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                    </ul>
                </li>
            <?php } ?>
        <?php } elseif ($menu->jenis == 0 && !empty($menu->link_menu)) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="dropdown-item-wrapper">
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="me-2 uil" data-feather="<?= $menu->icon ?>"></span>
                        <?php } ?>
                        <?= $menu->nama_menu ?>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?>
</ul>
