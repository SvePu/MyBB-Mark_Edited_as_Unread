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

$l['markEdited'] = 'Mark Edited as Unread';
$l['markEdited_desc'] = 'This plugin marks edited posts as unread by change date, if the post is last in topic.';

$l['setting_group_markEdited'] = 'Mark Edited as Unread Settings';
$l['setting_group_markEdited_desc'] = 'Settings for plugin "Mark Edited as Unread"';

$l['setting_markEdited_CompareType'] = 'Comparing the posts based on the number of changed characters';
$l['setting_markEdited_CompareType_desc'] = 'If enabled, the posts will be checked based on the number of characters changed, if not, based on the percentage of similarity.';

$l['setting_markEdited_MessageStatus'] = 'Mark unread last posts if they have been edited';
$l['setting_markEdited_MessageStatus_desc'] = "If disabled, edited posts won't be marked unread.";

$l['setting_markEdited_MessageValue'] = 'Exclude edits this or more similar to the original';
$l['setting_markEdited_MessageValue_desc'] = "Last posts that are similar to their pre-edit version this % or more, won't be marked unread (it means the edit was minor). Set to 0 to mark unread on <i>any</i> edits.";

$l['setting_markEdited_SubjectStatus'] = 'Also mark unread last posts if just their Subject has been edited';
$l['setting_markEdited_SubjectStatus_desc'] = "If disabled, editing the subject won't trigger marking the post as unread.";

$l['setting_markEdited_SubjectValue'] = 'Exclude Subject changes this or more similar to the original';
$l['setting_markEdited_SubjectValue_desc'] = "Last posts with Subjects that are similar to their pre-edit version this % or more, won't be marked unread (it means the Subject edit was minor). Set to 0 to mark unread on <i>any</i> Subject edits.";

$l['setting_markEdited_MinTime'] = 'Minimum time interval (minutes)';
$l['setting_markEdited_MinTime_desc'] = "Edits to the post or its Subject made sooner than this time after writing the post, won't cause it to be marked as unread.";

$l['setting_markEdited_MaxTime'] = 'Maximum time interval (minutes)';
$l['setting_markEdited_MaxTime_desc'] = "Edits to the post or its Subject made later than this time after writing the post, won't cause it to be marked as unread.";

$l['setting_markEdited_CheckUser'] = 'Only mark unread posts edited by the original author';
$l['setting_markEdited_CheckUser_desc'] = 'If enabled, the plugin will mark unread only last posts edited by their authors. If disabled, last posts edited by moderators will be marked unread as well.';
