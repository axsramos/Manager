<!-- Navbar-->
<ul class="navbar-nav ml-auto ml-md-0">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <?php
            
            if (!empty($data['FormDesign']['UserMenu'])) {
                $menu = json_decode($data['FormDesign']['UserMenu']);

                foreach ($menu->UserMenu as $itemMenu) {
                    if ($itemMenu->Description == '**[SEPARATOR]**!') {
                        echo '<div class="dropdown-divider"></div>';
                    } else {
                        echo '<a class="dropdown-item" href="' . $itemMenu->Link . '">' . $itemMenu->Icon . '</i>'. $itemMenu->Description .'</a>';
                    }
                }
            }
            ?>
        </div>
    </li>
</ul>