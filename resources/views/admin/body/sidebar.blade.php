<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('backend/assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">Admin</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
    </div>

    <ul class="metismenu" id="menu">

        <li>
            <a href="{{ route('admin.dashboard') }}">
                <div class="parent-icon"><i class='bx bx-home'></i></div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

        <li class="menu-label">Menu</li>

        @if (Auth::user()->can('category.menu'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-category'></i></div>
                <div class="menu-title">Category</div>
            </a>
            <ul>
                @if (Auth::user()->can('category.all'))
                <li><a href="{{ route('all.category') }}"><i class='bx bx-list-ul'></i>All Category</a></li>
                @endif
                @if (Auth::user()->can('subcategory.all'))
                <li><a href="{{ route('all.subcategory') }}"><i class='bx bx-list-check'></i>All SubCategory</a></li>
                @endif
            </ul>
        </li>
        @endif

        @if (Auth::user()->can('instructor.menu'))
        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-user-pin'></i></div>
                <div class="menu-title">Instructor</div>
            </a>
            <ul>
                <li><a href="{{ route('all.instructor') }}"><i class='bx bx-group'></i>All Instructor</a></li>
            </ul>
        </li>
        @endif

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-book'></i></div>
                <div class="menu-title">Courses</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.all.course') }}"><i class='bx bx-folder'></i>All Courses</a></li>
            </ul>
        </li>

        @if(0)
        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-purchase-tag'></i></div>
                <div class="menu-title">Coupon</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.all.coupon') }}"><i class='bx bx-gift'></i>All Coupon</a></li>
            </ul>
        </li>
        @endif

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-cog'></i></div>
                <div class="menu-title">Setting</div>
            </a>
            <ul>
                <li><a href="{{ route('smtp.setting') }}"><i class='bx bx-mail-send'></i>SMPT</a></li>
                <li><a href="{{ route('site.setting') }}"><i class='bx bx-wrench'></i>Site Setting</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-cart'></i></div>
                <div class="menu-title">Orders</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.pending.order') }}"><i class='bx bx-time-five'></i>Pending Orders</a></li>
                <li><a href="{{ route('admin.confirm.order') }}"><i class='bx bx-check-circle'></i>Confirm Orders</a></li>
            </ul>
        </li>

        @if(0)
        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-bar-chart-square'></i></div>
                <div class="menu-title">Report</div>
            </a>
            <ul>
                <li><a href="{{ route('report.view') }}"><i class='bx bx-show'></i>Report View</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-star'></i></div>
                <div class="menu-title">Review</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.pending.review') }}"><i class='bx bx-time'></i>Pending Review</a></li>
                <li><a href="{{ route('admin.active.review') }}"><i class='bx bx-check'></i>Active Review</a></li>
            </ul>
        </li>
        @endif

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-user'></i></div>
                <div class="menu-title">All User</div>
            </a>
            <ul>
                <li><a href="{{ route('all.user') }}"><i class='bx bx-user'></i>All User</a></li>
                <li><a href="{{ route('all.instructor') }}"><i class='bx bx-id-card'></i>All Instructor</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-news'></i></div>
                <div class="menu-title">Blog</div>
            </a>
            <ul>
                <li><a href="{{ route('blog.category') }}"><i class='bx bx-category-alt'></i>Blog Category</a></li>
                <li><a href="{{ route('blog.post') }}"><i class='bx bx-edit'></i>Blog Post</a></li>
            </ul>
        </li>

        <li class="menu-label">Role & Permission</li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-shield"></i></div>
                <div class="menu-title">Role & Permission</div>
            </a>
            <ul>
                <li><a href="{{ route('all.permission') }}"><i class='bx bx-key'></i>All Permission</a></li>
                <li><a href="{{ route('all.roles') }}"><i class='bx bx-user-circle'></i>All Roles</a></li>
                <li><a href="{{ route('add.roles.permission') }}"><i class='bx bx-lock'></i>Role In Permission</a></li>
                <li><a href="{{ route('all.roles.permission') }}"><i class='bx bx-layer'></i>All Role In Permission</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class="bx bx-user-voice"></i></div>
                <div class="menu-title">Admin</div>
            </a>
            <ul>
                <li><a href="{{ route('all.admin') }}"><i class='bx bx-group'></i>All Admin</a></li>
            </ul>
        </li>
    </ul>
</div>