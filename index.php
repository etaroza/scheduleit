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
            <div class="col-sm-7">
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

            <div class="col-sm-12">
                <?php
                    if (isset($_GET["email"])) {

                        $data = new Scheduleit(USER_ID, USERNAME, PASSWORD);
                        $singleTeacherData = $data->getSingleTeacherData();
                        $events = $data->prepareTeacherEventsData();

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
                <?php
                        } else {
                ?>
                            <div class="events">
                <?php
                                foreach ($events as $value) {
                ?>
                                    <p class="separator"><?php echo $value?></p>
                <?php
                                }
                                unset($value);
                ?>
                            </div>
                <?php
                        }
                    }
                ?>
            </div>
        </div>
    </main>

    <footer class="footer">
      <div class="container">
        <span class="text-muted">I am just a footer</span>
      </div>
    </footer>
  </body>
</html>
