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
 * Create plugin object
 * 
 */
$plugins->objects['markEdited'] = new markEdited();

/**
 * Standard MyBB info function
 * 
 */
function markEdited_info()
{
    global $lang;

    // Load lang file
    $lang->load('markEdited');
    
    $lang->markEditedDesc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="3BTVZBUG6TMFQ">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->markEditedDesc;

    return Array(
        "name" => $lang->markEditedName,
        "description" => $lang->markEditedDesc,
        "website" => "https://tkacz.pro",
        "author" => 'Lukasz Tkacz',
        "authorsite" => "https://tkacz.pro",
        "version" => "1.8",
        "compatibility" => "18*"
    );
}

/**
 * Standard MyBB installation functions 
 * 
 */
function markEdited_install()
{
    require_once('markEdited.settings.php');
    markEditedInstaller::install();
}

function markEdited_is_installed()
{
    global $mybb;

    return (isset($mybb->settings['markEditedMessageStatus']));
}

function markEdited_uninstall()
{
    require_once('markEdited.settings.php');
    markEditedInstaller::uninstall();
}

/**
 * Standard MyBB activation functions 
 * 
 */
function markEdited_activate()
{
}

function markEdited_deactivate()
{
}

/**
 * Plugin Class 
 * 
 */
class markEdited
{

    /**
     * Add all needed hooks
     */
    public function __construct()
    {
        global $mybb, $plugins;

        // Add all hooks
        $plugins->hooks["datahandler_post_validate_post"][10]["markEdited_main"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'markEdited\']->main();'));
        $plugins->hooks["pre_output_page"][10]["markEdited_pluginThanks"] = array("function" => create_function('&$arg', 'global $plugins; $plugins->objects[\'markEdited\']->pluginThanks($arg);'));
    }

    /**
     * Main Mark Edites As Unread Function - compare in few steps
     * 1) Get post and thread info from DB
     * 2) Check: is it last post in thread
     * 3) Check: max and minimum time intervals
     * 4) Compare 1: Check edit reason
     * 5) Compare 2: Check message subject
     * 6) Compare 3: Check message body
     * 7) If $mark_make == true, mark post as unread 
     */
    public function main()
    {
        global $mybb, $db, $posthandler, $post;

        if ($posthandler->method != 'update')
        {
            return;
        }

        if ($mybb->settings['markEditedSubjectStatus'] ||
                $mybb->settings['markEditedMessageStatus'] ||
                $mybb->settings['markEditedReasonStatus'])
        {
            $sql = "SELECT p.subject, p.tid, p.fid, p.message, p.uid, p.dateline, t.lastpost
                    FROM " . TABLE_PREFIX . "posts p
                    INNER JOIN " . TABLE_PREFIX . "threads t ON (p.tid = t.tid)
                    WHERE pid = '" . $posthandler->data['pid'] . "'";
            $result = $db->query($sql);
            $postData = $db->fetch_array($result);

            // Variables for mark and possible mark
            $mark_make = false;

            // Is it last post?
            if ($postData['dateline'] != $postData['lastpost'])
            {
                return;
            }

            // Is it good time to mark unread?
            $time_interval = TIME_NOW - $postData['dateline'];
            if ($time_interval < ($mybb->settings['markEditedMinTime'] * 60) ||
                    ($mybb->settings['markEditedMaxTime'] > 0 && $time_interval > ($mybb->settings['markEditedMaxTime'] * 60)))
            {
                return;
            }

            // Is it your post or you can mark unread?
            if ($mybb->settings['markEditedCheckUser'] && ($postData['uid'] != $mybb->user['uid']))
            {
                return;
            }

            // Is there any changes in subject?
            if (!$mark_make && $mybb->settings['markEditedSubjectStatus'] && THIS_SCRIPT != 'xmlhttp.php')
            {
                $similarValue = $this->calculateSimilarity($postData['subject'], $posthandler->data['subject']);

                if ($similarValue >= $mybb->settings['markEditedSubjectValue'])
                {
                    $mark_make = true;
                }
            }

            // Are there no changes in subject? Maybe are there changes in message?
            if (!$mark_make && $mybb->settings['markEditedMessageStatus'])
            {
                $similarValue = $this->calculateSimilarity($postData['message'], $posthandler->data['message']);

                if ($similarValue >= $mybb->settings['markEditedMessageValue'])
                {
                    $mark_make = true;
                }
            }

            // Are there any changes? Ok, let's do it
            if ($mark_make)
            {
                $posthandler->post_update_data['dateline'] = TIME_NOW;

                $update_sql = array('lastpost' => TIME_NOW);
                $db->update_query('threads', $update_sql, 'tid = ' . $postData['tid']);

                $update_sql = array('lastpost' => TIME_NOW);
                $db->update_query('forums', $update_sql, 'fid = ' . $postData['fid']);
                
                // Mark thread read for author
                require_once MYBB_ROOT."inc/functions_indicators.php";
                mark_thread_read($postData['tid'], $postData['fid']);
            }
        }
    }

    /**
     * Calculate strings similarity using similar_text function
     * @param $string1 First string to compare
     * @param $string2 Second string to compare
     * @return int Percentage of similarity or num of changed chars (depending of settings)
     */
    private function calculateSimilarity($string1, $string2)
    {
        global $mybb;

        $result = 0;

        if ($mybb->markEditedCompareType)
        {
            similar_text($string2, $string1, $result);
        }
        else
        {
            $length_old = $this->getLength($string1);
            $length_new = $this->getLength($string2);

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

    /**
     * Get string length by using multibyte or standard function
     * @param string $string String to calculate length
     * @return string length
     */
    private function getLength($string)
    {
        if (function_exists('mb_strlen'))
        {
            return mb_strlen($string);
        }
        return strlen($string);
    }
    
    /**
     * Say thanks to plugin author - paste link to author website.
     * Please don't remove this code if you didn't make donate
     * It's the only way to say thanks without donate :)     
     */
    public function pluginThanks(&$content)
    {
        global $session, $lukasamd_thanks;
        
        if (!isset($lukasamd_thanks) && $session->is_spider)
        {
            $thx = '<div style="margin:auto; text-align:center;">This forum uses <a href="https://tkacz.pro">Lukasz Tkacz</a> MyBB addons.</div></body>';
            $content = str_replace('</body>', $thx, $content);
            $lukasamd_thanks = true;
        }
    }
    
}
