@extends('layouts.app')
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SYS@1" class="form-control" required>
<div class="title"><legend>Company Assign Information</legend></div>
<div class="container">
  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
    <br/>
  </div>

  <div class="row">
      <div class="col-md-12">
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn btn-success btn-sm">
          <i class="fa fa-plus"></i>Assgin User</a>
     </div>
 </div>

 <div class="panel panel-success">
   <div class="row">
       <div class="col-md-12">
         @csrf
            <table class="table table-striped table-view">
              <thead class="thead-dark">
                 <tr>
                   <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
                   <th class="text-center" scope="col">Company</th>
                   <th style="display:none;" class="text-center" scope="col">User Id</th>
                   <th class="text-center" scope="col">User Name</th>
                   <th class="text-center" scope="col">Status</th>
                   <th class="text-center" colspan="2">Options</th>
                 </tr>
               </thead>
               <tbody>
                 @foreach($companies as $d)
                 <tr>
                   <td style=display:none;>{{ $d->id }}</td>
                   <td>{{ $d->comp_name }}</td>
                    <td style="display:none;">{{ $d->user_id }}</td>
                   <td>{{ $d->name }}</td>
                    <td>{{ $d->default == 1?'Yes':'No' }}</td>
                        <td>
                          <form  method="post" action="{{ url('/companyassign/destroy',$d->id) }}" class="delete_form">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                          <div class="btn-group btn-corner">
                              <span href="#"  data-toggle="modal" class="btn btn-sm btn-success edit" title="Edit">
                                  Edit
                              </span>
                               <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are You Sure? Want to Delete It.');"
                                      title="Delete">Delete
                               </button>
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
        <h5 class="modal-title" id="exampleModalLabel">Company Assign</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <br/>
      <form action="{{route('companyassign.store')}}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-body">

          <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Company Code&nbsp;:</span>
                </div>
                <select class="form-control m-bot15" name="company_code" required>
                  <option value="" >--Select--</option>
                   @if ($companieslist->count())
                       @foreach($companieslist as $company)
                           <option {{ request()->get('company_code') == $company->id ? 'selected' : '' }} value="{{ $company->id  }}" >{{ $company->id }}--{{ $company->name }}</option>
                       @endforeach
                   @endif
               </select>
              </div>
          </div>
          </div>

          <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">User&nbsp;:</span>
                </div>
                <select class="form-control m-bot15" name="user_id" required>
                  <option value="" >--Select--</option>
                   @if ($userslist->count())
                       @foreach($userslist as $d)
                           <option {{ request()->get('user_id') == $d->id ? 'selected' : '' }} value="{{ $d->id  }}" >{{ $d->id }}--{{ $d->name }}</option>
                       @endforeach
                   @endif
               </select>
              </div>
          </div>
          </div>

          <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Default&nbsp;:</span>
                   &nbsp;<input type="checkbox" name="default" id="default"  class="default la_checkbox" >
                </div>

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
        <h5 class="modal-title" id="exampleModalLabel">	<b>Company Assign</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" method="post" id="editForm" enctype="multipart/form-data">
        {{ csrf_field() }}
         {{ method_field('POST') }}
        <input type="text" name="id" id="id" class="form-control" readonly="true" />
        <br/>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Company Code&nbsp;:</span>
                </div>
                <select class="form-control m-bot15" name="company_code" id="company_code" required>
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
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">User&nbsp;:</span>
                </div>
                <select class="form-control m-bot15" name="user_id" id="user_id" required>
                  <option value="" >--Select--</option>
                   @if ($userslist->count())
                       @foreach($userslist as $d)
                           <option {{ request()->get('user_id') == $d->id ? 'selected' : '' }} value="{{ $d->id  }}" >{{ $d->id }}--{{ $d->name }}</option>
                       @endforeach
                   @endif
               </select>
              </div>
          </div>
          </div>

          <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Default&nbsp;:</span>
                   &nbsp;<input type="checkbox" name="default" id="default"  class="default la_checkbox" {{ request()->get('default') == 1 ? 'checked' : '' }}>
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

<script type="text/javascript">
  $(document).ready(function() {

    $('.edit').on('click',function() {
        $tr = $(this).closest('tr');
        var data = $tr.children("td").map(function(){
          return $(this).text();
        }).get();
        console.log(data);

        //alert('1->'+data[0]+':2->'+ data[1]+':3->'+data[2]);
        $('#id').val(data[0]);
        $('#company_code').val(data[0]);
        $('#user_id').val(data[2]);
        $('#user_id').val(data[2]);

        $('#editForm').attr('action','{{route("companyassign.update")}}');
        $('#editModal').modal('show');
    });

  });
</script>

@stop
