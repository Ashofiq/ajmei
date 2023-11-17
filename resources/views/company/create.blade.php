@extends('layouts.app')
@section('content')

<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SYS@1" class="form-control" required>
<div class="container-fluid">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>

<div class="container">
 <div class="panel panel-success">
   <div class="row">
     <div class="card-header">
       <p1 class="card-title"><a href="#" data-toggle="modal" data-target="#addModal" class="btn btn-success btn-sm">
       <i class="fa fa-plus"></i>Add New</a>&nbsp; &nbsp;</p1>
       <h4 class="card-title right">Company Profile</h4>

       <div class="card-tools">
         <ul class="pagination pagination-sm float-right">
           <p class="pull-right">

           </p>
         </ul>
       </div>
     </div>
   </div>

   <div class="row">
       <div class="col-md-12">
         <div class="card-body table-responsive p-0">
         @csrf
          <table id ="datatable" class="table table-striped table-bordered table-condensed table-hover table-sm">
               <thead class="thead-dark">
                 <tr>
                   <th class="text-center" scope="col">Bank & Branch</th>
                   <th class="text-center" scope="col">BG Category</th>
                   <th class="text-center" scope="col">BG No</th>
                   <th class="text-center" scope="col">BG Amount</th>
                   <th class="text-center" scope="col">Type</th>
                   <th class="text-center" scope="col">Remarks</th>
                   <th class="text-center" scope="col">Status</th>
                   <th class="text-center" scope="col">Email Person</th>
                 </tr>
               </thead>
               <tbody>

                 <tr>

                 </tr>

               </tbody>
          </table>
        </div>
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

      <form action="{{route('company.store')}}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-body">

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Company Name&nbsp;:</span>
                 </div>
                 <input type="text" name="companyname" class="form-control" placeholder="">
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
                <textarea class="form-control" name="address1" rows="3"></textarea>
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
               <input type="text" name="level" class="form-control" placeholder="">
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

        <div class="modal-body">

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Company Name&nbsp;:</span>
                 </div>
                 <input type="text" name="companyname" class="form-control" placeholder="">
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
                <textarea class="form-control" name="address1" rows="3"></textarea>
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
               <input type="text" name="level" class="form-control" placeholder="">
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

@stop()
