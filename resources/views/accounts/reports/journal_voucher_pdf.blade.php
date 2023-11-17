<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <style>
        body { margin: 0; font-size: 16px; font-family: "Arrial Narrow";}
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

        table, td {
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
<input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
  <div class="container">

    <div class="row justify-content-center">
      <div class="col-md-12">
        <table class="table table-striped table-data table-report">
          <thead>
            <thead>
              <tr><th align="center" colspan="3"><font size="4">{{$comp_name}}</font></th></tr>
              <tr><th align="center" colspan="3"><font size="2">{{$comp_add}}</font></th></tr>
            </thead>
            @foreach($rows_m as $row)
            <tr><th align="left" colspan="3">Voucher No:&nbsp;{{ $row->trans_type }}-{{ $row->voucher_no }}</th></tr>
            <tr><th align="left" colspan="3">Narration:&nbsp;{{ $row->t_narration }}</th></tr>
            <tr><th align="left" colspan="3">Voucher Date:&nbsp;{{ date('m/d/Y', strtotime($row->voucher_date)) }}</th></tr>
            @endforeach
            <tr>
              <td align="center"><b>Accounts Head</b></td>
              <td align="center"><b>Debit</b></td>
              <td align="center"><b>Credit</b></td>
            </tr>
          </thead>
          <tbody>
            <?php $i = 0; $t_d_amount = 0 ; $t_c_amount=0;   ?>
            @foreach($rows_d as $row)
            <tr>
              <td>{{ $row->acc_head }}</td>
              <td align="right">{{number_format($row->d_amount, 2)}}</td>
              <td align="right">{{ number_format($row->c_amount, 2) }}</td>
               <?php   $t_d_amount = $t_d_amount + $row->d_amount;
                $t_c_amount = $t_c_amount + $row->c_amount; ?>
            </tr>
            @endforeach
            <tr>
              <td colspan="1" align="right"><b>Total :</b></td>
                <td colspan="1" align="right"><b>{{ number_format($t_d_amount,2) }}</b></td>
              <td colspan="1" align="right"><b>{{ number_format($t_c_amount,2) }}</b></td>
			     
			      </tr>
          </tbody>
          </table>
          </div>
    </div>

  </div>
  </section>

</body>
</html>
