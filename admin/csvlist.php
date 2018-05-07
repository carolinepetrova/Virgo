<?php
$title = "Всички работници";
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
if (isset($_GET['id'])) {
    $del = $_GET['id'];
}
if(isset($_POST['delete'])) {
        $del_file= mysqli_query($db_connect, "SELECT * FROM reports WHERE id='$del'");
        $get_file=mysqli_fetch_assoc($del_file);
        unlink('reports/'.$get_file['file']);
		$delete = mysqli_query($db_connect, "DELETE FROM `reports` WHERE `id`='$del'");
        if (!$delete)  {
                die("SQL Error: ".mysqli_error($db_connect));
            }
        else {
            $success = "updated";
        }
		hideGET();
}
?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Всички справки
            <br>
            <span class="page-head-nav">Начало > Справки > Добави справки</span>
        </div>
        <?php if($success == "updated") {?>
        <script>
            alertify.success('Успешно добавихте справка"');

        </script>
        <?php } if($error != NULL) {?>
        <script>
            alertify.error('<?php echo $error2;?>');

        </script>
        <?php } ?>
        <div class="row" style="margin:15px;">
            <div class="widget widget-white col-md-12">
                <?php
                        if($success == "updated") {
                            echo '<div class="alert alert-success" role="alert">Успешно добавен</div>';
                        }
                        if($error != NULL) {
			                 echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                        }
                    ?>
                    <div class="table-responsive">
                        <table id="example" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Име</th>
                                    <th>Качено от</th>
                                    <th>Дата</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                            if($userinfo['is_admin']=='yes') {
                                $get_reports = mysqli_query($db_connect, "SELECT * FROM reports WHERE parent_id='$per_id'");
                            }
                            else {
                                $getid = $userinfo['parent_id'];
                                $get_reports = mysqli_query($db_connect, "SELECT * FROM reports WHERE parent_id='$getid'");
                            }
			                 while($reports = mysqli_fetch_assoc($get_reports)) {
			             ?>
                                    <tr>
                                        <td>
                                            <?php echo $reports['name']?>
                                        </td>
                                        <td>
                                            <?php echo $reports['uploaded_by']?>
                                        </td>
                                        <td>
                                            <?php echo $reports['date_added']?>
                                        </td>
                                        <td>
                                            <a href="viewreport?id=<?php echo $reports['id'];?>" class="btn-view" title="Преглед">
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                            </a>
                                            <a href="editreport?id=<?php echo $reports['id'];?>" class="btn-edit" title="Редактирай"><i class="fa fa-pencil"></i>
                                        </a>
                                            <?php if($userinfo['is_admin'] == 'yes') { ?>
                                            <form class="reports-forms" method="get">
                                                <button type="submit" title="Изтрии" id="delete" class="reports btn-delete" value="<?php echo $reports['id'];?>" name="id"><i class="fa fa-trash-o"></i></button>
                                            </form>
                                            <?php }?>
                                        </td>
                                    </tr>
                                    <?php }?>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
    <?php if($userinfo['is_admin'] == 'yes') { 
    if($_GET['id']) {?>
    <script type="text/javascript">
        window.onload = function() {
            $('#del').modal('show');
        }

    </script>
    <?php }?>
    <div class="modal fade" id="del" tabindex="-2" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <?php $delete_info = mysqli_query($db_connect, "SELECT * FROM reports WHERE id='$del'");
			while($del_inf = mysqli_fetch_assoc($delete_info)) {?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        Изтриване на
                        <?php echo $del_inf['name'];?>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <span class="text-center">
                            Наистина ли искате да изтриете
                            <?php echo $del_inf['name'];?>
                            <form method="POST" action="" enctype="multipart/form-data">

                                <button type="button" style="float: none" name="exit" id="exit" data-dismiss="modal" class="btn btn-login btn-red">Не</button> &nbsp;
                                <button type="submit" style="float: none" id="delete" name="delete" class="btn btn-login btn-green">Да</button>

                            </form>
                        </span>
                    </div>

                </div>
                <?php }?>
            </div>
        </div>
    </div>
    <?php }?>


    <?php
include("sections/footer.php");
?>
