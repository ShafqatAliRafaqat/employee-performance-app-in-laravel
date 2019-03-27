<?php

use Illuminate\Http\Request;


$v1Prefix = 'v1/{locale}';

include base_path('routes/apiAdmin.php');

include base_path('routes/apiUser.php');

include base_path('routes/apiPublic.php');
