<!DOCTYPE html>
<?php
include_once "lib/PersonController.php";
$controller = new \Vox\Scheduleit\PersonController();
$events = $controller->getEventsGroupedByDate();

$uniqueMonths = array();
foreach (array_keys($events) as $date){
    $d = new \DateTime($date);
    $uniqueMonths[] = $d->format('F');
}
unset($date);
$uniqueMonths = array_unique($uniqueMonths);

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
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />
    <!-- Custom styles -->
    <link href="css/styles.css" rel="stylesheet" type="text/css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  </head>

  <body class="scroll-spy" data-spy="scroll" data-target="#navbar-months" data-offset="220">

    <header class="sticky-top <?php echo ($controller->getPersonId() ? 'mb-5' : '')?>">
        <nav class="navbar navbar-expand-lg navbar-light navbar-bg">
            <div class="container">
                <a class="navbar-brand d-md-none d-lg-none" href="https://www.vox-sprachschule.ch">
                    <img src="img/vox_bubble.png" alt="VOX-Sprachschule">
                </a>
                <a class="navbar-brand d-none d-md-block d-lg-block" href="https://www.vox-sprachschule.ch">
                    <img src="img/vox-logo_250_71.jpg" alt="VOX-Sprachschule">
                </a>

                <?php if ($controller->getPersonId() && $controller->didEventsLoadSuccessfully() && count($events) !== 0) : ?>
                        <div class="col">
                            <div class="row">
                                <?php if ($controller->isTeacher()): ?>
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
                                        <a href="mailto:admin@vox-sprachschule.ch" class="btn btn-primary">Change <span class="d-none d-md-inline-block d-lg-inline-block">request</span></a>
                                    </span>
                                <?php endif ?>
                                <?php if($controller->isCustomer()): ?>
                                    <div class="col"></div>
                                    <div class="col-auto text-center">
                                        Please report mistakes to:<br>
                                        <a class="blink" href="mailto:admin@vox-sprachschule.ch">admin@vox-sprachschule.ch</a>
                                        <br>
                                        +41 44 22 111 33
                                    </div>

                                <?php endif ?>
                            </div>
                        </div>
                <?php endif; ?>
            </div>
        </nav>
    </header>


