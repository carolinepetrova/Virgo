<link rel="stylesheet" type="text/css" href="css/calendar.css" />
<?php
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res=mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
$title = "Табло";
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

if(isset($_POST['submit'])) {
$to      = strip_tags(htmlspecialchars($_POST['email']));
$subject = strip_tags(htmlspecialchars($_POST['about']));
$message = strip_tags(htmlspecialchars($_POST['text']));
$from = $userinfo['email'];
$headers = 'From:'.$from. "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$ifsent = mail($to, $subject, $message, $headers);
    if($ifsent == true) {
         $success = "updated";
    }

}
?>


    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Табло
            <br>
            <span class="page-head-nav">Начало > Табло</span>
        </div>
        <?php if($success == "updated") {?>
        <script>
            alertify.success('Успешно изпратихте имейла');

        </script>
        <?php } if($error != NULL) {?>
        <script>
            alertify.error('Възникна грешка');

        </script>
        <?php } ?>
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-white">
                    <span class="pull-left">
                <i class="fa fa-user pink" aria-hidden="true"></i>
            </span>
                    <span class="pull-right">
                <span class="widget-title pink counter"><?php  echo mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `users` WHERE `parent_id`='$per_id'")); ?></span><br>
                    <span class="widget-content">Всички работници</span>
                    </span>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-white">
                    <span class="pull-left">
                <i class="fa fa-wrench green" aria-hidden="true"></i>
            </span>
                    <span class="pull-right">
                <span class="widget-title pink counter"><?php  echo mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `reports` WHERE `parent_id`='$per_id'")); ?></span><br>
                    <span class="widget-content">Всички справки</span>
                    </span>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-white">
                    <span class="pull-left">
                <i class="fa fa-th-list" aria-hidden="true"></i>
            </span>
                    <span class="pull-right">
                        
                            <span class="widget-title pink counter"><?php  echo mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `tasks` WHERE `for_user`='$person_id' AND active='yes'")); ?></span><br>
                    <span class="widget-content">Моите задачи</span>
                    </span>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-white">
                    <span class="pull-left">
                <i class="fa fa-pie-chart" aria-hidden="true"></i>
            </span>
                    <span class="pull-right">
                        
                            <span class="widget-title pink counter"><?php  echo mysqli_num_rows(mysqli_query($db_connect, "SELECT * FROM `all_charts` WHERE `parent_id`='$per_id'")); ?></span><br>
                    <span class="widget-content">Моите диаграми</span>
                    </span>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="email">
                    <h3>Бърз имейл</h3>

                    <div class="row" style="margin:15px;">
                        <form method="post">
                            <div class="col-xs-12">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Имейл">
                            </div>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" name="about" placeholder="Относно">
                            </div>
                            <div class="col-xs-12">
                                <textarea rows="4" class="form-control" name="text" placeholder="Текст"></textarea>
                            </div>
                            <button type="submit" name="submit" class="btn btn-green">Изпрати</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="wraper container-fluid col-md-4">
                <div class="custom-calendar-wrap">
                    <div id="custom-inner" class="custom-inner">
                        <div class="custom-header clearfix">
                            <nav>
                                <span id="custom-prev" class="custom-prev"></span>
                                <span id="custom-next" class="custom-next"></span>
                            </nav>
                            <h2 id="custom-month" class="custom-month"></h2>
                            <h3 id="custom-year" class="custom-year"></h3>
                        </div>
                        <div id="calendar" class="fc-calendar-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
include("sections/footer.php");
?>
<!--- this has to be here to not mess up the other calendar -->
        <script>
            $(function() {

                var transEndEventNames = {
                        'WebkitTransition': 'webkitTransitionEnd',
                        'MozTransition': 'transitionend',
                        'OTransition': 'oTransitionEnd',
                        'msTransition': 'MSTransitionEnd',
                        'transition': 'transitionend'
                    },
                    transEndEventName = transEndEventNames[Modernizr.prefixed('transition')],
                    $wrapper = $('#custom-inner'),
                    $calendar = $('#calendar'),
                    cal = $calendar.calendario({
                        onDayClick: function($el, $contentEl, dateProperties) {

                            if ($contentEl.length > 0) {
                                showEvents($contentEl, dateProperties);
                            }

                        },
                        caldata: codropsEvents,
                        displayWeekAbbr: true
                    }),
                    $month = $('#custom-month').html(cal.getMonthName()),
                    $year = $('#custom-year').html(cal.getYear());

                $('#custom-next').on('click', function() {
                    cal.gotoNextMonth(updateMonthYear);
                });
                $('#custom-prev').on('click', function() {
                    cal.gotoPreviousMonth(updateMonthYear);
                });

                function updateMonthYear() {
                    $month.html(cal.getMonthName());
                    $year.html(cal.getYear());
                }

                // just an example..
                function showEvents($contentEl, dateProperties) {

                    hideEvents();

                    var $events = $('<div id="custom-content-reveal" class="custom-content-reveal"><h4>Events for ' + dateProperties.monthname + ' ' + dateProperties.day + ', ' + dateProperties.year + '</h4></div>'),
                        $close = $('<span class="custom-content-close"></span>').on('click', hideEvents);

                    $events.append($contentEl.html(), $close).insertAfter($wrapper);

                    setTimeout(function() {
                        $events.css('top', '0%');
                    }, 25);

                }

                function hideEvents() {

                    var $events = $('#custom-content-reveal');
                    if ($events.length > 0) {

                        $events.css('top', '100%');
                        Modernizr.csstransitions ? $events.on(transEndEventName, function() {
                            $(this).remove();
                        }) : $events.remove();

                    }

                }

            });

        </script>
