
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Pembayaran Booking</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6>Detail Booking:</h6>
                            <p>Tanggal: {{ $booking->booking_date->format('d F Y') }}</p>
                            <p>Konsol: {{ $booking->console_type }}</p>
                            <p>Jumlah Sesi: {{ $booking->session_count }}</p>
                            <p>Total Harga: Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button id="pay-button" class="btn btn-primary">Bayar Sekarang</button>
                            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            window.snap.pay('{{ $snap_token }}', {
                onSuccess: function(result){
                    window.location.href = '{{ route('bookings.success') }}?booking_id={{ $booking->id }}';
                },
                onPending: function(result){
                    alert("Pembayaran tertunda, silakan selesaikan pembayaran Anda");
                },
                onError: function(result){
                    alert("Pembayaran gagal!");
                },
                onClose: function(){
                    alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                }
            });
        });
    </script>
</body>
</html>