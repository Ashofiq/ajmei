@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SYS@1" class="form-control" required>
<div class="title">
  <legend>
  <div class="widget-header widget-header-small">
      <h6 class="widget-title smaller">
        <font size="3" color="blue"><b>Password Change - {{$user->name}} - {{$user->email}}</b></font>
      </h6>
     <div class="widget-toolbar">

     </div>
  </div></legend>
</div>
@if(Session::has('message'))
 <div class="row">
   <div class="col-md-12">
     <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
   </div>
</div>
@endif

 <form id="itm_Form" action="{{route('password.change', $user)}}" method="post">
   {{ csrf_field() }}
   <div class="widget-body">
     <div class="widget-main">
       @if($own == 1)
         <div class="row">
           <div class="col-md-6">
               <div class="input-group ss-item-required">
                   <div class="input-group-prepend ">
                       <div class="input-group-text" style="min-width:115px">Old Password :</div>
                   </div>
                   <input type="password" name="old_password" placeholder="Old Password"
                    class="form-control">
                   @error('old_password')
                    <span class="text-danger">{{$message}}</span>
                   @enderror
              </div>
           </div>
        </div>
      @endif

     <div class="row">
       <div class="col-md-6">
              <div class="input-group ss-item-required">
                  <div class="input-group-prepend ">
                      <div class="input-group-text" style="min-width:115px">New Password :</div>
                  </div>
                  <input type="password" name="password" placeholder="New Password"
                      class="form-control">
                  @error('password')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
             </div>
          </div>
      </div>
      <div class="row">
         <div class="col-md-6">
              <div class="input-group ss-item-required">
                  <div class="input-group-prepend ">
                      <div class="input-group-text" style="min-width:90px">Confirm Password :</div>
                  </div>
                  <input type="password" name="password_confirmation" placeholder="Confirm Password"
                      class="form-control">
              </div>
          </div>
     </div>
      <div class="row">
        <div class="col-md-4">
          <button class="btn btn-primary" type="submit">Update</button>
        </div>
      </div>
    </div>
   </div>
 </form>

@stop


@section('js')

<!-- ace scripts -->
<script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('assets/js/ace.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
<script src="{{ asset('assets/js/counterUp.js') }}"></script>

<!-- inline scripts related to this page -->
<script type="text/javascript">
    jQuery(function ($) {
            $('#sidebar2').insertBefore('.page-content');
            $('#navbar').addClass('h-navbar');
            $('.footer').insertAfter('.page-content');

            $('.page-content').addClass('main-content');

            $('.menu-toggler[data-target="#sidebar2"]').insertBefore('.navbar-brand');


            $(document).on('settings.ace.two_menu',
                function (e, event_name, event_val) {
                    if (event_name == 'sidebar_fixed') {
                        if ($('#sidebar').hasClass('sidebar-fixed')) $('#sidebar2').addClass('sidebar-fixed')
                        else $('#sidebar2').removeClass('sidebar-fixed')
                    }
                }).triggerHandler('settings.ace.two_menu', ['sidebar_fixed' , $('#sidebar').hasClass('sidebar-fixed')]);

            $('#sidebar2[data-sidebar-hover=true]').ace_sidebar_hover('reset');
            $('#sidebar2[data-sidebar-scroll=true]').ace_sidebar_scroll('reset', true);
        })
        $(document).ready(()=>{
            $('#datatable').dataTable();
            $('#dataCustomer').dataTable();
            $('#dataSupplier').dataTable();
        })

        $('.counter').counterUp({
            delay: 10,
            time: 1000
        });
</script>
@stop
