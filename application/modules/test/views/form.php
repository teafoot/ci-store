<?php
	echo form_open("test/submit");
		echo "Name: ";
		echo form_input("name") . "<br><br>";
		echo "City: ";
		echo form_input("city") . "<br><br>";
		echo "Age: ";
		echo form_input("age") . "<br><br>";
		echo form_submit("submit", "Submit");
	echo form_close();
?>