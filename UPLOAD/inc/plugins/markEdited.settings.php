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

/**
 * Disallow direct access to this file for security reasons
 * 
 */
if (!defined("IN_MYBB")) exit;

/**
 * Plugin Installator Class
 * 
 */
class markEditedInstaller
{

    public static function install()
    {
        global $db, $lang;
        self::uninstall();

        $result = $db->simple_select('settinggroups', 'MAX(disporder) AS max_disporder');
        $max_disporder = $db->fetch_field($result, 'max_disporder');
        $disporter = 0;

        $settings_group = array(
            'name' => 'markEdited',
            'title' => $db->escape_string($lang->markEditedName),
            'description' => $db->escape_string($lang->markEditedSettingGroupDesc),
            'disporder' => $max_disporder + 1,
            'isdefault' => '0'
        );
        $db->insert_query('settinggroups', $settings_group);
        $gid = (int) $db->insert_id();

        $setting = array(
            'name' => 'markEditedCompareType',
            'title' => $db->escape_string($lang->markEditedCompareType),
            'description' => $db->escape_string($lang->markEditedCompareTypeDesc),
            'optionscode' => 'onoff',
            'value' => '1',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'name' => 'markEditedMessageStatus',
            'title' => $db->escape_string($lang->markEditedMessageStatus),
            'description' => $db->escape_string($lang->markEditedMessageStatusDesc),
            'optionscode' => 'onoff',
            'value' => '1',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'name' => 'markEditedMessageValue',
            'title' => $db->escape_string($lang->markEditedMessageValue),
            'description' => $db->escape_string($lang->markEditedMessageValueDesc),
            'optionscode' => 'text',
            'value' => '30',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'name' => 'markEditedSubjectStatus',
            'title' => $db->escape_string($lang->markEditedSubjectStatus),
            'description' => $db->escape_string($lang->markEditedSubjectStatusDesc),
            'optionscode' => 'onoff',
            'value' => '1',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'name' => 'markEditedSubjectValue',
            'title' => $db->escape_string($lang->markEditedSubjectValue),
            'description' => $db->escape_string($lang->markEditedSubjectValueDesc),
            'optionscode' => 'text',
            'value' => '6',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'name' => 'markEditedMinTime',
            'title' => $db->escape_string($lang->markEditedMinTime),
            'description' => $db->escape_string($lang->markEditedMinTimeDesc),
            'optionscode' => 'text',
            'value' => '15',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'name' => 'markEditedMaxTime',
            'title' => $db->escape_string($lang->markEditedMaxTime),
            'description' => $db->escape_string($lang->markEditedMaxTimeDesc),
            'optionscode' => 'text',
            'value' => '10080',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'name' => 'markEditedCheckUser',
            'title' => $db->escape_string($lang->markEditedCheckUser),
            'description' => $db->escape_string($lang->markEditedCheckUserDesc),
            'optionscode' => 'onoff',
            'value' => '1',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        rebuild_settings();
    }

    public static function uninstall()
    {
        global $db;

        $result = $db->simple_select('settinggroups', 'gid', "name = 'markEdited'");
        $gid = (int) $db->fetch_field($result, "gid");
        
        if ($gid > 0)
        {
            $db->delete_query('settings', "gid = '{$gid}'");
        }
        $db->delete_query('settinggroups', "gid = '{$gid}'");
        
        rebuild_settings();
    }
    
}
