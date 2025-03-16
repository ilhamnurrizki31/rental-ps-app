<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Booking Berhasil!</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="display-1 text-success">
                                <i class="bi bi-check-circle-fill"></i>
                                ✓
                            </div>
                            <h2 class="mt-3">Terima kasih atas pemesanan Anda!</h2>
                            <p class="lead">Pembayaran Anda telah berhasil diproses.</p>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Detail Pemesanan</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Nomor Booking:</div>
                                    <div class="col-md-8">{{ $booking->id }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Tanggal Booking:</div>
                                    <div class="col-md-8">{{ $booking->booking_date->format('d F Y') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Konsol:</div>
                                    <div class="col-md-8">{{ $booking->console_type }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Jumlah Sesi:</div>
                                    <div class="col-md-8">{{ $booking->session_count }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Harga Dasar:</div>
                                    <div class="col-md-8">Rp {{ number_format($booking->base_price, 0, ',', '.') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Weekend Surcharge:</div>
                                    <div class="col-md-8">Rp {{ number_format($booking->weekend_surcharge, 0, ',', '.') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Total Harga:</div>
                                    <div class="col-md-8">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('bookings.index') }}" class="btn btn-primary">Kembali ke Halaman Utama</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>