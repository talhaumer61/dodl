<?php
echo'
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>'.moduleName(CONTROLER).'</title>
    <link rel="shortcut icon" type="image/x-icon" href="'.SITE_URL.'/assets/img/favicon.ico"/>    
    <script src="'.SITE_URL.'assets/js/qrcode.min.js"></script>
</head>
<style type="text/css">
    body {overflow: -moz-scrollbars-vertical; margin:0; font-family: Arial, Helvetica, sans-serif, Calibri, "Calibri Light";  }
    @media all {
        .page-break	{ display: none; }
    }
    @page {
        size: A4 landscape;
        margin: 0;
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
    
    .btn-print {
        background: #159f46;
        border: 1px solid #159f46;
        border-radius: 50px;
        padding: 10px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        margin: 0.25rem 1rem;
        cursor: pointer;
    }

    .btn-back {
        background: #FF5364;
        border: 1px solid #FF5364;
        border-radius: 50px;
        padding: 10px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        margin: 0.25rem 1rem;
        cursor: pointer;
    }
</style>';
if(!empty(ZONE)){
    $conditions = array ( 
                             'select'       =>	'f.feedback_id'
                            ,'where' 		=>	array( 
                                                         'f.id_std'         => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                        ,'f.id_enroll'      => cleanvars(ZONE) 
                                                    )
                            ,'return_type'	=>	'count'
                        );
    $countFeedback = $dblms->getRows(STUDENT_FEEDBACK.' f', $conditions);
    if($countFeedback){
        $conditions = array ( 
                                 'select'       =>	'gc.cert_id, gc.cert_no, gc.id_enroll, gc.id_std, gc.std_name, gc.cert_type, gc.cert_name, gc.curs_hours, gc.date_generated, gc.emply_name, gc.emply_specialization'
                                ,'where' 		=>	array( 
                                                             'gc.is_deleted'    => '0'
                                                            ,'gc.id_std' 	    => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                            ,'gc.id_enroll'     => cleanvars(ZONE) 
                                                        )
                                ,'return_type'	=>	'count'
                            );
        $count = $dblms->getRows(GENERATED_CERTIFICATES.' gc', $conditions);
        if($count){
            $conditions['return_type'] = 'single';
            $row = $dblms->getRows(GENERATED_CERTIFICATES.' gc', $conditions);
            
            $percent                = 100;
            $id_std                 = $row['id_std'];
            $id_enroll              = $row['id_enroll'];
            $latestID               = $row['cert_id'];
            $cert_type              = $row['cert_type'];
            $cert_name              = $row['cert_name'];
            $curs_hours             = $row['curs_hours'];
            $std_name               = $row['std_name'];
            $dated                  = $row['date_generated'];
            $cert_no                = $row['cert_no'];
            $emply_name             = $row['emply_name'];
            $emply_specialization   = $row['emply_specialization'];
        } else {
            $conditions = array ( 
                                    'select'       =>	'ec.secs_id, ec.id_type, ec.id_std, ec.id_curs, ec.id_mas, ec.id_ad_prg, s.std_name, c.curs_id, c.curs_name, c.curs_hours, m.mas_id, m.mas_name, ap.id, ap.program
                                                        ,e.emply_name, e.emply_specialization
                                                        ,COUNT(DISTINCT cl.lesson_id) as lesson_count
                                                        ,COUNT(DISTINCT ca.id) as assignment_count
                                                        ,COUNT(DISTINCT cq.quiz_id) as quiz_count
                                                        ,COUNT(DISTINCT lt.track_id) as track_count'
                                    ,'join'         =>	'INNER JOIN '.STUDENTS.' s ON s.std_id = ec.id_std AND s.is_deleted = 0 AND s.std_status = 1
                                                        LEFT JOIN '.ALLOCATE_TEACHERS.' alte ON alte.id_curs = ec.id_curs
                                                        LEFT JOIN '.EMPLOYEES.' e ON e.emply_id = alte.id_teacher AND e.emply_status = 1 AND e.is_deleted = 0
                                                        LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = ec.id_ad_prg
                                                        LEFT JOIN '.MASTER_TRACK.' m ON m.mas_id = ec.id_mas
                                                        LEFT JOIN '.COURSES.' c ON c.curs_id = ec.id_curs
                                                        LEFT JOIN '.COURSES_LESSONS.' cl ON FIND_IN_SET(cl.id_curs, ec.id_curs) AND cl.lesson_status = 1 AND cl.is_deleted = 0
                                                        LEFT JOIN '.COURSES_ASSIGNMENTS.' ca ON FIND_IN_SET(ca.id_curs, ec.id_curs) AND ca.status = 1 AND ca.is_deleted = 0
                                                        LEFT JOIN '.QUIZ.' cq ON FIND_IN_SET(cq.id_curs, ec.id_curs) AND cq.quiz_status = 1 AND cq.is_deleted = 0 AND cq.is_publish = 1
                                                        LEFT JOIN '.LECTURE_TRACKING.' lt ON FIND_IN_SET(lt.id_curs, ec.id_curs) AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.is_completed = 2 AND lt.is_deleted = 0 AND lt.id_mas = ec.id_mas AND lt.id_ad_prg = ec.id_ad_prg'
                                    ,'where' 		=>	array( 
                                                                'ec.is_deleted'    => '0'
                                                                ,'ec.secs_status'   => '1'
                                                                ,'ec.id_std' 	    => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                                ,'ec.secs_id' 	    => cleanvars(ZONE) 
                                                            )
                                    ,'return_type'	=>	'count'
                                );
            $count = $dblms->getRows(ENROLLED_COURSES.' ec', $conditions);
            if($count){
                $conditions['return_type'] = 'single';
                $row = $dblms->getRows(ENROLLED_COURSES.' ec', $conditions);
                
                // CERT NO
                $year = date('y');
                $sqlQuery = $dblms->querylms("SELECT 
                                                IFNULL(
                                                    CONCAT(
                                                        'DODL-', $year, '-', 
                                                        LPAD(
                                                            CAST(SUBSTRING_INDEX(MAX(cert_no), '-', -1) AS UNSIGNED) + 1, 
                                                            5, '0'
                                                        )
                                                    ), 
                                                    CONCAT('DODL-', $year, '-00001')
                                                ) AS new_cert_no
                                                FROM ".GENERATED_CERTIFICATES."
                                                WHERE cert_no LIKE CONCAT('DODL-', $year, '-%')
                                            ");
                $valQuery = mysqli_fetch_array($sqlQuery);
                
                // PERCENTAGE
                $Total      = $row['lesson_count']+$row['assignment_count']+$row['quiz_count'];
                $Obtain     = $row['track_count'];
                $percent    = (($Obtain/$Total)*100);
                $percent    = ($percent >= '100' ? '100' : $percent);
                
                // CERT TYPE & NAME
                if($row['id_type'] == 1){
                    $cert_name  = $row['program'];
                }elseif($row['id_type'] == 2){
                    $cert_name  = $row['mas_name'];
                }elseif($row['id_type'] == 3 || $row['id_type'] == 4){
                    $cert_name  = $row['curs_name'];
                    $curs_hours = $row['curs_hours'];
                }

                // CERT_TYPE, STD_NAME & DATE_GENERATED
                $cert_type              = $row['id_type'];
                $std_name               = $row['std_name'];
                $emply_name             = $row['emply_name'];
                $emply_specialization   = $row['emply_specialization'];
                $dated                  = date('Y-m-d G:i:s');
                $cert_no                = $valQuery['new_cert_no'];

                // INSERT IN GENERATED CERTIFICATES
                $values = array(
                                     'id_enroll'            => cleanvars($row['secs_id'])
                                    ,'id_std'			    => cleanvars($_SESSION['userlogininfo']['STDID'])
                                    ,'std_name'			    => cleanvars($std_name)
                                    ,'cert_type'            => cleanvars($cert_type)
                                    ,'cert_name'            => cleanvars($cert_name)
                                    ,'curs_hours'           => cleanvars($curs_hours)
                                    ,'cert_no'              => cleanvars($cert_no)
                                    ,'emply_name'           => cleanvars($emply_name)
                                    ,'emply_specialization' => cleanvars($emply_specialization)
                                    ,'date_generated'       => date('Y-m-d G:i:s')
                                    ,'id_added'             => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                    ,'date_added'           => date('Y-m-d G:i:s')
                                );     
                $sqlCert = $dblms->insert(GENERATED_CERTIFICATES, $values);
                if($sqlCert){   
                    $latestID = $dblms->lastestid();           
                    sendRemark('Certificate Generated', '1', $latestID);
                }                
                $id_std     = $row['id_std'];
                $id_enroll  = $row['secs_id'];
            }
        }
        if($row){
            if($percent == '100'){
                if($cert_type == 3){
                    echo'
                    <style type="text/css">
                        @media print {
                            .page-break	{ display: block; page-break-before: always; }
                            @page {
                                size: A4 landscape;
                                margin: 0;
                            }
                            body {
                                -webkit-print-color-adjust: exact; /* For Chrome */
                                background-image: url("'.SITE_URL.'assets/img/certificate.jpg");
                                background-size: cover;
                            }
                        }
                        
                        #main_table {
                            background-image: url("'.SITE_URL.'assets/img/certificate.jpg");
                            background-size: cover;
                            background-position: center;
                            height: 8.27in;
                            width: 11.69in;
                            border-collapse: collapse;
                        }
                    </style>
                    <body>
                        <center id="hide_on_download">
                            <button class="btn-print" onclick="printAndRedirect()">Download Certificate</button>
                            <a href="'.SITE_URL.'student/courses" class="btn-back">Go Back</a>
                        </center>
                        <table id="main_table" width="99%" border="0" class="page " cellpadding="5" cellspacing="10" align="center" style="border-collapse:collapse; margin-top:0px;">
                            <tr>
                                <td width="341" valign="top">
                                    <div>
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 17rem;">
                                            <tr>
                                                <td width="200" style="font-size:16px; font-weight:bold; text-align: center">'.date('M d, Y', strtotime($dated)).'</td>
                                                <td></td>
                                                <td width="200" style="font-size:16px; font-weight:bold; text-align: center">'.$cert_no.'</td>
                                            </tr>
                                        </table>
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 2rem;">
                                            <tr>
                                                <td style="font-size:30px; font-weight:bold; text-align: center">'.$std_name.'</td>
                                            </tr>
                                        </table>
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 3rem;">
                                            <tr>
                                                <td style="font-size:22px; font-weight:bold; text-align: center">'.html_entity_decode(html_entity_decode($cert_name)).' ('.get_enroll_type($cert_type).')</td>
                                            </tr>
                                        </table>
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 0.3rem;">
                                            <tr>
                                                <td></td>
                                                <td width="270" style="font-size:20px; font-weight:bold; text-align: right">'.$curs_hours.' Hours</td>
                                                <td></td>
                                            </tr>
                                        </table>                                    
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 11rem;">
                                            <tr>
                                                <td></td>
                                                <td width="100">';
                                                    $dataQR = array(
                                                        'cert_id'       => $latestID,
                                                        'id_std'        => $id_std,
                                                        'id_enroll'     => $id_enroll,
                                                    );
                                                    $dataJSON = get_dataHashingOnlyExp(json_encode($dataQR), true);
                                                    echo'
                                                    <div id="qrcode"></div>
                                                    <script>
                                                        const qrcode = new QRCode(document.getElementById(\'qrcode\'), {
                                                            text: \''.SITE_URL.'verify-certificate/'.$dataJSON.'\',
                                                            width: 90,
                                                            height: 90,
                                                            colorDark: "#000",
                                                            colorLight: "#fff",
                                                            correctLevel: QRCode.CorrectLevel.H
                                                        });
                                                    </script>';
                                                    echo'                                            
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div style="clear:both;"></div>
                                </td>
                            </tr>
                        </table>
                    </body>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
                    <script type="text/javascript">
                        function printAndRedirect() {
                            document.querySelector("#hide_on_download").style.display = "none"; // Hide print button
                            
                            const element = document.body; // Get the entire body of the page

                            // Options for html2pdf.js
                            const opt = {
                                margin:       0,
                                filename:     "'.to_seo_url($cert_no.'-'.$cert_name.'-'.$std_name).'.pdf",
                                image:        { type: "jpeg", quality: 0.98 },
                                html2canvas:  { dpi: 192, letterRendering: true, backgroundColor: "#ffffff" },
                                jsPDF:        { unit: "mm", format: "a4", orientation: "landscape" }
                            };

                            // Convert the entire body of the page into a PDF and download it
                            html2pdf().from(element).set(opt).save();
                            // window.location.href = "'.SITE_URL.'student/courses"; 
                        }
                    </script>
                    </html>';
                } else if ($cert_type == 4) {
                    echo'
                    <style type="text/css">
                        @media print {
                            .page-break	{ display: block; page-break-before: always; }
                            @page {
                                size: A4 landscape;
                                margin: 0;
                            }
                            body {
                                -webkit-print-color-adjust: exact; /* For Chrome */
                                background-image: url("'.SITE_URL.'assets/img/training-certificate.jpg");
                                background-size: cover;
                            }
                        }
                        
                        #main_table {
                            background-image: url("'.SITE_URL.'assets/img/training-certificate.jpg");
                            background-size: cover;
                            background-position: center;
                            height: 8.27in;
                            width: 11.69in;
                            border-collapse: collapse;
                        }
                    </style>
                    <body>
                        <center id="hide_on_download">
                            <button class="btn-print" onclick="printAndRedirect()">Download Certificate</button>
                            <a href="'.SITE_URL.'student/trainings" class="btn-back">Go Back</a>
                        </center>
                        <table id="main_table" width="99%" border="0" class="page " cellpadding="5" cellspacing="10" align="center" style="border-collapse:collapse; margin-top:0px;">
                            <tr>
                                <td width="341" valign="top">
                                    <div>
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 9.5rem;">
                                            <tr>
                                                <td width="200" style="font-size:16px; font-weight:bold; text-align: center">'.date('M d, Y', strtotime($dated)).'</td>
                                                <td></td>
                                                <td width="200" style="font-size:16px; font-weight:bold; text-align: center">'.$cert_no.'</td>
                                            </tr>
                                        </table>
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 7rem;">
                                            <tr>
                                                <td style="font-size:24px; font-weight:bold; text-align: left" width="12%"></td>
                                                <td style="font-size:24px; font-weight:bold; text-align: center">'.$std_name.'</td>
                                                <td style="font-size:24px; font-weight:bold; text-align: left" width="60%"></td>
                                            </tr>
                                        </table>
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 2.7rem;">
                                            <tr>
                                                <td style="font-size:24px; font-weight:bold; text-align: center">'.html_entity_decode(html_entity_decode($cert_name)).'</td>
                                            </tr>
                                        </table>
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 3.6rem;">
                                            <tr>
                                                <td></td>
                                                <td width="270" style="font-size:20px; font-weight:bold; text-align: right">'.$curs_hours.' '.($curs_hours>1 ? 'Hours' : 'Hour').'</td>
                                                <td></td>
                                            </tr>
                                        </table>
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 0.2rem;">
                                            <tr>
                                                <td width="550"></td>
                                                <td width="270" style="font-size:20px; font-weight:bold; text-align: left">'.$emply_name.' '.($emply_specialization != '' ? '('.$emply_specialization.')' : '').'</td>
                                                <td></td>
                                            </tr>
                                        </table>
                                        <table cellpadding="2" cellspacing="2" width="100%" style="margin-top: 9rem;">
                                            <tr>
                                                <td></td>
                                                <td width="100">';
                                                    $dataQR = array(
                                                        'cert_id'       => $latestID,
                                                        'id_std'        => $id_std,
                                                        'id_enroll'     => $id_enroll,
                                                    );
                                                    $dataJSON = get_dataHashingOnlyExp(json_encode($dataQR), true);
                                                    echo'
                                                    <div id="qrcode"></div>
                                                    <script>
                                                        const qrcode = new QRCode(document.getElementById(\'qrcode\'), {
                                                            text: \''.SITE_URL.'verify-certificate/'.$dataJSON.'\',
                                                            width: 90,
                                                            height: 90,
                                                            colorDark: "#000",
                                                            colorLight: "#fff",
                                                            correctLevel: QRCode.CorrectLevel.H
                                                        });
                                                    </script>';
                                                    echo'                                            
                                                </td>
                                                <td></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div style="clear:both;"></div>
                                </td>
                            </tr>
                        </table>
                    </body>
                    
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
                    <script type="text/javascript">
                        function printAndRedirect() {
                            document.querySelector("#hide_on_download").style.display = "none"; // Hide print button
                            
                            const element = document.body; // Get the entire body of the page

                            // Options for html2pdf.js
                            const opt = {
                                margin:       0,
                                filename:     "'.to_seo_url($cert_no.'-'.$cert_name.'-'.$std_name).'.pdf",
                                image:        { type: "jpeg", quality: 0.98 },
                                html2canvas:  { dpi: 192, letterRendering: true, backgroundColor: "#ffffff" },
                                jsPDF:        { unit: "mm", format: "a4", orientation: "landscape" }
                            };

                            // Convert the entire body of the page into a PDF and download it
                            html2pdf().from(element).set(opt).save();
                            // window.location.href = "'.SITE_URL.'student/trainings"; 
                        }
                    </script>
                    </html>';
                }
            } else {
                echo'<center><h3 style="margin-top: 15rem; color: red;">100% Completion is necessary for Certificate.</h3></center>';
            }
        } else {
            echo'<center><h3 style="margin-top: 15rem; color: red;">Record Not Found...!</h3></center>';
        }
    } else {
        $varx = 'feedback';
        echo'<script>window.location.href = "'.SITE_URL.'feedback/'.ZONE.'";</script>';
    }
} else {
  $varx = 'home';
  echo'<script>window.location.href = "'.SITE_URL.'dashboard";</script>';
}
?>