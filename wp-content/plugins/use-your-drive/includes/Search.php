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

class Search
{
    /**
     * @var CacheNode
     */
    private $_top_folder_node;

    /**
     * @var string
     */
    private $_query;

    public function do_search($query, CacheNode $folder_node)
    {
        $this->_top_folder_node = $folder_node;
        $this->_query = $query;

        $cache_name = Helpers::filter_filename(App::get_current_account()->get_id().'_'.Processor::instance()->get_listtoken().$folder_node->get_id().'_search_'.$query, false);
        $cached_request = new CacheRequest([], $cache_name);

        if (false === $cached_request->is_cached()) {
            // Make sure that all subfolders are loaded
            if (isset($_REQUEST['data'], $_REQUEST['data']['discover_folder_ids'])) {
                $folders = [];

                foreach (explode('|', $_REQUEST['data']['discover_folder_ids']) as $folder_id) {
                    $folders[$folder_id] = Cache::instance()->get_node_by_id($folder_id);
                }

                $this->load_folders($folders);
            } else {
                $this->load_folders($folder_node->get_all_sub_folders());
            }

            // Mark folder as completely loaded
            $folder_node->set_loaded_all_childfolders(true);

            // Search
            $api_results = $this->query();

            // Cache the search request
            $cached_request->add_cached_response(serialize($api_results));
        } else {
            $api_results = unserialize($cached_request->get_cached_response());
        }

        // Process the results and store them in cache
        $entries_found = $this->process($api_results);

        // Filter them for this specific request
        $entries_filtered = $this->filter($entries_found);

        // Save the cache
        Cache::instance()->set_updated();
        Cache::instance()->update_cache(false);

        return $entries_filtered;
    }

    public function load_folders($folders = [])
    {
        Helpers::set_time_limit(60);

        // Don't load folders that were loaded previously
        foreach ($folders as $id => $folder) {
            // Remove unauthorized folders from list
            if (false === Processor::instance()->_is_entry_authorized($folder)) {
                unset($folders[$id]);

                continue;
            }

            if ($folder->has_loaded_all_childfolders()) {
                unset($folders[$id]);

                continue;
            }

            if ($folder->has_loaded_children()) {
                unset($folders[$id]);
                $folders = array_merge($folders, $folder->get_all_sub_folders());
            }
        }

        // Remove virtual folders
        unset($folders['shared-drives'], $folders['shared-with-me'], $folders['computers']);

        // Can only make list calls with 99 parents, so split them if required
        $folders_id = array_keys($folders);
        $requests = array_chunk($folders_id, 99, true);

        $folders_found = [];
        foreach ($requests as $request_folder_ids) {
            $new_folders_found = $this->get_multiple_folders($request_folder_ids);
            $folders_found = array_merge($folders_found, $new_folders_found);
        }

        $new_found_sub_folders = [];
        foreach ($folders_found as $folder_entry) {
            $cached_node = Cache::instance()->is_cached($folder_entry->get_id(), 'id', 'as_parent');

            if (false === $cached_node) {
                $cached_node = Cache::instance()->add_to_cache($folder_entry);
                $cached_node->set_entry($folder_entry);
                $cached_node->set_loaded(false);
                $cached_node->set_updated();
            }
            $new_found_sub_folders[$cached_node->get_id()] = $cached_node;
        }

        // Save the cache
        Cache::instance()->set_updated();
        Cache::instance()->update_cache(false);

        if (!empty($new_found_sub_folders)) {
            echo json_encode([
                'result' => 'discovering_folders',
                'async' => true,
                'data' => [
                    'discover_folder_ids' => implode('|', array_keys($new_found_sub_folders)),
                ],
            ]);

            exit;
        }

        return true;
    }

    public function get_multiple_folders($parents_ids)
    {
        // Set the parents
        if (1 === count($parents_ids)) {
            $parents_query = " and ('".reset($parents_ids)."' in parents) ";
        } else {
            $parents_query = " and ('".implode("' in parents or '", $parents_ids)."' in parents) ";
        }

        $drive_id = $this->get_top_folder_node()->get_drive_id();

        // Find all items containing query
        $params = [
            'q' => "mimeType = 'application/vnd.google-apps.folder' {$parents_query} and trashed = false",
            'fields' => Client::instance()->apilistfilesfields,
            'pageSize' => 999,
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => (!in_array($drive_id, ['drive', 'mydrive', null])),
            'corpora' => (in_array($drive_id, ['drive', 'mydrive', null])) ? 'user' : 'drive',
        ];

        if (App::get_current_account()->has_app_folder_access()) {
            $params['spaces'] = 'appDataFolder';
        }

        if (!in_array($drive_id, ['drive', 'mydrive', null])) {
            $params['driveId'] = $drive_id;
        }

        // Do the request
        $nextpagetoken = null;
        $folders_found = [];
        $entries_found = [];

        do {
            try {
                $search_response = App::instance()->get_drive()->files->listFiles($params);
            } catch (\Exception $ex) {
                Helpers::log_error('Search received API error.', 'Search', null, __LINE__);

                return [];
            }

            // Process the response
            $more_files = $search_response->getFiles();
            $folders_found = array_merge($folders_found, $more_files);

            $nextpagetoken = $search_response->getNextPageToken();
            $params['pageToken'] = $nextpagetoken;
        } while (null !== $nextpagetoken);

        foreach ($folders_found as $folder) {
            $folder_entry = new Entry($folder);
            $entries_found[] = $folder_entry;
        }

        return $entries_found;
    }

