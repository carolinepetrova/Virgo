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
            Редактиране на
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
                        <div class="panel panel-default">
                            <table class="table table-csv" id="csvtable">
                                <thead>
                                    <tr>
                                        <?php
    // Show header
    for ($columnCnt=0; $columnCnt<$columns; $columnCnt++) {
        echo "<th>" . $row[$columnCnt] . "</th>";
    }
    echo "<th>&nbsp;</th>";
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
                        <div class="text-right">
                            <a href="#" id="addrow" class="btn btn-default"><i class="fa fa-plus"></i> Нов ред</a>
                        </div>
                        <hr>
                        <div>
                            <a href="#" id="cancel" class="btn btn-default"><i class="fa fa-undo"></i> Отказ</a>
                            <a href="#" id="save" style="float:none;" class="btn btn-green"><i class="fa fa-save"></i> Запази</a>
                        </div>
                    </form>
                    <div style="margin-top: 20px;" id="message">
                    </div>

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
            var csvfile = "<?php echo $csv;?>";

            // Enable/disable row 
            $(document).on("click", "a[rel=editrow]", function(e) {
                //    $("a[rel=editrow]").click(function(e) { 
                e.preventDefault();
                // get id clicked a and extract the linenumber
                var linenum = this.id.split("-")[1];

                // change button icon and row background color according to state
                var rowIsEnabled;
                if ($(this).children().attr("class") === "fa fa-unlock-alt") {
                    rowIsEnabled = true;
                    $(this).children().attr("class", "fa fa-lock");
                } else {
                    rowIsEnabled = false;
                    $(this).children().attr("class", "fa fa-unlock-alt");
                }
                $("#row-" + linenum).toggleClass("success");

                // Toggle (disable/enable) every input field in row
                $("input[rel=input-" + linenum + "]").each(function(i) {
                    $(this).prop("disabled", rowIsEnabled);
                });
            });

            // Delete row
            $(document).on("click", "a[rel=deleterow]", function(e) {
                //    $("a[rel=deleterow]").click(function(e) { 
                e.preventDefault();
                // get id clicked a and extract the linenumber
                var linenum = this.id.split("-")[1];
                // change background color of row to indicate that row is unlocked/locked
                $("#row-" + linenum).hide();
            });

            // Add row
            $("#addrow").click(function(e) {
                e.preventDefault();
                // get linenumber of last row
                var linenum = parseInt($("#csvtable tbody tr:last").attr("id").split("-")[1]);
                $("#csvtable tbody").append(makeTableRow(linenum + 1, <?php echo $columns;?>, true));
            });

            // Cancel (reload page)
            $("#cancel").click(function(e) {
                e.preventDefault();
                location.reload(true);
            });

            // Save
            $("#save").click(function(e) {
                e.preventDefault();

                var csvlines = {};

                var columncnt = 0;
                var linecnt = 0;
                // Loop through all (visible only) table rows and make data
                $("[rel=row]:visible").each(function() {
                    var linenum = this.id.split("-")[1];
                    var thisline = {};
                    columncnt = 0;
                    $("input[rel=input-" + linenum + "]").each(function() {
                        thisline['col-' + columncnt] = $(this).val();
                        columncnt++;
                    });
                    csvlines['line-' + linecnt] = thisline;
                    linecnt++;
                });
                var csvdata = {
                    csvfile: csvfile,
                    lines: linecnt,
                    columns: columncnt,
                    data: csvlines
                };
                //alert(JSON.stringify(csvdata));

                // Write data to file and show result to user
                $.ajax({
                    url: "savetocsv.php",
                    method: "POST",
                    contentType: 'application/json; charset=utf-8',
                    dataType: 'json',
                    async: false,
                    data: JSON.stringify(csvdata),
                    cache: false,
                    success: function(response) {
                        makeMessage("<h4>" + response.responseText + "</h4>Редактирано успешно!", "success", "message");
                        // reload page in 3 sec
                        setTimeout(function() {
                            location.reload();
                        }, 2500);

                    },
                    error: function(response) {
                        makeMessage("<h4>Известие</h4>" + response.status + " " + response.statusText, "success", "message");
                    }
                });
            });


            function makeMessage(messagetext, messagetype, messageid) {
                var h = "<div class=\"alert alert-" + messagetype + " alert-dismissible\" role=\"alert\">" +
                    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                    messagetext + "</div>";
                $("#" + messageid).html(h);
                return;
            }

            function makeTableRow(linenum, columns, isenabled) {
                var h = "<tr rel=\"row\" id=\"row-" + linenum + "\" class=\"" + (isenabled === true ? "success" : "") + "\">";
                for (var columncnt = 0; columncnt < columns; columncnt++) {
                    h += "<td><input class=\"form-control" + (columncnt == 0 ? " input-col-first" : " input-col-rest") + "\" rel=\"input-" + linenum + "\"" + (isenabled === true ? "" : " disabled") + " type=\"text\" value=\"\"></td>";
                }
                h += "<td>";
                h += " <a href=\"#\" rel=\"editrow\" id=\"editrow-" + linenum + "\" title=\"Edit row\" class=\"btn csv-btn-lock btn-sm\"><i class=\"fa " + (isenabled === true ? "fa-unlock-alt" : "fa-lock") + "\"></i></a>";
                h += " <a href=\"#\" rel=\"deleterow\" id=\"deleterow-" + linenum + "\" title=\"Delete row\" class=\"btn csv-btn-del btn-sm\"><i class=\"fa fa-trash\"></i></a>";
                h += "</td>";
                h += "</tr>";
                return h;
            }

        </script>
        <?php
function makeTableRow($lineCnt, $row, $columns) {
    $h = "<tr rel=\"row\" id=\"row-" . $lineCnt . "\">";
    for ($columnCnt=0; $columnCnt<$columns; $columnCnt++) {
        $h .= "<td><input class=\"form-control" . ($columnCnt==0 ? " input-col-first" : " input-col-rest") . "\" rel=\"input-" . $lineCnt . "\" disabled type=\"text\" value=\"" . $row[$columnCnt] . "\"></td>";
    }
    $h .= "<td>";
    $h .= " <a href=\"#\" rel=\"editrow\" id=\"editrow-" . $lineCnt . "\" title=\"Edit row\" class=\"btn csv-btn-lock btn-sm\"><i class=\"fa fa-lock\"></i></a>";
    $h .= " <a href=\"#\" rel=\"deleterow\" id=\"deleterow-" . $lineCnt . "\" title=\"Delete row\" class=\"btn csv-btn-del btn-sm\"><i class=\"fa fa-trash\"></i></a>";
    $h .= "</td>";
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
