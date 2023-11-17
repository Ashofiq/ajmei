<html>
<head>
    <style>
        @page {
            margin-top: 1.5cm;
        }

        footer {
            width: 100%;
            bottom: 5%;
        }

        body {
            font-family: sans-serif;
            font-size: 9pt;
        }

        table.items {
            border: 0.1mm solid #000000;
        }

        td {
            vertical-align: top;
        }

        .items td {
            border-left: 0.1mm solid #000000;
            border-right: 0.1mm solid #000000;
        }

        table thead td {
            background-color: #EEEEEE;
            text-align: center;
            border: 0.1mm solid #000000;
            font-variant: small-caps;
        }

        .items td.blanktotal {
            background-color: #EEEEEE;
            border: 0.1mm solid #000000;
            background-color: #FFFFFF;
            border: 0mm none #000000;
            border-top: 0.1mm solid #000000;
            border-right: 0.1mm solid #000000;
        }

        .items td.totals {
            text-align: right;
            border: 0.1mm solid #000000;
        }

        .items td.cost {
            text-align: center;
        }

        .barcode {
            margin-left: 0px;
            margin-right: -15px;

            vertical-align: middle;
            color: #000000;
            float: right;
        }

        .barcodecell {
            text-align: right;
            vertical-align: middle;
            padding: 0;
            margin-left: 0px;

        }

    </style>

    <style>
        body {
            font-family: "Montserrat", sans-serif;
            font-weight: 400;
            color: #322d28;
        }

        header.top-bar h1 {
            font-family: "Montserrat", sans-serif;
        }

        main {
            margin-top: 0rem;
            min-height: calc(100vh - 107px);
        }

        main .inner-container {
            max-width: 800px;
            margin: 0 auto;
        }

        table.invoice {
            background: #fff;
        }

        table.invoice .num {
            font-weight: 200;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: .8em;
        }

        table.invoice tr, table.invoice td {
            background: #fff;
            text-align: left;
            font-weight: 400;
            color: #322d28;
        }

        table.invoice tr.header td img {
            max-width: 300px;
        }

        table.invoice tr.header td h2 {
            text-align: right;
            font-family: "Montserrat", sans-serif;
            font-weight: 200;
            font-size: 2rem;
            color: #1779ba;
        }

        table.invoice tr.intro td:nth-child(2) {
            text-align: right;
        }

        table.invoice tr.details > td {
            padding-top: 4rem;
            padding-bottom: 0;
        }

        table.invoice tr.details td.id, table.invoice tr.details td.qty, table.invoice tr.details th.id, table.invoice tr.details th.qty {
            text-align: center;
        }

        table.invoice tr.details td:last-child, table.invoice tr.details th:last-child {
            text-align: right;
        }

        table.invoice tr.details table thead, table.invoice tr.details table tbody {
            position: relative;
        }

        table.invoice tr.details table thead:after, table.invoice tr.details table tbody:after {
            content: '';
            height: 1px;
            position: absolute;
            width: 100%;
            left: 0;
            margin-top: -1px;
            background: #c8c3be;
        }

        table.invoice tr.totals td {
            padding-top: 0;
        }

        table.invoice tr.totals table tr td {
            padding-top: 0;
            padding-bottom: 0;
        }

        table.invoice tr.totals table tr td:nth-child(1) {
            font-weight: 500;
        }

        table.invoice tr.totals table tr td:nth-child(2) {
            text-align: right;
            font-weight: 200;
        }

        table.invoice tr.totals table tr:nth-last-child(2) td {
            padding-bottom: .5em;
        }

        table.invoice tr.totals table tr:nth-last-child(2) td:last-child {
            position: relative;
        }

        table.invoice tr.totals table tr:nth-last-child(2) td:last-child:after {
            content: '';
            height: 4px;
            width: 110%;
            border-top: 1px solid #1779ba;
            border-bottom: 1px solid #1779ba;
            position: relative;
            right: 0;
            bottom: -.575rem;
            display: block;
        }

        table.invoice tr.totals table tr.total td {
            font-size: 1.2em;
            padding-top: .5em;
            font-weight: 700;
        }

        table.invoice tr.totals table tr.total td:last-child {
            font-weight: 700;
        }

        .additional-info h5 {
            font-size: .8em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1779ba;
        }

        .myButton {
            background-color: #44c767;
            border-radius: 28px;
            border: 1px solid #18ab29;
            display: inline-block;
            cursor: pointer;
            color: #ffffff;
            font-family: Arial;
            font-size: 17px;
            padding: 16px 31px;
            text-decoration: none;
            text-shadow: 0px 1px 0px #2f6627;
        }

        .myButton:hover {
            background-color: #5cbf2a;
        }

        .myButton:active {
            position: relative;
            top: 1px;
        }


    </style> 
