<?php
/**
 * This file is part of Mark Edited as Unread plugin for MyBB.
 * Copyright (C) 2010-2013 Lukasz Tkacz <lukasamd@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */ 

$l['markEditedName'] = 'Edytowane - nieprzeczytane / Powód edycji';
$l['markEditedDesc'] = 'Ten Plugin oznacza edytowane posty jako nieprzeczytane poprzez zmianę daty, jeżeli dany post jest ostatnim w temacie, pozwala również na wybranie powodu edycji postu.';

$l['markEditedSettingGroupDesc'] = 'Ustawienia pluginu, "Edytowane - nieprzeczytane".';

$l['markEditedCompareType'] = 'Porównywanie na podstawie ilości zmienionych znaków';
$l['markEditedCompareTypeDesc'] = 'Jeżeli włączone, posty będą sprawdzane na podstawie ilości zmienionych znaków, jeżeli nie, na podstawie procentowego podobieństwa.';

$l['markEditedMessageStatus'] = 'Sprawdzanie treści wiadomości';
$l['markEditedMessageStatusDesc'] = 'Określa, czy ma być sprawdzane podobieństwo treści wiadomości.';

$l['markEditedMessageValue'] = 'Minimalne podobieństwo wiadomości';
$l['markEditedMessageValueDesc'] = 'Określa w znakach / procentach minimalne podobieństwo pomiędzy nową a starą wiadomością.';

$l['markEditedSubjectStatus'] = 'Sprawdzanie nazwy tematu';
$l['markEditedSubjectStatusDesc'] = 'Określa, czy ma być sprawdzane podobieństwo tytułu tematu / posta.';

$l['markEditedSubjectValue'] = 'Minimalne podobieństwo tytułu';
$l['markEditedSubjectValueDesc'] = 'Określa w znakach / procentach minimalne podobieństwo pomiędzy nowym a starym tytułem.';

$l['markEditedMinTime'] = 'Minimalny odstęp czasowy';
$l['markEditedMinTimeDesc'] = 'Określa w minutach minimalny odstęp czasowy pomiędzy napisaniem wiadomości a jej edycją, która spowoduje oznaczenie postu jako nieprzeczytany.';

$l['markEditedMaxTime'] = 'Maksymalny odstęp czasowy';
$l['markEditedMaxTimeDesc'] = "Określa w minutach maksymalny odstęp czasowy pomiędzy napisaniem wiadomości a jej edycją, która spowoduje oznaczenie postu jako nieprzeczytany.";

$l['markEditedCheckUser'] = 'Sprawdzanie autora postu';
$l['markEditedCheckUserDesc'] = 'Jeżeli ustawione na tak, oznaczane będą tylko posty edytowane przez ich autorów (np. gdy robi to moderator, oznaczenia postów nie będą zmieniane).';