    public function query()
    {
        $all_subfolders = $this->get_top_folder_node()->get_all_child_folders();

        // Remove unauthorized folders from list
        foreach ($all_subfolders as $folder_id => $folder_node) {
            if (false === Processor::instance()->_is_entry_authorized($folder_node)) {
                unset($all_subfolders[$folder_id]);
            }
        }

        $folders_to_look_in = array_merge([$this->get_top_folder_node()->get_id() => $this->get_top_folder_node()], $all_subfolders);
        $api_results = [];

        // Remove virtual folders
        unset($folders_to_look_in['drive'], $folders_to_look_in['shared-drives'], $folders_to_look_in['shared-with-me'], $folders_to_look_in['computers']);

        // Get all folder IDs
        $folders_id = array_keys($folders_to_look_in);

        // Set search field
        if ('1' === Processor::instance()->get_shortcode_option('searchcontents')) {
            $field = 'fullText';
        } else {
            $field = 'name';
        }

        // Set the right corpora
        $drive_id = $this->get_top_folder_node()->get_drive_id();

        // Find all items containing query
        $params = [
            'fields' => Client::instance()->apilistfilesfields,
            'pageSize' => 500,
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => (!in_array($drive_id, ['mydrive', null])),
            'corpora' => (in_array($drive_id, ['mydrive', null])) ? 'user' : 'drive',
        ];

        if (App::get_current_account()->has_app_folder_access()) {
            $params['spaces'] = 'appDataFolder';
        }

        if (!in_array($drive_id, ['mydrive', null])) {
            $params['driveId'] = $drive_id;
        }

        // Max 99 parents per request
        $requests = array_chunk($folders_id, 99, true);

        foreach ($requests as $request_folder_ids) {
            if (empty($request_folder_ids)) {
                $parents_query = '';
            } elseif (1 === count($request_folder_ids)) {
                $parents_query = " and ('".$this->get_top_folder_node()->get_id()."' in parents) ";
            } else {
                $parents_query = " and ('".implode("' in parents or '", $request_folder_ids)."' in parents) ";
            }

            $params['q'] = "{$field} contains '".stripslashes($this->get_query())."' {$parents_query} and trashed = false";

            $params = apply_filters('useyourdrive_api_search_set_query', $params);

            // Do the request
            $nextpagetoken = null;

            do_action('useyourdrive_log_event', 'useyourdrive_searched', $this->get_top_folder_node(), ['query' => $this->get_query()]);

            do {
                try {
                    $search_response = App::instance()->get_drive()->files->listFiles($params);
                } catch (\Exception $ex) {
                    Helpers::log_error('Search received API error.', 'Search', null, __LINE__);

                    return [];
                }

                // Process the response
                $more_files = $search_response->getFiles();
                $api_results = array_merge($api_results, $more_files);

                $nextpagetoken = $search_response->getNextPageToken();
                $params['pageToken'] = $nextpagetoken;
            } while (null !== $nextpagetoken);
        }

        return $api_results;
    }

    public function process($api_results = [])
    {
        $entries_found = [];
        $new_parent_folders = [];
        $entries_in_searchedfolder = [];

        // Convert API results into Entries
        foreach ($api_results as $file) {
            $file_entry = new Entry($file);
            $entries_found[] = $file_entry;
            if ($file_entry->has_parent()) {
                $parent_id = $file_entry->get_parent_id();
                if (false === Cache::instance()->get_node_by_id($parent_id, false)) {
                    $new_parent_folders[$parent_id] = $parent_id;
                }
            }
        }

        // Load all new parents at once and store them in cache
        if (false === empty($new_parent_folders)) {
            $new_parents_folders_api = Client::instance()->get_multiple_entries($new_parent_folders);
            foreach ($new_parents_folders_api as $parent) {
                if (!$parent instanceof EntryAbstract) {
                    $parent = new Entry($parent);
                }

                Cache::instance()->add_to_cache($parent);
            }
        }

        // Add / Update files in cache
        foreach ($entries_found as $entry) {
            $cachedentry = Cache::instance()->add_to_cache($entry);

            $entries_in_searchedfolder[] = $cachedentry;
        }

        return $entries_in_searchedfolder;
    }

    public function filter($entries_filtered)
    {
        foreach ($entries_filtered as $key => $cached_node) {
            if (false === Processor::instance()->_is_entry_authorized($cached_node)) {
                unset($entries_filtered[$key]);
            }
        }

        return $entries_filtered;
    }

    /**
     * Get the value of _top_folder_node.
     *
     * @return CacheNode
     */
    public function get_top_folder_node()
    {
        return $this->_top_folder_node;
    }

    /**
     * Set the value of _top_folder_node.
     *
     * @return self
     */
    public function set_top_folder_node(CacheNode $_top_folder_node)
    {
        $this->_top_folder_node = $_top_folder_node;

        return $this;
    }

    /**
     * Get the value of _query.
     *
     * @return string
     */
    public function get_query()
    {
        return $this->_query;
    }

    /**
     * Set the value of _query.
     *
     * @return self
     */
    public function set_query(string $_query)
    {
        $this->_query = $_query;

        return $this;
    }
}
