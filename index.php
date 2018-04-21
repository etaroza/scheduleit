<?php
    include_once "Scheduleit.php";
    include_once "config.php";
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Schedule</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="css/sticky-footer.css" rel="stylesheet">
  </head>
  <body class="scroll-spy" data-spy="scroll" data-target="#navbar-months" data-offset="220">
    <!-- Begin page content -->
    <main role="main" class="container">
        <?php if (!isset($_GET["email"])) { ?>
            <div class="row jumbotron border-radius-0 row-no-gutters">
                <div class="mx-auto col-12 col-sm-10 col-lg-7">
                    <h1 class="display-4">Type in teachers email</h1>
                    <hr class="my-4">
        <?php } else { ?>
            <div class="row jumbotron border-radius-0 row-no-gutters">
                <div class="mx-auto col-12 col-sm-10 col-lg-7">
        <?php } ?>
                <form action="" method="get">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" id="teacherEmail" aria-describedby="teacherEmail"
                               placeholder="name@example.com" value="<?php echo isset($_GET['email']) ? $_GET['email'] : '' ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <?php if (!isset($_GET["email"])) { ?>
                <br><br>
                <?php } ?>
            </div>

        </div>

        <?php if (isset($_GET["email"])) {
            $data = new Scheduleit(USER_ID, USERNAME, PASSWORD);
            $singleTeacherData = $data->getSingleTeacherData();

            $events = $data->prepareTeacherEventsData();
            $eventsMonth = $data->getDataFromEventList()["month"];
            $eventsDates = $data->getDataFromEventList()["date"];
            $uniqueMonth = $data->getDataFromEventList()["uniqueMonth"];
            $amountOfEvents = $data->getDataFromEventList()["amountOfEvents"];
            $month = $data->getDataFromEventList()["month"];
            $dateEnd = $data->getDataFromEventList()["dateEnd"];
            $currentDate = new DateTime();

            $reorganizedEventMonths = $data->reorganizeEventMonths($events);

        if ($data->getResourceList() === "429") { ?>
                <div class="row">
                    <div class="col-12 col-sm-10 col-lg-7">
                        <div id="noTeacher" class="alert alert-danger" role="alert">
                            Scheduling service is currently unavailable, try again later.
                        </div>
                    </div>
                </div>
            <?php } elseif ($singleTeacherData == null) { ?>
                <div class="row">
                    <div class="col-12 col-sm-10 col-lg-7">
                        <div id="noTeacher" class="alert alert-danger" role="alert">
                            Can not find teacher by that email.
                        </div>
                    </div>
                </div>
            <?php } elseif (count($events) == 0) { ?>
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
                                                <div class="row">
                                                    <div class="col-4 col-sm-3 event-date-and-time">
                                                        <h2>
                                                        <?php if (!($j > 0)) { ?>
                                                            <span class="event-numerical-day-month">
                                                                <span>
                                                                    <?php echo date("d", strtotime($events[$i][$j][0]))
                                                                        . " " . date("M", strtotime($events[$i][$j][0])) ?>
                                                                </span>
                                                            </span>
                                                            <span class="event-day-hours">
                                                                <span>
                                                                    <?php echo date("l", strtotime($events[$i][$j][0])) ?>
                                                                </span>
                                                                <span>
                                                                    <?php echo $events[$i][$j][1] ?>
                                                                </span>
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="event-day-hours">
                                                                <span>
                                                                    <?php echo $events[$i][$j][1] ?>
                                                                </span>
                                                            </span>
                                                        <?php } ?>
                                                        </h2>
                                                    </div>
                                                    <?php if ($currentDate > new DateTime($dateEnd[$i])) { ?>
                                                        <div class="col-8 col-sm-9">
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
                                                        <div class="col-8 col-sm-9">
                                                            <div class="event-details">
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

    <footer class="footer">
        <div class="container padding-left-right-0">
            <nav id="navbar-months" class="navbar navbar-light bg-light">
                <ul class="nav nav-pills">
                    <?php foreach ($uniqueMonth as $value) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#<?php echo $value?>"><?php echo $value?></a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </footer>

    <?php }
    } ?>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
