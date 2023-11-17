<!DOCTYPE>
<html>
<head>
  <style>
  body { margin: 0; font-size: 11px; font-family: "Arrial Narrow";}

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
    <title>Trial Balance</title>

</head>
<body>
<section class="content">
<div class="container">

  <div class="row justify-content-center">
    <div class="col-md-12">
      <table>
        <thead>
          <thead>
            <tr><th align="center" width="100%" colspan="3"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
              <tr><th align="center" colspan="3"><font size="2"><b>Trial Balance</b><font></th></tr>
              <tr><th align="left" colspan="1"><b>{{request()->get('acc_Ledger')}}</b></th>
              <th align="right" colspan="2"><font size="3"><b>Date Range:&nbsp;{{ date('d/m/Y', strtotime($fromdate)) }} to {{ date('d/m/Y', strtotime($todate)) }}</b><font></th>
              </tr>
          </thead>
          <tr>
            <td align="center"><b>Account Name</td>
            <!-- td align="center" colspan="2"><b>Opening</b></td>
            <td align="center" colspan="2"><b>Transaction</b></td -->
            <td align="center" colspan="2"><b>Closing Balance</b></td>
          </tr>
          <tr>
            <td align="center" width="60%"></td>
            <!-- td align="center" width="13%"><b>Debit</b></td>
            <td align="center" width="13%"><b>Credit</b></td>
            <td align="center" width="13%"><b>Debit</b></td>
            <td align="center" width="13%"><b>Credit</b></td -->
            <td align="center" width="20%"><b>Debit</b></td>
            <td align="center" width="20%"><b>Credit</b></td>
          </tr>
        </thead>
        <tbody>
         <?php
          $total_op_debit = 0;  $total_op_credit = 0;
          $total_bal_debit = 0; $total_bal_credit = 0;
          $total_tr_debit = 0;  $total_tr_credit = 0;
          $i = 0;
         ?>
          @foreach($rows as $row)
          <?php
            /*$op  = $row->op_debit - $row->op_credit;
            $bal = $op + $row->tr_debit -  $row->tr_credit;

            $total_op = $total_op + $op;
            $total_bal  = $total_bal + $bal;

            $total_tr_debit = $total_tr_debit + $row->tr_debit;
            $total_tr_credit = $total_tr_credit + $row->tr_credit; */


            $op  = $row->op_debit - $row->op_credit;
            $bal = $op + $row->tr_debit -  $row->tr_credit;

            if ( $op > 0 ) $total_op_debit = $total_op_debit + $op;
            if ( $op < 0 ) $total_op_credit = $total_op_credit + $op;

            //$total_bal  = $total_bal + $bal;

            if ( $bal > 0 ) $total_bal_debit = $total_bal_debit + $bal;
            if ( $bal < 0 ) $total_bal_credit = $total_bal_credit + $bal;

            $total_tr_debit = $total_tr_debit + $row->tr_debit;
            $total_tr_credit = $total_tr_credit + $row->tr_credit;

         ?>
          <tr>
             <td width="19%">{{ $row->acc_head }}</td>
             <!--td width="13%" align="right">{{ $op > 0 ? number_format($op,2):''}}</td>
             <td width="13%" align="right">{{ $op < 0 ? number_format(abs($op),2):''}}</td>
             <td width="13%" align="right">{{ number_format($row->tr_debit,2)=='0.00'?'':number_format($row->tr_debit,2) }}</td>
             <td width="13%" align="right">{{ number_format($row->tr_credit,2)=='0.00'?'':number_format(abs($row->tr_credit),2) }}</td -->
             <td width="13%" align="right">{{ $bal > 0 ? number_format($bal,2):''}}</td>
             <td width="13%" align="right">{{ $bal < 0 ? number_format(abs($bal),2) :''}}</td>
          </tr>

          @endforeach
          <tr>
            <td align="right" colspan="1"><b>Total:</b></td>
            <!-- td align="right"><b>{{ number_format($total_op_debit,2) }}</b></td>
            <td align="right"><b>{{ number_format(abs($total_op_credit),2) }}</b></td>
            <td align="right"><b>{{ number_format($total_tr_debit,2) }}</b></td>
            <td align="right"><b>{{ number_format(abs($total_tr_credit),2) }}</b></td -->
            <td align="right"><b>{{ number_format($total_bal_debit,2) }}</b></td>
            <td align="right"><b>{{ number_format(abs($total_bal_credit),2) }}</b></td>
          </tr>
          </tbody>
        </table>
        </div>
  </div>

</div>
</section>
</body>
</html>
