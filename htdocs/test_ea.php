<?php

require_once("functions.php");
  echo "<pre>";
  var_dump($_SERVER);
f_MConnect();
  echo time();
  
//f_MQuery("INSERT INTO post (sender_id,receiver_id,title,content,money,np,deadline,readed) SELECT '1308118' as sender_id, player_id as receiver_id, '��������' as title, '��������!. 19.10.10 � 15:00 �� ���������� �������, ���� ������� ����� ������������ �������� �� 4 ���.' as content, '0' as money, '0' as np, '0' as deadline, '0' as readed FROM characters");

//phpinfo();
//die();
