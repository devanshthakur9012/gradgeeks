<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="healthcare">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title>payment</title>
    <meta name="description" content="healthcare">
    <meta name="keywords" content="healthcare">
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Readex+Pro:wght@160..700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/custom/css/powertranz.css') }}">
</head>

<body>
    <div class="payment">
        <div class="payment-form">
            <form class="payment-form" method="post" action="{{ $pay['action'] ?? '#' }}">
                @csrf
                <input type="hidden" name="data" value="{{ json_encode($pay) }}">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group user  d-flex align-items-center justify-content-between">
                            <div class="user-name">
                                @if (($pay['title'] ?? ' ') != ' ')
                                    <label><small>{{ __('Business') }}</small></label>
                                    <h3 class="h6">
                                        {{ $pay['title'] ?? ' ' }}
                                    </h3>
                                @endif
                            </div>
                            <div class="amount">
                                <label>{{ __('amount') }}</label>
                                <div class="Payment-amount">
                                    <span>{{ is_array($pay) ? $pay['currency'] . ' ' . $pay['amount'] : 0 }} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('Card number') }}</label>
                            <input type="text" class="form-control" placeholder="Card Number" name="cardNumber"
                                maxlength="16" pattern="\d{16}" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>{{ __('Expiry date') }}</label>
                            <input type="text" id="expiryDate" class="form-control" placeholder="mm/yy"
                                pattern="\d{2}/\d{2}" maxlength="5" name="expiryDate">
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>{{ __('cvv') }}</label>
                            <input type="text" class="form-control" placeholder="Cvv" pattern="\d{3,4}"
                                maxlength="3" name="cvv">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('Card holder Name') }}</label>
                            <input type="text" class="form-control" placeholder="Name" required=""
                                name="card_name">
                        </div>
                    </div>
                </div>
                <div class="paybtn-wrp">
                    <a href="{{ $pay['back_url']}}" class="btn btn-outline-primary px-5 float-end"
                        type="button">{{ __('Back') }}</a>
                    <button id="card-button" class="btn btn-outline-primary px-5 float-end"
                        type="submit">{{ __('Pay') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('expiryDate').addEventListener('input', function(e) {
            var input = e.target.value.replace(/\D/g, '').substring(0, 4);
            var month = input.substring(0, 2);
            var year = input.substring(2, 4);

            if (input.length > 2) {
                e.target.value = month + '/' + year;
            } else {
                e.target.value = month;
            }
        });
    </script>
</body>

</html>
