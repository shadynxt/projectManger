<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Project Task Manger - AlQemam Web Development</title>
      <!-- Latest compiled and minified CSS -->
      <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <!-- Fonts -->

        <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}" />

        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

        @yield('styles')

    </head>
    <body>
        <!-- /resources/views/layout.blade.php -->
        
        <nav id="myNavbar" class="navbar navbar-default" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">Task Manager</a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <!-- <li><a href="#">Home</a></li>  --> 

                        <li>
                            <a href="{{ route('user.index') }}"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Users</a>
                        </li>
                                                            

                        <li>
                            <a href="{{ route('project.show') }}"><span class="glyphicon glyphicon-list" aria-hidden="true"></span> Projects</a>
                        </li>



                        <li class="dropdown">
                            <a href="#" data-toggle="dropdown" class="dropdown-toggle">Tasks <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('task.show') }}"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> All Tasks</a></li>
                                <li><a href="{{ route('task.create') }}"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create new Task</a></li>
                            </ul>
                        </li>


                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>

                             <li class="dropdown">
                                <a href="#" class="dropdown-toggle notificaiton"  data-toggle="dropdown" role="button" aria-expanded="false">
                                     <span class="glyphicon glyphicon-globe"></span>
                                     notification

                                    <span id="count">{{ count(auth()->user()->unreadNotifications) }}</span>
                                     <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu" id="showNofication">
                                    @foreach(auth()->user()->notifications as $note)
                                        <li>
                                            <a href="" class="{{ $note->read_at == null ? 'unread' : '' }}">
                                                    {!! $note->data['data'] !!}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
</li>
                        @endif
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div>
        </nav>

        <section class="main-content">
        <div class="container">   
           
                @yield('content')
            
        </div>
        </section>

        <!--   FOOTER -->
       
        <div class="footer-bottom">

            <div class="container">
        
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

                    <div class="copyright">

                        © 2018, AlQemam for programming, All rights reserved

                    </div>

                </div>

                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right">

                    <div class="design">

                        <a target="_blank" href="http://www.alqemam.com/">Design and Development by AlQemam for programming</a>

                    </div>

                </div>

            </div>

        </div>
     

    </body>

<script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>

<script src="{{ asset('js/bootstrap.min.js') }}"></script>    

<script src="{{asset('js/toastr.min.js') }}"></script>

<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
<script src="/StreamLab/StreamLab.js"></script>
<script>
        var message, ShowDiv = $('#showNofication'), count = $('#count'), c;
        var slh = new StreamLabHtml();
        var sls = new StreamLabSocket({
            appId:"{{ config('stream_lab.app_id') }}",
            channelName:"test",
            event:"*"
        });
        sls.socket.onmessage = function(res){
            slh.setData(res);
            if(slh.getSource() === 'messages'){
                c = parseInt(count.html());
                count.html(c+1);
                message  = slh.getMessage();
                ShowDiv.prepend('<li><a href="" class="unread">'+message+'</a></li>');
            }
        }
        $('.notificaiton').on('click' , function(){
            setTimeout( function(){
                count.html(0);
                $('.unread').each(function(){
                    $(this).removeClass('unread');
                });
            }, 5000);
            $.get('MarkAllSeen' , function(){});
        });
    </script>

<script>

@if ( Session::has('success') )
    toastr.success("{{ Session::get('success') }}")
@endif

@if ( Session::has('info') )
    toastr.info("{{ Session::get('info') }}")
@endif

</script>

@yield('scripts')


</html>