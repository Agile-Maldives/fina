<body>
   <style>
       .htxt{
           font-size:12px;
           margin-top:4px;
       }
       .logo{
           width:100px;
       }
       .header-right{
           margin-left:40px;
       }
       .notification{
           text-align:center;
       }
       .vessel-info{
           text-align:left;
       }
       .tbl-vessel-info{
           width:100%;
           margin-top:20px;
           margin-bottom:20px;
       }
       .tbl-customer{
           width:100%;
           border-bottom:2px solid black;
       }
       .total-price-container{
           border-top:2px solid black;
           text-align:right;
       }
       .tbl-total-price{
       }
       .title-container{
           text-align:center;
           border-bottom:1px solid black;
       }
       .title{
           color:red;
       }
       .dt{
           font-weight:bold;
           font-size:14px;
       }
       .dd{
           margin-left:5px;
           font-size:14px;
       }
       .pricing-table{
           width:100%;
           margin-top:20px;
           border-collapse:collapse;
           border:1px solid black;
       }
       th{
           background-color:#D3D3D3;
       }
   </style>
   <table>
       <tr>
           <td>
               <img class="logo" src="{{public_path()}}/storage/logos/logo.png" />
           </td>
           <td>
               <div class="header-right">
                <div class="htxt"><b>{{$generalSettings->company_name}}</b></div>
                <div class="htxt">{{$generalSettings->company_address}}</div>
                <div class="htxt">Phone : {{$generalSettings->company_phone_no}}, HotLine {{$generalSettings->company_hotline_no}}  Fax: {{$generalSettings->company_fax_no}}</div>
                <div class="htxt">Email: {{$generalSettings->company_email}}</div>
               </div>
           </td>
       </tr>
   </table>
   <div class="title-container">
       <h3 class="title">{{$title}}</h3>
   </div>
   <table class="pricing-table">
       <tr>
           <th>Account Name</th>
           <th>ID Card</th>
           <th>Borrowing id</th>
           <th>Agreement id</th>
           <th>Currency</th>
           <th>Date Borrowing Start</th>
           <th>Issued amount</th>
           <th>Payment Interval Due Date</th>
           <th>Payment Interval Due amount</th>
       </tr>
       @foreach($loans as $loan)
           <tr>
               <td>{{$loan->account->account_name}}</td>
               <td>{{$loan->account->account_id_card}}</td>
               <td>{{$loan->id}}</td>
               <td>{{$loan->agreement_no}}</td>
               <td>{{$loan->currency}}</td>
               <td>
                   {{date('m-d-Y',strtotime($loan->date_loan_start))}}
               </td>
               <td>{{number_format($loan->loan_amount,2)}}</td>
               <td>
                   {{date('m-d-Y',strtotime($loan->due_date_next_payment))}}
               </td>
               <td>{{number_format($loan->balance_due,2)}}</td>
           </tr>
       @endforeach
   </table>
</body>
