@extends('layouts.app')
@section('content')

<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SYS@1" class="form-control" required>
  <div class="container">
  <div class="title">Company Profile</div>
  <div class="btn-top">
    <div class="row">
      <div class="col-12">
          @if(Session::has('message'))
             <p class="alert alert-success">{{ Session::get('message') }}</p>
          @endif
      </div>
    </div>
      <div class="row">
          <div class="col-md-12">
            <div class="btn-group">
                <a href="#" data-url="" data-toggle="modal" data-target="#addModal" class="tab-link btn btn-light">
                  <i class="mdi mdi-plus"></i> Add New</a>

                <a href="#" class="btn btn-light" id="companyerp-edit-companyposting">
                  <i class="mdi mdi-pencil"></i> Edit</a>

                <a href="{{URL('/company/destroy/')}}" class="btn btn-danger" id="companyerp-delete-companyposting">
                  <i class="mdi mdi-trash-can-outline"></i> Delete</a>

                  <form  method="post" action="{{URL('/company/destroy/')}}" class="delete_form">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <button id="companyerp-delete-companyposting" class='btn btn-danger btn-sm delete'  type="submit" ><i class="mdi mdi-trash-can-outline"></i> Delete</button>
                  </form>

              <div class="btn-group" role="group">
                  <a href="#" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="mdi mdi-export"></i> Export</a>
                  <div class="dropdown-menu">
                    <a href="#" onclick="tableTransferposting.print(true, false);" class="dropdown-item"><i class="mdi mdi-printer"></i> Print/PDF</a>
                    <a href="#" onclick="tableTransferposting.download('xlsx', document.title+'.xlsx');" class="dropdown-item"><i class="mdi mdi-file-excel-box"></i> To Excel</a>
                    <a href="#" onclick="tableTransferposting.download('csv', document.title+'.csv');" class="dropdown-item"><i class="mdi mdi-file-delimited"></i> To CSV</a>
                  </div>
              </div>

            </div>
          </div>
      </div>
  </div>

  <div class="alert" role="alert" style="display:none"></div>
  <style>.input-group-text { min-width: 110px; } </style>
  <input type="text" id="companyerp-selected-companyposting" class="" />
  <table class="" id="companyerp-table">

  </table>

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

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Company Code&nbsp;:</span>
                 </div>
                 <input type="text" name="companycode" class="form-control" placeholder="" required>
               </div>
           </div>
          </div>

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
                   <span class="input-group-text">Company Code&nbsp;:</span>
                 </div>
                 <input type="text" name="companycode" id = "companycode" class="form-control" placeholder="">
               </div>
           </div>
          </div>

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
 //var tabledata1 = {!!$companies!!}

 var searched = false;

 var options = {
    height: '100%',
    selectable: true, // 1 for single selection
    // selectableRangeMode: "click",
    index:'id',
    pagination:"local",
    paginationSize: 10,
    paginationSizeSelector:[10, 30, 50, 100],
    ajaxFiltering:true,
    ajaxSorting:true,
    ajaxURL:'http://localhost/erpapp/public/company/table',
    ajaxConfig:"GET",
    layout:"fitColumns",
    responsiveLayout: true,
    reactiveData:true, //enable reactive data
    movableColumns:true,
    placeholder:"No Data Available",
    columns:[
        {title:"<center>SL</center>", formatter:"rownum", headerSort: false, width: '40', hozAlign: "center"},
        {title:'Id',field:'id',formatter:'html',headerSort:true, },
        {title:'Name',field:'name',formatter:'html',headerSort:true, },
        {title:'Description',field:'description',formatter:'html',headerSort:true,},
        {title:'Address1',field:'address1',formatter:'html',headerSort:true,},
        {title:'Address2',field:'address2',formatter:'html',headerSort:true,},
        {title:'Level',field:'level',formatter:'html',headerSort:true,},
      ],

    rowClick:
        function(e, row){
        $('#companyerp-selected-companyposting').attr('class', row.getData().id);
    }
    ,
    rowDblClick:function(e, row){
        $('#companyerp-selected-companyposting').attr('class', row.getData().id);
        $('#companyerp-edit-companyposting').trigger('click');
    },

    renderComplete:function(){
    },

    downloadDataFormatter:function(data){
        data.data = data.data.map(function(item){
          Object.keys(item).map(function(key){
                if(item[key] !== null){
                    if(item[key].indexOf('<div class="float">') === -1)
                        item[key] = item[key].replace(/<(?:.|\n)*?>/gm, '');
                    else
                        item[key] = item[key].replace(/<(?:.|\n)*?>/gm, '').replace(/,/g,'');
                }
            });
            return item;
        });
        return data;
    },
    printAsHtml: true,
  };

  var tableTransferposting = new Tabulator("#companyerp-table", options);

  $('#companyerp-search-companyposting').on('click', function(){
      searched = true;
      tableTransferposting.setData('https://robi.erp2all.com/hrm/jobposting/transfer/json/&search='+$('#companyerp-search-input-companyposting').val())
      return false;
  });

  $('#companyerp-search-input-companyposting').on('keydown', function(e){
      if(e.keyCode === 13){
          $('#companyerp-search-companyposting').trigger('click');
      }
  });

  $('#companyerp-edit-companyposting').on('click',function() {

      if($('#companyerp-selected-companyposting').attr('class') == ''){
          alert('Select a row first');
      }else if($('#companyerp-selected-companyposting').attr('class').indexOf(' ') >= 0){
          alert('Select a single row please');
      }else{
          var value = $('#companyerp-selected-companyposting').attr('class');
          openTab('{{asset("/company/edit")}}'+value);

      }
  });

  var dltUrl = "{{URL('/company/destroy/')}}";
  $('#companyerp-delete-companyposting').on('click',function() {
      var btn = this;
      if($('#companyerp-selected-companyposting').attr('class') == ''){
          alert('Select a row first');
      }else{
          if (confirm("Are you sure you want to delete this?") == true) {
              var values = $('#companyerp-selected-companyposting').attr('class');
              values = values.split(" ").join(",");
              $.ajax({
                  type: "get",
                  dataType: "json",
                  url: dltUrl +'/'+values+'/BD01/',

                  success: function(data)
                  {

                    showAlert(data, btn);
                    if( typeof data['error'] == 'undefined' )
                    reloadTable('companyposting');
                  },
                  error: function(xhr, status, error) {
                      // check status && error
                      alert(xhr.responseText);
                      alert(xhr.status);
                      alert(error);
                  }
              });
                //alert(dltUrl+'/'+values+'/BD01/');


          }
      }
      return false;
  });


  $.ajaxSetup({ cache: false });
  if(typeof window.reloadTable === 'undefined'){
      function camelize(text){
          text = text.replace(/[-_\s.]+(.)?/g, (_, c) => c ? c.toUpperCase() : '');
          return text.substr(0, 1).toLowerCase() + text.substr(1);
      }
      function reloadTable(table){
          $('.alert-danger').slideUp();
                      eval(camelize('table-'+table)).setData();
              }

  }

  if (typeof window.companyerpRedirect === 'undefined') {
      function companyerpRedirect(uri){
          window.location = uri;
      }
  }
  if (typeof window.deselectAll === 'undefined') {
      function deselectAll(htmlID){
          $('#companyerp-table-'+htmlID+' tbody tr').removeClass('selected');
          $('#companyerp-selected-'+htmlID).prop('class','');
      }
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
        $('#companycode').val(data[1]);
        $('#companyname').val(data[2]);
        $('#descirption').val(data[3]);
        $('#address1').val(data[4]);
        $('#address2').val(data[5]);
        $('#level').val(data[6]);

        $('#editForm').attr('action','{{route("company.update")}}');
        $('#editModal').modal('show');
    });

  });
</script>

@stop
