<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        ?>
            <applet
                id="grabadora"
                CODE="com.softsynth.javasonics.recplay.RecorderUploadApplet"
                CODEBASE="listenup/codebase"
                ARCHIVE="JavaSonicsListenUp.jar"
                NAME="JavaSonicRecorderUploader"
                WIDTH="400" HEIGHT="120">

    <!-- Use a low sample rate that is good for voice. -->
                    <param name="frameRate" value="11025.0">
                    <!-- Most microphones are monophonic so use 1 channel. -->
                    <param name="numChannels" value="1">
                    <!-- Set maximum message length to whatever you want. -->
                    <param name="maxRecordTime" value="60.0">

	<!-- Specify URL and file to be played after upload. -->
                    <!--param name="refreshURL" value="play_message.php?sampleName=message_12345.wav"-->

	<!-- Specify name of file uploaded.
	     There are alternatives that allow dynamic naming. -->
                    <param name="uploadFileName" value="message_test.wav">

	<!-- Server script to receive the multi-part form data. -->
                    <param name="uploadURL" value="GuardarVoz.php?id=1234">
<?php


	// Pass username and password from server to Applet if required.
	if( isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) )
        {
		$authUserName = $_SERVER['PHP_AUTH_USER'];
		echo "    <param name=\"userName\" value=\"$authUserName\">\n";

		$authPassword = $_SERVER['PHP_AUTH_PW'];
		echo "    <param name=\"password\" value=\"$authPassword\">\n";
	}
?>
    </applet>
    </body>
</html>
