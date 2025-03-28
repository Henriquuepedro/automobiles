<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#61-title
    |
    */

    'title' => 'AdminLTE 3',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#62-logo
    |
    */

    'logo' => '<b>Admin</b>LTE',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image-xl',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'AdminLTE',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#63-layout
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Extra Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#64-classes
    |
    */

    'classes_body' => 'text-sm',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_header' => 'container-fluid',
    'classes_content' => 'container-fluid',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => 'nav-flat nav-legacy',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand-md',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#65-sidebar
    |
    */

    'sidebar_mini' => true,
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#66-control-sidebar-right-sidebar
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#67-urls
    |
    */

    'use_route_url' => false,

    'dashboard_url' => 'admin/home',

    'logout_url' => 'admin/logout',

    'login_url' => 'admin/login',

    'password_reset_url' => 'password/reset',

    'password_email_url' => 'password/email',

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#68-laravel-mix
    |
    */

    'enabled_laravel_mix' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#69-menu
    |
    */

    'menu' => [
        [
            'text'  => 'ADMINISTRAR EMPRESA',
            'route' => 'admin.company.index',
            'icon'  => 'fas fa-fw fa-building',
            'can'   => 'view-admin',
            'active'=> ['admin/empresa/*']
        ],
        [
            'text'  => 'ADMINISTRAR EMPRESAS',
            'route' => 'admin.master.company.index',
            'icon'  => 'fas fa-fw fa-building',
            'can'   => 'view-master',
            'active'=> ['admin/master/*']
        ],
        [
            'text'  => 'Dashboard',
            'route' => 'admin.home',
            'icon'  => 'fa fa-fw fa-tachometer-alt',
            'active'=> ['admin', 'admin/home']
        ],
        [
            'text'      => 'Automóvel',
            'icon'      => 'fas fa-fw fa-car',
            'submenu'   => [
                [
                    'text'  => 'Automóveis',
                    'route' => 'admin.automobiles.index',
                    'active'=> ['admin/automoveis', 'admin/automoveis/*']
                ]
            ]
        ],
        [
            'text'    => 'Aluguel',
            'icon'    => 'fas fa-fw fa-taxi',
            'can'     => 'manage-rent',
            'submenu' => [
                [
                    'text'  => 'Automóvel',
                    'route' => 'admin.rent.automobile.index',
                    'active'=> ['admin/aluguel/automovel/*']
                ],
//                [
//                    'text'  => 'Grupo',
//                    'route' => 'admin.rent.group.index',
//                    'active'=> ['admin/aluguel/grupo/*']
//                ],
                [
                    'text'  => 'Local',
                    'route' => 'admin.rent.place.index',
                    'active'=> ['admin/aluguel/local/*']
                ],
//                [
//                    'text'  => 'Configuração',
//                    'route' => 'admin.rent.setting.index',
//                    'active'=> ['admin/aluguel/configuracao/*']
//                ]
            ]
        ],
        [
            'text'    => 'Cadastro',
            'icon'    => 'fas fa-fw fa-plus',
            'submenu' => [
                [
                    'text'      => 'Complementares',
                    'route'     => 'admin.register.complements.manage',
                    'active'    => ['admin/config/complementares']
                ],
                [
                    'text'      => 'Opcionais',
                    'route'     => 'admin.register.optionals.manage',
                    'active'    => ['admin/config/opcionais']
                ],
                [
                    'text'      => 'Estado Financeiro',
                    'route'     => 'admin.register.financialsStatus.manage',
                    'active'    => ['admin/config/estadosFinanceiro']
                ],
                [
                    'text'      => 'Depoimentos',
                    'route'     => 'admin.testimony.index',
                    'active'    => ['admin/depoimento/*']
                ],
                [
                    'text'      => 'Cores dos Automóveis',
                    'route'     => 'admin.colorAuto.index',
                    'active'    => ['admin/cores-automoveis/*']
                ],
                [
                    'text'      => 'Características de Aluguel',
                    'route'     => 'admin.rent.characteristic.index',
                    'active'    => ['admin/aluguel/caracteristica']
                ]
            ]
        ],
        [
            'text'    => 'Configuração',
            'icon'    => 'fas fa-fw fa-cog',
            'submenu' => [
                [
                    'text'   => 'Página Inicial',
                    'route'  => 'admin.config.homePage',
                    'active' => ['admin/config/paginaInicial']
                ],
                [
                    'text'   => 'Página Dinâmica',
                    'route'  => 'admin.config.pageDynamic.index',
                    'active' => ['admin/config/paginaDinamica', 'admin/config/paginaDinamica/*']
                ],
                [
                    'text'   => 'Banner Inicial',
                    'route'  => 'admin.config.banner.index',
                    'active' => ['admin/config/banner']
                ],
                [
                    'text'   => 'Sobre a Loja',
                    'route'  => 'admin.config.about.index',
                    'active' => ['admin/config/sobre-loja']
                ]
            ],
        ],
        [
            'text'      => 'Mensagem de Contato',
            'route'     => 'admin.contactForm.index',
            'icon'      => 'fas fa-fw fa-envelope-open-text',
            'active'    => ['admin/formulario-contato/*']
        ],
        [
            'text'    => 'Relatório',
            'icon'    => 'fas fa-fw fa-paste',
            'can'     => 'manage-report',
            'submenu' => [
                [
                    'text'   => 'Variação FIPE',
                    'route'  => 'admin.report.fipeVariation',
                    'active' => ['admin/relatorio/variacao-fipe']
                ]
            ],
        ],
        [
            'text'      => 'Aplicativos',
            'route'     => 'admin.application.index',
            'icon'      => 'fab fa-fw fa-app-store-ios',
            'can'       => 'view-master',
            'active'    => ['admin/aplicativos/*']
        ],
        [
            'text'      => 'Planos',
            'route'     => 'admin.plan.index',
            'icon'      => 'fas fa-fw fa-star',
            'icon_color'=> 'warning',
            'classes'   => 'text-warning font-weight-bold',
            'active'    => ['admin/planos/*'],
            'can'       => ['view-admin','view-master']
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#610-menu-filters
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        //JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#611-plugins
    |
    */

    'plugins' => [
        [
            'name' => 'Datatables',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.css',
                ],
            ],
        ],
        [
            'name' => 'Select2',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        [
            'name' => 'Chartjs',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => 'https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js',
                ],
            ],
        ],
        [
            'name' => 'Sweetalert2',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@10',
                ],
            ],
        ],
        [
            'name' => 'Pace',
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],
];
