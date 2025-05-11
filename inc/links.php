        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="css/common.css">
        <?php 
                session_start();
                date_default_timezone_set("Asia/Kolkata");

                require('admin/inc/db_config.php');
                require('admin/inc/essentials.php');

                $contact_q = "SELECT * FROM `contact_details` WHERE `sr_no`=?";
                $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=?";
                $values = [1];
                $contact_r = mysqli_fetch_assoc(select($contact_q,$values,'i'));
                $settings_r = mysqli_fetch_assoc(select($settings_q,$values,'i'));

                if($settings_r['shutdown']){
                        echo<<<alertbar
                                <div class='bg-danger text-center p-2 fw-bold'>
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        Bookings are temporarily closed!
                                </div>
                        alertbar;
                }

        ?>