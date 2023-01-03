<header class="c-header c-header-light c-header-fixed shadow-sm">
    <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
        data-class="c-sidebar-show"><span class="c-header-toggler-icon"></span></button>
    <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar"
        data-class="c-sidebar-lg-show" responsive="true"><span class="c-header-toggler-icon"></span></button>
    <div style="width: 90%;text-align: center;">
        <span>{{(@$store) ? $store : ''}}</span>
    </div>
    <ul class="c-header-nav mfs-auto">
        <li class="c-header-nav-item dropdown mx-2">
{{--            <button class="btn btn-outline-dark dropdown-toggle" type="button" id="ad-store-shortcuts-btn"--}}
{{--                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-external-link-alt"></i> --}}
{{--                Store Shortcuts--}}
{{--            </button>--}}
            <div class="dropdown-menu" aria-labelledby="ad-store-shortcuts-btn">
                <a class="dropdown-item" href="https://www.amazon.com" target="_blank"><i
                        class="flag-icon flag-icon-us mr-2"></i> amazon.com</a>
                <a class="dropdown-item" href="https://www.walmart.com" target="_blank"><i
                        class="flag-icon flag-icon-us mr-2"></i> walmart.com</a>
                <a class="dropdown-item" href="https://www.amazon.com.au" target="_blank"><i
                        class="flag-icon flag-icon-au mr-2"></i> amazon.com.au</a>
                <a class="dropdown-item" href="https://www.amazon.com.br" target="_blank"><i
                        class="flag-icon flag-icon-br mr-2"></i> amazon.com.br</a>
                <a class="dropdown-item" href="https://www.amazon.ca" target="_blank"><i
                        class="flag-icon flag-icon-ca mr-2"></i> amazon.ca</a>
                <a class="dropdown-item" href="https://www.amazon.cn" target="_blank"><i
                        class="flag-icon flag-icon-cn mr-2"></i> amazon.cn</a>
                <a class="dropdown-item" href="https://www.amazon.fr" target="_blank"><i
                        class="flag-icon flag-icon-fr mr-2"></i> amazon.fr</a>
                <a class="dropdown-item" href="https://www.amazon.de" target="_blank"><i
                        class="flag-icon flag-icon-de mr-2"></i> amazon.de</a>
                <a class="dropdown-item" href="https://www.amazon.in" target="_blank"><i
                        class="flag-icon flag-icon-in mr-2"></i> amazon.in</a>
                <a class="dropdown-item" href="https://www.amazon.it" target="_blank"><i
                        class="flag-icon flag-icon-it mr-2"></i> amazon.it</a>
                <a class="dropdown-item" href="https://www.amazon.co.jp" target="_blank"><i
                        class="flag-icon flag-icon-jp mr-2"></i> amazon.co.jp</a>
                <a class="dropdown-item" href="https://www.amazon.com.mx" target="_blank"><i
                        class="flag-icon flag-icon-mx mr-2"></i> amazon.com.mx</a>
                <a class="dropdown-item" href="https://www.amazon.es" target="_blank"><i
                        class="flag-icon flag-icon-es mr-2"></i> amazon.es</a>
                <a class="dropdown-item" href="https://www.amazon.co.uk" target="_blank"><i
                        class="flag-icon flag-icon-gb mr-2"></i> amazon.co.uk</a>
            </div>
        </li>
        <alerts-manager></alerts-manager>
    </ul>
</header>
