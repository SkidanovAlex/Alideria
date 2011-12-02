<?

		if( isset( $_FILES['fl'] ) )
		{
        	list( $width, $height, $type, $attr ) = getimagesize( $_FILES['fl']['tmp_name'] );

        	settype( $width, 'integer' );
        	settype( $height, 'integer' );
        	
        	echo "<b>Заявленный размер файла:</b>: ".$_FILES['fl']['size']."<br>";
        	echo "<b>Реальный размер файла:</b>: ".(int)filesize($_FILES['fl']['tmp_name'])."<br>";
        	echo "<b>Размер картинки:</b>: {$width}x{$height}<br>";
        	echo "<b>Временная локация файла:</b> ".$_FILES['fl']['tmp_name']."<br><br>";
		}
		    		
    		echo '<form enctype="multipart/form-data" action=files_test.php method=post>';
    		echo "<table cellspacing=0 cellpadding=0 border=0>";
    		echo "<tr><td><input type=file name=fl value=''></td></tr>";
    		echo "<tr><td><input type=submit value='Загрузить'></td></tr>";
    		echo "</table>";
    		echo "</form>";

?>
    		