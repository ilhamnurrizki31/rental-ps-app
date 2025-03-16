
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Booking PS Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Buat Booking Baru</h5>
                    </div>
                    <div class="card-body">
                        <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="booking_date" class="form-label">Tanggal Booking</label>
                                <input type="date" class="form-control" id="booking_date" name="booking_date" value="{{ $selectedDate }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="console_type" class="form-label">Pilih Konsol</label>
                                <select class="form-select" id="console_type" name="console_type" required>
                                    <option value="">Pilih Konsol</option>
                                    <option value="PS4">PS4 (Rp 30.000/sesi)</option>
                                    <option value="PS5">PS5 (Rp 40.000/sesi)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="session_count" class="form-label">Jumlah Sesi</label>
                                <input type="number" class="form-control" id="session_count" name="session_count" min="1" value="1" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customer_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="customer_phone" name="customer_phone" required>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Rincian Biaya</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Harga Dasar:</span>
                                        <span id="base_price">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Weekend Surcharge:</span>
                                        <span id="weekend_surcharge">Rp 0</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total:</span>
                                        <span id="total_price">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Lanjutkan ke Pembayaran</button>
                                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function calculatePrice() {
                let consoleType = $('#console_type').val();
                let bookingDate = $('#booking_date').val();
                let sessionCount = $('#session_count').val();
                
                if (consoleType && bookingDate && sessionCount) {
                    $.ajax({
                        url: '{{ route("bookings.calculatePrice") }}',
                        method: 'POST',
                        data: {
                            console_type: consoleType,
                            booking_date: bookingDate,
                            session_count: sessionCount
                        },
                        success: function(response) {
                            $('#base_price').text('Rp ' + response.base_price.toLocaleString('id-ID'));
                            $('#weekend_surcharge').text('Rp ' + response.weekend_surcharge.toLocaleString('id-ID'));
                            $('#total_price').text('Rp ' + response.total_price.toLocaleString('id-ID'));
                        }
                    });
                }
            }

            $('#console_type, #booking_date, #session_count').change(calculatePrice);
        });
    </script>
</body>
</html>