</head>
<body>
<div class="row expanded" style="background-image: url('dist/img/bg.jpg');  background-repeat: no-repeat; background-position: center;">
    <main class="columns">
        <div class="inner-container">

            <section class="" style="#ccc ;padding: 5px">
                <header>
                    <table width="100%">
                        <tr>
                            <td align="left" valign="top" style="width:15%; ">
                                <img src="{{ url('dist/img/grouplogo.png') }}" alt="Ajmeri" style="width:30%;">
                            </td>
                            <td align="center" valign="top" style="width:55%; font-size: 12px;">
                                <p style="font-size: 15px;"><span style="font-size: 20px; text-align: center; font-weight: bold">AJMERI GOLDEN FIBER</span> <br/> Silo Road, Santahar.<br/>Phone : 01780090228, 01780090375<br/>Email:ajmerigoldenfiber@gmail.com  <br/> www.ajmerigroup.com 
                            </td>
                            <td align="right" valign="top" style="width:30%; font-size: 12px;">
                                <img src="{{ url('dist/img/logo.jpg') }}" alt="Ajmeri" style="width:18%;">
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" style="width:25%; "></td>
                            <td align="center" valign="top" style="width:25%; "><p style="font-size: 20px; text-align: center; font-weight: bold">Money Receipt</p>
                            <td valign="top" style="width:25%; "></td>
                        </tr>

                    </table>
                    <br>
                    <br>

                </header>
                 <?php $i = 0; $t_d_amount = 0 ; $t_c_amount=0; $head = ''; $bank = '';   ?>

                 @foreach($rows_d as $row)
                    
                    <?php  
                         if($row->d_amount == 0){
                            $head = $row->acc_head;
                         }else{
                            $bank =  $row->acc_head;
                         }  
                        $t_d_amount = $t_d_amount + $row->d_amount;
                        $t_c_amount = $t_c_amount + $row->c_amount; 
                        
                    ?>
                @endforeach
                
                <div>
                    <table width="100%" style="font-family: serif; margin-top:-10px;; font-size: 25px" cellpadding="10">
                        <tr>
                            <td width="35%">
                                <b>PV. NO:</b> <span>{{ $rows_m[0]->trans_type }}-{{ $rows_m[0]->voucher_no }} </span>
                            </td>

                            <td width="30%">
                            </td>

                            <td width="30%" >
                                <span><b>Date</b>:</span>{{ date('d/m/Y', strtotime($rows_m[0]->voucher_date)) }}
                            </td>
                        </tr>

                        <tr>
                            <td width="35%">
                                <b>Paid To:</b> {{ $head }}
                            </td>

                            <td width="30%">
                            </td>

                            <td width="30%" align="right">
                            </td>
                        </tr>

                        <tr>
                            <td width="100%">
                                <b>Amount of Taka:</b> {{ number_format($t_d_amount, 2) }}
                            </td>
                            <td width="30%"></td>
                            <td width="30%"></td>
                        </tr>

                        <tr>
                            <td width="100%">
                                <b>In Word:</b> {{ Helper::convert_number_to_words($t_d_amount) }} Only.
                            </td>
                            <td width="30%"></td>
                            <td width="30%"></td>
                        </tr>

                        <tr>
                            <td width="35%">
                                <b>By:</b><input type="checkbox" <?php echo ($rows_m[0]->trans_type == 'CP') ? 'checked' : ''; ?> > <b>Cash / </b><input type="checkbox"> <b>Cheque No.</b>
                            </td>

                            <td width="30%">
                                <b>Bank: </b>
                            </td>

                            <td width="30%" >
                                
                            </td>
                        </tr>

                        <tr>
                            <td width="35%">
                                <b>For The Purpose Of: </b> {{ $rows_m[0]->t_narration }}
                            </td>

                            <td width="30%"></td>

                            <td width="30%"></td>
                        </tr>

                    </table>

                    <br><br><br><br>
                    <table width="100%" style="font-family: serif; margin-top:-10px; font-size: 18px" cellpadding="10">
                        <tr>
                
                            <td width="35%">
                                <b style="text-decoration: overline;">Prepear By</b>
                            </td>

                            <td width="30%"></td>

                            <td width="30%" align="right">
                                <b style="text-decoration: overline;">Receive By</b>

                            </td>
                        </tr>
                    </table>

                </div>
              
            </section>
        </div>
    </main>
</div>
</body>
</html>