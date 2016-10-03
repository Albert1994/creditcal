<?php

function render_table_differentiated($array) {
        $return = '';
        $return .= '<table>
<thead>
<tr>
<th>Дата платежа</th>
<th>Остаток задолженности<br> по кредиту</th>
<th>Платеж<br> по процентам</th>
<th>Платеж<br> по кредиту</th>
<th>Аннуитетный<br> платеж</th>
</tr>
</thead>
<tbody>';
        foreach ($array as $key => $value) {
            $return .= '<tr>
                <td>' . $value['month'] . '</td>
                <td>' . $value['dept'] . '</td>
                <td>' . $value['percent_pay'] . '</td>
                <td>' . $value['credit_pay'] . '</td>
                <td>' . $value['payment'] . '</td>
                </tr>';
        }
        $return .= '</tbody></table>';

    return $return;
}

function render_table_annuity($array) {
    $return = '';
    $return .= '<table>
<thead>
<tr>
<th>Дата платежа</th>
<th>Остаток задолженности<br> по кредиту</th>
<th>Платеж<br> по процентам</th>
<th>Платеж<br> по кредиту</th>
<th>Ежемесячный<br>платеж</th>
</tr>
</thead>
<tbody>';
    foreach ($array as $key => $value) {
        $return .= '<tr>
                <td>' . $value['month'] . ' </td>
                <td>' . $value['dept'] . '</td>
                <td>' . $value['percent_pay'] . '</td>
                <td>' . $value['credit_pay'] . '</td>
                <td>' . $value['payment'] . '</td>
                </tr>';
    }
    $return .= '</tbody></table>';

    return $return;
}

function credit($term, $rate, $amount, $month, $year, $typecredit, $round = 2)  {
    // $term - срок кредита (в месяцах), $rate процентная ставка, $amount - сумма кредита (в рублях)
    // $month - месяц начала выплат, $year - год начала выплат, $round - округление сумм

    $result = array();

    $term = (integer)$term;
    $rate = (float)str_replace(",", ".", $rate);
    $amount = (float)str_replace(",", ".", $amount);
    $round = (integer)$round;
    $typecredit = (bool)$typecredit;
    $payment = 0;
    $overpay = 0;

    if($typecredit == 1) {

    $month_rate = ($rate/100/12) ;   //  месячная процентная ставка по кредиту (= годовая ставка / 12)
    $k = ($month_rate * pow((1 + $month_rate), $term)) / ( pow((1 + $month_rate), $term) - 1  ) ; // коэффициент аннуитета
    $payment = round($k * $amount, $round) ;   // Размер ежемесячных выплат
    $overpay = ($payment * $term) - $amount ;
    $debt = $amount ;

        for ($i = 1; $i <= $term; $i++) {

            $schedule[$i] = array();

            $percent_pay = round($debt * $month_rate, $round);
            $credit_pay = round($payment - $percent_pay, $round);

            $schedule[$i]['month'] = $month . '/' . $year;
            $schedule[$i]['dept'] = number_format($debt, $round, ',', ' ');
            $schedule[$i]['percent_pay'] = number_format($percent_pay, $round, ',', ' ');
            $schedule[$i]['credit_pay'] = number_format($credit_pay, $round, ',', ' ');
            $schedule[$i]['payment'] = number_format($payment, $round, ',', ' ');

            $debt = $debt - $credit_pay;

            if ($month++ >= 12) {$month = 1; $year++;}
        }

        $result['overpay'] = number_format($overpay, $round, ',', ' ') ; ;
        $result['payment'] = number_format($payment, $round, ',', ' ') ; ;
        $result['schedule'] = render_table_differentiated($schedule) ;
    }
    else{


        $credit_pay = $amount / ($term);
        $month_rate = ($rate/100/12);
        $debt = $amount ;

        for($i = 1; $i <= $term; $i++){

            $schedule[$i] = array();
            $percent_pay = round((($amount - ($i-1)*($credit_pay))* $month_rate),$round);
            $payment = round($credit_pay+$percent_pay,$round);

            $schedule[$i]['month'] = $month . '/' . $year;
            $schedule[$i]['dept'] = number_format($debt, $round, ',', ' ');
            $schedule[$i]['percent_pay'] = number_format($percent_pay, $round, ',', ' ');
            $schedule[$i]['credit_pay'] = number_format($credit_pay, $round, ',', ' ');
            $schedule[$i]['payment'] = number_format($payment, $round, ',', ' ');

            $overpay += $percent_pay;
            $debt = $debt - $credit_pay;

            if ($month++ >= 12) { $month = 1; $year++; }
        }


        $result['payment'] = "-" ;
        $result['schedule'] = render_table_annuity($schedule) ;
        $result['overpay'] = number_format($overpay, $round, ',', ' ') ; ;

    }


    return $result ;

}