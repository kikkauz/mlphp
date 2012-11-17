<?php
/*
Copyright 2002-2012 MarkLogic Corporation.  All Rights Reserved.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<title>Example: Chaining Document Methods</title>
</head>

<?php
// Set up global vars and class autoloading
require_once ('setup.php');

// Write text as a document to the database
$doc = new Document($mlphp['client']);
echo $doc->setContent('Hello, PHP!')->write('/chained1.txt')->getContent();
echo '<br />';
echo $doc->setContentFile('example.xml')->write('/chained2.xml')->getContent();
echo '<br />';
$doc2 = new Document($mlphp['client']);
echo $doc2->write();

?>

</body>
</html>