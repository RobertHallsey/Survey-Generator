<?php

/**
 * The Survey Plugin for Wolf CMS makes it easy to conduct custom surveys.
 *
 * This file is part of the Survey Plugin for Wolf CMS.
 *
 * @author Robert Hallsey <rhallsey@yahoo.com>
 * @copyright Robert Hallsey, 2015
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */
?>
<h1>Survey Plugin for Wolf CMS</h1>

<h2>Introduction</h2>

<p>If you're reading this, you must have already installed the plugin. Great!</p>

<h2>Files in the Package</h2>

<p>You should have received the plugin in a zip file containing the following files:</p>

<pre>
\index.php
\readme.md
\sample-survey
\Survey.php
\SurveyController.php
\views\index.php
\views\sidebar.php
\i18n\en-message.php
\i18n\sp-message.php
\i18n\nl-message.php
</pre>

<p>To replace missing files, you may download the latest version from <a href="https://github.com/RobertHallsey/Survey-plugin-for-Wolf-CMS">the plugin's GitHub repository.</a></p>

<p>Please move the sample-survey file to the Wolf public directory. You may delete the readme.md file, if you wish.</p>

<p>Note that there may be other language files in the i18n directory. You only need the language files for the languages you intend to support. Deleting the ones you don't need will not affect the plugin's operation.</p>

<h2>How to Use the Plugin</h2>

<p>After you create one or more survey definition files and place them in Wolf's public directory, use this code in any Wolf page,</p>

<code>&lt;?php if (Plugin::isEnabled('survey')) survey_conduct('my_survey'); ?>;</code>

<p>where 'my_survey' is the survey definition file you wish to use. The <code>if</code> statement is optional but prevents errors if you disable the plugin while there are still pages that call it.</p>

<p>To display a summary of responses to a particular survey, use this code in any Wolf page,</p>

<pre>&lt;?php if (Plugin::isEnabled('survey')) survey_summarize('my_survey'); ?>;</pre>

<h2>Creating Your Own Surveys</h2>

<p>The plugin knows about surveys because surveys definition files describe the surveys. These files are normal text files that are stored in Wolf's public directory. You can create and edit text files with a text editor like notepad.exe under Windows or TextEdit on a Mac. You can also use a word processor like MS-Word, but make sure in all cases to save files as plain text. Open the sample survey file, called "sample-survey," and take a look at it. You may recognize the familiar INI format, but even if not, at least some of it will make sense at first glance.</p>

<h3>Survey Definition Files</h3>

<p>Survey definition files are made up of two or more sections, each section with a unique section heading that names the section. Section headings are surrounded by square brackets (i.e. []). The first section, which must be called "meta," defines the survey itself, and subsequent sections define the questions the survey will ask. Those subsequent sections may be called anything, but their names must be unique. You can't have two sections with the same name. Section headings must also not contain spaces. The plugin does not sort sections, so you don't have to name them sequentially. However, doing so makes it easier on the human eye, and that's why they are numbered sequentially in the sample survey file.</p>

<p>Each section has a variable number of lines, and each line contains a pair of items separated by an equals sign. We call the first item a "property," and the second item the "property's value." Take the first line in the meta section. If you want to get technical, it says that the property value of the property "name" is "Sample Survey." You could also just say, "The name of the survey is 'Sample Survey.'"</p>

<code>name = "Sample Survey"</code>

<p>Property-value pairs have a couple of rules. Only certain property names are recognized. Property values that are text must be surrounded by double quotes, but numeric property values should not be. Property names that repeat must end in an empty pair of square brackets ([]).</p>

<p>Let's look at the "meta" section. It contains the following properties:</p>

<pre><code>name = "Sample Survey"
hello = "Please answer the following questions."
goodbye = "Thank you for taking this survey!"</code></pre>

<p>The name property is the name of the survey, and will appear as the survey's title on the survey form. The hello property is the text that introduces the survey and optionally instructs survey takers. The text appears below the survey title on the survey form. The goodbye property is the message that appears instead of the hello message after the survey form is submitted.</p>

<p>Now let's look at the other sections, the ones that define survey questions. As mentioned earlier, their heading names can be anything, as long as they're unique (and not "meta"). There are three types of survey questions, types 1, 2, and 3.</p>

<h4>Type 1 Question</h4>

<pre><code>[section_1]
type = 1
title = "Preferences"
help = "S=small M=medium L=large"
questions[] = "What size Coke do you prefer?"
questions[] = "What size popcorn do you prefer?"
questions[] = "What size candy bar do you prefer?"
questions[] = "What size T-Shirt do you wear?"
answers[] = "S"
answers[] = "M"
answers[] = "L"</code></pre>

