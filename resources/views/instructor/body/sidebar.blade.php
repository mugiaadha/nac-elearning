@php
$id = Auth::user()->id;
$instructorId = App\Models\User::find($id);
$status = $instructorId->status;
@endphp

<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('backend/assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">Instructor</h4>
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

        @if ($status === '1')

        <li class="menu-label">Course Manage</li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-layer'></i></div>
                <div class="menu-title">Course Manage</div>
            </a>
            <ul>
                <li><a href="{{ route('all.course') }}"><i class='bx bx-list-ul'></i>All Course</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-shopping-bag'></i></div>
                <div class="menu-title">All Orders</div>
            </a>
            <ul>
                <li><a href="{{ route('instructor.all.order') }}"><i class='bx bx-list-check'></i>All Orders</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-question-mark'></i></div>
                <div class="menu-title">All Question</div>
            </a>
            <ul>
                <li><a href="{{ route('instructor.all.question') }}"><i class='bx bx-comment-detail'></i>All Question</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-star'></i></div>
                <div class="menu-title">Reivew</div>
            </a>
            <ul>
                <li><a href="{{ route('instructor.all.review') }}"><i class='bx bx-message-square-dots'></i>All Review</a></li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-chat'></i></div>
                <div class="menu-title">Live Chat</div>
            </a>
            <ul>
                <li><a href="{{ route('instructor.live.chat') }}"><i class='bx bx-radio-circle'></i>Live Chat</a></li>
            </ul>
        </li>

        @endif
    </ul>
</div>