<body>
  <div id="invoiceholder">
  <div id="invoice" class="effect2">

    <style>
      .table-main tr{
        page-break-before: always;
      }
      td{
          height: 30px;
      }
      .headborder{
     
      }

    </style>

    <htmlpageheader name="page-header" style="padding-top:100px;">
    <div id="invoice-top">
        <div class="title">
          <h1 style="text-align: center;">Ajmeri Golden Fiber<span class="invoiceVal invoice_num"> </span></h1>
          <div style="text-align: center;">Silo Road Shantahar </div>
          <br>
        </div>
      </div>

      <div id="invoice-mid">   
        <div id="message">
          <h3 style="text-align: center;text-decoration: underline;">{{ $sectionData['unitName'] }} <?php if($info->employeeType == 'worker'){ echo 'Labour Wages Sheet'; }else{ echo "Officer Salary Sheet"; } ?>  ( {{ $sectionData['sectionName']  }})</h3>
        </div>
      </div>

      <div style="text-align: left; font-weight: bold">
          <div id="first-div" style="float:left;width:50%">
            Bill time: {{ $from  }} To {{ $to }}
          </div>
          <div id="first-div" style="width:47%; text-align:right">
              Payment Date: <?php $date=date_create($info->paymentDate); ?> {{ date_format($date, 'd/m/Y') }}
          </div>
        </div>
    </htmlpageheader>


    <div id="invoice-bot" >

      
      <div id="table" >

      <?php $i = 1; ?>
        
      @foreach($sectionData['new'] as $section)
      <!-- <h5>Section : {{ $sectionData['sectionName']  }}</h5> -->
        <table class="table-main" style="page-break-inside:always">
          
          <thead>    
              <tr class="tabletitle" >
                <th align="center"><h3> SL NO </h3></th>
                <th align="center"> <h3>Name </h3></th>
                <th align="center"> <h3> Designation </h3></th>
                <th align="center"><h3> Card No </h3></th>
                <th align="center"><h3>  {{ ($info->employeeType == 'worker') ? 'Wages': 'Salary'}}  </h3></th>
                <th align="center"><h3> Attn.  </h3></th>
                <th align="center"><h3>   Absent  </h3></th>
                <th align="center"><h3>  Duty Time </h3></th>
                <th align="center"><h3> Over Time </h3></th>
                <th align="center"><h3> Attn. Bonus </h3></th>
                <th align="center"><h3>  Adv. Deduct</h3></th>
                <th align="center"><h3> Total {{ ($info->employeeType == 'worker') ? 'Wages': 'Salary'}} </h3></th>
                <th align="center"><h3>  Signature  </h3></th>
                <th align="center"><h3>  Remarks  </h3></th>
              </tr>
          </thead>
          <tbody>
          <?php 
            $totalDutyTime = 0;
            $totalOtTime =  0;
            $totalAttnBonus = 0;
            $totalWages =  0; 
          ?>
          
          @foreach($section['empdata'] as $row) 
            <tr class="list-item">
              <td align="center" data-label="" class="tableitem" width="7%">{{ $row['sl'] }}</td>
              <td align="center" data-label="Description" class="tableitem" width="10%">{{ $row['name'] }}</td>
              <td style="font-size: 8px" align="center" data-label="Description" class="tableitem" width="11%">{{ $row['designation'] }}</td>
              <td align="center" data-label="Quantity" class="tableitem" width="6%">{{ $row['cartNo'] }}</td>
              <td align="center" data-label="Unit Price" class="tableitem" width="7%">{{ intval($row['wages']) }}</td>
              <td align="center" data-label="Taxable Amount" class="tableitem" width="5%">{{ $row['attendanceDay'] + $row['previousDays'] }}</td>
              <td align="center" data-label="Tax Code" class="tableitem" width="5%">{{ $row['absenceDay']  }}</td>
              <td align="center" data-label="%" class="tableitem" width="7%">{{ $row['dutyTime'] }}</td>
              <td align="center" data-label="Tax Amount" class="tableitem" width="7%">{{ $row['otTime'] }}</td>
              <td align="center" data-label="AWT" class="tableitem" width="7%">{{ $row['attnBonus'] }}</td>
              <td align="center" data-label="%" class="tableitem" width="7%">{{ $row['adv_deduction'] }}</td>
              <td align="center" data-label="Total" class="tableitem" width="7%"> {{ $row['totalWages'] }} </td>
              <td align="center" data-label="AWT" class="tableitem" width="6%"> </td>
              <td align="center" data-label="Total" class="tableitem" width="7%"> </td>
            </tr>

               
        
            <?php  if(in_array($i, $index)){  ?> 

              <tr class="list-item total-row" style="border: 1px solid black">
                <td data-label="Tax Code" class="tableitem" width="7%" > </td>
                <td data-label="Tax Code" class="tableitem" width="10%" > </td>
                <td data-label="Tax Code" class="tableitem" width="11%" > </td>
                <td data-label="Tax Code" class="tableitem" width="5%" > </td>
                <td data-label="Tax Code" class="tableitem" width="5%" > </td>
                <td data-label="Tax Code" class="tableitem" width="5%" > </td>
                <td data-label="Tax Code" class="tableitem" width="7%"> Total </td>
                <td data-label="%" class="tableitem" width="7%" style="border: 1px solid black"> {{ $section['pageDutyTime'] }} </td>
                <td data-label="Tax Amount" class="tableitem" width="7%" style="border: 1px solid black">{{ $section['pageOtTime'] }} </td>
                <td data-label="AWT" class="tableitem" style="border: 1px solid black" width="7%"> {{ $section['pageAttnBonus'] }} </td>
                <td data-label="Tax Code" class="tableitem" width="7%" > </td>
                <td data-label="Total" class="tableitem" width="7%" style="border: 1px solid black">  {{ $section['pageTotal']  }} </td>
                <td data-label="AWT" class="tableitem" style="border: 1px solid black"> </td>
                <td data-label="Total" class="tableitem">  </td>
              </tr>

            <?php } ?>

           
            @if($i == $totalCount)

              <!-- last page total -->
              @if(!in_array($totalCount, $index))
                <tr class="list-item total-row" style="border: 1px solid black">
                    <td data-label="" class="tableitem" width="7%"></td>
                    <td data-label="Description" class="tableitem" width="10%"> </td>
                    <td data-label="Description" class="tableitem" width="11%"> </td>
                    <td data-label="Quantity" class="tableitem" width="6%"> </td>
                    <td data-label="Unit Price" class="tableitem" width="5%"> </td>
                    <td data-label="Taxable Amount" class="tableitem" width="5%"> </td>
                    <td data-label="Tax Code" class="tableitem" width="7%">Page Total </td>
                    <td data-label="%" class="tableitem" width="7%" style="border: 1px solid black"> {{ $section['pageDutyTime'] }} </td>
                    <td data-label="Tax Amount" class="tableitem" width="7%" style="border: 1px solid black">{{ $section['pageOtTime'] }} </td>
                    <td data-label="AWT" class="tableitem" style="border: 1px solid black" width="7%"> {{ $section['pageAttnBonus'] }} </td>
                    <td data-label="Taxable Amount" class="tableitem" width="7%"> </td>
                    <td data-label="Total" class="tableitem" width="7%" style="border: 1px solid black">  {{ $section['pageTotal']  }} </td>
                    <td data-label="AWT" class="tableitem" style="border: 1px solid black"> </td>
                    <td data-label="Total" class="tableitem">  </td>
                  </tr>
              @endif

              <!-- section total -->
              <tr class="list-item total-row" style="border: 1px solid black">
                <td data-label="" class="tableitem" width="5%"></td>
                <td data-label="Description" class="tableitem" width="10%"> </td>
                <td data-label="Description" class="tableitem" width="11%"> </td>
                <td data-label="Quantity" class="tableitem" width="6%"> </td>
                <td data-label="Unit Price" class="tableitem" width="5%"> </td>
                <td data-label="Taxable Amount" class="tableitem" width="5%"> </td>
                <td data-label="Tax Code" class="tableitem" width="7%">Grand Total </td>
                <td data-label="%" class="tableitem" width="7%" style="border: 1px solid black">{{ $sectionData['totalDutyTime']  }} </td>
                <td data-label="Tax Amount" class="tableitem" width="7%" style="border: 1px solid black">{{ $sectionData['totalOtTime'] }} </td>
                <td data-label="AWT" class="tableitem" style="border: 1px solid black" width="7%">{{ $sectionData['totalAttnBonus'] }}</td>
                <td data-label="Taxable Amount" class="tableitem" width="7%"> </td>
                <td data-label="Total" class="tableitem" width="7%" style="border: 1px solid black">{{ $sectionData['totalWages']  }} </td>
                <td data-label="AWT" class="tableitem" style="border: 1px solid black"> </td>
                <td data-label="Total" class="tableitem">  </td>
              </tr>
              @break
            @endif
            <?php $i++ ?>

          @endforeach
          </tbody>
        </table>
       
          <!-- <pagebreak> -->
      @endforeach
      </div>
      

    <htmlpagefooter name="page-footer" style="">

        <div style="text-align: left; font-weight: bold; padding-bottom: 40px">
          <div id="first-div" style="float:left;width:20%; text-align: center">
            Accountant
          </div>
          <div id="first-div" style="float:left;width:20%; text-align: center ">
          Verifyer
          </div>
          <div id="first-div" style="float:left;width:20%; text-align: center">
            Manager
          </div>
          <div id="first-div" style="float:left;width:20%; text-align: center">
          Factory Manager
          </div>
          <div id="first-div" style="float:right;width:20%;text-align: center ">
          CEO
          </div>
        
        </div>
           
    </htmlpagefooter>
      
      
    </div><!--End InvoiceBot-->
    <footer>
      <div id="legalcopy" class="clearfix">
        <!-- <p class="col-right">Our mailing address is:
            <span class="email"><a href="mailto:supplier.portal@almonature.com">supplier.portal@almonature.com</a></span>
        </p> -->
      </div>
    </footer>
  </div><!--End Invoice-->
