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
// secure other charts not connected to the admin
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
		$delete = mysqli_query($db_connect, "DELETE FROM `all_charts` WHERE `id`='$del'");
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
            Всички диаграми
            <br>
            <span class="page-head-nav">Начало > Диаграми > Всички диаграми</span>
        </div>
        <?php if($success == "updated") {?>
        <script>
            alertify.success('Успешно добавихте диаграма"');

        </script>
        <?php } if($error != NULL) {?>
        <script>
            alertify.error('<?php echo $error2;?>');

        </script>
        <?php } ?>
        <div class="row" style="margin:15px;">
            <div class="widget widget-white col-md-12">
                    <div class="table-responsive">
                        <table id="example" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Име на диаграма</th>
                                    <th>Добавил</th>
                                    <th>Дата</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $get_charts = mysqli_query($db_connect, "SELECT * FROM all_charts WHERE parent_id='$per_id'");
			                 while($charts = mysqli_fetch_assoc($get_charts)) {
			             ?>
                                    <tr>
                                        <td>
                                            <?php echo $charts['name']?>
                                        </td>
                                        <td>
                                            <?php echo $charts['added_by']?>
                                        </td>
                                        <td>
                                            <?php echo $charts['date_added']?>
                                        </td>
                                        <td>
                                            <a href="viewchart?id=<?php echo $charts['id'];?>" class="btn-view" title="Преглед">
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                            </a>
                                            <?php if($userinfo['is_admin'] == 'yes') { ?>
                                            <a href="editchart?id=<?php echo $charts['id'];?>" class="btn-edit" title="Редактирай"><i class="fa fa-pencil"></i>
                                        </a>
                                            
                                            <form class="reports-forms" method="get">
                                                <button type="submit" title="Изтрии" id="delete" class="reports btn-delete" value="<?php echo $charts['id'];?>" name="id"><i class="fa fa-trash-o"></i></button>
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
            <?php $delete_info = mysqli_query($db_connect, "SELECT * FROM all_charts WHERE id='$del'");
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
