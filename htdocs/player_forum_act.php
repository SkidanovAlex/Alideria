<?
include_once( "functions.php" );
include_once( "player.php" );

header("Content-type: text/html; charset=windows-1251");
f_MConnect( );

if( !check_cookie( ) ) 	die( "�������� ��������� Cookie" );
	
$act = (int)$_GET['act'];
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$nkres = f_MQuery( "SELECT login FROM characters WHERE  player_id=$player->player_id" );
$nk_dim = f_MFetch( $nkres );

$Result="";
$Result.="<center><br><table border=1px; width=90% style=\"border:1px solid black\" background=images/chat/chat_bg.gif><tr><td align=center>";
$Result.="<tr><td align=center>#</td><td align=center><i><b>�������� ����</b></i></td>";
$Result.="<td align=center><i><b>����� ���������� ���������</b></i></td>";
$Result.="<td align=center><i><b>����� ���������� ���������</b></i></td>";
$Result.="<td align=center><i><b>����� �������</b></i></td>";
$Result.="<td align=center><i><b>��������</b></i></td></tr>";


//************************* BODY generation *************************************
switch ($act)
{   case 1: // Reload Page
    break;

    case 2: // Add New Link ���������
    $lnk = $_GET['lnk'];
    
if( substr( $lnk, 0, 7 ) != 'http://' ){$lnk = 'http://'.$lnk;}

    
    $comm = conv_utf(trim(HtmlSpecialChars($_GET['comm'])));
    if ($comm==''){$comm='None';}
    $all_num = f_MNum (f_MQuery(" select history_type from forum_player_history where history_type=0 AND id_player_save=$player->player_id"));
    if ($all_num < 20)
    {
    f_MQuery("INSERT INTO forum_player_history (id_player_save, history_type, www_name, www_link) VALUES ($player->player_id, 0, '".$comm."', '".$lnk."')");
    $flag_err = 0;
    }
    else
    {$flag_err = 1;}
    break;

    case 3: // Delete Link
    $id_del = (int)$_GET['del'];
    f_MQuery("delete from forum_player_history where id=".$id_del);
    break;

    case 4: // Load of old links
    $forum_all  = f_MQuery("select distinct author_id, thread_id from forum_posts where author_id=$player->player_id ");
    while( $f_all = f_MFetch($forum_all) )
     {
 $author = f_MFetch( f_MQuery( "select author_id from forum_threads where thread_id=".$f_all['thread_id']));
 if($author['author_id']==$player->player_id)
  {// ���� ���� ��� �� history_type=1
    $h_type=1;}
  else
  {// ���� ����� ��� �� history_type=2
    $h_type=2;}

 if(!f_MNum(f_MQuery( "select id_top from forum_player_history where id_top=".$f_all['thread_id'])))
 {$h_title = f_MFetch( f_MQuery( "select last_post_made, title from forum_threads where thread_id=".$f_all['thread_id']));

    // ������� � ������������� � 2 ������
    // 86400 = 60*60*24 - ������ � ������
    // 14 * 86400 = 1209600 ���. � 2� �������
    $actual= time() - $h_title['last_post_made'];
    if($actual<1209600)
    {
      $nm_lnk =  "<a href=forum.php?thread=".$f_all['thread_id']." target=_blank>".addslashes($h_title['title'])."</a>";
      f_MQuery( "INSERT INTO forum_player_history (id_player_save, id_top, www_name, www_link, history_type, last_time_post)   VALUES ($player->player_id, ".$f_all['thread_id'].", '".addslashes($h_title['title'])."', '".$nm_lnk."', ".$h_type.", ".$h_title['last_post_made'].")");
    }
 }
    }
    break;

    case 5: // clear history (����������� ���������)
f_MQuery("delete from forum_player_history where id_player_save=$player->player_id");
    break;

}
//***********  ������ ��������� ����������
if ($flag_err==1)
{
 $Result.="<font color=red><b><center>��������� ������ ����� ���� �� ����� 20. ������� �� ������ � ��������� �����.</center></b></font>";
}
    