<p>The very first line within any section is the survey question type. Type 1 questions present a panel of questions, all with the same possible answers.</p>

<p>The help property is a message the plugin displays at the top of the questions, to the left of the answer headings. The help property is optional, and only type 1 questions recognize this property.</p>

<p>The title property is also optional, but can be used in all three question types. It's a heading that appears before the question. It's optional so that you can group several sections under the same title.</p>

<p>Now comes a list of questions followed by a list of possible answers. It may seem that the properties <code>questions</code> and <code>answers</code> are duplicated, but the pairs of empty quotes become a running tally number, so the result becomes:</p>

<pre><code>questions[0] = "What size Coke do you prefer?"
questions[1] = "What size popcorn do you prefer?"
questions[2] = "What size candy bar do you prefer?"
questions[3] = "What size T-Shirt do you wear?"</code></pre>

and:

<pre><code>answers[] = "S"
answers[] = "M"
answers[] = "L"</code></pre>

<p>And so on and so forth if there are more questions or answers.</p>

<p>Don't make the answers too long to avoid exceeding the page width. The more answers there are, the shorter the answers should be. Ideally, keep answers to one to three letter or numbers. Use the help property to explain what the answers mean. When processing this section, the plugin places each question, followed by three round checkboxes, on its own line.</p>

<p>Recall that, in the case of property values, text is surrounded by double quotes but numbers are not. If you're using Microsoft Word, the double quotes cannot be curly quotes. They must be the standard straight quotes.</p>

<h4>Type 2 Question</h4>

<p>This type of question allows one and only one multiple choice question.</p>

<pre><code>[section_2]
type = 2
questions[] = "What kind of car do you drive?"
answers[] = "Honda"
answers[] = "Toyota"
answers[] = "Ford"
answers[] = "General Motors"
answers[] = "Other"
answers[] = "I don't drive"</code></pre>

<p>When processing this section, the plugin displays the question on one line, and beneath it, the list of questions, each on its own line, and each starting with a round checkbox to select it. A note on good surveying practice: you should always include a catchall answer. A catchall answer makes it possible for everyone to answer the question.</p>

<h4>Type 3 Question</h4>

<p>This is the last survey question type. Like Type 2, it allows one multiple choice question, except people can check all the answers that apply.</p>

<pre>[section_3]
type = 3
questions[] = "Things you like about your job"
answers[] = "Short commute"
answers[] = "Good supervisor"
answers[] = "Good co-workers"
answers[] = "Fulfilling"
answers[] = "High status"
answers[] = "Fun environment"
answers[] = "Pays well"
answers[] = "I don't like my job"</pre>

<p>In the case of Type 3 questions, the last possible answer must always be a catchall answer. The plugin will not allow survey takers to submit a survey in which they have checked some of the answers and the catchall answer. They must select one or the other.</p>

<h2>Taking Your Survey</h2>

<p>To take your survey, view the Wolf page where you entered the PHP code that calls the survey. The plugin displays your survey on the screen and allow you to fill it. When done, press the Done! button at the bottom of the survey.</p>

<p>The plugin will not allow you to submit an incomplete survey. After you submmit a completed survey, the plugin shows you a confirmation page with the thank-you message you selected. At the bottom of the page there is a Validation Timestamp. Although the surveys are anonymous, the validation timestamp can be used to identify an entry in the survey response file, and to double check that the answers given are the same.</p>

<h2>Survey Summaries</h2>

<p>To view a summary of the responses to your survey, view the Wolf page where you entered the PHP code that calls the survey summary function. Summaries are not stored but calculated every time, so the results will always include the latest responses entered.</p>

<p>You can also view survey summaries from the back end. The survey plugin admin panel offers a choice in the sidebar called "Summaries." This displays a clickable list of surveys found in the public directory. Click on the one you want to view the survey summary.

<h2>Styling Your Survey Forms and Summaries</h2>

