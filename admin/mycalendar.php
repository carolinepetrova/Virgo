<link href='css/fullcalendar.css' rel='stylesheet' />
<link href='css/fullcalendar.print.css' rel='stylesheet' media='print' />


<style>
    #trash {
        width: auto;
        height: auto;
        float: left;
        position: relative;
    }
    
    #wrap {
        margin: 15px;
    }
    
    #external-events {
        float: left;
        padding: 0 10px;
        border: 1px solid #ccc;
        background: #eee;
        text-align: left;
    }
    
    #external-events h4 {
        font-size: 16px;
        margin-top: 0;
        padding-top: 1em;
    }
    
    #external-events .fc-event {
        margin: 10px 0;
        cursor: pointer;
    }
    
    #external-events p {
        margin: 1.5em 0;
        font-size: 11px;
        color: #666;
    }
    
    #external-events p input {
        margin: 0;
        vertical-align: middle;
    }
    
    #calendar {}
    
    .fc-event {
        position: relative;
        display: block;
        font-size: 14px;
        padding: 5px 2px;
        border-radius: 3px;
        border: 1px solid #1FDA9A;
        background-color: #1FDA9A;
        font-weight: normal;
    }

</style>
<?php
include("inc/db_connect.php");
include("inc/functions.php");
$person_id = $_SESSION['user'];
$res=mysqli_query($db_connect, "SELECT * FROM users WHERE id='$person_id'");
$userinfo=mysqli_fetch_assoc($res);
$name = $userinfo['name'];
$title = "Табло";
include("sections/header.php");
?>
    <div id="content2" class="content">
        <div style="margin-top: 20px; margin-left: 25px; margin-bottom: 20px;" class="page-head">
            Табло
            <br>
            <span class="page-head-nav">Начало > Табло</span>
        </div>
        <div id='wrap'>

            <div id='calendar' class="col-md-12"></div>

            <div style='clear:both'></div>


        </div>
    </div>

    <?php
include("sections/footer.php");
?>
        <script src='js/moment.min.js'></script>
        <script src='js/jquery.min.js'></script>
        <script src='js/jquery-ui.min.js'></script>
        <script src='js/fullcalendar.min.js'></script>
        <script>
            $(document).ready(function() {

                var zone = "05:30"; //Change this to your timezone

                $.ajax({
                    url: 'personal-calendar.php',
                    type: 'POST', // Send post data
                    data: 'type=fetch',
                    async: false,
                    contentType: "application/x-www-form-urlencoded;charset=ISO-8859-15",
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
                    editable: false,
                    droppable: false,
                    slotDuration: '00:30:00',
                    eventReceive: function(event) {
                        var title = event.title;
                        var start = event.start.format("YYYY-MM-DD[T]HH:mm:SS");
                        $.ajax({
                            url: 'personal-calendar.php',
                            data: 'type=new&title=' + title + '&startdate=' + start + '&zone=' + zone,
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
                    eventResize: function(event, delta, revertFunc) {
                        console.log(event);
                        var title = event.title;
                        var end = event.end.format();
                        var start = event.start.format();
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
                    eventDragStop: function(event, jsEvent, ui, view) {
                        if (isElemOverDiv()) {
                            var con = confirm('Are you sure to delete this event permanently?');
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
                        }
                    }
                });

                function getFreshEvents() {
                    $.ajax({
                        url: 'personal-calendar.php',
                        type: 'POST', // Send post data
                        data: 'type=fetch',
                        async: false,
                        success: function(s) {
                            freshevents = s;
                        }
                    });
                    $('#calendar').fullCalendar('addEventSource', JSON.parse(freshevents));
                }
                



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
