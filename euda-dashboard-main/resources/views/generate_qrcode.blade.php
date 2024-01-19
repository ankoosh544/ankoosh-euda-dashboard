<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Generate QR Code</div>

                <div class="card-body">
                    <form action="{{ route('qrcode.generate') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="plantId">Plant ID:</label>
                            <input type="text" name="plantId" id="plantId" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Generate QR Code</button>
                    </form>

                    <!-- Display QR Code image -->
                    @if(isset($qrCode))
                        <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="QR Code">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>