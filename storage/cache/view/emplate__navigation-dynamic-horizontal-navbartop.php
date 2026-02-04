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
                            <span class="uil fs-8 me-2 uil-<?= $menu->icon_uil ?>"></span>
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

<script>
(function() {
    // Find all navigation links in the horizontal nav
    let elements = document.querySelectorAll('.navbar-nav-top a.nav-link, .navbar-nav-top a.dropdown-item');
    let found = null;

    // Find the best matching link based on current URL
    for (let el of elements) {
        // Ignore links with href="#!" or href="#"
        if (!el.href || el.getAttribute('href') === '#!' || el.getAttribute('href') === '#') continue;
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
        while (parent != null && !parent.classList.contains('navbar-nav-top')) {
            // Handle nav items
            if (parent.classList.contains('nav-item')) {
                parent.classList.add('active');
                if (firstItemFound) {
                    // parent.classList.add('show');
                }
                firstItemFound = true;
            }
            
            // Handle dropdown menus
            if (parent.classList.contains('dropdown')) {
                // parent.classList.add('show');
                // Find the dropdown-toggle and set aria-expanded
                let toggle = parent.querySelector('.dropdown-toggle');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'true');
                }
            }
            
            // Handle dropdown-menu - add 'show' class for 3-level support
            if (parent.classList.contains('dropdown-menu')) {
                // parent.classList.add('show');
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
