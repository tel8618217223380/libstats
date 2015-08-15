# Reporting in LibStats 1.0.5 #

This guide is to provide tips and assistance for creating new reports in LibStats version 1.0.5. Please note that there is a difference in reporting between 1.0.5 and 1.0.6.

The guide is broken into two (2) main parts:
  * the steps creating a new report
  * a fix for a possible bug when creating new reports.

# Creating New Reports #

There are three (3) changes that need to be made to LibStats to include a new report:
  1. creating a new reporting file
  1. adding an row/entry into the reports table
  1. modifying a file where results are displayed

For this example, a question type report will be demonstrated. It is important to maintain a certain naming consistency because the file name duplicates as a subclass, which the name is stored in the reports table.

## The Report File ##

All reports are kept in the listats/reports directory. When adding a new report, a new file must be created following the naming consistency.

Create a file called:
> reports/QuestionsByQuestionTypeReport.php

Inside the file is subclass containing the SQL for the report. **Note**: the file name is the same as the class name; these names **must** be the same.
```
    <?php
    require_once 'Report.php';

    /**
     * Report for questions types
     *
     * This will select question types for the requested dates
     *
     */
    class QuestionsByQuestionTypeReport extends Report {
      // This class is rather abstract, and its methods
      // are all do-nothing methods.
      // Then again, subclasses aren't that much better...

      function perform($sql, $param) {
        //echo $sql;
        // don't lose the db!
        $db = $_REQUEST['db'];
        $this->db = $db;

        // gather $result
        $fullQuery = <<<QUERYSTRING
          SELECT COUNT(questions.question) as questions, question_types.question_type
          FROM questions
          JOIN question_types ON
                  (questions.question_type_id = question_types.question_type_id) $sql
          GROUP BY question_types.question_type
    QUERYSTRING;
        //echo ($fullQuery);
        $result = $this->db->getAll($fullQuery, $param);

        return $result;
      }

      function isAuthenticationRequired() {
        return true;
      }
    }
    ?>
```

## The Reports Table in the Database ##

When a report is called, they are handled by the reports table in the libstats database. When adding a new report, a row/entry needs to be added. Here is what information is needed

|report\_id|_leave blank unless you want a specific number assigned to a report, if left blank when inserting into the table, the next sequence number will be inserted_|The ID number of the report|
|:---------|:-----------------------------------------------------------------------------------------------------------------------------------------------------------|:--------------------------|
|report\_name|_required_                                                                                                                                                  |The name of the report as it will appear on the reportList.do page and the heading of the report|
|report\_description|                                                                                                                                                            |The description of the report as it will appear on the reportList.do page|
|report\_class|_required_                                                                                                                                                  |The name used in the php file for the class and of the php file|

In continuing with the question type report, add the following information into the reports table:

| **column** | **value** |
|:-----------|:----------|
|report\_id  |           |
|report\_name|Questions by Question Type|
|report\_description|This report provides the count of questions for every question type.|
|report\_class|QuestionsByQuestionTypeReport|

## Displaying the Report ##

The actual results are located in libstats/content/reportReturn.php There is long conditional statement for `_`REQUEST['report`_`id']. At the bottom of the last elseif statement is where a new conditional statement will be added for the new "Questions by Question Type"/QuestionsByQuestionTypeReport report.

Before pasting the code, get the report\_id from the reports table for QuestionsByQuestionTypeReport. For this example, "9" will be used as the report`_`id for QuestionsByQuestionTypeReport.

Here is the code to continue the conditional statement and include the new report:
```
/**
* for QuestionsByQuestionTypeReport
* report id =9
*
* Reports the types of questions

*
*/
elseif ($_REQUEST['report_id'] == 9) {
 // used for debugging new reports
 //echo "<pre>"; print_r($reports); echo "</pre>";
 echo "<h3>{$rInfo['library_name']}";
 if(isset($rInfo['location_name']))
  echo " | {$rInfo['location_name']}";
 echo "</h3><h3>{$_REQUEST['report_name']} from $dateStart through $dateEnd- Full Report</h3>";

 // make my report table header...
 echo '<table id= "questionTable"><tr><th>Question Type</th><th>Question Count</th><th>Percentage</th></tr>';

 // get my report table data...
 $arrayIndex = 0;
 $count = array();
 $percentage = array();
 echo '<tbody>';
 foreach ($reports as $report) {
  echo "<tr>
   <td>{$report['question_type']}</td>
   <td>" . ($report["questions"] + 0) . "</td>
   <td>" . round(((($report["questions"] + 0) / $numberReportQuestionCount)*100), 1) . "%</td></tr>";
  $arrayIndex++;
  $count[$arrayIndex] = $report["questions"];
  $percentage[$arrayIndex] = round(((($report["questions"] + 0) / $numberReportQuestionCount)*100), 1);
 }
 echo '</tbody>';

 // total report summary
 $questionSum = array_sum($count);
 $questionPercentage = array_sum($percentage);
 echo "<tr><td><strong>Totals</strong></td><td><strong>$questionSum </strong></td>" .
  "<td><strong>$questionPercentage%</strong></td></tr></table>";
}
```

**Tip**: If a blank result screen for the report, check to make sure report\_class is the same and the php file name and the subclass name.

**Tip**: When creating any new report and to see the data returned from the SQL query, print out the $reports array
```
 // used for debugging new reports
 echo "<pre>"; print_r($reports); echo "</pre>";
```

# Fix to Possible Bug #

When adding reports using the above steps, sometimes the class is not declared and an
error as such may appear:
> Fatal error: Class 'QuestionsByQuestionTypeReport' not found in [to web directory](path.md)/libstats/actions/ReportReturnAction.php on line 86

Despite best efforts of checking, rechecking, and have others double and triple check, sometimes a class was still not found even though the subclass name, file name and report\_class value in the database all match. Some of these errors were resolved merely by switching .vimrc files, editing libstats/reports/QuestionsByQuestionTypeReport.php, setting ":set cp" in vim and then literally copying from one place to the file; (_not sure what good that was doing but it did resolve this particular error on some of the newer reports but not all them.... which led to a quicker fix- modifying a core file..._)

**WARNING**

The following code will check to see if a certain class exists. If it does not exists, it includes the file reading the class. Modifications are made to core LibStats files. Please backup original file before modifying them. Also, this is not very good practice, but it's a fix.

In libstats/actions/ReportReturnAction.php, insert the following code at approximately line 86 (or, if the error line varies, the line preceding the error):
```
/**
 * Check to see if the class for the report is declared
 *
 * This code was added because sometimes the class entry exists in the database but the class
 * is not defined. Not sure if this is a bug, but this code fixes the problem
 */
if(!class_exists($reportChosen["report_class"])) {
 require_once("reports/" . $reportChosen["report_class"] . ".php");
}
```

> The code should look like this when in place:
```
// select relevant data from db
$reportFinder = new ReportFinder($db);
$reportCount = $reportFinder->getReportCount();
$reportQuestionCount = $reportFinder->getReportQuestionCount($sql, $param);
$reportChosen = $reportFinder->getChosenReport($report_id);

/**
 * Check to see if the class for the report is declared
 *
 * This code was added because sometimes the class entry exists in the database but the class
 * is not defined. Not sure if this is a bug, but this code fixes the problem
 */
if(!class_exists($reportChosen["report_class"])) {
 require_once("reports/" . $reportChosen["report_class"] . ".php");
}

$reportPerform = new $reportChosen['report_class']($db);
if ($reportChosen['report_class'] == 'StatisticsReport') {
```