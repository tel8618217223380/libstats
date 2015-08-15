# Add Field/Column input type to Libstats #
(Based on outline and support from Erin - Thanks, Erin!) Tested on version 1.0.6 - not verified for 1.0.5.
These instructions use “read” as a type.  This is for adding the [READ scale](http://bkarrgerlich.googlepages.com/). You can substitute any name for "read" – just be consistent.

---

# WARNING #
These modification include core components of libstats.  You should back up your database and all files before attempting these changes.  It is strongly suggested that you try this on a test installation and not a production install.

---

## Database modifications ##
These modifications can be done in phpMyAdmin.

  1. Select patron\_types table and under operations tab copy table to read\_types (structure and data).
  1. Do the same with library\_patron\_types.
  1. For both read\_types and library\_read\_types, edit fields replacing ‘patron’ with ‘read’.
  1. Modify question table: add read\_type\_id after patron\_type\_id with same attributes (int(11) unsigned NULL default=0)
  1. Modify admin table: Insert read types following patron types model

## Action Folder ##
  1. Modify HelpAction**.php - insert at line 69
```
$result['descRead'] =
   $helpFinder->getReadOptions($library_id_post);
```
  1. Duplicate LibraryPatronTypeAction.php and rename LibraryReadTypeAction.php
    * Edit, changing ‘patron’ instances to ‘read’
    * NOTE: (be sure to match case.) and change $ptf instances to $rtf. ($rtf -in this case - represents read type finder) any other value can be used, it just needs to be consistent throughout as this string exists in several files.
  1. Duplicate PatronTypeAddAction.php and rename to  ReadTypeAddAction.php repeat above edits.
  1. Modify QuestionEditAction.php - insert at LINE 36
```
$qHash['read_type_id'] = gpwd('readType', null);
```
  1. Modify QuestionEditFormAction.php: insert:
    * LINE 35:
```
$rtf = new ReadTypeFinder($db);
```
    * LINE 45
```
$result['readTypeOpts'] = $rtf->findByLibrary($libId);
```
  1. Modify QuestionAddAction.php - insert LINE 24:
```
$qHash['read_type_id'] = gpwd('readType', null);
```
  1. Modify QuestionAddFormAction.php - insert:
    * LINE 36
```
$rtf = new ReadTypeFinder($db);
```
    * LINE 55
```
$result['readTypeOpts'] = $rtf->findByLibrary($libId);
```
    * LINES 77-9
```
$start = mTimeFloat();
$result['lastReadType'] = $rtf->getLast($clientIp);
$times['lastReadType'] = mTimeFloat() - $start;
```
## Content Folder ##
  1. In Admin folder: Modify Help.php - insert:
    * LINE 15-8
```
$descReadArray = $rInfo['descRead'];
if(!isset($descReadArray)){
$descReadArray = 0;
}
```
    * LINES 74-93
```
if ($rInfo['help_id'] == 4 && $descReadArray != 0){
	echo('<br /><h3>Options: </h3>');
	echo('<ul>');
	foreach($descReadArray as $choice){
		echo ('<li>' . $choice['read_type']);
			if (strlen($choice['description']) > 2){
				echo('---' . $choice['description'] . '</li>');
			} else {
				echo('</li>');
			}
			if (strlen($choice['examples']) > 2){
				echo('<br /><ul>
						<li>Examples: ' . $choice['examples'] . '</li>
					  </ul><br />');
			} else {
				continue;
			}
	}
	echo('</ul>');
}
```
  1. Modify QuestionAddForm.php to encompass another input
    * LINES 36-44
```
<div class = "inputBox">
    <h5><a href="help.do?advice=4" class="helpLink=">READ Scale</a></h5>
    <?php echo($optionFunction(
        'readType',
        $rInfo['readTypeOpts'],
        'read_type_id',
        'read_type', 
        $rInfo['lastReadType'], 5)); ?>
</div>
```
  1. Modify QuestionEditForm.php similar to the above
    * LINES 55-64
```
    <div class = "inputBox">
        <h5>Time Spent</h5>
        <?php echo( $optionFunction(
            'timeSpent',
            $rInfo['timeSpentOpts'],
            'time_spent_id',
            'time_spent',
            $rInfo['question']['time_spent_id'], 5)
            ); ?>
    </div>
```
## Root Folder ##
  1. Modify ControllerFunctions.php
    * LINE 96:
```
'libraryReadType.do' => new LibraryReadTypeAction(),
```
## Finders Folder ##
  1. In QuestionFinder.php - add
    * LINES 27-8
```
        questions.read_type_id,
        read_types.read_type,
```
    * LINES 52-3
```
    LEFT JOIN read_types ON 
        (questions.read_type_id = read_types.read_type_id)
```
  1. Modify HelpFinder.php –insert
    * LINES 41-53
```
	//Locate field option descriptions--patron_type
	function getReadOptions($library_id){
		$fullQuery =
		"SELECT *
		FROM `read_types`
		LEFT JOIN (library_read_types) ON
			(read_types.read_type_id = library_read_types.read_type_id)
		WHERE library_read_types.library_id = ?
		ORDER BY list_order";
		
		$result = $this->db->getAll($fullQuery, array($library_id));
		return $result;
	}
```
## Reports Folder ##
  1. Modify DataCSVReport.php – insert:
    * LINE 35:
```
		read_types.read_type,
```
    * LINE 59:
```
		JOIN read_types ON questions.read_type_id = read_types.read_type_id
```
  1. Duplicate reports>ByPatronType.php & rename ByReadType.php
    * Replace all instances of “Patron/patron” with “Read/read” (watch case.)**

  * In addition, make these modifications (thanks to cgrimland for catching this):

  * LINES 27-32:
```
    $fullQuery = "SELECT COUNT(questions.question) as questions, read_types.read_type
as readtypes
                        FROM questions
                        JOIN read_types ON
                        (questions.read_type_id = read_types.read_type_id)
                        $sql
                        GROUP BY readtypes";
```
  * LINE 74:
```
<td>{$report["readtypes"]}</td>
```

---
