<?php

if (!defined('ABSPATH')) {
    die;
}

class Guaven_woo_search_analytics
{
    public function init()
    {
        if (get_option('guaven_woos_sa_table_done') != 1) {
          $this->reports_db_construct();
          update_option('guaven_woos_sa_table_done', 1);
        }
    }

    public function run()
    {
        $this->save_settings();
        $this->init();

        $limit = 'limit 500';
        if (isset($_GET["lmt"])) {
            $limit = esc_sql('limit '.intval($_GET["lmt"]));
            echo $limit;
        }
        $tabledata = $this->make_table_data($limit);
        $osdata = $this->make_os_data();
        $chartdata = $this->make_chart_data($limit);
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/view-analytics.php';

        return;
    }

    public function make_table_data($limit)
    {
        global $wpdb;
        $date_and_state = $this->build_where();
        $flt            = $date_and_state[3];
        if ($flt == 'popular') {
            $sql = 'select *,count(*)  as date_or_count from ' . $wpdb->prefix . 'woos_search_analytics where 1=1
            ' . $date_and_state[0] . ' ' . $date_and_state[1] . ' ' . $date_and_state[2] . ' group by keyword order by date_or_count desc ' . $limit;
        } elseif ($flt == 'popular_uniq') {
            $sql = 'select *,count(keyword) as date_or_count from (select DISTINCT keyword,user_info,state,device_type,ID,created_date
  from ' . $wpdb->prefix . 'woos_search_analytics) a where 1=1 ' . $date_and_state[0] . ' ' . $date_and_state[1] . ' ' . $date_and_state[2] . '
  group by keyword order by date_or_count desc ' . $limit;
        } else {
            $sql = 'select user_info,device_type,keyword,state,created_date as date_or_count, ID
  from ' . $wpdb->prefix . 'woos_search_analytics where 1=1 ' . $date_and_state[0] . ' ' . $date_and_state[1] . ' ' . $date_and_state[2] . ' order by ID desc ' . $limit;
        }
        return $wpdb->get_results($sql);
    }

    public function make_os_data()
    {

        $color_codes = array(
            "'mobile'" => "'#F3D43B'",
            "'mobile_iOS'" => "'#147EFB'",
            "'desktop'" => "'#5F6368'",
            "'desktop_Windows'" => "'#0078D7'",
            "'desktop_Linux'" => "'#FCC624'",
            "'mobile_Android'" => "'#3DDC84'",
            "'Other'" => "'#9E9E9E'"
        );
      

        global $wpdb;
        $date_and_state = $this->build_where();
        $flt            = $date_and_state[3];
        if ($flt == 'popular_uniq') {
            $sql = 'select device_type,count(*) say from (select DISTINCT keyword,user_info,state,device_type,ID,created_date
  from ' . $wpdb->prefix . 'woos_search_analytics) a where 1=1 ' . $date_and_state[0] . ' ' . $date_and_state[1] . ' ' . $date_and_state[2] . '
  group by device_type order by date_or_count desc ' ;
        } else {
            $sql = 'select device_type,count(*) say
  from ' . $wpdb->prefix . 'woos_search_analytics where 1=1 ' . $date_and_state[0] . ' ' . $date_and_state[1] . ' ' . $date_and_state[2] . ' group by device_type order by ID desc ' . $limit;
        }
        $results=$wpdb->get_results($sql,ARRAY_A);
        if(!is_array($results))return false;
        $device_types=[];
        foreach($results as $result){
            $device_types["'".$result['device_type']."'"]="'".$result['say']."'";
        }
        ksort($device_types);

        $colors=[];
        foreach($device_types as $key=>$device_type){
            $colors[$key]=$color_codes[$key]??"'#9B59B6'";
        }
        //var_dump($colors);
        return  [$device_types,$colors];
    }


    public function make_chart_data($limit)
    {
        global $wpdb;
        $date_and_state    = $this->build_where();
        $sql               = 'select state,count(state) say,created_date,device_type
  from ' . $wpdb->prefix . 'woos_search_analytics where 1=1  ' . $date_and_state[0] . ' ' . $date_and_state[1] . '  ' . $date_and_state[2] . '
  group by state,created_date order by created_date asc ' . $limit;
        $chartres          = $wpdb->get_results($sql);
        $crkeys            = array();
        $crvalues          = array();
        $devicetype        = array();
        $devicetype_labels = '';
        $devicetype_values = '';
        foreach ($chartres as $key => $value) {
            $crkeys[$value->created_date]                  = "'" . $value->created_date . "'";
            $crvalues[$value->state][$value->created_date] = $value->say;
            if (empty($devicetype[$value->device_type])) {
                $devicetype[$value->device_type] = 0;
            }
            $devicetype[$value->device_type] += $value->say;
        }

        foreach ($devicetype as $key => $value) {
            $devicetype_labels .= "'" . $key . "',";
            $devicetype_values .= $value . ',';
        }

        $crdef = array();
        foreach ($crkeys as $key => $value) {
            $crdef['fail'][$key]      = 0;
            $crdef['success'][$key]   = 0;
            $crdef['corrected'][$key] = 0;
            $crdef['all'][$key]       = 0;
        }

        foreach ($crvalues as $key => $crvalue) {
            foreach ($crvalue as $ke => $crval) {
                $crdef[$key][$ke]  = $crval;
                $crdef['all'][$ke] = !empty($date_and_state) ? 0 : ($crdef['all'][$ke] + $crval);
            }
        }

        return array(
            $crkeys,
            $crdef,
            $devicetype_labels,
            $devicetype_values,
            $date_and_state[4],
            $date_and_state[5]
        );
    }



    public function build_where()
    {

        $fromdate=date("Y-m-d",time()-3600*24*7);
        $todate=date("Y-m-d",time());

        if(isset($_POST["days_from"]) and strlen($_POST["days_from"])==10){
            $fromdate=$_POST["days_from"];
        }
        if(isset($_POST["days_to"]) and strlen($_POST["days_to"])==10){
            $todate=$_POST["days_to"];
        }

        $date_sql      = ' and created_date between "'.esc_sql($fromdate).'" and "'.esc_sql($todate).'" ';

        
        if (isset($_POST['state']) and in_array($_POST['state'], array(
            'success',
            'fail',
            'corrected'
        ))) {
            $state_sql = "and state='" . esc_sql($_POST['state']) . "'";
        } else {
            $state_sql = '';
        }
        $device_type = !empty($_POST["device_type"]) ? esc_sql($_POST["device_type"]) : '';
        $device_sql  = ' and device_type like "%' . $device_type . '%" ';

        $flt = isset($_POST['flt']) ? $_POST['flt'] : '';

        return array(
            $date_sql,
            $state_sql,
            $device_sql,
            $flt,
            $fromdate,
            $todate
        );
    }

    function getOS() { 
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform  = "Other";
        $os_array     = array(
                              '/windows/i'            =>  'Windows',
                              '/macintosh|mac os x/i' =>  'iOS',
                              '/iphone/i'             =>  'iOS',
                              '/ipad/i'               =>  'iOS',
                              '/android/i'            =>  'Android',
                              '/linux/i'              =>  'Linux',
                        );
                        
        foreach ($os_array as $regex => $value) { 
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
                break;
            }
        }   
        return $os_platform;
    }

    public function guaven_woos_tracker_callback()
    {
        if (!isset($_POST["failed"]) or !isset($_POST["success"]) or !isset($_POST["corrected"]) or !isset($_POST["unid"])) {
            exit;
        }

        global $wpdb;
        $current_timestamp = time();
        $addcontrol        = esc_attr($_POST["addcontrol"]);
        if ($current_timestamp - intval($addcontrol) > 3600) {
            exit;
        }
        do_action('guaven_woos_tracker_insert',$_POST["unid"]);
        check_ajax_referer('guaven_woos_tracker_' . $addcontrol, 'ajnonce');
        $this->guaven_woos_tracker_inserter($_POST["failed"], 'fail', 'frontend', $_POST["unid"]);
        $this->guaven_woos_tracker_inserter($_POST["success"], 'success', 'frontend', $_POST["unid"]);
        $this->guaven_woos_tracker_inserter($_POST["corrected"], 'corrected', 'frontend', $_POST["unid"]);
        exit;
    }


    public function guaven_woos_tracker_inserter($failsuccess, $state, $froback, $unid)
    {
        $insert_or_not=apply_filters('gws_tracker_inserter',true);
        if(!$insert_or_not)return;
        
        $failed_arr                           = explode(", ", $failsuccess);
        $failed_arr_f[count($failed_arr) - 1] = $failed_arr[count($failed_arr) - 1];
        for ($i = count($failed_arr) - 2; $i >= 0; $i--) {
            if (strpos($failed_arr[$i + 1], $failed_arr[$i]) === false) {
                $failed_arr_f[$i] = $failed_arr[$i];
            }
        }

        $failed_arr_f = array_unique($failed_arr_f);
        global $wpdb;
        foreach ($failed_arr_f as $faf) {
            if (!empty($faf)) {
                $wpdb->insert($wpdb->prefix . "woos_search_analytics", array(
                    'keyword' => $faf,
                    'created_date' => date("Y:m:d"),
                    'user_info' => $unid,
                    'state' => $state,
                    'device_type' => (wp_is_mobile() ? 'mobile' : 'desktop').'_'.$this->getOS(),
                    'side' => $froback
                ), array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                ));
            }
        }
    }

    public static function guaven_woos_trend_inserter($pid, $unid, $score = 1)
    {
        $insert_or_not=apply_filters('gws_trend_inserter',true);
        if(!$insert_or_not)return;
        global $wpdb;
        $wpdb->query($wpdb->prepare("
      INSERT INTO " . $wpdb->prefix . "woos_search_trends (post_id, search_count, user_info,point,search_day) VALUES(%d, 1, %s,%d,%s)
      ON DUPLICATE KEY UPDATE search_count=search_count+1", $pid, $unid, $score, date("Y-m-d")));
    }
    
    public function save_settings()
    {
        if (isset($_GET['removekeyword']) and isset($_GET['_wpnonce']) and wp_verify_nonce($_GET['_wpnonce'], 'removekeyword_nonve')) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'woos_search_analytics';
            $wpdb->query($wpdb->prepare("delete from `$table_name` where ID=%d", $_GET['removekeyword']));
        }
        if (isset($_POST['guaven_woos_an_reset_nonce_f']) and wp_verify_nonce($_POST['guaven_woos_an_reset_nonce_f'], 'guaven_woos_an_reset_nonce')) {
            update_option('guaven_woos_sa_table_done', '');
            $this->init();
            add_settings_error('guaven_pnh_settings', esc_attr('settings_updated'), 'Success! All analytics data has been deleted', 'updated');
        } elseif (isset($_POST['guaven_woos_an_enable_nonce_f']) and wp_verify_nonce($_POST['guaven_woos_an_enable_nonce_f'], 'guaven_woos_an_enable_nonce')) {
            add_settings_error('guaven_pnh_settings', esc_attr('settings_updated'), 'Success! Search Analytics has been enabled', 'updated');
            update_option('guaven_woos_data_tracking', '1');
        } elseif (isset($_POST['guaven_woos_an_disable_nonce_f']) and wp_verify_nonce($_POST['guaven_woos_an_disable_nonce_f'], 'guaven_woos_an_disable_nonce')) {
            add_settings_error('guaven_pnh_settings', esc_attr('settings_updated'), 'Success! Search Analytics has been disabled', 'updated');
            update_option('guaven_woos_data_tracking', '');
        }
    }


    public function admin_menu()
    {
        $role_to_use_the_analytics=apply_filters('gws_role_to_use_the_plugin','manage_woocommerce');
        add_submenu_page('woocommerce', 'Guaven Woo Search Analytics', 'Search Analytics', $role_to_use_the_analytics, __FILE__, array(
            $this,
            'run'
        ));
    }

    private function reports_db_construct()
    {
      global $wpdb;
      $table_name      = $wpdb->prefix . 'woos_search_analytics';
      $charset_collate = $wpdb->get_charset_collate();
      global $wpdb;
      $wpdb->query("DROP TABLE IF EXISTS  `$table_name`;");
      $sql = " CREATE TABLE `$table_name` (
`ID` bigint(20) NOT NULL AUTO_INCREMENT,
`keyword` varchar(200) NOT NULL,
`created_date` date NOT NULL,
`device_type` varchar(200) NOT NULL,
`user_info` varchar(200) NOT NULL,
`state` varchar(10) NOT NULL,
`side` varchar(10) NOT NULL,
PRIMARY KEY (`ID`)
) $charset_collate;";
      require_once ABSPATH . 'wp-admin/includes/upgrade.php';
      dbDelta($sql);
    }
}
