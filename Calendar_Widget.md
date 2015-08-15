# Introduction #

These instructions are for adding a calendar widget to the Reports and Add Question page.

http://libstats.googlegroups.com/web/dhtml_cal_widget_example.jpg?gda=N8BrLU4AAADulaTf2ugpEEx7GUhNxTCui5__r1ho977euO-JA8zeDGkwBBZVaM4pZBxjyHxwGS2FVkxLswNDKSLKln-r0e6c47Cl1bPl-23V2XOW7kn5sQ

# Credits #

The following uses a DHTML Calendar script written by Mihai Bazon (mihai\_bazon@yahoo.com, http://dynarch.com/mishoo/), which is published under the GNU Lesser General Public License.

# WARNING #
Modifications are made to core LibStats files. Please backup original files before modifying them.

# Details #

1) In the main LibStats directory, create a subdirectory called "addons". For example:

> libstats/addons

2) Download 'The "Coolest" DHTML Calendar' from http://www.dynarch.com/projects/calendar/
> direct download link as of 2009/01/22: http://www.dynarch.com/static/jscalendar-1.0.zip

3) Unzip jscalendar-1.0.zip and copy the jscalendar-1.0 to the addons directory. The path should be:

> libstats/addons/jscalendar-1.0

4) In libstats/template\_renderer.inc, insert the follow into the header below the other CSS:
```
<!-- add the following CSS for the calendars -->
<style type="text/css">@import url(addons/jscalendar-1.0/calendar-win2k-1.css);</style>
<script type="text/javascript" src="addons/jscalendar-1.0/calendar.js"></script>
<script type="text/javascript" src="addons/jscalendar-1.0/lang/calendar-en.js"></script>
<script type="text/javascript" src="addons/jscalendar-1.0/calendar-setup.js"></script>
```

5) In libstats/content/reportFormDate.php, modify some of the HTML to slide in the calendar button for the starting date. Below the Begin Date text box, insert the following code:
```
<button id="trigger1">...</button>
```

> The code should look like this when in place:
```
 Begin Date: <input type="text" name="date1" id = "date1" class = "validDate" />
 <button id="trigger1">...</button>
```

6) Again in libstats/content/reportFormDate.php, modify some of the HTML to slide in the calendar button for the end date. Below the End Date text box and above the line breaks, insert the following code:
```
<button id="trigger2">...</button>
```

> The code should look like this when in place:
```
 End Date: <input type="text" name="date2" id = "date2" class = "validDate" />
 <button id="trigger2">...</button>
 <br /><br />
```

7) Still in libstats/content/reportFormDate.php, add some JavaScript for the calendar widget. The following code needs placed right after the closing of the form and before the other javascript:
```
<script type="text/javascript">
 Calendar.setup(
  {
   inputField  : "date1",              // ID of the input field
   ifFormat    : "%m/%d/%Y %I:%M %p",  // the date format
   button      : "trigger1",           // ID of the button
   showsTime   : "true",               // show time
   timeFormat  : "12"                  // set time to 12 hours, not 24
  }
 );

 Calendar.setup(
 {
   inputField  : "date2",              // ID of the input field
   ifFormat    : "%m/%d/%Y %I:%M %p",  // the date format
   button      : "trigger2",           // ID of the button
   showsTime   : "true",               // show time
   timeFormat  : "12"                  // set time to 12 hours, not 24
  }
 );
</script>
```

> The code should look like this when in place:
```
</form>

<script type="text/javascript">
 Calendar.setup(
  {
   inputField  : "date1",              // ID of the input field
   ifFormat    : "%m/%d/%Y %I:%M %p",  // the date format
   button      : "trigger1",           // ID of the button
   showsTime   : "true",               // show time
   timeFormat  : "12"                  // set time to 12 hours, not 24
  }
 );

 Calendar.setup(
 {
   inputField  : "date2",              // ID of the input field
   ifFormat    : "%m/%d/%Y %I:%M %p",  // the date format
   button      : "trigger2",           // ID of the button
   showsTime   : "true",               // show time
   timeFormat  : "12"                  // set time to 12 hours, not 24
  }
 );
</script>

<script type = "text/javascript">
 var opts = checkCookies();
```

8) Verify the calendar widget is working before proceeding to adding the calendar widget the Add Question page for backdating. If it's not working, there may be a problem with the linking path to the calendar widget JavaScript.

9) In libstats/content/questionAddForm.php, modifiy some of the HTML to slide in the calendar button. The code will go below the mydate text input and above the closing of the div:
```
<button id="trigger">...</button>
```

> The code should look like this when in place:
```
<div class = "inputBox">
 <h5><a href="help.do?advice=5" class="helpLink=">Backdate</a></h5>
 <input name = "mydate" id = "mydate" type = "text" size = "15"
 class = "validDate" />
 <button id="trigger">...</button>
</div>
```

10) Still in libstats/content/questionAddForm.php, add some javascript for the calendar. The following code needs placed right after the closing of the form and before the closing of the div:
```
<script type="text/javascript">
 Calendar.setup(
  {
   inputField  : "mydate",             // ID of the input field
   ifFormat    : "%m/%d/%Y %I:%M %p",  // the date format
   button      : "trigger",            // ID of the button
   showsTime   : "true",               // show time
   timeFormat  : "12"                  // set time to 12 hours, not 24
  }
 );
</script>
```

> The code should look like this when in place:
```
</form>

<script type="text/javascript">
 Calendar.setup(
  {
   inputField  : "mydate",             // ID of the input field
   ifFormat    : "%m/%d/%Y %I:%M %p",  // the date format
   button      : "trigger",            // ID of the button
   showsTime   : "true",               // show time
   timeFormat  : "12"                  // set time to 12 hours, not 24
  }
 );
</script>

</div>
```

11) Verify the calendar widget is working on the Add Question page. If it's not working, there may be a problem with the linking path to the calendar widget JavaScript.