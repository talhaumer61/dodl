<?php
// PAGE & LIMIT
$page   = (isset($_POST['page'])) ? $_POST['page'] : 1;
$limit  = (isset($_POST['limit'])) ? $_POST['limit'] : 10;

// COUNT
$condition['return_type'] = "count";
$count = $dblms->getRows($table, $condition);

// VARS
$page       = (int)$page;
$start      = ($page-1)*$limit;
$end        = $start+$limit;
$next       = $page+1;
$next       = ($end>=$count) ? $next-1 : $next;
$prev       = $page-1;
$prev       = ($prev < 1) ? 1 : $prev;

// FETCH RECORD
$condition['limit'] = " $start,$limit";
$condition['return_type'] = "all";
$values = $dblms->getRows($table, $condition);

// SR NO
$srno = ($page == 1 ? 0: ($page-1)*$limit);

function main_pg($count,$limit,$page,$next,$prev,$srno){
    $value = '';
    // if ($count > $limit) {
    if ($count) {
        $pagination_count = $count;
        $total_pagionations = ceil($count/$limit);
        $dots = false;
        $value .='
        <div class="d-flex">
            <div class="col">
                <div class="justify-content-start mb-0 mt-3">
                    Showing <b>'.($srno + 1).'</b> to <b>'.($page == $total_pagionations ? $count : ($page * $limit)).'</b> of <b>'.$count.'</b> entries
                </div>
            </div>
            <div class="col">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end mb-0 mt-3">
                        <li class="page-item">
                            <a class="page-link" onclick="'.($page!=1 ? 'call_ajax('.$prev.')': '').'" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>';
                        for ($pagination_no = 1;$pagination_count > 0; $pagination_no++) {
                            if(($pagination_no >= 1 && $pagination_no <= 2) || ($pagination_no >= $total_pagionations-1) || ($pagination_no>=$page-1 && $pagination_no<=$page+1)){
                                $dots = false;
                                $value .='<li class="page-item'.($pagination_no == $page?' active"' : '"' ).'><a class="page-link " onclick="'.($pagination_no != $page?'call_ajax('.$pagination_no.')' : '' ).'">'.$pagination_no.'</a></li>';
                            }
                            elseif (!$dots) {
                                $value .='<li class="page-item"><i class="page-link" >...</i></li>';
                                $dots = true;
                            }
                            $pagination_count -= $limit;
                        }
                        $value .='
                        <li class="page-item">
                            <a class="page-link" onclick="'.($page!=$total_pagionations ? 'call_ajax('.$next.')': '').'" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>';
    } else {
        echo '<h4 align="center" class="p-5 mb-0 text-danger bg-white border rounded">No Record Found...!</h4>';
    }
    return $value;
}
?>