<?php
if (!defined('ABSPATH')) {
    die;
}
?>


<style>
.guaven_woos_an_left {
    float: left;
    width: 35%
}

.guaven_woos_an_right {
    float: left;
    width: 65%
}

@media (max-width:1200px) {

    .guaven_woos_an_right,
    .guaven_woos_an_left {
        float: none;
        width: 100%
    }
}
</style>
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>WooCommerce Search Engine Analytics</h2>

    <?php
settings_errors();
?>


    <?php
if (get_option('guaven_woos_data_tracking') != 1) {
    ?>
    <form action="" method="post" name="an_enable_form">
        <?php
    wp_nonce_field('guaven_woos_an_enable_nonce', 'guaven_woos_an_enable_nonce_f'); ?>
        <p>
            <label>
                <input type="submit" value="Enable Search Analytics" /> </label>
        </p>
        <small>Search Analytics Currently Disabled for Your Website. </small>

    </form>
    <?php

} else {
    ?>


    <div>
        <br>
        <form action="" method="post">

        <label>From  <input type="date" name="days_from" style="width:150px" value="<?php
    echo $chartdata[4]; ?>" /></label>

<label>To <input type="date" name="days_to" style="width:150px" value="<?php
    echo $chartdata[5]; ?>" /></label>

            <select name="device_type">
                <option value="">Device type</option>
                <option value="">All</option>
                <option value="desktop" <?php echo isset($_POST["device_type"])?selected($_POST["device_type"],'desktop'):''; ?>>Desktop</option>
                <option value="mobile" <?php echo isset($_POST["device_type"])?selected($_POST["device_type"],'mobile'):''; ?>>Mobile</option>
            </select>
            <select name="state">
                <option value="all">All keywords</option>
                <option value="success" <?php echo isset($_POST["state"])?selected($_POST["state"],'success'):''; ?>>Successfull searches</option>
                <option value="corrected" <?php echo isset($_POST["state"])?selected($_POST["state"],'corrected'):''; ?>>Corrected keywords</option>
                <option value="fail" <?php echo isset($_POST["state"])?selected($_POST["state"],'fail'):''; ?>>"Notfound" keywords</option>
            </select>

            <select name="flt">
                <option value="recent" <?php echo isset($_POST["flt"])?selected($_POST["flt"],'recent'):''; ?>>Recently searched keywords</option>
                <option value="popular" <?php echo isset($_POST["flt"])?selected($_POST["flt"],'popular'):''; ?>>Popular keywords(unfiltered)</option>
                <option value="popular_uniq" <?php echo isset($_POST["flt"])?selected($_POST["flt"],'popular_uniq'):''; ?>>Popular keywords(by uniq users)</option>
            </select>
            
            <input name="lmt" type="number" style="width:100px" placeholder="Limit">

            <input type="submit" class="button" value="Show">
        </form>
    </div>

    <div class="guaven_woos_an_left">

    <br><br>

        <table style="width:100%">
            <tr>
                <th style="width:20px"></th>
                <th>Search String (<?php echo count($tabledata);?>)</th>
                <th>Count or Date</th>
                <th>State</th>
                <th style="width:80px">Device</th>
                <th style="width:20px;padding-right:40px"></th>
            </tr>
            <?php
    $sufaco = array();
    $i      = 0;
    foreach ($tabledata as $tdata) {
        $i++;
        echo '<tr><td>' . $i . '</td><td>' . esc_html(stripslashes($tdata->keyword)) . '</td><td>' . esc_html($tdata->date_or_count) . '</td><td>' . esc_html($tdata->state) . '</td>
    <td>' . esc_html($tdata->device_type) . '</td>
    <td class="gws_an_removetd"> <a style="color:#c4c4c4;text-decoration:none" href="' . wp_nonce_url('?page=woo-search-box%2Fadmin%2Fclass-search-analytics.php&removekeyword=' . $tdata->ID, 'removekeyword_nonve') . '" title="Remove this keyword">x</a></td>  </tr>';
        if (!isset($sufaco[$tdata->state])) {
            $sufaco[$tdata->state] = 0;
        }
        $sufaco[$tdata->state]++;
    } ?>
        </table>

        <br><br><button id="exportcsv" class="button button-default">Export CSV</button>

        <script>
            const table_data = <?php echo json_encode($tabledata);?>;

            document.getElementById("exportcsv").addEventListener("click", function () {
                const csv = jsonToCsv(table_data, ["user_info"]); // Exclude user_info column
                const blob = new Blob([csv], { type: "text/csv" });
                const url = URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = "data.csv";
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });

            function jsonToCsv(json, excludeColumns = []) {
                const headers = Object.keys(json[0]).filter(col => !excludeColumns.includes(col));
                const csvHeaders = headers.join(",") + "\n";
                const rows = json.map(row => headers.map(col => row[col]).join(",")).join("\n");
                return csvHeaders + rows;
            }
        </script>

    </div>

    <div class="guaven_woos_an_right">
        <?php
  if (!empty($chartdata[1])) {
  ?>
        <br>

        <canvas id="myLinechart" style="width:100%;max-width:1200px"></canvas>
        
        <small>
            <span style="color:white;padding:5px;background:#43A047">successfull</span>
            <span style="color:white;padding:5px;background:#E53935">notfound</span>
            <span style="color:white;padding:5px;background:#1E88E5">corrected</span>
        </small>

        <hr /><br><br>

        <canvas id="myBar" style="width:100%;max-width:600px"></canvas>

        <hr /><br><br>
       
        <canvas id="myPie" style="width:100%;max-width:600px"></canvas>

        </div>

        <script>
        <?php
        $i = 0;
        if (count($chartdata[0]) > 20) {
            foreach ($chartdata[0] as $key => $value) {
                $i++;
                if ($i != 1 and $i != count($chartdata[0]) and $i != round(0.5 * count($chartdata[0]))) {
                    $chartdata[0][$key] = "''";
                }
            }
        } 
        ?>
        const xValues = [<?php echo implode(",", $chartdata[0]); ?>];

        new Chart("myLinechart", {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    data: [<?php echo implode(",", $chartdata[1]['fail']); ?>],
                    borderColor: "#43A047",
                    fill: false
                }, {
                    data: [<?php echo implode(",", $chartdata[1]['success']); ?>],
                    borderColor: "#E53935",
                    fill: false
                }, {
                    data: [<?php echo implode(",", $chartdata[1]['corrected']); ?>],
                    borderColor: "#1E88E5",
                    fill: false
                }]
            },
            options: {
                title: {
                  display: true,
                  text: "Search Volume for the Selected Filters"
                },  
                legend: {
                    display: false
                }
            }
        });


        new Chart("myPie", {
          type: "pie",
          data: {
            labels: [<?php echo implode(",",array_keys($osdata[0])); ?> ],
            datasets: [{
              backgroundColor: [<?php echo implode(",",$osdata[1]); ?>],
              data: [<?php echo implode(",",($osdata[0])); ?>]
            }]
          },
          options: {
            title: {
              display: true,
              text: "OS Statistics for the Selected Filters"
            }
          }
        });
        var barColors = ["#43A047","#1E88E5","#E53935"];

        new Chart("myBar", {
          type: "bar",
          data: {
            labels: ['Successfull', 'Corrected', 'Notfound'],
            datasets: [{
              backgroundColor: barColors,
              data: [<?php  echo "'" . (!empty($sufaco['success']) ? $sufaco['success'] : 0) . "','" . (!empty($sufaco['corrected']) ? $sufaco['corrected'] : 0) . "','" . (!empty($sufaco['fail']) ? $sufaco['fail'] : 0) . "'"; ?>]
            }]
          },
          options: {
            legend: {display: false},
            scales: {
                yAxes: [{
                ticks: {
                    beginAtZero: true
                }
                }],
                xAxes: [{
                ticks: {
                    beginAtZero: true
                }
                }]
            },
            title: {
              display: true,
              text: "Summary of Results for the Selected Filters"
            }
          },
        });

        </script>
        <?php

    } else {
        echo '<br><h4>Insufficient data has been collected for the charts at this time. Please check back later...</h4>';
    } ?>
    </div>

    <div class="clear clearfix"></div>
    <hr />
    <div style="margin-top:100px">


        <form action="" method="post" name="an_disable_form" style="float:left;padding-right:10px">
            <?php
    wp_nonce_field('guaven_woos_an_disable_nonce', 'guaven_woos_an_disable_nonce_f'); ?>

            <input type="submit" value="Disable Search Analytics" class="button button-default" />
        </form>
        <form action="" method="post" name="an_reset_form">
            <?php
    wp_nonce_field('guaven_woos_an_reset_nonce', 'guaven_woos_an_reset_nonce_f'); ?>
            <input type="submit" onclick="return confirm('Are you sure to reset all search analytics data?')"
                class="button button-default" value="Delete all analytics data">
        </form>

    </div>
    <?php

}
?>
