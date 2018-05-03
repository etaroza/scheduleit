<?php
    include_once "config.php";
    include_once "Helpers.php";
    include_once "Scheduleit.php";

    $helpers = new Helpers();

    if (isset($_GET["email"])) {
        $currentDate = new DateTime();
        $firstActiveEvent = true;

        $data = new Scheduleit(USER_ID, USERNAME, PASSWORD);

        $singleTeacherData = $data->getSingleTeacherData();

        $uniqueMonth = $data->eventList()["uniqueMonth"];

        $events = $data->prepareTeacherEventsData();

        $dateAndLastHour = $helpers->eventEndingDateAndLastHour($events);

        $reorganizedEventMonths = $data->reorganizeEventMonths($events);
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="schedule for teachers">
    <meta name="author" content="">

    <title>Teacher schedule</title>

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
                <a class="navbar-brand" href="/">
                    <img src="img/logo.png" alt="vox-sprachschule logo">
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-dropdown-form" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbar-dropdown-form">
                    <form action="" method="get" class="form-inline mr-auto mr-3 my-3 my-lg-0" id="emailForm">
                        <input id="teacherEmail" class="form-control mr-sm-2" type="email" name="email" placeholder="name@example.com" aria-label="teacherEmail"
                               aria-describedby="teacherEmail" value="<?php echo $helpers->formInputValueChecker($helpers->formInputValidation($_GET["email"])) ?>" required>
                        <button class="btn btn-outline mt-3 my-sm-0" type="submit">Submit</button>
                    </form>

                    <?php if (isset($_GET["email"])) { ?>
                        <div class="navbar-nav mr-3">
                            <label for="city-select"></label>
                            <select class="city-select my-1 mr-sm-2" name="city-selector" id="city-select">
                                <option></option>
                                <option value="all">All</option>
                                <option value="zurich">Zurich</option>
                                <option value="winterthur">Winterthur</option>
                            </select>
                        </div>

                        <a href="#first-active-event" id="jump-to-today">Jump to Today</a>
                    <?php } ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Begin page content -->
    <main role="main" class="container">

        <?php if (isset($_GET["email"])) {

            if ($data->getResourceList() === "429") { ?>
                <div class="row">
                    <div class="col-12 col-sm-10 col-lg-7">
                        <div id="noTeacher" class="alert alert-danger" role="alert">
                            Scheduling service is currently unavailable, try again later.
                        </div>
                    </div>
                </div>
            <?php } elseif ($singleTeacherData === null) { ?>
                <div class="row">
                    <div class="col-12 col-sm-10 col-lg-7">
                        <div id="noTeacher" class="alert alert-danger" role="alert">
                            Can not find teacher by that email.
                        </div>
                    </div>
                </div>
            <?php } elseif (count($events) === 0) { ?>
                <div class="row">
                    <div class="col-12 col-sm-10 col-lg-7">
                        <div id="noEvents" class="alert alert-warning" role="alert">
                            This teacher has no scheduled events.
                        </div>
                    </div>
                </div>
            <?php } else { ?>

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
                                     * [9] - customers (Nicolas V., Vlaemynck J.)
                                     */
                                    if ($reorganizedEventMonths[$i] == $value) { ?>
                                        <div class="separator">
                                             <?php for ($j = 0; $j < count($events[$i]); $j++) { ?>
                                                <div class="row <?php echo $events[$i][$j][7] ?>">
                                                    <div class="col-3 col-sm-2 event-date">
                                                        <h2>
                                                            <?php if (!($j > 0)) { ?>
                                                                <span class="event-day">
                                                                    <?php echo date("D", strtotime($events[$i][$j][0])) ?>
                                                                </span>
                                                                <span class="event-numerical-day-month">
                                                                    <?php echo date("d", strtotime($events[$i][$j][0]))
                                                                        . " " . date("M", strtotime($events[$i][$j][0])) ?>
                                                                </span>
                                                            <?php } ?>
                                                        </h2>
                                                    </div>
                                                    <div class="col-2 col-sm-3 col-md-2 event-hours">
                                                        <h2>
                                                            <span>
                                                                <?php echo $events[$i][$j][1] ?>
                                                            </span>
                                                        </h2>
                                                    </div>
                                                    <?php if ($currentDate > new DateTime($dateAndLastHour[$i][$j][0])) { ?>
                                                        <div class="col-7 col-sm-7 col-md-8 padding-right-0 event-message">
                                                            <div class="event-details text-muted">
                                                                <span>
                                                                    <?php echo $events[$i][$j][2] . " " . $events[$i][$j][3] . " " .
                                                                        $events[$i][$j][4] . " | " . $events[$i][$j][7] . " - " .
                                                                        $events[$i][$j][5] . " - " . $events[$i][$j][6]
                                                                    ?>
                                                                </span>
                                                                <span>
                                                                    <?php echo $events[$i][$j][8] . ": " . $events[$i][$j][9] ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="col-7 col-sm-7 col-md-8 padding-right-0 event-message">
                                                            <?php if ($firstActiveEvent === true) {
                                                                $firstActiveEvent = false; ?>
                                                                <div id="first-active-event" class="event-details">
                                                            <?php } else { ?>
                                                                <div class="event-details">
                                                            <?php } ?>
                                                                <span>
                                                                    <?php echo $events[$i][$j][2] . " " . $events[$i][$j][3] . " " .
                                                                        $events[$i][$j][4] . " | " . $events[$i][$j][7] . " - " .
                                                                        $events[$i][$j][5] . " - " . $events[$i][$j][6]
                                                                    ?>
                                                                </span>
                                                                <span>
                                                                    <?php echo $events[$i][$j][8] . ": " . $events[$i][$j][9] ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
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
                        <?php foreach ($uniqueMonth as $value) { ?>
                            <li class="col-3 col-md-2 col-lg-1 nav-item">
                                <a class="nav-link" href="#<?php echo $value?>"><?php echo $value?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>
    </footer>

    <?php }
    } ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="js/scripts.js"></script>

    <!--Scroll to earliest active event after submit-->
    <?php if (isset($_GET["email"])) { ?>
        <script>
            jQuery(document).ready(function() {
                setTimeout(function () {
                    jQuery("html, body").animate({
                        scrollTop: jQuery("#first-active-event").offset().top - 65
                    }, 800, "swing");
                }, 400);
            });
        </script>
    <?php } ?>
  </body>
</html>
