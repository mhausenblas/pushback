<?php
/*
 * This script is for regenerate the RDFDump.rdf file 
 * which contain bugs description from Jira
 *
 * 13-03-2009 by sda
 */

$USERNAME="jirauser";
$PASSWORD="jirauser";
$DRIVER="com.mysql.jdbc.Driver";
$JDBCDSN="jdbc:mysql://localhost/jiradb";
$BASEURI="http://localhost:8080/";

// this is the path to the folder contain RDFDump.jar 
// this tool is used to generte RDFDump.rdf file with bugs description from Jira
$PATHTOTHISSCRIPT="/home/simon/RDFDump_copy";

// this is the path to folder from whole demo is served 
// to this location the RDFDump.rdf file will be copied that the application 
//can rich it simply by http  
$PATHTOHTDOCS="/opt/lampp/htdocs/pushback";

$commandEnd = ' > /dev/null; echo $?'; // this one shows the return status
$commandEnd = ' 2>&1'; // this is handy shows you stderr
$command1 = 'sudo java -jar '.$PATHTOTHISSCRIPT.'/RDFDump.jar '.$USERNAME.' '.$PASSWORD.' '.$DRIVER.' '.$JDBCDSN.' '.$BASEURI.$commandEnd ;
$command2 = 'sudo cp '.$PATHTOTHISSCRIPT.'/RDFDump.rdf '.$PATHTOHTDOCS.'/RDFDump.rdf  '.$commandEnd ;

chdir($PATHTOTHISSCRIPT);  

$output = 'Executing:<br />['.$command1.']<br />';
$output .='STDERR:<br />['.shell_exec($command1).']<br />';
$output .='Executing:<br />['.$command2.']<br />';
$output .='STDERR:<br />['.shell_exec($command2).']<br />';

echo $output;
?>