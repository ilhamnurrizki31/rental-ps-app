
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PS Rental - Booking Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
        }
        .fc-event {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">PS Rental Booking System</h1>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Booking Calendar</h5>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: @json($bookings),
                selectable: true,
                select: function(info) {
                    var today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    var selectedDate = new Date(info.startStr);
                    selectedDate.setHours(0, 0, 0, 0);
                    
                    if (selectedDate >= today) {
                        window.location.href = '{{ route("bookings.create") }}?date=' + info.startStr;
                    } else {
                        alert('Tidak dapat membuat booking untuk tanggal yang sudah lewat.');
                    }
                }
            });
            
            calendar.render();
        });
    </script>
</body>
</html>
