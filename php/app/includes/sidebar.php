<body class="skin-yellow fixed sidebar-mini">
  <div class="wrapper">
    <header class="main-header">
      <div class="logo">
        <span class="logo-mini">BFF</span>
        <span class="logo-lg"><b>BFF</b> Realty</span>
      </div>
      <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="/assets/images/admin.jpg" width="30px" class="user-image" alt="User Image">
                <span class="hidden-xs"><?= $name ?? "" ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header">
                  <img src="/assets/images/admin.jpg" width="30px" class="img-circle" alt="User Image">
                  <p>
                    <?= $name ?? "" ?>
                    <small><?= UserRole::getTextValue($role) ?></small>
                  </p>
                </li>
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="#" class="btn btn-default btn-flat">My Account</a>
                  </div>
                  <div class="pull-right">
                    <a href="/logout" class="btn btn-default btn-flat">Sign out</a>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>

    <aside class="main-sidebar">
      <section class="sidebar">
        <div class="user-panel">
          <div class="pull-left image">
            <img src="/assets/images/admin.jpg" width="30px" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p><?= $name ?? "" ?></p>
            <small>Role: <?= UserRole::getTextValue($role) ?></small>
          </div>
        </div>
        <?php 
          $pageUrl = $_SERVER["REQUEST_URI"] ?? "";
        ?>
        <ul class="sidebar-menu" data-widget="tree">
          <li class="header">MAIN NAVIGATION</li>
          <li class="<?= $pageUrl == '/dashboard' ? 'active' : '' ?>">
            <a href="/dashboard">
              <i class="fas fa-chart-pie fa-fw"></i> <span>Dashboard</span>
            </a>
          </li>
          <?php $isClientsOpen = in_array($pageUrl, ['/clients', '/clients-add']) ? 'menu-open active' : '' ?>
          <li class="treeview <?= $isClientsOpen ?>">
            <a href="#">
              <i class="fas fa-users fa-fw"></i> <span>Clients</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="<?= $pageUrl == '/clients' ? 'active' : '' ?>">
                <a href="/clients"><i class="far fa-circle fa-sm"></i> List</a>
              </li>
              <li class="<?= $pageUrl == '/clients-add' ? 'active' : '' ?>">
                <a href="/clients-add"><i class="far fa-circle fa-sm"></i> Add Client</a>
              </li>
            </ul>
          </li>
          <li class="<?= $pageUrl == '/payments' ? 'active' : '' ?>">
            <a href="/payments">
              <i class="fa fa-hand-holding-dollar fa-fw"></i><span>Payments</span>
            </a>
          </li>
          <li class="<?= $pageUrl == '/commissions' ? 'active' : '' ?>">
            <a href="/commissions">
              <i class="fa fa-dollar fa-fw"></i><span>Commissions</span>
            </a>
          </li>
          <?php $isReportsOpen = in_array($pageUrl, ['/reports-by-sale']) ? 'menu-open active' : '' ?>
          <li class="treeview <?= $isReportsOpen ?>">
            <a href="#">
              <i class="fas fa-chart-column fa-fw"></i> <span>Reports</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="<?= $pageUrl == '/reports-by-sale' ? 'active' : '' ?>">
                <a href="/reports-by-sale"><i class="far fa-circle fa-sm"></i> Sales Reports</a>
              </li>
            </ul>
          </li>
          <?php $isSettingsOpen = in_array($pageUrl, ['/banks', '/projects', '/vouchers', '/users']) ? 'menu-open active' : '' ?>
          <li class="treeview <?= $isSettingsOpen ?>">
            <a href="#">
              <i class="fas fa-wrench fa-fw"></i> <span>Settings</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li class="<?= $pageUrl == '/banks' ? 'active' : '' ?>">
                <a href="/banks"><i class="far fa-circle fa-sm"></i> Banks</a>
              </li>
              <li class="<?= $pageUrl == '/projects' ? 'active' : '' ?>">
                <a href="/projects"><i class="far fa-circle fa-sm"></i> Projects</a>
              </li>
              <?php if ((int) $role === (int) UserRole::ADMIN) { ?>
                <li class="<?= $pageUrl == '/vouchers' ? 'active' : '' ?>">
                  <a href="/vouchers"><i class="far fa-circle fa-sm"></i> Vouchers</a>
                </li>
                <li class="<?= $pageUrl == '/users' ? 'active' : '' ?>">
                  <a href="/users"><i class="far fa-circle fa-sm"></i> Users</a>
                </li>
              <?php } ?>
            </ul>
          </li>
        </ul>
      </section>
    </aside>