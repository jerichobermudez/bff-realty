<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <img src="/assets/images/admin.jpg" width="30px" class="border shadow img-circle" alt="User Image">
      </a>

      <div class="dropdown-menu dropdown-menu dropdown-menu-right py-0">
        <a href="#" class="dropdown-item"><i class="fas fa-user mr-2"></i> My Account</a>
        <a href="/logout" class="dropdown-item"><i class="fa fa-sign-out mr-2"></i> Logout</a>
      </div>
    </li>
  </ul>
</nav>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="#" class="brand-link">
    <img src="/assets/images/logo.png" alt="Marigold" class="brand-image img-circle elevation-3">
    <span class="brand-text font-weight-light ml-1">Company Name</span>
  </a>
  <div class="sidebar">
    <div class="user-panel mt-2 pb-2 d-flex align-items-center">
      <div class="image">
        <img src="/assets/images/admin.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <?php 
        $pageUrl = $_SERVER["REQUEST_URI"] ?? "";
      ?>
      <div class="info">
        <span class="text-white d-block"><?= $name ?? "" ?></span>
        <!-- <span class="text-light text-xs">Role: <?= UserRole::getTextValue($role) ?></span> -->
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar nav-legacy flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="/dashboard" class="nav-link <?= $pageUrl == '/dashboard' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <?php $isClientsOpen = in_array($pageUrl, ['/clients', '/clients-add']) ? 'menu-open active' : '' ?>
        <li class="nav-item <?= $isClientsOpen ?>">
          <a href="#" class="nav-link <?= $isClientsOpen ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Clients<i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/clients" class="nav-link <?= $pageUrl == '/clients' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>List</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/clients-add" class="nav-link <?= $pageUrl == '/clients-add' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Add Client</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="/payments" class="nav-link <?= $pageUrl == '/payments' ? 'active' : '' ?>">
            <i class="nav-icon fa fa-hand-holding-dollar"></i>
            <p>Payments</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="/expenses" class="nav-link <?= $pageUrl == '/expenses' ? 'active' : '' ?>">
            <i class="nav-icon fa fa-dollar"></i>
            <p>Expenses</p>
          </a>
        </li>
        <?php $isReportsOpen = in_array($pageUrl, ['/reports-by-date', '/reports-by-sale']) ? 'menu-open active' : '' ?>
        <li class="nav-item <?= $isReportsOpen ?>">
          <a href="#" class="nav-link <?= $isReportsOpen ?>">
            <i class="nav-icon fas fa-chart-column"></i>
            <p>Reports<i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/reports-by-date" class="nav-link <?= $pageUrl == '/reports-by-date' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>B/W Date Reports</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/reports-by-sale" class="nav-link <?= $pageUrl == '/reports-by-sale' ? 'active' : '' ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Sales Reports</p>
              </a>
            </li>
          </ul>
        </li>
        <?php $isSettingsOpen = in_array($pageUrl, ['/projects', '/users']) ? 'menu-open active' : '' ?>
        <li class="nav-item <?= $isSettingsOpen ?>">
          <a href="#" class="nav-link <?= $isSettingsOpen ?>">
            <i class="nav-icon fas fa-cogs"></i>
            <p>Settings<i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/projects" class="nav-link <?= $pageUrl == '/projects' ? 'active' : '' ?>">
                <i class="far fa-circle text-sm nav-icon"></i>
                <p>Projects</p>
              </a>
            </li>
            <?php if ((int) $role === (int) UserRole::ADMIN) { ?>
              <li class="nav-item">
                <a href="/users" class="nav-link <?= $pageUrl == '/users' ? 'active' : '' ?>">
                  <i class="far fa-circle text-sm nav-icon"></i>
                  <p>Users</p>
                </a>
              </li>
            <?php } ?>
          </ul>
        </li>
      </ul>
    </nav>
  </div>
</aside>