<?php if ($controller->getPersonId()): ?>
    <!-- Begin page content -->
    <main role="main" class="container">
        <?php if (!$controller->didEventsLoadSuccessfully()) { ?>
            <div class="row">
                <div class="col">
                    <div id="noTeacher" class="alert alert-warning" role="alert">
                        Schedule is currently unavailable, try again later.
                    </div>
                </div>
            </div>
        <?php } elseif (!($controller->isTeacher() || $controller->isCustomer())) { ?>
            <div class="row">
                <div class="col">
                    <div id="noTeacher" class="alert alert-danger" role="alert">
                        Can't find a schedule for <strong><?php echo $controller->getPersonEmail()?></strong>. We probably have your wrong email,
                        please inform <a href="mailto:admin@vox-sprachschule.ch">admin@vox-sprachschule.ch</a> about this.
                    </div>
                </div>
            </div>
        <?php } elseif (count($events) === 0) { ?>
            <div class="row">
                <div class="col">
                    <div id="noEvents" class="alert alert-warning" role="alert">
                        No scheduled events for <strong><?php echo $controller->getPersonEmail()?></strong>. If you think that's wrong,
                        please inform <a href="mailto:admin@vox-sprachschule.ch">admin@vox-sprachschule.ch</a>.
                    </div>
                </div>
            </div>
        <?php } else { ?>

        <div class="row row-no-gutters">
            <div class="col-12">
                <div id="events">
                    <?php foreach ($uniqueMonths as $m) { ?>
                        <div id="<?php echo strtolower($m)?>">
                            <h2><?php echo $m?></h2>
                            <?php foreach(array_keys($events) as $date): ?>
                                <?php $d = new \DateTime($date); ?>
                                <?php if ($m != $d->format('F')) { continue; }?>

                                <div class="separator">
                                    <?php $j = 0; foreach($events[$date] as $event): ?>
                                             <?php
                                                $startDateTime = new \DateTime($event['date_start']);
                                                $endDateTime = new \DateTime($event['date_end']);
                                                $hours = \Vox\Scheduleit\Events::printTimes($event);
                                                $language = \Vox\Scheduleit\Events::printLanguage($event);
                                                $course = \Vox\Scheduleit\Events::printCourse($event);
                                                $intensity = \Vox\Scheduleit\Events::printIntensity($event);
                                                $mode = \Vox\Scheduleit\Events::printLearningMode($event);
                                                $title = $event['title'];
                                                $schoolRoom = \Vox\Scheduleit\Events::printRoomInSchool($event);
                                                $schoolid = strtolower($controller->getSchoolId($event));
                                                $students = \Vox\Scheduleit\Events::printStudents($event);
                                                $teacher = \Vox\Scheduleit\Events::printTeacher($event);
                                                $isPast = new \DateTime() > $startDateTime;
                                             ?>
                                            <div class="row <?php echo $schoolid ?> <?php echo $isPast ? 'text-muted':'' ?>">
                                                <div class="col-3 col-sm-2 col-md-2 event-date">
                                                    <div class="event-date-inner <?php echo ($j++ > 0 ? 'd-none' : '') ?>">
                                                        <div class="event-day">
                                                            <?php echo $startDateTime->format('D') ?>
                                                        </div>
                                                        <div class="event-numerical-day-month">
                                                            <?php echo $startDateTime->format('d')
                                                                . " " . $startDateTime->format('M') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-9 col-sm-10 col-md-2 event-hours <?php echo (!$isPast ? "active-event" : "") ?>">
                                                    <h6>
                                                        <?php echo $hours ?>
                                                        <br>
                                                        <small>
                                                        <?php echo $schoolRoom ?>
                                                        </small>
                                                    </h6>
                                                    <div class="d-md-none">
                                                        <div>
                                                            <h6>
                                                                <?php echo "{$language}" ?>
                                                                <small>
                                                                    <?php echo $course ?>,
                                                                    <?php if($controller->isCustomer() && !empty($teacher)):?>
                                                                        <?php echo '<em>'. $teacher. '</em>';  ?>
                                                                    <?php else: ?>
                                                                        <?php echo '<em>'.$mode.'</em>';  ?>
                                                                    <?php endif ?>
                                                                </small>
                                                            </h6>
                                                            <h6>
                                                                <small><span><?php echo $title ?>:</span> <?php echo "{$students}"?></small>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-none d-md-block col-md-8">
                                                    <div class="event-details">
                                                        <h6>
                                                            <?php echo "{$language}" ?>
                                                            <small>
                                                                <?php echo $course?>,
                                                                <?php if($controller->isCustomer() && !empty($teacher)):?>
                                                                    <?php echo '<em>'. $teacher. '</em>';  ?>
                                                                <?php else: ?>
                                                                    <?php echo '<em>'.$mode.'</em>';  ?>
                                                                <?php endif ?>
                                                            </small>
                                                        </h6>
                                                        <h6>
                                                            <small><?php echo "{$title}: {$students}" ?></small>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    <?php }?>
                </div>
            </div>
        </div>

        <script>
            jQuery(function($) {
                function scrollToEarliestEvent() {
                    if ($(".active-event:visible").first().length == 1){
                        jQuery("html, body").animate({
                            scrollTop: jQuery(".active-event:visible").first().offset().top - $('.sticky-top').height() - 5
                        }, 300, "swing");
                    }
                }

                scrollToEarliestEvent();

                $(".nav-link").click(function () {
                    $("html, body").animate({
                        scrollTop: $($.attr(this, 'href')).offset().top - $('.sticky-top').height() - 5
                    }, 300, 'swing');

                    return false;
                });

                $("#jump-to-today").click(function () {
                    scrollToEarliestEvent();
                    return false;
                });

                $('#city-select').select2({
                    minimumResultsForSearch: Infinity,
                    placeholder: "Select a city",
                    theme: "bootstrap"
                });

                $("#city-select").change(function() {
                    var $allRows = $('.row.winterthur,.row.zurich,.row.external');
                    if ($("select[name=city-selector]").val() === "all") {
                        $allRows.removeClass('d-none');
                    } else{
                        var schoolId = $("select[name=city-selector]").val();
                        $allRows.addClass('d-none');
                        $allRows.filter('.'+schoolId).removeClass('d-none');
                    }
                    $(".event-date-inner", $allRows).addClass('d-none');
                    $(".separator").each(function(){
                       if($("> .row", this).length == $("> .row.d-none", this).length) {
                           // All rows hidden
                           $(this).hide();
                       } else {
                           // Some rows shown
                           $(this).show();
                           // Check that the date is showing
                           $("> .row", this).filter(":not(.d-none):first").find(".event-date-inner.d-none").removeClass("d-none");
                       }

                    });
                    scrollToEarliestEvent();
                });
            });
        </script>

    </main>

    <footer class="fixed-bottom">
        <div class="container">
            <div class="row">
                <nav id="navbar-months" class="col-12 navbar navbar-light">
                    <ul class="nav nav-pills">
                        <?php if ($controller->getPersonId()) { ?>
                            <li class="col-3 col-md-2 col-lg-1 nav-item align-self-center">
                                <a id="jump-to-today" href="javascript:void(0)" class="btn btn-outline-light">Today</a>
                            </li>
                        <?php } ?>
                        <?php foreach ($uniqueMonths as $m) { ?>
                            <li class="col-3 col-md-2 col-lg-1 nav-item align-self-center">
                                <a class="nav-link" href="#<?php echo strtolower($m)?>"><?php echo substr($m, 0, 3).'.'?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>
    </footer>

    <?php } ?>
<?php else: ?>
    <main role="main" class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6">
                <h1>Your schedule</h1>
                <form action="" method="get" class="" id="emailForm">
                    <div class="form-group">
                        <label for="email">Enter your email to retrieve the schedule:</label>
                    </div>
    
                    <div class="form-row">
                        <div class="col-auto">
                            <input id="email" class="form-control mr-sm-2" type="email" name="email" placeholder="name@example.com" value="" required>
                        </div>
                        <div class="col-auto">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6">
                <br class="d-md-none d-lg-none">
                <h1>School schedule</h1>
                <form action="rooms" method="get">
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

                    <button class="btn btn-primary" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </main>
<?php endif; ?>

  </body>
</html>
