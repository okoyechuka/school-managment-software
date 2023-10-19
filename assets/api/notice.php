<?php
// ask him to show u where he saved the database information
define('SOA', true);
require("../../drivers/includes/config.inc");
$db = mysqli_connect($sconfig['host'], $sconfig['user'], $sconfig['password'], $sconfig['database']);
if(isset($_GET['notice'])){
    $values = countNewNotice($db, $_GET['userId'], $_GET['class'], $_GET['userRole'], $_GET['school_id']);
    $json = "{";
    if($values['num2'] != 0) $json .= '"notNum": "' . $values['num2']. '"';
    else $json .= '"notNum": 0';

    if(count($values['noticeIds']) != 0){
        $json .= ', "notDetail": [';
        foreach($values['noticeIds'] as $id => $msg){
            $json .= '{"id": "' . $id . '", "title":"' . $msg['title'] . '", "text": "'.$msg['text'] .'"},';
        }
        $json = trim($json, ',');
        $json .= ']';
    }
    else $json .= ', "notDetail": []';

    $json .= '}';
    echo $json;
}

function noticeRead($id, $user, $db) {
    $sql = "SELECT * FROM notice_read WHERE notice_id = '$id' AND user_id = '$user'";
    $result = mysqli_query($db, $sql);
    $num = mysqli_num_rows($result);

    if($num > 0) { return true; } else { return false; }
}
function countNewNotice($db, $userID, $class, $role, $school) {
    $sql = "SELECT * FROM notice WHERE school_id = '$school'
            AND (role_id = '$role' OR role_id = '0'
            OR user_id = '$userID' OR class_id = '0' OR
            class_id = '$class' OR user_id = '0') ORDER BY id DESC";

    $output1 = mysqli_query($db, $sql) or die(mysqli_error($db));
    $result['num2'] = 0;
    $result['noticeIds'] = array();
    while($row = mysqli_fetch_assoc($output1)){
        if(!noticeRead($row['id'],$userID,$db)){
            $result['num2'] += 1;
            $result['noticeIds'][$row['id']]['title'] = $row['title'];
            $result['noticeIds'][$row['id']]['text'] = $row['text'];
        }
    }
    return $result;
}
?>
