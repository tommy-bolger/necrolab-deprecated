<?php
$replay_html = '&nbsp;';

if(!empty($this->replay_http_path)) {
    $replay_html = "<a href=\"$this->replay_http_path\" target=\"_blank\">Download</a>";
}

echo $replay_html;