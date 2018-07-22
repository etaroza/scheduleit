<?php
    include_once "lib/Resources.php";
    include_once "config.php";
    include_once "Helpers.php";
    include_once "Scheduleit.php";
    include_once "Reports.php";

    $helpers = new Helpers();

    if (isset($_GET["email"])) {
        $currentDate = new DateTime();
        $firstActiveEventSet = false;

        $data = new Scheduleit(USER_ID, USERNAME, PASSWORD);
        $reports = new Reports(USER_ID, USERNAME, PASSWORD);

        $uniqueMonth = ['January', 'January','January','January','January','January','January','January','January','January',];

        if ($data->getResourceList() === "429") {

        } else {
            $singleTeacherData = $data->getSingleTeacherData();

            $uniqueMonth = array_unique($data->prepEvents(1)['month'], SORT_REGULAR);

            $events = $data->prepareTeacherEventsData();

            $dateAndLastHour = $helpers->eventEndingDateAndLastHour($events);

            $reorganizedEventMonths = $data->reorganizeEventMonths($events);
        }


    }
?>
<!DOCTYPE html>
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
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />
    <!-- Custom styles -->
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
  </head>
  <body class="scroll-spy" data-spy="scroll" data-target="#navbar-months" data-offset="220">

    <header>
        <nav class="navbar fixed-top navbar-expand-lg navbar-light navbar-bg">
            <div class="container">
                <a class="navbar-brand d-md-none d-lg-none" href="https://www.vox-sprachschule.ch">
                    <img src="img/vox_bubble.png" alt="VOX-Sprachschule">
                </a>
                <a class="navbar-brand d-none d-md-block d-lg-block" href="https://www.vox-sprachschule.ch">
                    <img src="img/vox-logo_250_71.jpg" alt="VOX-Sprachschule">
                </a>

                <?php if (isset($_GET["email"])) { ?>
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <div class="">
                                    <select class="city-select" name="city-selector" id="city-select">
                                        <option value="all" selected="">All</option>
                                        <option value="zurich">Zurich</option>
                                        <option value="winterthur">Winterthur</option>
                                        <option value="external">External</option>
                                    </select>
                                </div>
                            </div>
                            <span class="col-auto">
                                <a href="mailto:admin@vox-sprachschule.ch" class="btn btn-primary">Change<span class="d-none d-md-inline-block d-lg-inline-block">request</span></a>
                            </span>
                        </div>
                    </div>

<!--
                    <div class="navbar-nav mr-3">&nbsp;
                        <a href="mailto:admin@vox-sprachschule.ch" class="btn btn-primary d-none">Request changes</a>
                    </div>
                    -->
                <?php } ?>