</div><!-- End Invoice Holder-->
  
<style>

@page {
  margin-header: 0px;
  margin-top: 205px;
  header: page-header;
  footer: page-footer;
  text-align:center;
  margin: 18% 10mm 10% 5mm;
}

.tableitem{
  text-align:justify;
}


@import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,900,700,500,300,100);
*{
  margin: 0;
  box-sizing: border-box;
  -webkit-print-color-adjust: exact;
}
body{
  font-weight: bold;
  font-family: 'Roboto', sans-serif;
}
::selection {background: #f31544; color: #FFF;}
::moz-selection {background: #f31544; color: #FFF;}
.clearfix::after {
    content: "";
    clear: both;
    display: table;
}
.col-left {
    float: left;
}
.col-right {
    float: right;
}
h1{
  font-size: 1.5em;
  color: #444;
}
h2{font-size: .9em;}
h3{
  font-size: 1.2em;
  font-weight: 300;
  
}
p{
  font-size: .75em;
  color: #666;
  line-height: 1.2em;
}
a {
    text-decoration: none;
    color: #00a63f;
}

#invoiceholder{
  width:100%;
  height: 100%;
}
#invoice{
  position: relative;
  margin: 0 auto;
  background: #FFF;
}

[id*='invoice-']{ /* Targets all id with 'col-' */
/*  border-bottom: 1px solid #EEE;*/
  padding: 20px;
}

