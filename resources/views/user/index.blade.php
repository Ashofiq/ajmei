@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SYS@1" class="form-control" required>
<div class="title"></div>
  <legend>
<div class="title">
  <div class="widget-header widget-header-small">
    <font size="3" color="blue"><b>User Information</b></font>
    <div class="widget-toolbar">
        <a href="{{route('user.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
    </div>
  </div>
</div></legend>

<div class="widget-body">
  <div class="widget-main">
  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>
  <form action="{{route('user.search')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
    <div class="col-md-2">
       <a href="{{route('user.create')}}" class="btn btn-success btn-sm">
         <i class="fa fa-plus"></i>Add New</a>
    </div>
      <div class="col-md-5">
          <div class="input-group ss-item-required">
                  <select name="user_id" class="col-xs-6 col-sm-4 chosen-select" id="user_id" required>
                      <option value="-1">- Select User -</option>
                      @foreach($combo as $user)
                          <option {{ old('user_id') == $user->id ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                      @endforeach
                  </select>
                  @error('user_id')
                  <span class="text-danger">{{ $message }}</span>
                  @enderror
         </div>
      </div>
      <div class="col-md-3">
        <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search"></span></button>
      </div>
  </div>
  </form>
<br/>

  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-view">
        <thead class="thead-blue">
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role Id</th>
          <th>Role Name</th>
          <th>Action</th>
        </thead>
        <tbody>
            @foreach($users as $user)
              <tr>
                  <td>{{$user->id}}</td>
                  <td>{{$user->name}}</td>
                  <td>{{$user->email}}</td>
                  <td>{{$user->role_id}}</td>
                  <td>{{$user->role?$user->role->name:''}}</td>
                  <td>
                      <a href="#" class="btn btn-xs btn-primary edit">Edit</a>
                      {{--  @can('change-password') --}}
                          <a href="{{route('password.change', $user)}}" class="btn btn-warning btn-sm">Change Password</a>
                      {{-- @endcan --}}
                      <a href="{{route('user.sp.index', $user)}}" class="btn btn-info btn-sm">SP Mapping</a>
                
                  </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
  </div>
  <div class="col-md-12">
    <div class="card-tools">
        <ul class="pagination pagination-sm float-right">
          <p class="pull-right">
             {{ $users->render("pagination::bootstrap-4") }}
          </p>
        </ul>
      </div>
    </div>
</div>
</div>
</section>

<!--- Start Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">	<b>Edit User</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form class="form-horizontal" id="editForm" action="{{ route('user.update', $user->id) }}" method="post" role="form" enctype="multipart/form-data">
         {{ csrf_field() }}
         {{ method_field('PUT') }}
        <input type="text" name="id" id="id" class="form-control" readonly="true" />
        <br/>
        <div class="modal-body">

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Name&nbsp;:</span>
                 </div>
                 <input type="text" name="name"  id="name"  class="form-control" placeholder="">
               </div>
   	       </div>
          </div>

         <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Email&nbsp;:</span>
                 </div>
                 <input type="text" name="email" id="email" class="form-control" placeholder="">
               </div>
   	       </div>
          </div>

          <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text">Role&nbsp;:</span>
               </div>
               <div class="col-xs-12 col-sm-8 @error('name') has-error @enderror">
                   <select id="role_id" name="role_id" class="form-control">
                       @foreach($roles as $role)
                           <option @if($role->id == $user->role_id) selected @endif value="{{$role->id}}">{{$role->name}}</option>
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


        </div>
         <div class="modal-footer">
           <button type="submit" class="btn btn-primary">Update</button>
           <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
         </div>
       </form>

    </div>
  </div>
</div>
<!--- End Edit Modal -->
@stop


@section('pagescript')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('assets/js/ace.min.js') }}"></script>
<script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {

    $('.edit').on('click',function() {
        $tr = $(this).closest('tr');
        var data = $tr.children("td").map(function(){
          return $(this).text();
        }).get();
        console.log(data);

        //alert('HELLO :1:'+data[1]+':2:'+ data[2]+':3:'+data[3] + ':4:'+ data[4]+':5:'+data[5] +':6:'+data[6]+':7:'+data[7]+':8:'+ data[8]+':Nine:'+data[9]+':ten:'+data[10]+':eleven:'+ data[11]+':12:'+ data[14]);
        $('#id').val(data[0]);
        $('#name').val(data[1]);
        $('#email').val(data[2]);
        $('#role_id').val(data[3]);

        $('#editForm').attr('action','{{route("user.update",$user->id)}}');
        $('#editModal').modal('show');
    });

  });
</script>

@stop
