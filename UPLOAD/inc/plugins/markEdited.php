<?php

// Disallow direct access to this file for security reasons
if (!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

if (defined('IN_ADMINCP'))
{
    $plugins->add_hook("admin_config_settings_begin", 'markEdited_load_lang');
}
else
{
    $plugins->add_hook('datahandler_post_validate_post', 'markEdited_datahandler_post_validate_post');
    $plugins->add_hook('datahandler_post_update', 'markEdited_datahandler_post_update');
}

function markEdited_info()
{
    global $lang;
    $lang->load('config_markEdited');
    return array(
        "name"          => "$lang->markEdited",
        "description"   => "$lang->markEdited_desc",
        "website"       => "https://github.com/SvePu/MyBB-Mark_Edited_as_Unread",
        "author"        => "SvePu",
        "authorsite"    => "https://github.com/SvePu",
        "version"       => "2.0",
        "codename"      => "markEdited",
        "compatibility" => "18*"
    );
}

function markEdited_install()
{
    global $db, $lang;
    $lang->load('config_markEdited');

    $group = array(
        'name'        => 'markEdited',
        'title'       => $db->escape_string($lang->setting_group_markEdited),
        'description' => $db->escape_string($lang->setting_group_markEdited_desc),
        'isdefault'   => 0
    );

    $query = $db->simple_select('settinggroups', 'gid', "name='markEdited'");

    if ($gid = (int)$db->fetch_field($query, 'gid'))
    {
        $db->update_query('settinggroups', $group, "gid='{$gid}'");
    }
    else
    {
        $query = $db->simple_select('settinggroups', 'MAX(disporder) AS disporder');
        $disporder = (int)$db->fetch_field($query, 'disporder');

        $group['disporder'] = ++$disporder;

        $gid = (int)$db->insert_query('settinggroups', $group);
    }

    $settings = array(
        'CompareType' => array(
            'optionscode' => 'yesno',
            'value' => 1
        ),
        'MessageStatus' => array(
            'optionscode' => 'yesno',
            'value' => 1
        ),
        'MessageValue' => array(
            'optionscode' => 'numeric \n min=0',
            'value' => 30
        ),
        'SubjectStatus' => array(
            'optionscode' => 'yesno',
            'value' => 1
        ),
        'SubjectValue' => array(
            'optionscode' => 'numeric \n min=0',
            'value' => 6
        ),
        'MinTime' => array(
            'optionscode' => 'numeric \n min=0',
            'value' => 15
        ),
        'MaxTime' => array(
            'optionscode' => 'numeric \n min=0',
            'value' => 10080
        ),
        'CheckUser' => array(
            'optionscode' => 'yesno',
            'value' => 1
        )
    );

    $disporder = 0;

    foreach ($settings as $key => $setting)
    {
        $key = "markEdited_{$key}";

        $setting['name'] = $db->escape_string($key);

        $lang_var_title = "setting_{$key}";
        $lang_var_description = "setting_{$key}_desc";

        $setting['title'] = $db->escape_string($lang->{$lang_var_title});
        $setting['description'] = $db->escape_string($lang->{$lang_var_description});
        $setting['disporder'] = $disporder;
        $setting['gid'] = $gid;

        $db->insert_query('settings', $setting);
        ++$disporder;
    }

    rebuild_settings();
}

function markEdited_is_installed()
{
    global $mybb;
    if (isset($mybb->settings['markEdited_CompareType']))
    {
        return true;
    }
    return false;
}

function markEdited_uninstall()
{
    global $db;

    $db->delete_query("settinggroups", "name='markEdited'");
    $db->delete_query("settings", "name LIKE 'markEdited%'");

    rebuild_settings();
}

function markEdited_activate()
{
}

function markEdited_deactivate()
{
}

function markEdited_load_lang()
{
    global $lang;
    $lang->load('config_markEdited');
}

function markEdited_datahandler_post_validate_post($posthandler)
{
    global $mybb, $db, $post;

    if ($posthandler->method != 'update')
    {
        return;
    }

    if ($mybb->settings['markEdited_SubjectStatus'] || $mybb->settings['markEdited_MessageStatus'])
    {
        $query = $db->query("
            SELECT p.subject, p.tid, p.fid, p.message, p.uid, p.dateline, t.lastpost
            FROM " . TABLE_PREFIX . "posts p
            LEFT JOIN " . TABLE_PREFIX . "threads t ON (p.tid = t.tid)
            WHERE p.pid = '{$posthandler->data['pid']}'
        ");
        $postData = $db->fetch_array($query);

        // Variables for mark and possible mark
        global $mark_make;
        $mark_make = false;

        // Is it last post?
        if ($postData['dateline'] != $postData['lastpost'])
        {
            return;
        }

        // Is it good time to mark unread?
        $time_interval = TIME_NOW - $postData['dateline'];
        if ($time_interval < ($mybb->settings['markEdited_MinTime'] * 60) || ($mybb->settings['markEdited_MaxTime'] > 0 && $time_interval > ($mybb->settings['markEdited_MaxTime'] * 60)))
        {
            return;
        }

        // Is it your post or you can mark unread?
        if ($mybb->settings['markEdited_CheckUser'] && ($postData['uid'] != $mybb->user['uid']))
        {
            return;
        }

        // Is there any changes in subject?
        if ($mark_make !== true && $mybb->settings['markEdited_SubjectStatus'] && THIS_SCRIPT != 'xmlhttp.php')
        {
            $similarValue = markEdited_calculateSimilarity($postData['subject'], $posthandler->data['subject']);

            if ($similarValue >= $mybb->settings['markEdited_SubjectValue'])
            {
                $mark_make = true;
            }
        }

        // Are there no changes in subject? Maybe are there changes in message?
        if ($mark_make !== true && $mybb->settings['markEdited_MessageStatus'])
        {
            $similarValue = markEdited_calculateSimilarity($postData['message'], $posthandler->data['message']);

            if ($similarValue >= $mybb->settings['markEdited_MessageValue'])
            {
                $mark_make = true;
            }
        }

        // Are there any changes? Ok, let's do it
        if ($mark_make !== false)
        {
            $update_sql = array('lastpost' => TIME_NOW);
            $db->update_query('threads', $update_sql, 'tid = ' . $postData['tid']);

            $update_sql = array('lastpost' => TIME_NOW);
            $db->update_query('forums', $update_sql, 'fid = ' . $postData['fid']);

            // Mark thread read for author
            require_once MYBB_ROOT . "inc/functions_indicators.php";
            mark_thread_read($postData['tid'], $postData['fid']);
        }
    }
}

function markEdited_datahandler_post_update($posthandler)
{
    global $mark_make;
    if ($mark_make !== false)
    {
        $posthandler->post_update_data['dateline'] = TIME_NOW;
    }
}

function markEdited_calculateSimilarity($string1, $string2)
{
    global $mybb;

    $result = 0;

    if ($mybb->settings['markEdited_CompareType'])
    {
        similar_text($string2, $string1, $result);
    }
    else
    {
        $length_old = markEdited_getLength($string1);
        $length_new = markEdited_getLength($string2);

        if ($length_old > $length_new)
        {
            $result = ($length_old - similar_text($string1, $string2));
        }
        else
        {
            $result = ($length_new - similar_text($string1, $string2));
        }
    }

    return $result;
}

function markEdited_getLength($string)
{
    if (function_exists('mb_strlen'))
    {
        return mb_strlen($string);
    }
    return strlen($string);
}