#invoice-top{border-bottom: 2px solid #00a63f;}
#invoice-mid{min-height: 110px;}
#invoice-bot{ min-height: 240px;}

.logo{
    display: inline-block;
    vertical-align: middle;
	width: 110px;
    overflow: hidden;
}
.info{
    display: inline-block;
    vertical-align: middle;
    margin-left: 20px;
}
.logo img,
.clientlogo img {
    width: 100%;
}
.clientlogo{
    display: inline-block;
    vertical-align: middle;
	width: 50px;
}
.clientinfo {
    display: inline-block;
    vertical-align: middle;
    margin-left: 20px
}
.title{
  float: right;
}
.title p{text-align: right;}
#message{margin-bottom: 30px; display: block;}
h2 {
    margin-bottom: 5px;
    color: #444;
}
.col-right td {
    color: #666;
    padding: 5px 8px;
    border: 0;
    font-size: 0.75em;
    border-bottom: 1px solid #eeeeee;
}
.col-right td label {
    margin-left: 5px;
    font-weight: 600;
    color: #444;
}
.cta-group a {
    display: inline-block;
    padding: 7px;
    border-radius: 4px;
    background: rgb(196, 57, 10);
    margin-right: 10px;
    min-width: 100px;
    text-align: center;
    color: #fff;
    font-size: 0.75em;
}
.cta-group .btn-primary {
    background: #00a63f;
}
.cta-group.mobile-btn-group {
    display: none;
}
table{
  width: 100%;
  border-collapse: collapse;
}

