<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( "../functions.php" );

f_MConnect( );

include( 'admin_header.php' );

?>

<br><br>

<center><table><tr><td valign=top>
<center><b>Администраторская Панель</b><br>
<a href=admin_add_orden_smile.php target=_top>Checking after added orden smile</a><br>
<a href=admin_pandora.php target=_top>Куб Пандоры</a><br>
<a href=help_editor.php target=_top>Редактор Справки</a><br>
<a href=attrib_editor.php target=_top>Редактор Аттрибутов</a><br>
<a href=cards_editor.php target=_top>Редактор Свитков</a><br>
<a href=auras_editor.php target=_top>Редактор Аур</a><br>
<a href=creatures_editor.php target=_top>Редактор Существ</a><br>
<a href=mob_editor.php target=_top>Редактор Мобов</a><br>
<a href=item_editor.php target=_top>Редактор Вещей</a><br>
<a href=craft_editor.php target=_top>Редактор Рецептов</a><br>
<a href=craft_shops.php target=_top>Магазины Крафтеров</a><br>
<a href=tournament_editor.php target=_top>Редактор Турниров</a><br>
<a href=admin_spams_list.php target=_top>Спам-список</a><br>
<a href=admin_voters.php target=_top>Смотрелка голосов на форуме</a><br>
<br>
<center><b>Редакторы квестов</b><br>
<a href=npc_editor.php target=_top>Редактор NPC</a><br>
<a href=quest_editor.php target=_top>Редактор хода квестов в Дневнике</a><br>
<a href=forest_additional_actions_editor.php target=_top>Редактирование доп. фраз</a><br>
<a href=../forum.php?post=1203&f=0&page=0 target=_blank>Занятые триггеры и значения</a><br>
<b>Редакторы для Мая</b><br>
<a href=admin_glash.php target=_top>Редактор Глашатая</a><br>

<br>
<center><b>Редакторы локаций</b><br>
<a href=admin_monster_camps_kill.php target=_top>Кемпинги монстров</a><br>
<a href=loc_editor.php target=_top>Редактор Локаций</a><br>
<a href=cave_editor.php target=_top>Редактор Пещер</a><br>
<a href=lab_editor.php target=_top>Редактор Лабиринтов</a><br>
<a href=lake_editor.php target=_top>Редактор Добывающих Гильдий</a><br>
<a href=forest_editor_items.php target=_top>Редактор Леса (вещи)</a><br>
<a href=forest_editor.php target=_top>Редактор Карты Леса</a> (не для IE)<br>
<a href=forest_map.php target=_blank>Карта Леса</a><br>
</center></td><td valign=top><center>
<b>Управление персонажами</b><br>
<a href=admin_change_passwd.php target=_top>Сменить пароль игроку</a><br>
<a href=admin_ranks.php target=_top>Управление правами персонажей</a><br>
<a href=admin_forum_ranks.php target=_top>Управление модераторами форума</a><br>
<a href=admin_items.php target=_top>Добавить Вещи Персонажу</a><br>
<a href=admin_cards.php target=_top>Добавить Свиток в Книгу Заклинаний Персонажу</a><br>
<a href=admin_leave_combat.php target=_top>Выкинуть Персонажа из боя</a><br>
<a href=admin_triggers.php target=_top>Работа со своими триггерами</a><br>
<a href=admin_quests.php target=_top>Работа с квестами игрока</a><br>
<a href=admin_present_present.php target=_top>Подарить подарок игроку</a><br>
<a href=admin_drop_from_clan.php target=_top>Высрать игрока из Ордена</a><br>
<a href=admin_divorces.php target=_top>Развести пару</a><br>
<br>
<a href=player_log.php target=_top>Движение вещей и денег у игрока</a><br>
<br>
<br>

<b>Радость</b><br>
<a href=admin_payers.php>Общее количество купленных талантов</a><br>
<a href=admin_payers2.php>Источники прибыли</a><br>
<a href=admin_payers_money_rate.php>Рейтинг Реальщиков</a><br>
<a href=admin_tals.php>Рейтинг наличности у игроков</a><br>
<br>
<br>

<b>Прочее</b><br>
<a href=admin_money_difs.php target=_top>Монетные изменения</a><br>
<a href=admin_drops.php target=_top>Сделки с Пустотой</a><br>
<a href=admin_combats.php target=_top>Текущие бои</a><br>
<a href=admin_orden_items.php target=_top>Склад ордена</a><br>
<a href=admin_show_item_number.php target=_top>Общее количество вещей одного вида</a><br>
<a href=admin_show_item_number_all.php target=_top>Общее количество вещей одного типа</a><br>
<a href=admin_news.php target=_top>Новости</a><br>
<a href=admin_koefs.php target=_top>Коэффициенты</a><br>
<a href=online_graph.php target=_top>Онлайн игроков</a><br>
<a href=admin_recalc_weights.php target=_top>Пересчитать веса вещей у персонажей</a><br>
<a href=admin_recalc_stats.php target=_top>Пересчитать все статы игроков</a><br>
<a href=admin_recalc_pstats.php target=_top>Проверить первичные статы у игроков</a><br>
<a href=admin_init_chat_server.php  target=_top>Инициализировать Java-сервер</a><br>
<a href=admin_db_size.php target=_top>Размер базы данных</a><br>
<a href=checker.php target=_top>Проверить базу данных</a><br>
<br />
<b><a href="/tgm/">Новая Админочка</a></b><br>

</center></td></tr></table>
</center>
