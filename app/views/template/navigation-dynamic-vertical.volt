{%- macro renderSubmenu(menuList, parentId) -%}
    {% for menu in menuList %}
        {% if menu.has_children %}
            <li class="nav-item">
                <a class="nav-link dropdown-indicator" href="#nv-{{ menu.id_menu }}" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-{{ menu.id_menu }}">
                    <div class="d-flex align-items-center">
                        <div class="dropdown-indicator-icon-wrapper">
                            <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                        </div>
                        {% if menu.icon is not empty %}
                            <span class="nav-link-icon">
                                <span data-feather="{{ menu.icon }}"></span>
                            </span>
                        {% endif %}
                        <span class="nav-link-text">{{ menu.nama_menu }}</span>
                    </div>
                </a>
                <div class="parent-wrapper">
                    <ul class="nav collapse parent" data-bs-parent="#{{ parentId }}" id="nv-{{ menu.id_menu }}">
                        <li class="collapsed-nav-item-title d-none">{{ menu.nama_menu }}</li>
                        {{ renderSubmenu(menu.children, menu.id_menu) }}
                    </ul>
                </div>
            </li>
        {% else %}
            <li class="nav-item">
                <a class="nav-link" href="{{ url(menu.link_menu) }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-link-text">{{ menu.nama_menu }}</span>
                    </div>
                </a>
            </li>
        {% endif %}
    {% endfor %}
{%- endmacro %}

<ul class="navbar-nav flex-column" id="navbarVerticalNav">
    {% for menu in menuModel.getUserMenuList() %}
        {% if menu.jenis == 0 %}
            {% if menu.link_menu is empty %}
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">{{ menu.nama_menu }}</p>
                    <hr class="navbar-vertical-line"/>
                    {% if menu.has_children %}
                        <!-- parent pages-->
                        {% for mainMenu in menu.children %}
                            <div class="nav-item-wrapper">
                                {% if mainMenu.has_children %}
                                    <a class="nav-link dropdown-indicator label-1" href="#nv-{{ mainMenu.id_menu }}" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-{{ mainMenu.id_menu }}">
                                        <div class="d-flex align-items-center">
                                            <div class="dropdown-indicator-icon-wrapper">
                                                <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                            </div>
                                            {% if mainMenu.icon is not empty %}
                                                <span class="nav-link-icon">
                                                    <span data-feather="{{ mainMenu.icon }}"></span>
                                                </span>
                                            {% endif %}
                                            <span class="nav-link-text-wrapper">
                                                <span class="nav-link-text">{{ mainMenu.nama_menu }}</span>
                                            </span>
                                        </div>
                                    </a>
                                    <div class="parent-wrapper label-1">
                                        <ul class="nav collapse parent" data-bs-parent="#{{ menu.id_menu }}" id="nv-{{ mainMenu.id_menu }}">
                                            <li class="collapsed-nav-item-title d-none">{{ mainMenu.nama_menu }}</li>
                                            {{ renderSubmenu(mainMenu.children, mainMenu.id_menu) }}
                                        </ul>
                                    </div>
                                {% else %}
                                    <a class="nav-link label-1" href="{{ url(mainMenu.link_menu) }}" role="button">
                                        <div class="d-flex align-items-center">
                                            {% if mainMenu.icon is not empty %}
                                                <span class="nav-link-icon">
                                                    <span data-feather="{{ mainMenu.icon }}"></span>
                                                </span>
                                            {% endif %}
                                            <span class="nav-link-text-wrapper">
                                                <span class="nav-link-text">{{ mainMenu.nama_menu }}</span>
                                            </span>
                                        </div>
                                    </a>
                                {% endif %}
                            </div>
                        {% endfor %}
                    {% endif %}
                </li>
            {% else %}
                <li class="nav-item">
                    <!-- parent pages-->
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1" href="{{ url(menu.link_menu) }}" role="button">
                            <div class="d-flex align-items-center">
                                {% if menu.icon is not empty %}
                                    <span class="nav-link-icon">
                                        <span data-feather="{{ menu.icon }}"></span>
                                    </span>
                                {% endif %}
                                <span class="nav-link-text-wrapper">
                                    <span class="nav-link-text">{{ menu.nama_menu }}</span>
                                </span>
                            </div>
                        </a>
                    </div>
                </li>
            {% endif %}
        {% endif %}
    {% endfor %}
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