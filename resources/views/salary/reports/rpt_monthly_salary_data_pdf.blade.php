<!DOCTYPE>
<html>
<head>
    <title>Monthly Salary Report</title>
    <style>
    body { margin: 0; font-size: 12px; font-family: "Arrial Narrow";} 

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
      height: 200px; 
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
      $sl = 1;  $i=1; $department = ''; $section = ''; $emp_id_no = ''; $firstpage = 'Y';
      $sub_Gross_Salary = 0;
      $sub_Wages_Salary = 0;
      $sub_OT_Hour = 0;
      $sub_Total_OT_Tk = 0;
      $sub_Absence_Deduction = 0;	
      $sub_Net_Payment = 0; 

      $gr_Gross_Salary = 0;
      $gr_Wages_Salary = 0;
      $gr_OT_Hour = 0;
      $gr_Total_OT_Tk = 0;
      $gr_Absence_Deduction = 0;	
      $gr_Net_Payment = 0; 
    ?>

     @foreach($rows as $row) 
     <?php if ($firstpage == 'Y') { ?>
     <table align="center">
        <thead>
            <tr><th class="text-center" colspan="5"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
            <tr><th class="text-center" colspan="5"><font size="3"><b>Factory Wages For the month of {{date("F", mktime(0, 0, 0, $month, 1)) }}, {{ $year }}</b><font></th></tr> 
            <tr><th class="text-center" colspan="5"><font size="3"><b>Section : {{$row->section}}</b><font></th></tr>
        </thead>
      </table> 

      <table autosize="1" width="100%" style="font-size: 18pt; border-collapse: collapse;" cellpadding="5">

            <thead class="thead-light"> 
              <tr style="width: 720px; padding-bottom: 232px;">
                  <td align="center"><b>SL</b></td>
                  <td align="center"><b>Name</b></td>
                  <td align="center"><b>Designation</b></td>
                  <td align="center"><b>D.O.J</b></td>
                  <td align="center"><b>ID NO</b></td>
                  <td align="center"><b>Grade</b></td>
                  <td align="center"><b>Dept</b></td>
                  <td align="center"><b>Inc.</b></td>
                  <td align="center"><b>Leave<br/>(CL)</b></td>
                  <td align="center"><b>Leave<br/>(SL)</b></td>
                  <td align="center"><b>Leave<br/>(ML)</b></td>
                  <td align="center"><b>Leave<br/>(EL)</b></td>
                  <td align="center"><b>Total<br/>Absence</b></td>
                  <td align="center"><b>Gross&nbsp;Salary</b></td>
                  <td align="center"><b>Work Day</b></td>
                  <td align="center"><b>Present Day</b></td> 

                  <td align="center"><b>Basic</b></td>
                  <td align="center"><b>H.Rent</b></td>
                  <td align="center"><b>Medi., Con. & Food</b></td>
                  <td align="center"><b>Wages / Salary</b></td>
                  <td align="center"><b>Att. Bonus</b></td> 

                  <td align="center"><b>OT Rate</b></td>
                  <td align="center"><b>OT Hour</b></td>
                  <td align="center"><b>Total OT (Tk)</b></td>
                  <td align="center"><b>Absence Deduction</b></td>
                  <td align="center"><b>Net Payment</b></td>
                  <td align="center"><b>Signature</b></td>

                </tr> 
              </thead> 
              <tbody>  

     <?php  
        $firstpage = 'N';
      }  

        $absent_deduction = ($row->dBasic/$row->iDays)*$row->iAbsentDays;
        $wages_sal = $row->dGross - $absent_deduction;

        $sub_Gross_Salary = $sub_Gross_Salary + $row->dGross;
        $sub_Wages_Salary = $sub_Wages_Salary + $wages_sal;
        $sub_OT_Hour      = $sub_OT_Hour + $row->ot_hour_sec;  
        $sub_Total_OT_Tk  =  $sub_Total_OT_Tk + $row->dOTAmount;
        $sub_Absence_Deduction = $sub_Absence_Deduction + $absent_deduction;	
        $sub_Net_Payment = $sub_Wages_Salary + $sub_Total_OT_Tk;

        $gr_Gross_Salary = $gr_Gross_Salary + $row->dGross;
        $gr_Wages_Salary = $gr_Wages_Salary + $wages_sal;
        $gr_OT_Hour     = $gr_OT_Hour + $row->ot_hour_sec;  
        $gr_Total_OT_Tk = $gr_Total_OT_Tk + $row->dOTAmount;
        $gr_Absence_Deduction = $gr_Absence_Deduction + $absent_deduction;	
        $gr_Net_Payment = $gr_Net_Payment + $gr_Total_OT_Tk;

     ?>

    <?php if( $section != '' && $section != $row->section ) { 
      
      $h = intval($sub_OT_Hour / 3600); 
      $totaltime = $sub_OT_Hour - ($h * 3600); 
      // Minutes is obtained by dividing
      // remaining total time with 60
      $m = intval($totaltime / 60); 
      // Remaining value is seconds
      $s = $totaltime - ($m * 60); 
      ?>
      <tr>
                <td>&nbsp;</td> 
                <td>&nbsp;</td>
                <td>&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td>  
                <td align="center" colspan="3"><b>Sub Total:</b></td> 
                <td align="right"><b>{{ number_format($sub_Gross_Salary,0) }}</b></td> 
                <td align="right">&nbsp;</td>  
                <td align="right">&nbsp;</td> 
                
                <td align="center" style="line-height: 14px;">&nbsp;</td> 
                <td align="right">&nbsp;</td> 
                <td align="right">&nbsp;</td>  
                <td align="right"><b>{{ number_format($sub_Wages_Salary,0) }}</b></td>
                <td align="right">&nbsp;</td> 
                 <?php $sub_ot_hour = $h.':'.$m.':'.$s; ?>
                <td align="right">&nbsp;</td> 
                <td align="right"><b><?php if($h != 0 && $m != 0 && $s != 0)  echo $sub_ot_hour;  else echo '&nbsp;'; ?></b></td> 
                <td align="right"><b>{{ number_format($sub_Total_OT_Tk,0) }}</b></td> 
                <td align="right"><b>{{ number_format($sub_Absence_Deduction,0) }}</b></td>
                <td align="right"><b>{{ number_format($sub_Net_Payment,0) }}</b></td>
                <td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> 
              </tr> 
    <?php 
       $sub_Gross_Salary = 0;
       $sub_Wages_Salary = 0;
       $sub_OT_Hour      = 0; 
       $sub_Total_OT_Tk  = 0;
       $sub_Absence_Deduction = 0;	
       $sub_Net_Payment = 0; 
       $i = 1;
      } 
    ?>

    <?php if (( $section != '' && $section != $row->section ) || $i == 8) { ?>
    </table>

    <div style="page-break-after:always;"></div> 
    
    <table align="center">
        <thead>
            <tr><th class="text-center" colspan="5"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
            <tr><th class="text-center" colspan="5"><font size="3"><b>Factory Wages For the month of {{date("F", mktime(0, 0, 0, $month, 1)) }}, {{ $year }}</b><font></th></tr> 
            <tr><th class="text-center" colspan="5"><font size="3"><b>Section : {{$row->section}}</b><font></th></tr>
        </thead>
    </table> 

    <table autosize="1" width="100%" style="font-size: 18pt; border-collapse: collapse;" cellpadding="5">
            <thead class="thead-light"> 
              <tr>
              <td align="center"><b>SL</b></td>
                  <td align="center"><b>Name</b></td>
                  <td align="center"><b>Designation</b></td>
                  <td align="center"><b>D.O.J</b></td>
                  <td align="center"><b>ID NO</b></td>
                  <td align="center"><b>Grade</b></td>
                  <td align="center"><b>Dept</b></td>
                  <td align="center"><b>Inc.</b></td>
                  <td align="center"><b>Leave<br/>(CL)</b></td>
                  <td align="center"><b>Leave<br/>(SL)</b></td>
                  <td align="center"><b>Leave<br/>(ML)</b></td>
                  <td align="center"><b>Leave<br/>(EL)</b></td>
                  <td align="center"><b>Total<br/>Absence</b></td>
                  <td align="center"><b>Gross&nbsp;Salary</b></td>
                  <td align="center"><b>Work Day</b></td>
                  <td align="center"><b>Present Day</b></td> 

                  <td align="center"><b>Basic</b></td>
                  <td align="center"><b>H.Rent</b></td>
                  <td align="center"><b>Medi., Con. & Food</b></td>
                  <td align="center"><b>Wages / Salary</b></td>
                  <td align="center"><b>Att. Bonus</b></td> 

                  <td align="center"><b>OT Rate</b></td>
                  <td align="center"><b>OT Hour</b></td>
                  <td align="center"><b>Total OT (Tk)</b></td>
                  <td align="center"><b>Absence Deduction</b></td>
                  <td align="center"><b>Net Payment</b></td>
                  <td align="center"><b>Signature</b></td>

                </tr> 
              </thead> 
              <tbody>
    <?php } ?> 
              <tr>
                <td>{{  $i }}</td> 
                <td>{{ $row->emp_name }}</td>
                <td>{{ $row->designation }}</td> 
                <td align="center">{{ $row->emp_joining_dt ==''?'':date('d-m-Y',strtotime($row->emp_joining_dt)) }}</td> 
                <td align="center">{{ $row->emp_id_no }}</td>
 
                <td align="center">{{ $row->emp_skill_grade }}</td> 
                <td align="center">{{ $row->department }}</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">{{ $row->dLeaveCL }}</td> 
                <td align="center">{{ $row->dLeaveSL }}</td> 
                <td align="center">{{ $row->dLeaveML }}</td>  
                <td align="center">{{ $row->dLeaveEL }}</td> 

                <td align="center">{{ $row->iAbsentDays }}</td> 
                <td align="right">{{ number_format($row->dGross,0) }}</td> 
                <td align="right">{{ $row->iDays }}</td>  
                <td align="right">{{ number_format($row->iDays - $row->iAbsentDays,0) }}</td> 
                
                <td align="center" style="line-height: 14px;">{{ number_format($row->dBasic,0) }}</td> 
                <td align="right">{{ number_format($row->dHouseRent,0) }}</td> 
                <td align="right">{{ number_format($row->dConveyance,0) }}</td>  
                <td align="right">{{ number_format($wages_sal,0) }}</td>
                <td align="right">{{ number_format($row->dBonus,0) }}</td> 

                <td align="right">{{ number_format($row->dOTRate,2) }}</td>
                <td align="right">{{ $row->dOTHour =="00:00:00"?'': $row->dOTHour }}</td> 
                <td align="right">{{ number_format($row->dOTAmount,0) }}</td> 
                <td align="right">{{ number_format($absent_deduction,0) }}</td>
                <td align="right">{{ number_format($wages_sal + $row->dOTAmount,0) }}</td>
                <td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
               
              </tr> 
               
              <?php $i++; $department = $row->department; $section = $row->section; 
               $emp_id_no = $row->emp_id_no;?>  
      @endforeach
      
      <tr>
                <td>&nbsp;</td> 
                <td>&nbsp;</td>
                <td>&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td>  
                <td align="center" colspan="3"><b>Sub Total:</b></td> 
                <td align="right"><b>{{ number_format($sub_Gross_Salary,0) }}</b></td> 
                <td align="right">&nbsp;</td>  
                <td align="right">&nbsp;</td> 
                
                <td align="center" style="line-height: 14px;">&nbsp;</td> 
                <td align="right">&nbsp;</td> 
                <td align="right">&nbsp;</td>  
                <td align="right"><b>{{ number_format($sub_Wages_Salary,0) }}</b></td>
                <td align="right">&nbsp;</td> 

                <td align="right">&nbsp;</td> 
                <?php $sub_ot_hour = $h.':'.$m.':'.$s; ?> 
                <td align="right"><b><?php if($h != 0 && $m != 0 && $s != 0)  echo $sub_ot_hour;  else echo '&nbsp;'; ?></b></td> 
                <td align="right"><b>{{ number_format($sub_Total_OT_Tk,0) }}</b></td> 
                <td align="right"><b>{{ number_format($sub_Absence_Deduction,0) }}</b></td>
                <td align="right"><b>{{ number_format($sub_Net_Payment,0) }}</b></td>
                <td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> 
              </tr> 

      <?php       
      $h = intval($gr_OT_Hour / 3600); 
      $totaltime = $gr_OT_Hour - ($h * 3600); 
      // Minutes is obtained by dividing
      // remaining total time with 60
      $m = intval($totaltime / 60); 
      // Remaining value is seconds
      $s = $totaltime - ($m * 60); 
      
      $gr_ot_hour = $h.':'.$m.':'.$s;
      ?>
        <tr>
                <td>&nbsp;</td> 
                <td>&nbsp;</td>
                <td>&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td> 
                <td align="center">&nbsp;</td>  

                <td align="center" colspan="3"><b>Grand Total</b></td> 
                <td align="right"><b>{{ number_format($gr_Gross_Salary,0) }}</b></td> 
                <td align="right">&nbsp;</td>  
                <td align="right">&nbsp;</td> 
                
                <td align="center" style="line-height: 14px;">&nbsp;</td> 
                <td align="right">&nbsp;</td> 
                <td align="right">&nbsp;</td>  
                <td align="right"><b>{{ number_format($gr_Wages_Salary,0) }}</b></td>
                <td align="right">&nbsp;</td> 

                <td align="right">&nbsp;</td> 
                <td align="right"><b><?php if($h != 0 && $m != 0 && $s != 0)  echo $gr_ot_hour;  else echo '&nbsp;'; ?></b></td>  
                <td align="right"><b>{{ number_format($gr_Total_OT_Tk,0) }}</b></td> 
                <td align="right"><b>{{ number_format($gr_Absence_Deduction,0) }}</b></td>
                <td align="right"><b>{{ number_format($gr_Net_Payment,0) }}</b></td>
                <td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> 
              </tr> 

              <tr>
                <td align="center" colspan="5">&nbsp;</td> 
                <td align="center" colspan="5">&nbsp;</td>
                <td align="center" colspan="5">&nbsp;</td> 
                <td align="center" colspan="5">&nbsp;</td> 
                <td align="center" colspan="5">&nbsp;</td> 
                <td align="center" colspan="5">&nbsp;</td>  
              </tr>

              <tr>
                <td align="center" colspan="5" rowspan='2'>Prepared By</td> 
                <td align="center" colspan="5" rowspan='2'>Head Of HR Admin</td>
                <td align="center" colspan="5" rowspan='2'>Head Of Accounts</td> 
                <td align="center" colspan="5" rowspan='2'>GM</td> 
                <td align="center" colspan="5" rowspan='2'>Chariman</td> 
                <td align="center" colspan="5" rowspan='2'>Managing Director</td>  
              </tr>

              </tbody> 
      </table>
    
    </font>
    </div>

  </div> 
</div>
</section>
</body>
</html>
