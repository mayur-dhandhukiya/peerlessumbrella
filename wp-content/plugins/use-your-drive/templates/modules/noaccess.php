<?php

namespace TheLion\UseyourDrive;

defined('ABSPATH') || exit;

$loaders = Settings::get('loaders');
?><div id='UseyourDrive'>
    <div class='UseyourDrive list-container noaccess'>
        <div style="max-width:512px; margin: 0 auto; text-align:center;">
            <img src="<?php echo $loaders['protected']; ?>" data-src-retina="<?php echo $loaders['protected']; ?>" style="display:inline-block" alt="">
            <?php echo Settings::get('userfolder_noaccess'); ?>
        </div>
    </div>
</div>