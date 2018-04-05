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
  <body>
    <!-- Begin page content -->
    <main role="main" class="container">
        <div class="row">
            <div class="col-12 col-sm-10 col-lg-7">
                <h1 class="mt-5">Type in teachers email</h1>
                <br><br>
                <form action="" method="get">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" id="teacherEmail" aria-describedby="teacherEmail" placeholder="name@example.com" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <br><br>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php
                if (isset($_GET["email"])) {

                    $data = new Scheduleit(USER_ID, USERNAME, PASSWORD);
                    $singleTeacherData = $data->getSingleTeacherData();

                    $events = $data->prepareTeacherEventsData();
                    $eventsMonth = $data->eventList()["month"];
                    $uniqueMonth = $data->eventList()["uniqueMonth"];
                    $month = $data->eventList()["month"];
                    $dateEnd = $data->eventList()["dateEnd"];
                    $currentDate = new DateTime();

                    if ($singleTeacherData == null) {
                        ?>
                        <div id="noTeacher" class="alert alert-danger" role="alert">
                            Can not find teacher by that email.
                        </div>
                        <?php
                    } elseif (count($events) == 0) {
                        ?>
                        <div id="noEvents" class="alert alert-warning" role="alert">
                            This teacher has no scheduled events.
                        </div>
            </div>
        </div>
                        <?php
                    } else {
                        ?>
        <div class="events">
                        <div class="row">
                            <div class="col-4 col-sm-3 col-lg-2">
                                <nav id="navbar-months" class="navbar navbar-light bg-light flex-column">
                                    <nav class="nav nav-pills flex-column">
                                        <?php
                                            foreach ($uniqueMonth as $value) {
                                        ?>
                                                <a class="nav-link" href="#<?php echo $value?>"><?php echo $value?></a>
                                        <?php
                                            }
                                            unset($value);
                                        ?>
                                    </nav>
                                </nav>
                            </div>
                            <div class="col-8 col-sm-9 col-lg-10">
                                <div class="scroll-spy" data-spy="scroll" data-target="#navbar-months" data-offset="0">
                                    <?php
                                    foreach ($uniqueMonth as $value) {
                                        ?>
                                        <div id="<?php echo $value?>">
                                            <h4><?php echo $value?></h4>

                                            <?php
                                            for ($i = 0; $i < count($events); $i++) {
                                                if ($eventsMonth[$i] == $value) {
                                                    if ($currentDate > new DateTime($dateEnd[$i])) {
                                                        ?>
                                                        <p class="text-muted separator"><?php echo $events[$i] ?></p>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <p class="separator"><?php echo $events[$i] ?></p>
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    unset($value);
                                    ?>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
