<?php
//1. Instanciate Word
//$word = new COM("word.application") or die("Unable to instantiate Word");
//2. specify the MS Word template document (with Bookmark TODAYDATE inside)
//$template_file = "c:\\temp\\reminder.doc";
//3. open the template document
//$word->Documents->Open($template_file);
//4. get the current date MM/DD/YYYY
$current_date = date("Ymd");
//5. get the bookmark and create a new MS Word Range (to enable text substitution)
//$bookmarkname = "TODAYDATE";
//$objBookmark = $word->ActiveDocument->Bookmarks($bookmarkname);
//$range = $objBookmark->Range;
//6. now substitute the bookmark with actual value
//$range->Text = $current_date;
//7. save the template as a new document (c:/reminder_new.doc)
//$new_file = "c:/temp/reminder_new.doc";
//$word->Documents[1]->SaveAs($new_file);
//8. free the object
//$word->Quit();
//$word->Release();
//$word = null;
echo $current_date;
?>

