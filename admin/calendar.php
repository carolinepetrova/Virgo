<?php
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res= mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
if($userinfo['is_admin'] == 'yes') {
    if($userinfo['parent_id'] == "0") {
            $par_id = $_SESSION['user'];
    }
    else {
            $par_id = $userinfo['parent_id'];
        }
    }
    else 
        {
            $par_id = $userinfo['parent_id'];
        }
$title = "Редактирай графици";
include("sections/header.php");
?>

    <div id="content2" class="content">
        <?php if ($userinfo['is_admin'] == 'yes') {?>
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Редактирай графици
            <br>
            <span class="page-head-nav">Начало > Редактирай графици</span>
        </div>
        <div id='wrap'>

            <div id='external-events' class="col-md-2 col-sm-12 col-xs-12">
                <h4>Работници</h4>
                <div class='fc-event' id="<?php echo $userinfo['id']; ?>">
                    <?php echo $userinfo['name']; ?>
                </div>
                <?php
            $get_users = mysqli_query($db_connect, "SELECT * FROM users WHERE parent_id='$par_id'");
			while($users = mysqli_fetch_assoc($get_users)) {
			?>
                    <div class='fc-event' id="<?php echo $users['id']; ?>">
                        <?php echo $users['name']; ?>
                    </div>
                    <?php }?>
                    <p>
                        <div style='clear:both'></div>
                    </p>
            </div>

            <div id='calendar' class="col-md-10 col-sm-12 col-xs-12"></div>

            <div style='clear:both'></div>


        </div>
        <?php } else { include("sections/error.php");}?>
    </div>

    <?php
include("sections/footer.php");
?>
        <script>
            $(document).ready(function() {

                var zone = "05:30"; //Change this to your timezone

                $.ajax({
                    url: 'process.php',
                    type: 'POST', // Send post data
                    data: 'type=fetch',
                    async: false,
                    success: function(s) {
                        json_events = s;
                    }
                });


                var currentMousePos = {
                    x: -1,
                    y: -1
                };
                jQuery(document).on("mousemove", function(event) {
                    currentMousePos.x = event.pageX;
                    currentMousePos.y = event.pageY;
                });

                /* initialize the external events
                -----------------------------------------------------------------*/

                $('#external-events .fc-event').each(function() {

                    // store data so the calendar knows to render an event upon drop
                    $(this).data('event', {
                        childid: $.trim($(this).attr('id')),
                        title: $.trim($(this).text()), // use the element's text as the event title
                        stick: true // maintain when user navigates (see docs on the renderEvent method)
                    });

                    // make the event draggable using jQuery UI
                    $(this).draggable({
                        zIndex: 999,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0 //  original position after the drag
                    });

                });


                /* initialize the calendar
                -----------------------------------------------------------------*/

                $('#calendar').fullCalendar({
                    events: JSON.parse(json_events),
                    //events: [{"id":"14","title":"New Event","start":"2015-01-24T16:00:00+04:00","allDay":false}],
                    utc: true,
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    editable: true,
                    droppable: true,
                    slotDuration: '00:30:00',
                    eventReceive: function(event) {
                        var title = event.title;
                        var start = event.start.format("YYYY-MM-DD[T]HH:mm:SS");
                        var child_id = event.childid;
                        $.ajax({
                            url: 'process.php',
                            data: 'type=new&title=' + title + '&startdate=' + start + '&zone=' + zone + '&child_id=' + child_id,
                            type: 'POST',
                            dataType: 'json',
                            success: function(response) {
                                event.id = response.eventid;
                                $('#calendar').fullCalendar('updateEvent', event);
                            },
                            error: function(e) {
                                console.log(e.responseText);

                            }
                        });
                        $('#calendar').fullCalendar('updateEvent', event);
                        console.log(event);
                    },
                    eventDrop: function(event, delta, revertFunc) {
                        var title = event.title;
                        var start = event.start.format();
                        var end = (event.end == null) ? start : event.end.format();
                        $.ajax({
                            url: 'process.php',
                            data: 'type=resetdate&title=' + title + '&start=' + start + '&end=' + end + '&eventid=' + event.id,
                            type: 'POST',
                            dataType: 'json',
                            success: function(response) {
                                if (response.status != 'success')
                                    revertFunc();
                            },
                            error: function(e) {
                                revertFunc();
                                alert('Error processing your request: ' + e.responseText);
                            }
                        });
                    },
                    eventClick: function(event, jsEvent, view) {
                        var con = confirm('Искате ли да изтриете записката?');
                        if (con == true) {
                            $.ajax({
                                url: 'process.php',
                                data: 'type=remove&eventid=' + event.id,
                                type: 'POST',
                                dataType: 'json',
                                success: function(response) {
                                    console.log(response);
                                    if (response.status == 'success') {
                                        $('#calendar').fullCalendar('removeEvents');
                                        getFreshEvents();
                                    }
                                },
                                error: function(e) {
                                    alert('Error processing your request: ' + e.responseText);
                                }
                            });
                        }
                    },
                    eventRender: function(event) {
                        var childid = event.childid;
                    },
                    eventResize: function(event, delta, revertFunc) {
                        console.log(event);
                        var title = event.title;
                        var end = event.end.format();
                        var start = event.start.format();
                        var child_id = event.childid;
                        $.ajax({
                            url: 'process.php',
                            data: 'type=resetdate&title=' + title + '&start=' + start + '&end=' + end + '&eventid=' + event.id + '&child_id=' + child_id,
                            type: 'POST',
                            dataType: 'json',
                            success: function(response) {
                                if (response.status != 'success')
                                    revertFunc();
                            },
                            error: function(e) {
                                revertFunc();
                                alert('Error processing your request: ' + e.responseText);
                            }
                        });
                    },
                    windowResize: function(view) {
                        if ($(window).width() < 514) {
                            $('#calendar').fullCalendar('changeView', 'agendaDay');
                        } else if ($(window).width() > 768) {
                            $('#calendar').fullCalendar('changeView', 'agendaWeek');
                        }
                    }
                });

                function getFreshEvents() {
                    $.ajax({
                        url: 'process.php',
                        type: 'POST', // Send post data
                        data: 'type=fetch',
                        async: false,
                        success: function(s) {
                            freshevents = s;
                        }
                    });
                    $('#calendar').fullCalendar('addEventSource', JSON.parse(freshevents));
                };


                function isElemOverDiv() {
                    var trashEl = jQuery('#trash');

                    var ofs = trashEl.offset();

                    var x1 = ofs.left;
                    var x2 = ofs.left + trashEl.outerWidth(true);
                    var y1 = ofs.top;
                    var y2 = ofs.top + trashEl.outerHeight(true);

                    if (currentMousePos.x >= x1 && currentMousePos.x <= x2 &&
                        currentMousePos.y >= y1 && currentMousePos.y <= y2) {
                        return true;
                    }
                    return false;
                }

            });

        </script>
