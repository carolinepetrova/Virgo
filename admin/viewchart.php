<?php
$title = "Преглед на диаграма";
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res= mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
    if($userinfo['is_admin'] == 'yes') {
    if($userinfo['parent_id'] == "0") {
            $per_id = $_SESSION['user'];
    }
    else {
            $per_id = $userinfo['parent_id'];
        }
    }
    else 
        {
            $per_id = $userinfo['parent_id'];
        }
include("sections/header.php");
$getid = $_GET['id'];
$fetch_chart= mysqli_query($db_connect, "SELECT * FROM all_charts WHERE id='$getid'");
$chart=mysqli_fetch_assoc($fetch_chart);
$get_chart_csv = $chart['csv'];
$fetch_csv= mysqli_query($db_connect, "SELECT * FROM reports WHERE id='$get_chart_csv'");
$csv_file=mysqli_fetch_assoc($fetch_csv);

?>
    <div id="content2" class="content">
        <?php if ($userinfo['is_admin'] == 'yes') {?>
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Преглед на диаграма
            <br>
            <span class="page-head-nav">Начало > Диаграми > Преглед на диаграма</span>
        </div>
        <div class="row" style="margin:15px;">
            <div class="widget widget-white col-md-12">
                <div id="chartDiv"></div>
                <script>
                    var csv_name = "<?php echo $csv_file['name'];?>";
                    var csv_file = "<?php echo $csv_file['file'];?>";
                    var type_chart = "<?php echo $chart['type_chart'];?>";
                    var chartData = {
                        type: type_chart, // Specify your chart type here.
                        title: {
                            text: csv_name // Adds a title to your chart
                        },
                        legend: {}, // Creates an interactive legend
                        "csv": {
                            "url": "../admin/reports/"+csv_file
                        }
                    };
                    zingchart.render({ // Render Method[3]
                        id: "chartDiv",
                        data: chartData,
                        height: 400,
                        width: '100%'
                    });

                </script>
            </div>
        </div>
    </div>
    <?php } else { include("sections/error.php"); } ?>
    <?php
include("sections/footer.php");
?>
