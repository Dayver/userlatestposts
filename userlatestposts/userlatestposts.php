<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=users.details.tags,ajax
  Tags=users.details.tpl:{USERS_DETAILS_LATESTPOSTS}
  [END_COT_EXT]
  ==================== */
defined('COT_CODE') or die('Wrong URL.');

require_once (cot_langfile('userlatestposts'));

$skin = cot_tplfile('userlatestposts', 'plug');
$user_posts = new XTemplate($skin);
if (empty($id) && empty($urr['user_id']))
{
	$id = cot_import('id', 'G', 'INT');
	$ajax = true;
}

if (empty($id) && $usr['id'] > 0)
{
	$id = $usr['id'];
}

if ($urr['user_id'] != $id)
{
	$sql = $db->query("SELECT user_id FROM $db_users WHERE user_id='$id' LIMIT 1");
	if ($sql->rowCount() == 0)
	{
		$disable = true;
	}
	else
	{
		$urr['user_id'] = $id;
	}
}

if ($cot_modules['forums'] && !$disable)
{
	require_once cot_incfile('forums', 'module');

	list($pnf, $df, $df_url) = cot_import_pagenav('df', $cfg['plugin']['userlatestposts']['countonpage']);

	$totalitems = $db->query("SELECT COUNT(*) FROM $db_forum_posts p, $db_forum_topics t	WHERE fp_posterid='".$urr['user_id']."' AND p.fp_topicid=t.ft_id")->fetchColumn();

	if ($cfg['plugin']['userlatestposts']['ajax'])
	{
		$upf_ajax_begin = "<div id='reloadf'>";
		$upf_ajax_end = "</div>";
	}

	$pagenav = cot_pagenav('users', 'm=details&id='.$urr['user_id'], $df, $totalitems, $cfg['plugin']['userlatestposts']['countonpage'], 'df', '', $cfg['plugin']['userlatestposts']['ajax'], "reloadf", 'plug', "r=userlatestposts&id=".$urr['user_id']);

	$sqluserlatestposts = $db->query("SELECT p.fp_id, p.fp_topicid, p.fp_updated, t.ft_title, t.ft_id, t.ft_cat
		 FROM $db_forum_posts p, $db_forum_topics t
		 WHERE fp_posterid='".$urr['user_id']."'
		 AND p.fp_topicid=t.ft_id
		 GROUP BY t.ft_id
		 ORDER BY fp_updated DESC
		 LIMIT $df, ".$cfg['plugin']['userlatestposts']['countonpage']);

	if ($sqluserlatestposts->rowCount() == 0)
	{
		$user_posts->parse("USERLATESTPOSTS.NONE");
	}
	else
	{
		$ii = 0;
		while ($row = $sqluserlatestposts->fetch())
		{
			if (cot_auth('forums', $row['ft_cat'], 'R'))
			{
				$ii++;
				$build_forum = cot_breadcrumbs(cot_forums_buildpath($row['ft_cat'], false), false);
				$user_posts->assign(array(
					"UPF_DATE" => cot_date('datetime_medium', $row['fp_updated']),
					"UPF_FORUMS" => $build_forum,
					"UPF_FORUMS_ID" => $row['fp_id'],
					"UPF_FORUMS_POST_URL" => cot_url('forums', 'm=posts&q=' . $row['fp_topicid'] . '&d=' . $durl, "#" . $row['fp_id']),
					"UPF_FORUMS_TITLE" => htmlspecialchars($row['ft_title']),
					"UPF_NUM" => $ii,
					"UPF_ODDEVEN" => cot_build_oddeven($ii),
				));
				$user_posts->parse("USERLATESTPOSTS.YES.TOPIC");
			}
		}

		$user_posts->assign(array(
			"UPF_AJAX_BEGIN" => $upf_ajax_begin,
			"UPF_AJAX_END" => $upf_ajax_end,
			"UPF_PAGENAV" => $pagenav['main'],
			"UPF_PAGENAV_PREV" => $pagenav['prev'],
			"UPF_PAGENAV_NEXT" => $pagenav['next'],
			"UPF_TOTALITEMS" => $totalitems,
			"UPF_COUNT_ON_PAGE" => $ii,
		));
		$user_posts->parse("USERLATESTPOSTS.YES");
	}
}
else
{
	$user_posts->parse("USERLATESTPOSTS.NONE");
}

$user_posts->parse("USERLATESTPOSTS");
$user_pos = $user_posts->text("USERLATESTPOSTS");

if (!defined('COT_PLUG'))
{
	$t->assign(array("USERS_DETAILS_LATESTPOSTS" => $user_pos));
}
else
{
	cot_sendheaders();
	echo $user_pos;
}