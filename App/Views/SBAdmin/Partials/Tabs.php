<div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
        <?php
        if (isset($data['FormDesign']['Tabs']['Template'])) {
            foreach ($data['FormDesign']['Tabs']['Items'] as $key => $item) {
                $classActive = '';
    
                if ($key == $data['FormDesign']['Tabs']['Current']) {
                    $classActive = 'active';
                } else {
                    if ($item['IsDisabled']) {
                        $classActive = 'disabled';
                    }
                }
    
                echo '<li class="nav-item">';
                echo '<a class="nav-link '. $classActive .'" href="' . $item['Link'] . '">' . $item['Name'] . '</a>';
                echo '</li>';
            }
        } else {
            echo '<li class="nav-item">&nbsp;</li>';
        }
        ?>
    </ul>
</div>