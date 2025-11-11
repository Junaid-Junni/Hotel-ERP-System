<aside class="main-sidebar sidebar-dark-primary elevation-4 dashbroad__sidebar__bg">
    <a href="{{ route('home') }}" class="brand-link">
        <img src="/uploads/hotelio.png"
             alt="Lagoon Logo"
             class="brand-image img-circle elevation-3" style="background-color: white; width: 50px; height: 50px;">
            <span class="brand-text font-weight-light text-truncate d-inline-block" style="max-width: 140px; overflow: hidden;">
                {{ config('app.name') }}
            </span>
    </a>

    <div class="sidebar custom-sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @include('layouts.menu')
            </ul>
        </nav>
    </div>

</aside>
