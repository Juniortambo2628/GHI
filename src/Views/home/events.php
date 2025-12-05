<!-- Events Start -->
<div class="container-fluid event col-bg-subtle py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 section-header-container">
            <h5 class="text-uppercase text-primary">Events & Activities</h5>
            <h1 class="mb-0">Each step brings us closer to our vision of a brighter future for All. Join us in making a difference!</h1>
        </div>
        <?php if (empty($pageData['upcomingEvents'])) : ?>
            <div class="text-center py-5">
                <p class="mb-4">No upcoming events at this time. Please check back later.</p>
                <a class="btn-hover-bg btn btn-primary text-white py-3 px-5" href="<?php echo BASE_URL; ?>/events.php">See All Events</a>
            </div>
        <?php else : ?>
            <div class="events-list-container">
                <?php foreach (array_slice($pageData['upcomingEvents'], 0, 3) as $event) :
                    $eventDate = new DateTime($event['date']);
                    $day = $eventDate->format('d');
                    $month = $eventDate->format('F');
                    $time = $eventDate->format('g:ia');
                    ?>
                    <div class="event-list-item">
                        <div class="event-date-block">
                            <div class="event-day"><?php echo $day; ?></div>
                            <div class="event-month"><?php echo $month; ?></div>
                        </div>
                        <div class="event-image-container">
                            <img src="<?php echo getImageUrl($event['image']); ?>" alt="<?php echo e($event['title']); ?>" class="event-image" loading="lazy" width="300" height="200">
                        </div>
                        <div class="event-details">
                            <h4 class="event-title"><?php echo e($event['title']); ?></h4>
                            <p class="event-subtitle"><?php echo e($event['initiative']); ?></p>
                            <div class="event-meta">
                                <span class="event-location"><i class="fas fa-map-marker-alt"></i> <?php echo e($event['location']); ?></span>
                                <span class="event-time"><i class="fas fa-clock"></i> <?php echo $time; ?></span>
                            </div>
                        </div>
                        <div class="event-action">
                            <button class="btn btn-dark btn-sm" data-open-event-modal="<?php echo htmlspecialchars(json_encode($event), ENT_QUOTES, 'UTF-8'); ?>">view details</button>
                        </div>
                    </div>
                <?php endforeach;
                ?>
            </div>
            <div class="text-center mt-4">
                <a class="btn-hover-bg btn btn-primary text-white py-3 px-5" href="<?php echo BASE_URL; ?>/events.php">See All Events</a>
            </div>
        <?php endif;
        ?>
    </div>
</div>
<!-- Events End -->

