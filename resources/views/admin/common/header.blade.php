<div class="main-header" data-background-color="purple">
	<!-- Logo Header -->
	<div class="logo-header">
		
		<h3 class="font-weight-bold text-white logo"> {{ $site_name }} </h3>
		<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon">
				<i class="fa fa-bars"></i>
			</span>
		</button>
		<button class="topbar-toggler more"><i class="fa fa-ellipsis-v"></i></button>
		<div class="navbar-minimize">
			<button class="btn btn-minimize btn-rounded">
				<i class="fa fa-bars"></i>
			</button>
		</div>
	</div>
	<!-- End Logo Header -->

	<!-- Navbar Header -->
	<nav class="navbar navbar-header navbar-expand-lg">
		
		<div class="container-fluid">
			<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
				<li class="nav-item toggle-nav-search hidden-caret">
					<a class="nav-link" data-toggle="collapse" href="#search-nav" role="button" aria-expanded="false" aria-controls="search-nav">
						<i class="fa fa-search"></i>
					</a>
				</li>
				{{--
				<li class="nav-item dropdown hidden-caret">
					<a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-bell"></i>
						<span class="notification">4</span>
					</a>
					<ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
						<li>
							<div class="dropdown-title">You have 4 new notification</div>
						</li>
						<li>
							<div class="notif-scroll scrollbar-outer">
								<div class="notif-center">
									<a href="#">
										<div class="notif-icon notif-primary"> <i class="fa fa-user-plus"></i> </div>
										<div class="notif-content">
											<span class="block">
												New user registered
											</span>
											<span class="time">5 minutes ago</span> 
										</div>
									</a>
									<a href="#">
										<div class="notif-icon notif-success"> <i class="fa fa-comment"></i> </div>
										<div class="notif-content">
											<span class="block">
												Rahmad commented on Admin
											</span>
											<span class="time">12 minutes ago</span> 
										</div>
									</a>
									<a href="#">
										<div class="notif-img"> 
											<img src="../assets/img/profile2.jpg" alt="Img Profile">
										</div>
										<div class="notif-content">
											<span class="block">
												Reza send messages to you
											</span>
											<span class="time">12 minutes ago</span> 
										</div>
									</a>
									<a href="#">
										<div class="notif-icon notif-danger"> <i class="fa fa-heart"></i> </div>
										<div class="notif-content">
											<span class="block">
												Farrah liked Admin
											</span>
											<span class="time">17 minutes ago</span> 
										</div>
									</a>
								</div>
							</div>
						</li>
						<li>
							<a class="see-all" href="javascript:void(0);">See all notifications<i class="fa fa-angle-right"></i> </a>
						</li>
					</ul>
				</li>
				--}}
				<li class="nav-item dropdown hidden-caret">
					<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
						<div class="avatar-sm">
							<img src="{{ asset('images/profile.png') }}" alt="..." class="avatar-img rounded-circle">
						</div>
					</a>
					<ul class="dropdown-menu dropdown-user animated fadeIn">
						<li>
							<div class="user-box">
								<div class="avatar-lg"><img src="{{ asset('images/profile.png') }}" alt="image profile" class="avatar-img rounded"></div>
								<div class="u-text">
									<h4> {{ Auth::guard('admin')->user()->username }} </h4>
									<p class="text-muted"> {{ Auth::guard('admin')->user()->email }} </p>
								</div>
							</div>
						</li>
						<li>
							<div class="dropdown-divider"></div>
							@if(Auth::guard('admin')->user()->can('update-admin_users'))
							<a class="dropdown-item" href="{{ route('admin.admin_users.edit',['id' => Auth::guard('admin')->id() ]) }}"> Edit Profile </a>
							@endif
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="{{ route('admin.logout') }}">Logout</a>
						</li>
					</ul>
				</li>
				
			</ul>
		</div>
	</nav>
	<!-- End Navbar -->
</div>

<!-- Sidebar -->
<div class="sidebar">
	
	<div class="sidebar-background"></div>
	<div class="sidebar-wrapper scrollbar-inner">
		<div class="sidebar-content">
			<div class="user">
				<div class="avatar-sm float-left mr-2">
					<img src="{{ asset('images/profile.png') }}" alt="..." class="avatar-img rounded-circle">
				</div>
				<div class="info">
					<a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
						<span>
							{{ Auth::guard('admin')->user()->username }}
							<span class="user-level">
								{{ Auth::guard('admin')->user()->role_name }}
							</span>
							<span class="caret"></span>
						</span>
					</a>
					<div class="clearfix"></div>

					<div class="collapse in" id="collapseExample">
						<ul class="nav">
							<li>
								<a href="{{ route('admin.admin_users.edit',['id' => Auth::guard('admin')->id() ]) }}"">
									<span class="link-collapse"> Edit Profile </span>
								</a>
							</li>
							<li>
								<a href="{{ route('admin.logout') }}">
									<span class="link-collapse">Log out </span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<ul class="nav">
				@foreach($menu_data as $menu)
				<li class="nav-item {{ (isActiveRoute($menu['route'])) ? 'active' : '' }}">
					<a href="{{ route($menu['route']) }}">
						<i class="{{ $menu['icon'] }}"></i>
						<p> {{ $menu['value'] }} </p>
					</a>
				</li>
				@endforeach
			</ul>
		</div>
	</div>
</div>
<!-- End Sidebar -->