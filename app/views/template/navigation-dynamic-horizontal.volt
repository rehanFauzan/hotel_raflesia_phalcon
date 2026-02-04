
{%- macro renderSubmenu(menuList, parentId) -%}
    {% for menu in menuList %}
        {% if menu.has_children %}
            <li class="dropdown">
                <a class="dropdown-item dropdown-toggle" id="{{ menu.id_menu }}" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <div class="dropdown-item-wrapper">
                        <span class="uil fs-8 uil-angle-right lh-1 dropdown-indicator-icon"></span>
                        <span>
                            {% if menu.icon is not empty %}
                                <span class="me-2 uil" data-feather="{{ menu.icon }}"></span>
                            {% endif %}
                            {{ menu.nama_menu }}
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    {{ renderSubmenu(menu.children, menu.id_menu) }}
                </ul>
            </li>
        {% else %}
            <li>
                <a class="dropdown-item" href="{{ url(menu.link_menu) }}">
                    <div class="dropdown-item-wrapper">
                        {% if menu.icon is not empty %}
                            <span class="me-2 uil" data-feather="{{ menu.icon }}"></span>
                        {% endif %}
                        {{ menu.nama_menu }}
                    </div>
                </a>
            </li>
        {% endif %}
    {% endfor %}
{%- endmacro -%}

<ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
    {% for menu in menuModel.getUserMenuList() %}
        {% if menu.jenis == 0 and menu.link_menu is empty %}
            {% if menu.has_children %}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        {% if menu.icon is not empty %}
                            <span class="uil fs-8 me-2 uil-{{ menu.icon }}"></span>
                        {% endif %}
                        {{ menu.nama_menu }}
                    </a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        {{ renderSubmenu(menu.children, menu.id_menu) }}
                    </ul>
                </li>
            {% endif %}
        {% elseif menu.jenis == 0 and menu.link_menu is not empty %}
            <li class="nav-item">
                <a class="nav-link" href="{{ url(menu.link_menu) }}">
                    <div class="dropdown-item-wrapper">
                        {% if menu.icon is not empty %}
                            <span class="me-2 uil" data-feather="{{ menu.icon }}"></span>
                        {% endif %}
                        {{ menu.nama_menu }}
                    </div>
                </a>
            </li>
        {% endif %}
    {% endfor %}
</ul>
