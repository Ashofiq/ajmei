@extends('layouts.app')
@section('content')

<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
  <div class="title"><legend>Chart Of Account Main Head</legend></div>
  <div class="container">
  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
        @endif
    </div>
  </div>
  <br/>
  <div class="row">
    <div class="col-md-2">
        <p1 class="card-title"></p1>
     </div>
    <div class="col-md-2">
        <p1 class="card-title"><a href="#" data-toggle="modal" data-target="#addModal" class="btn btn-success btn-sm">
        <i class="fa fa-plus"></i>Add New</a></p1>
     </div>
    <div class="col-md-4">
    <form action="" method="post">
    {{ csrf_field() }}

      <div class="input-group mb-3">
        <div class="input-group-prepend">
             <span class="input-group-text">Company Code&nbsp;:</span>
        </div>
        <select class="form-control m-bot15" name="company_code" required>
          <option value="" >--Select--</option>
              @if ($companies->count())
                  @foreach($companies as $company)
                      <option {{ request()->get('company_code') == $company->company_code ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                  @endforeach
              @endif
          </select>
         </div>
     </div>
     <div class="col-md-2">
         <button type="submit" class="btn btn-default"><span class="fas fa-search">Search</span></button>
     </div>
     </form>
    <div class="col-md-2"><a href="{{ route('acchead.tree.view.list') }}" >Chart of Account View</a></div>

  </div>
  <br/>

  <div class="row justify-content-center">
    <div class="col-md-8">
    @csrf
    <table class="table table-striped table-report">
      <thead class="thead-dark">
          <th style="display:none;" class="text-center">Sys.ID</th>
          <th style="display:none;" class="text-center">Comapny Id</th>
          <th style="display:none;" style="width: 5px">Comapny</th>
          <th style="display:none;" style="width: 4px" class="text-center" >Account Code</th>
          <th class="text-center">Account Head</th>
          <th style="width: 4px" class="text-center">Account Level</th>
          <th style="width: 4px" class="text-center">Action</th>
      </thead>

      <tbody>
      @foreach($chartofaccounts as $d)
       <tr>
         <td style=display:none;>{{ $d->id }}</td>
         <td style="display:none;">{{ $d->company_id }}</td>
         <td style="display:none;">{{ $d->name }}</td>
         <td style="display:none;">{{ $d->acc_code }}</td>
         <td><a href="{{ route('chartofacc.makechildhead',$d->id) }}">{{ $d->acc_head }}</a></td>
         <td class="text-center">{{ $d->acc_level }}</td> 
         
         <td>
           <form  method="post" action="{{ url('/chartofacc/destroy',$d->id) }}" class="delete_form">
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

        </form>
       </tr>
       @endforeach
      </tbody>

    </table>

      </div>
  </div>
  <div class="card-tools">
    <ul class="pagination pagination-sm float-right">
      <p class="pull-right">
          {{ $chartofaccounts->render("pagination::bootstrap-4") }} 
      </p>
    </ul>
  </div>

</div>
</section>

<!-- Start Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add::Chart Of Account Main Head</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <br/>
      <form action="{{route('chartofacc.acchead.store')}}" method="POST" enctype="multipart/form-data">
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
                    @if ($companies->count())
                        @foreach($companies as $company)
                            <option {{ $companycode == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
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
                   <span class="input-group-text">Account Head&nbsp;:</span>
                 </div>
                 <input type="text" name="acc_head"  class="form-control" required>
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
        <h5 class="modal-title" id="exampleModalLabel">Edit::Chart Of Account Main Head</h5>
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
                 <select class="form-control m-bot15" name="company_code" id="company_code"  required>
                   <option value="" >--Select--</option>
                    @if ($companies->count())
                        @foreach($companies as $company)
                            <option {{ $companycode == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
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
                    <span class="input-group-text">Account Head&nbsp;:</span>
                  </div>
                  <input type="text" name="acc_head" id="acc_head" class="form-control" required>
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
        $('#company_code').val(data[1]);
      //  $('#acc_code').val(data[3]);
        $('#acc_head').val(data[4]);

        $('#editForm').attr('action','{{route("chartofacc.acchead.update")}}');
        $('#editModal').modal('show');
    });

  });
</script>

@stop
