<?php

    /*
    *  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
    *  as-is and without warranty under the MIT License. See
    *  [root]/license.txt for more. This information must remain intact.
    */

    require_once('../../common.php');
    require_once('../project/class.project.php');

    //$Git = new Git();

    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();

    //////////////////////////////////////////////////////////////////
    // Get user's active files
    //////////////////////////////////////////////////////////////////

if ($_GET['action']=='submit') {
    $project_path = $_SESSION['project'];
    $submit = "";
    if (preg_match('/[a-zA-Z\-_\/~]+/', $project_path) && isset($_SESSION['user'])) {
        $date = new DateTime();
	$ts = $date->format('Y-m-d H:i:s');
        $ts_branch = $_SESSION['user']."_submit_".$date->format('U');
        $submit_branch = $_SESSION['user']."_submit";
        $wip_branch = $_SESSION['user']."_wip";
        $command = "cd ../../workspace/$project_path ; eval $(ssh-agent -s) ; ssh-add /etc/apache2/private/id_rsa ; git add . ; git commit -m \"".$_SESSION['user']." submitted these changes at ".$ts."\"; git branch -D $ts_branch || : ; git branch -D $submit_branch || : ; git checkout -b $ts_branch ; git checkout -b $submit_branch ; git push origin $ts_branch ; git push origin $submit_branch ; git checkout $wip_branch;";
        $submit = shell_exec($command);
    }

    //preg_replace('/\n/','<br>',$diff);
    echo formatJSEND("success", array("path"=>$project_path,"submit"=>$submit));
}

if ($_GET['action']=='stash') {
    $project_path = $_SESSION['project'];
    $stash = "";
    if (preg_match('/[a-zA-Z\-_\/~]+/', $project_path) && isset($_SESSION['user'])) {
        $date = new DateTime();
	$ts = $date->getTimestamp();
        $submit_branch = $_SESSION['user']."_submit";
        $stash = shell_exec("cd ../../workspace/$project_path ; eval $(ssh-agent -s) ; ssh-add /etc/apache2/private/id_rsa ; git stash ; git fetch ; git checkout origin/master ; git checkout $submit_branch || git checkout -b $submit_branch ; git reset --hard ; git clean -dfx");
error_log($stash);
    }

    //preg_replace('/\n/','<br>',$diff);
    echo formatJSEND("success", array("path"=>$project_path,"stash"=>$stash));
}

if ($_GET['action']=='diff') {
    $project_path = $_SESSION['project'];
    $diff = "";
    $wip_start = $_SESSION['user']."_submit";
    if (preg_match('/[a-zA-Z\-_\/~]+/', $project_path) && isset($_SESSION['user'])) {
        $diff = shell_exec("cd ../../workspace/$project_path ; git diff origin/".$wip_start);
    }

    //preg_replace('/\n/','<br>',$diff);
    echo formatJSEND("success", array("path"=>$project_path,"diff"=>$diff));
}

