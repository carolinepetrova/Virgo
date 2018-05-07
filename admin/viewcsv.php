<?php
$title = "Редактиране на справка";
include("inc/db_connect.php");
include("inc/functions.php");

$person_id = $_SESSION['user'];
$res= mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
include("sections/header.php");
include("inc/class.CSVHandler.php");
$getid = $_GET['id'];
$getinfo= mysqli_query($db_connect, "SELECT * FROM reports WHERE id='$getid'");
$inf=mysqli_fetch_assoc($getinfo);
$csvFile = 'reports/'.$inf['file'];
require_once 'config.php';

// Override default csv file if a csv file is provided

$csv = $inf['file'];

// Open CSV file
$filename = SDFE_CSVFolder . "/" . $csv;
$fp = fopen($filename, "r");
$content = fread($fp, filesize($filename));
$lines = explode("\n", $content);
fclose($fp);
?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Преглед на
            <?php echo  $inf['name'];?>
            <br>
            <span class="page-head-nav">Начало > Справки </span>
        </div>
        <div class="row" style="margin:15px;">
            <div class="col-lg-12">

                <?php
if(!isset($csv)) {
    
}
else {
    // CSV file is not empty, let's show the content
    $row = explode(SDFE_CSVSeparator, $lines[0]);
    $columns = sizeof($row);
?>
                    <form class="form-inline" method="post">
                        <div class="panel panel-default table-responsive">
                            <table class="table table-striped" id="viewcsvtable">
                                <thead>
                                    <tr>
                                        <?php
    // Show header
    for ($columnCnt=0; $columnCnt<$columns; $columnCnt++) {
        echo "<th>" . $row[$columnCnt] . "</th>";
    }
?>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php
    // Show content
    for ($lineCnt=1; $lineCnt<sizeof($lines); $lineCnt++) {
        $row = explode(SDFE_CSVSeparator, $lines[$lineCnt]);
        echo makeTableRow($lineCnt, $row, $columns);
    }
?>
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <?php
}
?>
            </div>

        </div>
    </div>
 
    <?php
include("sections/footer.php");
?>
<script>
$(document).ready( function () {
    $('#viewcsvtable').DataTable();
} );
</script>
        <?php
function makeTableRow($lineCnt, $row, $columns) {
    $h = "<tr rel=\"row\" id=\"row-" . $lineCnt . "\">";
    for ($columnCnt=0; $columnCnt<$columns; $columnCnt++) {
        $h .= "<td> $row[$columnCnt]</td>";
    }
    $h .= "</tr>";
    
    return $h;
}
function makeCSVFileLink($basename, $activebasename) {
    // Include CSV files only (defined by extension)
    if(substr($basename, -3)==SDFE_CSVFileExtension) {
        $h = "<a href=\"?file=" . $basename . "\" ";
        $h .= "class=\"list-group-item" . ($basename==$activebasename ? " active" : "") . "\">";
        $h .= $basename . "</a>";
    }
    return $h;
}

?>
