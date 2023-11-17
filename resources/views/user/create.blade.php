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
        <font size="3" color="blue"><b>New User Creation</b></font>
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

 <form id="itm_Form" action="{{ route('user.store') }}" method="post">
   {{ csrf_field() }}
   <div class="widget-body">
     <div class="widget-main">
       <div class="row">
            <div class="col-md-6">
              <div class="input-group mb-3">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:115px">Name :</div>
                </div>
                <input type="text" name="name"  id="name"  class="form-control" placeholder="" required>
              </div>
          </div>
         </div>

        <div class="row">
            <div class="col-md-6">
              <div class="input-group mb-3">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:115px">Email :</div>
                </div>
                <input type="text" name="email" id="email" class="form-control" placeholder="" required>
              </div>
          </div>
         </div>
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
        <div class="col-md-6">
          <div class="input-group">
            <div class="input-group-prepend ">
                <div class="input-group-text" style="min-width:115px">Role :</div>
            </div>
            <div class="col-xs-12 col-sm-6 @error('name') has-error @enderror">
                <select id="role_id" name="role_id" class="form-control" required>
                    <option value="">--Select--</option>
                    @foreach($roles as $role)
                        <option value="{{$role->id}}">{{$role->name}}</option>
                    @endforeach
                </select>

                @error('account_number')
                <span class="text-danger">
                         {{ $message }}
                    </span>
                @enderror
            </div>
          </div>
        </div>
       </div>
       <div class="row">
         <div class="col-md-6">
           <div class="input-group mb-3">
             <div class="input-group-prepend">
               <div class="input-group-text" style="min-width:115px">Company :</div>
             </div>
             <select class="form-control m-bot15" name="company_code" required>
               <option value="" >--Select--</option>
                @if ($companieslist->count())
                    @foreach($companieslist as $company)
                        <option value="{{ $company->id  }}" >{{ $company->id }}--{{ $company->name }}</option>
                    @endforeach
                @endif
            </select>
           </div>
       </div>
       </div>
      <div class="row">
        <div class="col-md-4">
          <button class="btn btn-primary" type="submit">Save</button>
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

</script>
@stop
