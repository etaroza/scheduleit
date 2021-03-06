<!DOCTYPE html>
<?php
include_once "../lib/RoomsController.php";
$controller = new \Vox\Scheduleit\RoomsController();
$rooms = $controller->getRooms();
$date = $controller->getRepresentativeDate();
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Schedule of courses and lessons.">
    <meta name="author" content="">
    <meta name="robots" CONTENT="NOINDEX,NOFOLLOW">
    <!-- Autorefresh -->
    <meta http-equiv="refresh" content="<?php echo 15*60 ?>">

    <title>VOX-Sprachschule Schedule</title>

    <!-- Bootstrap core CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">

    <!-- Custom styles -->
    <link href="../css/styles.css" rel="stylesheet" type="text/css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>

<header class="sticky-top <?php echo ($controller->getSchoolResourceGroupId() ? 'mb-0' : '')?>" data-offset="220">
    <nav class="navbar navbar-expand-lg navbar-light navbar-bg">
        <div class="container<?php echo ($controller->getSchoolResourceGroupId() ? '-fluid' : '')?>">
            <span class="navbar-brand">
                <a class="d-md-none d-lg-none" href="/schedule">
                    <img src="../img/vox_bubble.png" alt="VOX-Sprachschule">
                </a>
                <a class="d-none d-md-inline-block d-lg-inline-block" href="/schedule">
                    <img src="../img/vox-logo_250_71.jpg" alt="VOX-Sprachschule">
                </a>
                &nbsp;&nbsp;<a href="" class="btn btn-primary"><?php echo date("l, d F", strtotime($date))?></a>
                <br><small><?php echo $controller->getSchoolName() ?></small>

            </span>

            <span class="<?php echo ($controller->getSchoolResourceGroupId() ? '' : 'd-none')?>">
                <div class="text-center d-none d-lg-block d-md-block">
                    Please report mistakes to:<br>
                    <a class="blink" href="mailto:admin@vox-sprachschule.ch">admin@vox-sprachschule.ch</a>
                    <br>
                    +41 44 22 111 33
                </div>
            </span>

            <h5 class="text-center d-none d-lg-block d-md-block"><small>Check your personal schedule on:</small> <span><a href="https://www.vox-sprachschule.ch/schedule" target="_blank">vox-sprachschule.ch/schedule</a></span></h5>
        </div>
    </nav>
    <table class="table table-bordered mb-0 d-none d-md-table d-lg-table">
        <thead>
            <tr class="table-light">
                <?php foreach ($rooms as $room): ?>
                    <th style='width: <?php echo 100/count($rooms) ?>%' data-room="<?php echo $room['id'] ?>" scope="col" class="text-center"><?php echo $room['name'] ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
    </table>
</header>

