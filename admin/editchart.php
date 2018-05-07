<?php
$title = "Редактиране на диаграма";
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res= mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
include("sections/header.php");
if($userinfo['is_admin'] == 'yes') {
    if($userinfo['parent_id'] == "0") {
            $per_id = $_SESSION['user'];
            $needed_id = $userinfo['id'];
    }
    else {
            $per_id = $userinfo['parent_id'];
            $needed_id = $userinfo['parent_id'];
        }
    }
    else 
        {
            $per_id = $userinfo['parent_id'];
            $needed_id = $userinfo['parent_id'];
        }
// secure other profiles not connected to the admin
$link =  "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$getname = basename($link);
$ids_array = array();
$needed_id = $userinfo['id'];
$result = mysqli_query($db_connect,"SELECT id FROM reports WHERE parent_id='$needed_id'");
while($row = mysqli_fetch_array($result))
{
    $ids_array[] = $row['id'];
}
$show_modal = false;
$getid = $_GET['id'];
$fetch_chart= mysqli_query($db_connect, "SELECT * FROM all_charts WHERE id='$getid'");
$chart=mysqli_fetch_assoc($fetch_chart);
$get_chart_csv = $chart['csv'];
$fetch_csv= mysqli_query($db_connect, "SELECT * FROM reports WHERE id='$get_chart_csv'");
$csv_file=mysqli_fetch_assoc($fetch_csv);
$charttype = $chart['type_chart'];
if (isset($_GET['ty_chart'])) {
    
    $charttype = $_GET['ty_chart'];
}
if(isset($_POST['update_name'])) {
            $name = mysqli_real_escape_string($db_connect, $_POST['chart_name']); 
             $upd = mysqli_query($db_connect, "UPDATE `all_charts` SET `name`='$name' WHERE `id`='$getid'");
            if (!$upd)  {
                die("SQL Error: ".mysqli_error($db_connect));
                $error = "Възникна грешка";
            }
    else {
            $success = "updated";
    }
}
if(isset($_POST['update_type'])) {
             $ins = mysqli_query($db_connect, "UPDATE `all_charts` SET `type_chart`='$charttype' WHERE `id`='$getid'");
            if (!$ins)  {
                die("SQL Error: ".mysqli_error($db_connect));
                $error = "Възникна грешка";
            }
    else {
            $success2 = "updated";
    }
}

?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Редактиране на диаграма <?php echo $name;?>
            <br>
            <span class="page-head-nav">Начало > Диаграми > Редактиране на диаграма</span>
        </div>
         <?php if($success == "updated") {?>
        <script>
            alertify.success('Успешно сменихте името на диаграмата');

        </script>
        <?php } elseif($success2 == "updated") {?>
        <script>
            alertify.success('Успешно сменихте графиката на диаграмата');

        </script>
        <?php } if($error != NULL) {?>
        <script>
            alertify.error('<?php echo $error;?>');

        </script>
        <?php } ?>
        <div class="row" style="margin:15px;">
            <div class="widget widget-white col-md-12">
                <form method="post">
                    <div class="col-sm-10">
                        <label>Име</label>
                        <input name="chart_name" class="form-control" value="<?php echo $chart['name'];?>">
                    </div>

                    <div class="col-sm-2">
                        <button type="submit" style="margin-top: 35px;" name="update_name" class="btn btn-block btn-login btn-green ">Обнови</button>
                    </div>
                </form>
                <form method="get">
                    <div class="col-sm-8">
                        <label>Вид диаграма</label>
                        <select class="form-control" name="ty_chart">
                                    <?php
                                    $get_charts = mysqli_query($db_connect, "SELECT * FROM type_charts");
                                    while($charts = mysqli_fetch_assoc($get_charts)) {
                                    ?>
                                    <option value="<?php echo $charts['js_filename'];?>"><?php echo $charts['name'];?></option>
                                    
                                    <?php }?>
                                </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" style="margin-top: 35px;" name="id" value="<?php echo $chart['id'];?>" class="btn btn-block btn-login btn-blue ">Преглед</button>
                    </div>
                </form>
                <form method="post">
                    <div class="col-sm-2">
                        <button type="submit" style="margin-top: 35px;" name="update_type" class="btn btn-block btn-login btn-green ">Обнови</button>
                    </div>
                </form>
                <div class="col-md-12">
                    <label>Преглед</label>
                    <div id="chartDiv"></div>
                </div>
                <script>
                    var csv_name = "<?php echo $csv_file['name'];?>";
                    var csv_file = "<?php echo $csv_file['file'];?>";
                    var t_chart = "<?php echo $charttype; ?>";
                    var chartData = {
                        type: t_chart, // Specify your chart type here.
                        title: {
                            text: csv_name // Adds a title to your chart
                        },
                        legend: {}, // Creates an interactive legend
                        "csv": {
                            "url": "../admin/reports/" + csv_file
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



    <?php
include("sections/footer.php");
?>
