<?

include_once( "help/guilds_common.php" );

?>

<script>begin_help( 'Гильдия Алхимиков' );</script>


Основная локация: <a href=help.php?id=34270>Мастерская</a><br>
<? ShowGuildRating( 106 ); ?><br>
<br>
Члены Гильдии Алхимиков могут варить зелья и краску.<br>
Зелья дают временный бонус к характеристикам персонажа<br>
<? ShowProfExpText( "сваренные зелья и краску" ); ?>
<br>
Гильдия открывает перед вами одну основную возможность:<br>
1. Вы можете варить зелья и краску. <? ShowCraftText( "алхимиков", 50013 ); ?><br>
<br>

<? RecipesLink( 106 ); ?>

<script>end_help( );</script>
