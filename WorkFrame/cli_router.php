<?php

die('cli_router not quite implement yet');
isset($global_hooks) && $global_hooks->pre_cli_action_hook();
//$request_handler->pre_action_hook();
//$request_handler->$action();
//$request_handler->post_action_hook();
isset($global_hooks) && $global_hooks->post_cli_action_hook();
