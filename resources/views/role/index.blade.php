@extends('layouts.app')
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SYS@1" class="form-control" required>
<div class="title"><legend>Roles Profile</legend></div>
<div class="container">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>

   <div class="row">
       <div class="col-md-12">
         <a href="#" data-toggle="modal" data-target="#addModal" class="btn btn-success btn-sm">
                  <i class="fa fa-plus"></i>Add New</a>
      </div>
  </div>
<br/>
  <div class="row">
          <div class="col-md-12">
             @csrf
             <table class="table table-striped table-view">
              <thead class="thead-dark">
                 <th class="text-center" scope="col">Sys.ID</th>
                 <th class="text-center" scope="col">Name</th>
                 <th class="text-center" scope="col">Options</th>
               </thead>
               <tbody>
                 @foreach($roles as $role)
                 <tr>
                   <td>{{ $role->id }}</td>
                   <td>{{ $role->name }}</td>
                   <td>
                      <form  method="post" action="{{ url('/role/destroy/'.$role->id) }}" class="delete_form">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <div class="btn-group btn-corner">
                          <a href="#" class="btn btn-xs btn-primary edit">Edit</a>
                          <a href="{{ route('role.access',$role->id) }}" class="btn btn-xs btn-info">Mapping Access</a>
                          <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>
                        </div>
                      </form>
                  </td>
                 </tr>
                 @endforeach
               </tbody>
          </table>

      </div>
  </div>



</div>
</section>

<!-- Start Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New Role Creation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <br/>
      <form action="{{route('role.store')}}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-body">

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Role Name&nbsp;:</span>
                 </div>
                 <input type="text" name="name" class="form-control" placeholder="" required>
               </div>
   	       </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        </div>
      </form>

    </div>
  </div>
</div>
<!-- End Add Modal -->

<!--- Start Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">	<b>Edit Role</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" method="post" id="editForm" enctype="multipart/form-data">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
        <input type="text" name="id" id="id" class="form-control" readonly="true" />
        <br/>
        <div class="modal-body">

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Role Name&nbsp;:</span>
                 </div>
                 <input type="text" name="name"  id="name"  class="form-control" placeholder="">
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

        $('#editForm').attr('action','{{route("role.update",$role)}}');
        $('#editModal').modal('show');
    });

  });
</script>

@stop
