<?php

include_once '../../includes/easyparliament/init.php';
include_once INCLUDESPATH . 'easyparliament/member.php';

$this_page = 'divisions_vote';

$vote = get_http_var('vote');

if (!$vote) {
    $PAGE->error_message("No vote specified", true);
    exit();
}

$main_vote_mp = false;
if ($mp = get_http_var('p')) {
    $MEMBER = new MySociety\TheyWorkForYou\Member(array('person_id' => $mp, 'house' => HOUSE_TYPE_COMMONS));
    $main_vote_mp = true;
} else if ($THEUSER->postcode_is_set()) {
    $MEMBER = new MySociety\TheyWorkForYou\Member(array('postcode' => $THEUSER->postcode(), 'house' => HOUSE_TYPE_COMMONS));
}

$divisions = new MySociety\TheyWorkForYou\Divisions();
$data = array('division' => $divisions->getDivisionResults($vote));

if (!$data['division']) {
    $PAGE->error_message("Vote not found", true);
    exit();
}

$template = 'divisions/vote';
$DATA->set_page_metadata($this_page, 'title', $data['division']['division_title']);

$data['main_vote_mp'] = $main_vote_mp;

if (isset($MEMBER)) {
    $data['member'] = $MEMBER;
    $mp_vote = $divisions->getDivisionResultsForMember($vote, $MEMBER->person_id());
    if ($mp_vote) {
        $data['mp_vote'] = $mp_vote;
        $data['mp_vote']['with_majority'] = false;
        if ($data['mp_vote']['vote'] == $data['division']['vote']) {
            $data['mp_vote']['with_majority'] = true;
        }
    } else {
      if ($data['division']['date'] < $MEMBER->entered_house(1)['date']) {
          $data['before_mp'] = true;
      } else if ($data['division']['date'] > $MEMBER->left_house(1)['date']) {
          $data['after_mp'] = true;
      }
    }
}

$data['debate_time_human'] = False;
$data['debate_day_human'] = format_date($data['division']['date'], LONGDATEFORMAT);
$data['col_country'] = 'UK';
$data['location'] = 'in the House of Commons';
$data['current_assembly'] = 'uk-commons';
$data['nextprev'] = array('up' => array('url' => '/divisions/', 'body' => 'Recent Votes'));

MySociety\TheyWorkForYou\Renderer::output($template, $data);
