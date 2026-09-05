<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <?php
                if (!empty($data['FormDesign']['SideMenu'])) {
                    $menu = json_decode($data['FormDesign']['SideMenu']);
                    if ($menu->SideMenu) {
                        foreach ($menu->SideMenu as $menu_item) {
                            echo '<div class="sb-sidenav-menu-heading">'. $menu_item->Description .'</div>';
                            if ($menu_item->Items) {
                                foreach ($menu_item->Items as $items) {
                                    if ($items->SubItems) {
                                        echo '<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse'. $items->Anchor .'" aria-expanded="false" aria-controls="collapse'. $items->Anchor .'">';
                                        echo '    <div class="sb-nav-link-icon"><i class="'. $items->Icon .'"></i></div>';
                                        echo '    '. $items->Description;
                                        echo '    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>';
                                        echo '</a>';

                                        echo '<div class="collapse" id="collapse'. $items->Anchor .'" aria-labelledby="headingOne" data-parent="#sidenavAccordion">';
                                        echo '    <nav class="sb-sidenav-menu-nested nav">';
                                        foreach($items->SubItems as $subitem) {
                                            if ($subitem->SubItems) {
                                                echo '<div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">';
                                                echo '    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">';
                                                $first = true;
                                                foreach ($subitem->SubItems as $itemsSubitem) {
                                                    if ($first) {
                                                        $first = false;
                                                        echo '        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#pagesCollapse'. $subitem->Anchor .'" aria-expanded="false" aria-controls="pagesCollapse'. $subitem->Anchor .'">';
                                                        echo '            '. $subitem->Description;
                                                        echo '            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>';
                                                        echo '        </a>';
                                                        echo '        <div class="collapse" id="pagesCollapse'. $subitem->Anchor .'" aria-labelledby="headingOne" data-parent="#sidenavAccordionPages">';
                                                        echo '            <nav class="sb-sidenav-menu-nested nav">';
                                                    }
                                                    echo '                <a class="nav-link" href="'. $itemsSubitem->Link .'">'. $itemsSubitem->Description .'</a>';
                                                }
                                                if (!$first) {
                                                    echo '            </nav>';
                                                    echo '        </div>';
                                                }
                                                echo '    </nav>';
                                                echo '</div>';
                                            } else {
                                                echo '        <a class="nav-link" href="'. $subitem->Link .'">'. $subitem->Description .'</a>';
                                            }
                                        }
                                        echo '    </nav>';
                                        echo '</div>';
                                    } else {
                                        echo '<a class="nav-link" href="'. $items->Link .'">';
                                        echo '    <div class="sb-nav-link-icon"><i class="'. $items->Icon .'"></i></div>';
                                        echo '    '. $items->Description;
                                        echo '</a>';
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <?= App\Core\AuthSession::get()['USR_LOGGED']; ?>
        </div>
    </nav>
</div>