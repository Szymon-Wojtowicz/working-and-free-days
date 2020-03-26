<?php

//Function shows list of working days in month.
function listOfWorkingDaysInMonth($year = '', $month = '')
{
    //create a start and an end datetime value based on the year value entered in a form
    $startdate = strtotime($year . '-' . $month . '-01');
    $enddate = strtotime('+' . (date('t', $startdate) - 1) . ' days', $startdate);
    $currentdate = $startdate;

    //days free of work - public holidays, church holidays:
    $freeDays =
    array(
    '05-01', '05-03', '11-11',        //Public holidays (in Poland): 1st May, 3rd May, 11th November
    '01-01','01-06', '12-25','12-26'  /*Church holidays (in Poland): 1st January, 6th January, 25th December (1st day of Christmas), 26-th December (2nd day of Christmas),
                                        Date of Easter Monday changes dynamically in each year. It is calculated in function: easterMondayDate()*/
    );

    $easterMonday=easterMondayDate();

    //loop through the dates, from the start date to the end date
    while ($currentdate <= $enddate) {
        $md = date('m-d', $currentdate);

        //if you not encounter a Saturday or Sunday or days free of work (public holidays, church holidays) add day to the list of working days
        if ((date('D', $currentdate) != 'Sat') && (date('D', $currentdate) != 'Sun') && !in_array($md, $freeDays) && !in_array($md,$easterMonday))
        {
            $workingDay = date('m-d D', $currentdate) . "<br>";
            echo "<br>" . $workingDay;
        }
        $currentdate = strtotime('+1 day', $currentdate);
    } //end date walk loop

}

//Function shows list of free days in month.
function listOfFreeDaysInMonth($year = '', $month = '')
{
    //create a start and an end datetime value based on the year value entered in a form
    $startdate = strtotime($year . '-' . $month . '-01');
    $enddate = strtotime('+' . (date('t', $startdate) - 1) . ' days', $startdate);
    $currentdate = $startdate;

    //days free of work - public holidays, church holidays:
    $freeDays =
     array(
     '05-01', '05-03', '11-11',        //Public holidays (in Poland): 1st May, 3rd May, 11th November
     '01-01','01-06', '12-25','12-26'  /*Church holidays (in Poland): 1st January, 6th January, 25th December (1st day of Christmas), 26-th December (2nd day of Christmas),
                                         Date of Easter Monday changes dynamically in each year. It is calculated in function: easterMondayDate()*/
     );

    $easterMonday=easterMondayDate();

    //loop through the dates, from the start date to the end date
    while ($currentdate <= $enddate) {
        $md = date('m-d', $currentdate);

        //if you encounter a Saturday or Sunday or days free of work (public holidays, church holidays) add day to the list of free days
        if ((date('D', $currentdate) == 'Sat') || (date('D', $currentdate) == 'Sun') || in_array($md, $freeDays) || in_array($md, $easterMonday)) {
            $freeDay = date('m-d D', $currentdate) . "<br>";
            echo "<br>" . $freeDay;
        }
        $currentdate = strtotime('+1 day', $currentdate);

    } //end date walk loop

}

//Function returns number of working days in month.
function calculateWorkingDaysInMonth($year = '', $month = '')
{
    //create a start and an end datetime value based on the year value entered in a form
    $startdate = strtotime($year . '-' . $month . '-01');
    $enddate = strtotime('+' . (date('t', $startdate) - 1) . ' days', $startdate);
    $currentdate = $startdate;
    //get the total number of days in the month
    $return = intval((date('t', $startdate)), 10);

    //days free of work - public holidays, church holidays:
    $freeDays =
    array(
        '05-01', '05-03', '11-11',        //Public holidays (in Poland): 1st May, 3rd May, 11th November
        '01-01','01-06', '12-25','12-26'  /*Church holidays (in Poland): 1st January, 6th January, 25th December (1st day of Christmas), 26-th December (2nd day of Christmas),
                                            Date of Easter Monday changes dynamically in each year. It is calculated in function: easterMondayDate()*/
    );

    $easterMonday=easterMondayDate();

    //loop through the dates, from the start date to the end date
    while ($currentdate <= $enddate) {
        $md = date('m-d', $currentdate);

        //if you encounter a Saturday or Sunday or days free of work (public holidays, church holidays) remove from the total days count
        if ((date('D', $currentdate) == 'Sat') || (date('D', $currentdate) == 'Sun') || in_array($md, $freeDays) || in_array($md, $easterMonday)) {
            $return = $return - 1;
        }
        $currentdate = strtotime('+1 day', $currentdate);

    } //end date walk loop

    //return the number of working days
    return $return;
}

