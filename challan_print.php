<?php
echo'
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>'.moduleName(false).'</title>
    <style type="text/css">
        body {overflow: -moz-scrollbars-vertical; margin:0; font-family: Arial, Helvetica, sans-serif, Calibri, "Calibri Light";  }
        @media all {
            .page-break	{ display: none; }
        }

        @media print {
            .page-break	{ display: block; page-break-before: always; }
            @page { 
                size: A4 landscape;
            margin: 4mm 4mm 4mm 4mm; 
            }
        }
        h1 { text-align:left; margin:0; margin-top:0; margin-bottom:0px; font-size:18px; font-weight:700; text-transform:uppercase; }
        .spanh1 { font-size:14px; font-weight:normal; text-transform:none; float:right; margin-top:5px; }
        h2 { text-align:left; margin:0; margin-top:0; margin-bottom:1px; font-size:18px; font-weight:700; text-transform:uppercase; }
        .spanh2 { font-size:16px; font-weight:700; text-transform:none; }
        h3 { text-align:center; margin:0; margin-top:0; margin-bottom:1px; font-size:18px; font-weight:700; text-transform:uppercase; }
        h4 { 
            text-align:center; margin:0; margin-bottom:1px; font-weight:normal; font-size:15px; font-weight:700; word-spacing:0.1em;  
        }
        td { padding-bottom:4px; font-family: Arial, Helvetica, sans-serif, Calibri, "Calibri Light"; }
        .line1 { border:1px solid #333; width:100%; margin-top:2px; margin-bottom:5px; }
        .payable { border:2px solid #000; padding:2px; text-align:center; font-size:14px; }

        .paid:after
        {
            content:"PAID";
            
            position:absolute;
            top:30%;
            left:20%;
            z-index:1;
            font-family:Arial,sans-serif;
            -webkit-transform: rotate(-5deg); /* Safari */
            -moz-transform: rotate(-5deg); /* Firefox */
            -ms-transform: rotate(-5deg); /* IE */
            -o-transform: rotate(-5deg); /* Opera */
            transform: rotate(-5deg);
            font-size:250px;
            color:green;
            background:#fff;
            border:solid 4px yellow;
            padding:5px;
            border-radius:5px;
            zoom:1;
            filter:alpha(opacity=50);
            opacity:0.1;
            -webkit-text-shadow: 0 0 2px #c00;
            text-shadow: 0 0 2px #c00;
            box-shadow: 0 0 2px #c00;
        }
    </style>
    <link rel="shortcut icon" type="image/x-icon" href="https://localhost/GPT/dodl.mul.edu.pk/assets/img/favicon.ico"/>
</head>';
if(!empty(ZONE)){
    $condition = array(
                        'select'       =>  'ch.*, s.std_name'
                        ,'join'         =>  'INNER JOIN '.STUDENTS.' s ON s.std_id = ch.id_std AND s.is_deleted = 0'
                        ,'where'        =>  array(
                                                    'ch.is_deleted'  => 0
                                                    ,'ch.challan_no'  => cleanvars(ZONE)
                                                )
                        ,'return_type'  =>  'single'
    );
    $feercord = $dblms->getRows(CHALLANS.' ch', $condition, $sql);

    $condition = array (
                            'select'       =>	'ec.secs_id ,ec.id_curs, ec.id_mas, ec.id_ad_prg ,c.curs_name ,m.mas_name ,p.prg_name, aoc.admoff_amount as curs_amount, aom.admoff_amount as mas_amount, aop.admoff_amount as prg_amount'
                            ,'join'         =>	'LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = ec.id_ad_prg
                                                LEFT JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg
                                                LEFT JOIN '.MASTER_TRACK.' m ON m.mas_id = ec.id_mas
                                                LEFT JOIN '.COURSES.' c ON c.curs_id = ec.id_curs
                                                LEFT JOIN '.ADMISSION_OFFERING.' aop ON aop.admoff_degree = ap.id AND aop.admoff_type = 1
                                                LEFT JOIN '.ADMISSION_OFFERING.' aom ON aom.admoff_degree = m.mas_id AND aom.admoff_type = 2
                                                LEFT JOIN '.ADMISSION_OFFERING.' aoc ON aoc.admoff_degree = c.curs_id AND aoc.admoff_type = 3' 
                            ,'where'        =>	array( 
                                                    'ec.id_std'  => cleanvars($feercord['id_std']) 
                                                    ) 
                            ,'search_by'    =>  ' AND ec.secs_id IN ('.$feercord['id_enroll'].')'
                            ,'return_type'	=>	'all'
                        ); 
    $ENROLLED_COURSES = $dblms->getRows(ENROLLED_COURSES.' ec',$condition, $sql);
    echo'
    <body>
        <table width="99%" border="0" class="page " cellpadding="5" cellspacing="10" align="center" style="border-collapse:collapse; margin-top:0px;">
            <tr>';
                if($feercord['status'] == 1) { 
                    $clspaid = " paid";
                } else { 
                    $clspaid = "";
                }
                $cpi = 0;
                
                for($ifee = 1; $ifee<=3; $ifee++) { 
                    if($ifee<3) { 
                        $rightborder = 'style="border-right:1px dashed #333;"';
                    } else { 
                        $rightborder = '';
                    }
                    $cpi++;
                    
                    if($cpi==1) { 
                        $copyfor = 'Bank';
                    } else if($cpi==2) { 
                        $copyfor = 'Account';
                    }else if($cpi==3) { 
                        $copyfor = "Student's";
                    }
                    echo'
                    <td width="341" valign="top" '.$rightborder.' class="'.$clspaid.'">
                        <h1>ALFALAH/HBL <span class="spanh1">'.$copyfor.'</span></h1>
                        <h2>Minhaj University <span class="spanh2">Lahore</span></h2>
                        <h4 style="font-size:10px; text-align:left;">Alfalah Transact (MUL)<span style="padding-right:5px;"></span>
                            <span style="float:right;">HBL(0042-79015260-03)</span> 
                        </h4>
                        <div style="font-size:11px; margin-top:5px;">
                            <table style="border-collapse:collapse; border:2px solid black;" cellpadding="2" cellspacing="2" border="1" width="100%">
                                <tr>
                                    <td style="text-align: center; font-size:12px; font-weight:bold;">
                                        <img style="padding-top:5px;width: 40px;height: 40px;" src="'.SITE_URL.'assets/img/1LINK_logo.png" alt="1 Link">
                                    </td>
                                    <td style="padding-left:10px;text-align:left; font-size:12px; font-weight:bold;">
                                        All Mobile Banking Payments: <br>
                                        1 Bill Invoice ID: 100001400120220604023
                                    </td>
                                </tr>
                            <table>
                        </div>
                        <div style="font-size:12px; margin-top:5px;">
                            <table style="border-collapse:collapse;" width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="text-align:left; width:75px;">Challan #:</td>
                                    <td style="text-align:left; text-decoration:underline;">'.$feercord['challan_no'].'</td>
                                    <td style="text-align:left; width:70px;">Issue Date:</td>
                                    <td style="text-align:left; text-decoration:underline;">'.$feercord['issue_date'].'</td>
                                </tr>
                                <tr>
                                    <td style="text-align:left;">Name:</td>
                                    <td style="text-align:left; text-decoration:underline;">'.$feercord['std_name'].'</td>
                                    <td style="text-align:left;">Due Date:</td>
                                    <td style=" text-align:left; text-decoration:underline;">'.$feercord['due_date'].'</td>	
                                </tr>
                            </table>
                        </div>
                        <div style="font-size:11px; margin-top:5px;">
                            <table style="border-collapse:collapse; border:1px solid #666;" cellpadding="2" cellspacing="2" border="1" width="100%">
                                <tr>
                                    <td style="font-size:12px; font-weight:bold;">Title</td>
                                    <td style="font-size:12px; font-weight:bold;">Type</td>
                                    <td style="text-align:right; font-size:12px; font-weight:bold;">Rs.</td>
                                </tr>';
                                $totalAmount = 0;
                                foreach ($ENROLLED_COURSES as $key => $value) {
                                    if($value['prg_name']){
                                        $type = '1';
                                        $name = $value['prg_name'];
                                        $amount = $value['prg_amount'];
                                    }elseif($value['mas_name']){
                                        $type = '2';
                                        $name = $value['mas_name'];
                                        $amount = $value['mas_amount'];
                                    }elseif($value['curs_name']){
                                        $type = '3';
                                        $name = $value['curs_name'];   
                                        $amount = $value['curs_amount'];                        
                                    }
                                    $totalAmount += $amount;
                                    echo'                                
                                    <tr>
                                        <td>'.$name.'</td>
                                        <td>'.get_enroll_type($type).'</td>
                                        <td style="text-align:right; width:45%;">'.$amount.'</td>
                                    </tr>';
                                }
                                echo'
                                <tr>
                                    <td colspan="2" style="text-align:right; font-size:12px; font-weight:bold; border:2px solid #666;">Payable Before Due Date</td>
                                    <td style="text-align:right; font-size:12px; font-weight:bold;  border:2px solid #333;">'.$totalAmount.'</td>
                                </tr>
                            </table>
                            <span style="font-size:9px; float:right; margin-top:3px;">Printed Date: '.date('Y-m-d').'</span>
                        </div>
                        <div style="clear:both;"></div>
                        <div style="font-size:13px; color:#000; margin-top:10px;">
                            <table width="100%" border="0" style="border-collapse:collapse;" cellpadding="0" cellspacing="5">
                                <tr>
                                    <td style="font-weight:normal; font-style:italic; text-align:left; font-size:11px; width:80%;">Rupees in word: <span style="text-decoration:underline; font-size:9px; color:#000;">'.convert_number_to_words($totalAmount).'</span></td>
                                    <td style="font-weight:normal; font-style:italic; text-align:right;">Cashier</td>
                                </tr>
                            </table>
                        </div>
                        <div style="clear:both;"></div>
                        <div style="font-size:10px; color:#333; margin-top:5px;">
                            <b><u>Note:</u></b><br>1. Only Cash & Cheque/Payorder will be accepted.<br>
                            2. After Due Date student will pay PKR 300/, after 5 days of due date challan will not be accepted.<br>
                            3. The additional amount collected after the due date will be used for need based scholarship purposes.
                        </div>
                    </td>';
                }
                echo'
            </tr>
        </table>
    </body>
    <script type="text/javascript" language="javascript1.2">
    if (typeof(window.print) != "undefined") {
        window.print();
    }
    </script>
    </html>';
}else{
  $varx = 'home';
  echo'<script>window.location.href = "'.SITE_URL.'student/challans";</script>';
}
?>