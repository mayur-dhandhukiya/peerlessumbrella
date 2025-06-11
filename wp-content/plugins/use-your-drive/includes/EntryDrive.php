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

class EntryDrive extends EntryAbstract
{
    public function convert_api_entry($api_entry, $virtual_folder = false)
    {
        if (
            !$api_entry instanceof \UYDGoogle_Service_Drive_Drive
        ) {
            Helpers::log_error('Google response is not a valid entry.', 'Entry', null, __LINE__);

            exit;
        }

        // //Folders that only have a structural function, My Drive and Shared Drives are such folders
        $this->set_virtual_folder($virtual_folder);

        // Normal Meta Data
        $this->set_id($api_entry->getId());
        $this->set_drive_id($api_entry->getId());
        $this->set_name($api_entry->getName());
        $this->set_basename($api_entry->getName());
        $this->set_is_dir(true);
        $this->set_size(($this->is_dir()) ? 0 : $api_entry->getSize());

        // Set Permission
        $capabilities = $api_entry->getCapabilities();

        if (!empty($capabilities)) {
            $this->set_can_edit_by_cloud(false);
            $canadd = $capabilities->getCanEdit();
            $canrename = $capabilities->getCanEdit();
        }

        // Set the permissions
        $permissions = [
            'canpreview' => false,
            'candownload' => false,
            'canshare' => false,
            'candelete' => false,
            'cantrash' => false,
            'canadd' => $canadd,
            'canrename' => $canrename,
            'canmove' => false,
        ];

        $this->set_permissions($permissions);

        // Thumbnail
        $this->set_thumbnails($api_entry->getBackgroundImageLink());
    }

    public function set_thumbnails($thumbnail)
    {
        $this->set_has_own_thumbnail(true);

        if (false === strpos($thumbnail, '=')) {
            $thumbnail = $thumbnail.'=w1920-h216-n';
        }

        $this->set_thumbnail_icon(str_replace('=w1920-h216-n', '=s16-c-nu', $thumbnail));
        $this->set_thumbnail_small(str_replace('=w1920-h216-n', '=w500-h375-c-nu', $thumbnail));
        $this->set_thumbnail_small_cropped(str_replace('=w1920-h216-n', '=w500-h375-c-nu', $thumbnail));
        $this->set_thumbnail_large(str_replace('=w1920-h216-n', '', $thumbnail));
        $this->set_thumbnail_original($thumbnail);
    }

    public function get_thumbnail_with_size($thumbnailsize)
    {
        if (false !== strpos($this->get_thumbnail_small(), 'use-your-drive-cache')) {
            return $this->get_thumbnail_small();
        }

        return str_replace('=w500-h375-nu', '='.$thumbnailsize, $this->get_thumbnail_small());
    }
}
