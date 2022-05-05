  <div class="app-sidebar colored">
	<div class="sidebar-header">
		<a class="header-brand" href="dashboard.php">
			<div class="logo-img">
			   <img width="30px" height="30px" src="<?= PROJECT_LOGO; ?>" class="header-brand-img"> 
			</div>
			<span class="text"><?= PROJECT_NAME; ?></span>
		</a>
		<button type="button" class="nav-toggle"><i data-toggle="expanded" class="ik ik-toggle-right toggle-icon"></i></button>
		<button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
	</div>

	<div class="sidebar-content">
		<div class="nav-container">
			<nav id="main-menu-navigation" class="navigation-main">
				<div class="nav-lavel">Navigation</div>
				<div class="nav-item active">
					<a href="dashboard.php"><i class="ik ik-bar-chart-2"></i><span>Dashboard</span></a>
				</div>
				<!-- <div class="nav-item">
					<a href="pages/navbar.html"><i class="ik ik-menu"></i><span>Navigation</span> <span class="badge badge-success">New</span></a>
				</div>
				<div class="nav-item has-sub">
					<a href="javascript:void(0)"><i class="ik ik-layers"></i><span>Widgets</span> <span class="badge badge-danger">150+</span></a>
					<div class="submenu-content">
						<a href="pages/widgets.html" class="menu-item">Basic</a>
						<a href="pages/widget-statistic.html" class="menu-item">Statistic</a>
						<a href="pages/widget-data.html" class="menu-item">Data</a>
						<a href="pages/widget-chart.html" class="menu-item">Chart Widget</a>
					</div>
				</div> -->
				<div class="nav-lavel">Users Types</div>
				<div class="nav-item has-sub">
					<a href="#"><i class="ik ik-box"></i><span>Admin</span></a>
					<div class="submenu-content">
						<a href="admin_all.php" class="menu-item">All admin</a>
<!-- 						<a href="admin_all_agents.php" class="menu-item">Agents</a>
	 -->
					</div>
				</div>
				<div class="nav-item has-sub">
					<a href="#"><i class="ik ik-box"></i><span>Merchants</span></a>
					<div class="submenu-content">
						<a href="store_all.php" class="menu-item">All Stores</a>
						<a href="store_categories.php" class="menu-item">Categories</a>
					</div>
				</div>
				<div class="nav-item has-sub">
					<a href="#"><i class="ik ik-gitlab"></i><span>Users</span> <span class="badge badge-success">New</span></a>
					<div class="submenu-content">
						<a href="user_all.php" class="menu-item">All Users</a>
						<a href="user_filter_location.php" class="menu-item">By Location</a>
						<a href="user_filter_gender.php" class="menu-item">By Gender</a>
						<a href="user_filter_age.php" class="menu-item">By Age</a>
					
					</div>
				</div>
				<div class="nav-item has-sub">
					<a href="#"><i class="ik ik-gitlab"></i><span>Drivers</span> <span class="badge badge-success">New</span></a>
					<div class="submenu-content">
						<a href="pages/ui/modals.html" class="menu-item">All drivers</a>
						<a href="pages/ui/notifications.html" class="menu-item">Riders</a>
						<a href="pages/ui/carousel.html" class="menu-item">Bikers</a>
					</div>
				</div>
