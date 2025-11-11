<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{config('app.name')}}</title>

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{URL::asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="banner1">
        <img src="img/hotel/hotel1.jpg" alt="">
    </div>
    <div class="banner3">
        <img src="img/hotel/hotel2.jpg" alt="">
    </div>
    <div class="banner4">
        <img src="img/hotel/hotel3.jpg" alt="">
    </div>
    <div class="row">
        <div class="col-md-4">
                <div class="left">
                    <div class="col-md-9 text-center">
                        <div class="container" style="font-weight:bold">
                            <div class="d-flex flex-column  pt-5">
                                <div class="left-headig" >
                                    <h1 style="font-weight:bold">HOTEL MOUNTAIN</h1>
                                    <h2 style="font-weight:bold">LAGOON SKARDU</h2>
                                </div>
                                <div>
                                    <h4 style="font-weight:bold">Welcome Back!</h4>
                                </div>
                                <div>
                                    <p class="">
                                        The great advantage of a hotel is that it is a refuge from home life.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="left-img">
                            <img src="{{asset('img/bg-01.png')}}"
                            alt="logo"  class="">
                        </div>
                    </div>
                </div>
            </div>

        <div class="col-md-8 m-auto">
            <div class="right">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 m-auto" style="background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width:600px; height:500px; overflow-y: auto;">
                            <form method="post" action="{{ route('register')}}">
                                @csrf
                                <div class="text-center py-3">
                                    <h3 style="font-weight:bold">Sign Up Page</h3>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="type your full name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Address"
                                    value="{{ old('email') }}"
                                    value="{{ old('name') }}" placeholder="type your email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"  placeholder="Password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">ConPassword</label>
                                    <input type="password" name="password_confirmation"  class="form-control"  placeholder="Retype password">
                                    @error('con_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck">
                                    <label class="form-check-label" for="gridCheck">
                                    Remember my preference</label>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary w-100 mt-2" style="font-weight:bold">Sign Up</button>


                                </div>
                                <div class="form-check pt-2">
                                    <input class="form-check-input" type="checkbox" checked>
                                    <label for="form-check-lebel">
                                        I agree to the <a href="" class="text-decoration-none">Terms</a> and <a href="" class="text-decoration-none">Privacy Policy</a>
                                    </label>
                                </div>
                                <p>Already have an account?<a href="{{ route('login') }}" class="text-decoration-none" type="text" style="font-weight:bold"> Sign In</a></p>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="/1sday/js/bootstrap.esm.min.js"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
</body>
</html>








