@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')

<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="title"><legend>
    <ul class="nav navbar-nav master">
      <li role="presentation" class="nav-item dropdown">
        <a class="nav-link dropdown-toggle " href="#" data-toggle="dropdown"><i class="mdi mdi-tag-outline"></i> <span>Items</span>&nbsp;:::&nbsp;Item Category Main Head</a>
        <div class="dropdown-menu">
            <a href="{{ route('itm.cat.index') }}" color="blue" id="products" class="tab-link dropdown-item"><i class="mdi mdi-view-list"></i> <span>Item Category</span></a>
            <a href="{{ route('itm.create') }}" color="seagreen" id="depreciation" class="tab-link dropdown-item"><i class="mdi mdi-view-quilt"></i> <span>Items</span></a>
        </div>
      </li>
    </ul>
  </legend></div>
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
    <div class="col-md-1">
        <p1 class="card-title"></p1>
     </div>
    <div class="col-md-1">
      @if(Auth::user()->id == 2)
        <p1 class="card-title"><a href="#" data-toggle="modal" data-target="#addModal" class="btn btn-success btn-sm">
        <i class="fa fa-plus"></i> Add</a></p1>
      @endif
     </div>
    <div class="col-md-3">
    <form action="{{route('itm.cat.index')}}" method="post">
      {{ csrf_field() }}
      <div class="input-group mb-3">
        <div class="input-group-prepend">
             <span class="input-group-text">Company Code&nbsp;:</span>
        </div>
        <select class="form-control m-bot15" name="company_code" onchange="company(this.value)" required>
          <option value="" >--Select--</option>
              @if ($companies->count())
                  @foreach($companies as $company)
                      <option {{ old('company_code') == $company->company_code ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                  @endforeach
              @endif
          </select>
         </div>
     </div>
     <div class="col-md-1">
         <button type="submit" class="btn btn-default"><span class="fa fa-search"></span></button>
     </div>
     </form>

     <div class="col-md-4">
       <form action="{{route('itm.cat.tree.view')}}" method="post" target="_blank">
       {{ csrf_field() }}
         <input type="hidden" name="comp_id" id="comp_id" value="{{$companycode}}" class="form-control" autocomplete="off" required readonly/>
       <div class="input-group">
           <select name="itm_category" class="chosen-select" id="itm_category" required>
               <option value="0" selected>- Select Category -</option>
               @foreach($itm_cat as $cat)
                   <option {{ old('itm_category') == $cat->id ? 'selected' : '' }} value="{{ $cat->id }}">{{ $cat->itm_cat_name }} - {{ $cat->itm_cat_origin }}</option>
               @endforeach
           </select>
           @error('itm_category')
           <span class="text-danger">{{ $message }}</span>
           @enderror
        </div>
      </div>
      <div class="col-md-1">
        <button type="submit" class="btn btn-default"><span class="fa fa-info"></span></button>
      </form>
     </div>
  </div>
  <br/>

  <div class="row justify-content-center">
    <div class="col-md-10">
    @csrf
    <table class="table table-striped table-report">
      <thead class="thead-dark">
          <th style="display:none;" class="text-center">Sys.ID</th>
          <th style="display:none;" class="text-center">Comapny Id</th>
          <th style="display:none;" style="width: 4px" class="text-center" >Item Code</th>
          <th class="text-center">Item Category</th>
          <th style="width: 4px" class="text-center">Action</th>
      </thead>

      <tbody>
      @foreach($rows as $d)
       <tr>
         <td style=display:none;>{{ $d->id }}</td>
         <td style="display:none;">{{ $d->itm_comp_id }}</td>
         <td style="display:none;">{{ $d->itm_cat_code }}</td>
         <td><a href="{{ route('itm.cat.mkchild',$d->id) }}">{{ $d->itm_cat_name }}</a></td>

         <td>
         @if(Auth::user()->id == 2)
            <form  method="post" action="{{ url('/itm-cat/destroy',$d->id) }}" class="delete_form">
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
          @endif
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
          {{ $rows->render("pagination::bootstrap-4") }} 
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
        <h5 class="modal-title" id="exampleModalLabel">Add::Item Category Main Head</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <br/>
      <form action="{{route('itm.cat.store')}}" method="POST" enctype="multipart/form-data">
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
                   <span class="input-group-text">Category Name&nbsp;:</span>
                 </div>
                 <input type="text" name="cate_name"  class="form-control" required>
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
        <h5 class="modal-title" id="exampleModalLabel">Edit::Item Category Main Head</h5>
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
                    <span class="input-group-text">Category Name&nbsp;:</span>
                  </div>
                  <input type="text" name="cate_name" id="cate_name" class="form-control" required>
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

  function company($com_id){
    $('#comp_id').val($com_id);
    //alert($com_id);
  }

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
        $('#cate_name').val(data[3]);

        $('#editForm').attr('action','{{route("itm.cat.update")}}');
        $('#editModal').modal('show');
    });

  });
</script>

@stop