$Result.="<tr><td colspan=6 align=center><b>���� ��������� ".$nk_dim['login']."</b></td></tr>";
      $rld_res = f_MQuery("SELECT * FROM forum_player_history WHERE id_player_save =$player->player_id AND history_type=1");
      $num_res = f_MNum($rld_res );
      if (!$num_res)
      {
       $Result.="<tr><td colspan=6 align=center><i>���������� �� ����������.</i></td></tr>";
      }
      else
      {

      $nm=1;
       while( $arr_res = f_MFetch($rld_res) )
       {

      $all_tops = f_MFetch(f_MQuery("SELECT * FROM forum_threads WHERE thread_id=".$arr_res['id_top']));

//#
       $Result.="<tr><td align=center>".$nm."</td>";
// �������� ���� � ������ �� ���� (������������ ����)
       $Result.="<td align=center>".$arr_res['www_link']."</td>";
// ����� ���������� ���������
       $Result.="<td align=center>".date( "d.m.Y H:i",$all_tops['last_post_made'])."</td>";
// ����� ���������� ���������
       $plr = new Player( $all_tops['last_post_author']);
       $Result.="<td><center>' + ".$plr->Nick()." + '</center></td>";
// ����� �������
       $Result.="<td><center>".$all_tops['posts']."</center></td>";
// ��������
       $Result.="<td align=center><input title=\"������� ��� �� ������� (ID:".$arr_res['id'].")\" type=button value=\"x\" onclick=\"DelTop(".$arr_res['id'].")\"></td></tr>";
       ++$nm;
       }
      }
//***********  ����� ������ ���������� ����������
$Result.="<tr><td colspan=6 align=center><b>���� ���������� ".$nk_dim['login']."</b></td></tr>";
      $rld_res = f_MQuery("SELECT * FROM forum_player_history WHERE id_player_save =$player->player_id AND history_type=2");
      $num_res = f_MNum($rld_res );
      if (!$num_res)
      {
       $Result.="<tr><td colspan=6 align=center><i>���������� �� ����������.</i></td></tr>";
      }
      else
      {
            $nm=1;
       while( $arr_res = f_MFetch($rld_res) )
       {

      $all_tops = f_MFetch(f_MQuery("SELECT * FROM forum_threads WHERE thread_id=".$arr_res['id_top']));

//#
       $Result.="<tr><td align=center>".$nm."</td>";
// �������� ���� � ������ �� ���� (������������ ����)
       $Result.="<td align=center>".$arr_res['www_link']."</td>";
// ����� ���������� ���������
       $Result.="<td align=center>".date( "d.m.Y H:i",$all_tops['last_post_made'])."</td>";
// ����� ���������� ���������
       $plr = new Player( $all_tops['last_post_author']);
       $Result.="<td><center>' + ".$plr->Nick()." + '</center></td>";
// ����� �������
       $Result.="<td><center>".$all_tops['posts']."</center></td>";
// ��������
       $Result.="<td align=center ><center><input title=\"������� ��� �� ������� (ID:".$arr_res['id'].")\" type=button value=\"x\" onclick=\"DelTop(".$arr_res['id'].")\"></center></td></tr>";
       ++$nm;
       }


      }
      
//*********** ��������� ������
$Result.="<tr><td colspan=6 align=center><b>��������� ������</b></td></tr>";
      $rld_res = f_MQuery("SELECT * FROM forum_player_history WHERE id_player_save=$player->player_id AND history_type=0");
      $num_res = f_MNum($rld_res );
      if (!$num_res)
      {
       $Result.="<tr><td colspan=6 align=center><i>���������� �� ����������.</i></td></tr>";
      }
      else
      {
       $nm=1;
       while( $arr_res = f_MFetch($rld_res) )
       {
       $Result.="<tr><td align=center>".$nm."</td><td align=center><a href=\"".$arr_res['www_link']."\" target=_blank>".$arr_res['www_name']."</a></td>";
       $Result.="<td align=center>none...</td><td align=center>none..</td><td align=center>none..</td>";
       $Result.="<td align=center ><input title=\"������� ��� �� ������� (ID:".$arr_res['id'].")\" type=button value=\"x\" onclick=\"DelTop(".$arr_res['id'].")\"></td></tr>";
       ++$nm;
       }
      }

$Result.="</table>";
echo " _( 'myTOP' ).innerHTML = '". $Result ."';";

?>