<p>The plugin includes a stylesheet called survey.css. Wolf CMS automatically links in all plugin CSS files (each file must match its plugin's name), but only in the back end. Wolf CMS imposes no appearance or structure to the front end, leaving it all for you to do via the layout files. If you'd like to use the included survey.css file, you'll need to link to it in your page layout files. Put the following code in the <code>&lt;head></code> section of your page layout.</p>

<code>&lt;link rel="stylesheet" href="wolf/plugins/survey/survey.css" type="text/css"></code>

<p>You can also create your own stylesheet and link to it instead. To help you build a CSS file that styles your survey forms and charts exactly the way you want, here's the HTML skeleton for each. For brevity and clarity, some attributes and some entire rows are not included. Both survey form and survey summary are surrounded by a div with an id attribute. This lets you target your CSS specifically to the elements within.</p>

<h3>Survey Form HTML Skeleton</h3>

<pre><code>&lt;div id="sf">&lt;!-- sf surveyform -->

&lt;h2>Sample Survey&lt;/h2>

&lt;p>Please complete this survey&lt;/p>

&lt;form method="post">

&lt;h3>Preferences&lt;/h3>
&lt;table class="type1">
  &lt;colgroup>
  	&lt;col span="1">
    &lt;col span="3">
  &lt;/colgroup>
  &lt;thead>
    &lt;tr>
      &lt;th scope="row">S = small M = medium L = large&lt;/th>
      &lt;th scope="col">S&lt;/th>
      &lt;th scope="col">M&lt;/th>
      &lt;th scope="col">L&lt;/th>
    &lt;/tr>
  &lt;/thead>
  &lt;tbody>
    &lt;tr>
      &lt;th scope="row">1. What size Coke do you prefer?&lt;/th>
      &lt;td>&lt;input type="radio" value="1">&lt;/td>
      &lt;td>&lt;input type="radio" value="2">&lt;/td>
      &lt;td>&lt;input type="radio" value="3">&lt;/td>
    &lt;/tr>
  &lt;/tbody>
&lt;/table>

&lt;h3>Lifestyle&lt;/h3>
&lt;fieldset class="type2">
  &lt;legend>5. What kind of car do you drive?&lt;/legend>
  &lt;input type="radio" value="1">
  &lt;label for="Q50">Honda&lt;/label>&lt;br>
&lt;/fieldset>

&lt;fieldset class="type3">
  &lt;legend>6. Things you like about your job&lt;/legend>
  &lt;input type="checkbox" value="1">
  &lt;label for="Q60">Short commute&lt;/label>&lt;br>
&lt;/fieldset>

&lt;p>&lt;input type="reset" value="Clear form and start over">&lt;input type="submit" value="Done!">&lt;/p>

&lt;/form>

&lt;/div>&lt;!-- sf survey form --></code></pre>

<h3>Survey Summary HTML Skeleton</h3>

<p>And here is the HTML skeleton for the Survey Summary.</p>

<pre><code>&lt;div id="ss">&lt;!-- ss survey summary -->

&lt;h2>Survey Summary&lt;/h2>

&lt;h3>Survey Name: Sample Survey&lt;/h3>

&lt;h3>Total Responses: 53&lt;/h3>

&lt;h3>Preferences&lt;/h3>

&lt;table class="type1">
  &lt;colgroup>
    &lt;col span="1">
    &lt;col span="4">
    &lt;col span="4">
  &lt;/colgroup>
  &lt;thead>
    &lt;tr>
      &lt;th scope="row" rowspan="2">S = small M = medium L = large&lt;/th>
      &lt;th class="col" scope="col" colspan="4">Responses&lt;/th>
      &lt;th class="col" scope="col" colspan="4">Percentage&lt;/th>
    &lt;/tr>
    &lt;tr>
      &lt;th class="col" scope="col">S&lt;/th>
      &lt;th scope="col">M&lt;/th>
      &lt;th scope="col">L&lt;/th>
      &lt;th scope="col">Tot&lt;/th>
      &lt;th class="col" scope="col">S&lt;/th>
      &lt;th scope="col">M&lt;/th>
      &lt;th scope="col">L&lt;/th>
      &lt;th scope="col">Tot&lt;/th>
    &lt;/tr>
  &lt;/thead>
  &lt;tbody>
    &lt;tr>
      &lt;th scope="row">1. What size Coke do you prefer?&lt;/th>
      &lt;td class="col">25&lt;/td>
      &lt;td>&lt;strong>28&lt;/strong>&lt;/td>
      &lt;td>0&lt;/td>
      &lt;td>53&lt;/td>
      &lt;td class="col">47&lt;/td>
      &lt;td>&lt;strong>53&lt;/strong>&lt;/td>
      &lt;td>0&lt;/td>
      &lt;td>100&lt;/td>
    &lt;/tr>
  &lt;/tbody>
&lt;/table>

&lt;h3>Lifestyle&lt;/h3>

&lt;table class="type2">
  &lt;colgroup>
    &lt;col span="1">
    &lt;col span="2">
  &lt;/colgroup>
  &lt;thead>
    &lt;tr>
      &lt;th scope="col">5. What kind of car do you drive?&lt;/th>
      &lt;th scope="col">R&lt;/th>
      &lt;th scope="col">%&lt;/th>
    &lt;/tr>
  &lt;/thead>
  &lt;tbody>
    &lt;tr>
      &lt;th scope="row">Honda&lt;/th>
      &lt;td>6&lt;/td>
      &lt;td>11&lt;/td>
    &lt;/tr>
  &lt;/tbody>
&lt;/table>

&lt;table class="type3">
  &lt;colgroup>
    &lt;col span="1">
    &lt;col span="2">
  &lt;/colgroup>
  &lt;thead>
    &lt;tr>
      &lt;th scope="col">6. Things you like about your job&lt;/th>
      &lt;th scope="col">R&lt;/th>
      &lt;th scope="col">%&lt;/th>
    &lt;/tr>
  &lt;/thead>
  &lt;tbody>
    &lt;tr>
      &lt;th scope="row">Short commute&lt;/th>
      &lt;td>&lt;strong>19&lt;/strong>&lt;/td>
      &lt;td>&lt;strong>36&lt;/strong>&lt;/td>
    &lt;/tr>
  &lt;/tbody>
&lt;/table>

&lt;p>End of Summary&lt;/p>

&lt;/div>&lt;!-- ss:survey summary --></code></pre>

<h2>Some Frequently Asked Questions</h2>

<p>1.- Do I have to set up a page for each survey I conduct?</p>

<p>No. Suppose your website has various pages, and you want visitors to be able to click on a link from each page and take a survey related to that page. For a silly example, suppose your website is about fruit, and you have pages for apple, banana, and cherry, so you have survey files called "apple," "banana," and "cherry." Here's what you do. In your apple page, where you want the link to appear, insert this code:</p>

<pre><code>&lt;?php Flash::set('flavor', 'apple') ?>
&lt;a href="survey-page">Click here to take the Apple survey&lt;/a>
</code></pre>

<p>Repeat this on the banana and cherry pages, changing the name of the fruit accordingly. Now set up a page and make sure its slug is called <code>survey-page</code>. In that page, place the following code:</p>

<pre><code>&lt;?php
    $survey_name = Flash::get('flavor');
    survey_conduct($survey_name);
?></code></pre>

<p>Flash variables are a way to pass data from one page to another. Whether they are used or not, Flash variables disappear after the next page is loaded, so it doesn't matter if your web visitor clicks on the survey link or not.</p>

<p>2.- Can I put other content on a page that has a survey?</p>

<p>Yes. People probably expect long surveys to be on their own page, but you can certainly mix surveys with other content. For example, you might want to ask visitors to a page if they found the page helpful. For this, you could set up a survey with a single question and present that survey at the bottom of the page.</p>

<p>3.- Can I put surveys and summaries on the same page?</p>

<p>Yes. You can even set it up so the summary is displayed after your visitor completes the survey. Suppose this was the page with the survey for your apple, cherry, and banana pages. Here's the code you would use:</p>

<pre><code>&lt;?php
    $survey = Flash::get('survey');
    Flash::set('survey', $survey);
    survey_conduct($survey);
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_SESSION['save'])) {
        survey_summarize($survey);
    }
