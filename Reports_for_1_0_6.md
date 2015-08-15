

# Reporting in LibStats 1.0.6 #

A big difference between version 1.0.5 and 1.0.6 is the reporting.

In 1.0.5, a report was contained in multiple files and a table in the LibStats database. The SQL for the report was contained in a file in the /reports/ directory, the report output (as in the HTML) was part of another file with a very large conditional statement and the information about the report was kept in a reports tables in the LibStats database.

The change for 1.0.6 was to have all the information about the reports be self contained in one place- a file containing the information about the report, the SQL and report display.

There are advantages for having the report self contained is each report. Since all the information is contained within one (1) file, reports will be easier to create. will be easier to create; core files will not have to be modified. Also since the report is in one (1) file, reports can be shared. Other libraries my share reports with others by only dropping the report file in the /report directory.


## Report file ##

Each report itself is a (sub)class of Reports. There are four (4) sections to each report, with each section being a method:

  1. info- The information about the report- name and description.
  1. perform- The SQL statement which retrieves the data from the report.
  1. display- This displays the results of the SQL query in as HTML output.
  1. isAuthenticationRequired- This is legacy code included from the previous report (sub)class.

The (sub)class of the report must be named exactly as the file, minus the /.php/- e.g. file name is ByTimeOfDay.php; (sub)class of the report is called ByTimeOfDay.

Example:

```
class ByInitials extends Report {
```

It is important that the (sub)class of the report and the file be named the same. When running the report it matches the (sub)class of the report to the file- i.e. class tree will run tree.php. If the (sub)class and file are named differently, the report will not execute correctly, thus resulting in blank white screen or errors.


## info() ##

Information about the report is contained here. This method is called on the page when selecting which report to run. There are two (2) required indexes of an array which must be returned:

  1. [[name](name.md)]- The full name of the report.
  1. [[desc](desc.md)]- A detailed description of the report.

Returned from the info() method is an array containing the “name” and “desc” indexes.

Example:

```
	function info() { 
		$report_info["name"] = "Questions By Initials- Question Format";
		$report_info["desc"] = "This report will provide individual statistics based on the initials recorded on the \"Add A Question\" page for office visits.";

		return ($report_info); 
	}
```


## perform() ##

The perform() method is the heart of the report (sub)class because it contains the SQL query. The two (2) arguments passed into perform() are:

  1. the SQL “where” clause
  1. the parameters from the form

There are two (2) indexes of an array which must be returned:

  1. [[data](data.md)]- the results of the SQL query
  1. [[metadata](metadata.md)]- the column header information

If they are returned under a different array tree or a different index, the report will not work.


Example:
```
	function perform($sql, $param) { 

		// don't lose the db! 

		$db = $_REQUEST['db'];

		$this->db = $db; 

		// gather $result 
		$fullQuery = <<<QUERYSTRING
			SELECT COUNT(questions.question) as questions_count, initials, 
				questions.question_format_id, question_formats.question_format, 
				question_formats.description 
			FROM questions 
			JOIN question_formats ON 
				(questions.question_format_id = question_formats.question_format_id) $sql 
			GROUP BY initials, questions.time_spent_id 
	QUERYSTRING; 

		$result["data"] = $this->db->getAll($fullQuery, $param);
		$result['metadata'] = array_keys($result['data'][0]);

		return $result; 
	}
```

It is important to remember that the stronger your SQL statement is the more complete the CSV output of the individual report is. ''(more explained later.)''


## display() ##

This method is where the results from perform()'s SQL query are processed and outputted via HTML.

The single argument being passed is a multi-dimensional array. The display() method does not use every part of the multidimensional array only certain indexes:

  1. [[library\_name](library_name.md)]- the library name
  1. [[location\_name](location_name.md)]- name of the location
  1. [[reportList](reportList.md)][[name](name.md)] - name of the report
  1. [[date1](date1.md)]- start date of report
  1. [[date2](date2.md)]- end date of report
  1. [[reportResults](reportResults.md)][[data](data.md)]- this is where the SQL results from perform() are stored.

Instead of having a standardized report header display in part of the actual Report class, the entire report is left to be created from scratch. Typical information for reports are: the library name, location name, name of report and start and end dates.

Using the argument passed, a report can be further calculated and displayed. It is important to remember to loop through the [“reportResults”][“data”] array because this is where the results from perform()'s SQL query.

Nothing for this report needs returned.


## isAuthenticationRequired() ##

This is a legacy method moved over from 1.0.5. It is unclear if isAuthenticationRequired() is required.


## About the individual report CSV export ##

A new feature added to the later release of 1.0.6 is the ability to export a single report as a CSV (Comma Separated Value) file, which can then be imported into a spreadsheet- i.e. Excel.

This feature is not actually contained with the main Report class or the individual report (sub)classes. It was added to /actions/ReportReturnAction.php and /content/reportReturn.php. A new link was added to content/reportReturn.php to allow for a single report to be exported into a spreadsheet; the new link contains various information needed to essentially to recall perform(). A conditional statement was added to /actions/ReportReturnAction.php to check if the PHP request value of csv\_export is set.

The single exported CSV report will only be as good as the SQL query. This is the reason why it's important to have an accurate SQL query. If a report relies on PHP to further fine-tune and sort out the SQL results, it will only be displayed; the output of the PHP will not be in the exported CSV file.


## How to create a report ##

  * Know what information you want in the final report.
  * Create the SQL query through the shell or PHPMyAdmin.
  * Create a new report file: “MyNewReport.php”.
  * Create the main (sub)class:

```
<?php

	class MyNewReport extends Report { 

	}
```
  * Create the isAuthenticationRequired() method within the (sub)class:

```
	function isAuthenticationRequired() { 
		return true; 
	} 
```
  * Create the info() method method within the (sub)class:
  * Test it on the select a report page- /reportList.do .
  * Create the perform() method method within the (sub)class.
  * Create a basic display() method to display the results of the multidimensional array.
  * Test report by running it.
  * Finish display() until desired report is achieved.

Remember to check the the CSV output for the report; keep in mind that it's based off of the SQL, not the PHP.


## Possible new features ##

New ideas for reporting:
  * Setting up a cronjob to automatically email reports, with a CSV, to recipients.
  * create a report interface to allow reports to be created on the fly with no scripting experience- no PHP, no SQL, just GUI interface