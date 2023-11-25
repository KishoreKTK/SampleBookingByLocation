<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Print</title>
    <style>
        .table .thead-dark th {
            color: #343436;
            background-color: #6e1200;
            border-color: #6e1200;
            -webkit-print-color-adjust: exact;
        }

        #logo {
            width: 120px;
            margin-right: 20px;
        }

        .terms {
            margin-top: 100px;
        }

        #invoice-footer {
            margin-top: 100px;
        }

        @media print {
            .table .thead-dark th {
            color: #343436;
            background-color: #6e1200;
            border-color: #6e1200;
            -webkit-print-color-adjust: exact;
        }

        #logo {
            width: 120px;
            margin-right: 20px;
        }

        .terms {
            margin-top: 100px;
        }

        #invoice-footer {
            margin-top: 100px;
        }
        }

    </style>

</head>

<body>
    <div class="container">
        <section class="card p-3" style="border: none;">
            <div class="card-body">
                <!-- Invoice Company Details -->
                <div id="invoice-company-details" class="row">
                    <div class="col-md-6 col-sm-12 text-center text-md-left">
                        <div class="media">

                        <a href="">   <img src="{{url('/')}}/public/img/logo/mood-white-logo.png" style="width:150px; height:120px;background:#6e1200"  alt="logo.png"></a>
                            
                            <div class="media-body ">
                                <ul class="ml-2 px-0 list-unstyled">

                                    <li class="">Mood Portal</li>
                                    <li>Address</li>
                                    <li>Dubai, UAE</li>
                                   

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 text-center text-md-right">
                        <h2 class="font-weight-bolder">INVOICE</h2>
                        <!-- <p class="pb-3"># INV-001001</p> -->
                        <!-- <ul class="px-0 list-unstyled">
                            <li>Balance Due</li>
                        </ul> -->
                    </div>
                </div>
                <!--/ Invoice Company Details -->
                <!-- Invoice Customer Details -->
                <div id="invoice-customer-details" class="row pt-2">

                    <div class="col-md-6 col-sm-12 text-center text-md-left">
                        <h6>Bill to: </h6>
                        <ul class="px-0 list-unstyled">

                            <li class="font-weight-bold">{{$booking->first_name}} {{$booking->last_name}}</li>
                            <li>{{$booking->address}}</li>
                            <!-- <li>New Mexico-87102</li> -->
                        </ul>
                    </div>
                    <div class="col-md-6 col-sm-12 text-center text-md-right">
                        <p>
                            <span class="text-muted"> :</span> {{$today}}</p>
                        <!-- <p>
                            <span class="text-muted">Terms :</span> Due on Receipt</p>
                        <p>
                            <span class="text-muted">Due Date :</span> 10/05/2016</p> -->
                    </div>
                </div>
                <!--/ Invoice Customer Details -->
                <!-- Invoice Items Details -->
                <div id="invoice-items-details" class="pt-3">
                    <div class="row">
                        <div class="table-responsive  mb-3 col-sm-12">
                            <table class="table table-sm table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th class="text-right">Salon</th>
                                        <th class="text-right">Amount</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td class="text-right">{{$booking->name}}</td>
                                        <td class="text-right">{{$booking->amount}}</td>
                                    </tr>
                                    @if(isset($services) && count($services)>0)
                                    <tr>
                                        <th class="text-right">#</th>
                                        <th class="text-right">Service</th>
                                        <th class="text-right">Date</th>
                                        <th class="text-right">Time</th>
                                        <th class="text-right">Staff</th>
                                    </tr>
                                    @foreach($services as $index=>$service)
                                    <tr>
                                    <td class="text-right"> {{$index+1 }}</td>
                                    <td class="text-right"> {{$service->service}}</td>
                                    <td class="text-right"> {{$service->date}}</td>
                                    <td class="text-right">{{$service->start_time}} - {{$service->end_time}}</td>
                                    <td class="text-right">{{$service->staff}}</td>

                                    </tr>
                                    @endforeach
                                    @endif
                                   
                                  <!--   <tr>
                                        <th scope="row">3</th>
                                        <td>
                                            reate PSD for mobile APP

                                        </td>
                                        <td class="text-right">$ 20.00/hr</td>
                                        <td class="text-right">300</td>
                                        <td class="text-right">$ 6000.00</td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7 col-sm-12 text-center text-md-left">

                           <!--  <h5>Payment Methods:</h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table table-borderless table-sm">
                                        <tbody>
                                            <tr>
                                                <td>Bank name:</td>
                                                <td class="text-right">ABC Bank, USA</td>
                                            </tr>
                                            <tr>
                                                <td>Acc name:</td>
                                                <td class="text-right">Amanda Orton</td>
                                            </tr>
                                            <tr>
                                                <td>IBAN:</td>
                                                <td class="text-right">FGS165461646546AA</td>
                                            </tr>
                                            <tr>
                                                <td>SWIFT code:</td>
                                                <td class="text-right">BTNPP34</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div> -->
                        </div>
                        <div class="col-md-5 col-sm-12">

                            <div class="table-responsive">
                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Sub Total</td>
                                            <td class="text-right">{{$booking->actual_amount}}</td>
                                        </tr>
                                        <!-- <tr>
                                            <td>TAX (12%)</td>
                                            <td class="text-right">$ 1,788.00</td>
                                        </tr> -->
                                        <tr>
                                            <td class="text-bold-800">Amount Paid</td>
                                            <td class="text-bold-800 text-right">{{$booking->amount}}</td>
                                        </tr>
                                        <tr>
                                            <td>Mood Commission</td>
                                            <td class="pink text-right">{{$booking->mood_commission}}</td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Invoice Footer -->
                <div id="invoice-footer">
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <div class="terms">
                                <h6>Terms &amp; Condition</h6>
                                <p>You know, being a test pilot isn't always the healthiest business
                                    in the world. We predict too much for the next year and yet far
                                    too little for the next 10.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <p>Authorized person</p>
                                <img data-savepage-src="../../../app-assets/images/pages/signature-scan.png"
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPgAAACaCAMAAACzM3VoAAAAclBMVEUAAAAAAAB/f38PDw+/v78/Pz/Pz8/f398vLy+fn59fX18fHx+Pj4+vr69vb29PT08AAAAPDw8fHx8vLy8/Pz9fX19ycnJPT0+/v7+fn58AAAAPDw////+/v79vb28fHx+Kioo/Pz/f39+vr6+fn59fX19s2csMAAAAGnRSTlMA62zdLK0cDL1Mjc1cPHyd2cCljHVOOGEIF8qF+u0AAAkqSURBVHja7NrZjtMwAAXQe70vsZ0ZdiPxwv//I+ACbZKSB1o7EuQ8jCVPpfS63uIEp9PpdDqdTqfT6XQ6nU6n0+l0Op26M6JE/iC1MPhvhNgiN5LUCv8DpUlmj1/sFBnw7xOSzmPJy38+eWqxN0zMGMErHKOQ9xMaadBfYMQRjKNMuG8q6M6zUmE8I6nxJ4oWvUlOOWM4IxmwE1yhMz9n2DcYTnKvtbNEb04CeMVomhN2xIDObGv4twZjCer9f1t0sL2EUBhLyv3xL9Cbczgg+MS0u60p6I5TC+4xVNS7reLQnZ9tCy4wUtr5wZVjQX8ltkIkjLK/WKVCegwgMw4Y47rgnqmQFBYDqFkdEZwCG6ZIVhaDIQTRvB8cfMJaJOmywSBao3mLoZhwMAo0rxhKChxLMbXSDg6uA441VXUpA4bKEscKVR3S9QwTjqGSEFprWZ3+LjJjrBgxmk1Cy/lFi+9ylZcz/MqQMJIf3NQpSDLmyaKZam5lIDl7jBQ5YZRUSMac8FuhaWUUQHr9iIGMozMYQTkyBnVvpBnZincYSpDBojdbSJ3unjoBQbTi4ycMZQu7ryYmMiqseZpWSovmPQZTjjFhR6ej3OAu+Qsu3mG4FLsO9TJPuCPmS+Fx8d5gPCH73Ypa6rvVc7ugkvhJKBwhkxl96Ig7vFvdMAiBQ9jQa5Yzd7t6EauHVELgIEpTJnQgXgw2Xs2qN+ht8KK1Dgn9qUht8Hxlm9y8aX95TRs3wT0bhwFEn6E+vfp1TYsZaPELPVZEdYCK84QBrGY0eDrz+g4LHwwAy7D3SFrU1jqzwhATKfB8b14Tbry0S0n8JmiwMtUEQL1gEOu6DKtpdtdoqXUAl/CbI9YU1b0TMtNv/g90Fk+n4ovAT3lqEfa3OXa+Pl67KqzsllxQGmwpZfGIMEf1ezFb8jVgYwbMi9jc35ICvXjGbcbCSo9HeDmHy1yHlVI9Lry//cVlXH0v6QFO6MbTYSVLoQLxmDDLdO89MtLiQv4Ongi33AFMlzEoFR6TRNFNEAorEwsWvFNA1HiQ0vN7OLHtYbgI8naqmRcfDCyteQQeoEQkK6/yKnthwq0MwD+nk/makPKyp2c0itdUslaNG9PjV7cislKGydwegIp7b8hsqx4nfwS3uLKkQhPl4p1H89xD4cBKJ8x64qFeVlBgKTPhCTw/O7nao0U04iZcrPU2qZXx8VsRBoOtstqtRomFRI9nCPXLl2WIWEUrk6S9tnKVFleaCg8plMLirsxFVL8KOlk8ha5fly2sSPuzc4drXV0s7YYFsCn//UrKsrdxcbjBgB6kVAm39CWiYL2OJVdrFYtuErymtH9/wjRhR2BeHhp1YFnQKFwYfmvvXJfbBoEofJY7CCHZbaczJL237/+KtWiEcYRVqRm1servT5xYjnOyy2EXiJJMLJiW+NnBHSuFtxQjOfWCNFeYpSExOaMyh1+/YGXzgNY5mCkXixTT3EFQUyRqfEn/sKDtEuVIsCQWKCeZ80mesPgNJrZjHNIDnRLYki82rmWXgtDYNK4lUYwGW+q+XBQRUSwIYJ6DDXEpiSaB8Q2Z4rPzFZL/ekcGCC4BOz7D0vWKRxow0gsx05gozCPJLDtIvUp4Lm5bnoItJ68JFCOJYrDS2amHiJJJzYc+Dy3zlNWanXiqdNz1Al38tsnGEpypCW9Vpegt1hB0x9v6WW9H8eOHLFzxMmldA8gHNehW2VNaXjMinW15JS01Sy8sHoocVT35SbjPHtjIbA1MImHzK+PHx68E+F+6jY1d+V6CPsMS2fFvYLSjRlwZUk0Q6TLbcRqQyMzb9TKonbp6JVsk6TzFFmsiMknQJozZHD89Pn42MDzpdhDlLEVCxsdvcWxFaaC7OqRipIH0UQYv9KKBGNYf9XRNHqZ+av8SWVtRw/Okm5Maa5P4/fHrg+HDd7XDa9pSuPzwEL8/5NS2fG7nVocUaN6xFcedG778UpcfdTld9NWAWx6LMdmGdH2U+ZcSPzx+iXHI7y7lDYuiFB63PTqriK0+YQo9xrklM80gB3gPGIpl4S0V0tcsEiHG+ONTjPETREMOz4W/VZwzbAgjrBeeHS3ESaanKlPQiTLeECbpztOX/PI5RjJN/PIpJt3P5rsjNkZ2WEwYhRuT01qhRFlNlKa7Exd9g7PD1V1DGNDoHh7IqNRqkcVEuNpc+Jp8snl8jnF+PhPyFh39svOLeENzQHILSYByRg2DRCS16eNEOHPYGLJYTnhWvKrn7akGWmLCj3HMhA6Gi8HGpJk0FRXhxxYbQ+IP/DDkAs5PK2OKCYMS7sxTlZ36iyvC8zP+gK0hi7U0sraf5yWycCLn+ksLXdA7ujyPi4PF1hiGlXS8MiVo488aU0yPF9ZGbskpdTkagsTmdN3q+U9MhQepssSnSl0KrEQTjaumGpvjOdZQLi3nNpk1PidQP6Z0EFhL6FnKc66wPZr8OmNzF58ZxgKX6qybveR0mukDc73EX8HwNbq5QYmkWDYP5tDiJcLREXUWW7N+/0Un3SVCFPoudjBf/w2CGvKL4z27ei2PCrckXPGFc7ngs67jjhoFb1//3bAEXxRzO1+DuMOl0gNeP4IvqC8CSczgT7pvTviQ7UbNX2GIYQbRt7hAv8VNIOc3fBg1AjOoSXEtHG4DxqnxqKJt0zDM8mYi03vcCLojImdrZ7Zli3neT/P63aufzcrspLQsbXNRorwj6TVmqXfPR9wS2huKJ+gXXIZ2iTX2ApVT5zeG9s4QkXHMaizjDcOEcCve9gJCLbjGYu+IXmOC7jX2zpFhijfYO6Fq303AzkmOXsn0f35Dla0x3au8u8bm2INGhf3fqvfAUMHuPtPZATVkg51TD7iivXv6lYA72nv1Ug+4Jol9Y/srAb+hVvyPaFw94Hu3NtGrasD//W3wNsaZuqXvvj/hHhXk7ke4rbbc7e4tHa5BBU6vf8tsC08PPcPOqZ6kELufygAbBSY0u3c2gFWEd/3eu5O68NDv3tGrwlva/YJTVbgivvuZbMBHf6n7P5jBE4q6S93/R7xPcNIXN+/Z+6pLhp1DrmW/+/XkAkN2XHpr9l+3FOimD0LYrj/uf0v4GZKI/qv/53nnzp07d+7cuXNnc34C8pVJxt9zYQwAAAAASUVORK5CYII="
                                    alt="signature" class="height-100">
                                <h6>Amanda Orton</h6>
                                <p class="text-muted">Managing Director</p>
                            </div>
                        </div>

                    </div>
                </div>
                <!--/ Invoice Footer -->
            </div>
        </section>
    </div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>