<!-- Begin page content -->
<?php if ($controller->getSchoolResourceGroupId()) : ?>
    <main role="main" class="container-fluid pl-0 pr-0">
        <?php if(empty($rooms)): ?>
            <div id="noTeacher" class="alert alert-warning" role="alert">
                Schedule is currently unavailable, try again later.
            </div>
        <?php endif; ?>

        <table class="table table-bordered mb-0">
            <tbody>
                <?php
                    $eventsByRoom = $controller->getEventsByRoom();
                ?>
                <?php foreach (['06:', '07:', '08:', '09:', '10:', '11:', '12:', '13:', '14:', '15:', '16:', '17:', '18:', '19:', '20:', '21:'] as $h): ?>
                    <tr class="h-<?php echo substr($h, 0, -1)+0 ?>">
                        <?php foreach ($rooms as $rid => $room): ?>
                            <?php if (isset($eventsByRoom[$rid])):?>
                                <td style='width: <?php echo 100/count($rooms) ?>%' " data-room="<?php echo $room['id'] ?>" scope="col" class="text-left">
                                    <?php foreach($eventsByRoom[$rid] as $event): ?>
                                        <?php if(substr($event['date_start'], -5, 3) == $h): ?>
                                            <h6>
                                                <?php echo \Vox\Scheduleit\Events::printTimes($event) ?>
                                                <small class="d-md-none d-lg-none">
                                                    <br>
                                                    <?php echo $controller->printRoom($event)?>
                                                </small>
                                                <br>
                                                <small>
                                                    <?php $mode = \Vox\Scheduleit\Events::printLearningMode($event) ?>
                                                    <?php echo $event['title'] ?><?php echo (empty($mode) ? '' : ', ') ?>
                                                    <?php echo $mode ?>
                                                </small>
                                            </h6>
                                            <h6>
                                                <?php echo \Vox\Scheduleit\Events::printLanguage($event) ?>
                                                <small>
                                                    <?php $course = \Vox\Scheduleit\Events::printCourse($event) ?>
                                                    <?php $teacher = \Vox\Scheduleit\Events::printTeacher($event) ?>
                                                    <?php echo $course ?><?php echo (!empty($course) && !empty($teacher) ? ", <em>{$teacher}</em>" : '')?>

                                                    <?php $students = \Vox\Scheduleit\Events::printStudents($event)?>
                                                    <?php if(!empty($students)): ?>
                                                        <br>
                                                        <?php echo $students ?>
                                                    <?php endif ?>
                                                </small>
                                            </h6>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                            <?php else: ?>
                                <td data-room="<?php echo $room['id'] ?>" scope="col" class="text-left">
                                    <!-- No events this hour-->
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <!--Scroll to earliest active event after submit-->
    <script>
        jQuery(function($) {
            $('tbody tr').each(function() {
                var text = $.trim($(this).text());
                if (text.length == 0) {
                    $(this).addClass('d-none');
                }
            });
            if ($('.sticky-top').length == 1) {
                var currentH = (new Date()).getHours();
                // TODO: remove hardcoded beginning and end of the day
                currentH = Math.max(6, currentH);
                currentH = Math.min(21, currentH);
                var $currentRowAccordingToTime = $("tbody tr.h-" + currentH);
                if($currentRowAccordingToTime.is('.d-none')) {
                    var $newVisible = $currentRowAccordingToTime.nextAll(":not(.d-none)").first();
                    if ($newVisible.length == 0) {
                        $newVisible = $currentRowAccordingToTime.prevAll(":not(.d-none)").first();
                    }
                    if ($newVisible.length == 1) {
                        $currentRowAccordingToTime = $newVisible;
                    }
                }

                console.log($currentRowAccordingToTime);

                if ($currentRowAccordingToTime.length == 1){
                    $("html, body").animate({
                        scrollTop: $currentRowAccordingToTime.offset().top - $('.sticky-top').height()
                    }, 300, "swing");
                }
            }
        });
    </script>
<?php else: ?>
    <main role="main" class="container">
        <h1>School schedule</h1>
        <form action="" method="get">
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="school" id="school1" value="<?php echo \Vox\Scheduleit\Resources::GROUP_ROOMS_ZURICH ?>">
                    <label class="form-check-label" for="school1">
                        Zurich
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="school" id="school2" value="<?php echo \Vox\Scheduleit\Resources::GROUP_ROOMS_WINTERTHUR ?>">
                    <label class="form-check-label" for="school2">
                        Winterthur
                    </label>
                </div>
            </div>

            <button class="btn btn-<?php echo isset($_GET['school']) ? 'secondary' : 'primary' ?>" type="submit">Submit</button>
        </form>
    </main>
<?php endif; ?>


<?php /*if(isset($_GET['school'])):?>
    <footer class="fixed-bottom">
        <div class="container">
            <div class="row">
                <nav id="navbar-months" class="col-12 navbar navbar-light">
                    <ul class="nav nav-pills">
                        <li></li>
                    </ul>
                </nav>
            </div>
        </div>
    </footer>
<?php endif; */?>
</body>
</html>
