<!DOCTYPE html>
<?php
include_once "lib/RoomsController.php";
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

    <title>VOX-Sprachschule Schedule</title>

    <!-- Bootstrap core CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">

    <!-- Custom styles -->
    <link href="css/styles.css" rel="stylesheet" type="text/css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>

<header class="sticky-top <?php echo ($controller->getSchoolResourceGroupId() ? 'mb-0' : '')?>" data-offset="220">
    <nav class="navbar navbar-expand-lg navbar-light navbar-bg">
        <div class="container<?php echo ($controller->getSchoolResourceGroupId() ? '-fluid' : '')?>">
            <span class="navbar-brand">
                <a class="d-md-none d-lg-none" href="https://www.vox-sprachschule.ch">
                    <img src="img/vox_bubble.png" alt="VOX-Sprachschule">
                </a>
                <a class="d-none d-md-inline-block d-lg-inline-block" href="https://www.vox-sprachschule.ch">
                    <img src="img/vox-logo_250_71.jpg" alt="VOX-Sprachschule">
                </a>
                <a href="" class="btn btn-primary"><?php echo date("l, d F", strtotime($date))?></a>
                <small><?php echo $controller->getSchoolName() ?></small>
            </span>

            <span class="<?php echo ($controller->getSchoolResourceGroupId() ? '' : 'd-none')?>">
                <div class="text-center d-none d-lg-block d-md-block">
                    Please report mistakes to:<br>
                    <a class="blink" href="mailto:admin@vox-sprachschule.ch">admin@vox-sprachschule.ch</a>
                    <br>
                    +41 44 22 111 33
                </div>
            </span>

            <h4 class="d-none d-lg-block d-md-block"><small>Your personal schedule is on: </small><a href="https://www.vox-sprachschule.ch/schedule" target="_blank">vox-sprachschule.ch/schedule</a></h4>
        </div>
    </nav>
    <table class="table table-bordered mb-0">
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
        <table class="table table-bordered mb-0">
            <!--<thead>
                <tr>
                    <?php foreach ($rooms as $room): ?>
                        <th data-room="<?php echo $room['id'] ?>" scope="col" class="text-center"><?php echo $room['name'] ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>-->
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
                var $currentRowAccordingToTime = $("tbody tr.h-" + ((new Date()).getHours()));
                if ($currentRowAccordingToTime.first().length == 1){
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