//Function counting date of Easter (Easter Sunday) and Easter Monday. Function returns day of Easter Monday.
function easterMondayDate()
{
    $year = $_GET["year"]; //$_GET is a PHP super global variable which is used to collect form data after submitting an HTML form with method="get".

    //Calculating date of Easter in a given year by Gauss method for Gregorian calendar:
    $a = $year % 19; //Step1: Divide year by 19 and get remainder a.
    $b = $year % 4;  //Step2: Divide year by 4 and get remainder b.
    $c = $year % 7;  //Step3: Divide year by 7 and get remainder c.

    $A = 24; //Number A for years 1900-2099
    $d = (19 * $a + $A) % 30; //Step4: Remainder a multiply by 19, to product add number A, divide sum by 30 and get remainder d.
    $B = 5; //Number B for years 1900-2099
    $e = (2 * $b + 4 * $c + 6 * $d + $B) % 7; //Step5: Divide sum of products 2b +4c + 6d + B by 7 and get remainder e.
    $easter_date = $d + $e + 22; //Step6: Sum of remainders d + e add to to date 22nd March and get date of Easter.

    //Exception handling: calculating date of Easter in April
    if ($easter_date > 31) {
        $easter_date = $d + $e - 9;
        $easterDate = '04-' . $easter_date; //Easter is in April
        $easterDateString = $year . '-' . $easterDate;
        $formattedEasterDate = date("Y-m-d", strtotime($easterDateString));

        //get the next day after Easter (Easter Monday)
        $formattedEasterMondayDate = date('Y-m-d', strtotime('+1 day', strtotime($formattedEasterDate)));

        $easterMonday = date('m-d', strtotime($formattedEasterMondayDate));

        return array($easterMonday);
    }
    else {
        $easterDate = '03-' . $easter_date; //Easter is in March
        $easterDateString = $year . '-' . $easterDate;
        $formattedEasterDate = date("Y-m-d", strtotime($easterDateString));

        //get the next day after Easter (Easter Monday)
        $formattedEasterMondayDate = date('Y-m-d', strtotime('+1 day', strtotime($formattedEasterDate)));

        $easterMonday = date('m-d', strtotime($formattedEasterMondayDate));

        return array($easterMonday);
    }
}


$year_nr = $_GET["year"];  //PHP super global $_GET

$year_str = strval($year_nr);  //convert integer to string

echo "<h3>Working days in $year_str year:</h3>";
for ($m = 0; $m < 12; ++$m) {
    $mon = date("F", strtotime("January +$m months"));  //print full textual representation of months
    $month = $m + 1;
    echo "month: <a href='month.html?month=$month&year=$year_str'><b>$mon</b></a>";
    echo "<br>working days: ";
    echo "<b>" . calculateWorkingDaysInMonth($year_str, $month) . "</b><br><br>";
}

echo "<h3>Working days in months:</h3>";
for ($m = 0; $m < 12; ++$m) {
    $mon = date("F", strtotime("January +$m months"));
    $month = $m + 1;
    echo "<b>" . $mon . "</b>";
    echo "<br>" . listOfWorkingDaysInMonth($year_str, $month) . "<br>";
}

echo "<h3>Free days in months:</h3>";
for ($m = 0; $m < 12; ++$m) {
    $mon = date("F", strtotime("January +$m months"));
    $month = $m + 1;
    echo "<b>" . $mon . "</b>";
    echo "<br>" . listOfFreeDaysInMonth($year_str, $month) . "<br>";
}

echo '<a href="form.html">Back</a>';