?></code></pre>

<p>After the survey runs, the code checks to see if the page was posted. This indicates the survey was submitted. However, it might not be filled out completely, so we need to check if the survey was saved successfully. That's the <code>!isset($_SESSION['save'])</code> part. So, if the survey was submitted <i>and</i> it was saved successfully, then display the survey summary just below the completed survey.</p>

<p>If this were not a page that handled multiple surveys, you would do away with the <code>$survey</code> variable and just hardcode the survey name.</p>

<p>4.- Why aren't the surveys stored in the database?</p>

<p>For simplicity and ease. A survey is a complex object. A survey contains one or more sections, and each section contains one or more questions, each question with two or more possible answers and one response. This would have required five database tables, and worse, would have made entering a survey definition a tedious task. By using INI files, the task is reduced basically to typing the list of questions. If you maintain a website for someone who uses surveys, they can give you the list of questions, and you can quickly shape them into INI format with a competent text editor.</p>

<p>Another benefit is that the responses are saved in CSV format. It would have been possible to generate the CSV files on demand from the database, but that would have required additional complexity, additional coding, not to mention an additional step on your part. Instead, you can simply download the response files, and they're ready to be opened by any application that understands the CSV format, like Microsoft Excel. This is useful if you want to analyze the responses beyond what the summary does.</p>


<p>Thank you for using this plugin!</p>