<div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show custom-sdbar" id="sidebar">
    <div class="logo-font">

        @if(isset($global_content))
            <span>{{$global_content->side_bar_logo_text1}}</span>
            <span class="sub-lg">{{$global_content->side_bar_logo_text2}}</span>
        @else
            <span>AmaZone</span>
            <span class="sub-lg">DropShipper</span>
        @endif

{{--        <img class="c-sidebar-brand-full" src="/images/amazone-dropshipper-logo.png"--}}
{{--            alt="AmaZone Dropshipper Logo" style="width: 80%; height: auto;"><img class="c-sidebar-brand-minimized"--}}
{{--            src="/images/amazone-dropshipper-logo.png" alt="AmaZone Dropshipper Logo" style="width: 80%; height: auto;">--}}
    </div>
    <ul class="c-sidebar-nav mt-3">
        @if(isset($sidebar_contents) && count($sidebar_contents)>0)
            @foreach($sidebar_contents as $key=>$menu)
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link{{ request()->is($menu->menu_link_redirect_to) ? ' c-active' : '' }}" href="{{$menu->menu_link_redirect_to}}">
                        <img src="{{asset($menu->menu_icon)}}" class="{{$menu->menu_slug=="import-products" ? "import-home-img" : '' }}" alt="{{$menu->menu_title}}" />
                        {{--                <i class="c-sidebar-nav-icon fas fa-star"></i> --}}
                       {{$menu->menu_title}}

                    </a>
                </li>
            @endforeach
       @else
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link{{ request()->is('/import-home') ? ' c-active' : '' }}" href="/import-home">
                <img src="{{asset('images/sidebar/import.png')}}" class="import-home-img" alt="Import Products" />
                Import Products
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link{{ request()->is('/products') ? ' c-active' : '' }}" href="/products">
                <img src="{{asset('images/sidebar/task.png')}}"  alt="My Products" />
                My Products
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link{{ request()->is('/orders') ? ' c-active' : '' }}" href="/orders">
                <img src="{{asset('images/sidebar/shopping-bag.png')}}"  alt="My Orders" />
                My Orders
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link{{ request()->is('/help') ? ' c-active' : '' }}" href="/help">
                <img src="{{asset('images/sidebar/information.png')}}"  alt="Help / Videos / FAQ" />
                Help / Videos / FAQ
            </a>
        </li>

        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link{{ request()->is('/settings') ? ' c-active' : '' }}" href="/settings">
                <img src="{{asset('images/sidebar/settings.png')}}"  alt="Settings" />
                Settings
            </a>
        </li>
        @endif
{{--        <li class="c-sidebar-nav-dropdown mt-auto"><a class="c-sidebar-nav-dropdown-toggle{{ request()->is('/help*') ? ' c-active' : '' }}" href="#"><i class="c-sidebar-nav-icon fas fa-question-circle"></i> Help Center</a>--}}
{{--            <ul class="c-sidebar-nav-dropdown-items">--}}
{{--                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link{{ request()->is('/help') ? ' c-active' : '' }}" href="/help">Help Topics</a></li>--}}
{{--                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link{{ request()->is('/help/tutorials') ? ' c-active' : '' }}" href="/help/tutorials">Tutorials</a></li>--}}
{{--                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link{{ request()->is('/help/contact') ? ' c-active' : '' }}" href="/help/contact">Support &amp; Feedback</a></li>--}}
{{--                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link{{ request()->is('/help/announcements') ? ' c-active' : '' }}" href="/help/announcements">Announcements</a></li>--}}
{{--            </ul>--}}
{{--        </li>--}}

{{--        <li class="c-sidebar-nav-item mb-3">--}}
{{--            <a class="c-sidebar-nav-link" href="https://{{ Auth::user()->getDomain()->toNative() }}">--}}
{{--                <i class="c-sidebar-nav-icon fas fa-shopping-bag"></i> My Shop--}}
{{--            </a>--}}
{{--        </li>--}}
    </ul>
</div>



