<!DOCTYPE>
<html>
<head>
    <title>Job Card Report</title>
    <style>
    body { margin: 0; font-size: 14px; font-family: "Arrial Narrow";}

    @media print {
        *{
            font-family: "Times New Roman" !important;
        }
        .header, .header-space,
        .footer, .footer-space {
        }
        .wrapper{
            margin-top: 50px;
        }
        .header {
            position: fixed;
            top: 14px;
        }
        .footer {
            position: fixed;

            bottom: 7px;
        }
        .footer p{
            margin: 0px 0px !important;
        }
        p{
            margin: 1px 0px;
            font-size: 15px;
            font-weight: 700;
            font-family: Khaled;
        }
        .wrapper p{

            font-size: 18px;
            font-weight: 700;
            font-family: Khaled;
        }
        .single p{
            font-size: 15px !important;
            margin: 0px 0px !important;
        }
        .single {
            min-height:20px;
            overflow: hidden;
        }
        .row{
            overflow: hidden;
        }
        .margin-t{
            height: 273px;
            width: 100%;
        }
    }

    table {

    }

    td {
      border-top: none;
      border: 1px solid black;
    }
    th {
        border: none;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    </style>
</head>
<body>
<section class="content"> 
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12"> 
      <?php  
          $i=1; 
          $department = ''; 
          $section = ''; 
          $emp_id_no = '';
          $total_present=0; 
          $total_leave=0; 
          $total_absent=0;
      ?>
      @foreach($rows as $row)   
 
        <?php if($emp_id_no != '' && $emp_id_no != $row->emp_id_no) { ?>
          </table>  
          <table class="table table-bordered table-report">
                <tr>
                    <td colspan="12" bgcolor="white"><b>Number Of Days:</b> {{$row->no_days}}  
                    &nbsp;&nbsp;&nbsp;<b>Total Present:</b> {{$total_present}}
                    &nbsp;&nbsp;&nbsp;<b>Total Absent:</b> {{$total_absent}}
                    &nbsp;&nbsp;&nbsp;<b>Total Leave:</b> {{$total_leave}}</td> 
                </tr>
          </table>
        
          <div style="page-break-after:always;"></div> 
        <?php  $total_present=0; $total_absent =0; $total_leave =0;  $i=1;  } ?> 
        
        <?php if($emp_id_no != $row->emp_id_no) {  ?> 
          <table>
            <thead>
                <tr><th class="text-center" colspan="5"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
                <tr><th class="text-center" colspan="5"><font size="3"><b>Job Card Report</b><font></th></tr>
                <tr><th align="right"  colspan="5"><b>Date Range:&nbsp;{{ date('d/m/Y', strtotime($fromdate)) }} to {{ date('d/m/Y', strtotime($todate)) }}</b></th>
              </tr>
            </thead>
            <tbody>   
              <tr>
                <th align="left">Employee Id:</th><th align="left">{{ $row->emp_id_no }}</th> 
                <th align="left">Department:</th><th align="left">{{ $row->department}}</th>
              </tr>
              <tr>
                  <th align="left">Name:</th><th align="left">{{ $row->emp_name }}</th>
                  <th align="left">Section:</th><th align="left">{{ $row->section }}</th>
              </tr>
              <tr>
                  <th align="left">Designation:</th><th align="left">{{ $row->designation }}</th>
                  <th align="left">Shift:</th><th align="left">{{ $row->shiftName }}</th>
              </tr>
              <tr>
                  <th align="left">Joining Date:</th><th align="left">{{date('d-m-Y',strtotime($row->emp_joining_dt))}}</th>
                  <th align="left"></th><th align="left">&nbsp;</th>
              </tr> 
            </tbody>
         </table> 
       
        <table class="table table-bordered table-report"> 
        <thead class="thead-dark">
          <tr>
              <td align="center"><b>SL</b></td> 
              <td align="center"><b>Date</b></td>
              <td align="center"><b>In Time</b></td>
              <td align="center"><b>Out Time</b></td>
              <td align="center"><b>Late</b></td> 
              <td align="center"><b>Early</b></td>
              <td align="center"><b>Over Time</b></td> 
              <td align="center"><b>Status</b></td>   
          </tr>
        </thead>
        <?php } ?>
        <?php 
         
          if($row->vEmpStatus != 'Absent'){
            $total_present +=1;
          }
          if($row->vEmpStatus == 'Absent'){
            $total_absent +=1;
          }
          if($row->vEmpStatus == 'Leave'){
            $total_leave +=1;
          }
        ?>  
        <tbody>   
              <tr>
                <td>{{ $i++ }}</td>   
                <td align="center">{{ date('d-m-Y',strtotime($row->attDate)) }}</td> 
                <td align="center">{{ $row->tInTime }}</td> 
                <td align="center">{{ $row->tOutTime }}</td> 
                <td align="center">{{ $row->tLate }}</td> 
                <td align="center">{{ $row->tEleave }}</td> 
                <td align="center">{{$row->tOT }}</td>  
                <td align="center">{{ $row->vEmpStatus }}</td> 
              </tr> 
        </tbody>  
       <?php $department = $row->department; $section = $row->section; 
               $emp_id_no = $row->emp_id_no; ?>  
        @endforeach 
        <!-- the below table is for last person --> 
        </table>
        <table class="table table-bordered table-report">
            <tr>
              <td colspan="12" bgcolor="white"><b>Number Of Days:</b> {{$row->no_days}}  
              &nbsp;&nbsp;&nbsp;<b>Total Present:</b> {{$total_present}}
              &nbsp;&nbsp;&nbsp;<b>Total Absent:</b> {{$total_absent}}
              &nbsp;&nbsp;&nbsp;<b>Total Leave:</b> {{$total_leave}}</td> 
            </tr>
        </table>
      </div>

    </div>

</div>
</section>
</body>
</html>
