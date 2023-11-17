@extends('layouts.app')
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SYS@1" class="form-control" required>
<div class="title"><legend>Company Profile</legend></div>
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
                 <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
                 <th class="text-center" scope="col">Name</th>
                 <th class="text-center" scope="col">Description</th>
                 <th class="text-center" scope="col">Address1</th>
                 <th class="text-center" scope="col">Address2</th>
                 <th class="text-center" scope="col">Level</th>
                 <th class="text-center" scope="col">Options</th>
               </thead>
               <tbody>
                 @foreach($companies as $company)
                 <tr>
                   <td style=display:none;>{{ $company->id }}</td>
                   <td>{{ $company->name }}</td>
                   <td>{{ $company->description }}</td>
                   <td>{{ $company->address1 }}</td>
                   <td>{{ $company->address2 }}</td>
                   <td class="text-center">{{ $company->level }}</td>
                   <td>
                      <form  method="post" action="{{ url('/company/destroy/'.$company->id.'/'.$company->company_code) }}" class="delete_form">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <div class="btn-group btn-corner">
                          <a href="#" class="btn btn-xs btn-primary edit">Edit</a>
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
        <h5 class="modal-title" id="exampleModalLabel">Company Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <br/>
      <form action="{{route('company.store')}}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-body">

          <!-- div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Company Code&nbsp;:</span>
                 </div>
                 <input type="text" name="companycode" class="form-control" placeholder="" required>
               </div>
           </div>
         </div -->

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Company Name&nbsp;:</span>
                 </div>
                 <input type="text" name="companyname" class="form-control" placeholder="" required>
               </div>
   	       </div>
          </div>

         <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Description&nbsp;:</span>
                 </div>
                 <input type="text" name="descirption"  class="form-control" placeholder="">
               </div>
   	       </div>
          </div>

         <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Address1&nbsp;:</span>
                </div>
                <textarea class="form-control" name="address1" rows="3" required></textarea>
              </div>
            </div>
          </div>

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Address2&nbsp;:</span>
                 </div>
                 <textarea class="form-control" name="address2" rows="3"></textarea>
               </div>
             </div>
           </div>

          <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text">Level&nbsp;:</span>
               </div>
               <input type="text" name="level" class="form-control" placeholder="" required>
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
        <h5 class="modal-title" id="exampleModalLabel">	<b>Company Profile</b></h5>
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
                   <span class="input-group-text">Company Name&nbsp;:</span>
                 </div>
                 <input type="text" name="companyname"  id="companyname"  class="form-control" placeholder="">
               </div>
   	       </div>
          </div>

         <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Description&nbsp;:</span>
                 </div>
                 <input type="text" name="descirption" id="descirption" class="form-control" placeholder="">
               </div>
   	       </div>
          </div>

         <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Address1&nbsp;:</span>
                </div>
                <textarea class="form-control" name="address1" id="address1"  rows="3"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Address2&nbsp;:</span>
                 </div>
                 <textarea class="form-control" name="address2" id="address2" rows="3"></textarea>
               </div>
             </div>
           </div>

          <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text">Level&nbsp;:</span>
               </div>
               <input type="text" name="level" id="level" class="form-control" placeholder="">
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
        $('#companyname').val(data[1]);
        $('#descirption').val(data[2]);
        $('#address1').val(data[3]);
        $('#address2').val(data[4]);
        $('#level').val(data[5]);

        $('#editForm').attr('action','{{route("company.update")}}');
        $('#editModal').modal('show');
    });

  });
</script>

@stop
