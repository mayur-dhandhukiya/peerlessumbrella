<?php
/**
 * @author WP Cloud Plugins
 * @copyright Copyright (c) 2023, WP Cloud Plugins
 *
 * @since       2.0
 * @see https://www.wpcloudplugins.com
 */

namespace TheLion\UseyourDrive;

defined('ABSPATH') || exit;

class Skeleton
{
    /**
     * Return a HTML skeleton for the File Browser module.
     *
     * @param string $type   (folder|file)
     * @param int    $amount
     */
    public static function get_browser_placeholders($type = 'folder', $amount = 1)
    {
        for ($i = 0; $i < $amount; ++$i) {
            ?>
<div class="entry <?php echo $type; ?> skeleton-entry">
    <div class="entry_block">
        <?php
        if ('file' === $type) {
            ?> <div class="entry_thumbnail skeleton skeleton-img"></div><?php
        } ?>
        <div class="entry-info">
            <div class="entry-info-icon skeleton skeleton-icon"></div>
            <div class="entry-info-name"><span class="skeleton skeleton-text"></span></div>
            <?php
        if ('file' === $type) {
            ?>
            <div class="entry-info-modified-date skeleton skeleton-text-small"></div>
            <div class="entry-info-size skeleton skeleton-text-small"></div>
            <?php
        } ?>
        </div>
    </div>
</div>
<?php
        }
    }

    /**
     * Return a HTML skeleton for the Gallery module.
     *
     * @param int $target_height
     * @param int $amount
     */
    public static function get_gallery_placeholders($target_height, $amount = 1)
    {
        $dimensions = [.5625, 1, 1.33, 1.78]; // Most common aspect ratios

        for ($i = 0; $i < $amount; ++$i) {
            $target_width = round($target_height * $dimensions[array_rand($dimensions)]); ?>

<div class="image-container entry">
    <a>
        <img class="skeleton skeleton-gallery-img" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQYV2NgAAIAAAUAAarVyFEAAAAASUVORK5CYII=" width="<?php echo $target_width; ?>" height="<?php echo $target_height; ?>" style="width:<?php echo $target_width; ?>px !important;height:<?php echo $target_height; ?>px !important; ">
    </a>
</div>
<?php
        }
    }
}
