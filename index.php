<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>iMerhaba - Admin Dashboard</title>

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>iMerhaba</h3>
        </div>

        <ul class="list-unstyled components">
            <li class="active">
                <a href="#"><i data-feather="grid"></i>Dashboard</a>
            </li>
            <li>
                <a href="#"><i data-feather="users"></i>Clients</a>
            </li>
            <li>
                <a href="#"><i data-feather="truck"></i>Car Rental</a>
            </li>
            <li>
                <a href="#"><i data-feather="map"></i>Tourism</a>
            </li>
            <li>
                <a href="#"><i data-feather="home"></i>Real Estate</a>
            </li>
            <li>
                <a href="#"><i data-feather="briefcase"></i>Business Services</a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i data-feather="menu"></i>
                </button>
                <div class="ml-auto">
                    Welcome, Super Admin!
                </div>
            </div>
        </nav>

        <h2>Dashboard</h2>
        <p>Welcome to your control center. Here's a snapshot of your business activities.</p>

        <div class="row mb-4">
            <!-- Overview Card 1 -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Today's Bookings</h5>
                            <p class="card-text">12</p>
                        </div>
                        <div class="card-icon">
                            <i data-feather="calendar"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Overview Card 2 -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Income (Today)</h5>
                            <p class="card-text">&euro;1,250</p>
                        </div>
                        <div class="card-icon">
                            <i data-feather="dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Overview Card 3 -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Pending Contracts</h5>
                            <p class="card-text">4</p>
                        </div>
                        <div class="card-icon">
                            <i data-feather="file-text"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Overview Card 4 -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">New Leads</h5>
                            <p class="card-text">8</p>
                        </div>
                        <div class="card-icon">
                            <i data-feather="star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="line"></div>

        <h4>Quick Actions</h4>
        <div class="quick-actions mt-3">
            <a href="#" class="btn btn-primary"><i data-feather="plus-circle"></i> Add Client</a>
            <a href="#" class="btn btn-secondary"><i data-feather="file-plus"></i> Create Invoice</a>
            <a href="#" class="btn btn-primary"><i data-feather="calendar"></i> New Booking</a>
            <a href="#" class="btn btn-secondary"><i data-feather="briefcase"></i> Generate Offer</a>
        </div>
    </div>
</div>

<!-- jQuery CDN - Slim version (=without AJAX) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<!-- Popper.JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9k43UfT4" crossorigin="anonymous"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>

<script>
    feather.replace();
</script>

</body>
</html>