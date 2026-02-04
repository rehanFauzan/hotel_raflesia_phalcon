<?php $this->macros['renderSubmenu'] = function($__p = null) { if (isset($__p[0])) { $menuList = $__p[0]; } else { if (array_key_exists("menuList", $__p)) { $menuList = $__p["menuList"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: menuList");  } } if (isset($__p[1])) { $parentId = $__p[1]; } else { if (array_key_exists("parentId", $__p)) { $parentId = $__p["parentId"]; } else {  throw new \Phalcon\Mvc\View\Exception("Macro 'renderSubmenu' was called without parameter: parentId");  } }  ?><?php foreach ($menuList as $menu) { ?>
        <?php if ($menu->has_children) { ?>
            <li class="nav-item">
                <a class="nav-link dropdown-indicator" href="#nv-<?= $menu->id_menu ?>" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-<?= $menu->id_menu ?>">
                    <div class="d-flex align-items-center">
                        <div class="dropdown-indicator-icon-wrapper">
                            <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                        </div>
                        <?php if (!empty($menu->icon)) { ?>
                            <span class="nav-link-icon">
                                <span data-feather="<?= $menu->icon ?>"></span>
                            </span>
                        <?php } ?>
                        <span class="nav-link-text"><?= $menu->nama_menu ?></span>
                    </div>
                </a>
                <div class="parent-wrapper">
                    <ul class="nav collapse parent" data-bs-parent="#<?= $parentId ?>" id="nv-<?= $menu->id_menu ?>">
                        <li class="collapsed-nav-item-title d-none"><?= $menu->nama_menu ?></li>
                        <?= $this->callMacro('renderSubmenu', [$menu->children, $menu->id_menu]) ?>
                    </ul>
                </div>
            </li>
        <?php } else { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= $this->url->get($menu->link_menu) ?>">
                    <div class="d-flex align-items-center">
                        <span class="nav-link-text"><?= $menu->nama_menu ?></span>
                    </div>
                </a>
            </li>
        <?php } ?>
    <?php } ?><?php }; $this->macros['renderSubmenu'] = \Closure::bind($this->macros['renderSubmenu'], $this); ?>

<ul class="navbar-nav flex-column" id="navbarVerticalNav">
    <?php foreach ($this->menuModel->getUserMenuList() as $menu) { ?>
        <?php if ($menu->jenis == 0) { ?>
            <?php if (empty($menu->link_menu)) { ?>
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label"><?= $menu->nama_menu ?></p>
                    <hr class="navbar-vertical-line"/>
                    <?php if ($menu->has_children) { ?>
                        <!-- parent pages-->
                        <?php foreach ($menu->children as $mainMenu) { ?>
                            <div class="nav-item-wrapper">
                                <?php if ($mainMenu->has_children) { ?>
                                    <a class="nav-link dropdown-indicator label-1" href="#nv-<?= $mainMenu->id_menu ?>" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-<?= $mainMenu->id_menu ?>">
                                        <div class="d-flex align-items-center">
                                            <div class="dropdown-indicator-icon-wrapper">
                                                <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                            </div>
                                            <?php if (!empty($mainMenu->icon)) { ?>
                                                <span class="nav-link-icon">
                                                    <span data-feather="<?= $mainMenu->icon ?>"></span>
                                                </span>
                                            <?php } ?>
                                            <span class="nav-link-text-wrapper">
                                                <span class="nav-link-text"><?= $mainMenu->nama_menu ?></span>
                                            </span>
                                        </div>
                                    </a>
                                    <div class="parent-wrapper label-1">
                                        <ul class="nav collapse parent" data-bs-parent="#<?= $menu->id_menu ?>" id="nv-<?= $mainMenu->id_menu ?>">
                                            <li class="collapsed-nav-item-title d-none"><?= $mainMenu->nama_menu ?></li>
                                            <?= $this->callMacro('renderSubmenu', [$mainMenu->children, $mainMenu->id_menu]) ?>
                                        </ul>
                                    </div>
                                <?php } else { ?>
                                    <a class="nav-link label-1" href="<?= $this->url->get($mainMenu->link_menu) ?>" role="button">
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($mainMenu->icon)) { ?>
                                                <span class="nav-link-icon">
                                                    <span data-feather="<?= $mainMenu->icon ?>"></span>
                                                </span>
                                            <?php } ?>
                                            <span class="nav-link-text-wrapper">
                                                <span class="nav-link-text"><?= $mainMenu->nama_menu ?></span>
                                            </span>
                                        </div>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </li>
            <?php } else { ?>
                <li class="nav-item">
                    <!-- parent pages-->
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="<?= $this->url->get($menu->link_menu) ?>" role="button">
                            <div class="d-flex align-items-center">
                                <?php if (!empty($menu->icon)) { ?>
                                    <span class="nav-link-icon">
                                        <span data-feather="<?= $menu->icon ?>"></span>
                                    </span>
                                <?php } ?>
                                <span class="nav-link-text-wrapper">
                                    <span class="nav-link-text"><?= $menu->nama_menu ?></span>
                                </span>
                            </div>
                        </a>
                    </div>
                </li>
            <?php } ?>
        <?php } ?>
    <?php } ?>
</ul>

<script>

(function() {
    // Find all navigation links in the vertical nav
    let elements = document.querySelectorAll('#navbarVerticalNav a.nav-link');
    let found = null;

    // Find the best matching link based on current URL
    for (let el of elements) {
        const url = window.location.toString();
        if (url.startsWith(el.href)) {
            if (found == null || found.href.length < el.href.length) {
                found = el;
            }
        }
    }

    function setActiveNavigation(element) {
        let parent = element.parentElement;
        let firstItemFound = false;

        // Traverse up the DOM tree until we reach the main nav
        while (parent != null && !parent.matches('#navbarVerticalNav')) {
            // Handle nav items
            if (parent.classList.contains('nav-item')) {
                parent.classList.add('active');
                if (firstItemFound) {
                    parent.classList.add('show');
                }
                firstItemFound = true;
            }
            
            // Handle collapsible menus
            if (parent.classList.contains('collapse')) {
                parent.classList.add('show');
                // Find and update the dropdown indicator if it exists
                const indicator = parent.parentElement.querySelector('.dropdown-indicator');
                if (indicator) {
                    indicator.setAttribute('aria-expanded', 'true');
                }
            }
            
            parent = parent.parentElement;
        }
    }

    // Apply active classes if a matching link was found
    if (found) {
        found.classList.add('active');
        setActiveNavigation(found);
    }
})();

</script>