th{
   
}

td{
    border: 1px solid black;
    padding: 10px;
    /* border-bottom: 1px solid #cccaca; */
    font-size: 0.70em;
    text-align: center;
}

.total-row td{
  text-align: center;
}

.tabletitle th {
     border: 1px solid black  !important;
    text-align: center;
    font-weight: bold;
    color: black;
    font-size: 12px;
  
     height:18px !important;
    

  
}


.tabletitle th:nth-child(2) {
    text-align: left;
}

.item{width: 50%;}
.list-item td {
    text-align: right;
}
.list-item td:nth-child(2) {
    text-align: left;
}
.total-row th,
.total-row td {
    text-align: center;
    font-weight: 700;
    font-size: .75em;
    border: 0 none;
}
.table-main {
    
}
footer {
    border-top: 1px solid #eeeeee;;
    padding: 15px 20px;
}
.effect2
{
  position: relative;
}
.effect2:before, .effect2:after
{
  z-index: -1;
  position: absolute;
  content: "";
  bottom: 15px;
  left: 10px;
  width: 50%;
  top: 80%;
  max-width:300px;
  background: #777;
  -webkit-box-shadow: 0 15px 10px #777;
  -moz-box-shadow: 0 15px 10px #777;
  box-shadow: 0 15px 10px #777;
  -webkit-transform: rotate(-3deg);
  -moz-transform: rotate(-3deg);
  -o-transform: rotate(-3deg);
  -ms-transform: rotate(-3deg);
  transform: rotate(-3deg);
}
.effect2:after
{
  -webkit-transform: rotate(3deg);
  -moz-transform: rotate(3deg);
  -o-transform: rotate(3deg);
  -ms-transform: rotate(3deg);
  transform: rotate(3deg);
  right: 10px;
  left: auto;
}
@media screen and (max-width: 767px) {
    h1 {
        font-size: .9em;
    }
    #invoice {
        width: 100%;
    }
    #message {
        margin-bottom: 20px;
    }
    [id*='invoice-'] {
        padding: 20px 10px;
    }
    .logo {
        width: 140px;
    }
    .title {
        float: none;
        display: inline-block;
        vertical-align: middle;
        margin-left: 40px;
    }
    .title p {
        text-align: left;
    }
    .col-left,
    .col-right {
        width: 100%;
    }
    .table {
        margin-top: 20px;
    }
    #table {
        white-space: nowrap;
        overflow: auto;
    }
    td {
        white-space: normal;
    }
    .cta-group {
        text-align: center;
    }
    .cta-group.mobile-btn-group {
        display: block;
        margin-bottom: 20px;
    }
     /*==================== Table ====================*/
    .table-main {
        border: 0 none;
    }  
      .table-main thead {
        border: none;
        clip: rect(0 0 0 0);
        height: 1px;
        margin: -1px;
        overflow: hidden;
        padding: 0;
        position: absolute;
        width: 1px;
      }
      .table-main tr {
        border-bottom: 2px solid #eee;
        display: block;
        margin-bottom: 20px;
      }
      .table-main td {
        font-weight: 700;
        display: block;
        padding-left: 40%;
        max-width: none;
        position: relative;
        border: 1px solid #cccaca;
        text-align: left;
      }
      .table-main td:before {
        /*
        * aria-label has no advantage, it won't be read inside a table
        content: attr(aria-label);
        */
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: normal;
        text-transform: uppercase;
      }
    .total-row th {
        display: none;
    }
    .total-row td {
        text-align: left;
    }
    footer {text-align: center;}
}

</style>
  

</body>