<!--
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-dropdown-form" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbar-dropdown-form">
                </div>
                -->
            </div>
        </nav>
    </header>

    <!-- Begin page content -->
    <main role="main" class="container">

        <?php if (isset($_GET["email"])) { ?>

            <?php if ($data->getResourceList() === "429") { ?>
                <div class="row">
                    <div class="col-12 col-sm-10 col-lg-7">
                        <div id="noTeacher" class="alert alert-danger" role="alert">
                            Schedule is currently unavailable, try again later.
                        </div>
                    </div>
                </div>
            <?php } elseif ($singleTeacherData === null) { ?>
                <div class="row">
                    <div class="col-12 col-sm-10 col-lg-7">
                        <div id="noTeacher" class="alert alert-danger" role="alert">
                            Can not find a schedule by that email. We probably have your wrong email, 
                            please inform <a href="mailto:admin@vox-sprachschule.ch">admin@vox-sprachschule.ch</a> about this.
                        </div>
                    </div>
                </div>
            <?php } elseif (count($events) === 0) { ?>
                <div class="row">
                    <div class="col-12 col-sm-10 col-lg-7">
                        <div id="noEvents" class="alert alert-warning" role="alert">
                            No scheduled events for this email. If you think that's wrong, 
                            please inform <a href="mailto:admin@vox-sprachschule.ch">admin@vox-sprachschule.ch</a>.
                        </div>
                    </div>
                </div>
            <?php } else { ?>

            <div>
                <h4>In total <?php echo $reports->countHoursTillToday()["hours"]; ?> hours, had <?php echo $reports->countHoursTillToday()["amountOfEvents"]; ?> lessons.</h4>
                <p>
                    Till <?php echo $reports->countHoursTillLastDayOfPrevMonth()["date"]; ?>: <?php echo $reports->countHoursTillLastDayOfPrevMonth()["hours"]; ?> hours, had <?php echo $reports->countHoursTillLastDayOfPrevMonth()["amountOfEvents"];?> lessons.
                </p>
            </div>

            <hr>

            <div class="row row-no-gutters">
                <div class="col-12">
                    <div id="events">
                        <?php foreach ($uniqueMonth as $value) { ?>
                            <div id="<?php echo $value?>">
                                <h4><?php echo $value?></h4>
                                <?php for ($i = 0; $i < count($events); $i++) {
                                    /**
                                     * [0] - date (2018-07-17)
                                     * [1] - hours (17:30 - 19:00)
                                     * [2] - language (German(DE))
                                     * [3] - course (TALK B1-B2)
                                     * [4] - intensity (Standard (90x2x12))
                                     * [5] - mode (Small Group (max. 5))
                                     * [6] - title (G1243)
                                     * [7] - String "Zurich" or "Winterthur"
                                     * [8] - room (Room 2)
                                     * [9] - school id
                                     * [10] - customers (Nicolas V., Vlaemynck J.)
                                     */
                                    if ($reorganizedEventMonths[$i] == $value) { ?>
                                        <div class="separator">
                                             <?php for ($j = 0; $j < count($events[$i]); $j++) { ?>
                                                 <?php 
                                                    $date = $events[$i][$j][0];
                                                    $hours = $events[$i][$j][1];
                                                    $language = $events[$i][$j][2];
                                                    $course = $events[$i][$j][3];
                                                    $intensity = $events[$i][$j][4];
                                                    $mode = $events[$i][$j][5];
                                                    $title = $events[$i][$j][6];
                                                    $school = $events[$i][$j][7];
                                                    $room = $events[$i][$j][8];
                                                    $schoolid = $events[$i][$j][9];
                                                    $students = $events[$i][$j][10];

                                                    $isPast = $currentDate > new DateTime($dateAndLastHour[$i][$j][0]);
                                                 ?>
                                                <div class="row <?php echo $schoolid ?> <?php echo $isPast ? 'text-muted':'' ?>">
                                                    <div class="col-3 col-sm-2 col-md-2 event-date">
                                                        <?php if (!($j > 0)) { ?>
                                                            <div class="event-day">
                                                                <?php echo date("D", strtotime($date)) ?>
                                                            </div>
                                                            <div class="event-numerical-day-month">
                                                                <?php echo date("d", strtotime($date))
                                                                    . " " . date("M", strtotime($date)) ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col-9 col-sm-10 col-md-2 event-hours <?php echo ((!$firstActiveEventSet && !$isPast)) ? "first-active-event" : "" ?>">
                                                        <h6>
                                                            <?php echo $hours ?>
                                                            <br>
                                                            <small>
                                                            <?php echo $room ?><span><?php echo ($room && $school ? ", " : "") ?><?php echo "{$school}" ?></small></span>
                                                            </small>
                                                        </h6>
                                                        <div class="d-md-none">
                                                            <div>
                                                                <h6>
                                                                    <?php echo "{$language}" ?><br>
                                                                    <small><?php echo "{$course} {$intensity} "?></small>
                                                                </h6>
                                                                <h6>
                                                                    <?php echo "{$mode}" ?><br>
                                                                    <small><?php echo "{$students}"?></small>
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-none d-md-block col-md-8">
                                                        <div class="event-details">
                                                            <?php $firstActiveEventSet = (!$firstActiveEventSet && !$isPast); ?>
                                                            <div>
                                                                <h6>
                                                                    <?php echo "{$language}" ?> 
                                                                    <small><?php echo "{$course} {$intensity}"?> <?php //echo "{$school}" ?></small>
                                                                </h6>
                                                            </div>
                                                            <div>
                                                                <?php echo "{$mode}: {$students}" ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        <?php } unset($value);?>
                    </div>
                </div>
            </div>
    </main>

    <footer class="fixed-bottom">
        <div class="container">
            <div class="row">
                <nav id="navbar-months" class="col-12 navbar navbar-light">
                    <ul class="nav nav-pills">
                        <?php if (isset($_GET["email"])) { ?>
                            <li class="col-3 col-md-2 col-lg-1 nav-item align-self-center">
                                <a id="jump-to-today-small" href="javascript:void(0)" class="d-md-none btn btn-outline-light">Today</a>
                                <a id="jump-to-today" href="javascript:void(0)" class="d-none d-md-inline-block btn btn-outline-light">Today</a>
                            </li>
                        <?php } ?>
                        <?php foreach ($uniqueMonth as $value) { ?>
                            <li class="col-3 col-md-2 col-lg-1 nav-item align-self-center">
                                <a class="nav-link" href="#<?php echo $value?>"><?php echo substr($value, 0, 3).'.'?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>
    </footer>

    <?php }
    } else {
    ?>
    <main role="main" class="container">
        <div class="row">
            <div class="col">
                <h1>Your schedule</h1>
                <form action="" method="get" class="" id="emailForm">
                    <div class="form-group">
                        <label>Enter your email to retrieve the schedule:</label>
                    </div>
    
                    <div class="form-row">
                        <div class="col-auto">
                            <input id="teacherEmail" class="form-control mr-sm-2" type="email" name="email" placeholder="name@example.com" aria-label="teacherEmail"
                           aria-describedby="teacherEmail" value="<?php echo (isset($_GET["email"]) ? $helpers->formInputValueChecker($helpers->formInputValidation($_GET["email"])) : '') ?>" required>
                        </div>
                        <div class="col-auto">
                        <button class="btn btn-<?php echo isset($_GET['email']) ? 'secondary' : 'primary' ?>" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col">
                <h1>School schedule</h1>
                <form action="rooms.php" method="get">
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
            </div>
        </div>
    </main>
        <?php
    } ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="js/scripts.js"></script>

    <!--Scroll to earliest active event after submit-->
    <?php if (isset($_GET["email"]) && $data->getResourceList() != "429" &&
        $data->getResourceList() != null) { ?>
        <script>
            jQuery(document).ready(function() {
                setTimeout(function () {
                    if (jQuery(".first-active-event:visible").first().length == 1){
                        jQuery("html, body").animate({
                            scrollTop: jQuery(".first-active-event:visible").first().offset().top - 60
                        }, 300, "swing");
                    }
                }, 300);
            });
        </script>
    <?php } ?>
  </body>
</html>