<!-- 				<div class="nav-item has-sub">
					<a href="#"><i class="ik ik-package"></i><span>Extra</span></a>
					<div class="submenu-content">
						<a href="pages/ui/session-timeout.html" class="menu-item">Session Timeout</a>
					</div>
				</div>
				<div class="nav-item">
					<a href="pages/ui/icons.html"><i class="ik ik-command"></i><span>Icons</span></a>
				</div>
				<div class="nav-lavel">Forms</div>
				<div class="nav-item has-sub">
					<a href="#"><i class="ik ik-edit"></i><span>Forms</span></a>
					<div class="submenu-content">
						<a href="pages/form-components.html" class="menu-item">Components</a>
						<a href="pages/form-addon.html" class="menu-item">Add-On</a>
						<a href="pages/form-advance.html" class="menu-item">Advance</a>
					</div>
				</div>
				<div class="nav-item">
					<a href="pages/form-picker.html"><i class="ik ik-terminal"></i><span>Form Picker</span> <span class="badge badge-success">New</span></a>
				</div>

				<div class="nav-lavel">Tables</div>
				<div class="nav-item">
					<a href="pages/table-bootstrap.html"><i class="ik ik-credit-card"></i><span>Bootstrap Table</span></a>
				</div>
				<div class="nav-item">
					<a href="pages/table-datatable.html"><i class="ik ik-inbox"></i><span>Data Table</span></a>
				</div>

				<div class="nav-lavel">Charts</div>
				<div class="nav-item has-sub">
					<a href="#"><i class="ik ik-pie-chart"></i><span>Charts</span> <span class="badge badge-success">New</span></a>
					<div class="submenu-content">
						<a href="pages/charts-chartist.html" class="menu-item active">Chartist</a>
						<a href="pages/charts-flot.html" class="menu-item">Flot</a>
						<a href="pages/charts-knob.html" class="menu-item">Knob</a>
						<a href="pages/charts-amcharts.html" class="menu-item">Amcharts</a>
					</div>
				</div>

				<div class="nav-lavel">Apps</div>
				<div class="nav-item">
					<a href="pages/calendar.html"><i class="ik ik-calendar"></i><span>Calendar</span></a>
				</div>
				<div class="nav-item">
					<a href="pages/taskboard.html"><i class="ik ik-server"></i><span>Taskboard</span></a>
				</div>

				<div class="nav-lavel">Pages</div>

				<div class="nav-item has-sub">
					<a href="#"><i class="ik ik-lock"></i><span>Authentication</span></a>
					<div class="submenu-content">
						<a href="pages/login.html" class="menu-item">Login</a>
						<a href="pages/register.html" class="menu-item">Register</a>
						<a href="pages/forgot-password.html" class="menu-item">Forgot Password</a>
					</div>
				</div>
				<div class="nav-item has-sub">
					<a href="#"><i class="ik ik-file-text"></i><span>Other</span></a>
					<div class="submenu-content">
						<a href="pages/profile.html" class="menu-item">Profile</a>
						<a href="pages/invoice.html" class="menu-item">Invoice</a>
					</div>
				</div>
				<div class="nav-item">
					<a href="pages/layouts.html"><i class="ik ik-layout"></i><span>Layouts</span><span class="badge badge-success">New</span></a>
				</div> -->
				<div class="nav-lavel">Products</div>
				<div class="nav-item has-sub">
					<a href="javascript:void(0)"><i class="ik ik-list"></i><span>Products</span></a>
					<div class="submenu-content">
						<a href="products_categories.php" class="menu-item">Product Categories</a>
						<a href="products_all.php" class="menu-item">All Products</a>
						<a href="products_featured.php" class="menu-item">Featured Products</a>
					</div>
				</div>

				<div class="nav-lavel">Transactions</div>

				<div class="nav-item has-sub">
					<a href="javascript:void(0)"><i class="ik ik-list"></i><span>Orders</span></a>
					<div class="submenu-content">
						<a href="purchase_all.php" class="menu-item">All Purchase Orders</a>
						<a href="products_all.php" class="menu-item">All Products</a>
						<a href="products_featured.php" class="menu-item">Featured Products</a>
					</div>
				</div>
				<div class="nav-item has-sub">
					<a href="javascript:void(0)"><i class="ik ik-list"></i><span>Bookings</span></a>
					<div class="submenu-content">
						<a href="products_categories.php" class="menu-item">Product Categories</a>
						<a href="products_all.php" class="menu-item">All Products</a>
						<a href="products_featured.php" class="menu-item">Featured Products</a>
					</div>
				</div>
				<div class="nav-item has-sub">
					<a href="javascript:void(0)"><i class="ik ik-list"></i><span>Payment</span></a>
					<div class="submenu-content">
						<a href="message_payment_logs.php" class="menu-item">Payment Transaction</a>
						<a href="products_all.php" class="menu-item">Messages</a>
						
					</div>
				</div>

				<div class="nav-lavel">System</div>
				<div class="nav-item has-sub">
					<a href="javascript:void(0)"><i class="ik ik-list"></i><span>Notifications</span></a>
					<div class="submenu-content">
						<a href="notification_send.php" class="menu-item">In-App Notification</a>
						<a href="notification_popup.php" class="menu-item">Popup Notification</a>
						<a href="notification_sms.php" class="menu-item">SMS Notification</a>
					</div>
				</div>
				<div class="nav-item has-sub">
					<a href="javascript:void(0)"><i class="ik ik-list"></i><span>Locations</span></a>
					<div class="submenu-content">
						<a href="location_service.php" class="menu-item">Service Location</a>
						<a href="location_godsview.php" class="menu-item">Gods View</a>
						<a href="location_heatview.php" class="menu-item">Heat View</a>
					</div>

				</div>
			
			
				<div class="nav-lavel">Settings</div>
				<div class="nav-item">
					<a href="configuration_general.php"><i class="ik ik-monitor"></i><span>Configuration</span></a>
				</div>
				<div class="nav-item">
					<a href="javascript:void(0)"><i class="ik ik-help-circle"></i><span>Settings</span></a>
				</div>
			</nav>
		</div>
	</div>
</div>